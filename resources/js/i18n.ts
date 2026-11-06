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
i18n.on("languageChanged", (lng) => {
  document.documentElement.lang = i18n.resolvedLanguage ?? lng;
});

export default i18n;
