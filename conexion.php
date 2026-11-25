<?php


header("Content-Type: application/json");


include("conexion.php");


$data = json_decode(file_get_contents("php://input"), true);


$nombre = $data['nombre'];
$email = $data['email'];
$contrasena = $data['contrasena'];
$fecha_nac = $data['fecha_nac'];
$pais = $data['pais'];
$nivel = $data['nivel'];


$fecha_registro = date("Y-m-d");


$sql = "INSERT INTO usuario
(id_usuario,nombre,email,contraseña,fecha_registro,fecha_nac,pais,nivel)
VALUES (100,?,?,?,?,?,?,?)";


$stmt = $conn->prepare($sql);


$stmt->bind_param(
    "ssssssi",
    $nombre,
    $email,
    $contrasena,
    $fecha_registro,
    $fecha_nac,
    $pais,
    $nivel
);


if($stmt->execute()){
    echo json_encode([
        "estado" => true,
        "mensaje" => "✅ Usuario registrado correctamente"
    ]);
}else{
    echo json_encode([
        "estado" => false,
        "mensaje" => "❌ Error al registrar usuario"
    ]);
}


$stmt->close();
$conn->close();


?>
