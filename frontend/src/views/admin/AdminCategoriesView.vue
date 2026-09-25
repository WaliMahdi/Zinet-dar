<template>
  <div class="space-y-6 max-w-5xl mx-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <h2 class="font-display text-2xl font-medium text-primary">Catégories</h2>
      <button @click="openCreateModal" class="btn-primary flex items-center gap-2">
        <Plus :size="16" /> Ajouter une catégorie
      </button>
    </div>

    <!-- Loading -->
    <div v-if="categorieStore.loading && !categorieStore.categories.length" class="space-y-4">
      <div v-for="i in 3" :key="i" class="h-16 skeleton rounded-lg"></div>
    </div>

    <!-- Content -->
    <div v-else class="grid gap-6">
      
      <!-- Liste des catégories -->
      <div class="bg-surface border border-border rounded-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-border bg-background/50">
          <h3 class="font-medium text-primary">Toutes les catégories</h3>
        </div>
        <div class="divide-y divide-border/50">
          <div v-if="categorieStore.categories.length === 0" class="p-8 text-center text-muted text-sm">
            Aucune catégorie pour le moment.
          </div>
          
          <div 
            v-for="cat in categorieStore.categories" 
            :key="cat.id" 
            class="p-4 flex items-center justify-between transition-colors hover:bg-background/50"
          >
            <div class="flex items-center gap-4">
              <div class="w-10 h-10 rounded bg-background border border-border flex items-center justify-center overflow-hidden">
                <img v-if="cat.image" :src="cat.image" class="w-full h-full object-cover" />
                <FolderTree v-else :size="18" class="text-muted" />
              </div>
              <div>
                <p class="font-medium text-primary">{{ cat.nom }}</p>
                <p class="text-xs text-muted">
                  {{ cat.produits_count ?? 0 }} 
                  {{ (cat.produits_count ?? 0) > 1 ? 'produits' : 'produit' }}
                </p>
              </div>
            </div>
            
            <div class="flex items-center gap-2">
              <button 
                @click.stop="openEditModal(cat)"
                class="p-2 text-muted hover:text-secondary rounded-full hover:bg-background transition-colors"
                title="Modifier"
              >
                <Edit :size="16" />
              </button>
              <button 
                @click.stop="deleteCategorie(cat.id)"
                class="p-2 text-muted hover:text-red-500 rounded-full hover:bg-red-50 transition-colors"
                title="Supprimer"
              >
                <Trash2 :size="16" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modale Création/Édition -->
    <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/50" @click="closeModal"></div>
      
      <div class="bg-surface rounded-lg shadow-xl w-full max-w-md relative z-10 overflow-hidden">
        <div class="px-6 py-4 border-b border-border flex justify-between items-center">
          <h3 class="font-display font-medium text-primary text-lg">
            {{ isEditing ? 'Modifier la catégorie' : 'Nouvelle catégorie' }}
          </h3>
          <button @click="closeModal" class="text-muted hover:text-primary transition-colors">
            <X :size="20" />
          </button>
        </div>
        
        <form @submit.prevent="saveCategorie" class="p-6 space-y-4">
          <div class="space-y-1">
            <label class="label">Nom de la catégorie *</label>
            <input v-model="form.nom" type="text" required class="input" placeholder="Ex: Salons" />
          </div>
          
          <div class="space-y-1">
            <label class="label">Description</label>
            <textarea v-model="form.description" rows="2" class="input" placeholder="Optionnel..."></textarea>
          </div>
          
          <div class="space-y-3">
            <label class="label text-sm font-medium text-primary flex items-center justify-between">
              <span>Image</span>
              <button 
                v-if="(form.image || localPreviewUrl) && !removeImageFlag" 
                type="button" 
                @click="removeImage" 
                class="text-xs text-red-500 hover:text-red-600 font-medium flex items-center gap-1"
              >
                <Trash2 :size="14" /> Supprimer
              </button>
            </label>
            
            <!-- Image Preview Area -->
            <div 
              class="w-full aspect-video rounded-lg border-2 border-dashed flex flex-col items-center justify-center relative overflow-hidden transition-colors"
              :class="(localPreviewUrl || form.image) && !removeImageFlag ? 'border-transparent bg-background' : 'border-border hover:border-primary/50 bg-background/50'"
            >
              <template v-if="(localPreviewUrl || form.image) && !removeImageFlag">
                <img 
                  :src="localPreviewUrl || form.image" 
                  class="w-full h-full object-cover" 
                  alt="Aperçu"
                />
                <!-- Replace overlay -->
                <div class="absolute inset-0 bg-black/50 opacity-0 hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer" @click="$refs.fileInput.click()">
                  <span class="text-white text-sm font-medium flex items-center gap-2">
                    <Edit :size="16" /> Remplacer l'image
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
              class="hidden"
              @change="onFileChange"
            />
          </div>
          
          <div class="pt-4 flex justify-end gap-3">
            <button type="button" @click="closeModal" class="btn-outline px-4">Annuler</button>
            <button type="submit" :disabled="isSaving" class="btn-primary px-4">
              {{ isSaving ? 'Enregistrement...' : 'Enregistrer' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { Plus, Edit, Trash2, FolderTree, X, Image as ImageIcon, Upload } from '@lucide/vue'
import { useAdminCategorieStore } from '@/stores/adminCategorieStore'
import { useToast } from '@/composables/useToast'

const categorieStore = useAdminCategorieStore()
const toast = useToast()

const isModalOpen = ref(false)
const isEditing = ref(false)
const isSaving = ref(false)
const editingId = ref(null)
const fileInput = ref(null)
const localPreviewUrl = ref('')
const removeImageFlag = ref(false)

const form = reactive({
  nom: '',
  description: '',
  image: null
})

function openCreateModal() {
  isEditing.value = false
  editingId.value = null
  form.nom = ''
  form.description = ''
  form.image = null
  localPreviewUrl.value = ''
  removeImageFlag.value = false
  if (fileInput.value) fileInput.value.value = ''
  isModalOpen.value = true
}

function openEditModal(cat) {
  isEditing.value = true
  editingId.value = cat.id
  form.nom = cat.nom
  form.description = cat.description || ''
  form.image = cat.image
  localPreviewUrl.value = ''
  removeImageFlag.value = false
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

function onFileChange(event) {
  const file = event.target.files[0]
  if (file) {
    if (localPreviewUrl.value) {
      URL.revokeObjectURL(localPreviewUrl.value)
    }
    localPreviewUrl.value = URL.createObjectURL(file)
    removeImageFlag.value = false
  }
}

function removeImage() {
  removeImageFlag.value = true
  localPreviewUrl.value = ''
  if (fileInput.value) fileInput.value.value = ''
}

async function saveCategorie() {
  isSaving.value = true
  
  const formData = new FormData()
  formData.append('nom', form.nom)
  if (form.description) formData.append('description', form.description)
  
  if (fileInput.value && fileInput.value.files[0] instanceof File) {
    formData.append('image', fileInput.value.files[0], fileInput.value.files[0].name)
  }

  if (removeImageFlag.value) {
    formData.append('remove_image', '1')
  }

  try {
    if (isEditing.value) {
      formData.append('_method', 'PUT')
      await categorieStore.updateCategorie(editingId.value, formData)
      toast.success('Catégorie mise à jour.')
    } else {
      await categorieStore.createCategorie(formData)
      toast.success('Catégorie créée avec succès.')
    }
    closeModal()
  } catch (err) {
    toast.error('Erreur lors de l\'enregistrement.')
  } finally {
    isSaving.value = false
  }
}

async function deleteCategorie(id) {
  if (!confirm('Voulez-vous vraiment supprimer cette catégorie ?')) return
  try {
    await categorieStore.deleteCategorie(id)
    toast.success('Catégorie supprimée.')
  } catch (err) {
    toast.error('Erreur lors de la suppression.')
  }
}

onMounted(() => {
  categorieStore.fetchCategories()
})
</script>
