<template>
    <ConfirmationModal :open-modal="openModal" @close-modal="closeModal">
        <template #title>Delete shift?</template>
        <template #body>
            <p class="break-words">Are you sure you want to delete the shift <strong class="font-semibold text-slate-900">{{ shiftTitle }}</strong>?</p>
            <p>The shift and its crew assignments will be permanently deleted. This cannot be undone.</p>
            <p v-if="errorMessage" role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700">{{ errorMessage }}</p>
        </template>
        <template #buttons>
            <button
                type="button"
                :disabled="deleteHttp.processing"
                @click="closeModal"
                class="min-h-11 w-full cursor-pointer rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-600 disabled:cursor-not-allowed disabled:opacity-50 sm:mr-auto sm:w-auto"
            >
                Cancel
            </button>
            <button
                type="button"
                :disabled="deleteHttp.processing"
                @click="deleteShift"
                class="min-h-11 w-full cursor-pointer rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600 disabled:cursor-not-allowed disabled:opacity-50 sm:w-auto"
            >
                {{ deleteHttp.processing ? 'Deleting…' : 'Delete shift' }}
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
    emits: ['closeModal', 'deleted'],
    setup(props, { emit }) {
        const deleteHttp = useHttp();
        const errorMessage = ref('');

        function closeModal() {
            if (deleteHttp.processing) {
                return;
            }

            errorMessage.value = '';
            emit('closeModal');
        }

        function deleteShift() {
            if (!props.openModal || !props.shiftId || deleteHttp.processing) {
                return;
            }

            errorMessage.value = '';

            deleteHttp.transform(() => ({ id: props.shiftId })).delete('/admin/delete-shift', {
                onSuccess: (response) => {
                    if (!response.success) {
                        errorMessage.value = response.message;
                        return;
                    }

                    emit('closeModal');
                    emit('deleted');
                },
                onError: (errors) => {
                    errorMessage.value = Object.values(errors).flat().join(' ');
                },
            });
        }

        return {
            deleteHttp,
            errorMessage,
            closeModal,
            deleteShift,
        };
    },
}
</script>
