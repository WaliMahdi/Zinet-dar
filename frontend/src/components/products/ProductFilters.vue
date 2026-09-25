<template>
  <aside class="space-y-6">
    <!-- Search -->
    <div>
      <label class="label">Recherche</label>
      <div class="relative">
        <input
          :value="localQ"
          @input="onSearchInput"
          type="search"
          placeholder="Nom, référence, marque…"
          class="input pr-9"
          id="filter-search"
        />
        <Search :size="16" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted pointer-events-none" />
      </div>
    </div>

    <!-- Catégories -->
    <div v-if="categories.length">
      <h3 class="font-display font-medium text-lg mb-3">Catégories</h3>
      <div class="space-y-1">
        <!-- Toutes les catégories -->
        <button
          @click="update('categorie_id', null)"
          class="flex items-center gap-2 py-2 w-full text-left transition-colors group"
          :class="!filters.categorie_id ? 'text-primary font-medium' : 'text-muted hover:text-primary'"
        >
          <ChevronRight 
            :size="16" 
            class="transition-transform duration-200" 
            :class="{ 'rotate-90 text-primary': !filters.categorie_id }" 
          />
          <span class="text-sm">Tous les produits</span>
        </button>

        <!-- Liste des catégories -->
        <button
          v-for="cat in categories"
          :key="cat.id"
          @click="update('categorie_id', filters.categorie_id === cat.id ? null : cat.id)"
          class="flex items-center gap-2 py-2 w-full text-left transition-colors group"
          :class="filters.categorie_id === cat.id ? 'text-primary font-medium' : 'text-muted hover:text-primary'"
        >
          <ChevronRight 
            :size="16" 
            class="transition-transform duration-200" 
            :class="{ 'rotate-90 text-primary': filters.categorie_id === cat.id }" 
          />
          <span class="text-sm">{{ cat.nom }}</span>
        </button>
      </div>
    </div>

    <!-- Price range -->
    <div>
      <label class="label">Prix (TND)</label>
      <div class="flex gap-2 items-center">
        <input
          :value="filters.prix_min"
          @input="update('prix_min', $event.target.value || null)"
          type="number" min="0" placeholder="Min"
          class="input text-sm"
          id="filter-prix-min"
        />
        <span class="text-muted text-sm flex-shrink-0">—</span>
        <input
          :value="filters.prix_max"
          @input="update('prix_max', $event.target.value || null)"
          type="number" min="0" placeholder="Max"
          class="input text-sm"
          id="filter-prix-max"
        />
      </div>
    </div>

    <!-- Checkboxes -->
    <div class="space-y-2.5">
      <label class="flex items-center gap-2.5 cursor-pointer group">
        <input
          type="checkbox"
          :checked="filters.nouveau"
          @change="update('nouveau', $event.target.checked || null)"
          class="w-4 h-4 accent-secondary rounded"
          id="filter-nouveau"
        />
        <span class="text-sm text-muted group-hover:text-primary">Nouveautés uniquement</span>
      </label>
      <label class="flex items-center gap-2.5 cursor-pointer group">
        <input
          type="checkbox"
          :checked="filters.vedette"
          @change="update('vedette', $event.target.checked || null)"
          class="w-4 h-4 accent-secondary rounded"
          id="filter-vedette"
        />
        <span class="text-sm text-muted group-hover:text-primary">Produits vedettes</span>
      </label>
    </div>

    <!-- Sort -->
    <div>
      <label class="label" for="filter-sort">Trier par</label>
      <select
        :value="`${filters.sort}:${filters.order}`"
        @change="handleSortChange($event.target.value)"
        class="input text-sm"
        id="filter-sort"
      >
        <option value="created_at:desc">Plus récents</option>
        <option value="created_at:asc">Plus anciens</option>
        <option value="prix_apres_remise:asc">Prix croissant</option>
        <option value="prix_apres_remise:desc">Prix décroissant</option>
        <option value="nom:asc">Nom A–Z</option>
        <option value="nom:desc">Nom Z–A</option>
      </select>
    </div>

    <!-- Reset -->
    <button
      v-if="hasActiveFilters"
      @click="$emit('reset')"
      class="btn-ghost btn-sm w-full flex items-center gap-1 text-red-500 hover:bg-red-50"
    >
      <X :size="14" /> Réinitialiser les filtres
    </button>
  </aside>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { Search, X, ChevronRight } from '@lucide/vue'
import { debounce } from '@/utils/helpers'

const props = defineProps({
  filters:    { type: Object, required: true },
  categories: { type: Array,  default: () => [] },
})
const emit = defineEmits(['update:filters', 'reset'])

const localQ = ref(props.filters.q || '')

watch(() => props.filters.q, (newQ) => {
  if (newQ !== localQ.value) {
    localQ.value = newQ || ''
  }
})

const debouncedUpdateQ = debounce((val) => {
  update('q', val || null)
}, 350)

function onSearchInput(e) {
  localQ.value = e.target.value
  debouncedUpdateQ(e.target.value)
}

function update(key, value) {
  emit('update:filters', { ...props.filters, [key]: value })
}

function handleSortChange(value) {
  const [sort, order] = value.split(':')
  emit('update:filters', { ...props.filters, sort, order })
}

const hasActiveFilters = computed(() => {
  const f = props.filters
  return f.q || f.categorie_id || f.prix_min || f.prix_max || f.nouveau || f.vedette
})
</script>
