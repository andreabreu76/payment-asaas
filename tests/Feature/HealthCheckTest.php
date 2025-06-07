<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    /**
     * Test the health check endpoint.
     */
    public function test_health_check_endpoint(): void
    {
        $response = $this->get('/api/health');

        // Output the response content for debugging
        echo "Response content: " . $response->getContent() . "\n";

        $content = $response->json();

        // Check individual service statuses
        $this->assertEquals('ok', $content['services']['app']['status'], 'App service status is not ok');
        $this->assertEquals('ok', $content['services']['database']['status'], 'Database service status is not ok');
        $this->assertEquals('ok', $content['services']['queue']['status'], 'Queue service status is not ok: ' . ($content['services']['queue']['message'] ?? 'No error message'));

        // Check overall status last
        $this->assertEquals('ok', $content['status'], 'Overall status is not ok');
        $response->assertStatus(200, 'Response status is not 200 OK');
    }
}
