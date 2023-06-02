<template>
    <v-card class="mx-auto" max-width="720" outlined>
        <div>
            <v-alert
                v-if="alert.showAlert"
                :type="alert.alertVariant"
                class="my-2"
            >
                {{ alert.alertMessage }}
            </v-alert>
        </div>

        <v-card-item>
            <div>
                <div class="text-h4 mb-1 text-center my-3">
                    Account Verification
                </div>
                <div class="text-caption text-center my-3"></div>
            </div>
        </v-card-item>

        <v-card-text>
            <v-form ref="form" v-model="valid" lazy-validation>
                <v-text-field
                    v-model="otp"
                    :rules="otpRules"
                    label="OTP Code"
                    type="number"
                    required
                ></v-text-field>

                <div class="d-flex flex-column">
                    <v-btn
                        color="success"
                        class="mt-4"
                        block
                        @click="accountVerification"
                    >
                        Verification
                    </v-btn>

                    <v-btn color="error" class="mt-4" block @click="reset">
                        Reset
                    </v-btn>
                </div>
            </v-form>
        </v-card-text>

        <v-card-text>
            <div class="text-caption text-center">
                Regenerate OTP
                <a href="#" @click="generateOTPCode()">click here!</a>
            </div>
        </v-card-text>
    </v-card>
</template>

<script>
import { mapActions, mapWritableState } from 'pinia';
import { useUserStore } from '@/stores/user';

export default {
    data() {
        return {
            valid: true,
            alert: {
                showAlert: false,
                alertVariant: '',
                alertMessage: null
            },
            otp: null,
            otpRules: [
                (v) => !!v || "OTP Code is required",
                (v) => (v && v.length == 6) || "Password must be 6 numbers",
            ],
        }
    },
    computed: {
        ...mapWritableState(useUserStore, ['user'])
    },
    methods: {
        ...mapActions(useUserStore, ['verificationSuccess']),
        async accountVerification() {
            const { valid } = await this.$refs.form.validate();

            if (valid) {
                this.alert.showAlert = true;
                this.alert.alertVariant = "warning";
                this.alert.alertMessage = "Please wait!";

                try {
                    const verification = await axios.post('/api/auth/verification', { otp: this.otp });

                    this.alert.alertVariant = "success";
                    this.alert.alertMessage = verification.data.response_message;

                    this.$router.push('/');
                    
                    this.verificationSuccess();
                } catch (error) {
                    this.alert.alertVariant = "error";
                    this.alert.alertMessage = error;
                }
            }
        },
        async generateOTPCode() {
            this.alert.showAlert = true;
            this.alert.alertVariant = "warning";
            this.alert.alertMessage = "Please wait!";

            try {
                const generate = await axios.post(
                    "/api/auth/regenerate-otp-code",
                    {
                        email: this.user.email,
                    }
                );

                this.alert.showAlert = true;
                this.alert.alertVariant = "success";
                this.alert.alertMessage = generate.data.response_message;
            } catch (error) {
                this.alert.alertVariant = "error";
                this.alert.alertMessage = error;
            }
        },
        reset() {
            this.$refs.form.reset();
        }
    }

};
</script>
