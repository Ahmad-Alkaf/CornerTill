import { Typography } from "@material-tailwind/react";
import { ElementType, PropsWithChildren } from "react";

/** `as` changes the element (for example "h1"), not the style. */
export function H2({
  children,
  className = "",
  as,
}: PropsWithChildren<{ className?: string; as?: ElementType }>) {
  return (
    <Typography
      variant="h2"
      as={as}
      className={"mb-1 font-extrabold text-gray-900 " + className}
      placeholder={undefined}
      onPointerEnterCapture={undefined}
      onPointerLeaveCapture={undefined}
    >
      {children}
    </Typography>
  );
}
