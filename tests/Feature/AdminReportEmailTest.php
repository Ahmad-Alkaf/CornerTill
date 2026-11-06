<?php

namespace Tests\Feature;

use App\Mail\DatabaseTablesCounts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Scheduled task "Email Me Database Tables Counts" and ADMIN_REPORT_EMAIL.
 */
class AdminReportEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_goes_to_admin_report_email(): void
    {
        Mail::fake();
        config(['mail.admin_report_address' => 'admin@example.com']);

        $this->artisan('schedule:test', ['--name' => 'Email Me Database Tables Counts'])->assertSuccessful();

        Mail::assertQueued(DatabaseTablesCounts::class, fn ($mail) => $mail->hasTo('admin@example.com'));
    }

    public function test_no_report_when_admin_report_email_is_empty(): void
    {
        Mail::fake();
        config(['mail.admin_report_address' => null]);

        $this->artisan('schedule:test', ['--name' => 'Email Me Database Tables Counts'])->assertSuccessful();

        Mail::assertNothingQueued();
        Mail::assertNothingSent();
    }
}
