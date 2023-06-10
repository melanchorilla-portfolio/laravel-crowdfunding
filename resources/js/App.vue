<template>
    <v-app>
        <!-- v-app-bar -->
        <v-app-bar color="blue-lighten-1" density="compact">
            <template v-slot:prepend>
                <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
            </template>

            <v-app-bar-title>Crowdfunding App</v-app-bar-title>

            <v-spacer />

           
        </v-app-bar>
        <!-- end v-app-bar -->

        <!-- v-navigation-drawer -->
        <v-navigation-drawer app color="blue-lighten-1" v-model="drawer">
            <v-list>
                <v-list-item
                    v-if="userStore.isLogin"
                    :prepend-avatar="userStore.user.photo_profile ?? 'https://randomuser.me/api/portraits/men/78.jpg'"
                    :title="userStore.user.name"
                    :to="{ path: '/user' }"
                    nav
                >
                </v-list-item>

                <div class="pa-2" v-if="!userStore.isLogin">
                    <v-btn
                        block
                        color="primary"
                        class="mb-1"
                        @click="dialog = true"
                    >
                        <v-icon left>mdi-lock</v-icon>Login | Register</v-btn
                    >
                </div>
                <v-divider />

                <v-list density="compact" nav>
                    <v-list-item
                        prepend-icon="mdi-view-dashboard"
                        title="Home"
                        value="home"
                        :to="{ path: '/' }"
                    ></v-list-item>
                    <v-list-item
                        v-if="userStore.isAdmin"
                        prepend-icon="mdi-forum"
                        title="Campaign"
                        value="campaign"
                        :to="{ path: '/campaign' }"
                    ></v-list-item>
                    <v-list-item
                        v-if="userStore.isNotVerification"
                        prepend-icon="mdi-account-alert"
                        title="Verification"
                        value="verification"
                        :to="{ path: '/verification' }"
                    ></v-list-item>
                </v-list>
            </v-list>

            <template v-slot:append v-if="userStore.isLogin">
                <div class="pa-2">
                    <v-btn block color="" @click.prevent="logout">
                        <v-icon left>mdi-logout</v-icon>
                        Logout</v-btn
                    >
                </div>
            </template>
        </v-navigation-drawer>
        <!-- end v-navigation-drawer -->

        <!-- v-main -->
        <v-main>
            <v-container grid-list-md>
                <v-dialog
                    transition="dialog-bottom-transition"
                    width="auto"
                    v-model="dialog"
                >
                    <tab-auth @closeDialog="closeDialog"></tab-auth>
                </v-dialog>
                <router-view />
            </v-container>
        </v-main>
        <!-- end v-main -->
    </v-app>
</template>

<script>
import { useUserStore } from './stores/user';
import TabAuth from './components/auth/TabAuth.vue';

export default {
    setup() {
        const userStore = useUserStore();
        return { userStore }
    },
    components: { TabAuth },
    data() {
        return {
            drawer: false,
            dialog: false,
        };
    },
    methods: {
        closeDialog() {
            this.dialog = false;
        },
        async logout() {
            await this.userStore.removeAuth();
            this.$router.push('/');
        }
    },
};
</script>
