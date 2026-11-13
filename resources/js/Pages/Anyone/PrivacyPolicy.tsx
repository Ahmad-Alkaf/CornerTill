import LI from "@/Components/Typography/LI";
import P from "@/Components/Typography/P";
import UL from "@/Components/Typography/UL";
import {
  KAFLABS_PRIVACY_URL,
  PRIVACY_EMAIL,
  SUPPORT_EMAIL,
} from "@/Utilities/Constant";
import { PageProps } from "@/types";
import { Trans, useTranslation } from "react-i18next";
import LegalPage, { ExtLink, LegalSection } from "./Partials/LegalPage";

const GOOGLE_USER_DATA_POLICY_URL =
  "https://developers.google.com/terms/api-services-user-data-policy";

/**
 * CornerTill supplement to the KafLabs Privacy Policy.
 * Each statement here must be true for the code. When the data that CornerTill
 * collects, the providers, or the retention change, change this page and
 * LEGAL_LAST_UPDATED (Utilities/Constant.ts).
 */
export default function PrivacyPolicy({ auth }: PageProps) {
  const { t } = useTranslation();
  const b = <strong className="font-semibold text-gray-900" />;
  const mail = <ExtLink href={`mailto:${PRIVACY_EMAIL}`} />;
  const support = <ExtLink href={`mailto:${SUPPORT_EMAIL}`} />;

  return (
    <LegalPage
      user={auth.user}
      title={t("CornerTill Privacy Policy")}
      subtitle={t("Supplement to the KafLabs Privacy Policy")}
      intro={
        <P>
          <Trans
            i18nKey="CornerTill is a KafLabs product. This page is a supplement to the <master>KafLabs Privacy Policy</master>, which applies to all KafLabs products. It states the facts that are specific to CornerTill. Where this page and the KafLabs Privacy Policy differ, this page applies to CornerTill."
            components={{ master: <ExtLink href={KAFLABS_PRIVACY_URL} /> }}
          />
        </P>
      }
    >
      <LegalSection n={1} title={t("Who is responsible")}>
        <P>
          {t(
            "KafLabs runs CornerTill. KafLabs is responsible for your account data and for the technical data on this page.",
          )}
        </P>
        <P>
          {t(
            "A business uses CornerTill to record its own sales and to give accounts to its staff. For the staff accounts and the sales records that a business enters, the business decides what to record and is responsible for that data (it is the controller). KafLabs stores and processes that data for the business.",
          )}
        </P>
        <P>
          <Trans
            i18nKey="For privacy questions and requests, email <mail>privacy@kaflabs.com</mail>."
            components={{ mail }}
          />
        </P>
      </LegalSection>

      <LegalSection n={2} title={t("What CornerTill collects")}>
        <P>{t("CornerTill collects only the data below.")}</P>
        <UL>
          <LI>
            <Trans
              i18nKey="<b>Your account:</b> your name, email address, and password. We store only a hash of the password, never the password itself. We also store your role (Owner, Maintainer, or Cashier) and the time when you confirmed your email address."
              components={{ b }}
            />
          </LI>
          <LI>
            <Trans
              i18nKey="<b>Your business:</b> the business name, country, city, address, phone number, currency, and tax rate. If you add them, also the tax identification number and the logo."
              components={{ b }}
            />
          </LI>
          <LI>
            <Trans
              i18nKey="<b>Your records:</b> products (name, price, stock, barcode, description, and image) and bills (the products with their names and prices, the quantities, the tax rate, the currency, the cash received, and the time of each sale). CornerTill also records which account created each product and bill."
              components={{ b }}
            />
          </LI>
          <LI>
            <Trans
              i18nKey="<b>Staff accounts:</b> when an Owner adds a staff member, CornerTill stores that person's name, email address, password hash, and role, and sends an invitation to that email address."
              components={{ b }}
            />
          </LI>
          <LI>
            <Trans
              i18nKey="<b>Server logs:</b> for each request, our web server records the IP address, the date and time, the page requested, the referring page, and the browser type."
              components={{ b }}
            />
          </LI>
        </UL>
        <P>
          {t(
            "CornerTill does not ask for the names or contact details of the customers of your business.",
          )}
        </P>
      </LegalSection>

      <LegalSection n={3} title={t("Cookies and browser storage")}>
        <P>{t("CornerTill uses only the cookies that it needs to work:")}</P>
        <UL>
          <LI>{t("a session cookie that keeps you signed in;")}</LI>
          <LI>
            {t(
              "a security cookie (XSRF-TOKEN) that protects forms against cross-site request forgery;",
            )}
          </LI>
          <LI>
            {t(
              "a “remember me” cookie, only when you select “Remember me” at sign-in;",
            )}
          </LI>
          <LI>{t("a language cookie (i18next) that keeps your language.")}</LI>
        </UL>
        <P>
          {t(
            "Your browser also keeps your language and some checkout settings (for example the size of the product tiles and whether to print after each sale) in its local storage. These settings stay on your device.",
          )}
        </P>
        <P>
          {t(
            "CornerTill has no advertising, and it sets no advertising or cross-site tracking cookies.",
          )}
        </P>
        <P>
          {t(
            "To count visits and to measure how fast pages load, CornerTill uses Cloudflare Web Analytics. Cloudflare adds a small script to each page. For each page view, Cloudflare records the page address (Cloudflare states that it does not currently record the query string), the referring page, the page-load timings, and the country, browser, operating system, and device type of the visitor.",
          )}
        </P>
        <P>
          {t(
            "Cloudflare states that Web Analytics uses no cookies or local storage, does not fingerprint visitors, and does not track visitors across websites.",
          )}
        </P>
      </LegalSection>

      <LegalSection n={4} title={t("Registration with Google, GitHub, or X")}>
        <P>
          {t(
            "You can register with your Google, GitHub, or X account. The provider then sends CornerTill your name (or user name) and your email address. CornerTill uses them only to fill in the registration form and to mark your email address as confirmed. You still choose your own password.",
          )}
        </P>
        <P>
          {t(
            "CornerTill does not store your provider account ID, your profile picture, or any access token. It does not read or change any other data in your Google, GitHub, or X account. We do not sell this data, we do not use it for advertising, and we share it only as this page describes.",
          )}
        </P>
        <P>
          <Trans
            i18nKey="CornerTill's use of information received from Google APIs follows the <policy>Google API Services User Data Policy</policy>, including the Limited Use requirements."
            components={{
              policy: <ExtLink href={GOOGLE_USER_DATA_POLICY_URL} />,
            }}
          />
        </P>
      </LegalSection>

      <LegalSection n={5} title={t("How we use the data")}>
        <UL>
          <LI>
            {t(
              "to run CornerTill: sign you in, store and show your business records, and print receipts;",
            )}
          </LI>
          <LI>
            {t(
              "to send service emails: email confirmation, password reset, staff invitations, and a welcome email;",
            )}
          </LI>
          <LI>{t("to keep CornerTill secure, and to find and fix errors;")}</LI>
          <LI>{t("to answer your support requests;")}</LI>
          <LI>
            {t(
              "to count visits and to measure how fast pages load (see section 3);",
            )}
          </LI>
          <LI>
            {t(
              "to count the total numbers of businesses, accounts, products, and bills, so that we can plan the service.",
            )}
          </LI>
        </UL>
        <P>
          {t("We do not sell personal data, and we do not use it for advertising.")}
        </P>
      </LegalSection>

      <LegalSection n={6} title={t("Receipt links")}>
        <P>
          {t(
            "Each printed receipt has a QR code with a link to its bill. The link contains a long random ID. Anyone who has the link can open the bill for 180 days after the sale. The bill page shows the business details, the products, the amounts, and the name of the staff member who made the sale. After 180 days, only signed-in accounts of the business can open the bill. The link stops working when the bill is changed or deleted, or when the business is deleted.",
          )}
        </P>
      </LegalSection>

      <LegalSection n={7} title={t("Service providers")}>
        <P>
          {t(
            "We share data only with the providers that help us run CornerTill, and only as much as their work needs:",
          )}
        </P>
        <UL>
          <LI>
            <Trans
              i18nKey="<b>Hetzner</b> hosts the CornerTill servers and database."
              components={{ b }}
            />
          </LI>
          <LI>
            <Trans
              i18nKey="<b>Cloudflare</b> provides the network, DNS, and protection for cornertill.com, and the visit and page-speed statistics (Cloudflare Web Analytics). All traffic to CornerTill passes through Cloudflare."
              components={{ b }}
            />
          </LI>
          <LI>
            <Trans
              i18nKey="<b>Resend</b> sends the CornerTill emails."
              components={{ b }}
            />
          </LI>
          <LI>
            <Trans
              i18nKey="<b>Google, GitHub, and X</b> only when you choose to register with one of them (see section 4)."
              components={{ b }}
            />
          </LI>
        </UL>
        <P>
          {t(
            "CornerTill takes no payments now, so no payment provider receives your data. Before any paid plan starts, we will name the payment provider on this page.",
          )}
        </P>
        <P>
          {t(
            "We can also disclose data when the law requires it. Our providers can process data in countries other than yours; the KafLabs Privacy Policy describes these international transfers.",
          )}
        </P>
      </LegalSection>

      <LegalSection n={8} title={t("How long we keep data")}>
        <UL>
          <LI>
            {t(
              "Account, business, product, and bill data stay while the business uses CornerTill. CornerTill does not delete old bills automatically.",
            )}
          </LI>
          <LI>
            {t(
              "An Owner can remove a staff account. A removed account can be restored, or the Owner can delete it permanently.",
            )}
          </LI>
          <LI>
            {t(
              "When an Owner deletes the business, CornerTill signs the Owner out, and no account of the business can sign in any more. CornerTill keeps all data of the business for 30 days, so that the Owner can ask us to restore the business. After 30 days, CornerTill automatically and permanently deletes all data of the business: the business details and logo, the products and product images, the bills, and all accounts of the business.",
            )}
          </LI>
        </UL>
        <P>
          <Trans
            i18nKey="To restore a deleted business in these 30 days, email <mail>support@kaflabs.com</mail>."
            components={{ mail: support }}
          />
        </P>
      </LegalSection>

      <LegalSection n={9} title={t("Your rights")}>
        <P>
          {t(
            "You can change your name, email address, and password on your profile page. An Owner can change the business details and remove staff accounts. CornerTill has no export tool yet.",
          )}
        </P>
        <P>
          <Trans
            i18nKey="To get a copy of your data, to have it corrected or deleted, or to use any other right that the KafLabs Privacy Policy lists, email <mail>privacy@kaflabs.com</mail>."
            components={{ mail }}
          />
        </P>
        <P>
          {t(
            "If you are a staff member, first contact the business you work for. The business is responsible for your staff account, and we help the business to answer your request.",
          )}
        </P>
      </LegalSection>

      <LegalSection n={10} title={t("Children")}>
        <P>
          {t(
            "CornerTill is a tool for businesses. It is not directed to children under 13, and we do not knowingly collect their data.",
          )}
        </P>
      </LegalSection>

      <LegalSection n={11} title={t("Changes and other points")}>
        <P>
          {t(
            "When we change this page, we change the “Last updated” date at the top. For all points that this page does not cover, the KafLabs Privacy Policy applies.",
          )}
        </P>
      </LegalSection>
    </LegalPage>
  );
}
