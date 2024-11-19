<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include 'config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['deck_id']) && isset($data['cartas'])) {
    $deck_id = $data['deck_id'];
    $cartas = $data['cartas'];

    $stmt = $conn->prepare("DELETE FROM deck_cards WHERE deck_id = ?");
    $stmt->bind_param("i", $deck_id);
    $stmt->execute();

    $stmt = $conn->prepare("INSERT INTO deck_cards (deck_id, card_id) VALUES (?, ?)");
    foreach ($cartas as $carta) {
        $stmt->bind_param("ii", $deck_id, $carta['card_id']);
        $stmt->execute();
    }

    echo json_encode(['message' => 'Baraja actualizada correctamente']);
} else {
    echo json_encode(['message' => 'Datos incompletos o incorrectos']);
}
?>
