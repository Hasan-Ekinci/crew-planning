<template>
    <AdminLayout :active-page="activePage" title="Shifts" :navigation-items="navigationItems">
        SHIFTS PAGE
    </AdminLayout>
</template>

<script>
import AdminLayout from '@/shared-components/AdminLayout.vue';

export default {
    components: {
        AdminLayout
    },
    props: {
        activePage: {
            type: String,
            required: true
        },
        navigationItems: {
            type: Object,
            default: () => {},
        },
    },
    setup(props) {
    }
}
</script>