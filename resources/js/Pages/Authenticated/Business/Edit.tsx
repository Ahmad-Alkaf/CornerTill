import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";
import { AuthPageProps, IBusiness, ISubscriptionLinks, IUser } from "@/types";
import AccountsTable from "./Partials/AccountsTable";
import Footer from "@/Layouts/GuestLayout/Partials/Footer";
import IdentitySection from "./Partials/IdentitySection";
import LocationContactSection from "./Partials/LocationContactSection";
import FinancialSection from "./Partials/FinancialSection";
import DeleteBusinessSection from "./Partials/DeleteBusinessSection";
import SubscriptionSection from "./Partials/SubscriptionSection/SubscriptionSection";
import FreeBetaSection from "./Partials/FreeBetaSection";
import { useTranslation } from "react-i18next";
import useFeatures from "@/Utilities/useFeatures";

export default function Edit({
  auth,
  business,
  subscriptionLinks,
}: AuthPageProps<{
  business: IBusiness & { users: IUser[] };
  subscriptionLinks: ISubscriptionLinks;
}>) {

  const { t } = useTranslation();
  const { paymentsEnabled } = useFeatures();
  return (
    <AuthenticatedLayout user={auth.user} header="Business">
      <Head title={ t( "Business"   )}/>

      <div className="py-12">
        <div className="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
          {paymentsEnabled ? (
            <SubscriptionSection subscriptionLinks={subscriptionLinks} business={business} />
          ) : (
            <FreeBetaSection progress={subscriptionLinks.progress} />
          )}
          <AccountsTable business={business} />
          <IdentitySection business={business} />
          <LocationContactSection business={business} />
          <FinancialSection business={business} />
          <DeleteBusinessSection business={business} />
        </div>
      </div>
      <Footer />
    </AuthenticatedLayout>
  );
}
