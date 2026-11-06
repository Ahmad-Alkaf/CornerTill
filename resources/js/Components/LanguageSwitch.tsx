import { useTranslation } from "react-i18next";

/**
 * EN/AR switch. i18next saves the choice in the same localStorage key and
 * cookie that the language detector reads (see resources/js/i18n.ts), and
 * i18n.ts updates <html lang>. The pages set dir (rtl for Arabic) per section.
 */
export default function LanguageSwitch({ className = "" }: { className?: string }) {
  const { i18n } = useTranslation();
  const isArabic = i18n.resolvedLanguage === "ar";
  const next = isArabic ? "en" : "ar";
  return (
    <button
      type="button"
      onClick={() => i18n.changeLanguage(next)}
      lang={next}
      title={isArabic ? "English" : "العربية"}
      aria-label={isArabic ? "Switch to English" : "التبديل إلى العربية"}
      data-testid="language-switch"
      className={
        "rounded-md border border-gray-300 px-2 py-1 text-sm font-medium text-gray-600 transition hover:border-primary-800 hover:text-primary-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-600 " +
        className
      }
    >
      {isArabic ? "EN" : "عربي"}
    </button>
  );
}
