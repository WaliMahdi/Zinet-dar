<template>
  <!-- Overlay -->
  <Transition name="fade">
    <div
      v-if="isOpen"
      @click="$emit('close')"
      class="fixed inset-0 bg-primary/60 backdrop-blur-sm z-50 lg:hidden"
    />
  </Transition>

  <!-- Drawer -->
  <Transition name="slide-left">
    <aside
      v-if="isOpen"
      class="fixed top-0 right-0 h-full w-72 bg-surface z-50 shadow-warm-xl lg:hidden flex flex-col"
      role="dialog"
      aria-modal="true"
      aria-label="Menu navigation"
    >
      <!-- Header -->
      <div class="flex items-center justify-between px-6 py-4 border-b border-border">
        <ZinetLogo class="h-8 w-auto" />
        <button @click="$emit('close')" class="btn-icon text-muted" aria-label="Fermer le menu">
          <X :size="20" />
        </button>
      </div>

      <!-- Nav links -->
      <nav class="flex-1 overflow-y-auto py-4 px-4">
        <ul class="space-y-1">
          <li v-for="link in navLinks" :key="link.label">
            <RouterLink
              :to="link.to"
              @click="$emit('close')"
              class="flex items-center gap-3 px-4 py-3 rounded-sm text-sm font-medium text-muted hover:text-primary hover:bg-background transition-colors"
              exact-active-class="!text-primary bg-primary/5"
            >
              {{ link.label }}
            </RouterLink>
          </li>
        </ul>

        <div class="divider my-4" />

        <!-- Auth links -->
        <ul class="space-y-1">
          <li v-if="isAuthenticated">
            <RouterLink
              :to="{ name: 'account' }"
              @click="$emit('close')"
              class="flex items-center gap-3 px-4 py-3 rounded-sm text-sm font-medium text-muted hover:text-primary hover:bg-background"
            >
              <User :size="16" /> Mon compte
            </RouterLink>
          </li>
          <li v-if="isAuthenticated">
            <RouterLink
              :to="{ name: 'orders' }"
              @click="$emit('close')"
              class="flex items-center gap-3 px-4 py-3 rounded-sm text-sm font-medium text-muted hover:text-primary hover:bg-background"
            >
              <Package :size="16" /> Mes commandes
            </RouterLink>
          </li>
          <li v-if="!isAuthenticated">
            <RouterLink
              :to="{ name: 'login' }"
              @click="$emit('close')"
              class="flex items-center gap-3 px-4 py-3 rounded-sm text-sm font-medium text-secondary hover:text-secondary-600"
            >
              <User :size="16" /> Se connecter
            </RouterLink>
          </li>
          <li v-if="!isAuthenticated">
            <RouterLink
              :to="{ name: 'register' }"
              @click="$emit('close')"
              class="flex items-center gap-3 px-4 py-3 rounded-sm text-sm font-medium text-muted hover:text-primary hover:bg-background"
            >
              Créer un compte
            </RouterLink>
          </li>
          <li v-if="isAuthenticated">
            <button
              @click="handleLogout"
              class="flex items-center gap-3 px-4 py-3 rounded-sm text-sm font-medium text-red-500 hover:bg-red-50 w-full text-left transition-colors"
            >
              <LogOut :size="16" /> Se déconnecter
            </button>
          </li>
        </ul>
      </nav>

      <!-- Footer -->
      <div class="px-6 py-4 border-t border-border">
        <p class="text-xs text-muted text-center">Zinet Eddar — Le charme de votre intérieur</p>
      </div>
    </aside>
  </Transition>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { X, User, Package, LogOut } from '@lucide/vue'
import { useAuthStore } from '@/stores/authStore'
import { useToast }     from '@/composables/useToast'
import ZinetLogo from '@/components/common/ZinetLogo.vue'

defineProps({ isOpen: Boolean })
defineEmits(['close'])

const authStore = useAuthStore()
const router    = useRouter()
const toast     = useToast()

const isAuthenticated = computed(() => authStore.isAuthenticated)

const navLinks = [
  { label: 'Accueil',    to: { name: 'home' } },
  { label: 'Boutique',   to: { name: 'shop' } },
  { label: 'Catégories', to: { name: 'categories' } },
  { label: 'Nouveautés', to: { name: 'new-products' } },
  { label: 'Promotions', to: { name: 'promotions' } },
  { label: 'À propos',   to: { name: 'about' } },
  { label: 'Contact',    to: { name: 'contact' } },
]

async function handleLogout() {
  await authStore.logout()
  toast.success('Déconnexion réussie.')
  router.push({ name: 'home' })
}
</script>
