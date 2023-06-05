<template>
    <v-card class="mx-auto" width="1028" prepend-icon="mdi-hand-heart">
        <template v-slot:title>Campaign</template>
        <v-card-text>
            <list-campaign :campaigns="campaigns" @reload="getCampaigns"/>
        </v-card-text>
    </v-card>
</template>

<script>
import { useUserStore } from "@/stores/user";
import ListCampaign from "@/components/campaign/ListCampaign.vue";

export default {
    setup() {
        const userStore = useUserStore();
        return { userStore };
    },
    components: { ListCampaign },
    data() {
        return {
            campaigns: []
        }
    },
    methods: {
        async getCampaigns() {
            try {
                const token = this.userStore.token;
                const config = {
                    headers: { Authorization: `Bearer ${token}`}
                }
                const data = await axios.get('/api/campaign', config);

                this.campaigns = data.data.data.campaigns;
            } catch (error) {
                console.log(error);
            }      
        }
    },
    created() {
        this.getCampaigns();
    }
}
</script>