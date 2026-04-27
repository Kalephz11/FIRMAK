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
    <style>
        /* --- CORRECCIÓN DEL OVERLAY (PARA QUE NO SALGA AL INICIO) --- */
        #overlayBienvenida {
            position: fixed;
            top: 0; left: 0; 
            width: 100%; height: 100%;
            background: rgba(255, 255, 255, 0.98);
            display: none; /* Oculto por defecto */
            flex-direction: column;
            justify-content: center; 
            align-items: center;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }

        /* Se activa solo con la clase .show */
        #overlayBienvenida.show {
            display: flex !important;
            opacity: 1;
        }

        .welcome-logo-container {
            width: 120px; 
            margin-bottom: 20px;
            animation: popIn 0.6s cubic-bezier(0.17, 0.67, 0.83, 0.67);
        }

        .welcome-logo-container img {
            width: 100%;
            height: auto;
        }

        @keyframes popIn {
            0% { transform: scale(0.5); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        h2.welcome-text {
            color: #333;
            font-size: 28px;
            margin: 0;
            font-family: sans-serif;
        }

        p.welcome-subtext {
            color: #666;
            margin-top: 10px;
        }

        /* --- AJUSTE DE CONTRASEÑA --- */
        .password-container {
            position: relative;
            width: 100%;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #4db8ff;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            user-select: none;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="index-logo">
        <img src="imagenes/firmape.png" alt="Logo Empresa">
    </div>

    <h2 style="text-align: center; color: #333;">Login</h2>

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
// Lógica Ver/Ocultar Contraseña
const togglePass = document.getElementById('togglePass');
const passwordInput = document.getElementById('password');

togglePass.addEventListener('click', () => {
    const isPassword = passwordInput.getAttribute('type') === 'password';
    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
    togglePass.textContent = isPassword ? 'Ocultar' : 'Ver';
});

// Lógica de redirección con éxito
<?php if ($success): ?>
    const overlay = document.getElementById("overlayBienvenida");
    overlay.classList.add("show");

    setTimeout(() => {
        window.location.href = "principal.php";
    }, 2000);
<?php endif; ?>
</script>

</body>
</html>
