<template>
    <v-sheet width="600" class="mx-auto">
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
export default {
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
                this.$emit('closeDialog');
            }
        },
        reset() {
            this.$refs.form.reset();
        },
    },
    emits: ["closeDialog"],
};
</script>
