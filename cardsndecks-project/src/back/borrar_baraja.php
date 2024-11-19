<?php

include 'config.php';


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}


$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['deck_id'])) {
    $deck_id = $data['deck_id'];

    try {
        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        $conn->begin_transaction();

        $sql_delete_cards = "DELETE FROM deck_cards WHERE deck_id = ?";
        $stmt = $conn->prepare($sql_delete_cards);
        $stmt->bind_param("i", $deck_id);
        $stmt->execute();

        $sql_delete_deck = "DELETE FROM decks WHERE id = ?";
        $stmt = $conn->prepare($sql_delete_deck);
        $stmt->bind_param("i", $deck_id);
        $stmt->execute();

        $conn->commit();

        $stmt->close();
        $conn->close();

        echo json_encode(array("message" => "Baraja borrada exitosamente."));
    } catch (Exception $e) {
        $conn->rollback();
        $conn->close();

        echo json_encode(array("message" => "Error al borrar la baraja: " . $e->getMessage()));
    }
} else {
    echo json_encode(array("message" => "ID de baraja no proporcionado."));
}
?>
