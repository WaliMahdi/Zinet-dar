<template>
  <div class="admin-layout min-h-screen bg-background flex flex-col lg:flex-row relative">
    
    <!-- Sidebar Component -->
    <AdminSidebar 
      :is-open="isSidebarOpen" 
      @close="closeSidebar" 
    />

    <!-- Overlay for mobile/tablet when sidebar is open -->
    <div 
      v-if="isSidebarOpen" 
      class="sidebar-overlay fixed inset-0 bg-black/50 z-30 lg:hidden transition-opacity"
      @click="closeSidebar"
    ></div>

    <!-- Main Content Area -->
    <div class="admin-main flex-1 flex flex-col min-w-0 min-h-screen overflow-hidden relative">
      <!-- Header Component -->
      <AdminHeader 
        @toggle-sidebar="toggleSidebar" 
      />

      <!-- Scrollable Page Content -->
      <main class="admin-content flex-1 overflow-y-auto p-4 md:p-8 bg-background">
        <RouterView v-slot="{ Component }">
          <Transition name="fade" mode="out-in">
            <component :is="Component" />
          </Transition>
        </RouterView>
      </main>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import AdminSidebar from '@/components/admin/AdminSidebar.vue'
import AdminHeader from '@/components/admin/AdminHeader.vue'

const isSidebarOpen = ref(false)

const openSidebar = () => {
  isSidebarOpen.value = true
  document.body.style.overflow = 'hidden' // Prevent background scrolling on mobile
}

const closeSidebar = () => {
  isSidebarOpen.value = false
  document.body.style.overflow = '' // Restore scrolling
}

const toggleSidebar = () => {
  if (isSidebarOpen.value) {
    closeSidebar()
  } else {
    openSidebar()
  }
}

const handleEscape = (e) => {
  if (e.key === 'Escape' && isSidebarOpen.value) {
    closeSidebar()
  }
}

const handleResize = () => {
  // If window becomes desktop size, ensure scrolling is re-enabled just in case
  if (window.innerWidth >= 1024) {
    document.body.style.overflow = ''
  } else if (isSidebarOpen.value) {
    // If it becomes mobile size and sidebar is open, lock scroll
    document.body.style.overflow = 'hidden'
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleEscape)
  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleEscape)
  window.removeEventListener('resize', handleResize)
  document.body.style.overflow = '' // Clean up
})
</script>

<style scoped>
/* Optional: fade transition for overlay */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
