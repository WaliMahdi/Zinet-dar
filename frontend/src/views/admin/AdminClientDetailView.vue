<template>
  <div class="space-y-6">
    <div class="flex items-center gap-3 mb-6">
      <RouterLink :to="{ name: 'admin-clients' }" class="text-muted hover:text-primary transition-colors">
        <ArrowLeft :size="20" />
      </RouterLink>
      <h2 class="font-display text-2xl font-medium text-primary">Détail Client</h2>
    </div>

    <!-- Loading -->
    <div v-if="clientStore.loading && !client" class="space-y-6">
      <div class="h-32 skeleton rounded-lg"></div>
      <div class="h-64 skeleton rounded-lg"></div>
    </div>

    <template v-else-if="client">
      <div class="grid lg:grid-cols-3 gap-6">
        
        <!-- Sidebar Info -->
        <div class="space-y-6">
          <div class="bg-surface border border-border rounded-lg p-6 text-center">
            <div class="w-20 h-20 mx-auto rounded-full bg-secondary/10 flex items-center justify-center text-secondary font-display text-3xl mb-4 uppercase">
              {{ client.first_name ? client.first_name.charAt(0) : (client.name ? client.name.charAt(0) : client.email.charAt(0)) }}
            </div>
            <h3 class="font-display text-xl font-medium text-primary mb-1">
              {{ client.first_name || client.last_name ? `${client.first_name || ''} ${client.last_name || ''}`.trim() : (client.name || client.email) }}
            </h3>
            <p class="text-sm text-muted mb-4">Inscrit le {{ formatDate(client.created_at) }}</p>
            
            <div class="flex justify-center gap-2">
              <a :href="`mailto:${client.email}`" class="btn-outline px-3 py-1.5 text-xs inline-flex items-center gap-1.5">
                <Mail :size="14" /> Email
              </a>
              <button @click="deleteClient" class="btn-outline px-3 py-1.5 text-xs inline-flex items-center gap-1.5 text-red-600 border-red-200 hover:bg-red-50">
                <Trash2 :size="14" /> Supprimer
              </button>
            </div>
          </div>

          <div class="bg-surface border border-border rounded-lg p-5 space-y-4">
            <h4 class="font-medium text-primary border-b border-border pb-2">Informations</h4>
            
            <div>
              <p class="text-xs text-muted mb-1">Email</p>
              <p class="text-sm font-medium text-primary">{{ client.email }}</p>
            </div>
            
            <div>
              <p class="text-xs text-muted mb-1">Téléphone</p>
              <p class="text-sm font-medium text-primary">{{ client.phone || 'Non renseigné' }}</p>
            </div>

            <div>
              <p class="text-xs text-muted mb-1">Total dépensé</p>
              <p class="text-lg font-display text-primary">{{ formatPrice(client.commandes_sum_montant_total || 0) }}</p>
            </div>
          </div>
        </div>

        <!-- Commandes -->
        <div class="lg:col-span-2">
          <div class="bg-surface border border-border rounded-lg overflow-hidden">
            <div class="px-5 py-4 border-b border-border bg-background/50 flex justify-between items-center">
              <h3 class="font-medium text-primary">Historique des commandes ({{ client.commandes?.length || 0 }})</h3>
            </div>
            
            <div class="overflow-x-auto">
              <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                  <tr class="bg-background border-b border-border text-xs uppercase tracking-wider text-muted font-medium">
                    <th class="px-5 py-3">N°</th>
                    <th class="px-5 py-3">Date</th>
                    <th class="px-5 py-3 text-right">Montant</th>
                    <th class="px-5 py-3 text-right">Statut</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-border/50 text-sm">
                  <tr v-if="!client.commandes || client.commandes.length === 0">
                    <td colspan="4" class="px-5 py-8 text-center text-muted">
                      Ce client n'a passé aucune commande.
                    </td>
                  </tr>
                  <tr v-else v-for="cmd in client.commandes" :key="cmd.id" class="hover:bg-background/50 transition-colors cursor-pointer" @click="router.push({ name: 'admin-commande-detail', params: { id: cmd.id } })">
                    <td class="px-5 py-3 font-medium text-secondary">#{{ cmd.id }}</td>
                    <td class="px-5 py-3 text-muted">{{ formatDate(cmd.created_at) }}</td>
                    <td class="px-5 py-3 text-right font-medium text-primary">{{ formatPrice(cmd.montant_total) }}</td>
                    <td class="px-5 py-3 text-right">
                      <OrderStatusBadge :statut="cmd.statut" />
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { ArrowLeft, Mail, Trash2 } from '@lucide/vue'
import { useAdminClientStore } from '@/stores/adminClientStore'
import { useToast } from '@/composables/useToast'
import { formatPrice } from '@/composables/useFormatPrice'
import { formatDate } from '@/utils/helpers'
import OrderStatusBadge from '@/components/orders/OrderStatusBadge.vue'

const route = useRoute()
const router = useRouter()
const clientStore = useAdminClientStore()
const toast = useToast()

const client = computed(() => clientStore.currentClient)

async function loadClient() {
  try {
    await clientStore.fetchClient(route.params.id)
  } catch (err) {
    toast.error('Client introuvable.')
    router.push({ name: 'admin-clients' })
  }
}

async function deleteClient() {
  if (!confirm('Voulez-vous vraiment supprimer ce client ? Cette action est irréversible et supprimera également toutes ses commandes.')) return
  try {
    await clientStore.deleteClient(client.value.id)
    toast.success('Client supprimé.')
    router.push({ name: 'admin-clients' })
  } catch (err) {
    toast.error('Erreur lors de la suppression.')
  }
}

onMounted(loadClient)
</script>
