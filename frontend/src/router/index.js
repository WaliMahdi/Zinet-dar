import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

const routes = [
  // ── Public routes (DefaultLayout) ─────────────────────────────────────
  {
    path: '/',
    component: () => import('@/layouts/DefaultLayout.vue'),
    children: [
      {
        path: '',
        name: 'home',
        component: () => import('@/views/HomeView.vue'),
        meta: { title: 'Accueil' },
      },
      {
        path: 'boutique',
        name: 'shop',
        component: () => import('@/views/ShopView.vue'),
        meta: { title: 'Boutique' },
      },
      {
        path: 'nouveautes',
        name: 'new-products',
        component: () => import('@/views/ShopView.vue'),
        meta: { title: 'Nouveautés' },
      },
      {
        path: 'promotions',
        name: 'promotions',
        component: () => import('@/views/ShopView.vue'),
        meta: { title: 'Promotions' },
      },
      {
        path: 'boutique/:id',
        name: 'product-detail',
        component: () => import('@/views/ProductDetailView.vue'),
        meta: { title: 'Produit' },
      },
      {
        path: 'categories',
        name: 'categories',
        component: () => import('@/views/CategoriesView.vue'),
        meta: { title: 'Catégories' },
      },
      {
        path: 'a-propos',
        name: 'about',
        component: () => import('@/views/AboutView.vue'),
        meta: { title: 'À propos' },
      },
      {
        path: 'contact',
        name: 'contact',
        component: () => import('@/views/ContactView.vue'),
        meta: { title: 'Contact' },
      },
      // Protected routes (auth required)
      {
        path: 'panier',
        name: 'cart',
        component: () => import('@/views/CartView.vue'),
        meta: { title: 'Mon panier', requiresAuth: true },
      },
      {
        path: 'commander',
        name: 'checkout',
        component: () => import('@/views/CheckoutView.vue'),
        meta: { title: 'Commander', requiresAuth: true },
      },
      {
        path: 'commander/succes',
        name: 'checkout-success',
        component: () => import('@/views/CheckoutSuccessView.vue'),
        meta: { title: 'Commande confirmée', requiresAuth: true },
      },
      {
        path: 'mon-compte',
        name: 'account',
        component: () => import('@/views/AccountView.vue'),
        meta: { title: 'Mon compte', requiresAuth: true },
      },
      {
        path: 'mes-commandes',
        name: 'orders',
        component: () => import('@/views/OrdersView.vue'),
        meta: { title: 'Mes commandes', requiresAuth: true },
      },
      {
        path: 'mes-commandes/:id',
        name: 'order-detail',
        component: () => import('@/views/OrderDetailView.vue'),
        meta: { title: 'Détail commande', requiresAuth: true },
      },
    ],
  },

  // ── Auth routes (AuthLayout) ───────────────────────────────────────────
  {
    path: '/',
    component: () => import('@/layouts/AuthLayout.vue'),
    children: [
      {
        path: 'connexion',
        name: 'login',
        component: () => import('@/views/LoginView.vue'),
        meta: { title: 'Connexion', guestOnly: true },
      },
      {
        path: 'inscription',
        name: 'register',
        component: () => import('@/views/RegisterView.vue'),
        meta: { title: 'Créer un compte', guestOnly: true },
      },
      {
        path: 'verifier-email',
        name: 'verify-email',
        component: () => import('@/views/VerifyEmailView.vue'),
        meta: { title: 'Vérifier votre email' },
      },
    ],
  },

  // ── Admin routes (AdminLayout) ─────────────────────────────────────────
  {
    path: '/admin',
    component: () => import('@/layouts/AdminLayout.vue'),
    children: [
      {
        path: '',
        redirect: { name: 'admin-dashboard' },
      },
      {
        path: 'dashboard',
        name: 'admin-dashboard',
        component: () => import('@/views/admin/AdminDashboardView.vue'),
        meta: { title: 'Tableau de bord', requiresAdmin: true },
      },
      {
        path: 'commandes',
        name: 'admin-commandes',
        component: () => import('@/views/admin/AdminCommandesView.vue'),
        meta: { title: 'Commandes', requiresAdmin: true },
      },
      {
        path: 'commandes/:id',
        name: 'admin-commande-detail',
        component: () => import('@/views/admin/AdminCommandeDetailView.vue'),
        meta: { title: 'Détail commande', requiresAdmin: true },
      },
      {
        path: 'produits',
        name: 'admin-produits',
        component: () => import('@/views/admin/AdminProduitsView.vue'),
        meta: { title: 'Produits', requiresAdmin: true },
      },
      {
        path: 'produits/nouveau',
        name: 'admin-produit-nouveau',
        component: () => import('@/views/admin/AdminProduitFormView.vue'),
        meta: { title: 'Nouveau produit', requiresAdmin: true },
      },
      {
        path: 'produits/:id/modifier',
        name: 'admin-produit-modifier',
        component: () => import('@/views/admin/AdminProduitFormView.vue'),
        meta: { title: 'Modifier produit', requiresAdmin: true },
      },
      {
        path: 'categories',
        name: 'admin-categories',
        component: () => import('@/views/admin/AdminCategoriesView.vue'),
        meta: { title: 'Catégories', requiresAdmin: true },
      },
      {
        path: 'clients',
        name: 'admin-clients',
        component: () => import('@/views/admin/AdminClientsView.vue'),
        meta: { title: 'Clients', requiresAdmin: true },
      },
      {
        path: 'clients/:id',
        name: 'admin-client-detail',
        component: () => import('@/views/admin/AdminClientDetailView.vue'),
        meta: { title: 'Détail client', requiresAdmin: true },
      },
      {
        path: 'stock',
        name: 'admin-stock',
        component: () => import('@/views/admin/AdminStockView.vue'),
        meta: { title: 'Gestion du stock', requiresAdmin: true },
      },
      {
        path: 'bannieres',
        name: 'admin-bannieres',
        component: () => import('@/views/admin/AdminBannieresView.vue'),
        meta: { title: 'Bannières', requiresAdmin: true },
      },
      {
        path: 'settings',
        name: 'admin-settings',
        component: () => import('@/views/admin/AdminSettingsView.vue'),
        meta: { title: 'Paramètres', requiresAdmin: true },
      },
    ],
  },

  // ── 404 ───────────────────────────────────────────────────────────────
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('@/views/NotFoundView.vue'),
    meta: { title: 'Page introuvable' },
  },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) return savedPosition
    return { top: 0, behavior: 'smooth' }
  },
})

// ── Navigation guards ──────────────────────────────────────────────────────
router.beforeEach((to, from, next) => {
  const auth = useAuthStore()

  // Set document title
  const title = to.meta.title
  document.title = title ? `${title} — Zinet Eddar` : 'Zinet Eddar'

  // Requires auth but not logged in → redirect to login
  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return next({ name: 'login', query: { redirect: to.fullPath } })
  }

  // Requires admin but not admin → redirect to home or login
  if (to.meta.requiresAdmin) {
    if (!auth.isAuthenticated) {
      return next({ name: 'login', query: { redirect: to.fullPath } })
    }
    if (!auth.isAdmin) {
      return next({ name: 'home' }) // Client trying to access admin
    }
  }

  // Guest-only routes when already logged in → redirect to admin-dashboard or home
  if (to.meta.guestOnly && auth.isAuthenticated) {
    if (auth.isAdmin) {
      return next({ name: 'admin-dashboard' })
    }
    return next({ name: 'home' })
  }

  next()
})

export default router
