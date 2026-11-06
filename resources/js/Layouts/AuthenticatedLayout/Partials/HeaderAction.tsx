import { ReactNode } from "react";
import { useTranslation } from "react-i18next";

export type HeaderActionProps = { header: ReactNode | string | null };

export default function HeaderAction({ header }: HeaderActionProps) {
  const { t } = useTranslation();
  return (
    <header className="my-0 print:hidden bg-white shadow">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        {typeof header === "string" ? (
          <h2 className="py-5 text-xl font-semibold leading-tight text-gray-800">
            {t(header)}
          </h2>
        ) : (
          header ?? ""
        )}
      </div>
    </header>
  );
}
