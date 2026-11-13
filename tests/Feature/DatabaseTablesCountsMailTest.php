<?php

namespace Tests\Feature;

use App\Mail\DatabaseTablesCounts;
use Tests\TestCase;

/**
 * The daily scheduled task "Email Me Database Tables Counts" sends this mail.
 */
class DatabaseTablesCountsMailTest extends TestCase
{
    public function test_mail_renders_the_counts(): void
    {
        $mail = new DatabaseTablesCounts(11, 22, 33, 44);

        $mail->assertHasSubject('CornerTill: Database Tables Counts');
        $mail->assertSeeInHtml('11');
        $mail->assertSeeInHtml('44');
    }
}
