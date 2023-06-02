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
                v-model="user.name"
                :rules="nameRules"
                label="Name"
                type="text"
                autofocus
                autocomplete="off"
                required
            ></v-text-field>

            <v-text-field
                v-model="user.email"
                :rules="emailRules"
                label="E-mail"
                type="email"
                autocomplete="off"
                required
            ></v-text-field>

            <v-text-field
                v-model="user.password"
                :rules="passwordRules"
                label="Password"
                :append-icon="show1 ? 'mdi-eye' : 'mdi-eye-off'"
                :type="show1 ? 'text' : 'password'"
                @click:append="show1 = !show1"
                autocomplete="off"
                required
            ></v-text-field>

            <v-text-field
                v-model="user.password_confirmation"
                :rules="passwordRules"
                label="Confirm Password"
                :append-icon="show2 ? 'mdi-eye' : 'mdi-eye-off'"
                :type="show2 ? 'text' : 'password'"
                @click:append="show2 = !show2"
                autocomplete="off"
                required
            ></v-text-field>

            <div class="d-flex flex-column">
                <v-btn
                    color="success"
                    class="mt-4"
                    block
                    @click="registerSubmit"
                >
                    Register
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
                name: "",
                email: "",
                password: "",
                password_confirmation: "",
            },
            nameRules: [(v) => !!v || "Name is required"],
            emailRules: [
                (v) => !!v || "E-mail is required",
                (v) => /.+@.+\..+/.test(v) || "E-mail must be valid",
            ],
            passwordRules: [
                (v) => !!v || "Password is required",
                (v) =>
                    (v && v.length >= 8) ||
                    "Password must be more than 8 characters",
                (confirmation) =>
                    confirmation === this.user.password ||
                    "Password doesn't match",
            ],
            show1: false,
            show2: false,
        };
    },
    emits: ["closeDialog"],
    methods: {
        async registerSubmit() {
            const { valid } = await this.$refs.form.validate();

            if (valid) {
                this.alert.showAlert = true;
                this.alert.alertVariant = "warning";
                this.alert.alertMessage = "Please wait!";

                try {
                    const authRegister = await axios.post('/api/auth/register', this.user);
                    this.alert.alertVariant = "success";
                    this.alert.alertMessage = authRegister.data.response_message;

                    const dataUser = authRegister.data.data.user;
                    const token = authRegister.data.token;
                    
                    await this.userStore.setVerification(dataUser, token);

                    this.$router.push("/");
                    this.$emit("closeDialog");
                } catch (error) {
                    this.alert.alertVariant = "error";
                    this.alert.alertMessage = error;
                }
            }
        },
        reset() {
            this.$refs.form.reset();
        },
    },
};
</script>
