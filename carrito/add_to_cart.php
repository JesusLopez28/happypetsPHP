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
    $id_producto = $_POST['id_producto'];
    $email = $_POST['email'];
    $cantidad = 1;

    // Verificar si el usuario ya tiene un carrito
    $sql = "SELECT * FROM carrito WHERE usuario = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Usuario existente
        $carrito = $result->fetch_assoc();
        $productos = json_decode($carrito['productos'], true);
        $subTotal = $carrito['subTotal'];
        $iva = $carrito['iva'];
        $total = $carrito['total'];
        $status = $carrito['status'];

        // Verificar si el producto ya está en el carrito
        if (array_key_exists($id_producto, $productos)) {
            $productos[$id_producto] += $cantidad;
        } else {
            $productos[$id_producto] = $cantidad;
        }

        // Actualizar el carrito
        $subTotal += $productos[$id_producto]['precio'] * $cantidad;
        $iva = $subTotal * 0.16;
        $total = $subTotal + $iva;

        $id_usuario = $conn->query("SELECT id FROM usuario WHERE email = '$email'")->fetch_assoc()['id'];

        $sql = "UPDATE carrito SET productos = '" . json_encode($productos) . "', subTotal = $subTotal, iva = $iva, total = $total WHERE usuario = $id_usuario";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Producto agregado al carrito"]);
        } else {
            echo json_encode(["error" => "Error al agregar producto al carrito"]);
        }
    } else {
        // Consultar el producto
        $sql = "SELECT * FROM producto WHERE id = $id_producto";
        $result = $conn->query($sql);

        if ($result->num_rows === 0) {
            echo json_encode(["error" => "Producto no encontrado"]);
            exit;
        }

        $producto = $result->fetch_assoc();
        $productos = [$id_producto => $cantidad];
        $subTotal = $producto['precio'] * $cantidad;
        $iva = $subTotal * 0.16;
        $total = $subTotal + $iva;
        $status = 1;

        $id_usuario = $conn->query("SELECT id FROM usuario WHERE email = '$email'")->fetch_assoc()['id'];

        $queryCarrito = "INSERT INTO carrito (usuario, productos, subTotal, iva, total, status) 
                         VALUES ($id_usuario, '" . json_encode($productos) . "', $subTotal, $iva, $total, $status)";
        if ($conn->query($queryCarrito)) {
            echo json_encode(["success" => true, "message" => "Producto agregado al carrito"]);
        } else {
            echo json_encode(["error" => "Error al agregar producto al carrito: " . $conn->error]);
        }
    }
} else {
    echo json_encode(["error" => "Método no permitido"]);
}

// Cerrar conexión
$conn->close();
