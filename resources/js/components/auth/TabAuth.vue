<template>
    <v-card>
        <v-tabs v-model="tab" bg-color="primary" grow>
            <v-tab value="login">Login</v-tab>
            <v-tab value="register">Register</v-tab>
        </v-tabs>

        <v-card-text>
            <v-window v-model="tab">
                <v-window-item value="login">
                    <v-card flat>
                        <v-card-text>
                            <login-form @closeDialog="closeDialog" />
                        </v-card-text>
                    </v-card>
                </v-window-item>

                <v-window-item value="register">
                    <v-card flat>
                        <v-card-text>
                            <register-form @closeDialog="closeDialog" />
                        </v-card-text>
                    </v-card>
                </v-window-item>
            </v-window>
        </v-card-text>
    </v-card>
</template>

<script>
import LoginForm from './LoginForm.vue';
import RegisterForm from './RegisterForm.vue';

export default {
    components: { LoginForm, RegisterForm },
    name: "TabAuth",
    data() {
        return {
            tab: null,
        };
    },
    methods: {
        closeDialog() {
            this.$emit("closeDialog");
        },
    },
    emits: ["closeDialog"],
};
</script>
