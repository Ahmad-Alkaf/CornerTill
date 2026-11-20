/**
 * Sentry error reports from the browser (privacy policy, section 7).
 *
 * Off when the page has no <meta name="sentry-dsn"> (SENTRY_BROWSER_DSN is
 * empty): the SDK is then not downloaded and nothing is sent.
 *
 * Errors only: no session replay, no tracing, no logs. A report carries no
 * user, cookies, headers, request or response bodies, query strings, or
 * console text. The server side is config/sentry.php.
 */
import type { Breadcrumb, ErrorEvent } from "@sentry/browser";

/** The URL without its query string and fragment. */
export function withoutQuery(url: string): string {
  return url.split(/[?#]/)[0];
}

export function scrubEvent(event: ErrorEvent): ErrorEvent {
  delete event.user;
  if (event.request) {
    event.request = {
      url: event.request.url ? withoutQuery(event.request.url) : undefined,
    };
  }
  return event;
}

export function scrubBreadcrumb(breadcrumb: Breadcrumb): Breadcrumb | null {
  // Console text can hold business data.
  if (breadcrumb.category === "console") return null;
  const data = breadcrumb.data;
  if (data) {
    for (const key of ["url", "from", "to"]) {
      if (typeof data[key] === "string") data[key] = withoutQuery(data[key]);
    }
    delete data.request_body_size;
    delete data.response_body_size;
  }
  return breadcrumb;
}

const dsn = document
  .querySelector('meta[name="sentry-dsn"]')
  ?.getAttribute("content");

if (dsn) {
  // A separate file with a named import, so the build keeps only the error
  // reporting part of the SDK (no replay, tracing, or feedback code).
  import("./sentry-client")
    .then(({ start }) =>
      start(
        dsn,
        document
          .querySelector('meta[name="sentry-environment"]')
          ?.getAttribute("content") || "production",
      ),
    )
    .catch(() => {
      // The SDK did not load (for example a blocker). The app continues.
    });
}
