<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        foreach (['created', 'updated', 'deleted'] as $event) {
            static::$event(function ($model) use ($event) {
                $model->logActivity($event);
            });
        }
    }

    public function logActivity($action)
    {
        $changes = null;
        if ($action === 'updated') {
            $changes = [
                'before' => array_intersect_key($this->getRawOriginal(), $this->getDirty()),
                'after' => $this->getDirty(),
            ];
        }

        AuditLog::create([
            'user_type' => Auth::guard('admin')->check() ? 'admin' : (Auth::check() ? 'employee' : 'system'),
            'user_id' => Auth::guard('admin')->id() ?: (Auth::id() ?: 0),
            'action' => $action,
            'description' => ucfirst($action) . ' ' . class_basename($this) . ' (ID: ' . $this->id . ')',
            'model_type' => get_class($this),
            'model_id' => $this->id,
            'changes' => $changes,
            'ip_address' => request()->ip(),
        ]);
    }
}
