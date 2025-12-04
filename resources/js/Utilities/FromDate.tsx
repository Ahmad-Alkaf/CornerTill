import { Tooltip } from "@material-tailwind/react";
import moment from "moment";
import React, { useMemo } from "react";
import { useTranslation } from "react-i18next";

export default function FromDate({
  date,
  className = "",
}: {
  date: string;
  className?: string;
}) {
  const { i18n } = useTranslation();
  useMemo(() => moment.locale(i18n.language), [i18n.language]);
  return (
    <span className={className}>
      <Tooltip content={moment(date).format("LLLL")}>
        {moment(date).fromNow()}
      </Tooltip>
    </span>
  );
}
