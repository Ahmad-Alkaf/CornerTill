<?php

namespace Tests\Feature;

use App\Models\Bill;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    use RefreshDatabase;

    private function taskNames(): array
    {
        return collect(app(Schedule::class)->events())
            ->map(fn ($event) => $event->description)
            ->all();
    }

    public function test_no_scheduled_task_deletes_bills(): void
    {
        $this->assertNotContains('Delete Old Bills', $this->taskNames());
        $this->assertContains('Email Me Database Tables Counts', $this->taskNames());
    }

    public function test_old_bills_survive_the_daily_tasks(): void
    {
        $bill = Bill::factory()->create(['created_at' => now()->subYears(5), 'updated_at' => now()->subYears(5)]);

        $this->artisan('schedule:test', ['--name' => 'Email Me Database Tables Counts'])->assertSuccessful();

        $this->assertNotNull(Bill::find($bill->id));
    }
}
