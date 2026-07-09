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

# Refactorizacion de vistas para "Visitantes/Clientes"
- Comenzamos a limpiar nuestro codigo de vistas y componentes que no son necesarias como:
- "components/welcome": Solo tiene informacion de Jetstream
- Vista "welcome": pagina de inicio que instala Laravel por defecto, creamos un component menu y pegamos el codigo que contiene el <header></header>, en este menu iran los 
  items publicos de la pagina
- Remplazamos la vista "welcome" por "home" que sera nuestra vista publica, esta vista sera un componente de pagina completa de livewire, asi el catalogo es 
  dinamico ideal para sitios estilo e-commerce.
**Indicaciones:** 
1- Creamos el componente de pagina completa ej:"home", y asignamos un codigo para verificar que se muestra la vista
2- En el controlador de livewire ej:"Home" tenemos que indicar la siguiente sintaxis 
```php 
  #[Layout('layouts.app')] //esto es solo para componentes de pagina completa
    public function render()
    {
        return view('livewire.home');
    }
```
3- En las rutas asignamos la ruta principal ej:
```php
  Route::get('/', Home::class)->name('home');
```
4- Para evitar que la vista principal de error devemos realizar los siguiente en "layouts/app.blade.php"
```php
  @auth
    @livewire('navigation-menu')
  @else
    <x-menu-guest /> //Este es un componente que creamos como menu para las visiteas, Ver codigo en:"views/components/menu-guest.blade.php"
  @endauth
```
5- Para que el cliente pueda acceder a la pagina de la tienda, tenemos que modificar la ruta ya sea del logo o un enlace, que envie a el "home".

# Personalizacion de vistas
- Para modificar el logo que trea Jetstream por defecto, temenos que ubicar a los componentes:
  "application-logo.blade, application-mark.blade, authentication-card-logo.blade" y remplazar el codigo svg por la etiqueta img que apunte a la imagen de nuestro logo.
Por si en un futuro necesitamos actualizar Jetstream, no rompemos nada.

# Roles y Permisos
- Una ves tengamos construidos nuestro proyecto, tanto la parte "front" Clientes/Visitantes y nuestra parte "backend" Super Admin y empleados.
- Instalaremos el plugin Shield de filament, este pluging se basa en Spatie-Permission, al 2026 estamos utilizando la V3 con laravel V12 y Filament V3.
- Aclarada las versiones que utilizamos, seguiremos los pasos que indica la pagina, pero dejeare una guia con tips.
  Guia de instalacion: 
  ```php 
  1- composer require bezhansalleh/filament-shield

  2- php artisan vendor:publish --tag="filament-shield-config"

  3- php artisan vendor:publish --tag=filament-shield-migrations

  4- php artisan migrate
  ```
  5- Añadir el HasRoles Rasgo para su modelo de proveedor de autenticación:
  ```php 
  use Spatie\Permission\Traits\HasRoles;

  class User extends Authenticatable
  {
      use HasRoles;
  }
  ```
  ```php
  6- php artisan shield:install

  7- php artisan shield:generate --all

  8- php artisan shield:super-admin //Si no nos creeo el super_admin en el paso anterior
  ```
  Ahora debemos agregas el campo Rol al recurso del Usuario
  ```php
  //En el formulario
  Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),

  //En la tabla
  Tables\Columns\TextColumn::make('roles.name')
                    ->label('Rol')
                    ->badge() //Mostrar múltiples roles
                    ->default('Sin rol') //Evita problemas si un usuario no tiene rol
                    ->searchable(),
  ```
