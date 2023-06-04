<template>
    <v-app>
        <v-container grid-list-xs class="bg-white">
            <v-row no-gutter>
                <v-col v-for="campaign in campaigns" :key="campaign.id" cols="12" sm="6" class="mt-2">
                    <campaign-item :campaign="campaign" />
                </v-col>
            </v-row>
        </v-container>
        <v-pagination v-if="pagination" v-model="page" :length="lengthPage" @click="getCampaigns"></v-pagination>
    </v-app>
</template>

<script>
import CampaignItem from '@/components/CampaignItem.vue';


export default {
    components: { CampaignItem },
    data() {
        return {
            page: 1,
            lengthPage: 0,
            campaigns: [],
            pagination: true
        }
    },
    created() {
        this.getCampaigns();
    },
    methods: {
        getCampaigns() {
            let url = '/api/campaign/get-all?page=' + this.page;

            axios.get(url)
                .then((response) => {
                    let { data } = response.data;
                    this.campaigns = data.campaigns.data;
                    this.page = data.campaigns.current_page;
                    this.lengthPage = data.campaigns.last_page;
                }).catch((error) => {
                    console.log(error);
                });

            if (this.lengthPage == 1) {
                this.pagination = false;
            }
        }
    }
}
</script>