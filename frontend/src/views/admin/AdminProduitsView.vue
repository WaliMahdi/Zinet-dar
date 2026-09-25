<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <h2 class="font-display text-2xl font-medium text-primary">Produits</h2>
      <RouterLink :to="{ name: 'admin-produit-nouveau' }" class="btn-primary flex items-center gap-2">
        <Plus :size="16" /> Ajouter un produit
      </RouterLink>
    </div>

    <!-- Filters -->
    <div class="bg-surface border border-border rounded-lg p-4 flex flex-wrap gap-4 items-center">
      <div class="flex-1 min-w-[200px]">
        <div class="relative">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-muted" :size="16" />
          <input 
            type="text" 
            v-model="filters.nom" 
            @input="handleSearch"
            placeholder="Rechercher par nom..." 
            class="input pl-9 w-full"
          />
        </div>
      </div>
      <!-- You can add category filter here if needed -->
    </div>

    <!-- Loading -->
    <div v-if="produitStore.loading && !produitStore.produits.length" class="space-y-4">
      <div v-for="i in 5" :key="i" class="h-16 skeleton rounded-lg"></div>
    </div>

    <!-- Table -->
    <div v-else class="bg-surface border border-border rounded-lg overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
          <thead>
            <tr class="bg-background/50 border-b border-border text-xs uppercase tracking-wider text-muted font-medium">
              <th class="px-6 py-4">Image</th>
              <th class="px-6 py-4">Produit</th>
              <th class="px-6 py-4 text-right">Prix</th>
              <th class="px-6 py-4 text-center">Stock</th>
              <th class="px-6 py-4 text-center">Statut</th>
              <th class="px-6 py-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border/50 text-sm">
            <tr v-if="produitStore.produits.length === 0">
              <td colspan="6" class="px-6 py-10 text-center text-muted">
                Aucun produit trouvé.
              </td>
            </tr>
            <tr v-else v-for="produit in produitStore.produits" :key="produit.id" class="hover:bg-background/50 transition-colors">
              <td class="px-6 py-4">
                <div class="w-12 h-12 rounded-md bg-background border border-border/50 overflow-hidden flex items-center justify-center">
                  <img v-if="produit.image" :src="produit.image" :alt="produit.nom" class="w-full h-full object-cover" />
                  <PackageSearch v-else :size="20" class="text-muted" />
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="font-medium text-primary truncate max-w-[250px]">{{ produit.nom }}</div>
                <div class="text-xs text-muted">Réf. {{ produit.reference }}</div>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="font-medium text-primary">{{ formatPrice(produit.prix_apres_remise || produit.prix) }}</div>
                <div v-if="produit.remise > 0" class="text-xs text-red-500 line-through">{{ formatPrice(produit.prix) }}</div>
              </td>
              <td class="px-6 py-4 text-center">
                <span 
                  :class="[
                    'px-2 py-1 rounded-full text-xs font-medium',
                    produit.quantite_stock > 5 ? 'bg-green-100 text-green-700' : 
                    produit.quantite_stock > 0 ? 'bg-orange-100 text-orange-700' : 
                    'bg-red-100 text-red-700'
                  ]"
                >
                  {{ produit.quantite_stock }}
                </span>
              </td>
              <td class="px-6 py-4 text-center">
                <button 
                  @click="toggleStatut(produit)"
                  :class="[
                    'relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none',
                    produit.actif ? 'bg-secondary' : 'bg-border'
                  ]"
                >
                  <span 
                    :class="[
                      'pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                      produit.actif ? 'translate-x-4' : 'translate-x-0'
                    ]"
                  />
                </button>
              </td>
              <td class="px-6 py-4 text-right space-x-2">
                <RouterLink 
                  :to="{ name: 'admin-produit-modifier', params: { id: produit.id } }"
                  class="btn-outline px-3 py-1.5 text-xs inline-flex items-center gap-1.5"
                >
                  <Edit :size="14" /> Modifier
                </RouterLink>
                <button 
                  @click="deleteProduit(produit.id)"
                  class="btn-outline px-3 py-1.5 text-xs inline-flex items-center gap-1.5 text-red-600 border-red-200 hover:bg-red-50 hover:border-red-300"
                >
                  <Trash2 :size="14" /> Supprimer
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="p-4 border-t border-border flex justify-center" v-if="produitStore.pagination?.last_page > 1">
        <Pagination 
          :current-page="produitStore.pagination.current_page"
          :last-page="produitStore.pagination.last_page"
          @change="fetchProduits"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { Plus, Search, Edit, Trash2, PackageSearch } from '@lucide/vue'
import { useAdminProduitStore } from '@/stores/adminProduitStore'
import { useToast } from '@/composables/useToast'
import { formatPrice } from '@/composables/useFormatPrice'
import Pagination from '@/components/common/Pagination.vue'

const produitStore = useAdminProduitStore()
const toast = useToast()

const filters = reactive({
  nom: ''
})

const fetchProduits = (page = 1) => {
  const params = { page, per_page: 10 }
  if (filters.nom) params.q = filters.nom
  produitStore.fetchProduits(params)
}

let timeout = null
const handleSearch = () => {
  clearTimeout(timeout)
  timeout = setTimeout(() => {
    fetchProduits(1)
  }, 500)
}

async function toggleStatut(produit) {
  try {
    await produitStore.updateStatut(produit.id, !produit.actif)
    toast.success('Statut mis à jour.')
  } catch (err) {
    toast.error('Erreur lors de la modification.')
  }
}

async function deleteProduit(id) {
  if (!confirm('Voulez-vous vraiment supprimer ce produit ?')) return
  try {
    await produitStore.deleteProduit(id)
    toast.success('Produit supprimé.')
  } catch (err) {
    toast.error('Erreur lors de la suppression.')
  }
}

onMounted(() => {
  fetchProduits()
})
</script>
