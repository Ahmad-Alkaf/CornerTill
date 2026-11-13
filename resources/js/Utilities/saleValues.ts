import { IBillDetail } from "@/types";

/*
 * The values stored with a sale. A bill that the old code wrote (for example
 * during a rolling deploy) has no stored values until
 * `php artisan bills:fill-sale-values` runs: then use the current values.
 * A bill detail is "not filled" when its productName is null.
 */

/** Unit price of the sale, tax not included. */
export function saleUnitPrice(detail: IBillDetail): number | null {
  return detail.productName === null ? detail.product.price : detail.price;
}

/** Product name at the time of the sale. */
export function saleProductName(detail: IBillDetail): string {
  return detail.productName ?? detail.product.name;
}
