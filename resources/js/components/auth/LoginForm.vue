<template>
    <v-sheet width="600" class="mx-auto">
        <div>
            <v-alert
                :type="alert.alertVariant"
                v-if="alert.showAlert"
                class="my-2"
            >
                {{ alert.alertMessage }}
            </v-alert>
        </div>
        <v-form ref="form" v-model="valid" lazy-validation>
            <v-text-field
                v-model="user.email"
                :rules="emailRules"
                label="E-mail"
                type="email"
                autofocus
                autocomplete="off"
                required
            ></v-text-field>

            <v-text-field
                v-model="user.password"
                :rules="passwordRules"
                label="Password"
                :append-icon="show ? 'mdi-eye' : 'mdi-eye-off'"
                :type="show ? 'text' : 'password'"
                @click:append="show = !show"
                required
                autocomplete="off"
            ></v-text-field>

            <div class="d-flex flex-column">
                <v-btn color="success" class="mt-4" block @click="loginSubmit">
                    Log in
                </v-btn>

                <v-btn color="error" class="mt-4" block @click="reset">
                    Reset
                </v-btn>
            </div>
        </v-form>
    </v-sheet>
</template>

<script>
import { useUserStore } from '@/stores/user';

export default {
    setup() {
        const userStore = useUserStore();
        return { userStore }
    },
    data() {
        return {
            valid: true,
            alert: {
                showAlert: false,
                alertVariant: "",
                alertMessage: null,
            },
            user: {
                email: "",
                password: "",
            },
            emailRules: [
                (v) => !!v || "E-mail is required",
                (v) => /.+@.+\..+/.test(v) || "E-mail must be valid",
            ],
            passwordRules: [
                (v) => !!v || "Password is required",
                (v) =>
                    (v && v.length >= 8) ||
                    "Password must be more than 8 characters",
            ],
            show: false,
        };
    },
    methods: {
        async loginSubmit() {
            const { valid } = await this.$refs.form.validate();

            if (valid) {
                this.alert.showAlert = true;
                this.alert.alertVariant = 'warning';
                this.alert.alertMessage = 'Please wait!';


                try {
                    const authLogin = await axios.post('/api/auth/login', this.user);

                    this.alert.alertVariant = 'success';
                    this.alert.alertMessage = authLogin.data.response_message;
                    
                    await this.userStore.setToken(authLogin.data.token);

                    this.$router.push('/');
                    this.$emit("closeDialog");
                } catch (error) {
                    this.alert.alertVariant = 'error';
                    this.alert.alertMessage = error;
                }
            }
        },
        reset() {
            this.$refs.form.reset();
        },
    },
    emits: ["closeDialog"],
};
</script>
