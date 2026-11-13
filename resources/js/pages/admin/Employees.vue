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
                        <th scope="row" class="px-5 py-4 font-medium">
                            <Link
                                :href="`/admin/employees/edit/${employee.id}`"
                                view-transition
                                class="rounded text-blue-700 hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                            >
                                {{ employee.name }}
                            </Link>
                        </th>
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
import { Link } from '@inertiajs/vue3';
import AdminLayout from '@/shared-components/layouts/AdminLayout.vue';

export default {
    components: {
        AdminLayout,
        Link,
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
