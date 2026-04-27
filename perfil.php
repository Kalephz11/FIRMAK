<?php
session_start();
include 'config/conexion.php';

// 🔐 PROTECCIÓN
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$dni = $_SESSION['user'];
$mensaje = "";
$color_alerta = "#ff4d4d"; 
$success = false; // Variable para el overlay

// 🔍 OBTENER DATOS ACTUALES
$stmt = $conexion->prepare("SELECT nombre, correo FROM usuarios WHERE dni=?");
$stmt->bind_param("s", $dni);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();

$nombre = $user['nombre'] ?? "";
$correo = $user['correo'] ?? "";

// 💾 GUARDAR CAMBIOS (Solo correo)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nuevoCorreo = trim($_POST['correo']);

    $check = $conexion->prepare("SELECT id FROM usuarios WHERE correo=? AND dni != ?");
    $check->bind_param("ss", $nuevoCorreo, $dni);
    $check->execute();
    $existe = $check->get_result();

    if ($existe->num_rows > 0) {
        $mensaje = "El correo ya está registrado por otro usuario";
        $color_alerta = "#ff4d4d"; 
    } else {
        $stmt = $conexion->prepare("UPDATE usuarios SET correo=? WHERE dni=?");
        $stmt->bind_param("ss", $nuevoCorreo, $dni);
        
        if ($stmt->execute()) {
            $correo = $nuevoCorreo;
            $mensaje = "Correo actualizado correctamente";
            $color_alerta = "#00c853"; 
            $success = true; // Activa el overlay
        } else {
            $mensaje = "Error al guardar los cambios";
            $color_alerta = "#ff4d4d";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi perfil - FirmaPE</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        /* Ajustes específicos para que se vea igual a la imagen */
        .container {
            text-align: left; /* Título alineado a la izquierda como la imagen */
        }
        
        .login-content-perfil {
            padding: 40px; /* Padding extra para que respire como en la foto */
        }

        h2 {
            font-size: 28px;
            margin-bottom: 25px;
            color: #000;
        }

        .btn-volver {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            color: #4db8ff;
            font-weight: bold;
            font-size: 14px;
            transition: 0.3s;
        }
        .btn-volver:hover {
            color: #1a8cff;
        }
        
        input[readonly] {
            background-color: #eee !important;
            cursor: not-allowed;
            color: #777;
        }

        label {
            display: block;
            text-align: left;
            font-size: 13px;
            color: #666;
            margin: 15px 0 5px 0;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="login-content-perfil">
        <h2>Mi perfil</h2>

        <form method="POST">
            <label>Nombre Completo (No editable)</label>
            <input type="text" value="<?= htmlspecialchars($nombre) ?>" readonly>

            <label>Correo Electrónico</label>
            <input type="email" name="correo" placeholder="Correo" value="<?= htmlspecialchars($correo) ?>" required>

            <button type="submit">Guardar cambios</button>
        </form>

        <?php if ($mensaje): ?>
            <div class="alert-error show" style="background: <?= $color_alerta ?>; color: white; border: none; margin-top:15px;">
                <?= $mensaje ?>
            </div>
        <?php endif; ?>

        <div style="margin-top: 15px;">
            <a href="principal.php" class="btn-volver">⬅ Volver al panel</a>
        </div>
    </div>
</div>

<div id="overlayBienvenida" class="<?= $success ? 'show' : '' ?>">
    <div style="text-align: center;">
        <div class="check-animado">✔</div>
        <div class="loading-text">
            <span class="letter">A</span>
            <span class="letter">c</span>
            <span class="letter">t</span>
            <span class="letter">u</span>
            <span class="letter">a</span>
            <span class="letter">l</span>
            <span class="letter">i</span>
            <span class="letter">z</span>
            <span class="letter">a</span>
            <span class="letter">d</span>
            <span class="letter">o</span>
        </div>
        <p class="texto-espera">Los cambios se guardaron con éxito</p>
    </div>
</div>

<script>
// Quitar el overlay después de 2 segundos si hubo éxito
<?php if ($success): ?>
    setTimeout(() => {
        document.getElementById('overlayBienvenida').classList.remove('show');
    }, 2000);
<?php endif; ?>
</script>

</body>
</html>
