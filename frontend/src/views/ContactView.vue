<template>
  <div class="min-h-screen bg-background">
    <!-- Breadcrumb -->
    <div class="bg-surface py-4 border-b border-border">
      <div class="container-site">
        <Breadcrumb />
      </div>
    </div>
    <div class="bg-surface border-b border-border py-10">
      <div class="container-site">
        <h1 class="section-title">Contact</h1>
      </div>
    </div>

    <div class="container-site py-14 max-w-4xl">
      <div class="grid md:grid-cols-2 gap-10">
        <!-- Form -->
        <div class="bg-surface rounded-lg border border-border/50 p-7">
          <h2 class="font-display text-xl font-medium text-primary mb-5">Envoyez-nous un message</h2>
          <form @submit.prevent="handleSubmit" class="space-y-4" id="contact-form">
            <div>
              <label class="label" for="contact-name">Nom</label>
              <input id="contact-name" v-model="form.name" type="text" class="input" placeholder="Votre nom" required />
            </div>
            <div>
              <label class="label" for="contact-email">Email</label>
              <input id="contact-email" v-model="form.email" type="email" class="input" placeholder="votre@email.com" required />
            </div>
            <div>
              <label class="label" for="contact-msg">Message</label>
              <textarea id="contact-msg" v-model="form.message" rows="5" class="input resize-none" placeholder="Votre message…" required />
            </div>
            <Transition name="fade">
              <div v-if="sent" class="bg-green-50 border border-green-200 rounded-sm p-3 text-sm text-green-700 flex items-center gap-2">
                <CheckCircle :size="15" /> Merci ! Nous vous répondrons dans les plus brefs délais.
              </div>
            </Transition>
            <button type="submit" :disabled="sent" class="btn-primary w-full" id="contact-submit-btn">
              <Send :size="16" /> Envoyer le message
            </button>
          </form>
        </div>

        <!-- Info -->
        <div class="space-y-6">
          <div>
            <p class="section-eyebrow">Nos coordonnées</p>
            <h2 class="font-display text-2xl font-medium text-primary mb-5">Nous sommes à votre écoute</h2>
          </div>

          <div v-for="info in contactInfo" :key="info.label" class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-secondary/10 flex items-center justify-center flex-shrink-0">
              <component :is="info.icon" :size="18" class="text-secondary" />
            </div>
            <div>
              <p class="text-xs text-muted uppercase tracking-wider mb-1">{{ info.label }}</p>
              <p class="text-sm font-medium text-primary">{{ info.value }}</p>
            </div>
          </div>

          <div class="bg-background rounded-lg p-5 border border-border mt-6">
            <p class="text-sm font-medium text-primary mb-1">Horaires</p>
            <p class="text-muted text-sm">Lundi – Samedi : 9h – 19h</p>
            <p class="text-muted text-sm">Dimanche : 10h – 16h</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { CheckCircle, Send, Phone, Mail, MapPin } from '@lucide/vue'
import Breadcrumb from '@/components/common/Breadcrumb.vue'

const sent = ref(false)
const form = reactive({ name: '', email: '', message: '' })

function handleSubmit() {
  // TODO: Connect to email/contact API when available
  sent.value = true
}

const contactInfo = [
  { icon: Phone, label: 'Téléphone',    value: '+216 00 000 000' },
  { icon: Mail,  label: 'Email',        value: 'contact@zineteddar.tn' },
  { icon: MapPin,label: 'Localisation', value: 'Tunisie' },
]
</script>
