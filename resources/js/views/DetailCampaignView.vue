<template>
    <div>
        <v-card class="mx-auto" v-if="campaign.id" max-width="800">
            <v-img
                class="align-end text-white"
                height="400"
                :src="
                    campaign.image
                        ? campaign.image
                        : 'https://cdn.vuetifyjs.com/images/cards/docks.jpg'"
                cover
            >
                <v-card-title>{{ campaign.title }}</v-card-title>
            </v-img>

            <v-card-subtitle class="pt-4">
                <v-icon color="red">mdi-map-marker</v-icon>
                {{ campaign.address }}
            </v-card-subtitle>

            <v-card-text>
                <div>{{ campaign.description }}</div>
            </v-card-text>

            <v-card-text>
                <div class="text-h5">
                    Collected funds
                    Rp.{{ campaign.collected.toLocaleString("id-ID") }}
                </div>
                <div class="text-h5">
                    Required funds Rp.{{
                        campaign.required.toLocaleString("id-ID")
                    }}
                </div>
            </v-card-text>

            <v-card-actions>
                <v-btn
                    block
                    size="large"
                    @click="donate"
                    color="success"
                    :disabled="campaign.collected >= campaign.required"
                >
                    <v-icon>mdi-heart</v-icon>DONATE
                </v-btn>
            </v-card-actions>
        </v-card>
    </div>
</template>
<script>
import { useUserStore } from "@/stores/user";

export default {
    setup() {
        const useStore = useUserStore();
        return { useStore };
    },
    data() {
        return {
            campaign: {},
        };
    },
    created() {
        this.getCampaignDetail();
    },
    methods: {
        getCampaignDetail() {
            let { id } = this.$route.params;
            let url = "/api/campaign/" + id;
            axios
                .get(url)
                .then((response) => {
                    this.campaign = response.data.data;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        donate() {
            if (!this.useStore.isLogin) {
                alert("anda harus login terlebih dahulu");
            } else if (this.useStore.isNotVerification) {
                alert("anda belum verifikasi");
            } else {
                alert("donate");
            }
        },
    },
};
</script>
