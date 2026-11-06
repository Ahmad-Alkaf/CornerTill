import KeyValue from "@/Utilities/KeyValue";
import { ISubscriptionLinks } from "@/types";
import { useTranslation } from "react-i18next";
import SubscriptionProgress from "./SubscriptionSection/Partials/SubscriptionProgress";

/**
 * Shown in place of the subscription plans when PAYMENTS_ENABLED=false.
 */
export default function FreeBetaSection({
  progress,
}: {
  progress: ISubscriptionLinks["progress"];
}) {
  const { t } = useTranslation();
  return (
    <section className="bg-white p-4 shadow sm:rounded-lg sm:p-8">
      <div className="space-y-6">
        <header className="max-w-xl">
          <h2 className="text-lg font-medium text-gray-900">{t("Plan")}</h2>
          <p className="text-normal mb-4 text-gray-600">
            {t("Laptop POS is free during beta. No payment is necessary.")}
          </p>
          <KeyValue
            k={t("Plan")}
            v={
              <span className="text-green-700">{t("Free during beta")}</span>
            }
          />
        </header>
        {progress && <SubscriptionProgress progress={progress} />}
      </div>
    </section>
  );
}
