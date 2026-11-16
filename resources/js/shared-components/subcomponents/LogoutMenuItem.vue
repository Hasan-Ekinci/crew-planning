<template>
    <button
        type="button"
        aria-haspopup="dialog"
        :disabled="logoutHttp.processing"
        @click="openConfirmation = true"
        class="flex min-h-12 w-full cursor-pointer items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-3 py-3 text-left text-sm font-semibold text-red-700 shadow-[0_0_14px_rgba(239,68,68,0.15)] transition-[background-color,border-color,box-shadow] duration-200 hover:border-red-300 hover:bg-red-100 hover:shadow-[0_0_18px_rgba(239,68,68,0.25)] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 disabled:cursor-not-allowed disabled:opacity-50 motion-reduce:transition-none"
    >
        <LogoutIcon class="shrink-0" />
        <span>Log out</span>
    </button>

    <ConfirmationModal :open-modal="openConfirmation" @close-modal="closeConfirmation">
        <template #title>Log out?</template>
        <template #body>
            <p>Are you sure you want to log out of your account?</p>
            <p v-if="errorMessage" role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700">{{ errorMessage }}</p>
        </template>
        <template #buttons>
            <button
                type="button"
                :disabled="logoutHttp.processing"
                @click="closeConfirmation"
                class="min-h-11 w-full cursor-pointer rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-600 disabled:cursor-not-allowed disabled:opacity-50 sm:mr-auto sm:w-auto"
            >
                Cancel
            </button>
            <button
                type="button"
                :disabled="logoutHttp.processing"
                @click="logout"
                class="min-h-11 w-full cursor-pointer rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 disabled:cursor-not-allowed disabled:opacity-50 sm:w-auto"
            >
                {{ logoutHttp.processing ? 'Logging out…' : 'Log out' }}
            </button>
        </template>
    </ConfirmationModal>
</template>

<script>
import { ref } from 'vue';
import { useHttp } from '@inertiajs/vue3';
import LogoutIcon from '@/shared-components/icons/LogoutIcon.vue';
import ConfirmationModal from '@/shared-components/subcomponents/ConfirmationModal.vue';

export default {
    components: {
        LogoutIcon,
        ConfirmationModal,
    },
    setup() {
        const openConfirmation = ref(false);
        const errorMessage = ref('');
        const logoutHttp = useHttp();

        function closeConfirmation() {
            if (logoutHttp.processing) {
                return;
            }

            openConfirmation.value = false;
            errorMessage.value = '';
        }

        async function logout() {
            if (!openConfirmation.value || logoutHttp.processing) {
                return;
            }

            errorMessage.value = '';

            try {
                await logoutHttp.post('/logout');
                window.location.assign('/');
            } catch {
                errorMessage.value = 'Unable to log out. Please try again.';
            }
        }

        return {
            openConfirmation,
            errorMessage,
            logoutHttp,
            closeConfirmation,
            logout,
        };
    },
}
</script>
