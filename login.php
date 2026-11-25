<?php
session_start();
// Datos de conexión
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "otomi_magaly";
// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);
// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
// Obtener datos del formulario
$email = $_POST['email'];
$pass = $_POST['contrasena'];
// Preparar consulta segura
$stmt = $conn->prepare("SELECT * FROM usuario WHERE email = ? AND contrasena = ?");
$stmt->bind_param("ss", $email, $pass);
$stmt->execute();
$result = $stmt->get_result();
// Validar usuario
if ($result->num_rows > 0) {


    $usuario = $result->fetch_assoc();


    session_start();


    $_SESSION['usuario'] = $usuario['email'];
    $_SESSION['id_usuario'] = $usuario['id_usuario'];
    $_SESSION['nombre'] = $usuario['nombre'];


    echo "<script>
            window.location.href='login.html';
          </script>";
    exit();
}
$stmt->close();
$conn->close();
?>

