<template>
  <div class="space-y-6 max-w-3xl mx-auto">
    <div class="flex items-center justify-between">
      <h2 class="font-display text-2xl font-medium text-primary">Paramètres du site</h2>
    </div>

    <!-- Paramètre Logo -->
    <div class="bg-surface border border-border rounded-lg p-6 space-y-6">
      <h3 class="font-medium text-primary border-b border-border pb-3">Identité visuelle</h3>
      
      <div class="space-y-4">
        <label class="label">Logo de l'entreprise</label>
        
        <div class="flex flex-col md:flex-row gap-6 items-start">
          <div 
            class="w-48 h-48 rounded-lg border-2 border-dashed border-border flex flex-col items-center justify-center overflow-hidden relative hover:border-secondary transition-colors cursor-pointer bg-background"
            @click="$refs.fileInput.click()"
          >
            <img v-if="logoPreview || logoUrl" :src="logoPreview || logoUrl" class="max-w-full max-h-full object-contain p-4" />
            <div v-else class="text-center p-6">
              <UploadCloud :size="32" class="mx-auto text-muted mb-2" />
              <p class="text-xs text-muted">Cliquez pour ajouter</p>
            </div>
            <input 
              ref="fileInput" 
              type="file" 
              accept="image/*" 
              class="hidden" 
              @change="handleImageChange" 
            />
          </div>

          <div class="space-y-4 flex-1">
            <p class="text-sm text-muted">
              Ce logo sera affiché dans l'en-tête du site et sur la page de connexion. Il remplacera le logo SVG actuel.
            </p>
            <div class="flex gap-3">
              <button 
                type="button"
                @click="uploadLogo"
                :disabled="!selectedFile || isSaving"
                class="btn-primary"
              >
                {{ isSaving ? 'Enregistrement...' : 'Enregistrer le logo' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { UploadCloud } from '@lucide/vue'
import { adminSettingsService } from '@/services/adminSettingsService'
import { useToast } from '@/composables/useToast'

const toast = useToast()
const fileInput = ref(null)
const logoPreview = ref(null)
const logoUrl = ref(null)
const selectedFile = ref(null)
const isSaving = ref(false)

function handleImageChange(e) {
  const file = e.target.files[0]
  if (!file) return
  
  if (file.size > 2 * 1024 * 1024) {
    toast.error('Le logo ne doit pas dépasser 2Mo.')
    return
  }

  selectedFile.value = file
  logoPreview.value = URL.createObjectURL(file)
}

async function uploadLogo() {
  if (!selectedFile.value) return
  
  isSaving.value = true
  try {
    const res = await adminSettingsService.updateLogo(selectedFile.value)
    logoUrl.value = res.data.value
    selectedFile.value = null
    logoPreview.value = null
    toast.success('Logo mis à jour avec succès.')
  } catch (err) {
    toast.error('Erreur lors de la mise à jour du logo.')
  } finally {
    isSaving.value = false
  }
}

async function loadSettings() {
  try {
    const res = await adminSettingsService.getLogo()
    if (res.data && res.data.value) {
      logoUrl.value = res.data.value
    }
  } catch (err) {
    console.error(err)
  }
}

onMounted(loadSettings)
</script>
