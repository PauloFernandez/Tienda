# Pasos para la integracion de Laravel Jetstream + Filament

## 1- Creamos el proyecto dentro del contenedor Docker
```php
composer create-project "laravel/laravel:^12.0" .
```

## 2- Instalamos Jetstream
```php
composer require laravel/jetstream
```

## 3- Instalar con Livewire
```php
php artisan jetstream:install livewire
```
Instalar dependencias
```php
npm install
npm run build
```
Migrar la base de datos
```php
php artisan migrate
```

Si ingresamos a la app en la vista welcome aparece "login" y "Register"
Luego remplasaremos la codificacion de esta vista para lograr nuestra vista inicial "home"

---

## 4- Instalar Filament
```php
composer require filament/filament:"^3.3" -W

php artisan filament:install --panels
```

Creamos un usuario para filament
```php
php artisan make:filament-user
```
Ahora veremos en la siguiente url el login de filament URL:"http://localhost:8000/dashboard/login" 
- Aclaracion "dashboard" es el nombre del panel que le indicamos al momento de identificar el panel inicial.
**IMPORTANTE**: Cuando indiquemos el nombre del panel NO pongamos "dashboard" por que dara conflicto con Jetsteam, esto ocurre porque Jetstream ya registra una ruta /dashboard, y Filament también intenta usarla con el mismo nombre.

Esplicado lo anterior, si indicamos el nombre del panel como "dashboard", al ingresar no da un error que indica que la ruta no esta definida, para esto devemos realizar algunos cambios.

- Cambiar el path del panel en DashboardPanelProvider.php 
```php
// app/Providers/Filament/DashboardPanelProvider.php

public function panel(Panel $panel): Panel
{
    return $panel
        ->default()
        ->id('dashboard')
        ->path('admin')  // <-- cambiar 'dashboard' por 'admin'
        ->login() // <-- cuando terminemos de configurar el panel admin para los administradores eliminamos esta linea para que Jetstream realice esta funcion.
}
```

- En el modelo User agregar esta linea para que deje pasar cualquier usuario mientras estamos desarrollando.
```php
#[Override]
public function canAccessPanel(Panel $panel): bool
{
    // Para desarrollo: dejás pasar a cualquier usuario logueado
    return true;

    // Cuando quieras restringir por email:
    // return $this->email === 'admin@example.com';

    // O cuando tengas roles:
    // return $this->hasRole('admin');

    // o 
    // if ($panel->getId() === 'admin') 
    //     {
    //         return $this->hasAnyRole(['Administrador', 'Empleado']);
    //     }

    //     return false;
}
```
