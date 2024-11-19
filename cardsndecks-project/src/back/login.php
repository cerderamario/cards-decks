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

$username = $data['username'];
$password = $data['password'];

$sql = "SELECT * FROM usuarios WHERE username = '$username'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        echo json_encode(["message" => "Login exitoso", "user" => $user]);
    } else {
        echo json_encode(["message" => "Contraseña incorrecta"]);
    }
} else {
    echo json_encode(["message" => "Usuario no encontrado"]);
}

$conn->close();
?>
