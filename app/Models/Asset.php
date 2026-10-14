<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\LogsActivity;

class Asset extends Model
{
    use LogsActivity;

    protected $fillable = [
        'category_id', 'asset_tag', 'brand', 'model', 
        'serial_number', 'purchase_date', 'warranty_expiry', 
        'purchase_cost', 'status', 'specs', 'profile_photo', 'bill_image'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'specs' => 'array',
        'purchase_cost' => 'decimal:2',
    ];

    public function getPublicBillUrlAttribute()
    {
        return $this->bill_image ? asset('storage/' . $this->bill_image) : null;
    }

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function allocations()
    {
        return $this->hasMany(AssetAllocation::class);
    }

    public function maintenanceRecords()
    {
        return $this->hasMany(AssetMaintenance::class);
    }

    public function currentAllocation()
    {
        return $this->hasOne(AssetAllocation::class)->whereNull('returned_at');
    }

    public function getCurrentHolderAttribute()
    {
        $allocation = $this->currentAllocation;
        return $allocation ? $allocation->employee : null;
    }

    public function components()
    {
        return $this->hasMany(AssetComponent::class);
    }
}
