<template>
  <div class="space-y-4 select-none">
    <!-- Main image -->
    <div 
      class="relative aspect-[4/3] bg-surface rounded-xl overflow-hidden cursor-zoom-in"
      @mouseenter="isHovering = true"
      @mouseleave="isHovering = false"
      @mousemove="handleMouseMove"
      @click="openLightbox(currentIndex)"
    >
      <Transition name="gallery-fade" mode="out-in">
        <img
          :key="currentImage"
          :src="currentImage"
          :alt="product.nom"
          :style="imageTransformStyle"
          class="w-full h-full object-cover transition-transform duration-200 ease-out will-change-transform"
          @error="onMainError"
        />
      </Transition>

      <!-- Badges overlay -->
      <div class="absolute top-4 left-4 flex flex-col gap-2 z-10 pointer-events-none">
        <span v-if="product.nouveau" class="badge-new shadow-sm">Nouveau</span>
        <span v-if="discountLabel" class="badge-promo shadow-sm">{{ discountLabel }}</span>
      </div>
      
      <!-- Prev/Next Overlay Buttons (Desktop Hover) -->
      <div 
        v-if="allImages.length > 1" 
        class="absolute inset-0 flex items-center justify-between p-4 opacity-0 hover:opacity-100 transition-opacity duration-300 pointer-events-none md:pointer-events-auto"
      >
        <button 
          @click.stop="prevImage" 
          class="w-10 h-10 rounded-full bg-white/80 backdrop-blur-sm text-primary flex items-center justify-center shadow-md hover:bg-white hover:scale-105 transition-all pointer-events-auto"
          aria-label="Image précédente"
        >
          <ChevronLeft :size="20" />
        </button>
        <button 
          @click.stop="nextImage" 
          class="w-10 h-10 rounded-full bg-white/80 backdrop-blur-sm text-primary flex items-center justify-center shadow-md hover:bg-white hover:scale-105 transition-all pointer-events-auto"
          aria-label="Image suivante"
        >
          <ChevronRight :size="20" />
        </button>
      </div>
    </div>

    <!-- Thumbnails -->
    <div v-if="allImages.length > 1" class="flex gap-3 overflow-x-auto scrollbar-none py-1 px-0.5">
      <button
        v-for="(img, idx) in allImages"
        :key="idx"
        @click="currentIndex = idx"
        :class="[
          'flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 transition-all duration-300',
          currentIndex === idx 
            ? 'border-secondary shadow-md scale-100' 
            : 'border-transparent opacity-70 hover:opacity-100 hover:scale-[1.02] bg-surface'
        ]"
        :aria-label="`Image ${idx + 1}`"
      >
        <img :src="img.src" :alt="img.alt" class="w-full h-full object-cover" @error="$event.target.src = placeholder" loading="lazy" />
      </button>
    </div>

    <!-- Lightbox -->
    <Teleport to="body">
      <Transition name="fade">
        <div 
          v-if="isLightboxOpen" 
          class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 backdrop-blur-sm"
          @click="closeLightbox"
          @touchstart="handleTouchStart"
          @touchend="handleTouchEnd"
        >
          <!-- Top Bar -->
          <div class="absolute top-0 inset-x-0 p-4 flex justify-between items-center text-white z-10 pointer-events-none">
            <span class="text-sm font-medium tracking-widest">{{ lightboxIndex + 1 }} / {{ allImages.length }}</span>
            <div class="flex items-center gap-4 pointer-events-auto">
              <button @click.stop="zoomOut" class="hover:text-secondary transition-colors" aria-label="Dézoomer"><ZoomOut :size="24" /></button>
              <button @click.stop="zoomReset" class="text-xs font-medium hover:text-secondary transition-colors" aria-label="Taille originale">100%</button>
              <button @click.stop="zoomIn" class="hover:text-secondary transition-colors" aria-label="Zoomer"><ZoomIn :size="24" /></button>
              <div class="w-px h-6 bg-white/20 mx-2"></div>
              <button @click.stop="closeLightbox" class="hover:text-red-400 transition-colors" aria-label="Fermer"><X :size="28" /></button>
            </div>
          </div>
          
          <!-- Prev Button -->
          <button 
            v-if="allImages.length > 1"
            @click.stop="prevLightboxImage" 
            class="absolute left-4 md:left-8 w-12 h-12 rounded-full bg-white/10 text-white flex items-center justify-center hover:bg-white/20 hover:scale-105 transition-all z-10 hidden sm:flex"
            aria-label="Image précédente"
          >
            <ChevronLeft :size="28" />
          </button>

          <!-- Lightbox Image -->
          <div 
            class="relative w-full h-full flex items-center justify-center p-4 sm:p-12"
            @click.stop
          >
            <Transition name="gallery-fade" mode="out-in">
              <img
                :key="lightboxImage"
                :src="lightboxImage"
                :alt="product.nom"
                class="max-w-[95vw] max-h-[85vh] object-contain transition-transform duration-200"
                :style="{ transform: `scale(${lightboxZoom})` }"
                @error="onMainError"
              />
            </Transition>
          </div>

          <!-- Next Button -->
          <button 
            v-if="allImages.length > 1"
            @click.stop="nextLightboxImage" 
            class="absolute right-4 md:right-8 w-12 h-12 rounded-full bg-white/10 text-white flex items-center justify-center hover:bg-white/20 hover:scale-105 transition-all z-10 hidden sm:flex"
            aria-label="Image suivante"
          >
            <ChevronRight :size="28" />
          </button>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { ChevronLeft, ChevronRight, X, ZoomIn, ZoomOut } from '@lucide/vue'
import { formatDiscount } from '@/composables/useFormatPrice'
import productPlaceholder from '@/assets/images/product-placeholder.jpg'

const props = defineProps({
  product: { type: Object, required: true },
})

const placeholder = productPlaceholder
const currentIndex = ref(0)
const isHovering = ref(false)
const mousePos = ref({ x: 50, y: 50 })

// Lightbox state
const isLightboxOpen = ref(false)
const lightboxIndex = ref(0)
const lightboxZoom = ref(1)
let touchStartX = 0

// Construct allImages array matching API exactly
const allImages = computed(() => {
  const imgs = []
  
  if (props.product.image) {
    imgs.push({ src: props.product.image, alt: props.product.nom })
  }
  
  if (props.product.images?.length) {
    props.product.images.forEach((img) => {
      // Ensure we don't duplicate the main image if it somehow matches
      if (img.chemin !== props.product.image) {
        imgs.push({ src: img.chemin, alt: img.texte_alternatif || props.product.nom })
      }
    })
  }
  
  if (imgs.length === 0) {
    imgs.push({ src: productPlaceholder, alt: props.product.nom })
  }
  
  return imgs
})

const currentImage = computed(() => allImages.value[currentIndex.value]?.src || productPlaceholder)
const lightboxImage = computed(() => allImages.value[lightboxIndex.value]?.src || productPlaceholder)
const discountLabel = computed(() => props.product.remise > 0 ? formatDiscount(props.product.remise) : null)

// --- Hover Zoom Logic ---
const imageTransformStyle = computed(() => {
  if (isHovering.value && !isMobile()) {
    return {
      transformOrigin: `${mousePos.value.x}% ${mousePos.value.y}%`,
      transform: 'scale(1.6)'
    }
  }
  return {
    transformOrigin: '50% 50%',
    transform: 'scale(1)'
  }
})

function handleMouseMove(e) {
  if (!isHovering.value || isMobile()) return
  const rect = e.currentTarget.getBoundingClientRect()
  const x = ((e.clientX - rect.left) / rect.width) * 100
  const y = ((e.clientY - rect.top) / rect.height) * 100
  mousePos.value = { x, y }
}

function isMobile() {
  return window.matchMedia('(max-width: 768px)').matches || 'ontouchstart' in window
}

// --- Navigation Main ---
function prevImage() {
  currentIndex.value = currentIndex.value === 0 ? allImages.value.length - 1 : currentIndex.value - 1
}

function nextImage() {
  currentIndex.value = currentIndex.value === allImages.value.length - 1 ? 0 : currentIndex.value + 1
}

// --- Lightbox Logic ---
function openLightbox(index) {
  lightboxIndex.value = index
  lightboxZoom.value = 1
  isLightboxOpen.value = true
  document.body.style.overflow = 'hidden'
  window.addEventListener('keydown', handleKeydown)
}

function closeLightbox() {
  isLightboxOpen.value = false
  document.body.style.overflow = ''
  window.removeEventListener('keydown', handleKeydown)
}

function prevLightboxImage() {
  lightboxIndex.value = lightboxIndex.value === 0 ? allImages.value.length - 1 : lightboxIndex.value - 1
  lightboxZoom.value = 1 // reset zoom on change
}

function nextLightboxImage() {
  lightboxIndex.value = lightboxIndex.value === allImages.value.length - 1 ? 0 : lightboxIndex.value + 1
  lightboxZoom.value = 1
}

function zoomIn() {
  if (lightboxZoom.value < 3) lightboxZoom.value += 0.5
}

function zoomOut() {
  if (lightboxZoom.value > 0.5) lightboxZoom.value -= 0.5
}

function zoomReset() {
  lightboxZoom.value = 1
}

// --- Keyboard & Touch ---
function handleKeydown(e) {
  if (!isLightboxOpen.value) return
  if (e.key === 'Escape') closeLightbox()
  if (e.key === 'ArrowLeft') prevLightboxImage()
  if (e.key === 'ArrowRight') nextLightboxImage()
}

function handleTouchStart(e) {
  touchStartX = e.changedTouches[0].screenX
}

function handleTouchEnd(e) {
  const touchEndX = e.changedTouches[0].screenX
  const diff = touchEndX - touchStartX
  if (Math.abs(diff) > 50) { // minimum distance to trigger swipe
    if (diff > 0) {
      prevLightboxImage()
    } else {
      nextLightboxImage()
    }
  }
}

// Cleanup
onUnmounted(() => {
  document.body.style.overflow = ''
  window.removeEventListener('keydown', handleKeydown)
})

function onMainError(e) { e.target.src = productPlaceholder }
</script>

<style>
/* Transitions for changing images */
.gallery-fade-enter-active,
.gallery-fade-leave-active {
  transition: opacity 0.25s ease-out, transform 0.25s ease-out;
}

.gallery-fade-enter-from {
  opacity: 0;
  transform: scale(0.98);
}

.gallery-fade-leave-to {
  opacity: 0;
  transform: scale(1.02);
}

/* Lightbox overlay transitions */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
