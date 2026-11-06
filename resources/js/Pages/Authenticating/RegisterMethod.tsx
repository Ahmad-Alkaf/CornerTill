import PrimaryLink from "@/Components/Buttons/PrimaryLink";
import AppName from "@/Components/Logo/AppName";
import A from "@/Components/Typography/A";
import Divider from "@/Components/Typography/Divider";
import GuestFormLayout from "@/Layouts/GuestLayout/GuestFormLayout";
import { Head, Link } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import { FaGithub } from "react-icons/fa";
import { FaEnvelope, FaXTwitter } from "react-icons/fa6";
import useFeatures from "@/Utilities/useFeatures";

export default function RegisterMethod() {
  const { paymentsEnabled, social } = useFeatures();
  const params = new URLSearchParams(window.location.search);
  const subParam =
    paymentsEnabled && params.get("plan") && params.get("period")
      ? { plan: params.get("plan"), period: params.get("period") }
      : {};
  const anySocial = social.google || social.github || social.x;

  const { t, i18n } = useTranslation();
  return (
    <GuestFormLayout>
      <Head title="Register Method" />
      <div dir={i18n.dir()} className="p-4">
        <h2 className="mb-1 text-2xl font-bold text-primary-900 dark:text-white">
          {t("Signing up for")} <AppName className="!font-extrabold" />{" "}
          {t("is fast and free.")}
        </h2>
        <div className="mt-10 grid space-y-4">
          {social.google && (
          <a
            href={route("register.OAuth", ["google", subParam])}
            className="group block h-12 items-center rounded-full border-2 !border-gray-300 bg-white px-6 py-2 text-center text-xs font-semibold normal-case tracking-wider text-white transition duration-200 ease-in-out hover:!border-primary-800  hover:bg-white focus:outline-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 active:scale-95 disabled:opacity-25 disabled:active:scale-100"
          >
            <div className="relative mt-0.5 flex items-center justify-center space-x-4">
              <img
                src="/assets/google-icon.png"
                className="absolute left-0 w-5"
                alt="google logo"
              />
              <span className="block w-max text-sm font-semibold tracking-wide text-gray-700 transition duration-300 group-hover:text-primary-800 dark:text-white sm:text-base">
                {t("Register with Google")}
              </span>
            </div>
          </a>
          )}
          {/* X does not give the user's email, so the user must verify the email after registration. Off by default (SOCIAL_X_ENABLED=false). */}
          {social.x && (
          <a
            href={route("register.OAuth", ["x", subParam])}
            className="group block h-12 items-center rounded-full border-2 !border-gray-300 bg-white px-6 py-2 text-center text-xs font-semibold normal-case tracking-wider text-white transition duration-200 ease-in-out hover:!border-primary-800  hover:bg-white focus:outline-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 active:scale-95 disabled:opacity-25 disabled:active:scale-100"
          >
            <div className="relative mt-0.5 flex items-center justify-center space-x-4">
              <FaXTwitter className="absolute left-0 h-5 w-5 text-black transition duration-300 group-hover:text-primary-800 " />
              <span className="block w-max text-sm font-semibold tracking-wide text-gray-700 transition duration-300 group-hover:text-primary-800 dark:text-white sm:text-base">
                {t("Register with X")}
              </span>
            </div>
          </a>
          )}
          {social.github && (
          <a
            href={route("register.OAuth", ["github", subParam])}
            className="group block h-12 items-center rounded-full border-2 !border-gray-300 bg-white px-6 py-2 text-center text-xs font-semibold normal-case tracking-wider text-white transition duration-200 ease-in-out hover:!border-primary-800  hover:bg-white focus:outline-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 active:scale-95 disabled:opacity-25 disabled:active:scale-100"
          >
            <div className="relative mt-0.5 flex items-center justify-center space-x-4">
              <FaGithub className="absolute left-0 h-5 w-5 text-black transition duration-300 group-hover:text-primary-800 " />
              <span className="block w-max text-sm font-semibold tracking-wide text-gray-700 transition duration-300 group-hover:text-primary-800 dark:text-white sm:text-base">
                {t("Register with Github")}
              </span>
            </div>
          </a>
          )}

          {anySocial && <Divider>{t("or")}</Divider>}

          <PrimaryLink
            href={route("register", subParam)}
            className="group h-12 !rounded-full border-2 !border-gray-300 bg-white px-6 normal-case  hover:!border-primary-800 hover:bg-white"
          >
            <div className="relative mt-0.5 flex items-center justify-center space-x-4">
              <FaEnvelope className="absolute left-0 h-5 w-5 text-gray-800 transition duration-300 group-hover:text-primary-800" />
              <span className="block w-max text-sm font-semibold tracking-wide text-gray-700 transition duration-300 group-hover:text-primary-800 dark:text-white sm:text-base">
                {t("Continue with email")}
              </span>
            </div>
          </PrimaryLink>
        </div>
        {/* <p className="text-md mt-6 text-center text-gray-600">
          Already registered? <A href={route("login")}>Login</A>
        </p> */}

        <p className="mt-6 text-center text-base">
          <Link
            href={route("login")}
            className="text-gray-600 underline hover:text-gray-900 focus:outline-none "
          >
            {t("Already registered?")}
          </Link>
        </p>

        <p className="mt-4 text-center text-xs text-gray-600">
          {t("By creating an account, you agree to our")}{" "}
          <A href={route("termsAndConditions")}>{t("Terms & Conditions")}</A>{" "}
          {t("and confirm you have read our")}{" "}
          <A href={route("privacyPolicy")}>
            {t("Privacy and Cookie Statement")}
          </A>
          .
        </p>
      </div>
    </GuestFormLayout>
  );
}
