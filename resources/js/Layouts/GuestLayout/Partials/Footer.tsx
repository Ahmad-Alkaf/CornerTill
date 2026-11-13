import SupportEmailLink from "@/Components/SupportEmailLink";
import { KAFLABS_URL } from "@/Utilities/Constant";
import { Link } from "@inertiajs/react";
import { Tooltip } from "@material-tailwind/react";
import { Trans, useTranslation } from "react-i18next";

const linkClass =
  "text-gray-500 transition duration-200 hover:text-gray-700 hover:underline";

export default function Footer({ className = "" }: { className?: string }) {
  const { t } = useTranslation();
  return (
    <div className="print:hidden">
      {/* Space for the footer, which sits at the bottom of the page. */}
      <div className="pb-32 sm:pb-24"></div>
      <div className="absolute bottom-0 w-full">
        <footer
          className={
            "my-2 flex min-h-full w-full flex-wrap-reverse items-center justify-center gap-x-6 gap-y-2 px-6 py-4 sm:justify-between " +
            className
          }
        >
          <div className="text-center text-sm text-gray-500 dark:text-gray-400 sm:text-start">
            <p>
              &copy; {new Date().getFullYear()} KafLabs.{" "}
              {t("All rights reserved.")}
            </p>
            <p>
              <Trans
                i18nKey="CornerTill is a <kaflabs>KafLabs</kaflabs> product."
                components={{
                  kaflabs: (
                    <a
                      href={KAFLABS_URL}
                      target="_blank"
                      rel="noopener"
                      className="underline underline-offset-2 transition duration-200 hover:text-gray-700"
                    />
                  ),
                }}
              />
            </p>
          </div>
          <div className="text-center text-sm">
            <Tooltip content={t("Contact us")}>
              <span className="!font-light text-gray-500">
                <SupportEmailLink />
              </span>
            </Tooltip>
            {" · "}
            {/* CornerTill supplements on this domain; they link to the KafLabs master pages. */}
            <Link className={linkClass} href={route("termsAndConditions")}>
              {t("Terms")}
            </Link>
            {" · "}
            <Link className={linkClass} href={route("privacyPolicy")}>
              {t("Privacy Policy")}
            </Link>
          </div>
        </footer>
      </div>
    </div>
  );
}
