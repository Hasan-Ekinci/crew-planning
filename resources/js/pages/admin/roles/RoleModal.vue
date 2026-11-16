<template>
    <Modal :open-modal="openModal" size="s" @close-modal="closeModal">
        <h2 class="mb-5 pr-8 text-xl font-semibold text-slate-900">Edit role</h2>

        <form class="space-y-5" @submit.prevent="submitForm">
            <p v-if="errorMessage" role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm leading-relaxed text-red-700">{{ errorMessage }}</p>

            <div>
                <label for="role-name" class="mb-1.5 block text-sm font-medium text-slate-700">Role name</label>
                <input
                    id="role-name"
                    v-model="form.name"
                    name="name"
                    type="text"
                    required
                    maxlength="255"
                    :disabled="submitHttp.processing"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 disabled:bg-slate-50"
                />
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-5">
                <button
                    type="button"
                    :disabled="submitHttp.processing"
                    @click="closeModal"
                    class="min-h-11 cursor-pointer rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    :disabled="submitHttp.processing"
                    class="min-h-11 cursor-pointer rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition-colors duration-200 hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:cursor-not-allowed disabled:opacity-50 motion-reduce:transition-none"
                >
                    {{ submitHttp.processing ? 'Saving…' : 'Save role' }}
                </button>
            </div>
        </form>
    </Modal>
</template>

<script>
import { reactive, ref } from 'vue';
import { useHttp } from '@inertiajs/vue3';
import Modal from '@/shared-components/subcomponents/Modal.vue';

export default {
    components: {
        Modal,
    },
    props: {
        openModal: {
            type: Boolean,
            default: false
        },
        role: {
            type: Object,
            required: true
        }
    },
    emits: ['closeModal', 'saved'],
    setup(props, { emit }) {
        const form = reactive({
            id: props.role.id,
            name: props.role.name,
        });
        const submitHttp = useHttp();
        const errorMessage = ref('');

        function closeModal() {
            if (submitHttp.processing) {
                return;
            }

            emit('closeModal');
        }

        function submitForm() {
            if (submitHttp.processing) {
                return;
            }

            errorMessage.value = '';

            submitHttp.transform(() => ({ ...form })).post('/admin/roles/save', {
                onSuccess: (response) => {
                    if (!response.success) {
                        errorMessage.value = response.message;
                        return;
                    }

                    emit('saved');
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
            closeModal,
            submitForm,
        };
    },
}
</script>
