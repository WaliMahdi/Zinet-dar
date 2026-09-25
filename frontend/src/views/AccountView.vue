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
        <h1 class="section-title">Mon compte</h1>
      </div>
    </div>

    <div class="container-site py-10">
      <div class="grid lg:grid-cols-3 gap-8 items-start">
        <!-- Profile -->
        <div class="lg:col-span-1">
          <div class="bg-surface rounded-lg border border-border/50 p-6">
            <!-- Avatar -->
            <div class="flex flex-col items-center text-center mb-6">
              <div class="w-16 h-16 rounded-full bg-secondary/20 flex items-center justify-center mb-3">
                <span class="font-display text-2xl font-medium text-secondary">
                  {{ initials }}
                </span>
              </div>
              <h2 class="font-display text-xl font-medium text-primary">{{ fullName }}</h2>
              <p class="text-muted text-sm">{{ user?.email }}</p>
            </div>

            <!-- Nav -->
            <nav class="space-y-1">
              <RouterLink :to="{ name: 'orders' }" class="flex items-center gap-3 px-3 py-2.5 rounded-sm text-sm text-muted hover:text-primary hover:bg-background transition-colors">
                <Package :size="16" /> Mes commandes
              </RouterLink>
              <button @click="handleLogout" class="flex items-center gap-3 px-3 py-2.5 rounded-sm text-sm text-red-500 hover:bg-red-50 w-full text-left transition-colors">
                <LogOut :size="16" /> Se déconnecter
              </button>
            </nav>
          </div>
        </div>

        <!-- Profile form -->
        <div class="lg:col-span-2">
          <div class="bg-surface rounded-lg border border-border/50 p-6 mb-6">
            <h3 class="font-display text-lg font-medium text-primary mb-5">Informations personnelles</h3>
            <form @submit.prevent="handleUpdateProfile" class="space-y-4" id="account-form">
              <div class="grid sm:grid-cols-2 gap-4">
                <div>
                  <label class="label" for="acc-first-name">Prénom</label>
                  <input id="acc-first-name" v-model="profileForm.first_name" type="text" class="input" placeholder="Prénom" />
                </div>
                <div>
                  <label class="label" for="acc-last-name">Nom</label>
                  <input id="acc-last-name" v-model="profileForm.last_name" type="text" class="input" placeholder="Nom" />
                </div>
              </div>
              <div>
                <label class="label" for="acc-username">Nom d'utilisateur</label>
                <input id="acc-username" v-model="profileForm.username" type="text" class="input" placeholder="username" />
              </div>
              <div v-if="profileError" class="bg-red-50 border border-red-200 rounded-sm p-3 text-sm text-red-700">{{ profileError }}</div>
              <button type="submit" :disabled="authStore.loading" class="btn-primary btn-sm" id="update-profile-btn">
                <Loader2 v-if="authStore.loading" :size="14" class="animate-spin" />
                Enregistrer
              </button>
            </form>
          </div>

          <!-- Password change -->
          <div class="bg-surface rounded-lg border border-border/50 p-6">
            <h3 class="font-display text-lg font-medium text-primary mb-5">Changer le mot de passe</h3>
            <form @submit.prevent="handleChangePassword" class="space-y-4" id="password-form">
              <div>
                <label class="label" for="acc-cur-pwd">Mot de passe actuel</label>
                <input id="acc-cur-pwd" v-model="pwdForm.current_password" type="password" class="input" required />
              </div>
              <div>
                <label class="label" for="acc-new-pwd">Nouveau mot de passe</label>
                <input id="acc-new-pwd" v-model="pwdForm.new_password" type="password" class="input" required minlength="8" />
              </div>
              <div>
                <label class="label" for="acc-confirm-pwd">Confirmer</label>
                <input id="acc-confirm-pwd" v-model="pwdForm.new_password_confirmation" type="password" class="input" required />
              </div>
              <div v-if="pwdError" class="bg-red-50 border border-red-200 rounded-sm p-3 text-sm text-red-700">{{ pwdError }}</div>
              <button type="submit" :disabled="authStore.loading" class="btn-outline btn-sm" id="change-password-btn">
                Changer le mot de passe
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { Package, LogOut, Loader2 } from '@lucide/vue'
import { useAuthStore } from '@/stores/authStore'
import { useToast }     from '@/composables/useToast'
import Breadcrumb       from '@/components/common/Breadcrumb.vue'

const authStore = useAuthStore()
const router    = useRouter()
const toast     = useToast()

const user     = computed(() => authStore.user)
const fullName = computed(() => authStore.fullName || user.value?.email || '')
const initials = computed(() => {
  const name = fullName.value
  return name.split(' ').filter(Boolean).slice(0, 2).map(w => w[0]).join('').toUpperCase() || '?'
})

const profileForm = reactive({
  first_name: user.value?.first_name || '',
  last_name:  user.value?.last_name  || '',
  username:   user.value?.username   || '',
})
const profileError = ref(null)

const pwdForm = reactive({ current_password: '', new_password: '', new_password_confirmation: '' })
const pwdError = ref(null)

async function handleUpdateProfile() {
  profileError.value = null
  try {
    await authStore.updateProfile(profileForm)
    toast.success('Profil mis à jour.')
  } catch (err) {
    profileError.value = err.response?.data?.message || 'Erreur lors de la mise à jour.'
  }
}

async function handleChangePassword() {
  pwdError.value = null
  if (pwdForm.new_password !== pwdForm.new_password_confirmation) {
    pwdError.value = 'Les mots de passe ne correspondent pas.'
    return
  }
  try {
    await authStore.updateProfile(pwdForm)
    toast.success('Mot de passe modifié.')
    Object.assign(pwdForm, { current_password: '', new_password: '', new_password_confirmation: '' })
  } catch (err) {
    pwdError.value = err.response?.data?.message || 'Erreur lors du changement de mot de passe.'
  }
}

async function handleLogout() {
  await authStore.logout()
  toast.success('Déconnexion réussie.')
  router.push({ name: 'home' })
}

onMounted(() => authStore.fetchCurrentUser())
</script>
