import { Option } from "@material-tailwind/react";
import React, { useEffect, useMemo, useState } from "react";
import COUNTRIES, { Country } from "./COUNTRIES";
import { ICreateBusiness } from "@/types";
import SelectInput from "@/Components/Inputs/SelectInput";
import { UseBetterForm } from "@/Utilities/useBetterForm";
import { useTranslation } from "react-i18next";

export default function CurrencyInput<T extends ICreateBusiness>({
  form,
  chosenCountry: country,
}: {
  form: UseBetterForm<T>;
  chosenCountry: Country | null;
}) {
  //One option for each currency name (many countries use the Euro), sorted by name.
  const currencies = useMemo(() => {
    const byName = new Map<string, { name: string; symbol: string }>();
    for (const c of COUNTRIES) {
      const currency = c.currencies?.[0];
      if (currency?.name && currency?.symbol && !byName.has(currency.name))
        byName.set(currency.name, {
          name: currency.name,
          symbol: currency.symbol,
        });
    }
    return [...byName.values()].sort((a, b) =>
      a.name > b.name ? 1 : a.name < b.name ? -1 : 0,
    );
  }, []);

  //The option of the country's currency (found by the currency name, not by the country).
  function getCurrencyIndex(country: Country | null): number | null {
    const name = country?.currencies?.[0]?.name;
    const i = name ? currencies.findIndex((c) => c.name === name) : -1;
    return i === -1 ? null : i;
  }

  useEffect(() => {
    if (country === null) return;
    const index = getCurrencyIndex(country);
    setCurrencyIndex(index);
    //No currency for this country: clear it, so the owner selects one
    //(do not keep the currency of the country selected before).
    form.setData("currency", index === null ? "" : currencies[index].symbol);
  }, [country]);

  const [currencyIndex, setCurrencyIndex] = useState<number | null>(
    getCurrencyIndex(country),
  );

  const { t } = useTranslation();
  return (
    <SelectInput
      label="Currency"
      value={currencyIndex?.toString()}
      errorMsg={form.errors.currency}
      hideError={form.isDirty("currency")}
      disabled={form.processing}
      onChange={(v) => {
        setCurrencyIndex(Number(v));
        form.setData("currency", currencies[Number(v)].symbol);
      }}
      required
      selected={(element) =>
        element &&
        React.cloneElement(element, {
          disabled: true,
          className:
            "flex items-center opacity-100 px-0 gap-2 pointer-events-none",
        })
      }
    >
      {currencies.map((c, i) => (
        <Option
          key={i}
          value={i.toString()}
          className="flex items-center gap-2"
        >
          <span className="w-7 text-gray-900">{c.symbol}</span>
          {t(c.name)}
        </Option>
      ))}
    </SelectInput>
  );
}
