<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class Logger
{
    public static function log($action, $description = null, $model = null)
    {
        if (!Auth::check()) return; // Hanya catat jika user login

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'subject_type' => $model ? get_class($model) : null,
            'subject_id' => $model ? $model->id : null,
            'ip_address' => Request::ip(),
        ]);
    }
}