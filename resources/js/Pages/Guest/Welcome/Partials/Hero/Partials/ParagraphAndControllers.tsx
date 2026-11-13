import SecondaryLink from "@/Components/Buttons/SecondaryLink";
import { useTranslation } from "react-i18next";
import { FaCircleCheck } from "react-icons/fa6";
import useFeatures from "@/Utilities/useFeatures";

export default function ParagraphAndControllers() {
  const { t } = useTranslation();
  const { paymentsEnabled } = useFeatures();
  return (
    <div className="mx-auto max-w-3xl">
      <p
        className="mb-8 text-xl text-gray-600"
        data-aos="zoom-y-out"
        data-aos-delay="150"
      >
        {t(
          "Streamline your business operations with our user-friendly Point of Sale app",
        )}
      </p>
      <div
        className="mx-auto flex max-w-xs justify-center"
        data-aos="zoom-y-out"
        data-aos-delay="300"
      >
        <SecondaryLink
          href={route("register.method")}
          className="!bg-gradient-to-br !from-yellow-600  to-secondary-400 px-7 py-4 normal-case shadow-lg "
        >
          <span className="text-sm tracking-wide">
            {t("Create FREE Account")}
          </span>
        </SecondaryLink>
      </div>
      <div className="mt-5 flex translate-x-[20%] rtl:-translate-x-[20%] flex-col justify-center text-gray-500 md:translate-x-0 md:rtl:translate-x-0 md:flex-row md:space-x-4 md:rtl:space-x-reverse ">
        <div className="flex ">
          <FaCircleCheck className="mx-1 mt-0.5 text-lg text-green-400" />
          {paymentsEnabled ? t("Free Trial") : t("Free during beta")}
        </div>
        <div className="flex">
          <FaCircleCheck className="mx-1 mt-0.5 text-lg text-green-400" />
          { t( "No credit card required" ) }
        </div>
        <div className="flex">
          <FaCircleCheck className="mx-1 mt-0.5 text-lg text-green-400" />
          { t( "Unlimited access to all features" ) }
        </div>
      </div>
    </div>
  );
}
