import { IFeatures, PageProps } from "@/types";
import { usePage } from "@inertiajs/react";

/**
 * Launch switches from config/features.php:
 * PAYMENTS_ENABLED and SOCIAL_{GOOGLE,GITHUB,X}_ENABLED.
 */
export default function useFeatures(): IFeatures {
  return usePage<PageProps>().props.features;
}
