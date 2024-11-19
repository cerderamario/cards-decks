<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($user_id == 0) {
    echo json_encode(["message" => "user_id no proporcionado o no válido."]);
    exit;
}

$sql = "SELECT d.id, d.name, dc.card_id
        FROM decks d
        JOIN deck_cards dc ON d.id = dc.deck_id
        WHERE d.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$barajas = [];
$cartas = [];

while ($row = $result->fetch_assoc()) {
    $deck_id = $row['id'];
    $deck_name = $row['name'];
    $card_id = $row['card_id'];

    if (!isset($barajas[$deck_id])) {
        $barajas[$deck_id] = [
            'id' => $deck_id,
            'name' => $deck_name,
            'cartas' => []
        ];
    }

    $cartas[] = $card_id;
    $barajas[$deck_id]['cartas'][] = $card_id;
}

$stmt->close();
$conn->close();

if (count($cartas) > 0) {
    $cartas_str = implode(',', array_unique($cartas));
    $api_url = "https://db.ygoprodeck.com/api/v7/cardinfo.php?id={$cartas_str}";
    $response = file_get_contents($api_url);
    $data = json_decode($response, true);

    $cartas_info = [];
    foreach ($data['data'] as $carta) {
        $cartas_info[$carta['id']] = $carta;
    }

    foreach ($barajas as &$baraja) {
        foreach ($baraja['cartas'] as &$card_id) {
            $card_id = $cartas_info[$card_id];
        }
    }
}

echo json_encode(array_values($barajas));
?>
