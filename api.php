<?php
$port = '/dev/ttyUSB0';  //À adapter selon le pc
$baud = 9600;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commande'])) {
    $cmd = $_POST['commande'];

    $handle = fopen($port, 'w+');
    if (!$handle) {
        http_response_code(500);
        echo json_encode(["error" => "Erreur ouverture du port"]);
        exit;
    }

    exec("stty -F $port $baud");
    fwrite($handle, $cmd . "\n");
    fflush($handle);

    usleep(150000);  // 150 ms
    $response = fgets($handle);
    fclose($handle);

    echo $response ?: json_encode(["error" => "Pas de réponse"]);
}
?>
