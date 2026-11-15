<?php

namespace Tests\Feature;

use App\Support\SentryScrubber;
use Sentry\Event;
use Sentry\ExceptionDataBag;
use Sentry\UserDataBag;
use Tests\TestCase;

/**
 * Sentry error reports: off without a DSN, and no personal or business data
 * in a report (privacy policy, section 7).
 */
class SentryTest extends TestCase
{
    public function test_sentry_is_off_without_a_dsn(): void
    {
        $this->assertNull(config('sentry.dsn'));
    }

    public function test_settings_keep_personal_data_out(): void
    {
        $this->assertFalse(config('sentry.send_default_pii'));
        $this->assertSame('none', config('sentry.max_request_body_size'));
        $this->assertNull(config('sentry.traces_sample_rate'));
        $this->assertNull(config('sentry.profiles_sample_rate'));
        $this->assertFalse(config('sentry.breadcrumbs.sql_bindings'));
        $this->assertFalse(config('sentry.breadcrumbs.logs'));
        $this->assertSame([SentryScrubber::class, 'beforeSend'], config('sentry.before_send'));
    }

    public function test_scrubber_removes_user_request_data_and_sql(): void
    {
        $event = Event::createEvent();
        $event->setUser(UserDataBag::createFromArray(['id' => 7, 'email' => 'owner@example.com', 'ip_address' => '203.0.113.9']));
        $event->setRequest([
            'method' => 'POST',
            'url' => 'https://cornertill.com/inventory?search=milk#top',
            'query_string' => 'search=milk',
            'data' => ['name' => 'Milk', 'price' => 5],
            'cookies' => ['laravel_session' => 'secret'],
            'headers' => [
                'User-Agent' => ['Firefox'],
                'Cookie' => ['laravel_session=secret'],
                'X-Forwarded-For' => ['203.0.113.9'],
                'Authorization' => ['Bearer token'],
            ],
            'env' => ['REMOTE_ADDR' => '203.0.113.9'],
        ]);
        $event->setExceptions([new ExceptionDataBag(new \RuntimeException(
            'SQLSTATE[23000]: Integrity constraint violation (Connection: mysql, SQL: insert into `products` (`name`) values (Milk))'
        ))]);

        $clean = SentryScrubber::beforeSend($event);

        $this->assertNull($clean->getUser());
        $this->assertSame([
            'method' => 'POST',
            'url' => 'https://cornertill.com/inventory',
            'headers' => ['User-Agent' => ['Firefox']],
        ], $clean->getRequest());

        $message = $clean->getExceptions()[0]->getValue();
        $this->assertStringContainsString('SQLSTATE[23000]', $message);
        $this->assertStringNotContainsString('Milk', $message);

        $this->assertStringNotContainsString('203.0.113.9', json_encode($clean->getRequest()));
    }

    public function test_message_without_sql_does_not_change(): void
    {
        $this->assertSame('Division by zero', SentryScrubber::withoutSql('Division by zero'));
    }
}
