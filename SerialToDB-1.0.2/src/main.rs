use serialport::{SerialPortInfo, SerialPortType};
use std::time::Duration;
use std::io::{BufRead, BufReader};
use std::path::Path;
use config::{Config, File};
use serde::Deserialize;
use tokio_postgres::NoTls;
use mysql_async::prelude::*;
use chrono::{DateTime, Utc};
use std::sync::{Arc, Mutex};

// ============================================================================
// Configuration structures (inchangées)
// ============================================================================

#[derive(Debug, Deserialize)]
#[serde(rename_all = "lowercase")]
enum DatabaseType {
    Postgres,
    MySQL,
    MariaDB,
}

#[derive(Debug, Deserialize)]
struct SerialConfig {
    port: String,
    baud_rate: u32,
    timeout_ms: u64,
}

#[derive(Debug, Deserialize)]
struct DatabaseConfig {
    db_type: DatabaseType,
    host: String,
    port: u16,
    user: String,
    password: String,
    db_name: String,
    table: String,
}

#[derive(Debug, Deserialize)]
struct Settings {
    serial: SerialConfig,
    database: DatabaseConfig,
}

// ============================================================================
// Database abstraction
// ============================================================================

enum DatabaseInner {
    Postgres(tokio_postgres::Client),
    MySQL(mysql_async::Pool),
}

// NEW: Database est clonable pour être passé aux tâches
#[derive(Clone)]
struct Database {
    inner: Arc<DatabaseInner>,
    table_name: String,
}

impl Database {
    async fn new(config: &DatabaseConfig) -> Result<Self, Box<dyn std::error::Error>> {
        let inner = match config.db_type {
            DatabaseType::Postgres => Self::connect_postgres(config).await?,
            DatabaseType::MySQL | DatabaseType::MariaDB => Self::connect_mysql(config).await?,
        };

        let db = Database {
            inner: Arc::new(inner), // MODIFIED: Use Arc for shared ownership
            table_name: config.table.clone(),
        };

        db.create_table_if_not_exists().await?;
        Ok(db)
    }

    async fn connect_postgres(config: &DatabaseConfig) -> Result<DatabaseInner, Box<dyn std::error::Error>> {
        let connection_string = format!(
            "host={} port={} user={} password={} dbname={}",
            config.host, config.port, config.user, config.password, config.db_name
        );
        let (client, connection) = tokio_postgres::connect(&connection_string, NoTls).await?;
        tokio::spawn(async move {
            if let Err(e) = connection.await {
                eprintln!("PostgreSQL connection error: {}", e);
            }
        });
        Ok(DatabaseInner::Postgres(client))
    }

    async fn connect_mysql(config: &DatabaseConfig) -> Result<DatabaseInner, Box<dyn std::error::Error>> {
        let url = format!(
            "mysql://{}:{}@{}:{}/{}",
            config.user, config.password, config.host, config.port, config.db_name
        );
        let pool = mysql_async::Pool::new(url.as_str());
        Ok(DatabaseInner::MySQL(pool))
    }

    // MODIFIED: La table stocke un booléen pour l'état du moteur
    async fn create_table_if_not_exists(&self) -> Result<(), Box<dyn std::error::Error>> {
        match &*self.inner {
            DatabaseInner::Postgres(client) => {
                let query = format!(
                    "CREATE TABLE IF NOT EXISTS {} (
                        id SERIAL PRIMARY KEY,
                        timestamp TIMESTAMPTZ NOT NULL,
                        is_running BOOLEAN NOT NULL
                    )",
                    self.table_name
                );
                client.execute(&query, &[]).await?;
            }
            DatabaseInner::MySQL(pool) => {
                let query = format!(
                    "CREATE TABLE IF NOT EXISTS {} (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        timestamp TIMESTAMP NOT NULL,
                        is_running BOOLEAN NOT NULL
                    )",
                    self.table_name
                );
                let mut conn = pool.get_conn().await?;
                conn.query_drop(query).await?;
            }
        }
        Ok(())
    }

    // MODIFIED: Insère un état booléen
    async fn insert_state(&self, is_running: bool) -> Result<(), Box<dyn std::error::Error>> {
        let now: DateTime<Utc> = Utc::now();
        
        match &*self.inner {
            DatabaseInner::Postgres(client) => {
                let query = format!(
                    "INSERT INTO {} (timestamp, is_running) VALUES ($1, $2)",
                    self.table_name
                );
                client.execute(&query, &[&now, &is_running]).await?;
            }
            DatabaseInner::MySQL(pool) => {
                let query = format!(
                    "INSERT INTO {} (timestamp, is_running) VALUES (?, ?)",
                    self.table_name
                );
                let mut conn = pool.get_conn().await?;
                let mysql_timestamp = now.format("%Y-%m-%d %H:%M:%S").to_string();
                conn.exec_drop(query, (mysql_timestamp, is_running)).await?;
            }
        }
        Ok(())
    }
}

// ============================================================================
// Serial port utilities (inchangé)
// ============================================================================
struct SerialPortManager;
impl SerialPortManager {
    fn list_available_ports() -> Result<Vec<SerialPortInfo>, Box<dyn std::error::Error>> {
        Ok(serialport::available_ports()?)
    }
    fn display_available_ports() {
        println!("Available serial ports:");
        match Self::list_available_ports() {
            Ok(ports) => {
                for port in ports {
                    let port_type_str = match port.port_type {
                        SerialPortType::UsbPort(info) => format!("USB ({})", info.product.unwrap_or_default()),
                        SerialPortType::PciPort => "PCI".to_string(),
                        SerialPortType::BluetoothPort => "Bluetooth".to_string(),
                        SerialPortType::Unknown => "Unknown".to_string(),
                    };
                    println!("  - {} ({})", port.port_name, port_type_str);
                }
            }
            Err(e) => eprintln!("Error searching for serial ports: {}", e),
        }
    }
    fn open_port(config: &SerialConfig) -> Result<Box<dyn serialport::SerialPort>, Box<dyn std::error::Error>> {
        Ok(serialport::new(&config.port, config.baud_rate)
            .timeout(Duration::from_millis(config.timeout_ms))
            .open()?)
    }
}

// ============================================================================
// Configuration management
// ============================================================================

struct ConfigManager;
impl ConfigManager {
    // MODIFIED: Simplifié pour ne plus charger la config "upload"
    fn load() -> Result<Settings, Box<dyn std::error::Error>> {
        let config_path = Path::new("config/default.toml");
        let settings = Config::builder()
            .add_source(File::from(config_path))
            .build()?
            .try_deserialize()?;
        Ok(settings)
    }

    fn display(settings: &Settings) {
        println!("\nCurrent configuration:");
        println!("  Port: {}", settings.serial.port);
        println!("  Baud rate: {}", settings.serial.baud_rate);
        println!("  Timeout: {} ms", settings.serial.timeout_ms);
        println!("  Database: {:?}", settings.database.db_type);
        println!("  Table: {}", settings.database.table);
    }
}


// ============================================================================
// Engine monitor (remplace DataProcessor)
// ============================================================================

// NEW: Structure pour gérer l'état du moteur
struct EngineMonitor {
    last_known_state: Arc<Mutex<Option<bool>>>,
    database: Database,
}

impl EngineMonitor {
    fn new(database: Database) -> Self {
        Self {
            last_known_state: Arc::new(Mutex::new(None)),
            database,
        }
    }

    // MODIFIED: Traite la ligne et déclenche l'envoi si l'état a changé
    fn process_line(&self, line: &str) {
        let trimmed_line = line.trim();

        // Tente de parser la ligne en booléen (0 -> false, 1 -> true)
        let current_state = match trimmed_line {
            "1" => Some(true),
            "0" => Some(false),
            _ => {
                // Ignore les lignes invalides
                eprintln!("✗ Invalid data received: '{}'. Expected '0' or '1'.", trimmed_line);
                return;
            }
        };

        if let Some(state) = current_state {
            let mut last_state_guard = self.last_known_state.lock().unwrap();
            
            // Vérifie si l'état a changé ou s'il s'agit du premier état reçu
            if last_state_guard.is_none() || last_state_guard.unwrap() != state {
                println!(
                    "🔄 Engine state changed to: {}",
                    if state { "RUNNING" } else { "STOPPED" }
                );

                // Met à jour l'état connu
                *last_state_guard = Some(state);

                // Déclenche l'envoi vers la base de données dans une nouvelle tâche asynchrone
                let db_clone = self.database.clone();
                tokio::spawn(async move {
                    match db_clone.insert_state(state).await {
                        Ok(_) => println!("✓ Engine state successfully uploaded."),
                        Err(e) => eprintln!("✗ Upload error: {}", e),
                    }
                });
            }
        }
    }
}

// ============================================================================
// Serial reader (inchangé, mais appellera la nouvelle logique)
// ============================================================================

struct SerialReader;
impl SerialReader {
    async fn read_continuous(
        port: Box<dyn serialport::SerialPort>,
        monitor: &EngineMonitor,
    ) -> Result<(), Box<dyn std::error::Error>> {
        let mut reader = BufReader::new(port);
        let mut line_buffer = String::new();
        
        println!("\n🔄 Reading engine state from serial port...");
        
        loop {
            // Utilise read_line pour être plus robuste
            match reader.read_line(&mut line_buffer) {
                Ok(0) => { // Connection closed
                    println!("Serial port connection closed.");
                    break;
                }
                Ok(_) => {
                    monitor.process_line(&line_buffer);
                    line_buffer.clear(); // Important: vider le buffer après lecture
                }
                Err(e) => {
                    if e.kind() == std::io::ErrorKind::TimedOut {
                        continue;
                    }
                    eprintln!("Error while reading serial port: {}", e);
                    break;
                }
            }
        }
        
        Ok(())
    }
}

// ============================================================================
// Application main (modifié pour utiliser EngineMonitor)
// ============================================================================

struct Application;
impl Application {
    async fn run() -> Result<(), Box<dyn std::error::Error>> {
        println!("🚀 Starting engine monitoring application");

        let settings = ConfigManager::load()?;

        SerialPortManager::display_available_ports();
        ConfigManager::display(&settings);

        let port = SerialPortManager::open_port(&settings.serial)?;
        println!("\n✓ Serial port successfully opened");

        let database = Database::new(&settings.database).await?;
        println!("✓ Database connection established");

        // NEW: Initialise le moniteur de moteur au lieu du processeur de données
        let monitor = EngineMonitor::new(database);
        println!("✓ Engine monitor started");

        // MODIFIED: Passe le moniteur au lecteur série
        SerialReader::read_continuous(port, &monitor).await?;

        Ok(())
    }
}

// ============================================================================
// Main function (inchangé)
// ============================================================================

#[tokio::main]
async fn main() -> Result<(), Box<dyn std::error::Error>> {
    if let Err(e) = Application::run().await {
        eprintln!("❌ Fatal error: {}", e);
        println!("\nPress Enter to exit...");
        let mut input = String::new();
        std::io::stdin().read_line(&mut input)?;
        std::process::exit(1);
    }
    
    println!("\nPress Enter to exit...");
    let mut input = String::new();
    std::io::stdin().read_line(&mut input)?;
    
    Ok(())
}