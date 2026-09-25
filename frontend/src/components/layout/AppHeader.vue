<template>
  <!-- Announcement bar -->
  <div class="bg-primary text-white text-center py-2 px-4 text-xs font-body tracking-wide">
    <div class="container-site flex items-center justify-center gap-2 flex-wrap">
      <span>Livraison partout en Tunisie</span>
      <span class="text-secondary">•</span>
      <span>Paiement à la livraison</span>
      <span class="text-secondary">•</span>
      <span>Design qui vous ressemble</span>
    </div>
  </div>

  <!-- Navbar -->
  <header
    :class="[
      'sticky top-0 z-40 transition-all duration-300 ease-warm',
      isScrolled
        ? 'bg-surface/95 backdrop-blur-sm shadow-warm border-b border-border/50'
        : 'bg-surface',
    ]"
  >
    <nav class="container-site">
      <div class="flex items-center justify-between h-16 lg:h-18">

        <!-- Logo -->
        <RouterLink :to="{ name: 'home' }" class="flex-shrink-0 flex items-center" aria-label="Zinet Eddar — Accueil">
          <ZinetLogo class="h-10 w-auto" />
        </RouterLink>

        <!-- Desktop nav links -->
        <ul class="hidden lg:flex items-center gap-1">
          <li v-for="link in navLinks" :key="link.name">
            <RouterLink
              :to="link.to"
              class="px-3 py-2 text-sm font-medium transition-colors rounded-sm text-muted hover:text-primary hover:bg-primary/5"
              exact-active-class="!text-primary bg-primary/5"
            >
              {{ link.label }}
            </RouterLink>
          </li>
        </ul>

        <!-- Desktop actions -->
        <div class="hidden lg:flex items-center gap-1">
          <!-- Search -->
          <button
            @click="toggleSearch"
            class="btn-icon text-muted hover:text-primary"
            aria-label="Rechercher"
            id="search-btn"
          >
            <Search :size="18" />
          </button>

          <!-- Account -->
          <RouterLink
            :to="isAuthenticated ? { name: 'account' } : { name: 'login' }"
            class="btn-icon text-muted hover:text-primary"
            :aria-label="isAuthenticated ? 'Mon compte' : 'Se connecter'"
            id="account-btn"
          >
            <User :size="18" />
          </RouterLink>

          <!-- Cart -->
          <button
            @click="handleCartClick"
            class="btn-icon text-muted hover:text-primary relative"
            aria-label="Panier"
            id="cart-btn"
          >
            <ShoppingBag :size="18" />
            <span
              v-if="cartTotal > 0"
              class="absolute -top-0.5 -right-0.5 bg-secondary text-white text-2xs font-bold
                     rounded-full w-4 h-4 flex items-center justify-center leading-none"
            >
              {{ cartTotal > 99 ? '99+' : cartTotal }}
            </span>
          </button>
        </div>

        <!-- Mobile actions -->
        <div class="flex lg:hidden items-center gap-1">
          <button
            @click="handleCartClick"
            class="btn-icon text-muted hover:text-primary relative"
            aria-label="Panier"
          >
            <ShoppingBag :size="18" />
            <span
              v-if="cartTotal > 0"
              class="absolute -top-0.5 -right-0.5 bg-secondary text-white text-2xs font-bold
                     rounded-full w-4 h-4 flex items-center justify-center"
            >
              {{ cartTotal > 99 ? '99+' : cartTotal }}
            </span>
          </button>
          <button
            @click="isMobileMenuOpen = true"
            class="btn-icon text-muted hover:text-primary"
            aria-label="Menu"
            aria-expanded="false"
            id="mobile-menu-btn"
          >
            <Menu :size="20" />
          </button>
        </div>
      </div>

      <!-- Search bar (expanded) -->
      <Transition name="fade">
        <div v-if="isSearchOpen" class="pb-3 animate-slide-up">
          <form @submit.prevent="handleSearch" class="flex gap-2">
            <input
              ref="searchInput"
              v-model="searchQuery"
              type="search"
              placeholder="Rechercher un meuble, une référence…"
              class="input flex-1"
              aria-label="Terme de recherche"
            />
            <button type="submit" class="btn-primary btn-sm px-4">
              <Search :size="16" /> Rechercher
            </button>
          </form>
        </div>
      </Transition>
    </nav>
  </header>

  <!-- Mobile menu -->
  <MobileMenu :is-open="isMobileMenuOpen" @close="isMobileMenuOpen = false" />
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { Search, User, ShoppingBag, Menu } from '@lucide/vue'
import { useAuthStore }  from '@/stores/authStore'
import { useCartStore }  from '@/stores/cartStore'
import ZinetLogo    from '@/components/common/ZinetLogo.vue'
import MobileMenu   from '@/components/layout/MobileMenu.vue'

const router = useRouter()
const route  = useRoute()
const authStore = useAuthStore()
const cartStore = useCartStore()

const isScrolled      = ref(false)
const isMobileMenuOpen= ref(false)
const isSearchOpen    = ref(false)
const searchQuery     = ref('')
const searchInput     = ref(null)

const isAuthenticated = computed(() => authStore.isAuthenticated)
const cartTotal       = computed(() => cartStore.totalItems)

const navLinks = [
  { label: 'Accueil',    to: { name: 'home' } },
  { label: 'Boutique',   to: { name: 'shop' } },
  { label: 'Catégories', to: { name: 'categories' } },
  { label: 'Nouveautés', to: { name: 'new-products' } },
  { label: 'Promotions', to: { name: 'promotions' } },
  { label: 'À propos',   to: { name: 'about' } },
  { label: 'Contact',    to: { name: 'contact' } },
]

async function toggleSearch() {
  isSearchOpen.value = !isSearchOpen.value
  if (isSearchOpen.value) {
    await nextTick()
    searchInput.value?.focus()
  }
}

function handleSearch() {
  if (searchQuery.value.trim()) {
    router.push({ name: 'shop', query: { q: searchQuery.value.trim() } })
    isSearchOpen.value = false
    searchQuery.value  = ''
  }
}

function handleCartClick() {
  if (authStore.isAuthenticated) {
    cartStore.openDrawer()
  } else {
    router.push({ name: 'login', query: { redirect: '/panier' } })
  }
}

function onScroll() {
  isScrolled.value = window.scrollY > 10
}

onMounted(() => window.addEventListener('scroll', onScroll, { passive: true }))
onUnmounted(() => window.removeEventListener('scroll', onScroll))

// Close search on route change
watch(() => route.path, () => { isSearchOpen.value = false })
</script>
