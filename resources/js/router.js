import { createRouter, createWebHistory } from 'vue-router';

import Home from './views/HomeView.vue';
import Campaign from './views/CampaignView.vue';
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
        component: Campaign
    },
    {
        path: '/verification',
        name: 'verification',
        component: Verification
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});


export default router;