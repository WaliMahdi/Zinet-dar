/**
 * Formats a number as Tunisian Dinar (TND).
 * e.g. 1250.500 → "1 250,500 TND"
 */
export function formatPrice(value) {
  if (value === null || value === undefined || value === '') return '—'
  const num = parseFloat(value)
  if (isNaN(num)) return '—'
  return num.toLocaleString('fr-TN', {
    minimumFractionDigits: 3,
    maximumFractionDigits: 3,
  }) + ' TND'
}

/**
 * Formats a discount percentage.
 * e.g. 20 → "-20%"
 */
export function formatDiscount(remise) {
  if (!remise || remise <= 0) return null
  return `-${Math.round(remise)}%`
}

/**
 * Calculates the discount percentage between prix and prix_apres_remise.
 */
export function calcDiscountPercent(prix, prixApresRemise) {
  if (!prix || !prixApresRemise || prix <= 0) return 0
  return Math.round(((prix - prixApresRemise) / prix) * 100)
}

export function useFormatPrice() {
  return { formatPrice, formatDiscount, calcDiscountPercent }
}
