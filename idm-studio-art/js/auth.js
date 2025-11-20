// Sistema de autenticación
class AuthSystem {
    constructor() {
        this.users = JSON.parse(localStorage.getItem('idm_users')) || [];
        this.currentUser = JSON.parse(localStorage.getItem('idm_currentUser')) || null;
        this.init();
    }

    init() {
        this.updateNavigation();
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Formulario de login
        document.getElementById('loginForm')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.login();
        });

        // Formulario de registro
        document.getElementById('registerForm')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.register();
        });
    }

    login() {
        const email = document.getElementById('loginEmail').value;
        const password = document.getElementById('loginPassword').value;

        const user = this.users.find(u => u.email === email && u.password === password);
        
        if (user) {
            this.currentUser = user;
            localStorage.setItem('idm_currentUser', JSON.stringify(user));
            this.showMessage('¡Bienvenido de nuevo!', 'success');
            this.updateNavigation();
            closeLoginModal();
            
            // Redirigir directamente al tab de perfil en espectador.html
            setTimeout(() => {
                window.location.href = 'espectador.html#perfil';
            }, 1000);
        } else {
            this.showMessage('Email o contraseña incorrectos', 'error');
        }
    }

    register() {
        const name = document.getElementById('registerName').value;
        const email = document.getElementById('registerEmail').value;
        const password = document.getElementById('registerPassword').value;
        const accountType = document.getElementById('accountType').value;

        // Verificar si el usuario ya existe
        if (this.users.find(u => u.email === email)) {
            this.showMessage('Este email ya está registrado', 'error');
            return;
        }

        const newUser = {
            id: Date.now().toString(),
            name,
            email,
            password,
            accountType,
            joinDate: new Date().toISOString()
        };

        this.users.push(newUser);
        localStorage.setItem('idm_users', JSON.stringify(this.users));
        
        this.showMessage('¡Cuenta creada exitosamente!', 'success');
        closeRegisterModal();
        
        // Auto-login y redirigir al perfil después del registro
        this.currentUser = newUser;
        localStorage.setItem('idm_currentUser', JSON.stringify(newUser));
        this.updateNavigation();
        
        setTimeout(() => {
            window.location.href = 'espectador.html#perfil';
        }, 1000);
    }

    logout() {
        this.currentUser = null;
        localStorage.removeItem('idm_currentUser');
        this.updateNavigation();
        this.showMessage('Sesión cerrada correctamente', 'success');
        
        // Redirigir a la página principal
        setTimeout(() => {
            window.location.href = 'index.html';
        }, 1000);
    }

    updateNavigation() {
        const authButtons = document.querySelector('.auth-buttons');
        
        if (this.currentUser) {
            authButtons.innerHTML = `
                <span style="margin-right: 1rem;">Hola, ${this.currentUser.name}</span>
                <button class="btn btn-outline" onclick="auth.logout()">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </button>
            `;
        } else {
            authButtons.innerHTML = `
                <button class="btn btn-outline" onclick="openLoginModal()">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </button>
                <button class="btn btn-filled" onclick="openRegisterModal()">
                    <i class="fas fa-user-plus"></i> Registrarse
                </button>
            `;
        }
    }

    showMessage(message, type) {
        // Crear elemento de mensaje
        const messageEl = document.createElement('div');
        messageEl.className = `message ${type}`;
        messageEl.innerHTML = `
            <span>${message}</span>
            <button onclick="this.parentElement.remove()">&times;</button>
        `;
        
        // Estilos del mensaje
        messageEl.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            background: ${type === 'success' ? '#4CAF50' : '#f44336'};
            color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 1rem;
            animation: slideIn 0.3s ease;
        `;
        
        document.body.appendChild(messageEl);
        
        // Auto-remover después de 3 segundos
        setTimeout(() => {
            if (messageEl.parentElement) {
                messageEl.remove();
            }
        }, 3000);
    }

    isLoggedIn() {
        return this.currentUser !== null;
    }

    getCurrentUser() {
        return this.currentUser;
    }
}

// Inicializar sistema de autenticación
const auth = new AuthSystem();

// Funciones para modales
function openLoginModal() {
    document.getElementById('loginModal').style.display = 'block';
}

function closeLoginModal() {
    document.getElementById('loginModal').style.display = 'none';
}

function openRegisterModal() {
    document.getElementById('registerModal').style.display = 'block';
}

function closeRegisterModal() {
    document.getElementById('registerModal').style.display = 'none';
}

function switchToRegister() {
    closeLoginModal();
    openRegisterModal();
}

function switchToLogin() {
    closeRegisterModal();
    openLoginModal();
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    const loginModal = document.getElementById('loginModal');
    const registerModal = document.getElementById('registerModal');
    
    if (event.target === loginModal) {
        closeLoginModal();
    }
    if (event.target === registerModal) {
        closeRegisterModal();
    }
}