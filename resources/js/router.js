import { createRouter, createWebHistory } from 'vue-router';
import { useUserStore } from './stores/user';

import Home from './views/HomeView.vue';
import Campaign from './views/CampaignView.vue';
import DetailCampaign from './views/DetailCampaignView.vue';
import Verification from './views/VerificationView.vue';

const routes = [
    {
        path: '/',
        name: 'home',
        component: Home
    },
    {
        path: '/campaign',
        name: 'campaign',
        component: Campaign,
        meta: {
            requiresAdmin: true
        }
    },
    {
        path: '/campaign/:id',
        name: 'campaign-detail',
        component: DetailCampaign,
    },
    {
        path: '/verification',
        name: 'verification',
        component: Verification,
        meta: {
            requiresVerification: true
        }
    },
    {
        path: '/:catchAll(.*)',
        redirect: '/'
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});


router.beforeEach((to, from) => {
    const auth = useUserStore();

    if (to.meta.requiresVerification) {
        if (!auth.isNotVerification) {
            alert('Unauthorized');
            return { path: '/' }
        }
    }

    if (to.meta.requiresAdmin) {
        if (!auth.isAdmin) {
            alert('Unauthorized');
            return { path: '/' }
        }
    }
});


export default router;