<?php
include 'config/conexion.php';
$mensaje = "";

if ($_POST) {
    $dni = $_POST['dni'];
    $codigo = $_POST['codigo'];
    $nueva = $_POST['nueva'];

    // validar contraseña fuerte
    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{8,}$/', $nueva)) {
        $mensaje = "Contraseña débil";
    } else {

        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE dni=? AND codigo=?");
        $stmt->bind_param("ss", $dni, $codigo);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {

            $hash = password_hash($nueva, PASSWORD_DEFAULT);

            $stmt = $conexion->prepare("UPDATE usuarios SET password=?, codigo=NULL WHERE dni=?");
            $stmt->bind_param("ss", $hash, $dni);
            $stmt->execute();

            $mensaje = "✅ Contraseña actualizada correctamente";

        } else {
            $mensaje = "Código incorrecto o DNI inválido";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Cambiar contraseña</title>
<link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<div class="container">
<h2>Cambiar contraseña</h2>

<form method="POST">
<input name="dni" placeholder="DNI" required>
<input name="codigo" placeholder="Código" required>
<input type="password" name="nueva" placeholder="Nueva contraseña" required>
<button>Cambiar</button>
</form>

<p><?= $mensaje ?></p>

<a href="login.php">Volver</a>

</div>

</body>
</html>