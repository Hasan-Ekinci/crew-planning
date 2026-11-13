<template>
    <AdminLayout :active-page="activePage" title="Shifts" :navigation-items="navigationItems">
        <div class="flex min-h-0 flex-1 flex-col gap-4">
            <WeekCalender
                :shifts="shiftsFetched"
                :week-start-date-time="weekStartDateTimeUpdated"
                @change-week="changeWeek"
                @set-selected-shift="setSelectedShift"
            >
                <template #actions>
                    <button
                        @click="setSelectedShift"
                        type="button"
                        class="ml-auto inline-flex min-h-11 w-full cursor-pointer items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors duration-200 hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 motion-reduce:transition-none sm:w-auto"
                    >
                        <span aria-hidden="true" class="text-lg leading-none">+</span>
                        Add shift
                    </button>
                </template>
            </WeekCalender>
        </div>

        <span v-if="openModal">
            <ShiftModal
                :open-modal="openModal"
                :existing-shift="selectedShift"
                @close-modal="openModal = false"
                @fetch-all-shifts="fetchAllShifts"
            ></ShiftModal>
        </span>
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
            // Fetch shifts using the current week start timestamp.

            // Pass weekStartDateTimeUpdated.value and the direction with the request.
            const params = new URLSearchParams({
                oldDate: weekStartDateTimeUpdated.value,
                direction: direction,
            });
            const response = await http.get(`/admin/get-shifts?${params.toString()}`);

            shiftsFetched.value = response.shifts
            weekStartDateTimeUpdated.value = response.newWeekStartDate
        }

        const selectedShift = ref(null)
        function setSelectedShift(shift) {
            selectedShift.value = shift
            openModal.value = true
        }

        return {
            openModal,
            shiftsFetched,
            weekStartDateTimeUpdated,
            selectedShift,
            setSelectedShift,
            fetchAllShifts,
            changeWeek: fetchAllShifts
        }
    }
}
</script>
