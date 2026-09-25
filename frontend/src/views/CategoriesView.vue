<template>
  <div class="min-h-screen bg-background">
    <!-- Breadcrumb -->
    <div class="bg-surface py-4 border-b border-border">
      <div class="container-site">
        <Breadcrumb />
      </div>
    </div>

    <BannerSection 
      section="categories" 
      eyebrow="Collections" 
      title="Toutes les catégories" 
    />

    <div class="container-site py-12">
      <!-- Loading -->
      <div v-if="loading" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
        <div v-for="n in 8" :key="n" class="aspect-square skeleton rounded-lg"></div>
      </div>

      <!-- Error -->
      <ErrorState v-else-if="error" :message="error" @retry="loadCategories" />

      <!-- Categories -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <RouterLink
          v-for="(cat, idx) in categories"
          :key="cat.id"
          :to="{ name: 'shop', query: { categorie_id: cat.id } }"
          class="group relative rounded-xl overflow-hidden bg-surface border border-border/50
                 hover:shadow-warm hover:-translate-y-1 transition-all duration-300 ease-warm"
        >
          <!-- Image / gradient -->
          <div class="aspect-[16/9] relative overflow-hidden">
            <img
              v-if="cat.image"
              :src="cat.image"
              :alt="cat.nom"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />
            <div
              v-else
              :style="{ background: gradients[idx % gradients.length] }"
              class="w-full h-full"
            />
            <div class="absolute inset-0 bg-primary/30 group-hover:bg-primary/20 transition-colors" />
          </div>
          <!-- Content -->
          <div class="p-5 flex items-start justify-between">
            <div>
              <h2 class="font-display text-xl font-medium text-primary group-hover:text-secondary transition-colors">{{ cat.nom }}</h2>
              <p v-if="cat.description" class="text-muted text-sm mt-1 line-clamp-2">{{ cat.description }}</p>
            </div>
            <ArrowRight :size="18" class="text-muted group-hover:text-secondary group-hover:translate-x-1 transition-all flex-shrink-0 mt-1" />
          </div>
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { ArrowRight } from '@lucide/vue'
import { categorieService } from '@/services/categorieService'
import ErrorState from '@/components/common/ErrorState.vue'
import BannerSection from '@/components/common/BannerSection.vue'
import Breadcrumb from '@/components/common/Breadcrumb.vue'

const categories = ref([])
const loading    = ref(true)
const error      = ref(null)

const gradients = [
  'linear-gradient(135deg, #3D3530, #6B5043)',
  'linear-gradient(135deg, #5C4A3A, #8B6E55)',
  'linear-gradient(135deg, #354038, #607068)',
  'linear-gradient(135deg, #4A3E35, #7A6254)',
  'linear-gradient(135deg, #503828, #907058)',
  'linear-gradient(135deg, #3E4840, #687870)',
  'linear-gradient(135deg, #4A4030, #8A7060)',
  'linear-gradient(135deg, #3A3530, #7A6560)',
]

async function loadCategories() {
  loading.value = true
  error.value   = null
  try {
    const data = await categorieService.getAll()
    categories.value = data.data || []
  } catch {
    error.value = 'Impossible de charger les catégories.'
  } finally {
    loading.value = false
  }
}

onMounted(loadCategories)
</script>
