<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

//Modelo Pedido
class Order extends Model
{
    use SoftDeletes;

    protected $fillable = ['customer_id', 'address_id', 'cupon_id', 'payment_method_id', 'employee_id', 'order_number', 'state', 'subtotal',
                            'discount', 'shipping_cost', 'tax', 'total', 'note'];

    public function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            ];
    }

    public function customer():BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function address():BelongsTo
    {
        return $this->belongsTo(Address::class);
    }
    
    public function cupon():BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function payment_method():BelongsTo
    {
        return $this->belongsTo(Payment_method::class);
    }

    public function employee():BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function order_item(): HasMany
    {
        return $this->hasMany(Order_item::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function shipments():HasOne
    {
        return $this->hasOne(Shipment::class);
    }

}
