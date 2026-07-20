<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

//Modelo Empleados
class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = ['type_document', 'number_document', 'phone', 'birthdate', 'position', 'date_hiring', 'salary', 'active', 'user_id'];

    public function casts(): array
    {
        return [
            'birthdate' => 'date',
            'date_hiring' => 'date',
            'salary' => 'decimal:2',
            'active' => 'boolean',
            ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

}
