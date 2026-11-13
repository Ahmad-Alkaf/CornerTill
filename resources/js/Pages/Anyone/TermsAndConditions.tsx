import LI from "@/Components/Typography/LI";
import P from "@/Components/Typography/P";
import UL from "@/Components/Typography/UL";
import A from "@/Components/Typography/A";
import {
  KAFLABS_TERMS_URL,
  LEGAL_EMAIL,
  SUPPORT_EMAIL,
} from "@/Utilities/Constant";
import { PageProps } from "@/types";
import { Trans, useTranslation } from "react-i18next";
import LegalPage, { ExtLink, LegalSection } from "./Partials/LegalPage";

/**
 * CornerTill supplement to the KafLabs Terms of Service.
 * Each statement here must be true for the code (beta limits, deletion of a business,
 * no e-invoicing integration, no payments). When one of them changes, change
 * this page and LEGAL_LAST_UPDATED (Utilities/Constant.ts).
 */
export default function TermsAndConditions({ auth }: PageProps) {
  const { t } = useTranslation();
  const support = <ExtLink href={`mailto:${SUPPORT_EMAIL}`} />;
  const privacy = <A href={route("privacyPolicy")} />;

  return (
    <LegalPage
      user={auth.user}
      title={t("CornerTill Terms")}
      subtitle={t("Supplement to the KafLabs Terms of Service")}
      intro={
        <P>
          <Trans
            i18nKey="CornerTill is a KafLabs product. These terms are a supplement to the <master>KafLabs Terms of Service</master>, which apply to all KafLabs products. When you use CornerTill, you accept both. Where these terms and the KafLabs Terms of Service differ, these terms apply to CornerTill."
            components={{ master: <ExtLink href={KAFLABS_TERMS_URL} /> }}
          />
        </P>
      }
    >
      <LegalSection n={1} title={t("Free beta")}>
        <P>
          {t(
            "CornerTill is in a free beta. We provide it “as is” and “as available”. Features, screens, and limits can change, and the service can sometimes be unavailable.",
          )}
        </P>
        <P>
          <Trans
            i18nKey="Each business has limits on the number of staff accounts, products, and bills per month. The CornerTill home page shows the current limits, and CornerTill tells you when you reach one. To ask for more, email <mail>support@kaflabs.com</mail>."
            components={{ mail: support }}
          />
        </P>
      </LegalSection>

      <LegalSection n={2} title={t("Your business records")}>
        <P>
          {t(
            "The products, bills, and other records that you enter belong to your business. You allow KafLabs to store, process, and show them only as necessary to run CornerTill for you. We do not sell your records, and we do not use them for advertising.",
          )}
        </P>
        <P>
          {t(
            "If the law requires you to keep sales records for a period of time, also keep your own copies: CornerTill has no export tool yet.",
          )}
        </P>
      </LegalSection>

      <LegalSection n={3} title={t("Prices, taxes, and receipts")}>
        <P>
          {t(
            "You are responsible for the prices, tax rates, and other details that you enter, and for checking each receipt. CornerTill calculates the totals from the data that you enter.",
          )}
        </P>
        <P>
          {t(
            "CornerTill is not a certified fiscal or e-invoicing system. It is not connected to the systems of any tax authority (for example, ZATCA e-invoicing in Saudi Arabia). You are responsible for following the tax, invoicing, and record-keeping laws that apply to your business.",
          )}
        </P>
      </LegalSection>

      <LegalSection n={4} title={t("Accounts and staff access")}>
        <P>
          {t(
            "The Owner of a business decides who gets a staff account and with which role. The Owner is responsible for what is done with the accounts of the business.",
          )}
        </P>
        <P>
          <Trans
            i18nKey="When you add a staff account, you give us the name and email address of that person. Make sure that you may do this and that your staff know how CornerTill uses their data (see the <privacy>Privacy Policy</privacy>)."
            components={{ privacy }}
          />
        </P>
        <P>
          <Trans
            i18nKey="Keep passwords secret. Remove a staff account when that person no longer works for you. If you think that someone used an account without permission, email <mail>support@kaflabs.com</mail> at once."
            components={{ mail: support }}
          />
        </P>
      </LegalSection>

      <LegalSection n={5} title={t("Acceptable use")}>
        <P>{t("Use CornerTill only for the lawful sales of your own business. Do not:")}</P>
        <UL>
          <LI>{t("use it to sell or record illegal goods or services;")}</LI>
          <LI>
            {t(
              "try to get into the data of another business, or into parts of CornerTill that your account cannot use;",
            )}
          </LI>
          <LI>
            {t(
              "upload harmful files, or try to break, overload, or test the security of CornerTill without our written permission;",
            )}
          </LI>
          <LI>
            {t(
              "use automated tools to create accounts or to send large numbers of requests;",
            )}
          </LI>
          <LI>
            {t("resell access to the hosted CornerTill service, or offer it as your own service.")}
          </LI>
        </UL>
        <P>
          {t(
            "We can suspend or close an account that breaks these rules, as the KafLabs Terms of Service describe.",
          )}
        </P>
        <P>
          {t(
            "These terms cover the hosted service at cornertill.com. The CornerTill source code has its own license, which is in the code repository.",
          )}
        </P>
      </LegalSection>

      <LegalSection n={6} title={t("Closing your business account")}>
        <P>
          {t(
            "The Owner can delete the business in the business settings. Before you do this, keep copies of the records that you need: CornerTill has no export tool yet.",
          )}
        </P>
        <P>
          <Trans
            i18nKey="When the Owner deletes the business, no account of the business can sign in any more. CornerTill keeps the data of the business for 30 days. In this time, email <mail>support@kaflabs.com</mail> to restore the business. After 30 days, CornerTill deletes all data of the business permanently, and we cannot restore it."
            components={{ mail: support }}
          />
        </P>
        <P>
          <Trans
            i18nKey="To get a copy of your records, email <mail>support@kaflabs.com</mail>."
            components={{ mail: support }}
          />
        </P>
      </LegalSection>

      <LegalSection n={7} title={t("Paid plans")}>
        <P>
          {t(
            "CornerTill has no paid plans now, and we do not charge you. Before any paid plan starts, we will publish its prices, payment terms, and refund policy. You will then choose whether to pay.",
          )}
        </P>
      </LegalSection>

      <LegalSection n={8} title={t("Liability, changes, and other points")}>
        <P>
          {t(
            "The disclaimers, the limits of liability, and all other points of the KafLabs Terms of Service apply to CornerTill. When we change these terms, we change the “Last updated” date at the top.",
          )}
        </P>
        <P>
          <Trans
            i18nKey="For questions about these terms, email <mail>legal@kaflabs.com</mail>."
            components={{ mail: <ExtLink href={`mailto:${LEGAL_EMAIL}`} /> }}
          />
        </P>
      </LegalSection>
    </LegalPage>
  );
}
