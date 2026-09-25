<template>
  <div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <RouterLink :to="{ name: 'admin-produits' }" class="text-muted hover:text-primary transition-colors">
          <ArrowLeft :size="20" />
        </RouterLink>
        <h2 class="font-display text-2xl font-medium text-primary">
          {{ isEditing ? 'Modifier le produit' : 'Nouveau produit' }}
        </h2>
      </div>
    </div>

    <!-- Form -->
    <form @submit.prevent="handleSubmit" class="space-y-6">
      
      <!-- Basic Info -->
      <div class="bg-surface border border-border rounded-lg p-6 space-y-6">
        <h3 class="font-medium text-primary border-b border-border pb-3">Informations de base</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-1 md:col-span-2">
            <label class="label">Nom du produit *</label>
            <input v-model="form.nom" type="text" required class="input" placeholder="Ex: Canapé d'angle moderne" />
          </div>

          <div class="space-y-1">
            <label class="label">Référence *</label>
            <input v-model="form.reference" type="text" required class="input uppercase" placeholder="Ex: REF-001" />
          </div>

          <div class="space-y-1">
            <label class="label">Catégorie *</label>
            <select v-model="form.categorie_id" required class="input">
              <option value="" disabled>Sélectionner une catégorie</option>
              <option v-for="cat in categorieStore.categories" :key="cat.id" :value="cat.id">
                {{ cat.nom }}
              </option>
            </select>
          </div>
        </div>

        <div class="space-y-1">
          <label class="label">Description courte</label>
          <textarea v-model="form.description_courte" rows="2" class="input" placeholder="Une brève description..."></textarea>
        </div>

        <div class="space-y-1">
          <label class="label">Description détaillée</label>
          <textarea v-model="form.description_detaillee" rows="5" class="input" placeholder="Description complète..."></textarea>
        </div>
      </div>

      <!-- Pricing & Stock -->
      <div class="bg-surface border border-border rounded-lg p-6 space-y-6">
        <h3 class="font-medium text-primary border-b border-border pb-3">Prix et Stock</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="space-y-1">
            <label class="label">Prix normal (TND) *</label>
            <input v-model.number="form.prix" type="number" step="0.001" min="0" required class="input" />
          </div>

          <div class="space-y-1">
            <label class="label">Remise (%)</label>
            <input v-model.number="form.remise" type="number" step="1" min="0" max="100" class="input" />
          </div>

          <div class="space-y-1">
            <label class="label">Prix après remise</label>
            <input :value="prixCalcule" type="text" disabled class="input bg-background/50 cursor-not-allowed font-medium text-primary" />
          </div>

          <div class="space-y-1">
            <label class="label">Quantité en stock *</label>
            <input v-model.number="form.quantite_stock" type="number" min="0" required class="input" />
          </div>
        </div>
      </div>

      <!-- Image & Status -->
      <div class="bg-surface border border-border rounded-lg p-6 space-y-6">
        <h3 class="font-medium text-primary border-b border-border pb-3">Image et Visibilité</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-4">
            <label class="label">Image principale</label>
            
            <div 
              class="w-full aspect-square md:aspect-video rounded-lg border-2 border-dashed border-border flex flex-col items-center justify-center overflow-hidden relative hover:border-secondary transition-colors cursor-pointer"
              @click="$refs.fileInput.click()"
            >
              <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover" />
              <div v-else class="text-center p-6">
                <UploadCloud :size="32" class="mx-auto text-muted mb-2" />
                <p class="text-sm font-medium text-primary">Cliquez pour ajouter une image</p>
                <p class="text-xs text-muted mt-1">PNG, JPG ou WEBP (max. 2Mo)</p>
              </div>
              <input 
                ref="fileInput" 
                type="file" 
                accept="image/jpeg,image/jpg,image/png,image/webp" 
                class="hidden" 
                @change="handleImageChange" 
              />
            </div>
          </div>

          <div class="space-y-6">
            <div class="flex items-center justify-between p-4 bg-background rounded-lg border border-border/50">
              <div>
                <p class="font-medium text-primary">Produit actif</p>
                <p class="text-xs text-muted">Afficher ce produit sur la boutique</p>
              </div>
              <button 
                type="button"
                @click="form.actif = !form.actif"
                :class="[
                  'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none',
                  form.actif ? 'bg-secondary' : 'bg-border'
                ]"
              >
                <span 
                  :class="[
                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                    form.actif ? 'translate-x-5' : 'translate-x-0'
                  ]"
                />
              </button>
            </div>
          </div>

          <!-- Galerie d'images -->
          <div class="space-y-4 md:col-span-2 pt-4 border-t border-border">
            <label class="label">Images supplémentaires (Galerie)</label>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
              <!-- Upload Button -->
              <div 
                class="aspect-square rounded-lg border-2 border-dashed border-border flex flex-col items-center justify-center relative hover:border-secondary transition-colors cursor-pointer"
                @click="$refs.galleryInput.click()"
              >
                <UploadCloud :size="24" class="text-muted mb-2" />
                <span class="text-xs font-medium text-primary text-center px-2">Ajouter des images</span>
                <input 
                  ref="galleryInput" 
                  type="file" 
                  multiple
                  accept="image/jpeg,image/jpg,image/png,image/webp" 
                  class="hidden" 
                  @change="handleGalleryChange" 
                />
              </div>

              <!-- Existing Images -->
              <div v-for="img in existingGallery" :key="img.id" class="aspect-square rounded-lg border border-border/50 relative overflow-hidden group">
                <img :src="img.chemin" class="w-full h-full object-cover" @error="console.error('Erreur image admin:', img.chemin)" />
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                  <button type="button" @click.prevent="deleteExistingImage(img.id)" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition-colors shadow">
                    Supprimer
                  </button>
                </div>
              </div>

              <!-- New Selected Images Preview -->
              <div v-for="(preview, index) in galleryPreviews" :key="'new-'+index" class="aspect-square rounded-lg border border-border/50 relative overflow-hidden group">
                <img :src="preview.url" class="w-full h-full object-cover" />
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                  <button type="button" @click.prevent="removeNewImage(index)" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition-colors shadow">
                    Retirer
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex justify-end gap-3 pt-4">
        <RouterLink :to="{ name: 'admin-produits' }" class="btn-outline px-6">
          Annuler
        </RouterLink>
        <button type="submit" :disabled="produitStore.loading" class="btn-primary px-8">
          {{ produitStore.loading ? 'Enregistrement...' : (isEditing ? 'Mettre à jour' : 'Créer le produit') }}
        </button>
      </div>

    </form>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { ArrowLeft, UploadCloud } from '@lucide/vue'
import { useAdminProduitStore } from '@/stores/adminProduitStore'
import { useAdminCategorieStore } from '@/stores/adminCategorieStore'
import { useToast } from '@/composables/useToast'
import { adminProduitService } from '@/services/adminProduitService'

const route = useRoute()
const router = useRouter()
const produitStore = useAdminProduitStore()
const categorieStore = useAdminCategorieStore()
const toast = useToast()

const isEditing = computed(() => !!route.params.id)
const fileInput = ref(null)
const imagePreview = ref(null)
const selectedFile = ref(null)

const galleryInput = ref(null)
const existingGallery = ref([])
const newGalleryFiles = ref([])
const galleryPreviews = ref([])

const form = reactive({
  nom: '',
  reference: '',
  description_courte: '',
  description_detaillee: '',
  prix: 0,
  remise: 0,
  quantite_stock: 0,
  categorie_id: '',
  actif: true
})

const prixCalcule = computed(() => {
  if (!form.prix) return '0.000 TND'
  if (!form.remise) return `${form.prix.toFixed(3)} TND`
  const final = form.prix - (form.prix * (form.remise / 100))
  return `${final.toFixed(3)} TND`
})

function handleImageChange(e) {
  const file = e.target.files?.[0] ?? null
  
  if (!file) {
    selectedFile.value = null
    return
  }
  
  if (!file.type.startsWith('image/')) {
    toast.error('Le fichier sélectionné n\'est pas une image.')
    selectedFile.value = null
    return
  }

  if (file.size > 2 * 1024 * 1024) {
    toast.error('L\'image ne doit pas dépasser 2Mo.')
    selectedFile.value = null
    return
  }

  selectedFile.value = file
  imagePreview.value = URL.createObjectURL(file)
}

function handleGalleryChange(e) {
  const files = Array.from(e.target.files || [])
  for (const file of files) {
    if (!file.type.startsWith('image/')) {
      toast.error(`Le fichier ${file.name} n'est pas une image.`)
      continue
    }
    if (file.size > 5 * 1024 * 1024) {
      toast.error(`L'image ${file.name} dépasse 5Mo.`)
      continue
    }
    newGalleryFiles.value.push(file)
    galleryPreviews.value.push({ url: URL.createObjectURL(file) })
  }
  // Reset input so the same files can be selected again if removed
  if (e.target) e.target.value = ''
}

function removeNewImage(index) {
  newGalleryFiles.value.splice(index, 1)
  galleryPreviews.value.splice(index, 1)
}

async function deleteExistingImage(imageId) {
  if (!confirm('Voulez-vous vraiment supprimer cette image de la galerie ?')) return
  try {
    await adminProduitService.deleteImage(route.params.id, imageId)
    existingGallery.value = existingGallery.value.filter(img => img.id !== imageId)
    toast.success('Image supprimée.')
  } catch (err) {
    toast.error('Erreur lors de la suppression de l\'image.')
  }
}

async function handleSubmit() {
  const formData = new FormData()
  
  // Append all form fields
  Object.keys(form).forEach(key => {
    formData.append(key, form[key] === null ? '' : form[key])
  })
  
  // Convert boolean to 1/0 for PHP
  formData.set('actif', form.actif ? 1 : 0)

  // Append image if selected
  if (selectedFile.value instanceof File) {
    formData.append('image', selectedFile.value, selectedFile.value.name)
  }

  // Append new gallery images
  newGalleryFiles.value.forEach((file) => {
    formData.append('images[]', file)
  })

  try {
    if (isEditing.value) {
      formData.append('_method', 'PUT')
      await produitStore.updateProduit(route.params.id, formData)
      toast.success('Produit mis à jour avec succès.')
    } else {
      await produitStore.createProduit(formData)
      toast.success('Produit créé avec succès.')
    }
    router.push({ name: 'admin-produits' })
  } catch (err) {
    toast.error('Erreur lors de l\'enregistrement.')
  }
}

async function loadData() {
  // Load categories for the select
  if (categorieStore.categories.length === 0) {
    await categorieStore.fetchCategories()
  }

  if (isEditing.value) {
    try {
      const data = await adminProduitService.getById(route.params.id)
      const p = data.data
      
      form.nom = p.nom
      form.reference = p.reference
      form.description_courte = p.description_courte || ''
      form.description_detaillee = p.description_detaillee || ''
      form.prix = parseFloat(p.prix)
      form.remise = parseFloat(p.remise || 0)
      form.quantite_stock = parseInt(p.quantite_stock || 0)
      form.categorie_id = p.categorie_id
      form.actif = !!p.actif

      if (p.image) {
        imagePreview.value = p.image
      }
      
      if (p.images) {
        existingGallery.value = p.images
      }
    } catch (err) {
      toast.error('Impossible de charger le produit.')
      router.push({ name: 'admin-produits' })
    }
  }
}

onMounted(loadData)
</script>
