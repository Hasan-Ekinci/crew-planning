<template>
    <AdminLayout :active-page="activePage" :title="employee ? 'Edit user' : 'Add user'" :navigation-items="navigationItems">
        <p v-if="errorMessage" role="alert" class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm leading-relaxed text-red-700">{{ errorMessage }}</p>

        <form class="w-full space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm" @submit.prevent="submitForm">
            <div class="grid gap-6 lg:grid-cols-2">
                <div class="min-w-0 space-y-5">
                    <div>
                        <label for="employee-name" class="mb-1.5 block text-sm font-medium text-slate-700">Full name</label>
                        <input
                            id="employee-name"
                            v-model="form.name"
                            name="name"
                            type="text"
                            autocomplete="name"
                            required
                            maxlength="255"
                            :disabled="submitHttp.processing"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 disabled:bg-slate-50"
                        />
                    </div>

                    <div>
                        <label for="employee-email" class="mb-1.5 block text-sm font-medium text-slate-700">Email address</label>
                        <input
                            id="employee-email"
                            v-model="form.email"
                            name="email"
                            type="email"
                            autocomplete="email"
                            required
                            maxlength="255"
                            :disabled="submitHttp.processing"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 disabled:bg-slate-50"
                        />
                    </div>
                </div>

                <div class="min-w-0">
                    <label id="employee-roles-label" for="employee-roles" class="mb-1.5 block text-sm font-medium text-slate-700">Roles</label>
                    <Multiselect
                        id="employee-roles"
                        v-model="form.roles"
                        class="employee-roles"
                        mode="tags"
                        :options="roles"
                        value-prop="id"
                        label="name"
                        track-by="name"
                        :searchable="true"
                        :close-on-select="false"
                        :disabled="submitHttp.processing"
                        :aria="{ 'aria-labelledby': 'employee-roles-label' }"
                        placeholder="Select roles"
                    />
                    <p class="mt-2 text-xs text-slate-500">Select Admin on its own, or choose one or more other roles.</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-5">
                <Link
                    href="/admin/employees"
                    view-transition
                    class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                >
                    Cancel
                </Link>
                <button
                    type="submit"
                    :disabled="submitHttp.processing"
                    class="min-h-11 cursor-pointer rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition-colors duration-200 hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:cursor-not-allowed disabled:opacity-50 motion-reduce:transition-none"
                >
                    {{ submitHttp.processing ? 'Saving…' : 'Save user' }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>

<script>
import { reactive, ref } from 'vue';
import { Link, router, useHttp } from '@inertiajs/vue3';
import Multiselect from '@vueform/multiselect';
import AdminLayout from '@/shared-components/layouts/AdminLayout.vue';

export default {
    components: {
        AdminLayout,
        Link,
        Multiselect,
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
        employee: {
            type: Object,
            default: null
        },
        roles: {
            type: Array,
            default: () => []
        }
    },
    setup(props) {
        const form = reactive({
            id: props.employee?.id ?? null,
            name: props.employee?.name ?? '',
            email: props.employee?.email ?? '',
            roles: (props.employee?.roles ?? []).map(role => role.id),
        });
        const submitHttp = useHttp();
        const errorMessage = ref('');

        function submitForm() {
            errorMessage.value = '';

            submitHttp.transform(() => ({ ...form })).post('/admin/employees/save', {
                onSuccess: (response) => {
                    if (!response.success) {
                        errorMessage.value = response.message;
                        return;
                    }

                    router.visit('/admin/employees', { viewTransition: true });
                },
                onError: (errors) => {
                    errorMessage.value = Object.values(errors).flat().join(' ');
                },
            });
        }

        return {
            form,
            submitHttp,
            errorMessage,
            submitForm,
        };
    },
}
</script>

<style src="@vueform/multiselect/themes/default.css"></style>

<style scoped>
.employee-roles {
    --ms-font-size: 0.875rem;
    --ms-radius: 0.5rem;
    --ms-py: 0.625rem;
    --ms-tag-bg: #2563eb;
    --ms-ring-color: #dbeafe;
}
</style>
