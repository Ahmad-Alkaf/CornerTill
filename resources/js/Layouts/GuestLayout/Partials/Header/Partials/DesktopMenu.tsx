import PrimaryLink from "@/Components/Buttons/PrimaryLink";
import TextLink from "@/Components/TextLink";
import { useTranslation } from "react-i18next";
import { FaArrowRight } from "react-icons/fa";

export default function DesktopMenu() {
  const { t } = useTranslation();
  return (
    <nav className="hidden md:flex md:grow">
      <ul className="flex grow flex-wrap items-center justify-end">
        <li>
          <TextLink href={route("login")}>{t("Login")}</TextLink>
        </li>
        <li>
          <PrimaryLink
            className="group ms-5 flex normal-case"
            href={route("register.method")}
          >
            <span>{t("Register")}&nbsp;</span>
            <FaArrowRight className="transition group-hover:translate-x-1 rtl:rotate-180 rtl:group-hover:-translate-x-1" />
          </PrimaryLink>
        </li>
      </ul>
    </nav>
  );
}
