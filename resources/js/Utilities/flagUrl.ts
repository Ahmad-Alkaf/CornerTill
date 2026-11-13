/**
 * Country flags are files of this app, so the browser does not contact a flag
 * CDN (the privacy policy names no flag provider). The files come from the
 * flag-icons package (MIT). A file in resources/images/flags replaces the
 * flag-icons file with the same name (af.svg: the flag that the country list
 * showed before, from Wikimedia Commons).
 * vite.config.js sets assetsInlineLimit 0, so each flag is a separate file
 * that loads only when it is shown (not a data: URL in the JS bundle).
 */
const PACKAGE_FLAGS = import.meta.glob<string>(
  "/node_modules/flag-icons/flags/4x3/*.svg",
  { eager: true, query: "?url", import: "default" },
);
const LOCAL_FLAGS = import.meta.glob<string>("/resources/images/flags/*.svg", {
  eager: true,
  query: "?url",
  import: "default",
});

const FLAG_URLS: Record<string, string> = {};
for (const flags of [PACKAGE_FLAGS, LOCAL_FLAGS])
  for (const [path, url] of Object.entries(flags))
    FLAG_URLS[path.slice(path.lastIndexOf("/") + 1, -".svg".length)] = url;

/** URL of the flag of a country code ("sa"), or undefined when there is no file. */
export default function flagUrl(code: string): string | undefined {
  return FLAG_URLS[code];
}
