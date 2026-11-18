<template>
    <ConfirmationModal :open-modal="openModal" @close-modal="closeModal">
        <template #title>Accept shift?</template>
        <template #body>
            <p class="break-words">Do you want to accept the shift <strong class="font-semibold text-slate-900">{{ shiftTitle }}</strong>?</p>
            <p v-if="errorMessage" role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700">{{ errorMessage }}</p>
        </template>
        <template #buttons>
            <button
                type="button"
                :disabled="acceptHttp.processing"
                @click="closeModal"
                class="min-h-11 w-full cursor-pointer rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-600 disabled:cursor-not-allowed disabled:opacity-50 sm:mr-auto sm:w-auto"
            >
                Cancel
            </button>
            <button
                type="button"
                :disabled="acceptHttp.processing"
                @click="acceptShift"
                class="min-h-11 w-full cursor-pointer rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 disabled:cursor-not-allowed disabled:opacity-50 sm:w-auto"
            >
                {{ acceptHttp.processing ? 'Accepting…' : 'Accept shift' }}
            </button>
        </template>
    </ConfirmationModal>
</template>

<script>
import { ref } from 'vue';
import { useHttp } from '@inertiajs/vue3';
import ConfirmationModal from '@/shared-components/subcomponents/ConfirmationModal.vue';

export default {
    components: {
        ConfirmationModal,
    },
    props: {
        openModal: {
            type: Boolean,
            default: false
        },
        shiftId: {
            type: Number,
            required: true
        },
        shiftTitle: {
            type: String,
            default: ''
        }
    },
    emits: ['closeModal', 'accepted'],
    setup(props, { emit }) {
        const acceptHttp = useHttp();
        const errorMessage = ref('');

        function closeModal() {
            if (acceptHttp.processing) {
                return;
            }

            errorMessage.value = '';
            emit('closeModal');
        }

        function acceptShift() {
            if (acceptHttp.processing) {
                return;
            }

            errorMessage.value = '';

            acceptHttp.transform(() => ({ id: props.shiftId })).post('/crew/accept-shift', {
                onSuccess: (response) => {
                    if (!response.success) {
                        errorMessage.value = response.message;
                        return;
                    }

                    emit('accepted');
                },
                onError: (errors) => {
                    errorMessage.value = Object.values(errors).flat().join(' ');
                },
            });
        }

        return {
            acceptHttp,
            errorMessage,
            closeModal,
            acceptShift,
        };
    },
}
</script>
