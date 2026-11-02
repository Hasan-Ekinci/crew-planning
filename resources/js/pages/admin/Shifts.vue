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

            <WeekCalender :shifts="shiftsFetched" :week-start-date-time="weekStartDateTimeUpdated" @change-week="changeWeek"></WeekCalender>
        </div>

        <ShiftModal :open-modal="openModal" @close-modal="openModal = false" @fetch-all-shifts="fetchAllShifts"></ShiftModal>
    </AdminLayout>
</template>

<script>
import AdminLayout from '@/shared-components/layouts/AdminLayout.vue';
import WeekCalender from '@/shared-components/subcomponents/WeekCalender.vue';
import ShiftModal from './shifts/ShiftModal.vue';
import { ref } from 'vue';
import { useHttp } from '@inertiajs/vue3';

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
        },
        shifts: {
            type: Array,
            default: () => []
        },
        weekStartDateTime: {
            type: String,
            required: true
        }
    },
    setup(props) {
        const openModal = ref(false)
        const http = useHttp();
        
        const shiftsFetched = ref(props.shifts)
        const weekStartDateTimeUpdated = ref(props.weekStartDateTime)

        async function fetchAllShifts(direction) {
            // fetch het met wat de de timestamp van nu

            // weekStartDateTimeUpdated.value (parsen?) en meegeven in de call EN de direction
            const params = new URLSearchParams({
                oldDate: weekStartDateTimeUpdated.value,
                direction: direction,
            });
            const response = await http.get(`/admin/get-shifts?${params.toString()}`);

            shiftsFetched.value = response.shifts
            weekStartDateTimeUpdated.value = response.newWeekStartDate
        }

        return {
            openModal,
            shiftsFetched,
            weekStartDateTimeUpdated,
            fetchAllShifts,
            changeWeek: fetchAllShifts
        }
    }
}
</script>
