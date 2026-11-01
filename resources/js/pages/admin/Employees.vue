<template>
    <AdminLayout :active-page="activePage" title="Employees" :navigation-items="navigationItems">
        Employees
    </AdminLayout>
</template>

<script>
import AdminLayout from '@/shared-components/layouts/AdminLayout.vue';

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