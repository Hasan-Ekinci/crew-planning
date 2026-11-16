<template>
    <AdminLayout :active-page="activePage" title="Employees" :navigation-items="navigationItems">
        <div class="mb-4 flex justify-end">
            <Link
                href="/admin/employees/create"
                view-transition
                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors duration-200 hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 motion-reduce:transition-none"
            >
                <span aria-hidden="true" class="text-lg leading-none">+</span>
                Add user
            </Link>
        </div>
        <Table
            :columns="columns"
            :rows="employees"
            caption="Employees with their full names, email addresses, and roles"
            empty-message="No employees found."
        >
            <template #cell-name="{ row }">
                <Link
                    :href="`/admin/employees/edit/${row.id}`"
                    view-transition
                    class="rounded text-blue-700 hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                >
                    {{ row.name }}
                </Link>
            </template>
            <template #cell-roles="{ row }">
                {{ row.roles.map(role => role.name).join(', ') || '—' }}
            </template>
        </Table>
    </AdminLayout>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/shared-components/layouts/AdminLayout.vue';
import Table from '@/shared-components/subcomponents/Table.vue';

export default {
    components: {
        AdminLayout,
        Link,
        Table,
    },
    props: {
        activePage: {
            type: String,
            required: true
        },
        navigationItems: {
            type: Object,
            default: () => ({})
        },
        employees: {
            type: Array,
            default: () => []
        }
    },
    setup() {
        const columns = [
            { key: 'name', label: 'Full name' },
            { key: 'email', label: 'Email address' },
            { key: 'roles', label: 'Roles' },
        ];

        return {
            columns,
        };
    }
}
</script>
