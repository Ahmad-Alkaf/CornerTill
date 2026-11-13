import PrimaryButton from "@/Components/Buttons/PrimaryButton";
import A from "@/Components/Typography/A";
import { Link } from "@inertiajs/react";
import { useTranslation } from "react-i18next";

export default function RegisterAction({
  processing,
}: {
  processing: boolean;
}) {
  const { t } = useTranslation();
  return (
    <>
      <div className="mx-2 mt-2">
        <p className="text-xs text-gray-600">
          {t("By proceeding, you agree to our")}{" "}
          <A href={route("termsAndConditions")}>{t("Terms & Conditions")}</A>{" "}
          {t("and confirm you have read our")}{" "}
          <A href={route("privacyPolicy")}>
            {t("Privacy Policy")}
          </A>
          .
        </p>
      </div>
      <div className="mt-3 flex items-center justify-end">
        <div className="flex items-center justify-end">
          <Link
            href={route("login")}
            className="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none "
          >
            {t("Already registered?")}
          </Link>

          <PrimaryButton type="submit" className="ms-4" disabled={processing}>
            {t("Register")}
          </PrimaryButton>
        </div>
      </div>
    </>
  );
}
