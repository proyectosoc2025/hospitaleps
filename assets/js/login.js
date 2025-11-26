// Elementos del DOM
const loginForm = document.getElementById('loginForm');
const usuarioInput = document.getElementById('usuario');
const passwordInput = document.getElementById('password');
const submitButton = loginForm.querySelector('button[type="submit"]');
const alertContainer = document.getElementById('alertContainer');
const togglePassword = document.getElementById('togglePassword');
const toggleIcon = document.getElementById('toggleIcon');

// Animación de entrada para los inputs
document.addEventListener('DOMContentLoaded', () => {
    // Agregar clase fade-in a elementos
    document.querySelectorAll('.login-container > *').forEach((el, index) => {
        el.style.animationDelay = `${index * 0.1}s`;
        el.classList.add('fade-in');
    });
    
    // Focus automático en el primer input
    setTimeout(() => {
        usuarioInput.focus();
    }, 500);
});

// Toggle para mostrar/ocultar contraseña
if (togglePassword) {
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Cambiar icono
        if (type === 'text') {
            toggleIcon.classList.remove('bi-eye');
            toggleIcon.classList.add('bi-eye-slash');
        } else {
            toggleIcon.classList.remove('bi-eye-slash');
            toggleIcon.classList.add('bi-eye');
        }
        
        // Mantener el focus en el input
        passwordInput.focus();
    });
}

// Efecto de escritura en inputs
[usuarioInput, passwordInput].forEach(input => {
    input.addEventListener('input', function() {
        if (this.value.length > 0) {
            this.style.borderColor = '#0d6efd';
        } else {
            this.style.borderColor = '#e0e0e0';
        }
    });
    
    // Efecto al hacer focus
    input.addEventListener('focus', function() {
        this.parentElement.style.transform = 'scale(1.02)';
    });
    
    input.addEventListener('blur', function() {
        this.parentElement.style.transform = 'scale(1)';
        if (this.value.length === 0) {
            this.style.borderColor = '#e0e0e0';
        }
    });
});

// Validación en tiempo real
usuarioInput.addEventListener('input', function() {
    validateInput(this, this.value.length >= 3);
});

passwordInput.addEventListener('input', function() {
    validateInput(this, this.value.length >= 4);
});

function validateInput(input, isValid) {
    if (input.value.length === 0) {
        input.classList.remove('is-valid', 'is-invalid');
        return;
    }
    
    if (isValid) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
    } else {
        input.classList.remove('is-valid');
        input.classList.add('is-invalid');
    }
}

// Manejo del formulario con efectos visuales
loginForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const usuario = usuarioInput.value.trim();
    const password = passwordInput.value;
    
    // Validación básica
    if (usuario.length < 3) {
        showAlert('El usuario debe tener al menos 3 caracteres', 'warning', true);
        usuarioInput.focus();
        return;
    }
    
    if (password.length < 4) {
        showAlert('La contraseña debe tener al menos 4 caracteres', 'warning', true);
        passwordInput.focus();
        return;
    }
    
    // Deshabilitar el botón y mostrar spinner
    setLoadingState(true);
    
    try {
        const response = await fetch('api/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ usuario, password })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Mostrar mensaje de éxito
            showAlert('¡Inicio de sesión exitoso! Redirigiendo...', 'success');
            
            // Animación de salida
            document.querySelector('.login-container').style.animation = 'fadeInUp 0.5s ease reverse';
            
            // Redireccionar después de un breve delay
            setTimeout(() => {
                window.location.href = 'dashboard.php';
            }, 1000);
        } else {
            // Mostrar mensaje de error con shake
            showAlert(data.message || 'Usuario o contraseña incorrectos', 'danger', true);
            
            // Limpiar campos
            usuarioInput.value = '';
            passwordInput.value = '';
            usuarioInput.classList.remove('is-valid', 'is-invalid');
            passwordInput.classList.remove('is-valid', 'is-invalid');
            
            // Restaurar botón
            setLoadingState(false);
            
            // Enfocar en el campo usuario
            setTimeout(() => {
                usuarioInput.focus();
            }, 100);
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Error de conexión. Por favor, intente nuevamente.', 'danger', true);
        setLoadingState(false);
        
        // Enfocar en el campo usuario
        setTimeout(() => {
            usuarioInput.focus();
        }, 100);
    }
});

// Función para mostrar alertas con animación
function showAlert(message, type, shake = false) {
    const iconMap = {
        success: 'bi-check-circle-fill',
        danger: 'bi-exclamation-circle-fill',
        warning: 'bi-exclamation-triangle-fill',
        info: 'bi-info-circle-fill'
    };
    
    const icon = iconMap[type] || 'bi-info-circle-fill';
    
    alertContainer.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show ${shake ? 'shake' : ''}" role="alert">
            <i class="bi ${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    // Auto-cerrar después de 5 segundos
    setTimeout(() => {
        const alert = alertContainer.querySelector('.alert');
        if (alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 5000);
}

// Función para manejar el estado de carga
function setLoadingState(isLoading) {
    if (isLoading) {
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Iniciando sesión...
        `;
        loginForm.classList.add('loading');
    } else {
        submitButton.disabled = false;
        submitButton.innerHTML = `
            <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar
        `;
        loginForm.classList.remove('loading');
    }
}

// Detectar Enter en los campos
usuarioInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        passwordInput.focus();
    }
});

// Efecto de vibración en móviles (si está disponible)
function vibrateDevice() {
    if ('vibrate' in navigator) {
        navigator.vibrate(200);
    }
}

// Agregar vibración en errores
const originalShowAlert = showAlert;
showAlert = function(message, type, shake = false) {
    if (type === 'danger' || type === 'warning') {
        vibrateDevice();
    }
    originalShowAlert(message, type, shake);
};

// Prevenir zoom en iOS al hacer focus en inputs
if (/iPhone|iPad|iPod/i.test(navigator.userAgent)) {
    const viewport = document.querySelector('meta[name=viewport]');
    if (viewport) {
        viewport.setAttribute('content', 
            'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no');
    }
}

// Efecto parallax suave en la imagen (solo en desktop)
if (window.innerWidth > 768) {
    window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const parallax = document.querySelector('.hospital-image img');
        if (parallax) {
            parallax.style.transform = `translateY(${scrolled * 0.5}px)`;
        }
    });
}
