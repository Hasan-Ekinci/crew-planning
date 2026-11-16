<template>
    <AdminLayout :active-page="activePage" title="Roles" :navigation-items="navigationItems">
        <Table
            :columns="columns"
            :rows="roles"
            caption="Roles and the number of users assigned to each role"
            empty-message="No roles found."
        >
            <template #cell-name="{ row }">
                <span v-if="row.name === 'Admin'">{{ row.name }}</span>
                <button
                    v-else
                    type="button"
                    aria-haspopup="dialog"
                    :aria-label="`Edit role ${row.name}`"
                    @click="selectedRole = row"
                    class="cursor-pointer rounded text-left text-blue-700 hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                >
                    {{ row.name }}
                </button>
            </template>
            <template #cell-users_count="{ row }">
                <button
                    type="button"
                    aria-haspopup="dialog"
                    :aria-label="`View users assigned to ${row.name}`"
                    @click="selectedUsersRole = row"
                    class="cursor-pointer rounded text-blue-700 hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                >
                    {{ row.users_count }}
                </button>
            </template>
        </Table>

        <RoleModal
            v-if="selectedRole"
            :open-modal="true"
            :role="selectedRole"
            @close-modal="selectedRole = null"
            @saved="onRoleSaved"
        />
        <RoleUsersModal
            v-if="selectedUsersRole"
            :open-modal="true"
            :role="selectedUsersRole"
            @close-modal="selectedUsersRole = null"
        />
    </AdminLayout>
</template>

<script>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminLayout from '@/shared-components/layouts/AdminLayout.vue';
import Table from '@/shared-components/subcomponents/Table.vue';
import RoleModal from './roles/RoleModal.vue';
import RoleUsersModal from './roles/RoleUsersModal.vue';

export default {
    components: {
        AdminLayout,
        Table,
        RoleModal,
        RoleUsersModal,
    },
    props: {
        activePage: {
            type: String,
            required: true
        },
        navigationItems: {
            type: Object,
            default: () => ({}),
        },
        roles: {
            type: Array,
            default: () => [],
        },
    },
    setup() {
        const selectedRole = ref(null);
        const selectedUsersRole = ref(null);
        const columns = [
            { key: 'name', label: 'Role name' },
            { key: 'users_count', label: 'Assigned users' },
        ];

        function onRoleSaved() {
            selectedRole.value = null;
            router.reload({ only: ['roles'] });
        }

        return {
            columns,
            selectedRole,
            selectedUsersRole,
            onRoleSaved,
        };
    }
}
</script>
