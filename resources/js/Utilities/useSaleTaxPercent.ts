import { AuthPageProps, IBill, ICreateBill } from "@/types";
import { usePage } from "@inertiajs/react";

/**
 * The tax rate of the checkout cart. A new bill uses the current tax rate of the
 * business. An edited bill keeps the tax rate of its sale (the server stores it).
 */
export default function useSaleTaxPercent(bill: ICreateBill | IBill): number {
  const businessTaxPercent =
    usePage<AuthPageProps>().props.auth.business.taxPercent;
  const saleTaxPercent = (bill as Partial<IBill>).taxPercent;
  return typeof saleTaxPercent === "number"
    ? saleTaxPercent
    : businessTaxPercent;
}
