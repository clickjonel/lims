import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/authStore';

import LoginLayout from '@/layouts/LoginLayout.vue';
import PageLayout from '@/layouts/PageLayout.vue';

import Dashboard from '@/views/Dashboard/Dashboard.vue';
import Deliveries from '@/views/Delivery/Deliveries.vue';
import CreateDeliveryForm from '@/views/Delivery/form/CreateDeliveryForm.vue';
import UpdateDeliveryForm from '@/views/Delivery/form/UpdateDeliveryForm.vue';
import IARForm from '@/views/Delivery/form/IARForm.vue';
import NOD from '@/views/Delivery/form/NOD.vue';
import UserDeliveries from '@/views/Delivery/UserDeliveries.vue';
import SectionDeliveries from '@/views/Delivery/SectionDeliveries.vue';
import StockCards from '@/views/StockCard/StockCards.vue';
import CreateStockCard from '@/views/StockCard/form/CreateStockCard.vue';
import UpdateStockCard from '@/views/StockCard/form/UpdateStockCard.vue';
import ViewStockCard from '@/views/StockCard/form/ViewStockCard.vue';
import SectionStockCards from '@/views/StockCard/SectionStockCards.vue';
import Properties from '@/views/Property/Properties.vue';
import CreateProperty from '@/views/Property/form/CreateProperty.vue';
import TransferProperty from '@/views/Property/form/TransferProperty.vue';
import CreateInventoryReport from '@/views/Property/form/CreateInventoryReport.vue';
import Sticker from '@/views/Property/form/Sticker.vue';
import UserProperties from '@/views/Property/UserProperties.vue';
import CreateItemStockCard from '@/views/Delivery/form/CreateItemStockCard.vue';
import DrugsAndMeds from '@/views/StockCard/Categories/DrugsAndMeds.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'Login',
      component: LoginLayout,
      meta: { requiresAuth: false }
    },
    {
      path: '/admin',
      name: 'Admin',
      component: PageLayout,
      redirect: '/admin/dashboard',
      children: [
        {
          path: 'dashboard',
          name: 'Dashboard',
          component: Dashboard,
          meta: { requiresAuth: true }
        },
        {
          path: 'deliveries',
          name: 'Deliveries',
          component: Deliveries,
          meta: { requiresAuth: true }
        },
        {
          path: 'deliveries/user',
          name: 'User Deliveries',
          component: UserDeliveries,
          meta: { requiresAuth: true }
        },
        {
          path: 'deliveries/section',
          name: 'Section Deliveries',
          component: SectionDeliveries,
          meta: { requiresAuth: true }
        },

        {
          path: 'stock_cards',
          name: 'Stock Cards',
          component: StockCards,
          meta: { requiresAuth: true }
        },
        {
          path: 'stock_cards/section',
          name: 'Section Stock Cards',
          component: SectionStockCards,
          meta: { requiresAuth: true }
        },
        {
          path: 'properties',
          name: 'Properties',
          component: Properties,
          meta: { requiresAuth: true }
        },
        {
          path: 'properties/user',
          name: 'User Properties',
          component: UserProperties,
          meta: { requiresAuth: true }
        },
        {
          path: 'stock_card/drugs_and_medicines',
          name: 'Drugs and Medicines Stock Cards',
          component: DrugsAndMeds,
          meta: { requiresAuth: true }
        },

      ]
    },
    {
      path: '/delivery/create',
      name: 'Create Delivery',
      component: CreateDeliveryForm,
      meta: { requiresAuth: true }
    },
    {
      path: '/delivery/items/stock/:id',
      name: 'Stock Delivery Items',
      component: CreateItemStockCard,
      meta: { requiresAuth: true }
    },
    {
      path: '/stock_card/create',
      name: 'Create Stock Card',
      component: CreateStockCard,
      meta: { requiresAuth: true }
    },
    {
      path: '/property/create',
      name: 'Create Property',
      component: CreateProperty,
      meta: { requiresAuth: true }
    },
    {
      path: '/delivery/update/:id',
      name: 'Update Delivery',
      component: UpdateDeliveryForm,
      meta: { requiresAuth: true }
    },
    {
      path: '/stock_card/update/:id',
      name: 'Update Stock Card',
      component: UpdateStockCard,
      meta: { requiresAuth: true }
    },
    {
      path: '/delivery/iar/:id',
      name: 'IAR Delivery',
      component: IARForm,
      meta: { requiresAuth: true }
    },
    {
      path: '/stock_card/view/:id',
      name: 'View Stock Card',
      component: ViewStockCard,
      meta: { requiresAuth: true }
    },
    {
      path: '/delivery/nod',
      name: 'Notice of Delivery',
      component: NOD,
      meta: { requiresAuth: true }
    },
    {
      path: '/property/transfer',
      name: 'Transfer Property',
      component: TransferProperty,
      meta: { requiresAuth: true }
    },
    {
      path: '/property/report',
      name: 'Inventory Report',
      component: CreateInventoryReport,
      meta: { requiresAuth: true }
    },
    {
      path: '/property/sticker',
      name: 'Sticker',
      component: Sticker,
      meta: { requiresAuth: true }
    }

  ]
})

let isInitialized = false

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore()
    
    if (!isInitialized) {
        await authStore.initAuth()
        isInitialized = true
    }
    
    const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
    
    if (requiresAuth && !authStore.isAuthenticated) {
      next({ name: 'Login' })
    }

    else if (!requiresAuth && authStore.isAuthenticated && to.name === 'Login') {
      next({ name: 'Dashboard' })
    } 
    
    else {
      next()
    }
})

export default router