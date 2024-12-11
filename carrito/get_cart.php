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
    // Obtener el email del usuario desde los parámetros de la solicitud
    if (isset($_GET['email'])) {
        $email = $_GET['email'];

        // Consultar el carrito del usuario
        $sql = "SELECT * FROM carrito WHERE usuario = (SELECT id FROM usuario WHERE email = '$email')";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Si el carrito existe
            $carrito = $result->fetch_assoc();
            $productos = json_decode($carrito['productos'], true);
            $subTotal = $carrito['subTotal'];
            $iva = $carrito['iva'];
            $total = $carrito['total'];

            // sleccionar los productos del carrito
            $sql = "SELECT * FROM producto WHERE id IN (" . implode(',', $productos) . ")";
            $result = $conn->query($sql);
            $finalProducts = [];
            while ($row = $result->fetch_assoc()) {
                $finalProducts[] = $row;
            }

            // Preparar la respuesta
            $response = [
                "success" => true,
                "carrito" => [
                    "productos" => $finalProducts,
                    "subTotal" => $subTotal,
                    "iva" => $iva,
                    "total" => $total
                ]
            ];
            echo json_encode($response);
        } else {
            // Si no existe el carrito
            echo json_encode(["error" => "Carrito no encontrado"]);
        }
    } else {
        echo json_encode(["error" => "Correo electrónico no proporcionado"]);
    }
} else {
    echo json_encode(["error" => "Método no permitido"]);
}

// Cerrar conexión
$conn->close();
