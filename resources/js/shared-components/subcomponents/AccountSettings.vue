<template>
    <section aria-label="Account settings" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <form class="space-y-6" @submit.prevent="submitForm">
            <p v-if="errorMessage" role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm leading-relaxed text-red-700">{{ errorMessage }}</p>
            <p v-if="successMessage" role="status" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm leading-relaxed text-emerald-800">{{ successMessage }}</p>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="account-name" class="mb-1.5 block text-sm font-medium text-slate-700">Full name</label>
                    <input
                        id="account-name"
                        v-model="form.name"
                        name="name"
                        type="text"
                        autocomplete="name"
                        required
                        maxlength="255"
                        :disabled="submitHttp.processing"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 disabled:bg-slate-50"
                        :class="isAdminDashboard ? 'focus:border-blue-500 focus:ring-blue-100' : 'focus:border-emerald-500 focus:ring-emerald-100'"
                    />
                </div>

                <div>
                    <label for="account-email" class="mb-1.5 block text-sm font-medium text-slate-700">Email address</label>
                    <input
                        id="account-email"
                        v-model="form.email"
                        name="email"
                        type="email"
                        autocomplete="email"
                        required
                        maxlength="255"
                        :disabled="submitHttp.processing"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 disabled:bg-slate-50"
                        :class="isAdminDashboard ? 'focus:border-blue-500 focus:ring-blue-100' : 'focus:border-emerald-500 focus:ring-emerald-100'"
                    />
                </div>
            </div>

            <div class="flex justify-end border-t border-slate-100 pt-5">
                <button
                    type="submit"
                    :disabled="submitHttp.processing"
                    class="min-h-11 cursor-pointer rounded-lg px-5 py-2.5 text-sm font-semibold text-white transition-colors duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 disabled:cursor-not-allowed disabled:opacity-50 motion-reduce:transition-none"
                    :class="isAdminDashboard ? 'bg-blue-600 hover:bg-blue-700 focus-visible:outline-blue-600' : 'bg-emerald-600 hover:bg-emerald-700 focus-visible:outline-emerald-600'"
                >
                    {{ submitHttp.processing ? 'Saving…' : 'Save changes' }}
                </button>
            </div>
        </form>
    </section>
</template>

<script>
import { reactive, ref } from 'vue';
import { useHttp } from '@inertiajs/vue3';

export default {
    props: {
        account: {
            type: Object,
            required: true
        },
        isAdminDashboard: {
            type: Boolean,
            required: true
        }
    },
    setup(props) {
        const form = reactive({
            name: props.account.name,
            email: props.account.email,
        });
        const submitHttp = useHttp();
        const errorMessage = ref('');
        const successMessage = ref('');

        function submitForm() {
            if (submitHttp.processing) {
                return;
            }

            errorMessage.value = '';
            successMessage.value = '';

            submitHttp.transform(() => ({ ...form })).post('/account-settings/save', {
                onSuccess: (response) => {
                    if (!response.success) {
                        errorMessage.value = response.message;
                        return;
                    }

                    successMessage.value = 'Account settings saved.';
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
            successMessage,
            submitForm,
        };
    },
}
</script>
