<template>
    <v-card class="mx-auto" max-width="720">
        <v-img
            class="align-end text-white"
            height="400"
            :src="
                campaignItem.image ??
                'https://cdn.vuetifyjs.com/images/cards/docks.jpg'
            "
            cover
        >
            <v-card-title>{{ campaignItem.title }}</v-card-title>
        </v-img>

        <v-card-subtitle class="pt-4">
            <v-icon color="red">mdi-map-marker</v-icon>
            {{ campaignItem.address }}
        </v-card-subtitle>

        <v-card-text>
            <div>{{ campaignItem.description }}</div>
        </v-card-text>

        <v-card-actions>
            <v-btn color="info">required {{ campaignItem.required.toLocaleString("id-ID") }} </v-btn>

            <v-btn color="success">
                {{ campaignItem.collected.toLocaleString("id-ID") }}
            </v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
export default {
    props: {
        campaignItem: Object,
    },
};
</script>
