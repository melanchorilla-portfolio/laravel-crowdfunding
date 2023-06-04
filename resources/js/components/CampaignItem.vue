<template>
    <div>
        <v-card :to="'/campaign/' + campaign.id">
            <v-img
                class="align-end text-white"
                height="200px"
                :src="
                    campaign.image
                        ? campaign.image
                        : 'https://cdn.vuetifyjs.com/images/cards/docks.jpg'
                "
                cover
            >
            </v-img>

            <v-card-title
                class="fill-height align-end"
                v-text="campaign.title"
            ></v-card-title>

            <v-card-actions>
                <v-progress-linear
                    v-model="progress"
                    height="7"
                ></v-progress-linear>
            </v-card-actions>

            <v-card-actions>
                <v-icon> mdi-cash </v-icon>
                <span>
                    Rp.{{ campaign.required.toLocaleString("id-ID") }}
                </span>
                <v-spacer></v-spacer>
                <span> {{ progress }} % </span>
            </v-card-actions>
        </v-card>
    </div>
</template>

<script>
export default {
    props: ["campaign"],
    data() {
        return {};
    },
    computed: {
        progress() {
            return (this.campaign.collected / this.campaign.required) * 100;
        },
    },
};
</script>
