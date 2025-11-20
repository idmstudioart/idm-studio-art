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
    
    // Redirigir al perfil del espectador
    setTimeout(() => {
        window.location.href = 'espectador.html#perfil';
    }, 1000);
}