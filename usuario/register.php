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
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $password = $_POST['password'];
    $direccion = $_POST['direccion'];
    $nombre_mascota = $_POST['nombre_mascota'];
    $raza_mascota = $_POST['raza_mascota'];
    $edad_mascota = $_POST['edad_mascota'];
    $type = 1;

    // Insertar el usuario
    $queryUsuario = "INSERT INTO usuario (nombre, email, telefono, password, direccion, type) 
                         VALUES ('$nombre', '$email', '$telefono', '$password', '$direccion', $type)";
    if (!$conn->query($queryUsuario)) {
        echo json_encode(["error" => "Error al insertar el usuario: " . $conn->error]);
        exit;
    }

    // Obtener el ID del usuario recién creado
    $sql = "SELECT id FROM usuario WHERE email = '$email'";
    $result = $conn->query($sql);
    $idUsuario = $result->fetch_assoc()['id'];

    // Insertar las mascotas y recopilar sus IDs
    $mascotaIds = [];

    $queryMascota = "INSERT INTO mascotas (nombre, raza, edad, idUsuario) 
                             VALUES ('$nombre_mascota', '$raza_mascota', $edad_mascota, $idUsuario)";
    if ($conn->query($queryMascota)) {
        $sql = "SELECT id FROM mascotas WHERE nombre = '$nombre_mascota' AND raza = '$raza_mascota' AND edad = $edad_mascota";
        $result = $conn->query($sql);
        $mascotaId = $result->fetch_assoc()['id'];
        array_push($mascotaIds, $mascotaId);
    } else {
        echo json_encode(["error" => "Error al insertar la mascota: " . $conn->error]);
        exit;
    }


    // Actualizar el campo mascota del usuario con los IDs en formato JSON
    $mascotaJson = json_encode($mascotaIds);
    $queryActualizarMascota = "UPDATE usuario SET mascota = '$mascotaJson' WHERE id = $idUsuario";

    if (!$conn->query($queryActualizarMascota)) {
        echo json_encode(["error" => "Error al actualizar el campo mascota: " . $conn->error]);
        exit;
    }

    // Respuesta de éxito
    echo json_encode([
        "success" => true,
        "message" => "Usuario y mascotas creados exitosamente",
        "idUsuario" => $idUsuario,
        "mascotaIds" => $mascotaIds
    ]);
} else {
    echo json_encode(['error' => 'Método no permitido o acción no especificada']);
}

// Cerrar conexión
$conn->close();
