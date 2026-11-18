<template>
    <Modal :open-modal="openModal" @close-modal="closeModal">
        <h2 class="mb-6 break-words border-b border-slate-100 pb-4 pr-8 text-2xl font-semibold tracking-tight text-slate-900">{{ shift.title }}</h2>

        <div class="space-y-6">
            <section>
                <h3 class="mb-2 text-sm font-semibold text-slate-900">Description</h3>
                <p class="whitespace-pre-line break-words text-sm leading-relaxed text-slate-600">{{ shift.description || 'No description provided.' }}</p>
            </section>

            <section>
                <h3 class="mb-2 text-sm font-semibold text-slate-900">Other assigned crew</h3>
                <ul v-if="otherCrewMembers.length > 0" class="divide-y divide-slate-100">
                    <li v-for="member in otherCrewMembers" :key="member.id" class="break-words py-2 text-sm text-slate-700">
                        {{ member.name }}
                    </li>
                </ul>
                <p v-else class="text-sm text-slate-500">No other crew members assigned yet.</p>
            </section>

            <div v-if="canAccept" class="flex justify-end border-t border-slate-100 pt-5">
                <button
                    type="button"
                    :disabled="openAcceptConfirmation"
                    @click="openAcceptConfirmation = true"
                    class="min-h-11 w-full cursor-pointer rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-emerald-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 disabled:cursor-not-allowed disabled:opacity-50 sm:w-auto"
                >
                    Accept shift
                </button>
            </div>
        </div>
    </Modal>

    <AcceptShiftConfirmationModal
        v-if="openAcceptConfirmation"
        :open-modal="true"
        :shift-id="shift.id"
        :shift-title="shift.title"
        @close-modal="openAcceptConfirmation = false"
        @accepted="onShiftAccepted"
    />
</template>

<script>
import { computed, ref } from 'vue';
import Modal from '@/shared-components/subcomponents/Modal.vue';
import AcceptShiftConfirmationModal from './shift-modal/AcceptShiftConfirmationModal.vue';

export default {
    components: {
        Modal,
        AcceptShiftConfirmationModal,
    },
    props: {
        openModal: {
            type: Boolean,
            default: false
        },
        shift: {
            type: Object,
            required: true
        },
        currentUserId: {
            type: Number,
            required: true
        },
        canAccept: {
            type: Boolean,
            default: false
        }
    },
    emits: ['closeModal', 'accepted'],
    setup(props, { emit }) {
        const openAcceptConfirmation = ref(false);
        const otherCrewMembers = computed(() => (props.shift.users ?? []).filter(
            member => member.id !== props.currentUserId
        ));

        function closeModal() {
            if (openAcceptConfirmation.value) {
                return;
            }

            emit('closeModal');
        }

        function onShiftAccepted() {
            openAcceptConfirmation.value = false;
            emit('accepted');
        }

        return {
            openAcceptConfirmation,
            otherCrewMembers,
            closeModal,
            onShiftAccepted,
        };
    },
}
</script>
