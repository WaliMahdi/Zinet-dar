<template>
  <div>
    <div class="text-center mb-6">
      <h2 class="font-display text-2xl font-medium text-primary">Créer un compte</h2>
      <p class="text-muted text-sm mt-1">Rejoignez Zinet Eddar pour commander facilement.</p>
    </div>

    <form @submit.prevent="handleRegister" class="space-y-4" id="register-form">
      <div>
        <label class="label" for="reg-email">Email *</label>
        <input
          id="reg-email"
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
        <label class="label" for="reg-password">Mot de passe *</label>
        <div class="relative">
          <input
            id="reg-password"
            v-model="form.password"
            :type="showPwd ? 'text' : 'password'"
            class="input pr-10"
            placeholder="Min. 8 caractères"
            required
            minlength="8"
            autocomplete="new-password"
          />
          <button type="button" @click="showPwd = !showPwd" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted">
            <Eye v-if="!showPwd" :size="16" />
            <EyeOff v-else :size="16" />
          </button>
        </div>
      </div>

      <div>
        <label class="label" for="reg-confirm">Confirmer le mot de passe *</label>
        <input
          id="reg-confirm"
          v-model="form.confirm"
          :type="showPwd ? 'text' : 'password'"
          class="input"
          :class="{ 'input-error': errors.confirm }"
          placeholder="Répétez votre mot de passe"
          required
        />
        <p v-if="errors.confirm" class="text-red-500 text-xs mt-1">{{ errors.confirm }}</p>
      </div>

      <div v-if="apiError" class="bg-red-50 border border-red-200 rounded-sm p-3 text-sm text-red-700">
        {{ apiError }}
      </div>

      <button
        type="submit"
        :disabled="authStore.loading"
        class="btn-primary w-full"
        id="register-submit-btn"
      >
        <Loader2 v-if="authStore.loading" :size="16" class="animate-spin" />
        <UserPlus v-else :size="16" />
        {{ authStore.loading ? 'Création…' : 'Créer mon compte' }}
      </button>
    </form>

    <p class="mt-6 text-center text-sm text-muted">
      Déjà un compte ?
      <RouterLink :to="{ name: 'login' }" class="text-secondary hover:text-secondary-600 font-medium">Se connecter</RouterLink>
    </p>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter }    from 'vue-router'
import { Eye, EyeOff, UserPlus, Loader2 } from '@lucide/vue'
import { useAuthStore } from '@/stores/authStore'
import { useToast }     from '@/composables/useToast'

const authStore = useAuthStore()
const router    = useRouter()
const toast     = useToast()

const showPwd  = ref(false)
const apiError = ref(null)
const form     = reactive({ email: '', password: '', confirm: '' })
const errors   = reactive({ email: null, confirm: null })

async function handleRegister() {
  errors.email = errors.confirm = null
  apiError.value = null
  if (form.password !== form.confirm) {
    errors.confirm = 'Les mots de passe ne correspondent pas.'
    return
  }
  try {
    await authStore.register(form.email, form.password)
    toast.success('Compte créé ! Un code de vérification a été envoyé à votre email.')
    router.push({ name: 'verify-email', query: { email: form.email } })
  } catch (err) {
    const data = err.response?.data
    if (data?.errors?.email) {
      errors.email = data.errors.email[0]
    } else {
      apiError.value = data?.message || 'Erreur lors de l\'inscription.'
    }
  }
}
</script>
