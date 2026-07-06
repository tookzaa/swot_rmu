<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ActivityLogger
{
    public static function log(string $action, ?int $targetUserId = null, ?string $description = null): void
    {
        Log::channel('single')->info('[activity] ' . $action, [
            'misid' => Session::get('MISID'),
            'user' => Session::get('user'),
            'target_user_id' => $targetUserId,
            'description' => $description,
        ]);
    }
}
