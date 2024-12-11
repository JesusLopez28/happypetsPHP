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

    // Verificar si el usuario tiene un carrito
    $sql = "SELECT * FROM carrito WHERE usuario = (SELECT id FROM usuario WHERE email = '$email')";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $carrito = $result->fetch_assoc();
        $productos = json_decode($carrito['productos'], true);

        // Detallar información de cada producto en el carrito
        $detalleProductos = [];
        foreach ($productos as $id_producto => $cantidad) {
            $sqlProducto = "SELECT * FROM producto WHERE id = $id_producto";
            $resultProducto = $conn->query($sqlProducto);

            if ($resultProducto->num_rows > 0) {
                $producto = $resultProducto->fetch_assoc();
                $detalleProductos[] = [
                    "id" => $producto['id'],
                    "nombre" => $producto['nombre'],
                    "precio" => $producto['precio'],
                    "cantidad" => $cantidad,
                    "subtotal" => $producto['precio'] * $cantidad
                ];
            }
        }

        echo json_encode([
            "success" => true,
            "carrito" => [
                "productos" => $detalleProductos,
                "subTotal" => $carrito['subTotal'],
                "iva" => $carrito['iva'],
                "total" => $carrito['total']
            ]
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "El carrito está vacío"]);
    }
} else {
    echo json_encode(["error" => "Método no permitido"]);
}

// Cerrar conexión
$conn->close();
