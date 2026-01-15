<?php

namespace Modules\People\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Sale\Entities\Sale; // Importamos el modelo Sale

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];

    // RELACIÓN: Un cliente tiene muchas ventas
    public function sales()
    {
        return $this->hasMany(Sale::class, 'customer_id', 'id');
    }

    protected static function newFactory()
    {
        return \Modules\People\Database\factories\CustomerFactory::new();
    }
}
