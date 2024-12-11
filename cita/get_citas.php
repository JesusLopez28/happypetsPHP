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

    // Obtener el ID del usuario con base en el email
    $result = $conn->query("SELECT id FROM usuario WHERE email = '$email'");
    if ($result->num_rows > 0) {
        $user_id = $result->fetch_assoc()['id'];

        // Consultar las citas del usuario
        $citasResult = $conn->query("SELECT id, mascota, fecha, hora FROM cita WHERE usuario = $user_id");
        $citas = [];

        if ($citasResult->num_rows > 0) {
            while ($row = $citasResult->fetch_assoc()) {
                $mascota_id = $row['mascota'];

                // Obtener el nombre de la mascota asociado a cada cita
                $mascotaResult = $conn->query("SELECT nombre FROM mascotas WHERE id = $mascota_id");
                $mascotaNombre = $mascotaResult->num_rows > 0 ? $mascotaResult->fetch_assoc()['nombre'] : "Desconocido";

                // Agregar la cita con el nombre de la mascota
                $citas[] = [
                    "id" => $row['id'],
                    "fecha" => $row['fecha'],
                    "hora" => $row['hora'],
                    "mascota_nombre" => $mascotaNombre
                ];
            }
            echo json_encode(["success" => true, "data" => $citas]);
        } else {
            echo json_encode(["success" => true, "data" => []]);
        }
    } else {
        echo json_encode(["error" => "Usuario no encontrado"]);
    }
} else {
    echo json_encode(["error" => "Método no permitido"]);
}

// Cerrar conexión
$conn->close();
?>
