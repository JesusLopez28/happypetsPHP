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
    $id = $_POST['id'] ?? null;
    $stock = $_POST['stock'] ?? null;

    if (!$id) {
        echo json_encode(["error" => "ID no proporcionado"]);
        exit;
    }

    if (!$stock) {
        echo json_encode(["error" => "Stock no proporcionado"]);
        exit;
    }

    $sql = "UPDATE producto SET stock = $stock WHERE id = $id";
    $result = $conn->query($sql);

    if ($conn->affected_rows > 0) {
        echo json_encode(["success" => true, "message" => "Stock de producto actualizado"]);
    } else {
        echo json_encode(["error" => "Producto no encontrado"]);
    }
} else {
    echo json_encode(["error" => "Método no permitido"]);
}

// Cerrar conexión
$conn->close();
