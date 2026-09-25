import productPlaceholder from '@/assets/images/product-placeholder.jpg'

const STORAGE_URL = import.meta.env.VITE_STORAGE_URL

/**
 * Resolves the best available image URL for a product.
 * Priority:
 *  1. images[] gallery — principale === true
 *  2. images[0] if any exist
 *  3. product.image (already a full URL from API)
 *  4. fallback placeholder
 */
export function getProductImage(product) {
  if (!product) return productPlaceholder

  // 1. Look for principal image in gallery
  if (product.images && product.images.length > 0) {
    const principale = product.images.find((img) => img.is_principale)
    const chosen = principale || product.images[0]
    if (chosen?.url) {
      return chosen.url.startsWith('http')
        ? chosen.url
        : `${STORAGE_URL}/${chosen.url}`
    }
  }

  // 2. Top-level image field (API returns full URL)
  if (product.image) {
    return product.image.startsWith('http')
      ? product.image
      : `${STORAGE_URL}/${product.image}`
  }

  // 3. Fallback
  return productPlaceholder
}

/**
 * Resolves a category image or returns a category-specific placeholder gradient.
 */
export function getCategoryImage(categorie) {
  if (!categorie) return null
  if (categorie.image) {
    return categorie.image.startsWith('http')
      ? categorie.image
      : `${STORAGE_URL}/${categorie.image}`
  }
  return null
}

export function useProductImage() {
  return { getProductImage, getCategoryImage }
}
