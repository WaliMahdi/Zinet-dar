<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <h2 class="font-display text-2xl font-medium text-primary">Gestion des stocks</h2>
    </div>

    <!-- Filters -->
    <div class="bg-surface border border-border rounded-lg p-4 flex flex-wrap gap-4 items-center justify-between">
      <div class="flex-1 min-w-[200px]">
        <div class="relative">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-muted" :size="16" />
          <input 
            type="text" 
            v-model="filters.nom" 
            @input="handleSearch"
            placeholder="Rechercher un produit..." 
            class="input pl-9 w-full max-w-sm"
          />
        </div>
      </div>
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
              <th class="px-6 py-4">Produit</th>
              <th class="px-6 py-4 text-center">État du stock</th>
              <th class="px-6 py-4 text-center">Quantité actuelle</th>
              <th class="px-6 py-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border/50 text-sm">
            <tr v-if="produitStore.produits.length === 0">
              <td colspan="4" class="px-6 py-10 text-center text-muted">
                Aucun produit trouvé.
              </td>
            </tr>
            <tr v-else v-for="produit in produitStore.produits" :key="produit.id" class="hover:bg-background/50 transition-colors">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded bg-background border border-border/50 overflow-hidden flex-shrink-0 flex items-center justify-center">
                    <img v-if="produit.image" :src="produit.image" class="w-full h-full object-cover" />
                    <PackageSearch v-else :size="16" class="text-muted" />
                  </div>
                  <div>
                    <div class="font-medium text-primary">{{ produit.nom }}</div>
                    <div class="text-xs text-muted">Réf. {{ produit.reference }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 text-center">
                <span 
                  :class="[
                    'px-2 py-1 rounded-full text-xs font-medium inline-flex items-center gap-1.5',
                    produit.quantite_stock > 5 ? 'bg-green-100 text-green-700' : 
                    produit.quantite_stock > 0 ? 'bg-orange-100 text-orange-700' : 
                    'bg-red-100 text-red-700'
                  ]"
                >
                  <span class="w-1.5 h-1.5 rounded-full" 
                    :class="[
                      produit.quantite_stock > 5 ? 'bg-green-500' : 
                      produit.quantite_stock > 0 ? 'bg-orange-500' : 'bg-red-500'
                    ]">
                  </span>
                  {{ 
                    produit.quantite_stock > 5 ? 'En stock' : 
                    produit.quantite_stock > 0 ? 'Stock faible' : 'Rupture' 
                  }}
                </span>
              </td>
              <td class="px-6 py-4 text-center">
                <div class="font-display text-lg font-medium text-primary">{{ produit.quantite_stock }}</div>
              </td>
              <td class="px-6 py-4 text-right">
                <button 
                  @click="openStockModal(produit)"
                  class="btn-outline px-3 py-1.5 text-xs inline-flex items-center gap-1.5"
                >
                  <Edit3 :size="14" /> Ajuster
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

    <!-- Modale Ajustement de stock -->
    <div v-if="isModalOpen && selectedProduit" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/50" @click="closeModal"></div>
      
      <div class="bg-surface rounded-lg shadow-xl w-full max-w-sm relative z-10 overflow-hidden">
        <div class="px-6 py-4 border-b border-border flex justify-between items-center bg-background/50">
          <h3 class="font-display font-medium text-primary text-lg">Ajuster le stock</h3>
          <button @click="closeModal" class="text-muted hover:text-primary transition-colors">
            <X :size="20" />
          </button>
        </div>
        
        <form @submit.prevent="updateStock" class="p-6 space-y-5">
          <div>
            <p class="font-medium text-primary text-sm">{{ selectedProduit.nom }}</p>
            <p class="text-xs text-muted">Réf. {{ selectedProduit.reference }}</p>
          </div>
          
          <div class="space-y-2">
            <label class="label text-sm">Nouvelle quantité en stock</label>
            <div class="flex items-center gap-3">
              <button 
                type="button" 
                @click="newQuantite = Math.max(0, newQuantite - 1)"
                class="w-10 h-10 rounded-md border border-border flex items-center justify-center text-muted hover:bg-background transition-colors"
              >
                -
              </button>
              <input 
                v-model.number="newQuantite" 
                type="number" 
                min="0" 
                required 
                class="input text-center font-display text-lg" 
              />
              <button 
                type="button" 
                @click="newQuantite++"
                class="w-10 h-10 rounded-md border border-border flex items-center justify-center text-muted hover:bg-background transition-colors"
              >
                +
              </button>
            </div>
          </div>
          
          <div class="pt-2 flex justify-end gap-3 border-t border-border mt-6">
            <button type="button" @click="closeModal" class="btn-outline px-4">Annuler</button>
            <button type="submit" :disabled="isSaving" class="btn-primary px-4">
              {{ isSaving ? 'Sauvegarde...' : 'Enregistrer' }}
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { Search, Edit3, X, PackageSearch } from '@lucide/vue'
import { useAdminProduitStore } from '@/stores/adminProduitStore'
import { adminProduitService } from '@/services/adminProduitService'
import { useToast } from '@/composables/useToast'
import Pagination from '@/components/common/Pagination.vue'

const produitStore = useAdminProduitStore()
const toast = useToast()

const filters = reactive({
  nom: ''
})

const isModalOpen = ref(false)
const selectedProduit = ref(null)
const newQuantite = ref(0)
const isSaving = ref(false)

const fetchProduits = (page = 1) => {
  const params = { page, per_page: 15 }
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

function openStockModal(produit) {
  selectedProduit.value = produit
  newQuantite.value = produit.quantite_stock
  isModalOpen.value = true
}

function closeModal() {
  isModalOpen.value = false
  selectedProduit.value = null
}

async function updateStock() {
  isSaving.value = true
  try {
    const data = await adminProduitService.updateStock(selectedProduit.value.id, newQuantite.value)
    
    // Update local state directly to avoid refetching
    const index = produitStore.produits.findIndex(p => p.id === selectedProduit.value.id)
    if (index !== -1) {
      produitStore.produits[index].quantite_stock = newQuantite.value
    }
    
    toast.success('Stock mis à jour avec succès.')
    closeModal()
  } catch (err) {
    toast.error('Erreur lors de la mise à jour du stock.')
  } finally {
    isSaving.value = false
  }
}

onMounted(() => {
  fetchProduits()
})
</script>
