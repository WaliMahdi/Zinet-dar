<template>
  <div class="space-y-8 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h2 class="font-display text-2xl font-medium text-primary">Gestion des bannières</h2>
        <p class="text-sm text-muted mt-1">Gérez les visuels affichés sur les différentes sections du site.</p>
      </div>
      <button @click="openCreateModal" class="btn-primary flex items-center gap-2">
        <Plus :size="16" /> Ajouter une bannière
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="space-y-8">
      <div v-for="i in 2" :key="i" class="space-y-4">
        <div class="h-6 w-48 skeleton rounded"></div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="j in 3" :key="j" class="h-48 skeleton rounded-lg"></div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <ErrorState v-else-if="error" :message="error" @retry="fetchBannieres" />

    <!-- Empty State -->
    <div v-else-if="bannieres.length === 0" class="bg-surface border border-border rounded-lg p-12 text-center">
      <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary/10 text-primary mb-4">
        <ImageIcon :size="32" />
      </div>
      <h3 class="text-lg font-medium text-primary mb-2">Aucune bannière disponible</h3>
      <p class="text-muted text-sm mb-6 max-w-sm mx-auto">
        Vous n'avez pas encore configuré de bannières pour le site. Ajoutez votre première bannière dès maintenant.
      </p>
      <button @click="openCreateModal" class="btn-primary inline-flex items-center gap-2">
        <Plus :size="16" /> Ajouter une bannière
      </button>
    </div>

    <!-- Content (Grouped by section) -->
    <div v-else class="space-y-12">
      <div v-for="section in sections" :key="section.id">
        <!-- Section Header -->
        <div class="flex items-center gap-3 mb-6 pb-2 border-b border-border">
          <component :is="section.icon" class="text-primary" :size="20" />
          <h3 class="font-display font-medium text-lg text-primary uppercase tracking-wider">
            {{ section.label }} <span class="text-muted text-sm normal-case font-normal ml-1">({{ groupedBannieres[section.id]?.length || 0 }})</span>
          </h3>
        </div>

        <!-- Section Grid -->
        <div v-if="groupedBannieres[section.id]?.length > 0" class="flex flex-col gap-12">
          <div 
            v-for="ban in groupedBannieres[section.id]" 
            :key="ban.id" 
            class="relative min-h-[60vh] lg:min-h-[75vh] flex items-center overflow-hidden rounded-2xl shadow-xl group"
          >
            <!-- Background image -->
            <div class="absolute inset-0">
              <img 
                :src="ban.image_url" 
                :alt="'Bannière ' + ban.section"
                class="w-full h-full object-cover object-center transition-transform duration-1000 ease-out group-hover:scale-105"
                loading="lazy"
              />
              <!-- Gradient overlay exact from HeroSection -->
              <div class="absolute inset-0 bg-hero-gradient"></div>
            </div>

            <!-- Content exact from HeroSection -->
            <div class="relative z-10 w-full px-6 md:px-12 lg:px-20">
              <div class="max-w-xl">
                <!-- Eyebrow -->
                <p class="text-secondary text-sm font-medium uppercase tracking-widest mb-4">
                  Collection 2026
                </p>

                <!-- Headline -->
                <h1 class="font-display text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-light text-white leading-none mb-6 capitalize">
                  {{ ban.section }}
                </h1>
              </div>
            </div>

            <!-- Admin Controls -->
            <div class="absolute top-4 right-4 md:top-6 md:right-6 z-20 flex flex-col sm:flex-row items-end sm:items-center gap-3">
              <button 
                @click.stop="toggleStatus(ban)"
                class="flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium border backdrop-blur-md transition-colors hover:brightness-110 shadow-sm"
                :class="ban.is_active ? 'bg-green-500/20 text-green-50 border-green-500/30' : 'bg-red-500/20 text-red-50 border-red-500/30'"
              >
                <span :class="['w-2 h-2 rounded-full', ban.is_active ? 'bg-green-400' : 'bg-red-400']"></span>
                {{ ban.is_active ? 'Active' : 'Inactive' }}
              </button>
              
              <div class="flex gap-2 bg-black/30 backdrop-blur-md rounded-lg p-1 border border-white/10 shadow-sm">
                <button 
                  @click.stop="openEditModal(ban)" 
                  class="p-2.5 text-white/80 hover:text-white rounded-md hover:bg-white/20 transition-colors"
                  title="Modifier"
                >
                  <Edit2 :size="18" />
                </button>
                <button 
                  @click.stop="deleteBanniere(ban.id)" 
                  class="p-2.5 text-white/80 hover:text-red-400 rounded-md hover:bg-white/20 transition-colors"
                  title="Supprimer"
                >
                  <Trash2 :size="18" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Section Empty State -->
        <div v-else class="text-sm text-muted bg-background/50 border border-dashed border-border rounded-xl p-8 text-center flex flex-col items-center justify-center">
          <ImageIcon class="text-border mb-2" :size="24" />
          <p>Aucune bannière pour la section {{ section.label }}.</p>
        </div>
      </div>
    </div>

    <!-- Modale Création / Édition -->
    <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="closeModal"></div>
      
      <div class="bg-surface rounded-xl shadow-2xl w-full max-w-lg relative z-10 max-h-[90vh] flex flex-col">
        <div class="px-6 py-4 border-b border-border flex justify-between items-center bg-surface rounded-t-xl shrink-0">
          <h3 class="font-display font-medium text-primary text-lg">
            {{ editingId ? 'Modifier la Bannière' : 'Nouvelle Bannière' }}
          </h3>
          <button @click="closeModal" class="text-muted hover:text-primary transition-colors p-1 rounded hover:bg-primary/5">
            <X :size="20" />
          </button>
        </div>
        
        <form @submit.prevent="saveBanniere" class="flex-1 overflow-y-auto p-6 space-y-6">
          
          <div class="space-y-3">
            <label class="label text-sm font-medium text-primary flex items-center justify-between">
              <span>Image de la bannière <span class="text-red-500" v-if="!editingId">*</span></span>
              <span class="text-xs text-muted font-normal">Format 21:9 recommandé</span>
            </label>
            
            <!-- Image Preview Area -->
            <div 
              class="w-full aspect-[21/9] rounded-lg border-2 border-dashed flex flex-col items-center justify-center relative overflow-hidden transition-colors"
              :class="localPreviewUrl || existingImageUrl ? 'border-transparent bg-background' : 'border-border hover:border-primary/50 bg-background/50'"
            >
              <template v-if="localPreviewUrl || existingImageUrl">
                <img 
                  :src="localPreviewUrl || existingImageUrl" 
                  class="w-full h-full object-cover" 
                  alt="Aperçu"
                />
                <!-- Replace overlay -->
                <div class="absolute inset-0 bg-black/50 opacity-0 hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer" @click="$refs.fileInput.click()">
                  <span class="text-white text-sm font-medium flex items-center gap-2">
                    <Edit2 :size="16" /> Remplacer l'image
                  </span>
                </div>
              </template>
              <template v-else>
                <div class="text-center cursor-pointer p-4 w-full h-full flex flex-col items-center justify-center" @click="$refs.fileInput.click()">
                  <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center mb-2">
                    <Upload :size="20" />
                  </div>
                  <p class="text-sm font-medium text-primary">Cliquez pour uploader</p>
                  <p class="text-xs text-muted mt-1">JPG, PNG ou WEBP (max 5 Mo)</p>
                </div>
              </template>
            </div>

            <!-- Hidden File Input -->
            <input 
              type="file" 
              ref="fileInput" 
              accept="image/jpeg,image/png,image/webp,image/jpg" 
              :required="!editingId && !localPreviewUrl" 
              class="hidden"
              @change="onFileChange"
            />
            
            <p v-if="editingId && !localPreviewUrl" class="text-xs text-muted">
              L'image actuelle sera conservée si vous n'en sélectionnez pas une nouvelle.
            </p>
          </div>

          <div class="space-y-4">
            <div class="space-y-1.5">
              <label class="label text-sm font-medium text-primary">Titre (Optionnel)</label>
              <input v-model="form.title" type="text" class="input w-full" placeholder="Texte affiché sur la bannière (ex: Promotions)" />
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-1.5">
                <label class="label text-sm font-medium text-primary">Emplacement</label>
                <input 
                  type="text" 
                  class="input w-full bg-background/50 text-muted cursor-not-allowed capitalize" 
                  :value="sections.find(s => s.id === form.section)?.label || form.section" 
                  disabled 
                />
              </div>
              <div class="space-y-1.5">
                <label class="label text-sm font-medium text-primary">Statut</label>
                <select v-model="form.is_active" class="input w-full">
                  <option :value="1">Active</option>
                  <option :value="0">Inactive</option>
                </select>
              </div>
            </div>
          </div>
          
          <div class="pt-6 flex justify-end gap-3 border-t border-border mt-8">
            <button type="button" @click="closeModal" class="btn-outline px-5">Annuler</button>
            <button type="submit" :disabled="isSaving" class="btn-primary px-6 flex items-center gap-2">
              <span v-if="isSaving" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
              {{ isSaving ? 'Enregistrement...' : (editingId ? 'Mettre à jour' : 'Créer la bannière') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modale Prévisualisation (Preview) -->
    <div v-if="previewBannerData" class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-10">
      <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="closePreviewModal"></div>
      
      <div class="relative w-full max-w-6xl z-10 flex flex-col">
        <div class="flex justify-between items-center mb-4 text-white">
          <h3 class="font-medium text-lg">Aperçu : Section {{ previewBannerData.section }}</h3>
          <button @click="closePreviewModal" class="p-2 hover:bg-white/10 rounded-full transition-colors text-white/80 hover:text-white">
            <X :size="24" />
          </button>
        </div>
        
        <!-- Conteneur image qui respecte le ratio réel d'une bannière -->
        <div class="w-full bg-black rounded-lg overflow-hidden shadow-2xl relative aspect-[21/9]">
          <img 
            :src="previewBannerData.image_url" 
            class="w-full h-full object-cover"
            alt="Aperçu grand format" 
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'
import { Plus, Trash2, X, Edit2, Eye, Image as ImageIcon, Upload, Home, ShoppingBag, Sparkles, Tag, Layers } from '@lucide/vue'
import { adminBanniereService } from '@/services/adminBanniereService'
import { useToast } from '@/composables/useToast'
import ErrorState from '@/components/common/ErrorState.vue'

const toast = useToast()

// Constants
const sections = [
  { id: 'categories', label: 'Catégories', icon: Layers },
  { id: 'boutique', label: 'Boutique', icon: ShoppingBag },
  { id: 'nouveautes', label: 'Nouveautés', icon: Sparkles },
  { id: 'promotions', label: 'Promotions', icon: Tag }
]

// State
const bannieres = ref([])
const loading = ref(true)
const error = ref(null)

// Modal State
const isModalOpen = ref(false)
const isSaving = ref(false)
const fileInput = ref(null)
const editingId = ref(null)
const existingImageUrl = ref('')
const localPreviewUrl = ref('')

// Preview Modal State
const previewBannerData = ref(null)

const form = reactive({
  title: '',
  section: 'categories',
  is_active: 1
})

// Computed
const groupedBannieres = computed(() => {
  const groups = {
    accueil: [],
    categories: [],
    boutique: [],
    nouveautes: [],
    promotions: []
  }
  
  bannieres.value.forEach(ban => {
    if (groups[ban.section]) {
      groups[ban.section].push(ban)
    }
  })
  
  return groups
})

// Fetch
async function fetchBannieres() {
  loading.value = true
  error.value = null
  try {
    const res = await adminBanniereService.getAll()
    bannieres.value = res.data
  } catch (err) {
    error.value = "Impossible de charger les bannières."
    toast.error('Erreur lors du chargement des bannières.')
  } finally {
    loading.value = false
  }
}

// File Handling
function onFileChange(event) {
  const file = event.target.files[0]
  if (file) {
    // Validate type (basic client side)
    const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg']
    if (!validTypes.includes(file.type)) {
      toast.error('Format non supporté. Utilisez JPG, PNG ou WEBP.')
      if (fileInput.value) fileInput.value.value = ''
      return
    }
    
    // Validate size (< 5MB)
    if (file.size > 5 * 1024 * 1024) {
      toast.error('L\'image est trop lourde (max 5 Mo).')
      if (fileInput.value) fileInput.value.value = ''
      return
    }
    
    // Clean up previous blob URL to avoid memory leaks
    if (localPreviewUrl.value) {
      URL.revokeObjectURL(localPreviewUrl.value)
    }
    
    localPreviewUrl.value = URL.createObjectURL(file)
  }
}

// Modals
function openCreateModal() {
  editingId.value = null
  existingImageUrl.value = ''
  localPreviewUrl.value = ''
  form.title = ''
  form.section = 'categories'
  form.is_active = 1
  if (fileInput.value) fileInput.value.value = ''
  isModalOpen.value = true
}

function openEditModal(ban) {
  editingId.value = ban.id
  existingImageUrl.value = ban.image_url
  localPreviewUrl.value = ''
  form.title = ban.title || ''
  form.section = ban.section || 'categories'
  form.is_active = ban.is_active
  if (fileInput.value) fileInput.value.value = ''
  isModalOpen.value = true
}

function closeModal() {
  isModalOpen.value = false
  if (localPreviewUrl.value) {
    URL.revokeObjectURL(localPreviewUrl.value)
    localPreviewUrl.value = ''
  }
}

function openPreviewModal(ban) {
  previewBannerData.value = ban
  document.body.style.overflow = 'hidden'
}

function closePreviewModal() {
  previewBannerData.value = null
  document.body.style.overflow = ''
}

// Actions
async function saveBanniere() {
  const file = fileInput.value && fileInput.value.files[0]
  
  if (!editingId.value && !file) {
    toast.error('Veuillez sélectionner une image.')
    return
  }

  isSaving.value = true
  const submitForm = {
    ...form,
    image: file || null
  }

  try {
    if (editingId.value) {
      await adminBanniereService.updateBanniere(editingId.value, submitForm)
      toast.success('Bannière mise à jour avec succès.')
    } else {
      await adminBanniereService.createBanniere(submitForm)
      toast.success('Nouvelle bannière ajoutée avec succès.')
    }
    closeModal()
    fetchBannieres()
  } catch (err) {
    toast.error('Erreur lors de la sauvegarde de la bannière.')
  } finally {
    isSaving.value = false
  }
}

async function toggleStatus(ban) {
  const originalStatus = ban.is_active
  const newStatus = originalStatus ? 0 : 1
  
  // Optimistic UI update
  ban.is_active = newStatus
  
  try {
    await adminBanniereService.updateStatus(ban.id, newStatus)
    toast.success(`La bannière a été ${newStatus ? 'activée' : 'désactivée'}.`)
  } catch (err) {
    // Revert on error
    ban.is_active = originalStatus
    toast.error('Erreur lors du changement de statut.')
  }
}

async function deleteBanniere(id) {
  if (!confirm('Voulez-vous vraiment supprimer cette bannière ? Cette action est irréversible.')) return
  
  try {
    await adminBanniereService.deleteBanniere(id)
    toast.success('Bannière supprimée avec succès.')
    fetchBannieres()
  } catch (err) {
    toast.error('Erreur lors de la suppression de la bannière.')
  }
}

// Lifecycle
onMounted(() => {
  fetchBannieres()
})

onUnmounted(() => {
  if (localPreviewUrl.value) {
    URL.revokeObjectURL(localPreviewUrl.value)
  }
  document.body.style.overflow = ''
})
</script>
