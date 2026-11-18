<template>
    <BaseLayout :active-page="activePage" :title="title" :navigation-items="navigationItems" :is-admin-dashboard="false">
        <template #navigation-icon="{ item }">
            <component :is="navigationIcons[item.name]" class="relative shrink-0" />
        </template>
        <slot></slot>
    </BaseLayout>
</template>

<script>
import BaseLayout from './BaseLayout.vue';
import CalendarIcon from '@/shared-components/icons/CalendarIcon.vue';

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
    setup() {
        const navigationIcons = {
            shifts: CalendarIcon,
            findShifts: CalendarIcon,
        };

        return {
            navigationIcons,
        };
    },
}
</script>
