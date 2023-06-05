<template>
    <!-- alert -->
    <v-alert :type="alert.alertVariant" v-if="alert.showAlert" class="my-2">
        {{ alert.alertMessage }}
    </v-alert>
    <!-- end alert -->

    <div class="d-flex justify-end">
        <v-btn
            color="primary"
            size="x-large"
            icon
            class="md-1"
            @click="getCreate"
        >
            <v-icon size="x-large">mdi-plus-circle</v-icon>
            <v-tooltip activator="parent" location="top">Add</v-tooltip>
        </v-btn>
    </div>

    <v-table>
        <thead>
            <tr>
                <th class="text-left">Title</th>
                <th class="text-left">Required</th>
                <th class="text-left">Collected</th>
                <th class="text-left">Action</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="campaign in campaigns" :key="campaign.title">
                <td>{{ campaign.title }}</td>
                <td>{{ campaign.required.toLocaleString("id-ID") }}</td>
                <td>{{ campaign.collected.toLocaleString("id-ID") }}</td>
                <td>
                    <v-btn
                        color="info"
                        class="md-1 mx-3"
                        @click="getDetail(campaign)"
                    >
                        <v-tooltip activator="parent" location="top"
                            >Detail</v-tooltip
                        >
                        <v-icon left>mdi-information-outline</v-icon>
                    </v-btn>
                    <v-btn
                        color="warning"
                        class="md-1 mx-3"
                        @click="getEdit(campaign)"
                    >
                        <v-tooltip activator="parent" location="top"
                            >Edit</v-tooltip
                        >
                        <v-icon left>mdi-pencil</v-icon>
                    </v-btn>
                    <v-btn
                        color="error"
                        class="md-1 mx-3"
                        @click="getDelete(campaign)"
                    >
                        <v-tooltip activator="parent" location="top"
                            >Delete</v-tooltip
                        >
                        <v-icon left>mdi-delete</v-icon>
                    </v-btn>
                </td>
            </tr>
        </tbody>
    </v-table>

    <!-- dialog detail -->
    <v-dialog
        transition="dialog-bottom-transition"
        max-width="720"
        scrollable
        v-model="dialogDetail"
    >
        <detail-campaign :campaignItem="showData" scrollable />
    </v-dialog>
    <!-- end dialog detail -->

    <!-- dialog create -->
    <v-dialog
        v-model="dialogCreate"
        scrollable
        max-width="720"
        transition="dialog-transition"
    >
        <action-campaign
            statusButton="create"
            @closed="dialogCreate = false"
            @reload="reload"
        >
        </action-campaign>
    </v-dialog>
    <!-- end dialog create -->

    <!-- dialog edit -->
    <v-dialog
        transition="dialog-bottom-transition"
        max-width="600"
        scrollable
        v-model="dialogEdit"
    >
        <action-campaign
            :campaignItem="showData"
            statusButton="update"
            @closed="dialogEdit = false"
            @reload="reload"
        />
    </v-dialog>
    <!-- end dialog edit -->

    <!-- dialog delete -->
    <v-dialog
        persistent
        transition="dialog-transition"
        max-width="720"
        scrollable
        v-model="dialogDelete"
    >
        <v-card>
            <v-card-title class="text-h4 text-center text-danger">
                Delete Data
            </v-card-title>
            <v-card-text class="text-center">
                <div>Are you sure want to delete {{ showData.title }} ?</div>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    color="info"
                    variant="text"
                    @click="dialogDelete = false"
                >
                    No
                </v-btn>
                <v-btn color="error" variant="text" @click="deleteData">
                    Yes
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
    <!-- end dialog delete -->
</template>

<script>
import { useUserStore } from "@/stores/user";
import DetailCampaign from "./DetailCampaign.vue";
import ActionCampaign from "./ActionCampaign.vue";

export default {
    setup() {
        const userStore = useUserStore();
        return { userStore };
    },
    props: {
        campaigns: Array,
    },
    emits: ["reload"],
    components: { DetailCampaign, ActionCampaign },
    data() {
        return {
            alert: {
                showAlert: false,
                alertVariant: "",
                alertMessage: null,
            },
            dialogDetail: false,
            dialogCreate: false,
            dialogEdit: false,
            dialogDelete: false,
            showData: null,
        };
    },
    methods: {
        getDetail(campaign) {
            this.dialogDetail = true;
            this.showData = campaign;
        },
        getCreate() {
            this.dialogCreate = true;
        },
        getEdit(campaign) {
            this.dialogEdit = true;
            this.showData = campaign;
        },
        getDelete(campaign) {
            this.dialogDelete = true;
            this.showData = campaign;
        },
        async deleteData() {
            this.alert.showAlert = true;
            this.alert.alertVariant = "warning";
            this.alert.alertMessage = "please wait";
            try {
                const token = this.userStore.token;
                const config = {
                    method: "post",
                    params: { _method: "Delete" },
                    url: `api/campaign/${this.showData.id}`,
                    headers: { Authorization: `Bearer ${token}` },
                };
                const dataResponse = await axios(config);
                this.alert.alertVariant = "success";
                this.alert.alertMessage = dataResponse.data.response_message;

                this.dialogDelete = false;
                this.reload();

                setTimeout(() => {
                    this.alert.showAlert = false
                }, 5000);
                
            } catch (error) {
                this.alert.alertVariant = "error";
                this.alert.alertMessage = error;
            }
        },
        reload() {
            this.$emit("reload");
        },
    },
};
</script>
