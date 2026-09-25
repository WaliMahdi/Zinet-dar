<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <h2 class="font-display text-2xl font-medium text-primary">Commandes</h2>
    </div>

    <!-- Filters -->
    <div class="bg-surface border border-border rounded-lg p-4 flex flex-wrap gap-4 items-center">
      <div class="flex-1 min-w-[200px]">
        <div class="relative">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 text-muted" :size="16" />
          <input 
            type="text" 
            v-model="filters.nom_client" 
            @input="handleSearch"
            placeholder="Rechercher un client..." 
            class="input pl-9 w-full"
          />
        </div>
      </div>
      <div class="w-full sm:w-auto">
        <select v-model="filters.statut" @change="fetchCommandes(1)" class="input min-w-[150px]">
          <option value="">Tous les statuts</option>
          <option value="en_attente">En attente</option>
          <option value="confirmee">Confirmée</option>
          <option value="livree">Livrée</option>
          <option value="annulee">Annulée</option>
        </select>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="commandeStore.loading && !commandeStore.commandes.length" class="space-y-4">
      <div v-for="i in 5" :key="i" class="h-16 skeleton rounded-lg"></div>
    </div>

    <!-- Table -->
    <div v-else class="bg-surface border border-border rounded-lg overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
          <thead>
            <tr class="bg-background/50 border-b border-border text-xs uppercase tracking-wider text-muted font-medium">
              <th class="px-6 py-4">N°</th>
              <th class="px-6 py-4">Date</th>
              <th class="px-6 py-4">Client</th>
              <th class="px-6 py-4">Statut</th>
              <th class="px-6 py-4 text-right">Total</th>
              <th class="px-6 py-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border/50 text-sm">
            <tr v-if="commandeStore.commandes.length === 0">
              <td colspan="6" class="px-6 py-10 text-center text-muted">
                Aucune commande trouvée.
              </td>
            </tr>
            <tr v-else v-for="cmd in commandeStore.commandes" :key="cmd.id" class="hover:bg-background/50 transition-colors">
              <td class="px-6 py-4 font-medium text-primary">#{{ cmd.id }}</td>
              <td class="px-6 py-4 text-muted">{{ formatDate(cmd.created_at) }}</td>
              <td class="px-6 py-4">
                <div class="font-medium text-primary">{{ cmd.nom_client }}</div>
                <div class="text-xs text-muted">{{ cmd.telephone }}</div>
              </td>
              <td class="px-6 py-4">
                <OrderStatusBadge :statut="cmd.statut" />
              </td>
              <td class="px-6 py-4 text-right font-medium text-primary">
                {{ formatPrice(cmd.montant_total) }}
              </td>
              <td class="px-6 py-4 text-right space-x-2">
                <RouterLink 
                  :to="{ name: 'admin-commande-detail', params: { id: cmd.id } }"
                  class="btn-outline px-3 py-1.5 text-xs inline-flex items-center gap-1.5"
                >
                  <Eye :size="14" /> Voir
                </RouterLink>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="p-4 border-t border-border flex justify-center" v-if="commandeStore.pagination?.last_page > 1">
        <Pagination 
          :current-page="commandeStore.pagination.current_page"
          :last-page="commandeStore.pagination.last_page"
          @change="fetchCommandes"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { Search, Eye } from '@lucide/vue'
import { useAdminCommandeStore } from '@/stores/adminCommandeStore'
import { formatPrice } from '@/composables/useFormatPrice'
import { formatDate } from '@/utils/helpers'
import OrderStatusBadge from '@/components/orders/OrderStatusBadge.vue'
import Pagination from '@/components/common/Pagination.vue'

const commandeStore = useAdminCommandeStore()

const filters = reactive({
  nom_client: '',
  statut: ''
})

const fetchCommandes = (page = 1) => {
  const params = { page }
  if (filters.nom_client) params.nom_client = filters.nom_client
  if (filters.statut) params.statut = filters.statut
  commandeStore.fetchCommandes(params)
}

let timeout = null
const handleSearch = () => {
  clearTimeout(timeout)
  timeout = setTimeout(() => {
    fetchCommandes(1)
  }, 500)
}

onMounted(() => {
  fetchCommandes()
})
</script>
