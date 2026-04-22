<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config/conexion.php';
include 'includes/mail.php';

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $dni = trim($_POST['dni']);
    $codigo = rand(100000,999999);

    $stmt = $conexion->prepare("SELECT correo FROM usuarios WHERE dni=?");
    $stmt->bind_param("s", $dni);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows > 0) {

        $row = $res->fetch_assoc();
        $correo = $row['correo'];

        $stmt = $conexion->prepare("UPDATE usuarios SET codigo=? WHERE dni=?");
        $stmt->bind_param("ss", $codigo, $dni);
        $stmt->execute();

        if (enviarCodigo($correo, $codigo)) {
            $mensaje = "Código enviado al correo";
        } else {
            $mensaje = "Error al enviar correo";
        }

    } else {
        $mensaje = "DNI no registrado";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Recuperar</title>
<link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<div class="container">
<h2>Recuperar contraseña</h2>

<form method="POST">
<input name="dni" placeholder="DNI" required>
<button>Enviar código</button>
</form>

<p><?php echo $mensaje; ?></p>

<form action="cambiar.php" method="GET">
    <button type="submit" class="btn-secundario">Ya tengo código</button>
</form>

<a href="login.php">Volver</a>

</div>

</body>
</html>