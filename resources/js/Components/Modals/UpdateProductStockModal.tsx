import React, { FormEvent, useEffect, useState } from "react";
import TemplateModal from "./TemplateModal";
import PrimaryMaterialBtn from "../Buttons/Material/PrimaryMaterialBtn";
import { IProduct } from "@/types";
import useBetterForm from "@/Utilities/useBetterForm";
import Input from "../Inputs/Input";
import SecondaryMaterialBtn from "../Buttons/Material/SecondaryMaterialBtn";
import { VisitOptions } from "@inertiajs/inertia";
import { useTranslation } from "react-i18next";

export default function UpdateProductStockModal({
  modalAction,
  close,
}: {
  close: () => void;
  modalAction:
    | { open: true; product: IProduct }
    | { open: false; product?: IProduct };
}) {
  const product = modalAction.product;
  const [increase, setIncrease] = useState<number>(0);
  /** The stock to send. A product without stock tracking (stock null) stays
   * without it when nothing is added. */
  const newStock = (add: number): number | null =>
    (product?.stock ?? null) === null && add === 0
      ? null
      : (product?.stock ?? 0) + add;
  const form = useBetterForm<{ stock: number | null }>({
    stock: product?.stock ?? null,
  });

  //The modal stays mounted: each time it opens, start from the current stock of
  //its product. Without this, "Update" with no change sent the stock of the first
  //render (0) and set the stock of the product to 0.
  useEffect(() => {
    if (!modalAction.open) return;
    setIncrease(0);
    form.setData("stock", newStock(0));
  }, [modalAction.open, product]);

  function handleSubmit(e: FormEvent) {
    e.preventDefault();
    form.patch(route(`product.update`, product?.id), {
      preserveScroll: true,
      onSuccess: () => {
        form.clearErrors();
        form.reset();
        close();
      },
    } as VisitOptions);
  }

  const { t } = useTranslation();
  return (
    <TemplateModal
      title="Update Stock"
      open={modalAction.open}
      closeModal={() => {
        close();
        form.reset();
        form.clearErrors();
      }}
    >
      <form className="mt-3" onSubmit={handleSubmit}>
        <Input
          id="stock"
          label="Add to Stock"
          type="number"
          name="stock"
          autoFocus
          value={increase}
          className="mt-1 block w-full"
          disabled={form.processing}
          onChange={(e) => {
            form.setData("stock", newStock(Number(e.target.value)));
            setIncrease(Number(e.target.value));
          }}
          errorMsg={form.errors.stock}
          hideError={form.isDirty("stock")}
          onFocus={(e) => e.target.select()}
          required={true}
          hint={
            <span>
              {t("Current stock")}{" "}
              <span className="font-semibold text-gray-600">
                {product?.stock ?? t("N/A")}
              </span>
              {". "}
              {t("Will update to")}{" "}
              <span className="font-semibold text-gray-600">
                {(product?.stock ?? 0) + increase}
              </span>
            </span>
          }
        />

        <div className="mt-4 flex flex-col gap-4 sm:flex-row-reverse">
          <PrimaryMaterialBtn type="submit" disabled={form.processing}>
            {t("Update")}
          </PrimaryMaterialBtn>
          <SecondaryMaterialBtn
            type="button"
            onClick={() => {
              form.cancel();
              close();
              form.reset();
              form.clearErrors();
            }}
          >
            {t("Cancel")}
          </SecondaryMaterialBtn>
        </div>
      </form>
    </TemplateModal>
  );
}
