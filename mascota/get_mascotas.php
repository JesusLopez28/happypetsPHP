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
    // recibir el email y consultar usuario
    $email = $_POST['email'];
    $sql = "SELECT * FROM usuario WHERE email = '$email'";
    $result = $conn->query($sql);

    // sacar sus campo json mascota y ver si tiene mascota
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $mascota = json_decode($row['mascota'], true);
        if ($mascota) {
            // consultar las mascotas
            $sql = "SELECT * FROM mascotas WHERE id IN (" . implode(',', $mascota) . ")";
            $result = $conn->query($sql);
            $mascotas = [];
            while ($row = $result->fetch_assoc()) {
                $mascotas[] = $row;
            }
            echo json_encode(["success" => true, "data" => $mascotas]);
        } else {
            echo json_encode(["error" => "No tiene mascota"]);
        }
    } else {
        echo json_encode(["error" => "Usuario no encontrado"]);
    }
} else {
    echo json_encode(["error" => "Método no permitido"]);
}

// Cerrar conexión
$conn->close();
