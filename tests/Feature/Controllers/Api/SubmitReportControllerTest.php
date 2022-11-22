<?php

namespace Tests\Feature\Controllers\Api;

use Tests\TestCase;
use App\Models\BugReport;

class SubmitReportControllerTest extends TestCase
{       

    public function testShouldSubmitBugReport()
    {
        
        list($headers, $response) = $this->submitReport();

        $this->assertDatabaseHas('bug_reports', $response->original['reports'][0]);
    }

    protected function submitReport()
    {
        $request = [
            'vulnerability_type' => 'CWE-19 Test Vulnerability',
            'serverity_level' => 'Critical',
            'title' => 'Bug Report',
            'description' => 'Bug Report Description'
        ];

        $response = $this->getAuthenticatedJWTToken();
        $headers = [
            'Authorization' => sprintf('Bearer %s', $response['access_token'])
        ];
        $response = $this->json('POST', route('api.v1.post.submit.report'), $request, $headers);

        return [$headers, $response];
    }

    public function testShouldGetUserReports()
    {

        list($headers, $response) = $this->submitReport();
        
        $response = $this->json('GET', route('api.v1.get.reports'), [], $headers);

        $reports = $response->original['reports'][0];
        $this->assertArrayHasKey('uuid', $reports);
        $this->assertArrayHasKey('vulnerability_type', $reports);
        $this->assertArrayHasKey('title', $reports);
        $this->assertArrayHasKey('description', $reports);
    }

    public function testShouldDeleteReport()
    {
        list($headers, $response) = $this->submitReport();
        $response = $this->json('GET', route('api.v1.get.reports'), [], $headers);

        $reports = $response->original['reports'][0];
        $uuid = $reports['uuid'];
        
        $response = $this->json('DELETE', route('api.v1.delete.report', ['id' => $uuid]), [], $headers);
        $this->assertSame('Report successfully deleted.', $response->original['message']);
        $this->assertNull(BugReport::first());
        
    }

    protected function getAuthenticatedJWTToken()
    {
        // 2FA
        $original = $this->signInWithEnabled2FA()->original;
        

        $this->mockAuthenticator();
        $request = [
            'code' => '1234'
        ];
        
        // 2FA code validation
        $headers = [
            'Authorization' => sprintf('Bearer %s', $original['access_token'])
        ];

        $response = $this->json('POST', route('api.v1.post.verify.2fa'), $request, $headers);
        return $response->original;

    }
}