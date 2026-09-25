<template>
  <section class="py-16 md:py-24 bg-secondary/10">
    <div class="container-site">
      <div class="max-w-lg mx-auto text-center">
        <p class="section-eyebrow">Newsletter</p>
        <h2 class="section-title mb-4">Recevez nos inspirations</h2>
        <p class="text-muted text-sm mb-8">
          Inscrivez-vous pour découvrir nos nouveautés et nos offres exclusives en avant-première.
        </p>

        <form @submit.prevent="handleSubmit" class="flex gap-2 flex-wrap sm:flex-nowrap">
          <input
            v-model="email"
            type="email"
            required
            placeholder="Votre adresse email"
            class="input flex-1 min-w-0"
            :disabled="submitted"
            id="newsletter-email"
          />
          <button
            type="submit"
            class="btn-primary flex-shrink-0"
            :disabled="submitted"
            id="newsletter-submit"
          >
            S'inscrire
          </button>
        </form>

        <Transition name="fade">
          <p v-if="submitted" class="mt-4 text-secondary text-sm flex items-center justify-center gap-2">
            <CheckCircle :size="16" /> Merci ! Vous serez parmi les premiers à découvrir nos nouveautés.
          </p>
        </Transition>

        <p class="text-muted text-xs mt-4">
          Pas de spam. Désabonnement en un clic.
        </p>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'
import { CheckCircle } from '@lucide/vue'

const email     = ref('')
const submitted = ref(false)

function handleSubmit() {
  // TODO: Connect to newsletter API when available
  submitted.value = true
  email.value = ''
}
</script>
