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
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $categoria = $_POST['categoria'];

    // Insertar el producto
    $queryProducto = "INSERT INTO producto (nombre, descripcion, precio, stock, categoria) 
                      VALUES ('$nombre', '$descripcion', $precio, $stock, '$categoria')";

    if (!$conn->query($queryProducto)) {
        echo json_encode(["error" => "Error al insertar el producto: " . $conn->error]);
        exit;
    }

    // Obtener el ID del producto recién creado
    $sql = "SELECT id FROM producto WHERE nombre = '$nombre' AND categoria = '$categoria'";
    $result = $conn->query($sql);
    $idProducto = $result->fetch_assoc()['id'];

    // Respuesta de éxito
    echo json_encode([
        "success" => true,
        "message" => "Producto creado exitosamente",
        "idProducto" => $idProducto
    ]);
} else {
    echo json_encode(['error' => 'Método no permitido o acción no especificada']);
}

// Cerrar conexión
$conn->close();
