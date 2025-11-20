<?php
session_start();
require 'database.php';

$error = '';
$success = '';

// Verificar si ya está logueado
if (isset($_SESSION['user'])) {
    header("Location: index.html");
    exit();
}

// Procesar formulario de registro
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_completo = trim($_POST['fullName']);
    $email = trim($_POST['email']);
    $nombre_usuario = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirmPassword']);
    $tipo = $_POST['user_type'] ?? 'espectador';
    
    // Validaciones
    if (empty($nombre_completo) || empty($email) || empty($nombre_usuario) || empty($password)) {
        $error = "Todos los campos son obligatorios";
    } elseif ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden";
    } elseif (strlen($password) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres";
    } else {
        try {
            // Verificar si el email o usuario ya existen
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? OR nombre_usuario = ?");
            $stmt->execute([$email, $nombre_usuario]);
            
            if ($stmt->fetch()) {
                $error = "El email o nombre de usuario ya está registrado";
            } else {
                // Insertar nuevo usuario
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO usuarios (email, password_hash, tipo, nombre_usuario, nombre_completo) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$email, $password_hash, $tipo, $nombre_usuario, $nombre_completo]);
                
                $success = "¡Registro exitoso! Serás redirigido al login en 3 segundos.";
                
                // Redirigir después de 3 segundos
                header("refresh:3;url=login.php");
            }
        } catch (PDOException $e) {
            $error = "Error en el registro: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - IDM Studio Art</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* PEGA AQUÍ TODO EL CSS DE TU registro.html ORIGINAL */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --color-primary: #FDEEF5;
            --color-secondary: #FFCFE2;
            --color-accent: #FDBFD3;
            --color-light: #E9EDF6;
            --color-dark: #FDC9DA;
        }

        body {
            background-color: var(--color-primary);
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ... TODO EL CSS DE registro.html ... */

        /* Mensajes de éxito y error */
        .error-message {
            background: #FF6B6B;
            color: white;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: 500;
        }

        .success-message {
            background: #96CEB4;
            color: white;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <nav class="navbar">
            <div class="logo">
                <i class="fas fa-palette"></i>
                <span class="logo-text">IDM Studio Art</span>
            </div>
            <div class="nav-links">
                <a href="index.html"><i class="fas fa-home"></i> Inicio</a>
                <a href="explorar.html"><i class="fas fa-compass"></i> Explorar</a>
                <a href="artistas.html"><i class="fas fa-users"></i> Artistas</a>
                <a href="#"><i class="fas fa-gem"></i> Destacados</a>
                <a href="#"><i class="fas fa-blog"></i> Blog</a>
            </div>
            <div class="auth-buttons">
                <button class="btn btn-outline" onclick="window.location.href='login.php'">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </button>
                <button class="btn btn-filled">
                    <i class="fas fa-user-plus"></i> Registrarse
                </button>
            </div>
        </nav>
    </header>

    <!-- Registration Section -->
    <section class="registration-section">
        <div class="registration-container">
            <!-- Left Side - Information -->
            <div class="registration-left">
                <h2>Únete a Nuestra Comunidad Creativa</h2>
                <p>Descubre un espacio diseñado especialmente para artistas digitales y amantes del arte. Comparte tus obras, conecta con otros creadores y haz crecer tu audiencia.</p>
                
                <ul class="features-list">
                    <li>
                        <i class="fas fa-check"></i>
                        <span>Exhibe tu portafolio profesional</span>
                    </li>
                    <li>
                        <i class="fas fa-check"></i>
                        <span>Conecta con artistas y coleccionistas</span>
                    </li>
                    <li>
                        <i class="fas fa-check"></i>
                        <span>Recibe feedback constructivo</span>
                    </li>
                    <li>
                        <i class="fas fa-check"></i>
                        <span>Participa en desafíos creativos</span>
                    </li>
                    <li>
                        <i class="fas fa-check"></i>
                        <span>Vende tus obras digitales</span>
                    </li>
                </ul>
            </div>

            <!-- Right Side - Registration Form -->
            <div class="registration-right">
                <div class="registration-header">
                    <h1>Crear Cuenta</h1>
                    <p>Comienza tu journey artístico con nosotros</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="error-message">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="success-message">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <!-- User Type Selection -->
                <div class="user-type-selector">
                    <div class="user-type-option selected" data-type="artista">
                        <div class="user-type-icon">
                            <i class="fas fa-paint-brush"></i>
                        </div>
                        <h3>Soy Artista</h3>
                        <p>Quiero exhibir mis obras y conectar con otros artistas</p>
                    </div>
                    <div class="user-type-option" data-type="espectador">
                        <div class="user-type-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h3>Soy Espectador</h3>
                        <p>Quiero descubrir arte y apoyar a los creadores</p>
                    </div>
                </div>

                <input type="hidden" id="user_type" name="user_type" value="artista">

                <!-- Social Registration -->
                <div class="social-registration">
                    <button type="button" class="social-btn google">
                        <i class="fab fa-google"></i>
                        Google
                    </button>
                    <button type="button" class="social-btn facebook">
                        <i class="fab fa-facebook-f"></i>
                        Facebook
                    </button>
                    <button type="button" class="social-btn apple">
                        <i class="fab fa-apple"></i>
                        Apple
                    </button>
                </div>

                <div class="divider">
                    <span>O regístrate con email</span>
                </div>

                <!-- Registration Form -->
                <form id="registrationForm" method="POST" action="registro.php">
                    <div class="form-group">
                        <label class="form-label" for="fullName">Nombre Completo</label>
                        <input type="text" id="fullName" name="fullName" class="form-input" placeholder="Ingresa tu nombre completo" required value="<?php echo isset($_POST['fullName']) ? htmlspecialchars($_POST['fullName']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" class="form-input" placeholder="tu@email.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="username">Nombre de Usuario</label>
                        <div class="input-with-icon">
                            <input type="text" id="username" name="username" class="form-input" placeholder="Elige un nombre de usuario único" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                            <i class="fas fa-user input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Contraseña</label>
                        <div class="input-with-icon">
                            <input type="password" id="password" name="password" class="form-input" placeholder="Crea una contraseña segura" required>
                            <i class="fas fa-lock input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="confirmPassword">Confirmar Contraseña</label>
                        <div class="input-with-icon">
                            <input type="password" id="confirmPassword" name="confirmPassword" class="form-input" placeholder="Repite tu contraseña" required>
                            <i class="fas fa-lock input-icon"></i>
                        </div>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms">
                            Acepto los <a href="#">Términos de Servicio</a> y la <a href="#">Política de Privacidad</a> de IDM Studio Art
                        </label>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="newsletter" name="newsletter">
                        <label for="newsletter">
                            Quiero recibir noticias sobre nuevos features, desafíos artísticos y promociones especiales
                        </label>
                    </div>

                    <button type="submit" class="submit-btn" id="submitBtn">
                        <i class="fas fa-rocket"></i> Crear Cuenta
                    </button>
                </form>

                <div class="login-link">
                    ¿Ya tienes una cuenta? <a href="login.php">Inicia Sesión aquí</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <!-- PEGA AQUÍ EL FOOTER DE registro.html -->
    </footer>

    <script>
        // User Type Selection
        document.querySelectorAll('.user-type-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.user-type-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                this.classList.add('selected');
                document.getElementById('user_type').value = this.getAttribute('data-type');
            });
        });

        // Social Registration Buttons
        document.querySelectorAll('.social-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const platform = this.classList.contains('google') ? 'Google' : 
                               this.classList.contains('facebook') ? 'Facebook' : 'Apple';
                alert(`Registro con ${platform} - Esta funcionalidad estará disponible pronto.`);
            });
        });

        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strength = calculatePasswordStrength(password);
            // Puedes agregar feedback visual aquí
        });

        function calculatePasswordStrength(password) {
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            return strength;
        }

        // Form submission loading state
        document.getElementById('registrationForm').addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando cuenta...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>