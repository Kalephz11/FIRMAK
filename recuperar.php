<?php
// Mantenemos tu lógica PHP intacta
session_start();
include 'config/conexion.php';
$mensaje = "";
$success = false; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dni = trim($_POST['dni']);
    // ... tu lógica de validación y envío aquí ...
    // (Simulamos éxito para el ejemplo del overlay)
    if (!empty($dni)) { $success = true; } 
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar - FirmaPE</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="icon" href="imagenes/favicon.png">
</head>
<body>

<div class="container">
    <div class="index-logo">
        <img src="imagenes/firmape.png" alt="Logo FirmaPE">
    </div>

    <div class="login-content">
        <h2 style="text-align: center; margin-top: 0;">Recuperar contraseña</h2>

        <form method="POST" id="recuperarForm">
            <div class="input-group">
                <input type="text" name="dni" placeholder="DNI (8 dígitos)" required maxlength="8">
            </div>
            
            <button type="submit">Enviar código</button>
            <button type="button" onclick="window.location.href='cambiar.php'">Ya tengo código</button>
        </form>

        <?php if (!empty($mensaje)): ?>
            <div class="alert-error show">
                <span class="icon">⚠️</span> <?= $mensaje ?>
            </div>
        <?php endif; ?>

        <div class="links">
            <a href="index.php">← Regresar</a>
        </div>
    </div>
</div>

<div id="overlayCheck" class="<?= $success ? 'show' : '' ?>">
    <div class="check">✔</div>
    <p>Código enviado al correo</p>
</div>

<script>
// ✅ Éxito estilo pantalla completa (Tu script original)
<?php if ($success): ?>
window.onload = () => {
    const overlay = document.getElementById("overlayCheck");
    overlay.classList.add("show");

    setTimeout(() => {
        overlay.classList.remove("show");
        window.location.href = "cambiar.php"; // 🔥 redirige automático
    }, 2500);
};
<?php endif; ?>
</script>

</body>
</html>
