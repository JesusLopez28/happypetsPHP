<?php
// Credenciales de la base de datos
$servername = "localhost";
$username = "happypets_user";
$password = "SeguraContrasena123!";
$database = "happy_pets";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida: " . $conn->connect_error]));
}

// Verificar el método de la solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $direccion = $_POST['direccion'];
    $metodo_pago = $_POST['metodo_pago'];
    $tipo_envio = $_POST['tipo_envio'];

    $id_usuario = $conn->query("SELECT id FROM usuario WHERE email = '$email'")->fetch_assoc()['id'];
    $carrito = $conn->query("SELECT id FROM carrito WHERE usuario = $id_usuario AND status = 1")->fetch_assoc()['id'];

    // Insertar la compra
    $sql = "INSERT INTO compra (carrito, direccion, metodo_pago, tipo_envio) VALUES ('$carrito', '$direccion', '$metodo_pago', '$tipo_envio')";
    if ($conn->query($sql) === TRUE) {
        $update = "UPDATE carrito SET status = 0 WHERE id = $carrito";
        $conn->query($update);

        echo json_encode(["success" => true, "message" => "Compra registrada"]);
    } else {
        echo json_encode(["error" => "Error al registrar la compra: " . $conn->error]);
    }
} else {
    echo json_encode(["error" => "Método no permitido"]);
}

// Cerrar conexión
$conn->close();
