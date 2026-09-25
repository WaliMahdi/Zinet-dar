<template>
  <div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <h2 class="font-display text-2xl font-medium text-primary">Clients</h2>
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
      <div class="flex-1 min-w-[200px]">
        <div class="relative">
          <Mail class="absolute left-3 top-1/2 -translate-y-1/2 text-muted" :size="16" />
          <input 
            type="email" 
            v-model="filters.email" 
            @input="handleSearch"
            placeholder="Rechercher par email..." 
            class="input pl-9 w-full"
          />
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="clientStore.loading && !clientStore.clients.length" class="space-y-4">
      <div v-for="i in 5" :key="i" class="h-16 skeleton rounded-lg"></div>
    </div>

    <!-- Table -->
    <div v-else class="bg-surface border border-border rounded-lg overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
          <thead>
            <tr class="bg-background/50 border-b border-border text-xs uppercase tracking-wider text-muted font-medium">
              <th class="px-6 py-4">Client</th>
              <th class="px-6 py-4">Contact</th>
              <th class="px-6 py-4 text-center">Commandes</th>
              <th class="px-6 py-4 text-right">Total Dépensé</th>
              <th class="px-6 py-4">Inscription</th>
              <th class="px-6 py-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border/50 text-sm">
            <tr v-if="clientStore.clients.length === 0">
              <td colspan="6" class="px-6 py-10 text-center text-muted">
                Aucun client trouvé.
              </td>
            </tr>
            <tr v-else v-for="client in clientStore.clients" :key="client.id" class="hover:bg-background/50 transition-colors">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full bg-secondary/10 flex items-center justify-center text-secondary font-medium uppercase">
                    {{ client.first_name ? client.first_name.charAt(0) : (client.name ? client.name.charAt(0) : client.email.charAt(0)) }}
                  </div>
                  <div class="font-medium text-primary">
                    {{ client.first_name || client.last_name ? `${client.first_name || ''} ${client.last_name || ''}`.trim() : (client.name || client.email) }}
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm text-muted">{{ client.email }}</div>
              </td>
              <td class="px-6 py-4 text-center font-medium text-primary">
                {{ client.commandes_count || 0 }}
              </td>
              <td class="px-6 py-4 text-right font-medium text-primary">
                {{ formatPrice(client.commandes_sum_montant_total || 0) }}
              </td>
              <td class="px-6 py-4 text-muted">
                {{ formatDate(client.created_at) }}
              </td>
              <td class="px-6 py-4 text-right space-x-2">
                <RouterLink 
                  :to="{ name: 'admin-client-detail', params: { id: client.id } }"
                  class="btn-outline px-3 py-1.5 text-xs inline-flex items-center gap-1.5"
                >
                  <Eye :size="14" /> Profil
                </RouterLink>
                <button 
                  @click="deleteClient(client.id)"
                  class="btn-outline px-3 py-1.5 text-xs inline-flex items-center gap-1.5 text-red-600 border-red-200 hover:bg-red-50 hover:border-red-300"
                >
                  <Trash2 :size="14" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="p-4 border-t border-border flex justify-center" v-if="clientStore.pagination?.last_page > 1">
        <Pagination 
          :current-page="clientStore.pagination.current_page"
          :last-page="clientStore.pagination.last_page"
          @change="fetchClients"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, onMounted } from 'vue'
import { Search, Mail, Eye, Trash2 } from '@lucide/vue'
import { useAdminClientStore } from '@/stores/adminClientStore'
import { useToast } from '@/composables/useToast'
import { formatPrice } from '@/composables/useFormatPrice'
import { formatDate } from '@/utils/helpers'
import Pagination from '@/components/common/Pagination.vue'

const clientStore = useAdminClientStore()
const toast = useToast()

const filters = reactive({
  nom: '',
  email: ''
})

const fetchClients = (page = 1) => {
  const params = { page }
  if (filters.nom) params.nom = filters.nom
  if (filters.email) params.email = filters.email
  clientStore.fetchClients(params)
}

let timeout = null
const handleSearch = () => {
  clearTimeout(timeout)
  timeout = setTimeout(() => {
    fetchClients(1)
  }, 500)
}

async function deleteClient(id) {
  if (!confirm('Voulez-vous vraiment supprimer ce client ? Cette action est irréversible et supprimera également toutes ses commandes.')) return
  try {
    await clientStore.deleteClient(id)
    toast.success('Client supprimé.')
  } catch (err) {
    toast.error('Erreur lors de la suppression.')
  }
}

onMounted(() => {
  fetchClients()
})
</script>
