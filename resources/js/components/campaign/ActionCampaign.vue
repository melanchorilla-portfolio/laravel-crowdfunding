<template>
        <div>
            <v-alert :type="alert.alertVariant" v-if="alert.showAlert" class="my-2">
                {{ alert.alertMessage }}
            </v-alert>
        </div>
        <v-card color="white">
            <v-card-title>
                {{
                    statusButton == "create"
                        ? "Create campaign"
                        : "Update campaign"
                }}
            </v-card-title>

            <v-divider />

            <v-card-text>
                <v-form ref="form" v-model="valid" lazy-validation>
                    <v-text-field
                        v-model="campaign.title"
                        :rules="titleRules"
                        label="Title"
                        type="Title"
                        required
                    ></v-text-field>

                    <v-text-field
                        v-model="campaign.required"
                        :rules="requiredRules"
                        label="Required"
                        type="number"
                    ></v-text-field>
                    <v-textarea
                        v-model="campaign.description"
                        :rules="descriptionRules"
                        label="Description"
                    >
                    </v-textarea>
                    <v-textarea
                        v-model="campaign.address"
                        :rules="addressRules"
                        label="Address"
                    >
                    </v-textarea>
                    <v-text-field
                        v-model="campaign.image"
                        label="Image"
                        type="file"
                        name="image"
                        ref="image"
                    ></v-text-field>

                    <v-btn
                        color="primary"
                        block
                        class="mr-4"
                        @click="submitForm"
                    >
                        {{ statusButton }}
                    </v-btn>
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
    emits: ["closed", "reload"],
    props: {
        statusButton: {
            type: String,
            default: "create",
        },
        campaignItem: {
            type: Object,
            default: null,
        },
    },
    created() {
        if (this.campaignItem) {
            this.campaign = {
                title: this.campaignItem.title,
                required: this.campaignItem.required,
                description: this.campaignItem.description,
                address: this.campaignItem.address,
                collected: this.campaignItem.collected,
            };
        }
    },
    data() {
        return {
            alert: {
                showAlert: false,
                alertVariant: "",
                alertMessage: null,
            },
            campaign: {
                title: "",
                required: "",
                address: "",
                description: "",
                collected: 0,
            },
            valid: true,
            titleRules: [(v) => !!v || "Title is required"],
            requiredRules: [(v) => !!v || "Required is required"],
            addressRules: [(v) => !!v || "Address is required"],
            descriptionRules: [(v) => !!v || "Description is required"],
        };
    },
    methods: {
        async submitForm() {
            const { valid } = await this.$refs.form.validate();

            if (valid) {
                this.alert.showAlert = true;
                this.alert.alertVariant = "warning";
                this.alert.alertMessage = "Please wait";

                try {
                    const token = this.userStore.token;
                    let image = this.$refs.image.files[0];
                    let formData = new FormData();

                    formData.append("title", this.campaign.title);
                    formData.append("description", this.campaign.description);
                    formData.append("address", this.campaign.address);
                    formData.append("required", this.campaign.required);
                    formData.append("collected", this.campaign.collected);

                    if (image != undefined) {
                        formData.append("image", image);
                    }

                    if (this.statusButton === "create") {
                        const config = {
                            headers: { Authorization: `Bearer ${token}` },
                        };

                        const dataResponse = await axios.post(
                            "/api/campaign",
                            formData,
                            config
                        );

                        this.alert.showAlert = true;
                        this.alert.alertVariant = "success";
                        this.alert.alertMessage =
                            dataResponse.data.response_message;
                    } else {
                        const config = {
                            headers: { Authorization: `Bearer ${token}` },
                            params: { _method: "PUT" },
                        };

                        const dataResponse = await axios.post(
                            `/api/campaign/${this.campaignItem.id}`,
                            formData,
                            config
                        );

                        this.alert.showAlert = true;
                        this.alert.alertVariant = "success";
                        this.alert.alertMessage = dataResponse.data.response_message;
                    }

                    this.close();
                } catch (error) {
                    this.alert.alertVariant = "error";
                    this.alert.alertMessage = error;
                }
            }
        },
        close() {
            this.$emit("reload");
            this.$emit("closed");
        },
    },
};
</script>
