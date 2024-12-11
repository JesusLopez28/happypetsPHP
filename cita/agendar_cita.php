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
    $mascota_id = $_POST['mascota_id'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $email = $_POST['email'];

    $id = $conn->query("SELECT id FROM usuario WHERE email = '$email'")->fetch_assoc()['id'];

    $sql = "INSERT INTO cita (usuario, mascota, fecha, hora) VALUES ('$id', '$mascota_id', '$fecha', '$hora')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Cita agendada correctamente"]);
    } else {
        echo json_encode(["error" => "Error al agendar cita"]);
    }
} else {
    echo json_encode(["error" => "Método no permitido"]);
}

// Cerrar conexión
$conn->close();
