<template>
    <AdminLayout :active-page="activePage" title="Employees" :navigation-items="navigationItems">
        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="min-w-full text-left text-sm">
                <caption class="sr-only">Employees with their full names, email addresses, and roles</caption>
                <thead class="border-b border-slate-200 bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th scope="col" class="px-5 py-4">Full name</th>
                        <th scope="col" class="px-5 py-4">Email address</th>
                        <th scope="col" class="px-5 py-4">Roles</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr
                        v-for="employee in employees"
                        :key="employee.id"
                        class="transition-colors duration-200 hover:bg-slate-50 motion-reduce:transition-none"
                    >
                        <th scope="row" class="px-5 py-4 font-medium text-slate-900">{{ employee.name }}</th>
                        <td class="px-5 py-4 text-slate-600">{{ employee.email }}</td>
                        <td class="px-5 py-4 text-slate-600">{{ employee.roles.map(role => role.name).join(', ') || '—' }}</td>
                    </tr>
                    <tr v-if="employees.length === 0">
                        <td colspan="3" class="px-5 py-10 text-center text-slate-500">No employees found.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>

<script>
import AdminLayout from '@/shared-components/layouts/AdminLayout.vue';

export default {
    components: {
        AdminLayout,
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
}
</script>
