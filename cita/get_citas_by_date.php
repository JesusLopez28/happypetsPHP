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
    // Obtener la fecha desde los parámetros de la URL
    $fecha = $_GET['fecha'];

    // Consultar las citas para la fecha especificada
    $query = "
        SELECT c.id, c.hora, c.fecha, u.nombre AS usuario_nombre, m.nombre AS mascota_nombre
        FROM cita c
        INNER JOIN usuario u ON c.usuario = u.id
        INNER JOIN mascotas m ON c.mascota = m.id
        WHERE c.fecha = '$fecha'
    ";

    $result = $conn->query($query);
    $citas = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $citas[] = [
                "id" => $row['id'],
                "fecha" => $row['fecha'],
                "hora" => $row['hora'],
                "usuario" => $row['usuario_nombre'],
                "mascota" => $row['mascota_nombre']
            ];
        }
        echo json_encode(["success" => true, "data" => $citas]);
    } else {
        echo json_encode(["success" => true, "data" => []]);
    }
} else {
    echo json_encode(["error" => "Método no permitido"]);
}

// Cerrar conexión
$conn->close();
