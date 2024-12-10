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
            "usuario" => $usuario
        ]);
    } else {
        // Registrar nuevo usuario con datos predeterminados
        $nombre = "Usuario Google";
        $telefono = "0000000000";
        $password = "autenticadoGoogle";
        $direccion = "Sin dirección";
        $type = 2; // Tipo de usuario (2: Usuario)

        $queryUsuario = "INSERT INTO usuario (nombre, email, telefono, password, direccion, type) 
                         VALUES ('$nombre', '$email', '$telefono', '$password', '$direccion', $type)";
        if ($conn->query($queryUsuario)) {
            // Obtener el ID del usuario recién creado
            $idUsuario = $conn->insert_id;

            echo json_encode([
                "success" => true,
                "message" => "Usuario registrado con éxito",
                "idUsuario" => $idUsuario,
                "email" => $email
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
