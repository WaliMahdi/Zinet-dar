<template>
  <div class="min-h-screen bg-background">
    <!-- Top breadcrumb bar -->
    <div class="bg-surface py-4 border-b border-border">
      <div class="container-site flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <Breadcrumb :items="breadcrumbItems" />
        <p class="text-muted text-sm">{{ pagination?.total || 0 }} produit{{ (pagination?.total || 0) > 1 ? 's' : '' }}</p>
      </div>
    </div>

    <!-- Banner (which now displays the title) -->
    <BannerSection :section="currentSection" :title="pageTitle" eyebrow="Collection 2026" />

    <div class="container-site py-8 md:py-12">
      <div class="flex gap-8 items-start">

        <!-- Filters sidebar — desktop -->
        <div class="hidden lg:block w-64 flex-shrink-0">
          <ProductFilters
            :filters="filters"
            :categories="categories"
            @update:filters="onFiltersChange"
            @reset="resetFilters"
          />
        </div>

        <!-- Main content -->
        <div class="flex-1 min-w-0">
          <!-- Mobile filter toggle -->
          <div class="flex items-center justify-between mb-6 lg:hidden">
            <p class="text-sm text-muted">{{ pagination?.total || 0 }} résultat{{ (pagination?.total || 0) > 1 ? 's' : '' }}</p>
            <button @click="showMobileFilters = !showMobileFilters" class="btn-outline btn-sm">
              <SlidersHorizontal :size="14" /> Filtres
            </button>
          </div>

          <!-- Mobile filters drawer -->
          <Transition name="slide-up">
            <div v-if="showMobileFilters" class="lg:hidden bg-surface border border-border rounded-lg p-5 mb-6">
              <ProductFilters
                :filters="filters"
                :categories="categories"
                @update:filters="onFiltersChange"
                @reset="resetFilters"
              />
            </div>
          </Transition>

          <!-- Loading -->
          <SkeletonCard v-if="loading" :count="12" />

          <!-- Error -->
          <ErrorState v-else-if="error" :message="error" @retry="loadProducts" />

          <!-- Products -->
          <template v-else>
            <ProductGrid :products="products">
              <template #empty v-if="filters.categorie_id && !filters.q">
                 <EmptyState
                  icon="package"
                  title="Catégorie vide"
                  description="Aucun produit disponible dans cette catégorie."
                 />
              </template>
            </ProductGrid>
            <Pagination
              v-if="pagination"
              :current-page="pagination.current_page"
              :last-page="pagination.last_page"
              @change="onPageChange"
            />
          </template>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { ChevronRight, SlidersHorizontal } from '@lucide/vue'
import { produitService }  from '@/services/produitService'
import { categorieService } from '@/services/categorieService'
import { debounce } from '@/utils/helpers'
import ProductFilters from '@/components/products/ProductFilters.vue'
import ProductGrid    from '@/components/products/ProductGrid.vue'
import SkeletonCard   from '@/components/common/SkeletonCard.vue'
import ErrorState     from '@/components/common/ErrorState.vue'
import Pagination     from '@/components/common/Pagination.vue'
import BannerSection  from '@/components/common/BannerSection.vue'
import Breadcrumb     from '@/components/common/Breadcrumb.vue'

const route  = useRoute()
const router = useRouter()

const pageTitle = computed(() => route.meta.title || 'Boutique')
const currentSection = computed(() => {
  if (route.name === 'new-products') return 'nouveautes'
  if (route.name === 'promotions') return 'promotions'
  return 'boutique'
})

const products         = ref([])

const categories       = ref([])

const breadcrumbItems = computed(() => {
  const items = []
  
  if (route.name === 'new-products') {
    items.push({ label: 'Nouveautés' })
  } else if (route.name === 'promotions') {
    items.push({ label: 'Promotions' })
  } else {
    if (filters.categorie_id) {
      items.push({ label: 'Catégories', to: { name: 'categories' } })
      const catName = categories.value.find(c => c.id === filters.categorie_id)?.nom
      items.push({ label: catName || 'Chargement...' })
    } else {
      items.push({ label: 'Boutique' })
    }
  }
  return items
})
const pagination       = ref(null)
const loading          = ref(true)
const error            = ref(null)
const showMobileFilters= ref(false)

const filters = reactive({
  q:              route.query.q      || null,
  categorie_id:   route.query.categorie_id ? Number(route.query.categorie_id) : null,
  prix_min:       route.query.prix_min || null,
  prix_max:       route.query.prix_max || null,
  nouveau:        route.name === 'new-products' ? true : (route.query.nouveau === '1' ? true : null),
  promo:          route.name === 'promotions' ? true : (route.query.promo === '1' ? true : null),
  vedette:        route.query.vedette === '1' ? true : null,
  sort:           route.query.sort   || 'created_at',
  order:          route.query.order  || 'desc',
  page:           Number(route.query.page) || 1,
  per_page:       12,
})

let currentAbortController = null

async function loadProducts() {
  if (currentAbortController) {
    currentAbortController.abort()
  }
  const abortController = new AbortController()
  currentAbortController = abortController

  loading.value = true
  error.value   = null
  try {
    // Build clean params (skip null/undefined)
    const params = Object.fromEntries(
      Object.entries(filters).filter(([_, v]) => v !== null && v !== undefined && v !== false && v !== '')
    )
    const response = await produitService.getAll(params, { signal: abortController.signal })
    products.value   = response.data || []
    pagination.value = response.pagination || null
  } catch (err) {
    if (err.name === 'CanceledError' || err.message === 'canceled') {
      return // Ignore canceled requests
    }
    error.value = 'Impossible de charger les produits.'
  } finally {
    if (currentAbortController === abortController) {
      loading.value = false
    }
  }
}

const debouncedLoad = debounce(loadProducts, 350)

function onFiltersChange(newFilters) {
  // Clear search if category is explicitly changed
  if (newFilters.categorie_id !== filters.categorie_id) {
    newFilters.q = null
  }
  Object.assign(filters, { ...newFilters, page: 1 })
  updateRouteQuery()
  debouncedLoad()
}

function resetFilters() {
  Object.assign(filters, {
    q: null, categorie_id: null,
    prix_min: null, prix_max: null, 
    nouveau: route.name === 'new-products' ? true : null, 
    promo: route.name === 'promotions' ? true : null, 
    vedette: null,
    sort: 'created_at', order: 'desc', page: 1,
  })
  updateRouteQuery()
  loadProducts()
}

function onPageChange(page) {
  filters.page = page
  updateRouteQuery()
  loadProducts()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

function updateRouteQuery() {
  const q = {}
  if (filters.q)             q.q = filters.q
  if (filters.categorie_id)  q.categorie_id = filters.categorie_id
  if (filters.nouveau && route.name !== 'new-products') q.nouveau = '1'
  if (filters.promo && route.name !== 'promotions') q.promo = '1'
  if (filters.vedette)       q.vedette = '1'
  if (filters.prix_min)      q.prix_min = filters.prix_min
  if (filters.prix_max)      q.prix_max = filters.prix_max
  if (filters.sort !== 'created_at') q.sort = filters.sort
  if (filters.order !== 'desc')      q.order = filters.order
  if (filters.page > 1)              q.page = filters.page
  router.replace({ query: q })
}

watch(
  () => route.name,
  (newName, oldName) => {
    if (newName !== oldName && ['shop', 'new-products', 'promotions'].includes(newName)) {
      Object.assign(filters, {
        q:              null,
        categorie_id:   null,
        prix_min:       null,
        prix_max:       null,
        nouveau:        newName === 'new-products' ? true : null,
        promo:          newName === 'promotions' ? true : null,
        vedette:        null,
        sort:           'created_at',
        order:          'desc',
        page:           1,
      })
      updateRouteQuery()
      loadProducts()
    }
  }
)

watch(
  () => route.query,
  (newQuery) => {
    let changed = false
    
    const newQ = newQuery.q || null
    if (filters.q !== newQ) {
      filters.q = newQ
      changed = true
    }
    
    const newCat = newQuery.categorie_id ? Number(newQuery.categorie_id) : null
    if (filters.categorie_id !== newCat) {
      filters.categorie_id = newCat
      changed = true
    }
    
    const newPage = Number(newQuery.page) || 1
    if (filters.page !== newPage) {
      filters.page = newPage
      changed = true
    }

    if (changed) {
      loadProducts()
    }
  },
  { deep: true }
)

onMounted(async () => {
  const [, cats] = await Promise.allSettled([
    loadProducts(),
    categorieService.getAll(),
  ])
  if (cats.status === 'fulfilled') categories.value = cats.value.data || []
})
</script>
