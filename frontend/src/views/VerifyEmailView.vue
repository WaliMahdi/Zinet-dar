<template>
  <div>
    <div class="text-center mb-6">
      <div class="w-14 h-14 rounded-full bg-secondary/10 flex items-center justify-center mx-auto mb-4">
        <Mail :size="24" class="text-secondary" />
      </div>
      <h2 class="font-display text-2xl font-medium text-primary">Vérifiez votre email</h2>
      <p class="text-muted text-sm mt-2">
        Un code à 6 chiffres a été envoyé à<br/>
        <span class="font-medium text-primary">{{ email }}</span>
      </p>
    </div>

    <form @submit.prevent="handleVerify" class="space-y-4" id="verify-email-form">
      <div>
        <label class="label" for="verify-email-input">Email</label>
        <input
          id="verify-email-input"
          v-model="email"
          type="email"
          class="input"
          placeholder="votre@email.com"
          required
        />
      </div>

      <div>
        <label class="label" for="verify-code">Code de vérification</label>
        <input
          id="verify-code"
          v-model="code"
          type="text"
          inputmode="numeric"
          maxlength="6"
          class="input text-center text-xl tracking-[0.5em] font-mono"
          placeholder="000000"
          required
        />
      </div>

      <div v-if="apiError" class="bg-red-50 border border-red-200 rounded-sm p-3 text-sm text-red-700">
        {{ apiError }}
      </div>
      <div v-if="successMsg" class="bg-green-50 border border-green-200 rounded-sm p-3 text-sm text-green-700">
        {{ successMsg }}
      </div>

      <button type="submit" :disabled="authStore.loading" class="btn-primary w-full" id="verify-submit-btn">
        <Loader2 v-if="authStore.loading" :size="16" class="animate-spin" />
        <CheckCircle v-else :size="16" />
        {{ authStore.loading ? 'Vérification…' : 'Vérifier mon email' }}
      </button>
    </form>

    <div class="mt-5 text-center">
      <button
        @click="handleResend"
        :disabled="resendCooldown > 0 || authStore.loading"
        class="text-sm text-secondary hover:text-secondary-600 disabled:opacity-50"
        id="resend-code-btn"
      >
        {{ resendCooldown > 0 ? `Renvoyer dans ${resendCooldown}s` : 'Renvoyer le code' }}
      </button>
    </div>

    <p class="mt-4 text-center text-sm text-muted">
      <RouterLink :to="{ name: 'login' }" class="text-secondary hover:text-secondary-600">Retour à la connexion</RouterLink>
    </p>
  </div>
</template>

<script setup>
import { ref, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { Mail, CheckCircle, Loader2 } from '@lucide/vue'
import { useAuthStore } from '@/stores/authStore'
import { useToast }     from '@/composables/useToast'

const authStore = useAuthStore()
const router    = useRouter()
const route     = useRoute()
const toast     = useToast()

const email       = ref(route.query.email || '')
const code        = ref('')
const apiError    = ref(null)
const successMsg  = ref(null)
const resendCooldown = ref(0)

let cooldownTimer

async function handleVerify() {
  apiError.value   = null
  successMsg.value = null
  try {
    await authStore.verifyEmail(email.value, code.value)
    toast.success('Email vérifié ! Vous pouvez maintenant vous connecter.')
    router.push({ name: 'login', query: { email: email.value } })
  } catch (err) {
    apiError.value = err.response?.data?.message || 'Code invalide ou expiré.'
  }
}

async function handleResend() {
  apiError.value   = null
  successMsg.value = null
  try {
    await authStore.resendVerification(email.value)
    successMsg.value = 'Un nouveau code a été envoyé à votre email.'
    startCooldown()
  } catch (err) {
    apiError.value = err.response?.data?.message || 'Impossible de renvoyer le code.'
  }
}

function startCooldown() {
  resendCooldown.value = 60
  cooldownTimer = setInterval(() => {
    resendCooldown.value--
    if (resendCooldown.value <= 0) clearInterval(cooldownTimer)
  }, 1000)
}

onUnmounted(() => clearInterval(cooldownTimer))
</script>
