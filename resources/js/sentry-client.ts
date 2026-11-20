// Loaded by sentry.ts only when the page has a DSN.
import { init } from "@sentry/browser";
import { scrubBreadcrumb, scrubEvent } from "./sentry";

export function start(dsn: string, environment: string): void {
  init({
    dsn,
    environment,
    sendClientReports: false,
    dataCollection: {
      userInfo: false,
      cookies: false,
      httpHeaders: false,
      httpBodies: [],
      urlQueryParams: false,
    },
    beforeSend: scrubEvent,
    beforeBreadcrumb: scrubBreadcrumb,
  });
}
