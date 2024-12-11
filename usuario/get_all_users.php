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
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM usuario";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $usuarios = [];
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
        echo json_encode(["success" => true, "data" => $usuarios]);
    } else {
        echo json_encode(["error" => "No hay usuarios registrados"]);
    }
} else {
    echo json_encode(["error" => "Método no permitido"]);
}

// Cerrar conexión
$conn->close();
