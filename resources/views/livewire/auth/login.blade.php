<div>
    <form wire:submit="login">
        @csrf
        
        <div class="mb-4">
            <label for="usuario" class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-user"></i>
                </span>
                <input type="text" 
                       class="form-control @error('usuario') is-invalid @enderror" 
                       id="usuario" 
                       wire:model="usuario"
                       placeholder="Ingrese su email"
                       required
                       autofocus>
            </div>
            @error('usuario')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-4">
            <label for="password" class="form-label">Contraseña</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-lock"></i>
                </span>
                <input type="password" 
                       class="form-control @error('password') is-invalid @enderror" 
                       id="password" 
                       wire:model="password"
                       placeholder="Ingrese su contraseña"
                       required>
                <button class="btn password-toggle" type="button" id="togglePassword" aria-label="Mostrar contraseña">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="mb-4 form-check">
            <input type="checkbox" class="form-check-input" id="remember" wire:model="remember">
            <label class="form-check-label" for="remember">Recordarme</label>
        </div>
        
        <button type="submit" class="btn btn-login" wire:loading.attr="disabled" wire:target="login">
            <span wire:loading.remove wire:target="login">
                <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
            </span>
            <span wire:loading wire:target="login">
                <i class="fas fa-spinner fa-spin me-2"></i>Verificando...
            </span>
        </button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const passwordIcon = togglePassword.querySelector('i');
            
            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.type === 'password' ? 'text' : 'password';
                    passwordInput.type = type;
                    
                    if (type === 'text') {
                        passwordIcon.classList.remove('fa-eye');
                        passwordIcon.classList.add('fa-eye-slash');
                    } else {
                        passwordIcon.classList.remove('fa-eye-slash');
                        passwordIcon.classList.add('fa-eye');
                    }
                });
            }
        });
    </script>
</div>