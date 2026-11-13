<template>
    <component
        :is="isAdminDashboard ? 'AdminLayout' : 'CrewLayout'"
        active-page="accountSettings"
        title="Account settings"
        :navigation-items="navigationItems"
    >
        <AccountSettingsContent />
    </component>
</template>

<script>
import AdminLayout from '@/shared-components/layouts/AdminLayout.vue';
import CrewLayout from '@/shared-components/layouts/CrewLayout.vue';
import AccountSettingsContent from '@/shared-components/subcomponents/AccountSettings.vue';

export default {
    components: {
        AdminLayout,
        CrewLayout,
        AccountSettingsContent,
    },
    props: {
        isAdminDashboard: {
            type: Boolean,
            required: true
        },
        navigationItems: {
            type: Object,
            default: () => ({})
        }
    },
}
</script>
