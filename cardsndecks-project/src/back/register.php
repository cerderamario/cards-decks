<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array('message' => 'Método no permitido'));
    exit;
}

include 'config.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (empty($data['username']) || empty($data['password']) || empty($data['email'])) {
    http_response_code(400);
    echo json_encode(array('message' => 'Por favor, completa todos los campos'));
    exit;
}

$username = mysqli_real_escape_string($conn, $data['username']);
$password = password_hash($data['password'], PASSWORD_DEFAULT);
$email = mysqli_real_escape_string($conn, $data['email']);

$sql_check_user = "SELECT * FROM usuarios WHERE username = '$username'";
$result_check_user = $conn->query($sql_check_user);

if ($result_check_user->num_rows > 0) {
    http_response_code(400);
    echo json_encode(array('message' => 'El nombre de usuario ya está en uso'));
    exit;
}


$sql_insert_user = "INSERT INTO usuarios (username, password, email) VALUES ('$username', '$password', '$email')";

if ($conn->query($sql_insert_user) === TRUE) {
    $to = $email;
    $subject = 'Bienvenido a nuestro sitio';
    $message = 'Hola ' . $username . ', bienvenido a Cards&Decks. Esperamos que disfrutes creando tus mazos';
    $headers = 'From: cards&decks@gmail.com';

    if (mail($to, $subject, $message, $headers)) {
        http_response_code(201);
        echo json_encode(array('message' => 'Usuario registrado correctamente. Te hemos enviado un correo de bienvenida ;)'));
    } else {
        http_response_code(500);
        echo json_encode(array('message' => 'Error al registrar el usuario.'));
    }
} else {
    http_response_code(500);
    echo json_encode(array('message' => 'Error al registrar el usuario: ' . $conn->error));
}

$conn->close();
?>
