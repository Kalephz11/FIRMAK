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

<div class="login-card">
    <div class="card-header">
        <img src="imagenes/firmape.png" alt="Logo FirmaPE">
    </div>

    <div class="card-body">
        <h2>Bienvenido</h2>
        <p class="subtitle">Por favor, ingresa tus credenciales</p>

        <form method="POST" id="loginForm">
            <div class="input-group">
                <label>Documento de Identidad</label>
                <input type="text" name="dni" placeholder="Ingresa tu DNI" required maxlength="8">
            </div>
            
            <div class="input-group">
                <label>Contraseña</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="password" placeholder="••••••••" required>
                    <span class="toggle-password" id="togglePass">Ver</span>
                </div>
            </div>

            <button type="submit" class="btn-login">Ingresar al Sistema</button>
        </form>

        <?php if (!empty($mensaje)): ?>
            <div class="alert-error animate-shake">
                <span class="icon">⚠️</span> <?= $mensaje ?>
            </div>
        <?php endif; ?>

        <div class="card-footer">
            <a href="register.php">Crear cuenta</a>
            <span class="divider">|</span>
            <a href="recuperar.php">¿Olvidaste tu contraseña?</a>
        </div>
    </div>
</div>

<div id="overlayBienvenida" class="<?= $success ? 'show' : '' ?>">
    <div class="welcome-content">
        <div class="loader-ring"></div>
        <img src="imagenes/firmape.png" alt="Logo">
        <h2>¡Acceso Exitoso!</h2>
        <p>Preparando tu panel de control...</p>
    </div>
</div>

<script>
const togglePass = document.getElementById('togglePass');
const passwordInput = document.getElementById('password');

togglePass.addEventListener('click', () => {
    const isPassword = passwordInput.type === 'password';
    passwordInput.type = isPassword ? 'text' : 'password';
    togglePass.textContent = isPassword ? 'Ocultar' : 'Ver';
});

<?php if ($success): ?>
    setTimeout(() => {
        window.location.href = "principal.php";
    }, 2500);
<?php endif; ?>
</script>
</body>
</html>
