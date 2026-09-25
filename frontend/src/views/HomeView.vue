<template>
  <div>
    <HeroSection />
    <CategorySection />
    <NewProductsSection />
    <BannerSection section="accueil" />
    <FeaturedCollection />
    <AdvantagesSection />
    <BestSellersSection />
    <TestimonialsSection />
    <NewsletterSection />
  </div>
</template>

<script setup>
import HeroSection         from '@/components/home/HeroSection.vue'
import CategorySection     from '@/components/home/CategorySection.vue'
import NewProductsSection  from '@/components/home/NewProductsSection.vue'
import BannerSection       from '@/components/common/BannerSection.vue'
import FeaturedCollection  from '@/components/home/FeaturedCollection.vue'
import AdvantagesSection   from '@/components/home/AdvantagesSection.vue'
import BestSellersSection  from '@/components/home/BestSellersSection.vue'
import TestimonialsSection from '@/components/home/TestimonialsSection.vue'
import NewsletterSection   from '@/components/home/NewsletterSection.vue'
</script>
