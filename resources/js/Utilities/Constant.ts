export const MAX_QUANTITY = 9999;
export const ID_LENGTH = 8;//show last 8 id characters. Maximum length is 26
/**
 * Image types that the server accepts for product images and business logos.
 * Keep it equal to the Laravel `image` rule (jpg, png, gif, bmp, webp; an APNG
 * file is detected as PNG). SVG is blocked; AVIF is not accepted by `image`.
 */
export const ACCEPTED_IMAGE_TYPES =
  "image/jpeg,image/png,image/apng,image/bmp,image/webp,image/gif";

/**
 * CornerTill is a KafLabs product. The KafLabs master Terms and Privacy Policy
 * apply to all KafLabs products; the CornerTill pages (/terms-and-conditions,
 * /privacy-policy) are supplements that link to them.
 */
export const KAFLABS_URL = "https://kaflabs.com";
export const KAFLABS_TERMS_URL = "https://kaflabs.com/terms";
export const KAFLABS_PRIVACY_URL = "https://kaflabs.com/privacy";
/** The contacts of the KafLabs master pages. */
export const PRIVACY_EMAIL = "privacy@kaflabs.com";
export const LEGAL_EMAIL = "legal@kaflabs.com";
export const SUPPORT_EMAIL = "support@kaflabs.com";
/** Keep this date equal to the day the wording of a legal page last changed. */
export const LEGAL_LAST_UPDATED = "13 November 2026";
