<?php
require_once '../backendVistaMontana/utils/response.php';
require_once '../backendVistaMontana/utils/validator.php';

// controllers/lugares.php

function obtenerLugares() {
    $database = new Database();
    $db = $database->getConnection();

    $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : null;

    $query = "SELECT * FROM lugares_turisticos WHERE activo = 1";
    
    if ($tipo) {
        $query .= " AND tipo = :tipo";
    }
    
    $query .= " ORDER BY distancia_km";

    $stmt = $db->prepare($query);
    
    if ($tipo) {
        $stmt->bindParam(':tipo', $tipo);
    }
    
    $stmt->execute();

    $lugares = $stmt->fetchAll(PDO::FETCH_ASSOC);
    Response::success($lugares);
}

function obtenerLugar($id) {
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT * FROM lugares_turisticos WHERE id = :id AND activo = 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $lugar = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$lugar) {
        Response::error('Lugar turístico no encontrado', 404);
    }

    Response::success($lugar);
}

function crearLugar() {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $database = new Database();
    $db = $database->getConnection();

    // Verificar autenticación
    // if (!estaAutenticado()) {
    //     Response::error('No autorizado', 401);
    // }

    $query = "INSERT INTO lugares_turisticos 
              (nombre, descripcion, latitud, longitud, imagen, tipo, distancia_km, duracion_visita, dificultad)
              VALUES 
              (:nombre, :descripcion, :latitud, :longitud, :imagen, :tipo, :distancia_km, :duracion_visita, :dificultad)";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':nombre', $data['nombre']);
    $stmt->bindParam(':descripcion', $data['descripcion']);
    $stmt->bindParam(':latitud', $data['latitud']);
    $stmt->bindParam(':longitud', $data['longitud']);
    $stmt->bindParam(':imagen', $data['imagen']);
    $stmt->bindParam(':tipo', $data['tipo']);
    $stmt->bindParam(':distancia_km', $data['distancia_km']);
    $stmt->bindParam(':duracion_visita', $data['duracion_visita']);
    $stmt->bindParam(':dificultad', $data['dificultad']);

    if ($stmt->execute()) {
        Response::success(['id' => $db->lastInsertId()], 'Lugar turístico creado exitosamente');
    } else {
        Response::error('Error al crear el lugar turístico', 500);
    }
}

?>