<template>
  <footer class="bg-primary text-white mt-16">
    <!-- Main footer -->
    <div class="container-site py-14 lg:py-16">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-12">

        <!-- Brand -->
        <div class="lg:col-span-1">
          <ZinetLogo variant="white" class="h-10 w-auto mb-4" />
          <p class="text-white/60 text-sm leading-relaxed mb-5">
            Zinet Eddar vous propose une sélection de meubles et d'objets de décoration pour créer
            un intérieur élégant qui vous ressemble.
          </p>
          <!-- Social -->
          <div class="flex items-center gap-3">
            <a href="#" aria-label="Facebook" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center hover:bg-secondary transition-colors">
              <Share2 :size="16" />
            </a>
            <a href="#" aria-label="Instagram" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center hover:bg-secondary transition-colors">
              <AtSign :size="16" />
            </a>
            <a href="#" aria-label="Pinterest" class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center hover:bg-secondary transition-colors">
              <ExternalLink :size="16" />
            </a>
          </div>
        </div>

        <!-- Navigation -->
        <div>
          <h4 class="font-display text-base font-medium text-white mb-4 uppercase tracking-wider text-sm">Boutique</h4>
          <ul class="space-y-2.5">
            <li v-for="link in shopLinks" :key="link.label">
              <RouterLink :to="link.to" class="text-white/60 text-sm hover:text-secondary transition-colors">
                {{ link.label }}
              </RouterLink>
            </li>
          </ul>
        </div>

        <!-- Customer Service -->
        <div>
          <h4 class="font-display text-base font-medium text-white mb-4 uppercase tracking-wider text-sm">Service Client</h4>
          <ul class="space-y-2.5">
            <li v-for="link in serviceLinks" :key="link.label">
              <RouterLink :to="link.to" class="text-white/60 text-sm hover:text-secondary transition-colors">
                {{ link.label }}
              </RouterLink>
            </li>
          </ul>
        </div>

        <!-- Contact -->
        <div>
          <h4 class="font-display text-base font-medium text-white mb-4 uppercase tracking-wider text-sm">Contact</h4>
          <ul class="space-y-3">
            <li class="flex items-start gap-2.5 text-sm text-white/60">
              <MapPin :size="15" class="flex-shrink-0 mt-0.5 text-secondary" />
              <span>Tunisie</span>
            </li>
            <li class="flex items-start gap-2.5 text-sm text-white/60">
              <Phone :size="15" class="flex-shrink-0 mt-0.5 text-secondary" />
              <a href="tel:+21600000000" class="hover:text-secondary transition-colors">+216 00 000 000</a>
            </li>
            <li class="flex items-start gap-2.5 text-sm text-white/60">
              <Mail :size="15" class="flex-shrink-0 mt-0.5 text-secondary" />
              <a href="mailto:contact@zineteddar.tn" class="hover:text-secondary transition-colors">contact@zineteddar.tn</a>
            </li>
          </ul>

          <!-- Payment -->
          <div class="mt-5 p-3 bg-white/5 rounded-sm border border-white/10">
            <div class="flex items-center gap-2 text-xs text-white/70">
              <Banknote :size="15" class="text-secondary flex-shrink-0" />
              <span>Paiement en espèces à la livraison</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom bar -->
    <div class="border-t border-white/10">
      <div class="container-site py-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-white/40">
        <span>© {{ new Date().getFullYear() }} Zinet Eddar. Tous droits réservés.</span>
        <span class="italic font-display text-white/30">Le charme de votre intérieur</span>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { Share2, AtSign, ExternalLink, MapPin, Phone, Mail, Banknote } from '@lucide/vue'
import ZinetLogo from '@/components/common/ZinetLogo.vue'

const shopLinks = [
  { label: 'Boutique',          to: { name: 'shop' } },
  { label: 'Catégories',        to: { name: 'categories' } },
  { label: 'Nouveautés',        to: { name: 'new-products' } },
  { label: 'Promotions',        to: { name: 'promotions' } },
]

const serviceLinks = [
  { label: 'Mon compte',        to: { name: 'account' } },
  { label: 'Mes commandes',     to: { name: 'orders' } },
  { label: 'À propos',          to: { name: 'about' } },
  { label: 'Contact',           to: { name: 'contact' } },
]
</script>
