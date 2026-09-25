<template>
  <section class="py-16 md:py-24 bg-background overflow-hidden">
    <div class="container-site">
      <!-- Header -->
      <div class="text-center mb-16 flex flex-col items-center">
        <span class="text-secondary text-sm font-medium tracking-[0.2em] uppercase mb-3">Univers</span>
        <h2 class="font-display text-4xl md:text-5xl font-medium text-primary">Explorez nos collections</h2>
        <div class="w-16 h-[2px] bg-secondary/50 mt-6 mb-6"></div>
        <p class="text-muted text-base max-w-xl mx-auto font-light">
          Chaque espace mérite une attention particulière. Découvrez nos sélections exclusives pour sublimer votre intérieur.
        </p>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 auto-rows-[300px]">
        <div v-for="n in 4" :key="n" class="skeleton rounded-2xl" :class="getGridClass(n - 1)"></div>
      </div>

      <!-- Categories Bento Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 auto-rows-[250px] md:auto-rows-[300px] lg:auto-rows-[340px]">
        <RouterLink
          v-for="(cat, idx) in displayCategories"
          :key="cat.id || idx"
          :to="{ name: 'shop', query: { categorie_id: cat.id } }"
          :class="getGridClass(idx)"
          class="group relative rounded-2xl overflow-hidden cursor-pointer bg-surface"
        >
          <!-- Image Layer -->
          <div class="absolute inset-0 transition-transform duration-700 ease-out group-hover:scale-105">
            <img
              v-if="cat.image"
              :src="cat.image"
              :alt="cat.nom"
              class="w-full h-full object-cover"
              loading="lazy"
              @error="$event.target.style.display='none'"
            />
            <div
              v-else
              :style="{ background: categoryGradient(idx) }"
              class="w-full h-full"
            />
          </div>

          <!-- Overlay Layer -->
          <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-70 group-hover:opacity-90 transition-opacity duration-500 ease-in-out"></div>

          <!-- Content Layer -->
          <div class="absolute inset-0 p-6 md:p-8 flex flex-col justify-end items-start text-left">
            <div class="transform transition-transform duration-500 ease-out group-hover:-translate-y-2">
              <h3 class="font-display text-2xl md:text-3xl lg:text-4xl font-medium text-white tracking-wide">
                {{ cat.nom }}
              </h3>
              <p v-if="cat.description" class="text-white/80 text-sm md:text-base mt-2 max-w-md font-light leading-relaxed opacity-0 group-hover:opacity-100 transition-opacity duration-500 ease-out line-clamp-2">
                {{ cat.description }}
              </p>
            </div>
            
            <!-- Hover Action Icon -->
            <div class="absolute bottom-6 md:bottom-8 right-6 md:right-8 opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-500 ease-out">
              <div class="w-12 h-12 rounded-full bg-white text-primary flex items-center justify-center shadow-lg">
                <ArrowRight :size="20" class="transition-transform duration-300 group-hover:translate-x-1" />
              </div>
            </div>
          </div>
        </RouterLink>
      </div>

      <!-- View all -->
      <div class="mt-16 text-center">
        <RouterLink :to="{ name: 'categories' }" class="inline-flex items-center gap-3 px-8 py-4 bg-transparent border border-primary text-primary hover:bg-primary hover:text-white transition-colors duration-300 rounded-full font-medium tracking-wide uppercase text-sm">
          Voir tous les univers <ArrowRight :size="16" />
        </RouterLink>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { ArrowRight } from '@lucide/vue'
import { categorieService } from '@/services/categorieService'

const categories = ref([])
const loading    = ref(true)

const displayCategories = computed(() => categories.value.slice(0, 4))

const GRADIENTS = [
  'linear-gradient(135deg, #3D3530 0%, #6B5043 100%)',
  'linear-gradient(135deg, #5C4A3A 0%, #8B6E55 100%)',
  'linear-gradient(135deg, #4A3E35 0%, #7A6254 100%)',
  'linear-gradient(135deg, #3E4840 0%, #687870 100%)',
  'linear-gradient(135deg, #4A4030 0%, #8A7060 100%)',
  'linear-gradient(135deg, #503828 0%, #907058 100%)',
  'linear-gradient(135deg, #354038 0%, #607068 100%)',
]
function categoryGradient(idx) { return GRADIENTS[idx % GRADIENTS.length] }

/**
 * Fixed Bento Grid Layout Logic for exactly 4 items
 * Desktop (4 cols):
 * [0: 2x2] [1: 2x1]
 * [0: 2x2] [2: 1x1] [3: 1x1]
 * Tablet (2 cols):
 * [0: 2x1]
 * [1: 1x1] [2: 1x1]
 * [3: 2x1]
 */
function getGridClass(idx) {
  if (idx === 0) return 'md:col-span-2 lg:col-span-2 lg:row-span-2'
  if (idx === 1) return 'md:col-span-1 lg:col-span-2'
  if (idx === 2) return 'md:col-span-1 lg:col-span-1'
  if (idx === 3) return 'md:col-span-2 lg:col-span-1'
  
  return 'col-span-1'
}

onMounted(async () => {
  try {
    const data = await categorieService.getAll()
    categories.value = data.data || []
  } catch {
    categories.value = STATIC_CATEGORIES
  } finally {
    loading.value = false
  }
})

// Static fallback
const STATIC_CATEGORIES = [
  { id: null, nom: 'Meubles',      description: 'Trouvez le meuble idéal pour aménager vos espaces intérieurs avec goût et raffinement.', image: null },
  { id: null, nom: 'Tables',       description: 'Tables de tous styles pour la salle à manger, le salon ou la cuisine.', image: null },
  { id: null, nom: 'Chaises',      description: 'Pour chaque espace, un confort optimal et un design soigné.', image: null },
  { id: null, nom: 'Fauteuils',    description: 'Confort et élégance pour vos coins lecture et détente.', image: null },
  { id: null, nom: 'Canapés',      description: 'Pour vos moments de détente en famille ou entre amis.', image: null },
  { id: null, nom: 'Miroirs',      description: 'Agrandissez vos espaces avec notre collection de miroirs design.', image: null },
  { id: null, nom: 'Décoration',   description: 'Mille et un détails qui transforment une maison en un véritable foyer chaleureux.', image: null },
]
</script>
