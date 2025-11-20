<?php
session_start();
require 'database.php';

$error = '';

// Verificar si ya está logueado
if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['tipo'] == 'artista') {
        header("Location: artist-dashboard.html");
    } else {
        header("Location: index.html");
    }
    exit();
}

// Procesar formulario de login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    if (!empty($email) && !empty($password)) {
        // Buscar usuario por email o nombre de usuario
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? OR nombre_usuario = ?");
        $stmt->execute([$email, $email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Login exitoso
            $_SESSION['user'] = [
                'id' => $user['id'],
                'nombre' => $user['nombre_usuario'],
                'email' => $user['email'],
                'tipo' => $user['tipo'],
                'loggedIn' => true
            ];
            
            // Redirigir según tipo de usuario
            if ($user['tipo'] == 'artista') {
                header("Location: artist-dashboard.html");
            } else {
                header("Location: index.html");
            }
            exit();
        } else {
            $error = "Credenciales incorrectas. Usa las cuentas demo o regístrate.";
        }
    } else {
        $error = "Por favor, completa todos los campos";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - IDM Studio Art</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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

        /* Header */
        header {
            background-color: white;
            box-shadow: 0 2px 15px rgba(253, 191, 211, 0.2);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 5%;
            max-width: 1400px;
            margin: 0 auto;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.8rem;
            font-weight: 700;
        }

        .logo i {
            color: #FDBFD3;
            font-size: 2rem;
        }

        .logo-text {
            background: linear-gradient(135deg, #FDBFD3, #FFCFE2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
        }

        .nav-links a {
            text-decoration: none;
            color: #555;
            font-weight: 500;
            transition: color 0.3s;
            position: relative;
        }

        .nav-links a:hover {
            color: #FDBFD3;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 0;
            background-color: #FDBFD3;
            transition: width 0.3s;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .auth-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.7rem 1.8rem;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            font-size: 0.95rem;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid #FDBFD3;
            color: #FDBFD3;
        }

        .btn-filled {
            background: #FDBFD3;
            color: white;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(253, 191, 211, 0.3);
        }

        /* Login Section */
        .login-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 5%;
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-light) 100%);
        }

        .login-container {
            display: flex;
            max-width: 1100px;
            width: 100%;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            min-height: 650px;
        }

        .login-left {
            flex: 1;
            background: linear-gradient(135deg, var(--color-secondary) 0%, var(--color-accent) 100%);
            padding: 4rem;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
        }

        .welcome-back {
            position: relative;
            z-index: 2;
        }

        .welcome-back h2 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }

        .welcome-back p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            line-height: 1.7;
        }

        .testimonial {
            background: rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            border-radius: 15px;
            margin-top: 2rem;
            position: relative;
            z-index: 2;
            backdrop-filter: blur(10px);
        }

        .testimonial-text {
            font-style: italic;
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .author-info h4 {
            font-size: 1rem;
            margin-bottom: 0.2rem;
        }

        .author-info p {
            font-size: 0.9rem;
            opacity: 0.9;
            margin: 0;
        }

        .login-right {
            flex: 1;
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .login-header h1 {
            font-size: 2.5rem;
            color: #444;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #666;
            font-size: 1.1rem;
        }

        .form-group {
            margin-bottom: 1.8rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.7rem;
            color: #555;
            font-weight: 500;
            font-size: 1rem;
        }

        .form-input {
            width: 100%;
            padding: 1rem 1.5rem;
            border: 2px solid var(--color-light);
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s;
            background: white;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--color-accent);
            box-shadow: 0 0 0 3px rgba(253, 191, 211, 0.1);
        }

        .input-with-icon {
            position: relative;
        }

        .input-icon {
            position: absolute;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }

        .password-toggle {
            cursor: pointer;
            transition: color 0.3s;
        }

        .password-toggle:hover {
            color: var(--color-accent);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.7rem;
        }

        .remember-me input {
            transform: scale(1.2);
        }

        .remember-me label {
            color: #666;
            font-size: 0.95rem;
            cursor: pointer;
        }

        .forgot-password {
            color: var(--color-accent);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: color 0.3s;
        }

        .forgot-password:hover {
            color: var(--color-dark);
            text-decoration: underline;
        }

        .login-btn {
            width: 100%;
            padding: 1.2rem;
            background: var(--color-accent);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 1.5rem;
        }

        .login-btn:hover {
            background: var(--color-dark);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(253, 191, 211, 0.3);
        }

        .login-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 2rem 0;
            color: #888;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--color-light);
        }

        .divider span {
            padding: 0 1rem;
            font-size: 0.9rem;
        }

        .social-login {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .social-btn {
            flex: 1;
            padding: 1rem;
            border: 2px solid var(--color-light);
            border-radius: 12px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            color: #555;
        }

        .social-btn:hover {
            border-color: var(--color-accent);
            transform: translateY(-2px);
        }

        .social-btn.google i {
            color: #DB4437;
        }

        .social-btn.facebook i {
            color: #4267B2;
        }

        .social-btn.apple i {
            color: #000;
        }

        .register-link {
            text-align: center;
            color: #666;
            font-size: 1rem;
        }

        .register-link a {
            color: var(--color-accent);
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        /* Demo Accounts */
        .demo-accounts {
            margin-top: 2rem;
            padding: 1.5rem;
            background: var(--color-primary);
            border-radius: 12px;
            border-left: 4px solid var(--color-accent);
        }

        .demo-accounts h4 {
            color: #444;
            margin-bottom: 1rem;
            font-size: 1rem;
        }

        .demo-account {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.8rem 0;
            border-bottom: 1px solid rgba(253, 191, 211, 0.2);
        }

        .demo-account:last-child {
            border-bottom: none;
        }

        .demo-info {
            flex: 1;
        }

        .demo-role {
            font-weight: 600;
            color: #444;
            font-size: 0.9rem;
        }

        .demo-credentials {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.2rem;
        }

        .demo-login-btn {
            padding: 0.5rem 1rem;
            background: var(--color-accent);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .demo-login-btn:hover {
            background: var(--color-dark);
        }

        /* Footer */
        footer {
            background: linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%);
            color: white;
            padding: 3rem 5% 2rem;
            margin-top: auto;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 3rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .footer-column h3 {
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
            color: #FDBFD3;
        }

        .footer-column p {
            color: #ccc;
            line-height: 1.7;
            margin-bottom: 1.5rem;
        }

        .footer-column ul {
            list-style: none;
        }

        .footer-column ul li {
            margin-bottom: 0.8rem;
        }

        .footer-column a {
            color: #ddd;
            text-decoration: none;
            transition: color 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        .footer-column a i {
            width: 18px;
        }

        .footer-column a:hover {
            color: var(--color-secondary);
        }

        .social-icons {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .social-icons a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: #444;
            border-radius: 50%;
            transition: all 0.3s;
        }

        .social-icons a:hover {
            background: linear-gradient(135deg, var(--color-secondary) 0%, var(--color-accent) 100%);
            transform: translateY(-3px);
        }

        .copyright {
            text-align: center;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid #444;
            color: #aaa;
            font-size: 0.9rem;
        }

        /* Error Message */
        .error-message {
            background: #FF6B6B;
            color: white;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 968px) {
            .login-container {
                flex-direction: column;
            }
            
            .login-left {
                padding: 3rem 2rem;
            }
            
            .login-right {
                padding: 3rem 2rem;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 1.2rem;
                padding: 1.2rem 5%;
            }

            .nav-links {
                gap: 1.2rem;
                flex-wrap: wrap;
                justify-content: center;
            }

            .auth-buttons {
                width: 100%;
                justify-content: center;
            }

            .social-login {
                flex-direction: column;
            }

            .welcome-back h2 {
                font-size: 2rem;
            }

            .form-options {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
        }

        @media (max-width: 480px) {
            .login-left, .login-right {
                padding: 2rem 1.5rem;
            }
            
            .login-header h1 {
                font-size: 2rem;
            }
            
            .footer-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .demo-account {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .demo-login-btn {
                align-self: stretch;
                text-align: center;
            }
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
                <button class="btn btn-filled">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </button>
                <button class="btn btn-outline" onclick="window.location.href='registro.php'">
                    <i class="fas fa-user-plus"></i> Registrarse
                </button>
            </div>
        </nav>
    </header>

    <!-- Login Section -->
    <section class="login-section">
        <div class="login-container">
            <!-- Left Side - Welcome -->
            <div class="login-left">
                <div class="welcome-back">
                    <h2>¡Bienvenido de nuevo!</h2>
                    <p>Nos alegra verte de nuevo en IDM Studio Art. Accede a tu cuenta para continuar tu journey creativo, ver tus notificaciones y conectar con la comunidad.</p>
                    
                    <div class="testimonial">
                        <p class="testimonial-text">"IDM Studio Art ha transformado completamente mi forma de compartir arte. La comunidad es increíblemente supportive y las herramientas son perfectas para artistas digitales."</p>
                        <div class="testimonial-author">
                            <div class="author-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="author-info">
                                <h4>María González</h4>
                                <p>Ilustradora Digital</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Login Form -->
            <div class="login-right">
                <div class="login-header">
                    <h1>Iniciar Sesión</h1>
                    <p>Accede a tu cuenta para continuar</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="error-message">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <!-- Social Login -->
                <div class="social-login">
                    <button class="social-btn google">
                        <i class="fab fa-google"></i>
                        Google
                    </button>
                    <button class="social-btn facebook">
                        <i class="fab fa-facebook-f"></i>
                        Facebook
                    </button>
                    <button class="social-btn apple">
                        <i class="fab fa-apple"></i>
                        Apple
                    </button>
                </div>

                <div class="divider">
                    <span>O inicia sesión con email</span>
                </div>

                <!-- Login Form -->
                <form id="loginForm" method="POST" action="login.php">
                    <div class="form-group">
                        <label class="form-label" for="email">Correo Electrónico o Usuario</label>
                        <div class="input-with-icon">
                            <input type="text" id="email" name="email" class="form-input" placeholder="tu@email.com o tu_usuario" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            <i class="fas fa-user input-icon"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Contraseña</label>
                        <div class="input-with-icon">
                            <input type="password" id="password" name="password" class="form-input" placeholder="Ingresa tu contraseña" required>
                            <i class="fas fa-eye password-toggle input-icon" id="passwordToggle"></i>
                        </div>
                    </div>

                    <div class="form-options">
                        <div class="remember-me">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Recordar sesión</label>
                        </div>
                        <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button type="submit" class="login-btn" id="loginButton">
                        <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                    </button>
                </form>

                <!-- Demo Accounts (for testing) -->
                <div class="demo-accounts">
                    <h4>Cuentas de Demo (Para pruebas):</h4>
                    <div class="demo-account">
                        <div class="demo-info">
                            <div class="demo-role">Artista Demo</div>
                            <div class="demo-credentials">Usuario: artista_demo | Contraseña: demo123</div>
                        </div>
                        <button class="demo-login-btn" data-email="artista_demo" data-password="demo123">
                            Usar
                        </button>
                    </div>
                    <div class="demo-account">
                        <div class="demo-info">
                            <div class="demo-role">Espectador Demo</div>
                            <div class="demo-credentials">Usuario: espectador_demo | Contraseña: demo123</div>
                        </div>
                        <button class="demo-login-btn" data-email="espectador_demo" data-password="demo123">
                            Usar
                        </button>
                    </div>
                </div>

                <div class="register-link">
                    ¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-column">
                <h3>IDM Studio Art</h3>
                <p>La plataforma definitiva para artistas digitales. Donde la creatividad y la tecnología se encuentran.</p>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-pinterest"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div class="footer-column">
                <h3>Explorar</h3>
                <ul>
                    <li><a href="index.html"><i class="fas fa-chevron-right"></i> Inicio</a></li>
                    <li><a href="explorar.html"><i class="fas fa-chevron-right"></i> Galería</a></li>
                    <li><a href="artistas.html"><i class="fas fa-chevron-right"></i> Artistas</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Eventos</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Concursos</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Para Artistas</h3>
                <ul>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Subir Obra</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Perfil de Artista</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Estadísticas</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Recursos</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Vender Arte</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Soporte</h3>
                <ul>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Centro de Ayuda</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Contacto</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Términos de Uso</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Política de Privacidad</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right"></i> Preguntas Frecuentes</a></li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; 2024 IDM Studio Art. Todos los derechos reservados. | Diseñado con <i class="fas fa-heart" style="color: #FDBFD3;"></i> para la comunidad creativa</p>
        </div>
    </footer>

    <script>
        // Password Toggle Visibility
        const passwordToggle = document.getElementById('passwordToggle');
        const passwordInput = document.getElementById('password');

        passwordToggle.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        // Demo Account Login
        document.querySelectorAll('.demo-login-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const email = this.getAttribute('data-email');
                const password = this.getAttribute('data-password');
                
                document.getElementById('email').value = email;
                document.getElementById('password').value = password;
                
                // Auto-submit after a short delay
                setTimeout(() => {
                    document.getElementById('loginForm').submit();
                }, 500);
            });
        });

        // Social Login Buttons
        document.querySelectorAll('.social-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const platform = this.classList.contains('google') ? 'Google' : 
                               this.classList.contains('facebook') ? 'Facebook' : 'Apple';
                
                alert(`Inicio de sesión con ${platform} - Esta funcionalidad estará disponible pronto.`);
            });
        });

        // Loading state for form submission
        document.getElementById('loginForm').addEventListener('submit', function() {
            const loginBtn = document.getElementById('loginButton');
            loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Iniciando sesión...';
            loginBtn.disabled = true;
        });
    </script>
</body>
</html>