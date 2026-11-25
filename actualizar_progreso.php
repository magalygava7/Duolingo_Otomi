
<?php

ini_set('session.gc_maxlifetime', (string)(30 * 24 * 60 * 60));
session_start();

header("Content-Type: application/json; charset=UTF-8");

function responder($success, $mensaje) {
    echo json_encode([
        "success" => $success,
        "mensaje" => $mensaje
    ]);
    exit;
}

// Conexión
$conexion = new mysqli(
    "localhost",
    "root",
    "",
    "otomi_magaly"
);

if ($conexion->connect_error) {
    responder(false, "Error de conexión con la base de datos");
}

$conexion->set_charset("utf8mb4");

// Verificar sesión
if (!isset($_SESSION['id_usuario'])) {
    responder(false, "Debes iniciar sesión nuevamente");
}

// Recibir datos
$datos = json_decode(file_get_contents("php://input"), true);

if (!is_array($datos) || !isset($datos["experienciaGanada"])) {
    responder(false, "No se recibieron correctamente los puntos");
}

$puntajeGanado = filter_var(
    $datos["experienciaGanada"],
    FILTER_VALIDATE_INT
);

if ($puntajeGanado === false || $puntajeGanado < 0) {
    responder(false, "El puntaje recibido no es válido");
}

$idUsuario = (int) $_SESSION["id_usuario"];

// Actualizar progreso
$sql = "
UPDATE progreso
SET
    experiencia = experiencia + ?,
    racha = racha + 1,
    porcentaje = LEAST(porcentaje + 5, 100)
WHERE id_usuario = ?
";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    responder(false, "Error al preparar la consulta");
}

$stmt->bind_param("ii", $puntajeGanado, $idUsuario);

if (!$stmt->execute()) {
    responder(false, "Error al guardar el progreso");
}

// Comprobar si existe el registro
if ($stmt->affected_rows === 0) {

    $stmt->close();

    $consulta = $conexion->prepare(
        "SELECT id_usuario FROM progreso WHERE id_usuario = ?"
    );

    $consulta->bind_param("i", $idUsuario);
    $consulta->execute();
    $consulta->store_result();

    if ($consulta->num_rows === 0) {
        $consulta->close();
        responder(false, "El estudiante no tiene un registro en la tabla progreso");
    }

    $consulta->close();
}

$stmt->close();
$conexion->close();

responder(true, "Progreso actualizado correctamente");

?>
