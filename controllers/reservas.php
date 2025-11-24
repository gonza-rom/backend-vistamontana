<?php
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/validator.php';

// controllers/reservas.php
function verificarDisponibilidad() {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT COUNT(*) as total
              FROM reservas
              WHERE alquiler_id = :alquiler_id
              AND estado != 'cancelada'
              AND (
                  (fecha_entrada <= :fecha_entrada AND fecha_salida > :fecha_entrada)
                  OR (fecha_entrada < :fecha_salida AND fecha_salida >= :fecha_salida)
                  OR (fecha_entrada >= :fecha_entrada AND fecha_salida <= :fecha_salida)
              )";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':alquiler_id', $data['alquiler_id']);
    $stmt->bindParam(':fecha_entrada', $data['fecha_entrada']);
    $stmt->bindParam(':fecha_salida', $data['fecha_salida']);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $disponible = $result['total'] == 0;

    Response::success([
        'disponible' => $disponible,
        'mensaje' => $disponible ? 'Disponible para las fechas seleccionadas' : 'No disponible para las fechas seleccionadas'
    ]);
}

function crearReserva() {
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Validar datos
    $errors = Validator::validateReserva($data);
    if (!empty($errors)) {
        Response::error(implode(', ', $errors), 400);
    }

    $database = new Database();
    $db = $database->getConnection();

    // Verificar disponibilidad
    $query = "SELECT COUNT(*) as total
              FROM reservas
              WHERE alquiler_id = :alquiler_id
              AND estado != 'cancelada'
              AND (
                  (fecha_entrada <= :fecha_entrada AND fecha_salida > :fecha_entrada)
                  OR (fecha_entrada < :fecha_salida AND fecha_salida >= :fecha_salida)
                  OR (fecha_entrada >= :fecha_entrada AND fecha_salida <= :fecha_salida)
              )";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':alquiler_id', $data['alquiler_id']);
    $stmt->bindParam(':fecha_entrada', $data['fecha_entrada']);
    $stmt->bindParam(':fecha_salida', $data['fecha_salida']);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result['total'] > 0) {
        Response::error('El alquiler no está disponible para las fechas seleccionadas', 400);
    }

    // Calcular total
    $query = "SELECT precio FROM alquileres WHERE id = :alquiler_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':alquiler_id', $data['alquiler_id']);
    $stmt->execute();
    $alquiler = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$alquiler) {
        Response::error('Alquiler no encontrado', 404);
    }

    $fecha_entrada = new DateTime($data['fecha_entrada']);
    $fecha_salida = new DateTime($data['fecha_salida']);
    $noches = $fecha_entrada->diff($fecha_salida)->days;
    $total = $noches * $alquiler['precio'];

    // Insertar reserva
    $query = "INSERT INTO reservas 
              (alquiler_id, nombre_cliente, email_cliente, telefono_cliente, 
               fecha_entrada, fecha_salida, cantidad_personas, total, notas)
              VALUES 
              (:alquiler_id, :nombre_cliente, :email_cliente, :telefono_cliente,
               :fecha_entrada, :fecha_salida, :cantidad_personas, :total, :notas)";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':alquiler_id', $data['alquiler_id']);
    $stmt->bindParam(':nombre_cliente', $data['nombre_cliente']);
    $stmt->bindParam(':email_cliente', $data['email_cliente']);
    $stmt->bindParam(':telefono_cliente', $data['telefono_cliente']);
    $stmt->bindParam(':fecha_entrada', $data['fecha_entrada']);
    $stmt->bindParam(':fecha_salida', $data['fecha_salida']);
    $stmt->bindParam(':cantidad_personas', $data['cantidad_personas']);
    $stmt->bindParam(':total', $total);
    $notas = $data['notas'] ?? null;
    $stmt->bindParam(':notas', $notas);

    if ($stmt->execute()) {
        $reserva_id = $db->lastInsertId();
        
        // Aquí podrías enviar email de confirmación
        // enviarEmailConfirmacion($data, $reserva_id, $total, $noches);
        
        Response::success([
            'id' => $reserva_id,
            'total' => $total,
            'noches' => $noches
        ], 'Reserva creada exitosamente');
    } else {
        Response::error('Error al crear la reserva', 500);
    }
}

function obtenerReservas() {
    $database = new Database();
    $db = $database->getConnection();

    // Verificar autenticación (implementar según tu sistema)
    // if (!estaAutenticado()) {
    //     Response::error('No autorizado', 401);
    // }

    $query = "SELECT 
                r.*,
                a.nombre as alquiler_nombre,
                a.tipo as alquiler_tipo
              FROM reservas r
              INNER JOIN alquileres a ON r.alquiler_id = a.id
              ORDER BY r.created_at DESC";

    $stmt = $db->prepare($query);
    $stmt->execute();

    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    Response::success($reservas);
}

function actualizarReserva($id) {
    $data = json_decode(file_get_contents("php://input"), true);
    
    $database = new Database();
    $db = $database->getConnection();

    // Verificar autenticación
    // if (!estaAutenticado()) {
    //     Response::error('No autorizado', 401);
    // }

    $query = "UPDATE reservas SET estado = :estado WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':estado', $data['estado']);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        Response::success(['id' => $id], 'Reserva actualizada exitosamente');
    } else {
        Response::error('Error al actualizar la reserva', 500);
    }
}
