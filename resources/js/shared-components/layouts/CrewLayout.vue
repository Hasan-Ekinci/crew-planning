<template>
    <BaseLayout :active-page="activePage" :title="title" :navigation-items="navigationItems" :is-admin-dashboard="false">
        <slot></slot>
    </BaseLayout>
</template>

<script>
import BaseLayout from './BaseLayout.vue';

export default {
    components: {
        BaseLayout
    },
    props: {
        activePage: {
            type: String,
            required: true
        },
        title: {
            type: String,
            required: true,
        },
        navigationItems: {
            type: Object,
            default: () => {},
        },
    },
}
</script>
