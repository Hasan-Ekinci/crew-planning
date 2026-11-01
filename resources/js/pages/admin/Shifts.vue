<template>
    <AdminLayout :active-page="activePage" title="Shifts" :navigation-items="navigationItems">
        <div class="flex min-h-0 flex-1 flex-col gap-4">
            <div class="flex shrink-0 items-center justify-end">
                <button
                    @click="openModal = true"
                    type="button"
                    class="rounded border border-gray-300 bg-slate-200 px-4 py-2 text-sm font-medium hover:bg-gray-400 hover:cursor-pointer"
                >
                    Shift toevoegen
                </button>
            </div>

            <WeekCalender></WeekCalender>
        </div>

        <ShiftModal :open-modal="openModal" @close-modal="openModal = false" @fetch-all-shifts="fetchAllShifts"></ShiftModal>
    </AdminLayout>
</template>

<script>
import AdminLayout from '@/shared-components/layouts/AdminLayout.vue';
import WeekCalender from '@/shared-components/subcomponents/WeekCalender.vue';
import ShiftModal from './shifts/ShiftModal.vue';
import { ref } from 'vue';

export default {
    components: {
        AdminLayout,
        WeekCalender,
        ShiftModal
    },
    props: {
        activePage: {
            type: String,
            required: true
        },
        navigationItems: {
            type: Object,
            default: () => {},
        }
    },
    setup(props) {
        const openModal = ref(false)

        function fetchAllShifts() {
            console.log('ik moet nu alle shifts ophalen')
        }

        return {
            openModal,
            fetchAllShifts
        }
    }
}
</script>
