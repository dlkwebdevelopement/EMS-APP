<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class AssetAllocation extends Model
{
    use LogsActivity;

    protected $fillable = [
        'asset_id', 'employee_id', 'allocated_by', 'allocated_at', 
        'returned_at', 'condition_on_allocation', 
        'condition_on_return', 'remarks'
    ];

    protected $casts = [
        'allocated_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function allocatedBy()
    {
        return $this->belongsTo(Admin::class, 'allocated_by');
    }
}
