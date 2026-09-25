/**
 * Translates order status keys to French labels.
 */
export const ORDER_STATUS_LABELS = {
  en_attente:     'En attente',
  confirmee:      'Confirmée',
  livree:         'Livrée',
  annulee:        'Annulée',
}

/**
 * Returns the CSS class for an order status badge.
 */
export function getStatusClass(statut) {
  const map = {
    en_attente:     'status-en_attente',
    confirmee:      'status-confirmee',
    livree:         'status-livree',
    annulee:        'status-annulee',
  }
  return map[statut] || 'status-badge bg-gray-100 text-gray-600'
}

/**
 * Formats a date string to French locale.
 */
export function formatDate(dateString) {
  if (!dateString) return '—'
  return new Date(dateString).toLocaleDateString('fr-TN', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

/**
 * Truncates text to a given length.
 */
export function truncate(text, maxLength = 80) {
  if (!text) return ''
  return text.length > maxLength ? text.slice(0, maxLength) + '…' : text
}

/**
 * Debounce utility.
 */
export function debounce(fn, delay = 300) {
  let timer
  return (...args) => {
    clearTimeout(timer)
    timer = setTimeout(() => fn(...args), delay)
  }
}

/**
 * Category icon map — maps category names to emoji/icon slugs.
 */
export const CATEGORY_ICONS = {
  meubles:    '🪑',
  tables:     '🪵',
  chaises:    '🪑',
  fauteuils:  '🛋️',
  canapés:    '🛋️',
  canapés:    '🛋️',
  miroirs:    '🪞',
  décoration: '🏮',
  accessoires:'🏠',
}
