<template>
  <div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div class="flex items-center gap-3">
        <RouterLink :to="{ name: 'admin-commandes' }" class="text-muted hover:text-primary transition-colors">
          <ArrowLeft :size="20" />
        </RouterLink>
        <h2 class="font-display text-2xl font-medium text-primary">
          Commande #{{ route.params.id }}
        </h2>
        <OrderStatusBadge v-if="commande" :statut="commande.statut" />
      </div>
      <div class="flex items-center gap-3">
        <button @click="printOrder" class="btn-outline inline-flex items-center gap-2">
          <Printer :size="16" /> Imprimer
        </button>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="commandeStore.loading && !commande" class="space-y-6">
      <div class="h-32 skeleton rounded-lg"></div>
      <div class="h-64 skeleton rounded-lg"></div>
    </div>

    <template v-else-if="commande">
      <div class="grid lg:grid-cols-3 gap-6">
        
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
          
          <!-- Products -->
          <div class="bg-surface border border-border rounded-lg overflow-hidden">
            <div class="px-5 py-4 border-b border-border">
              <h3 class="font-medium text-primary">Articles de la commande</h3>
            </div>
            <div class="divide-y divide-border/50">
              <div v-for="detail in commande.details" :key="detail.id" class="p-5 flex gap-4">
                <div class="w-16 h-16 rounded-md bg-background border border-border/50 flex items-center justify-center overflow-hidden flex-shrink-0">
                  <img 
                    v-if="detail.produit?.image" 
                    :src="detail.produit.image" 
                    :alt="detail.nom_produit"
                    class="w-full h-full object-cover"
                  />
                  <Package v-else :size="24" class="text-muted" />
                </div>
                <div class="flex-1 min-w-0 flex flex-col justify-center">
                  <h4 class="font-medium text-primary text-sm truncate">{{ detail.nom_produit }}</h4>
                  <p class="text-xs text-muted mt-0.5">Réf. {{ detail.reference_produit }}</p>
                </div>
                <div class="text-right flex flex-col justify-center">
                  <p class="text-sm font-medium text-primary">{{ formatPrice(detail.montant) }}</p>
                  <p class="text-xs text-muted">{{ formatPrice(detail.prix_unitaire) }} × {{ detail.quantite }}</p>
                </div>
              </div>
            </div>
            <div class="bg-background p-5 border-t border-border space-y-2">
              <div class="flex justify-between text-sm text-muted">
                <span>Sous-total</span>
                <span>{{ formatPrice(commande.sous_total) }}</span>
              </div>
              <div class="flex justify-between text-sm font-medium text-primary pt-2 border-t border-border/50">
                <span>Total à payer (Espèces)</span>
                <span class="text-lg font-display">{{ formatPrice(commande.montant_total) }}</span>
              </div>
            </div>
          </div>

          <!-- Internal Note -->
          <div class="bg-surface border border-border rounded-lg p-5">
            <h3 class="font-medium text-primary mb-3">Note interne (Admin)</h3>
            <div class="flex gap-3">
              <textarea 
                v-model="internalNote" 
                rows="2" 
                class="input resize-none flex-1 text-sm"
                placeholder="Ajouter une note interne..."
              ></textarea>
              <button 
                @click="saveInternalNote" 
                :disabled="isSavingNote"
                class="btn-primary self-end"
              >
                <Save :size="16" />
              </button>
            </div>
          </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
          
          <!-- Actions Status -->
          <div class="bg-surface border border-border rounded-lg p-5">
            <h3 class="font-medium text-primary mb-4">Changer le statut</h3>
            <div class="space-y-2">
              <button 
                v-if="['en_attente'].includes(commande.statut)"
                @click="updateStatus('confirmee')"
                class="btn-primary w-full justify-center"
              >
                Confirmer la commande
              </button>
              
              <button 
                v-if="['confirmee'].includes(commande.statut)"
                @click="updateStatus('livree')"
                class="bg-green-600 hover:bg-green-700 text-white font-medium rounded-md px-4 py-2 w-full transition-colors flex items-center justify-center gap-2"
              >
                <CheckCircle :size="18" /> Commande Livrée
              </button>
              
              <hr class="border-border my-2" />
              

              
              <button 
                v-if="!['livree', 'annulee'].includes(commande.statut)"
                @click="updateStatus('annulee')"
                class="btn-outline w-full justify-center text-red-600 border-red-200 hover:bg-red-50 hover:border-red-300"
              >
                Annuler
              </button>
            </div>
          </div>

          <!-- Customer Info -->
          <div class="bg-surface border border-border rounded-lg p-5 text-sm">
            <h3 class="font-medium text-primary mb-4 flex items-center gap-2">
              <User :size="16" class="text-muted" /> Client
            </h3>
            <div class="space-y-3">
              <p class="font-medium text-primary">{{ commande.nom_client }}</p>
              
              <div class="flex items-start gap-2 text-muted">
                <Phone :size="14" class="mt-0.5 flex-shrink-0" />
                <a :href="`tel:${commande.telephone}`" class="hover:text-primary transition-colors">
                  {{ commande.telephone }}
                </a>
              </div>
              
              <div class="flex items-start gap-2 text-muted">
                <MapPin :size="14" class="mt-0.5 flex-shrink-0" />
                <span>{{ commande.adresse }}</span>
              </div>
            </div>
          </div>

          <!-- Note Client -->
          <div v-if="commande.note_client" class="bg-surface border border-border rounded-lg p-5 text-sm">
            <h3 class="font-medium text-primary mb-2 flex items-center gap-2">
              <MessageSquare :size="16" class="text-muted" /> Note du client
            </h3>
            <p class="text-muted italic bg-background p-3 rounded-md border border-border/50">
              "{{ commande.note_client }}"
            </p>
          </div>
          
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { ArrowLeft, Printer, Package, User, Phone, MapPin, MessageSquare, Save, CheckCircle } from '@lucide/vue'
import { useAdminCommandeStore } from '@/stores/adminCommandeStore'
import { useToast } from '@/composables/useToast'
import { formatPrice } from '@/composables/useFormatPrice'
import OrderStatusBadge from '@/components/orders/OrderStatusBadge.vue'

const route = useRoute()
const commandeStore = useAdminCommandeStore()
const toast = useToast()

const commande = computed(() => commandeStore.currentCommande)
const internalNote = ref('')
const isSavingNote = ref(false)

async function loadCommande() {
  try {
    const data = await commandeStore.fetchCommande(route.params.id)
    internalNote.value = data?.note_admin || ''
  } catch (err) {
    toast.error('Impossible de charger la commande.')
  }
}

async function updateStatus(newStatus) {
  const confirmationMsg = {
    confirmee: 'Confirmer cette commande ? Le stock sera vérifié.',
    livree: 'Confirmer la livraison de cette commande ? L\'action est finale.',
    annulee: 'Annuler cette commande ? Le stock sera restauré.'
  }

  if (!confirm(confirmationMsg[newStatus] || 'Changer le statut ?')) return

  try {
    await commandeStore.updateStatut(commande.value.id, newStatus)
    toast.success('Statut mis à jour avec succès.')
  } catch (err) {
    toast.error(err.response?.data?.message || 'Erreur lors de la mise à jour.')
  }
}

async function saveInternalNote() {
  isSavingNote.value = true
  try {
    await commandeStore.updateNote(commande.value.id, internalNote.value)
    toast.success('Note interne enregistrée.')
  } catch (err) {
    toast.error('Erreur lors de l\'enregistrement de la note.')
  } finally {
    isSavingNote.value = false
  }
}

function printOrder() {
  window.print()
}

onMounted(loadCommande)
</script>

<style>
/* Print Styles */
@media print {
  body * {
    visibility: hidden;
  }
  .bg-surface, .bg-surface * {
    visibility: visible;
  }
  .bg-surface {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    border: none !important;
    box-shadow: none !important;
  }
  button, a {
    display: none !important;
  }
}
</style>
