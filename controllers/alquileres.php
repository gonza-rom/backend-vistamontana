<?php
// controllers/alquileres.php
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/validator.php';

function obtenerAlquileres() {
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT 
                a.*,
                h.nombre as habitacion_nombre,
                h.fotos as habitacion_fotos
              FROM alquileres a
              LEFT JOIN habitaciones h ON a.habitacion_id = h.id
              WHERE a.activo = 1
              ORDER BY a.tipo, a.id";

    $stmt = $db->prepare($query);
    $stmt->execute();

    $alquileres = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['habitacion_fotos']) {
            $row['habitacion_fotos'] = json_decode($row['habitacion_fotos']);
        }
        $alquileres[] = $row;
    }

    Response::success($alquileres);
}

function obtenerAlquiler($id) {
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT 
                a.*,
                h.nombre as habitacion_nombre,
                h.fotos as habitacion_fotos,
                h.descripcion as habitacion_descripcion
              FROM alquileres a
              LEFT JOIN habitaciones h ON a.habitacion_id = h.id
              WHERE a.id = :id AND a.activo = 1";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    $alquiler = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$alquiler) {
        Response::error('Alquiler no encontrado', 404);
    }

    if ($alquiler['habitacion_fotos']) {
        $alquiler['habitacion_fotos'] = json_decode($alquiler['habitacion_fotos']);
    }

    Response::success($alquiler);
}
?>