import { Tooltip } from "@material-tailwind/react";
import moment from "moment";
// Vite loads the ESM build of moment (dist/moment.js); its locales are in dist/locale.
import "moment/dist/locale/ar";
import React from "react";
import { useTranslation } from "react-i18next";

export default function FromDate({
  date,
  className = "",
}: {
  date: string;
  className?: string;
}) {
  // Relative dates in the selected language ("2 hours ago" / "منذ ساعتين").
  const { i18n } = useTranslation();
  const m = moment(date).locale(i18n.resolvedLanguage === "ar" ? "ar" : "en");
  return (
    <span className={className}>
      <Tooltip content={m.format("LLLL")}>{m.fromNow()}</Tooltip>
    </span>
  );
}
