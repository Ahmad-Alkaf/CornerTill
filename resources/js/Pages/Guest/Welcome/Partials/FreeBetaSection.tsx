import PrimaryLink from "@/Components/Buttons/PrimaryLink";
import { BetaMaxResources } from "@/types";
import { usePage } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import {
  NumberOfAccountsBenefit,
  NumberOfInvoicesBenefit,
  NumberOfProductsBenefit,
  ResponsiveBenefit,
} from "./PricingCards/Partials/SharedBenefits";

/**
 * Shown in place of the pricing cards when PAYMENTS_ENABLED=false.
 */
export default function FreeBetaSection() {
  const { t } = useTranslation();
  const { betaMaxRes } = usePage<{ betaMaxRes: BetaMaxResources }>().props;
  const benefits = [
    <NumberOfAccountsBenefit numberOfAccounts={betaMaxRes.accounts} />,
    <NumberOfProductsBenefit numberOfProducts={betaMaxRes.products} />,
    <NumberOfInvoicesBenefit numberOfInvoices={betaMaxRes.bills} />,
    <ResponsiveBenefit />,
  ];

  return (
    <section id="pricing" className="bg-gray-100">
      <div className="mx-auto max-w-screen-xl px-4 py-8 lg:px-6 lg:py-16">
        <header className="mx-auto mb-10 max-w-screen-md text-center">
          <p className="mb-3 text-lg font-light tracking-wide text-primary-600">
            {t("Free during beta")}
          </p>
          <h2 className="mb-4 text-4xl font-extrabold tracking-tight text-gray-900">
            {t("Use CornerTill free during the beta")}
          </h2>
          <p className="mb-5 font-light text-gray-500 sm:text-xl">
            {t("No payment is necessary. No credit card required.")}
          </p>
        </header>
        <div className="mx-auto flex max-w-lg flex-col rounded-lg border border-gray-100 bg-white p-6 text-center text-gray-900 shadow xl:p-8">
          <h3 className="mb-4 text-2xl font-semibold">{t("Free during beta")}</h3>
          <ul role="list" className="mb-8 space-y-4 text-start">
            {benefits.map((b, i) => (
              <li key={i} className="flex items-center space-x-3 rtl:space-x-reverse">
                {b}
              </li>
            ))}
          </ul>
          <PrimaryLink
            href={route("register.method")}
            className="text-center border-none bg-gradient-to-r from-primary-600 to-primary-800 !text-base normal-case shadow hover:shadow-lg"
          >
            {t("Get Started")}
          </PrimaryLink>
        </div>
      </div>
    </section>
  );
}
