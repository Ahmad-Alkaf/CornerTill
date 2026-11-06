import { FaArrowLeft, FaArrowRight } from "react-icons/fa";
import { IconButton } from "@material-tailwind/react/components/IconButton";
import { Link } from "@inertiajs/react";
import { ILaravelPaginate } from "@/types";
import { HTMLAttributes, StyleHTMLAttributes } from "react";
import BetterLink from "./Buttons/BetterLink";

export default function Pagination({
  paginateItems,
  className = "",
  ...props
}: HTMLAttributes<HTMLDivElement> & {
  paginateItems: ILaravelPaginate<any>;
}) {
  return (
    <>
      {paginateItems.links.length > 3 && (
        // Many pages make the row wider than a phone screen: it scrolls sideways.
        <div {...props} className={"max-w-full overflow-x-auto pb-10 " + className}>
        <div className="mx-auto flex w-max">
          <BetterLink
            href={paginateItems.prev_page_url ?? ""}
            disabled={!paginateItems.prev_page_url}
            preserveState={true}
          >
            <IconButton
              disabled={!paginateItems.prev_page_url}
              variant="outlined"
              className="ltr:rounded-r-none ltr:border-r-0 rtl:rounded-l-none rtl:border-l-0"
              placeholder={undefined}
              onPointerEnterCapture={undefined}
              onPointerLeaveCapture={undefined}
            >
              <FaArrowLeft strokeWidth={2} className="h-4 w-4 rtl:rotate-180" />
            </IconButton>
          </BetterLink>

          {paginateItems.links
            .filter((v) => Number.isInteger(Number(v.label))) //remove Previous/Next links
            .map((v) => (
              <Link key={v.label} href={v.url as string} preserveState={true}>
                <IconButton
                  variant="outlined"
                  className={
                    "rounded-none ltr:border-r-0 rtl:border-l-0 text-base " +
                    (v.active ? "bg-primary-800 text-gray-100" : "")
                  }
                  placeholder={undefined}
                  onPointerEnterCapture={undefined}
                  onPointerLeaveCapture={undefined}
                >
                  {v.label}
                </IconButton>
              </Link>
            ))}

          <BetterLink
            disabled={!paginateItems.next_page_url}
            href={paginateItems.next_page_url ?? ""}
            preserveState={true}
          >
            <IconButton
              disabled={!paginateItems.next_page_url}
              variant="outlined"
              className="ltr:rounded-l-none rtl:rounded-r-none"
              placeholder={undefined}
              onPointerEnterCapture={undefined}
              onPointerLeaveCapture={undefined}
            >
              <FaArrowRight strokeWidth={2} className="h-4 w-4 rtl:rotate-180" />
            </IconButton>
          </BetterLink>
        </div>
        </div>
      )}
    </>
  );
}
