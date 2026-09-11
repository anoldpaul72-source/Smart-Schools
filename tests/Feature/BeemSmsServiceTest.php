<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Student;
use App\Models\SmsLog;
use App\Services\BeemSmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BeemSmsServiceTest extends TestCase
{
    use RefreshDatabase;
    public function test_phone_number_formatting()
    {
        $service = new BeemSmsService();

        $this->assertEquals('255712345678', $service->formatPhoneNumber('0712345678'));
        $this->assertEquals('255712345678', $service->formatPhoneNumber('+255712345678'));
        $this->assertEquals('255712345678', $service->formatPhoneNumber('255712345678'));
        $this->assertEquals('255712345678', $service->formatPhoneNumber('712345678'));
        $this->assertEquals('255682000000', $service->formatPhoneNumber('0682000000'));
    }

    public function test_simulated_sms_send_and_logging()
    {
        $service = new BeemSmsService();

        $result = $service->sendSms('0712345678', 'Jaribio la SMS kwa mzazi');

        $this->assertTrue($result['success']);
        $this->assertTrue($result['simulated']);

        $this->assertDatabaseHas('sms_logs', [
            'recipient_phone' => '255712345678',
            'status'          => 'simulated',
        ]);
    }
}
