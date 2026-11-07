import { Link } from "@inertiajs/react";
import React from "react";
import Logo from "./Logo";
import AppName from "./AppName";

export default function FullLogo({
  size = "lg",
}: {
  size?: "lg" | "xl"; //add other sizes when you need it
}) {
  const logoSize = {
    lg: "h-10 w-10",
    xl: "h-12 w-12",
  }[size];
  const textSize = {
    lg: "text-2xl",
    xl: "text-3xl",
  }[size];

  return (
    <Link href="/" className="flex shrink-0 items-center gap-2 self-stretch">
      <Logo className={logoSize} />
      <h1 className={"px-1 tracking-tight " + textSize}>
        <AppName className="!font-extrabold" />
      </h1>
    </Link>
  );
}
