<template>
  <div class="min-h-screen bg-background">
    <!-- Breadcrumb -->
    <div class="bg-surface py-4 border-b border-border">
      <div class="container-site">
        <Breadcrumb />
      </div>
    </div>
    <div class="bg-surface border-b border-border py-10">
      <div class="container-site">
        <h1 class="section-title">Passer la commande</h1>
      </div>
    </div>

    <div class="container-site py-10">
      <!-- Cart empty redirect hint -->
      <div v-if="cartStore.isEmpty && !cartStore.loading" class="text-center py-16">
        <p class="text-muted mb-4">Votre panier est vide.</p>
        <RouterLink :to="{ name: 'shop' }" class="btn-primary">Retour à la boutique</RouterLink>
      </div>

      <div v-else class="grid lg:grid-cols-3 gap-10 items-start">
        <!-- Checkout form -->
        <div class="lg:col-span-2 space-y-6">
          <div class="bg-surface rounded-lg border border-border/50 p-6">
            <h2 class="font-display text-xl font-medium text-primary mb-6">Informations de livraison</h2>

            <form @submit.prevent="handleSubmit" class="space-y-5" id="checkout-form">
              <!-- Nom complet -->
              <div>
                <label class="label" for="checkout-nom">Nom complet *</label>
                <input
                  id="checkout-nom"
                  v-model="form.nom_client"
                  type="text"
                  class="input"
                  :class="{ 'input-error': errors.nom_client }"
                  placeholder="Votre nom et prénom"
                  required
                />
                <p v-if="errors.nom_client" class="text-red-500 text-xs mt-1">{{ errors.nom_client }}</p>
              </div>

              <!-- Téléphone -->
              <div>
                <label class="label" for="checkout-tel">Téléphone *</label>
                <input
                  id="checkout-tel"
                  v-model="form.telephone"
                  type="tel"
                  class="input"
                  :class="{ 'input-error': errors.telephone }"
                  placeholder="ex. 22 123 456"
                  required
                />
                <p v-if="errors.telephone" class="text-red-500 text-xs mt-1">{{ errors.telephone }}</p>
              </div>

              <!-- Adresse -->
              <div>
                <label class="label" for="checkout-adresse">Adresse de livraison *</label>
                <textarea
                  id="checkout-adresse"
                  v-model="form.adresse"
                  rows="3"
                  class="input resize-none"
                  :class="{ 'input-error': errors.adresse }"
                  placeholder="Rue, numéro, ville, gouvernorat…"
                  required
                />
                <p v-if="errors.adresse" class="text-red-500 text-xs mt-1">{{ errors.adresse }}</p>
              </div>

              <!-- Note -->
              <div>
                <label class="label" for="checkout-note">Note (optionnelle)</label>
                <textarea
                  id="checkout-note"
                  v-model="form.note_client"
                  rows="2"
                  class="input resize-none"
                  placeholder="Instructions de livraison, précisions…"
                />
              </div>

              <!-- Payment method (display only) -->
              <div class="bg-background rounded-lg p-4 flex items-center gap-3">
                <Banknote :size="22" class="text-secondary flex-shrink-0" />
                <div>
                  <p class="text-sm font-medium text-primary">Paiement en espèces à la livraison</p>
                  <p class="text-xs text-muted">Préparez le montant exact lors de la réception.</p>
                </div>
              </div>

              <!-- Error -->
              <div v-if="apiError" class="bg-red-50 border border-red-200 rounded-sm p-3 text-sm text-red-700">
                {{ apiError }}
              </div>

              <!-- Submit -->
              <button
                type="submit"
                :disabled="cartStore.loading || isSubmitting"
                class="btn-primary w-full btn-lg"
                id="checkout-submit-btn"
              >
                <Loader2 v-if="isSubmitting" :size="18" class="animate-spin" />
                <CheckCircle v-else :size="18" />
                {{ isSubmitting ? 'Traitement…' : `Confirmer la commande — ${formatPrice(cartStore.totalAmount)}` }}
              </button>
            </form>
          </div>
        </div>

        <!-- Order summary -->
        <CartSummary
          :items="cartStore.items"
          :sous-total="cartStore.sousTotal"
          :montant-total="cartStore.totalAmount"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { Banknote, Loader2, CheckCircle } from '@lucide/vue'
import { useCartStore }  from '@/stores/cartStore'
import { useAuthStore }  from '@/stores/authStore'
import { useToast }      from '@/composables/useToast'
import { formatPrice }   from '@/composables/useFormatPrice'
import CartSummary from '@/components/cart/CartSummary.vue'
import Breadcrumb  from '@/components/common/Breadcrumb.vue'

const cartStore  = useCartStore()
const authStore  = useAuthStore()
const router     = useRouter()
const toast      = useToast()

const isSubmitting = ref(false)
const apiError     = ref(null)

const form = reactive({
  nom_client:  `${authStore.user?.first_name || ''} ${authStore.user?.last_name || ''}`.trim(),
  telephone:   '',
  adresse:     '',
  note_client: '',
})

const errors = reactive({ nom_client: null, telephone: null, adresse: null })

function validate() {
  errors.nom_client = form.nom_client.trim() ? null : 'Le nom est requis.'
  errors.telephone  = form.telephone.trim()  ? null : 'Le téléphone est requis.'
  errors.adresse    = form.adresse.trim()    ? null : 'L\'adresse est requise.'
  return !errors.nom_client && !errors.telephone && !errors.adresse
}

async function handleSubmit() {
  if (!validate()) return
  isSubmitting.value = true
  apiError.value     = null
  try {
    const result = await cartStore.checkout({
      nom_client:  form.nom_client.trim(),
      telephone:   form.telephone.trim(),
      adresse:     form.adresse.trim(),
      note_client: form.note_client.trim() || undefined,
    })
    if (result?.success) {
      toast.success('Commande passée avec succès !')
      router.push({ name: 'checkout-success', query: { id: result.data?.id } })
    } else {
      apiError.value = result?.message || 'Erreur lors de la commande.'
    }
  } catch (err) {
    apiError.value = err.response?.data?.message || 'Erreur lors de la commande. Veuillez réessayer.'
  } finally {
    isSubmitting.value = false
  }
}

onMounted(() => { if (!cartStore.isEmpty) cartStore.fetchCart() })
</script>
