<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class AssetComponent extends Model
{
    use LogsActivity;

    protected $fillable = [
        'asset_id', 'name', 'brand', 'model', 
        'serial_number', 'purchase_date', 'warranty_expiry', 
        'purchase_cost', 'bill_image', 'is_active', 'remarks'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'purchase_cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
