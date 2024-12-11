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
    //  .add("email", email.toString())
    //.add("producto_id", producto["id"].toString())
    $email = $_POST['email'];
    $producto_id = $_POST['producto_id'];

    $id_usuario = $conn->query("SELECT id FROM usuario WHERE email = '$email'")->fetch_assoc()['id'];

    // Borrar el id del array de productos 
    $sql = "SELECT * FROM carrito WHERE usuario = $id_usuario AND status = 1";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $carrito = $result->fetch_assoc();
        $productos = json_decode($carrito['productos'], true);
        $productos = array_diff($productos, [$producto_id]);
        $sql = "UPDATE carrito SET productos = '" . json_encode(array_values($productos)) . "' WHERE usuario = $id_usuario";
        if ($conn->query($sql) === TRUE) {
            // si el carrito se queda vacío, borrarlo
            if (count($productos) === 0) {
                $sql = "DELETE FROM carrito WHERE usuario = $id_usuario";
                if ($conn->query($sql) === TRUE) {
                    echo json_encode(["success" => true, "message" => "Producto eliminado del carrito"]);
                } else {
                    echo json_encode(["error" => "Error al eliminar producto del carrito"]);
                }
            }
        } else {
            echo json_encode(["error" => "Error al eliminar producto del carrito"]);
        }
    } else {
        echo json_encode(["error" => "Carrito no encontrado"]);
    }
} else {
    echo json_encode(["error" => "Método no permitido"]);
}