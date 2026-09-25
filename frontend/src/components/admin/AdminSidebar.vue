<template>
  <aside 
    :class="[
      'fixed inset-y-0 left-0 z-40 w-64 bg-surface border-r border-border flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0 lg:sticky lg:top-0 lg:h-screen',
      isOpen ? 'translate-x-0' : '-translate-x-full'
    ]"
  >
    <!-- Header -->
    <div class="p-6 border-b border-border flex justify-between items-center h-16">
      <span class="font-display text-xl font-semibold text-primary">Zinet Admin</span>
      <button @click="$emit('close')" class="lg:hidden text-muted hover:text-primary transition-colors">
        <X :size="20" />
      </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
      <RouterLink 
        v-for="item in navItems" 
        :key="item.name"
        :to="{ name: item.routeName }"
        class="flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-medium transition-colors"
        :class="route.name === item.routeName || route.path.startsWith('/admin/' + item.pathPrefix)
          ? 'bg-primary/5 text-primary' 
          : 'text-muted hover:bg-background hover:text-primary'"
        @click="$emit('close')"
      >
        <component :is="item.icon" :size="18" />
        {{ item.name }}
      </RouterLink>
    </nav>

    <!-- Footer Profile -->
    <div class="p-4 border-t border-border">
      <div class="flex items-center gap-3 mb-4 px-2">
        <div class="w-8 h-8 rounded-full bg-secondary/10 flex items-center justify-center">
          <User :size="16" class="text-secondary" />
        </div>
        <div class="overflow-hidden">
          <p class="text-sm font-medium text-primary truncate">{{ authStore.fullName }}</p>
          <p class="text-xs text-muted truncate">Administrateur</p>
        </div>
      </div>
      <button 
        @click="handleLogout" 
        class="flex items-center gap-2 w-full px-3 py-2 text-sm text-red-500 hover:bg-red-50 rounded-md transition-colors"
      >
        <LogOut :size="16" />
        Déconnexion
      </button>
    </div>
  </aside>
</template>

<script setup>
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import { useToast } from '@/composables/useToast'
import { 
  X, LogOut, User, LayoutDashboard, ShoppingBag, 
  PackageSearch, FolderTree, Users, Boxes,
  Image, Monitor, Settings 
} from '@lucide/vue'

defineProps({
  isOpen: {
    type: Boolean,
    required: true
  }
})

defineEmits(['close'])

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const toast = useToast()

const navItems = [
  { name: 'Tableau de bord', routeName: 'admin-dashboard', icon: LayoutDashboard, pathPrefix: 'dashboard' },
  { name: 'Commandes', routeName: 'admin-commandes', icon: ShoppingBag, pathPrefix: 'commandes' },
  { name: 'Produits', routeName: 'admin-produits', icon: PackageSearch, pathPrefix: 'produits' },
  { name: 'Catégories', routeName: 'admin-categories', icon: FolderTree, pathPrefix: 'categories' },
  { name: 'Clients', routeName: 'admin-clients', icon: Users, pathPrefix: 'clients' },
  { name: 'Stock', routeName: 'admin-stock', icon: Boxes, pathPrefix: 'stock' },
  { name: 'Bannières', routeName: 'admin-bannieres', icon: Image, pathPrefix: 'bannieres' },
  { name: 'Paramètres', routeName: 'admin-settings', icon: Settings, pathPrefix: 'settings' },
]

async function handleLogout() {
  await authStore.logout()
  toast.success('Déconnexion réussie')
  router.push({ name: 'login' })
}
</script>
