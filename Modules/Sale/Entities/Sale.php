<?php

namespace Modules\Sale\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function saleDetails()
    {
        return $this->hasMany(SaleDetails::class, 'sale_id', 'id');
    }

    public function salePayments()
    {
        return $this->hasMany(SalePayment::class, 'sale_id', 'id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $lastSale = Sale::latest()->first();
            $number = $lastSale ? $lastSale->id + 1 : 1;

            // Determinar prefijo según usuario
            $prefijo = self::getPrefijoSede();

            // Generar: LC-00001 o BL-00002
            $model->reference = make_reference_id($prefijo, $number);

            // NOTA: Ya NO guardamos sede en note
            // La sede ya está identificada en la referencia (LC- o BL-)
            // El campo note queda solo para observaciones del usuario
        });
    }

    /**
     * Obtener prefijo según el usuario logueado
     */
    private static function getPrefijoSede()
    {
        if (!auth()->check()) {
            return 'LC'; // Default si no hay usuario
        }

        $userId = auth()->id();

        // Mapeo directo
        $mapeo = [
            2 => 'BL',     // Blindex → BL
            3 => 'LC',     // LaCancha → LC
            4 => 'LC',     // Raul → LC
        ];

        return $mapeo[$userId] ?? 'LC'; // Default La Cancha
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    public function getShippingAmountAttribute($value)
    {
        return $value / 100;
    }

    public function getPaidAmountAttribute($value)
    {
        return $value / 100;
    }

    public function getTotalAmountAttribute($value)
    {
        return $value / 100;
    }

    public function getDueAmountAttribute($value)
    {
        return $value / 100;
    }

    public function getTaxAmountAttribute($value)
    {
        return $value / 100;
    }

    public function getDiscountAmountAttribute($value)
    {
        return $value / 100;
    }
}
