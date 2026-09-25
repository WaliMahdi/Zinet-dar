<template>
  <div class="space-y-6">
    <!-- Loading State -->
    <div v-if="dashboardStore.loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="i in 4" :key="i" class="h-32 skeleton rounded-lg"></div>
    </div>

    <template v-else-if="stats">
      <!-- Stats Overview -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Chiffre d'affaires -->
        <div class="bg-surface border border-border rounded-lg p-5 flex flex-col justify-between hover:shadow-sm transition-shadow">
          <div class="flex items-center justify-between">
            <span class="text-muted text-sm font-medium uppercase tracking-wider">Chiffre d'affaires (Mois)</span>
            <div class="w-10 h-10 rounded-full bg-secondary/10 flex items-center justify-center">
              <Banknote class="text-secondary" :size="20" />
            </div>
          </div>
          <div class="mt-4">
            <span class="font-display text-3xl font-semibold text-primary">{{ formatPrice(stats.chiffre_affaires.mois) }}</span>
            <p class="text-xs text-muted mt-1">Total généré : {{ formatPrice(stats.chiffre_affaires.total) }}</p>
          </div>
        </div>

        <!-- Commandes -->
        <div class="bg-surface border border-border rounded-lg p-5 flex flex-col justify-between hover:shadow-sm transition-shadow">
          <div class="flex items-center justify-between">
            <span class="text-muted text-sm font-medium uppercase tracking-wider">Commandes du jour</span>
            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
              <ShoppingBag class="text-blue-600" :size="20" />
            </div>
          </div>
          <div class="mt-4">
            <span class="font-display text-3xl font-semibold text-primary">{{ stats.commandes.today_count }}</span>
            <p class="text-xs text-muted mt-1">{{ stats.commandes.total }} au total</p>
          </div>
        </div>

        <!-- Clients -->
        <div class="bg-surface border border-border rounded-lg p-5 flex flex-col justify-between hover:shadow-sm transition-shadow">
          <div class="flex items-center justify-between">
            <span class="text-muted text-sm font-medium uppercase tracking-wider">Clients Inscrits</span>
            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
              <Users class="text-green-600" :size="20" />
            </div>
          </div>
          <div class="mt-4">
            <span class="font-display text-3xl font-semibold text-primary">{{ stats.clients.total }}</span>
            <p class="text-xs text-muted mt-1">Clients actifs</p>
          </div>
        </div>

        <!-- Stock Alert -->
        <div class="bg-surface border border-border rounded-lg p-5 flex flex-col justify-between hover:shadow-sm transition-shadow">
          <div class="flex items-center justify-between">
            <span class="text-muted text-sm font-medium uppercase tracking-wider">Stock faible / Rupture</span>
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
              <AlertTriangle class="text-red-600" :size="20" />
            </div>
          </div>
          <div class="mt-4">
            <span class="font-display text-3xl font-semibold text-primary">{{ stats.produits.stock_bas + stats.produits.rupture }}</span>
            <p class="text-xs text-muted mt-1">{{ stats.produits.rupture }} en rupture totale</p>
          </div>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Commandes Status (Simple CSS bars) -->
        <div class="lg:col-span-1 bg-surface border border-border rounded-lg p-6">
          <h3 class="font-display text-lg font-medium text-primary mb-5">Statut des commandes</h3>
          
          <div class="space-y-4">
            <div class="flex items-center justify-between text-sm mb-1">
              <span class="text-muted">En attente</span>
              <span class="font-medium text-primary">{{ stats.commandes.en_attente }}</span>
            </div>
            <div class="w-full bg-background rounded-full h-2">
              <div class="bg-yellow-500 h-2 rounded-full" :style="{ width: getPercentage(stats.commandes.en_attente) + '%' }"></div>
            </div>

            <div class="flex items-center justify-between text-sm mb-1 mt-3">
              <span class="text-muted">Livrée</span>
              <span class="font-medium text-primary">{{ stats.commandes.livree }}</span>
            </div>
            <div class="w-full bg-background rounded-full h-2">
              <div class="bg-green-500 h-2 rounded-full" :style="{ width: getPercentage(stats.commandes.livree) + '%' }"></div>
            </div>
          </div>
        </div>

        <!-- Dernières Commandes -->
        <div class="lg:col-span-2 bg-surface border border-border rounded-lg p-6">
          <div class="flex items-center justify-between mb-5">
            <h3 class="font-display text-lg font-medium text-primary">Dernières commandes</h3>
            <RouterLink :to="{ name: 'admin-commandes' }" class="text-sm text-secondary hover:text-primary transition-colors">
              Voir tout
            </RouterLink>
          </div>
          
          <div v-if="stats.recent_commandes.length === 0" class="text-center py-8 text-muted text-sm">
            Aucune commande récente.
          </div>
          <div v-else class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="border-b border-border text-sm text-muted">
                  <th class="py-3 font-medium">N°</th>
                  <th class="py-3 font-medium">Client</th>
                  <th class="py-3 font-medium">Date</th>
                  <th class="py-3 font-medium">Montant</th>
                  <th class="py-3 font-medium text-right">Statut</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-border/50 text-sm">
                <tr v-for="cmd in stats.recent_commandes" :key="cmd.id" class="hover:bg-background/50 transition-colors">
                  <td class="py-3 font-medium text-primary">
                    <RouterLink :to="{ name: 'admin-commande-detail', params: { id: cmd.id } }" class="hover:text-secondary">
                      #{{ cmd.id }}
                    </RouterLink>
                  </td>
                  <td class="py-3 text-muted">{{ cmd.nom_client }}</td>
                  <td class="py-3 text-muted">{{ formatDate(cmd.created_at) }}</td>
                  <td class="py-3 font-medium text-primary">{{ formatPrice(cmd.montant_total) }}</td>
                  <td class="py-3 text-right">
                    <OrderStatusBadge :statut="cmd.statut" />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useAdminDashboardStore } from '@/stores/adminDashboardStore'
import { formatPrice } from '@/composables/useFormatPrice'
import { formatDate } from '@/utils/helpers'
import OrderStatusBadge from '@/components/orders/OrderStatusBadge.vue'
import { Banknote, ShoppingBag, Users, AlertTriangle } from '@lucide/vue'

const dashboardStore = useAdminDashboardStore()
const stats = computed(() => dashboardStore.stats)

function getPercentage(value) {
  if (!stats.value || stats.value.commandes.total === 0) return 0
  return (value / stats.value.commandes.total) * 100
}

onMounted(() => {
  dashboardStore.fetchStats()
})
</script>
