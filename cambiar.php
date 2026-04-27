<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config/conexion.php';

$error = "";
$success = false;

$dni = "";
$codigo = "";
$nueva = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $dni = $_POST['dni'] ?? '';
    $codigo = $_POST['codigo'] ?? '';
    $nueva = $_POST['nueva'] ?? '';

    // 🔴 VALIDACIÓN DNI
    if (!preg_match('/^\d{8}$/', $dni)) {
        $error = "El DNI debe tener 8 dígitos";
    } elseif ($codigo == "" || $nueva == "") {
        $error = "Completa todos los campos";
    } else {
        // 🔴 VALIDACIÓN FORTALEZA CONTRASEÑA
        $faltantes = [];
        if (!preg_match('/[A-Z]/', $nueva)) $faltantes[] = "una MAYÚSCULA";
        if (!preg_match('/[a-z]/', $nueva)) $faltantes[] = "una minúscula";
        if (!preg_match('/[0-9]/', $nueva)) $faltantes[] = "un número";
        if (!preg_match('/[\W]/', $nueva)) $faltantes[] = "un símbolo";
        if (strlen($nueva) < 8) $faltantes[] = "mínimo 8 caracteres";

        if (!empty($faltantes)) {
            $error = "<div style='text-align:center'>
            <b>La contraseña necesita:</b><br>
            • " . implode("<br>• ", $faltantes) . "
            </div>";
        } else {
            // 🔴 VALIDAR CÓDIGO EN BASE DE DATOS
            $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE dni=? AND codigo=?");
            $stmt->bind_param("ss", $dni, $codigo);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows > 0) {
                $hash = password_hash($nueva, PASSWORD_DEFAULT);
                $up = $conexion->prepare("UPDATE usuarios SET password=?, codigo=NULL WHERE dni=?");
                $up->bind_param("ss", $hash, $dni);
                $up->execute();

                $success = true;
                $dni = ""; $codigo = ""; $nueva = "";
            } else {
                $error = "Código incorrecto o no coincide con el DNI";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar contraseña - FirmaPE</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<div class="container">
    <div class="index-logo">
        <img src="imagenes/firmape.png" alt="Logo FirmaPE">
    </div>

    <div class="login-content">
        <h2 style="margin-top: 0; text-align: center;">Cambiar contraseña</h2>

        <form method="POST">
            <input name="dni" placeholder="DNI (8 dígitos)" required value="<?= htmlspecialchars($dni) ?>" maxlength="8">
            <input name="codigo" placeholder="Código enviado al correo" required value="<?= htmlspecialchars($codigo) ?>">

            <div class="input-group">
                <input type="password" name="nueva" id="password" placeholder="Nueva contraseña" required>
                <span class="eye" id="togglePass">Ver</span>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert-error show">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <button type="submit">Cambiar Contraseña</button>
        </form>

        <div class="links" style="margin-top: 15px; text-align: center;">
            <a href="recuperar.php">← Regresar</a>
        </div>
    </div>
</div>

<div id="overlayBienvenida" class="<?= $success ? 'show' : '' ?>">
    <div style="text-align: center;">
        <div class="check-animado">✔</div>
        <p class="texto-exito">Contraseña cambiada correctamente</p>
        <p class="texto-espera">Redirigiendo al login...</p>
    </div>
</div>

<script>
// 👁 Lógica Ver/Ocultar
document.getElementById("togglePass").onclick = function() {
    const p = document.getElementById("password");
    if (p.type === "password") {
        p.type = "text";
        this.textContent = "Ocultar";
    } else {
        p.type = "password";
        this.textContent = "Ver";
    }
};

// ✔ Redirección al login
<?php if ($success): ?>
    setTimeout(() => {
        window.location.href = "index.php";
    }, 2500);
<?php endif; ?>
</script>

</body>
</html>
