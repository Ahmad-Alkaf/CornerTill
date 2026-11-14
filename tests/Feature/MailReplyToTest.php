<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Email;
use Tests\TestCase;

/**
 * Global Reply-To address (config/mail.php, MAIL_REPLY_TO_ADDRESS).
 */
class MailReplyToTest extends TestCase
{
    public function test_reply_to_defaults_to_the_support_address(): void
    {
        $this->assertSame('support@kaflabs.com', config('mail.reply_to.address'));
    }

    public function test_every_email_has_the_reply_to_address(): void
    {
        config(['mail.reply_to' => ['address' => 'help@example.com', 'name' => 'Help']]);
        Mail::purge();

        Mail::raw('Text', fn ($message) => $message->to('customer@example.com')->subject('Subject'));

        $replyTo = $this->lastSentEmail()->getReplyTo();
        $this->assertCount(1, $replyTo);
        $this->assertSame('help@example.com', $replyTo[0]->getAddress());
        $this->assertSame('Help', $replyTo[0]->getName());
    }

    public function test_empty_reply_to_address_adds_no_header(): void
    {
        config(['mail.reply_to' => ['address' => null, 'name' => 'Help']]);
        Mail::purge();

        Mail::raw('Text', fn ($message) => $message->to('customer@example.com')->subject('Subject'));

        $this->assertCount(0, $this->lastSentEmail()->getReplyTo());
    }

    private function lastSentEmail(): Email
    {
        $messages = Mail::mailer()->getSymfonyTransport()->messages();
        $this->assertNotEmpty($messages);

        return $messages->last()->getOriginalMessage();
    }
}
