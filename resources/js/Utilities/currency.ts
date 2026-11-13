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
/**
 * Old Saudi riyal symbols that mean the Saudi riyal in any country (the same
 * list as App\Support\Currency::OLD_SAUDI_RIYAL_SYMBOLS). The server already
 * stores the new sign; this keeps the display right for an old stored value
 * (for example on a bill that was not normalized yet).
 */
const OLD_SAUDI_RIYAL_SYMBOLS = ["ر.س", "ر.س.", "ر. س"];

/** The symbol to show: an old Saudi riyal symbol shows as the Saudi riyal sign. */
export function displaySymbol(symbol: string): string {
  return OLD_SAUDI_RIYAL_SYMBOLS.includes(symbol.trim())
    ? SAUDI_RIYAL_SIGN
    : symbol;
}

export function isSymbolAlwaysLeft(symbol: string): boolean {
  return symbol.trim() === SAUDI_RIYAL_SIGN;
}
