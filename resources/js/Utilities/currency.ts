/**
 * U+20C1 SAUDI RIYAL SIGN (SAMA, February 2025; Unicode 17.0).
 * The bundled font "CornerTill Riyal Sign" shows it on devices that do not
 * have the glyph yet (see resources/css/app.css).
 */
export const SAUDI_RIYAL_SIGN = "⃁";

/**
 * SAMA usage rules: the riyal sign is to the left of the number in all
 * languages, with a space between them. In Arabic (RTL) text, U+20C1 is a weak
 * bidi character and shows to the right of the number. So we show such an
 * amount as an isolated left-to-right unit.
 */
export function isSymbolAlwaysLeft(symbol: string): boolean {
  return symbol.trim() === SAUDI_RIYAL_SIGN;
}
