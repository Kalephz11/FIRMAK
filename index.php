<?php
session_start();
include 'config/conexion.php';
$mensaje = "";
$success = false; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dni = trim($_POST['dni']);
    $pass = $_POST['password'];

    if (!preg_match('/^[0-9]{8}$/', $dni)) {
        $mensaje = "El DNI debe tener 8 dígitos";
    } else {
        $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE dni=?");
        $stmt->bind_param("s", $dni);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        if (!$res) {
            $mensaje = "El DNI no se encuentra registrado";
        } elseif (!password_verify($pass, $res['password'])) {
            $mensaje = "La contraseña es incorrecta";
        } else {
            $_SESSION['user'] = $dni;
            $success = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - FirmaPE</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="icon" href="imagenes/favicon.png">
</head>
<body>

<div class="container">
    <div class="index-logo">
        <img src="imagenes/firmape.png" alt="Logo FirmaPE">
    </div>

    <div class="login-content">
        <h2 style="text-align: center; margin-top: 0;">Bienvenido</h2>
        <p style="text-align: center; color: #666; font-size: 14px; margin-bottom: 25px;">
            Por favor, ingresa tus credenciales
        </p>

        <form method="POST" id="loginForm">
            <div class="input-group">
                <label style="font-size: 13px; font-weight: bold; color: #333;">Documento de Identidad</label>
                <input type="text" name="dni" placeholder="Ingresa tu DNI" required maxlength="8">
            </div>
            
            <div class="input-group">
                <label style="font-size: 13px; font-weight: bold; color: #333;">Contraseña</label>
                <div style="position: relative;">
                    <input type="password" name="password" id="password" placeholder="••••••••" required>
                    <span class="eye" id="togglePass" style="font-size: 11px; font-weight: bold; color: #4db8ff; text-transform: uppercase;">Ver</span>
                </div>
            </div>

            <button type="submit">Ingresar al Sistema</button>
        </form>

        <?php if (!empty($mensaje)): ?>
            <div class="alert-error show">
                <?= $mensaje ?>
            </div>
        <?php endif; ?>

        <div class="links">
            <a href="register.php">Crear cuenta</a>
            <a href="recuperar.php">¿Olvidaste tu contraseña?</a>
        </div>
    </div>
</div>

<div id="overlayBienvenida" class="<?= $success ? 'show' : '' ?>">
    <div style="text-align: center;">
        <div style="font-size: 80px; color: #00ff88; margin-bottom: 20px;">✔</div>
        <img src="imagenes/firmape.png" alt="Logo" style="width: 150px; margin-bottom: 20px;">
        <h2 style="color: white; margin: 0;">¡Acceso Exitoso!</h2>
        <p style="color: #ddd;">Preparando tu panel de control...</p>
    </div>
</div>

<script>
// Ver / Ocultar contraseña
const togglePass = document.getElementById('togglePass');
const passwordInput = document.getElementById('password');

togglePass.addEventListener('click', () => {
    const isPassword = passwordInput.type === 'password';
    passwordInput.type = isPassword ? 'text' : 'password';
    togglePass.textContent = isPassword ? 'Ocultar' : 'Ver';
});

// Redirección si todo sale bien
<?php if ($success): ?>
    setTimeout(() => {
        window.location.href = "principal.php";
    }, 2500);
<?php endif; ?>
</script>
</body>
</html>
