import React from "react";
import { useTranslation } from "react-i18next";

/**
 * Product name in the logo colors: "CornerTill" (English), "كورنر تِل" (Arabic).
 */
export default function AppName({
  className = "",
  cornerClassName = "",
  tillClassName = "",
}: {
  className?: string;
  cornerClassName?: string;
  tillClassName?: string;
}) {
  const { t, i18n } = useTranslation();
  const isArabic = i18n.resolvedLanguage === "ar";
  return (
    <bdi>
      <span
        className={"font-semibold text-brand-ink " + className + " " + cornerClassName}
      >
        {t("Corner")}
      </span>
      {isArabic ? " " : ""}
      <span
        className={"font-semibold text-brand-accent " + className + " " + tillClassName}
      >
        {t("Till")}
      </span>
    </bdi>
  );
}
