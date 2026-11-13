import Input, { InputProps } from "@/Components/Inputs/Input";
import Num from "@/Utilities/Num";
import React from "react";
import { useTranslation } from "react-i18next";

export default function TaxRateInput({
  value,
  onChange,
  disabled,
  errorMsg,
  hideError,
  currency,
}: {
  value: number;
  onChange: InputProps["onChange"];
  disabled: boolean;
  errorMsg: InputProps["errorMsg"];
  hideError: InputProps["hideError"];
  currency?: string; //when registering the <Num showCurrency /> component can't figure out the currency
}) {
  const { t } = useTranslation();
  return (
    <Input
      id="taxPercent"
      label="Tax Rate"
      className="mt-1 block w-full"
      max={100}
      min={0}
      type="number"
      icon={<span>%</span>}
      value={value.toString()}
      onChange={(e) => onChange?.(e)}
      disabled={disabled}
      required
      errorMsg={errorMsg}
      hideError={hideError}
      hint={
        <span className="text-start">
          <span className="font-semibold text-blue-gray-600">
            <Num amount={value} />%
          </span>{" "}
          {t("tax on")}{" "}
          <span className="font-semibold text-blue-gray-600">
            {/* No symbol while the currency is empty (Num would read the business of the logged-in user). */}
            <Num amount={100} showCurrency={!!currency} currency={currency} />
          </span>{" "}
          {t("is")}{" "}
          <span className="font-semibold text-blue-gray-600">
            <Num
              amount={(value / 100) * 100}
              showCurrency={!!currency}
              currency={currency}
            />
          </span>
        </span>
      }
    />
  );
}
