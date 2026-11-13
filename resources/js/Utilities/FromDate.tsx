import { Tooltip } from "@material-tailwind/react";
import moment from "moment";
// Vite loads the ESM build of moment (dist/moment.js); its locales are in dist/locale.
import "moment/dist/locale/ar";
import React from "react";

// Keep Arabic month and day names, but use Western digits (0-9): the "ar" locale
// changes digits to ١٢٣ in postformat. Keep its Arabic comma.
moment.updateLocale("ar", {
  postformat: (s: string) => s.replace(/,/g, "،"),
});
// updateLocale also sets the global locale; each date sets its own locale below.
moment.locale("en");
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
