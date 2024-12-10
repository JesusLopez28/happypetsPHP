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
    // Obtener datos desde POST
    $googleEmail = $_POST['email']; // Email proporcionado por la API de Google

    // Validar entrada
    if (!filter_var($googleEmail, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["error" => "Correo electrónico no válido"]);
        exit;
    }

    // Verificar si el usuario ya existe
    $sql = "SELECT * FROM usuario WHERE email = '$googleEmail'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Usuario existente
        $usuario = $result->fetch_assoc();
        echo json_encode(["status" => "success", "message" => "Usuario encontrado", "data" => $usuario]);
    } else {
        // Usuario no encontrado, registrar con datos por defecto
        $defaultName = "Usuario de Google";
        $defaultPassword = password_hash("password123", PASSWORD_BCRYPT); // Contraseña encriptada por defecto

        $insertSql = "INSERT INTO usuario (email, password, nombre) VALUES ('$googleEmail', '$defaultPassword', '$defaultName')";
        if ($conn->query($insertSql) === TRUE) {
            $newUserId = $conn->insert_id;
            echo json_encode([
                "status" => "success",
                "message" => "Usuario registrado con datos por defecto",
                "data" => [
                    "id" => $newUserId,
                    "email" => $googleEmail,
                    "nombre" => $defaultName
                ]
            ]);
        } else {
            echo json_encode(["error" => "Error al registrar usuario: " . $conn->error]);
        }
    }
} else {
    echo json_encode(["error" => "Método no permitido"]);
}

$conn->close();
