import { H2 } from "@/Components/Typography/H2";
import { H4 } from "@/Components/Typography/H4";
import P from "@/Components/Typography/P";
import GuestAuthLayout from "@/Layouts/GuestAuthLayout";
import { LEGAL_LAST_UPDATED } from "@/Utilities/Constant";
import { IUser } from "@/types";
import { Head } from "@inertiajs/react";
import { PropsWithChildren, ReactNode } from "react";
import { useTranslation } from "react-i18next";

export const linkClass =
  "text-blue-700 transition duration-200 hover:text-blue-500 hover:underline";

/** An external link for <Trans> components (mailto: or another site). */
export function ExtLink({
  href,
  children,
}: PropsWithChildren<{ href: string }>) {
  const external = href.startsWith("http");
  return (
    <a
      href={href}
      className={linkClass}
      {...(external ? { target: "_blank", rel: "noopener" } : {})}
    >
      {children}
    </a>
  );
}

/**
 * Layout of the CornerTill legal supplements (privacy policy and terms):
 * title, subtitle, "Last updated" line, a box that links to the KafLabs
 * master page, and the numbered sections.
 */
export default function LegalPage({
  user,
  title,
  subtitle,
  intro,
  children,
}: PropsWithChildren<{
  user: IUser | null;
  title: string;
  subtitle: string;
  intro: ReactNode;
}>) {
  const { t } = useTranslation();
  return (
    <>
      <Head title={title} />
      <GuestAuthLayout user={user} header={title}>
        <article className="mx-auto mt-6 w-11/12 max-w-4xl space-y-6 md:w-10/12">
          <header className="space-y-2">
            <H2>{title}</H2>
            <P className="text-lg font-semibold">{subtitle}</P>
            <P className="text-sm !text-gray-500">
              {t("Last updated: {{date}}", { date: t(LEGAL_LAST_UPDATED) })}
            </P>
            <div className="mt-4 rounded-xl border border-gray-200 bg-gray-50 p-4">
              {intro}
            </div>
          </header>
          {children}
        </article>
      </GuestAuthLayout>
    </>
  );
}

export function LegalSection({
  n,
  title,
  children,
}: PropsWithChildren<{ n: number; title: string }>) {
  return (
    <section className="space-y-2">
      <H4>
        {n}. {title}
      </H4>
      {children}
    </section>
  );
}
