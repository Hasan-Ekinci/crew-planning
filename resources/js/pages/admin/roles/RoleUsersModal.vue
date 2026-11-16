<template>
    <Modal :open-modal="openModal" @close-modal="$emit('closeModal')">
        <h2 class="mb-5 break-words pr-8 text-xl font-semibold text-slate-900">Users assigned to {{ role.name }}</h2>

        <p v-if="http.processing" role="status" class="py-4 text-sm text-slate-500">Loading users…</p>
        <p v-else-if="errorMessage" role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm leading-relaxed text-red-700">{{ errorMessage }}</p>
        <ul v-else-if="users.length > 0" class="divide-y divide-slate-100">
            <li v-for="user in users" :key="user.id" class="py-3">
                <a
                    :href="`/admin/employees/edit/${user.id}`"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="rounded text-sm font-medium text-blue-700 hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                >
                    {{ user.name }}
                    <span class="sr-only">(opens in a new tab)</span>
                </a>
            </li>
        </ul>
        <p v-else class="py-4 text-sm text-slate-500">No users assigned to this role.</p>

        <div class="mt-5 flex justify-end border-t border-slate-100 pt-5">
            <button
                type="button"
                @click="$emit('closeModal')"
                class="min-h-11 cursor-pointer rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
            >
                Close
            </button>
        </div>
    </Modal>
</template>

<script>
import { onMounted, ref } from 'vue';
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
    emits: ['closeModal'],
    setup(props) {
        const users = ref([]);
        const http = useHttp();
        const errorMessage = ref('');

        onMounted(async () => {
            try {
                users.value = await http.get(`/admin/roles/${props.role.id}/users`);
            } catch {
                errorMessage.value = 'Unable to load users. Please try again.';
            }
        });

        return {
            users,
            http,
            errorMessage,
        };
    },
}
</script>
