<?php
// test.php - Guardar en C:\xampp\htdocs\backendVistaMontana\test.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Test 1: PHP funciona<br>";

// Test 2: Cargar database
require_once __DIR__ . '/config/database.php';
echo "Test 2: Database.php cargado<br>";

// Test 3: Conectar a la base de datos
$database = new Database();
$db = $database->getConnection();
echo "Test 3: Conexión a BD exitosa<br>";

// Test 4: Query simple
$query = "SELECT COUNT(*) as total FROM alquileres";
$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Test 4: Hay " . $result['total'] . " alquileres en la BD<br>";

// Test 5: Cargar Response
require_once __DIR__ . '/utils/Response.php';
echo "Test 5: Response.php cargado<br>";

echo "<br>✅ Todos los tests pasaron!";
?>