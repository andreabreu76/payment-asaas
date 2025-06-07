<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class HealthController extends Controller
{
    public function check()
    {
        $status = [
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
            'services' => [
                'app' => $this->checkApp(),
                'database' => $this->checkDatabase(),
                'queue' => $this->checkQueue(),
            ]
        ];

        $overallStatus = in_array('error', array_column($status['services'], 'status')) ? 'error' : 'ok';
        $status['status'] = $overallStatus;

        return response()->json($status, $overallStatus === 'ok' ? 200 : 503);
    }

    private function checkApp()
    {
        return [
            'status' => 'ok',
            'version' => config('app.version', '1.0.0'),
        ];
    }

    private function checkDatabase()
    {
        try {
            DB::connection()->getPdo();
            return [
                'status' => 'ok',
                'connection' => config('database.default'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    private function checkQueue()
    {
        try {
            $connection = new AMQPStreamConnection(
                config('queue.connections.rabbitmq.host'),
                config('queue.connections.rabbitmq.port'),
                config('queue.connections.rabbitmq.login'),
                config('queue.connections.rabbitmq.password')
            );
            $connection->close();

            return [
                'status' => 'ok',
                'connection' => 'rabbitmq',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }
}
