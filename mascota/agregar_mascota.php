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
    $nombre = $_POST['nombre'];
    $raza = $_POST['raza'];
    $edad = $_POST['edad'];
    $sql = "SELECT * FROM usuario WHERE email = '$email'";
    $result = $conn->query($sql);

    // sacar sus campo json mascota y ver si tiene mascota
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id = $row['id'];
        $mascota = json_decode($row['mascota'], true);

        // Crear mascota
        $sql = "INSERT INTO mascotas (nombre, raza, edad) VALUES ('$nombre', '$raza', '$edad')";
        if ($conn->query($sql) === TRUE) {

            $last_mascota_sql = "SELECT * FROM mascotas ORDER BY id DESC LIMIT 1";
            $last_mascota_result = $conn->query($last_mascota_sql);
            $last_mascota = $last_mascota_result->fetch_assoc();
            $mascota[] = $last_mascota['id'];
            $sql = "UPDATE usuario SET mascota = '" . json_encode($mascota) . "' WHERE id = $id";
            if ($conn->query($sql) === TRUE) {
                echo json_encode(["success" => true, "message" => "Mascota agregada correctamente"]);
            } else {
                echo json_encode(["error" => "Error al agregar mascota"]);
            }
        } else {
            echo json_encode(["error" => "Error al agregar mascota"]);
        }

    } else {
        echo json_encode(["error" => "Usuario no encontrado"]);
    }
} else {
    echo json_encode(["error" => "Método no permitido"]);
}

// Cerrar conexión
$conn->close();
