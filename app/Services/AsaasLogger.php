<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class AsaasLogger
{
    public static function info(string $message, array $context = [])
    {
        Log::channel('asaas')->info($message, $context);
    }

    public static function error(string $message, array $context = [])
    {
        Log::channel('asaas')->error($message, $context);
    }

    public static function warning(string $message, array $context = [])
    {
        Log::channel('asaas')->warning($message, $context);
    }

    public static function debug(string $message, array $context = [])
    {
        Log::channel('asaas')->debug($message, $context);
    }
}
