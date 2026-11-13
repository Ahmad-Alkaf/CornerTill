import { InertiaFormProps } from "@/types/global";
import { setDataByMethod, setDataByObject } from "@inertiajs/inertia-react";
import { useForm } from "@inertiajs/react";
import { useEffect, useState } from "react";
/**
 * `useBetterForm` is a add-on on `useForm` which adds more functionality such as `setData` with type hint.
 * And `dirty` which tells if a field have been changed, which typically will be used for hiding an error, after user change the field
 */
export type UseBetterForm<T extends object> = Omit<
  InertiaFormProps<T>,
  "setData" | "isDirty" | "processing"
> & {
  //for type hint.
  //We remove `setData` from `InertiaFormProps` and add another `setData` with different param type.
  setData: <K extends keyof T>(key: K, value: T[K]) => void;
  setAllData: setDataByObject<T> & setDataByMethod<T>;
  isDirty: (key?: keyof T) => boolean;
  setProcessing: (isProcessing: boolean) => void;
  readonly processing: boolean;
};

/**
 * @param rememberKey Optional. With a key, the form keeps its data in the browser
 * history, so it comes back after a reload or Back (the checkout cart uses this).
 * The key must be unique on the page and must not change while the component is
 * mounted. Never use it for a form with a password, and not for forms that show
 * server data (a remembered value would hide a newer server value).
 * Without a key (the default) the form starts from `initialValue` each time.
 * A shared default key ("hi") made all forms of a page restore the same data.
 */
export default function useBetterForm<T extends object>(
  initialValue: T,
  rememberKey?: string,
): UseBetterForm<T> {
  const [oldValues, setOldValues] = useState<T>(
    JSON.parse(JSON.stringify(initialValue)),
  );

  //`rememberKey` is fixed for the life of the component, so the same hooks run each render.
  const form =
    rememberKey === undefined
      ? useForm<T>(initialValue)
      : useForm<T>(rememberKey, initialValue);

  const [processing, setProcessing] = useState<boolean>(form.processing);

  useEffect(() => {
    setOldValues(form.data);
  }, [form.hasErrors]);

  useEffect(() => {
    setProcessing(form.processing);
  }, [form.processing]);

  //Use the function form of `setData`. In @inertiajs/react 1.x `setData(key, value)` copies
  //the data of the last render. So two calls in one render (for example two effects) lose
  //the first value: the register form sent `currency: "$"` for Saudi Arabia.
  const setData: UseBetterForm<T>["setData"] = (key, value) => {
    form.setData((previous: T) => ({ ...previous, [key]: value }));
  };

  const setAllData: UseBetterForm<T>["setAllData"] = (data) => {
    form.setData(data);
  };

  const isDirty: UseBetterForm<T>["isDirty"] = (key) => {
    if (key) {
      return form.data[key] !== oldValues[key];
    }
    return form.isDirty;
  };


  const better: UseBetterForm<T> = {
    ...form,
    setData,
    setAllData,
    isDirty,
    get processing() {
      return processing;
    },
    setProcessing: (isProcessing) => setProcessing(isProcessing),
  };
  return better;
}
