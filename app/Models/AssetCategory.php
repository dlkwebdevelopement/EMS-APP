<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\LogsActivity;

class AssetCategory extends Model
{
    use LogsActivity;

    protected $fillable = ['name', 'description'];

    public function assets()
    {
        return $this->hasMany(Asset::class, 'category_id');
    }
}
