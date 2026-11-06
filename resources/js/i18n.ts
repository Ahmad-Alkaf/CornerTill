import i18n from "i18next";
import { initReactI18next } from "react-i18next";
import Backend from "i18next-http-backend";
import LanguageDetector from "i18next-browser-languagedetector";

i18n
  .use(Backend)
  .use(initReactI18next)
  .use(LanguageDetector)
  .init({
    // English unless the browser asks for Arabic (or the user picked a language before).
    fallbackLng: "en",
    supportedLngs: ["en", "ar"],
    load: "languageOnly", // "ar-SA" -> "ar"
    detection: {
      order: ["localStorage", "cookie", "navigator"],
      caches: ["localStorage", "cookie"], // keep the user's language
    },
  });

// Keep <html lang> in step with the selected language.
// <html dir> stays "ltr" on purpose: the pages set dir={i18n.dir()} on each
// section, and some layouts (dashboard cards, checkout cart) break when the
// whole document is rtl.
function applyLanguageToHtml(lng: string) {
  document.documentElement.lang = i18n.resolvedLanguage ?? lng;
}
i18n.on("languageChanged", applyLanguageToHtml);
if (i18n.resolvedLanguage) applyLanguageToHtml(i18n.resolvedLanguage);

export default i18n;
