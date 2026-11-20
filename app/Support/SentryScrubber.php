<?php

namespace App\Support;

use Sentry\Event;
use Sentry\EventHint;

/**
 * Removes personal and business data from an error report before it goes to
 * Sentry. The privacy policy (section 7) describes this; keep them in step.
 *
 * A report keeps: the error type, the stack trace, the request method and
 * path, and the browser type. It loses: form contents, the query string,
 * cookies, all other headers, the IP address, the signed-in user, and the
 * SQL text with its values in database errors.
 */
class SentryScrubber
{
    /** Request headers that stay in a report. */
    private const KEPT_HEADERS = ['user-agent', 'accept-language', 'content-type'];

    public static function beforeSend(Event $event, ?EventHint $hint = null): ?Event
    {
        $event->setUser(null);

        $request = $event->getRequest();
        if ($request !== []) {
            $clean = [];
            if (isset($request['method'])) {
                $clean['method'] = $request['method'];
            }
            if (isset($request['url'])) {
                // No query string or fragment: they can hold tokens and search text.
                $clean['url'] = strtok((string) $request['url'], '?#');
            }
            $headers = [];
            foreach ((array) ($request['headers'] ?? []) as $name => $value) {
                if (in_array(strtolower((string) $name), self::KEPT_HEADERS, true)) {
                    $headers[$name] = $value;
                }
            }
            if ($headers !== []) {
                $clean['headers'] = $headers;
            }
            $event->setRequest($clean);
        }

        foreach ($event->getExceptions() as $exception) {
            $exception->setValue(self::withoutSql($exception->getValue()));
        }
        if ($event->getMessage() !== null) {
            $event->setMessage(self::withoutSql($event->getMessage()));
        }

        return $event;
    }

    /**
     * A Laravel QueryException message ends with "(Connection: x, SQL: ...)",
     * and the SQL text contains the values of the query (names, prices,
     * emails). Keep the database error and drop the SQL text.
     */
    public static function withoutSql(string $message): string
    {
        $position = strpos($message, ', SQL: ');
        if ($position === false) {
            return $message;
        }

        return substr($message, 0, $position).', SQL: [removed])';
    }
}
