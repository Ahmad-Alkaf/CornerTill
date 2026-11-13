import "./bootstrap";
// Figtree (variable font, weights 300-900) from the app itself, not from a font CDN.
import "@fontsource-variable/figtree";
import "../css/app.css";
import i18n from "./i18n";
import { createRoot } from "react-dom/client";
import { createInertiaApp } from "@inertiajs/react";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { Suspense } from "react";
//&#8239; is narrow space character
const appName = import.meta.env.VITE_APP_NAME || "CornerTill";

/**
 * After a deploy, the HTML can ask for a JS chunk that the server does not
 * have (a rolling deploy) or that the browser cached as missing. Reload the
 * page one time to get a matching HTML and chunks. The sessionStorage flag
 * stops a reload loop; a page that loads clears it. (Inertia visits after a
 * deploy already do a full reload: the asset version changes.)
 */
const CHUNK_RELOAD_KEY = "cornertill-chunk-reload";
function reloadOnceForMissingChunk(): void {
  try {
    if (sessionStorage.getItem(CHUNK_RELOAD_KEY)) return;
    sessionStorage.setItem(CHUNK_RELOAD_KEY, "1");
  } catch {
    return; // no sessionStorage: no guard against a loop, so no reload
  }
  window.location.reload();
}
window.addEventListener("vite:preloadError", reloadOnceForMissingChunk);

createInertiaApp({
  // Page titles (<Head title="...">) in the selected language.
  title: (title) =>
    title ? `${i18n.t(title)}&#8239;-&#8239;${i18n.t(appName)}` : i18n.t(appName),
  resolve: (name) =>
    resolvePageComponent(
      `./Pages/${name}.tsx`,
      import.meta.glob("./Pages/**/*.tsx"),
    ).then(
      (page) => {
        try {
          sessionStorage.removeItem(CHUNK_RELOAD_KEY);
        } catch {
          // no sessionStorage
        }
        return page;
      },
      (error) => {
        reloadOnceForMissingChunk();
        throw error;
      },
    ),
  setup({ el, App, props }) {
    const root = createRoot(el);

    root.render(
      <Suspense fallback={<h4>Loading...</h4>}>
        <App {...props} />
      </Suspense>,
    );
  },
  progress: {
    color: "#115e59", //primary-800
    showSpinner: true,
  },
});
