<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

include 'config.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if ($data === null) {
    echo json_encode(["message" => "No se pudieron decodificar los datos."]);
    exit;
}

if (isset($data['user_id']) && isset($data['deck_name']) && isset($data['cards'])) {
    $user_id = intval($data['user_id']);
    $deck_name = $data['deck_name'];
    $cards = $data['cards'];


    if (empty($user_id) || empty($deck_name) || empty($cards)) {
        echo json_encode(["message" => "Datos incompletos o incorrectos"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        echo json_encode(["message" => "El usuario no existe."]);
        $stmt->close();
        exit;
    }
    $stmt->close();

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("INSERT INTO decks (user_id, name) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $deck_name);
        $stmt->execute();
        $deck_id = $stmt->insert_id;
        $stmt->close();

        $stmt = $conn->prepare("INSERT INTO deck_cards (deck_id, card_id) VALUES (?, ?)");

        foreach ($cards as $card) {
            $card_id = intval($card['id']);
            $stmt_check = $conn->prepare("SELECT id FROM cards WHERE id = ?");
            $stmt_check->bind_param("i", $card_id);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows === 0) {
                $stmt_insert_card = $conn->prepare("INSERT INTO cards (id) VALUES (?)");
                $stmt_insert_card->bind_param("i", $card_id);
                $stmt_insert_card->execute();
                $stmt_insert_card->close();
            }
            $stmt_check->close();

            $stmt->bind_param("ii", $deck_id, $card_id);
            $stmt->execute();
        }
        $stmt->close();

        $conn->commit();

        echo json_encode(["message" => "Baraja guardada correctamente"]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["message" => "Error al guardar la baraja", "error" => $e->getMessage()]);
    }

    $conn->close();
} else {
    echo json_encode(["message" => "Datos incompletos o incorrectos"]);
}
?>
