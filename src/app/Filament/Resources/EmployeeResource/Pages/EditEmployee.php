<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = $this->record->user;

        $data['name'] = $user->name;
        $data['last_name'] = $user->last_name;
        $data['email'] = $user->email;
        $data['roles'] = $user->roles->pluck('name')->toArray();
        $data['password'] = null; // nunca precargar el hash

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data) {
            $userData = [
                'name' => $data['name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
            ];

            if (filled($data['password'] ?? null)) {
                $userData['password'] = $data['password'];
            }

            $record->user->update($userData);
            $record->user->syncRoles($data['roles']);

            $record->update([
                'type_document' => $data['type_document'],
                'number_document' => $data['number_document'] ?? null,
                'phone' => $data['phone'] ?? null,
                'birthdate' => $data['birthdate'] ?? null,
                'position' => $data['position'] ?? null,
                'date_hiring' => $data['date_hiring'] ?? null,
                'salary' => $data['salary'] ?? null,
                'active' => $data['active'] ?? true,
            ]);

            return $record;
        });
    }
}
