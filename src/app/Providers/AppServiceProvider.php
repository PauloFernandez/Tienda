<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $flushPermissionCache = fn() => app(PermissionRegistrar::class)->forgetCachedPermissions();

        Role::saved($flushPermissionCache);
        Role::deleted($flushPermissionCache);
        Permission::saved($flushPermissionCache);
        Permission::deleted($flushPermissionCache);
    }
}
