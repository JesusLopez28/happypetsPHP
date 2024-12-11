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
    // Obtener el correo de la autenticación de Google
    $email = $_POST['email'] ?? null;

    if (!$email) {
        echo json_encode(["error" => "Correo no proporcionado"]);
        exit;
    }

    // Verificar si el usuario ya existe
    $sql = "SELECT * FROM usuario WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Usuario existente
        $usuario = $result->fetch_assoc();
        echo json_encode([
            "success" => true,
            "message" => "Usuario ya registrado",
            "data" => $usuario
        ]);
    } else {
        // Registrar nuevo usuario con datos predeterminados
        $nombre = $_POST['nombre'] ?? "Usuario Google";
        $telefono = $_POST['telefono'] ?? "0000000000";
        $password = "autenticadoGoogle";
        $direccion = "Sin dirección";
        $type = 1; // Tipo de usuario (2: Usuario)

        $queryUsuario = "INSERT INTO usuario (nombre, email, telefono, password, direccion, type) 
                         VALUES ('$nombre', '$email', '$telefono', '$password', '$direccion', $type)";
        if ($conn->query($queryUsuario)) {

            $sql = "SELECT * FROM usuario WHERE email = '$email'";
            // Usuario existente
            $result = $conn->query($sql);
            $usuario = $result->fetch_assoc();
            echo json_encode([
                "success" => true,
                "message" => "Usuario ya registrado",
                "data" => $usuario
            ]);
        } else {
            echo json_encode(["error" => "Error al registrar el usuario: " . $conn->error]);
        }
    }
} else {
    echo json_encode(["error" => "Método no permitido"]);
}

// Cerrar conexión
$conn->close();
