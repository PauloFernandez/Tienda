# Pasos para la integracion de Laravel Jetstream + Filament

## 1- Creamos el proyecto dentro del contenedor Docker
```php
composer create-project "laravel/laravel:^12.0" .
```

## 2- Instalamos Jetstream "v 5.x"
```php
composer require laravel/jetstream
```
## 3- Instalar con Livewire
```php
php artisan jetstream:install livewire
```

## 4- Instalar dependencias
```php
npm install
npm run build
```

## 5- Instalar Filament
```php
composer require filament/filament:"^3.3" -W

php artisan filament:install --panels
```

**ADVERTENCIA** 
- Antes de migrar la base de datos que trae la instaltacion debemos crear las tabla "clientes" y "empleados".
- En la tabla 'user' vamos a agregar la columna 'apellido'. (esto nos servira para la logica de negocio)
- En las tablas 'cliente' y 'empleado' indicaremos las columnas para datos necesarios, menos los datos de login (nombre, apellido, email, etc.)

## User solo para login 
  - Vamos a separar auth de perfil
    1) Estructura de tablas
  ```php
    users            -> solo auth: id, nombre, 'apellido->nullable', email, password, remember_token, timestamps
    clientes         -> id, user_id (FK único a users), nombre, telefono, etc.
    empleados        -> id, user_id (FK único a users), nombre, cargo, etc.

    // migration clientes
  Schema::create('clientes', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
      // ...otros campos
      $table->timestamps();
  });

  // migration empleados (igual, con sus campos propios)
  ```

   2) En los modelos
  ```php
  //Modelo User
    class User extends Authenticatable implements FilamentUser, HasAvatar
  {
      public function cliente() { return $this->hasOne(Cliente::class); }
      public function empleado() { return $this->hasOne(Empleado::class); }
      public function isCustomer(): bool { return $this->customer()->exists(); }
      public function isEmployee(): bool { return $this->employee()->exists(); }

      // Truco clave: mantiene compatibilidad con TODO el código de Jetstream/Filament
      protected function fullName(): Attribute
      {
          return Attribute::make(
              get: fn() => trim("{$this->attributes['name']} {$this->attributes['last_name']}"),
          );
      }

      #[Override]
      public function getFilamentAvatarUrl(): ?string
      {
          return $this->profile_photo_url;
      }

      #[Override]
      public function canAccessPanel(Panel $panel): bool
      {
        // Para desarrollo: dejás pasar a cualquier usuario logueado
          return true;
      }
  }

  // Con el accessor fullName, todas las vistas Blade que hoy hacen {{ $user->name }} (nav de Jetstream, saludo, notificaciones, avatar por iniciales, etc.) siguen funcionando sin tocarlas.

  //Modelo Cliente
  class Cliente extends Model 
  { 
    public function user() { return $this->belongsTo(User::class); } 
  }

  //Modelo Empleado
  class Empleado extends Model 
  { 
    public function user() { return $this->belongsTo(User::class); } 
  }
  ```

   3) Migrar la base de datos
  ```php
  php artisan migrate
  ```

  Si ingresamos a la app en la vista welcome aparece "login" y "Register"
  Luego remplasaremos la codificacion de esta vista para lograr nuestra vista inicial "home"

  Creamos un usuario para filament
  ```php
  php artisan make:filament-user
  ```

  Tenemos que generar las vistas
  ```php
    php artisan make:filament-resource NombreModelo --generate
  ```

  Ahora veremos en la siguiente url el login de filament URL:"http://localhost:8000/dashboard/login" 
  - Aclaracion "dashboard" es el nombre del panel que le indicamos al momento de identificar el panel inicial.

  - **IMPORTANTE**: Cuando indiquemos el nombre del panel *NO pongamos "dashboard"* por que dara conflicto con Jetsteam, esto ocurre porque Jetstream ya registra una ruta "/dashboard". Filament también y intenta usarla con el mismo nombre.

  Esplicado lo anterior, si indicamos el nombre del panel como "dashboard", al ingresar nos da un error que indica que la ruta no esta definida, para esto devemos realizar algunos cambios.

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

# Roles y Permisos
- Para poder configurar nuestro login unico con Jetstream y probar que el mismo redirija a las vistas correctas tanto la parte "front" Clientes/Visitantes y nuestra parte "backend" Super Admin y empleados, necesitamos los roles.
- Instalaremos el plugin Shield de filament, este pluging se basa en Spatie-Permission, al 2026 estamos utilizando la V3 con laravel V12 y Filament V3.
- Aclarada las versiones que utilizamos, seguiremos los pasos que indica la pagina, pero dejeare una guia con tips.
  
  - Guia de instalacion: 
  ```php 
  1- composer require bezhansalleh/filament-shield

  2- php artisan vendor:publish --tag="filament-shield-config"

  3- php artisan vendor:publish --tag=filament-shield-migrations

  4- php artisan migrate
  ```

  - Añadir el HasRoles para su modelo de proveedor de autenticación:
  ```php 
  use Spatie\Permission\Traits\HasRoles;

  class User extends Authenticatable
  {
      use HasRoles;
  }
  ```

  - Instalar Shield
  ```php
  6- php artisan shield:install

  7- php artisan shield:generate --all //este comando lo podemos utilizar mas adelante a medida que lo vallamos necesitando

  8- php artisan shield:super-admin //Si no nos creeo el super_admin en el paso anterior
  ```

  - Registro de empleados vía Filament
  Filament no sabe nada de esto por defecto: tenés que hookear la creación. Lo más limpio es sobreescribir el método de creación en la página 
  
  CreateEmpleado:
  ```php
    class CreateEmployee extends CreateRecord
    {
      protected static string $resource = EmployeeResource::class;

      protected function handleRecordCreation(array $data): Model
      {
          return DB::transaction(function () use ($data) {
              $user = User::create([
                  'name' => $data['name'],
                  'last_name' => $data['last_name'],
                  'email' => $data['email'],
                  'password' => $data['password'], // ya viene hasheado por dehydrateStateUsing
              ]);

              $user->syncRoles($data['roles']);

              return $user->employee()->create([
                  'type_document' => $data['type_document'],
                  'number_document' => $data['number_document'],
                  'phone' => $data['phone'] ?? null,
                  'birthdate' => $data['birthdate'] ?? null,
                  'position' => $data['position'] ?? null,
                  'date_hiring' => $data['date_hiring'] ?? null,
                  'salary' => $data['salary'] ?? null,
                  'active' => $data['active'] ?? true,
              ]);
          });
      }
    }
  ```
  - Y en el EmpleadoResource::form() agregás los campos email y password (con Hidden/dehydrated(false) para que no intente guardarlos directo en la tabla empleados).
  ```php
    public static function form(Form $form): Form
    {
      return $form
      ->schema([
          Forms\Components\Section::make('Dstos del Usuario')
              ->schema([
                  Forms\Components\TextInput::make('name')
                      ->required()
                      ->maxLength(255),
                  Forms\Components\TextInput::make('last_name')
                      ->required()
                      ->maxLength(255),
                  Forms\Components\TextInput::make('email')
                      ->email()
                      ->required()
                      ->unique(
                          table: 'users',
                          column: 'email',
                          ignoreRecord: true,
                          // como el record es Employee, hay que ignorar por user_id
                          modifyRuleUsing: fn(Forms\Get $get, $record, $rule) =>
                          $record ? $rule->ignore($record->user_id, 'id') : $rule
                      ),
                  Forms\Components\TextInput::make('password')
                      ->password()
                      ->revealable()
                      ->hiddenOn('edit')
                      ->required(fn(string $context) => $context === 'create')
                      ->dehydrated(fn($state) => filled($state))
                      ->dehydrateStateUsing(fn($state) => Hash::make($state))
                      ->maxLength(255),
                  Forms\Components\Select::make('roles')
                      ->label('Rol')
                      ->multiple()
                      ->options(Role::pluck('name', 'name'))
                      ->preload()
                      ->required(),

              ])->columns(2),

           Forms\Components\Section::make('Datos Empleado')
               ->schema([
                   Forms\Components\Select::make('type_document')
                       ->options([
                           'DNI' => 'DNI',
                          'CI' => 'CI',
                           'PASSPORT' => 'Pasaporte',
                       ])
                       ->required(),
                   Forms\Components\TextInput::make('number_document')
                       ->maxLength(20)
                       ->required(),
                  //Mas datos necesarios
               ])->columns(2),
       ]);
    }
  ```

# Jetstream como unico login
Para que Jetstream maneje únicamente la autenticación a nuestras vistas, que pueden ser las creadas en filament "panel administrativo" o Livewire "e-commerce".

- Tenemos que crear los roles que necesitemos
- Para un Sistema estilo e-commerce necesitaremos el rol 'cliente' para que se pueda registrar y acceder a su dashboard.
- Los roles para el manejo interno del sistema seran dinamicos, solo los usuarios con permisos podran dar de alta a nuevos usuarios en el formulario de Empleados.
  
## Configuracion Recurso de Filament
  Ahora debemos agregar el campo Rol al recurso de Empleado
  ```php
  //En el formulario
  Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
  // Agregamos los demas datos como 'nombre', 'apellido', 'email', etc. los necesarios.

  //En la tabla
  Tables\Columns\TextColumn::make('roles.name')
                    ->label('Rol')
                    ->badge() //Mostrar múltiples roles
                    ->default('Sin rol') //Evita problemas si un usuario no tiene rol
                    ->searchable(),
   // Agregamos los demas datos como 'nombre', 'apellido', 'email', etc. los necesarios.
  ```






2- Crear un nuevo archivo: app/Actions/Fortify/LoginResponse.php
3- Pegar este codigo:
```php
  <?php

//Esta clase va a redirigir según tipo de rol
namespace App\Actions\Fortify;

use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasRole('cliente')) {
            return redirect()->intended(route('dashboard'));
        }

        return redirect()->intended(route('/admin'));
    }
}
```
4- Registrar la clase en app/Providers/FortifyServiceProvider.php, en el método boot(), agregar:
```php
use App\Actions\Fortify\LoginResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

// Dentro de boot():
$this->app->bind(LoginResponseContract::class, LoginResponse::class);
```
5- Modificar app/Models/User.php, cambiar canAccessPanel() para que clientes no accedan al panel de Filament:
```php
public function canAccessPanel(Panel $panel): bool
{
    return ! $this->hasRole('cliente');
    
    // o 
    // if ($panel->getId() === 'admin') 
    //     {
    //         return $this->hasAnyRole(['Administrador', 'Empleado']); //Si tenemos ya Roles definidos para el acceso a filament
    //     }
    //     return false;
}
```
5- Para no tener problemas de cache tenemos que agregar en el proveedor de servicio "app/Providers/AppServiceProvider.php", este codigo:
```php 

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

public function boot(): void
{
    $flushPermissionCache = fn () => app(PermissionRegistrar::class)->forgetCachedPermissions();

    Role::saved($flushPermissionCache);
    Role::deleted($flushPermissionCache);
    Permission::saved($flushPermissionCache);
    Permission::deleted($flushPermissionCache);
}
```
- Esto asegura que cualquier guardado o borrado de rol/permiso que venga de "Shield", de un seeder, de Tinker, o de donde sea" dispare la limpieza de caché automáticamente, sin que dependa de que el flujo interno de Shield lo haga bien en todos los casos.


Con esto nuestros usuarios se redigiran segun el rol que tengan a las vistas correspondientes.


## Configuracion Actions de Fortify Jetstream
- A. CreateNewUser.php (registro de clientes vía Jetstream)
  Hoy hace un solo User::create([...]). Ahora necesita crear ambas filas en una transacción:
```php
  public function create(array $input): User
{
    Validator::make($input, [
        'nombre' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => $this->passwordRules(),
        //Validamos todas las columnas que necesitemos para guardar los datos del cliente segun necesidad de negocio
    ])->validate();

    return DB::transaction(function () use ($input) {
        $user = User::create([
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        $user->cliente()->create([
            'nombre' => $input['nombre'],
            //Indicamos todas las columnas que necesitemos para guardar los datos del cliente segun necesidad de negocio
        ]);

        return $user;
    });
}
```
  
  B. UpdateUserProfileInformation.php (Actiualizacion de clientes vía Jetstream)
  ```php
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
          //A los datos que ya estan agragar esta linea
            'last_name' => ['required', 'string', 'max:255'],
        ])->validateWithBag('updateProfileInformation');

        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
              //A los datos que ya estan agragar esta linea
              'last_name' => $input['last_name'],
            ])->save();
        }
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

- Tenemos que eliminar el "->login()" de DashboardPanelProvider o AdminPanelProvider
