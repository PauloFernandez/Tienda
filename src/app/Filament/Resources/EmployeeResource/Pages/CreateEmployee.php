<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
                'number_document' => $data['number_document'] ?? null,
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
