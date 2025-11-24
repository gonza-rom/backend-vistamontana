<?php
// index.php - Router principal
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/utils/Response.php';
require_once __DIR__ . '/utils/validator.php';

// Obtener la ruta solicitada
$request_uri = $_SERVER['REQUEST_URI'];
$base_path = '/backendVistaMontana'; // Ajusta según tu estructura
$path = str_replace($base_path, '', parse_url($request_uri, PHP_URL_PATH));
$method = $_SERVER['REQUEST_METHOD'];

// Router simple
switch (true) {
    // Alquileres
    case preg_match('/^\/alquileres$/', $path) && $method === 'GET':
        require __DIR__ . '/controllers/alquileres.php';
        obtenerAlquileres();
        break;
    
    case preg_match('/^\/alquileres\/(\d+)$/', $path, $matches) && $method === 'GET':
        require __DIR__ . '/controllers/alquileres.php';
        obtenerAlquiler($matches[1]);
        break;

    // Reservas
    case preg_match('/^\/reservas$/', $path) && $method === 'POST':
        require __DIR__ . '/controllers/reservas.php';
        crearReserva();
        break;

    case preg_match('/^\/reservas$/', $path) && $method === 'GET':
        require __DIR__ . '/controllers/reservas.php';
        obtenerReservas();
        break;

    case preg_match('/^\/reservas\/(\d+)$/', $path, $matches) && $method === 'PUT':
        require __DIR__ . '/controllers/reservas.php';
        actualizarReserva($matches[1]);
        break;

    case preg_match('/^\/reservas\/disponibilidad$/', $path) && $method === 'POST':
        require __DIR__ . '/controllers/reservas.php';
        verificarDisponibilidad();
        break;

    // Lugares turísticos
    case preg_match('/^\/lugares$/', $path) && $method === 'GET':
        require __DIR__ . '/controllers/lugares.php';
        obtenerLugares();
        break;

    case preg_match('/^\/lugares\/(\d+)$/', $path, $matches) && $method === 'GET':
        require __DIR__ . '/controllers/lugares.php';
        obtenerLugar($matches[1]);
        break;

    case preg_match('/^\/lugares$/', $path) && $method === 'POST':
        require __DIR__ . '/controllers/lugares.php';
        crearLugar();
        break;

    // Contacto
    case preg_match('/^\/contacto$/', $path) && $method === 'POST':
        require __DIR__ . '/controllers/contacto.php';
        enviarMensajeContacto();
        break;

    default:
        Response::error('Endpoint no encontrado: ' . $path, 404);
        break;
}
?>