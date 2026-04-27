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
    <title>Login - FirmaPE</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="icon" href="imagenes/favicon.png">
</head>
<body>

<div class="container">
    <div class="index-logo">
        <img src="imagenes/firmape.png" alt="Logo Empresa">
    </div>

    <h2>Login</h2>

    <form method="POST">
        <input type="text" name="dni" placeholder="DNI" required maxlength="8">
        
        <div class="password-container">
            <input type="password" name="password" id="password" placeholder="Contraseña" required>
            <span class="toggle-password" id="togglePass">Ver</span>
        </div>

        <button type="submit">Ingresar</button>
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

<div id="overlayBienvenida">
    <div class="welcome-logo-container">
        <img src="imagenes/firmape.png" alt="Logo Empresa">
    </div>
    <h2 class="welcome-text">¡Bienvenido!</h2>
    <p class="welcome-subtext">Accediendo al panel de control...</p>
</div>

<script>
// Lógica para el botón VER/OCULTAR
const togglePass = document.getElementById('togglePass');
const passwordInput = document.getElementById('password');

togglePass.addEventListener('click', () => {
    const isPassword = passwordInput.getAttribute('type') === 'password';
    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
    togglePass.textContent = isPassword ? 'Ocultar' : 'Ver';
});

<?php if ($success): ?>
window.onload = () => {
    const overlay = document.getElementById("overlayBienvenida");
    overlay.classList.add("show");
    setTimeout(() => {
        window.location.href = "principal.php";
    }, 2000);
};
<?php endif; ?>
</script>

</body>
</html>
