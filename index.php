<?php
// index.php - Router principal
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/utils/Response.php';
require_once __DIR__ . '/utils/validator.php';

// Obtener la ruta solicitada
$request_uri = $_SERVER['REQUEST_URI'];

// Obtener el path sin query string
$uri_path = parse_url($request_uri, PHP_URL_PATH);

// Remover /backendVistaMontana y /index.php si existen
$path = preg_replace('#^/backendVistaMontana#', '', $uri_path);
$path = preg_replace('#^/index\.php#', '', $path);

// Asegurar que siempre empiece con /
$path = '/' . trim($path, '/');

$method = $_SERVER['REQUEST_METHOD'];

// Debug - Descomentar si necesitas revisar las rutas
// error_log("=== DEBUG ===");
// error_log("REQUEST_URI: " . $_SERVER['REQUEST_URI']);
// error_log("Path procesado: " . $path);
// error_log("Method: " . $method);
// error_log("=============");

// Router simple
switch (true) {
    // Root
    case $path === '/' && $method === 'GET':
        Response::success([
            'message' => 'API Hospedaje Vista Montaña',
            'version' => '1.0',
            'endpoints' => [
                'GET /alquileres' => 'Obtener todos los alquileres',
                'GET /alquileres/{id}' => 'Obtener un alquiler específico',
                'POST /reservas' => 'Crear una reserva',
                'GET /lugares' => 'Obtener lugares turísticos',
                'POST /contacto' => 'Enviar mensaje de contacto'
            ]
        ]);
        break;

    // Alquileres
    case $path === '/alquileres' && $method === 'GET':
        require __DIR__ . '/controllers/alquileres.php';
        obtenerAlquileres();
        break;
    
    case preg_match('/^\/alquileres\/(\d+)$/', $path, $matches) && $method === 'GET':
        require __DIR__ . '/controllers/alquileres.php';
        obtenerAlquiler($matches[1]);
        break;

    // Reservas
    case $path === '/reservas' && $method === 'POST':
        require __DIR__ . '/controllers/reservas.php';
        crearReserva();
        break;

    case $path === '/reservas' && $method === 'GET':
        require __DIR__ . '/controllers/reservas.php';
        obtenerReservas();
        break;

    case preg_match('/^\/reservas\/(\d+)$/', $path, $matches) && $method === 'PUT':
        require __DIR__ . '/controllers/reservas.php';
        actualizarReserva($matches[1]);
        break;

    case $path === '/reservas/disponibilidad' && $method === 'POST':
        require __DIR__ . '/controllers/reservas.php';
        verificarDisponibilidad();
        break;

    // Lugares turísticos
    case $path === '/lugares' && $method === 'GET':
        require __DIR__ . '/controllers/lugares.php';
        obtenerLugares();
        break;

    case preg_match('/^\/lugares\/(\d+)$/', $path, $matches) && $method === 'GET':
        require __DIR__ . '/controllers/lugares.php';
        obtenerLugar($matches[1]);
        break;

    case $path === '/lugares' && $method === 'POST':
        require __DIR__ . '/controllers/lugares.php';
        crearLugar();
        break;

    // Contacto
    case $path === '/contacto' && $method === 'POST':
        require __DIR__ . '/controllers/contacto.php';
        enviarMensajeContacto();
        break;

    default:
        Response::error('Endpoint no encontrado: ' . $path . ' [Method: ' . $method . ']', 404);
        break;
}
?>