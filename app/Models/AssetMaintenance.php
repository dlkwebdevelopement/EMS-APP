<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\LogsActivity;

class AssetMaintenance extends Model
{
    use LogsActivity;

    protected $fillable = [
        'asset_id', 'issue_description', 'action_taken', 
        'service_provider', 'cost', 'maintenance_at', 
        'received_at', 'completion_at', 'status', 
        'was_replaced', 'replacement_asset_id', 'bill_image'
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'maintenance_at' => 'datetime',
        'received_at' => 'datetime',
        'completion_at' => 'datetime',
        'was_replaced' => 'boolean',
    ];

    public function getPublicBillUrlAttribute()
    {
        return $this->bill_image ? asset('storage/' . $this->bill_image) : null;
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function replacementAsset()
    {
        return $this->belongsTo(Asset::class, 'replacement_asset_id');
    }
}
