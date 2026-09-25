<template>
  <div>
    <div class="text-center mb-6">
      <h2 class="font-display text-2xl font-medium text-primary">Bon retour !</h2>
      <p class="text-muted text-sm mt-1">Connectez-vous pour accéder à votre compte.</p>
    </div>

    <form @submit.prevent="handleLogin" class="space-y-4" id="login-form">
      <div>
        <label class="label" for="login-email">Email</label>
        <input
          id="login-email"
          v-model="form.email"
          type="email"
          class="input"
          :class="{ 'input-error': errors.email }"
          placeholder="votre@email.com"
          required
          autocomplete="email"
        />
        <p v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email }}</p>
      </div>

      <div>
        <label class="label" for="login-password">Mot de passe</label>
        <div class="relative">
          <input
            id="login-password"
            v-model="form.password"
            :type="showPwd ? 'text' : 'password'"
            class="input pr-10"
            :class="{ 'input-error': errors.password }"
            placeholder="••••••••"
            required
            autocomplete="current-password"
          />
          <button type="button" @click="showPwd = !showPwd" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted">
            <Eye v-if="!showPwd" :size="16" />
            <EyeOff v-else :size="16" />
          </button>
        </div>
        <p v-if="errors.password" class="text-red-500 text-xs mt-1">{{ errors.password }}</p>
      </div>

      <div v-if="apiError" class="bg-red-50 border border-red-200 rounded-sm p-3 text-sm text-red-700">
        {{ apiError }}
      </div>

      <button
        type="submit"
        :disabled="authStore.loading"
        class="btn-primary w-full"
        id="login-submit-btn"
      >
        <Loader2 v-if="authStore.loading" :size="16" class="animate-spin" />
        <LogIn v-else :size="16" />
        {{ authStore.loading ? 'Connexion…' : 'Se connecter' }}
      </button>
    </form>

    <div class="mt-6 text-center space-y-2">
      <p class="text-sm text-muted">
        Pas encore de compte ?
        <RouterLink :to="{ name: 'register' }" class="text-secondary hover:text-secondary-600 font-medium">Créer un compte</RouterLink>
      </p>
      <p class="text-xs text-muted">
        Email non vérifié ?
        <RouterLink :to="{ name: 'verify-email' }" class="text-secondary hover:text-secondary-600">Vérifier mon email</RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { Eye, EyeOff, LogIn, Loader2 } from '@lucide/vue'
import { useAuthStore } from '@/stores/authStore'
import { useCartStore } from '@/stores/cartStore'
import { useToast }     from '@/composables/useToast'

const authStore = useAuthStore()
const cartStore = useCartStore()
const router    = useRouter()
const route     = useRoute()
const toast     = useToast()

const showPwd  = ref(false)
const apiError = ref(null)

const form = reactive({ email: '', password: '' })
const errors = reactive({ email: null, password: null })

async function handleLogin() {
  errors.email = errors.password = null
  apiError.value = null
  try {
    await authStore.login(form.email, form.password)
    toast.success('Connexion réussie. Bienvenue !')
    await cartStore.fetchCart()
    
    // Redirect logic
    if (authStore.isAdmin) {
      router.push({ name: 'admin-dashboard' })
    } else {
      const redirect = route.query.redirect || '/'
      router.push(redirect)
    }
  } catch (err) {
    const msg = err.response?.data?.message || 'Identifiants incorrects.'
    // Handle unverified email — redirect to verify
    if (err.response?.status === 403) {
      apiError.value = msg
      setTimeout(() => router.push({ name: 'verify-email', query: { email: form.email } }), 2000)
    } else {
      apiError.value = msg
    }
  }
}
</script>
