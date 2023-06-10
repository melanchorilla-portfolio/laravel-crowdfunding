<template>
    <v-card class="mx-auto" width="1028" prepend-icon="mdi-account">
        <template v-slot:title>User</template>
        <div>
            <v-alert
                :type="alert.alertVariant"
                v-if="alert.showAlert"
                class="my-2"
            >
                {{ alert.alertMessage }}
            </v-alert>
        </div>

        <v-card-text>
            <v-form ref="form">
                <v-container>
                    <v-row>
                        <v-col cols="12" md="12">
                            <v-text-field
                                v-model="name"
                                :rules="nameRules"
                                label="Full name"
                                required
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-text-field
                                v-model="photo_profile"
                                label="Profile Photo"
                                type="file"
                                name="photo_profile"
                                ref="photo_profile"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col md="2">
                            <v-btn
                                color="success"
                                class="mt-4"
                                block
                                @click="submit"
                            >
                                Submit
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-container>
            </v-form>
        </v-card-text>
    </v-card>
</template>

<script>
import { useUserStore } from "@/stores/user";

export default {
    setup() {
        const userStore = useUserStore();
        return { userStore };
    },
    data() {
        return {
            alert: {
                showAlert: false,
                alertVariant: "",
                alertMessage: null,
            },
            valid: true,
            name: this.userStore.user.name,
            photo_profile: null,
            nameRules: [(v) => !!v || "Name is required"],
        };
    },
    methods: {
        async submit() {
            const { valid } = await this.$refs.form.validate();

            if (valid) {
                this.alert.showAlert = true;
                this.alert.alertVariant = "warning";
                this.alert.alertMessage = "Please wait";

                try {
                    const token = this.userStore.token;
                    let photo_profile = this.$refs.photo_profile.files[0];

                    let formData = new FormData();
                    formData.append("name", this.name);

                    if (photo_profile != undefined) {
                        formData.append("photo_profile", photo_profile);
                    }

                    const config = {
                        headers: { Authorization: `Bearer ${token}` },
                    };

                    const dataResponse = await axios.post(
                        "/api/update-profile",
                        formData,
                        config
                    );

                    this.alert.showAlert = true;
                    this.alert.alertVariant = "success";
                    this.alert.alertMessage =
                        dataResponse.data.response_message;

                    await this.userStore.setToken(token);
                } catch (error) {
                    console.log(error);
                }
            }
        },
    },
};
</script>
