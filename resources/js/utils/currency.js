/**
 * Paystack-supported African currencies.
 * https://paystack.com/docs/payments/multi-currency
 */
export const PAYSTACK_CURRENCIES = [
    { code: 'NGN', symbol: '₦',    name: 'Nigerian Naira',         locale: 'en-NG' },
    { code: 'GHS', symbol: 'GH₵',  name: 'Ghanaian Cedi',          locale: 'en-GH' },
    { code: 'ZAR', symbol: 'R',     name: 'South African Rand',     locale: 'en-ZA' },
    { code: 'KES', symbol: 'KSh',  name: 'Kenyan Shilling',        locale: 'en-KE' },
    { code: 'XOF', symbol: 'CFA',  name: 'West African CFA Franc', locale: 'fr-CI' },
    { code: 'EGP', symbol: 'E£',   name: 'Egyptian Pound',         locale: 'ar-EG' },
    { code: 'RWF', symbol: 'Rwf',  name: 'Rwandan Franc',          locale: 'rw-RW' },
    { code: 'USD', symbol: '$',     name: 'US Dollar',              locale: 'en-US' },
];

/**
 * Get the currency symbol for a given currency code.
 * Falls back to the code itself if not found.
 */
export function getCurrencySymbol(code) {
    return PAYSTACK_CURRENCIES.find(c => c.code === code)?.symbol ?? (code ?? '₦');
}

/**
 * Format a numeric amount using the locale for the given currency code.
 * Returns a plain number string (no symbol — callers prepend the symbol themselves).
 */
export function formatPrice(amount, currencyCode = 'NGN') {
    const currency = PAYSTACK_CURRENCIES.find(c => c.code === currencyCode);
    const locale = currency?.locale ?? 'en-NG';
    return new Intl.NumberFormat(locale).format(Number(amount) || 0);
}
