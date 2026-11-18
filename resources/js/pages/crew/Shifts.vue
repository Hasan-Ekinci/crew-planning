<template>
    <CrewLayout :active-page="activePage" title="My Shifts" :navigation-items="navigationItems">
        <div class="flex min-h-0 flex-1 flex-col gap-4">
            <WeekCalender
                :shifts="shiftsFetched"
                :week-start-date-time="weekStartDateTimeUpdated"
                @change-week="changeWeek"
                @set-selected-shift="selectedShift = $event"
            />
        </div>

        <ShiftModal
            v-if="selectedShift"
            :open-modal="true"
            :shift="selectedShift"
            :current-user-id="currentUserId"
            @close-modal="selectedShift = null"
        />
    </CrewLayout>
</template>

<script>
import { ref } from 'vue';
import { useHttp } from '@inertiajs/vue3';
import CrewLayout from '@/shared-components/layouts/CrewLayout.vue';
import WeekCalender from '@/shared-components/subcomponents/WeekCalender.vue';
import ShiftModal from './shifts/ShiftModal.vue';

export default {
    components: {
        CrewLayout,
        WeekCalender,
        ShiftModal,
    },
    props: {
        activePage: {
            type: String,
            required: true
        },
        navigationItems: {
            type: Object,
            default: () => ({})
        },
        shifts: {
            type: Array,
            default: () => []
        },
        weekStartDateTime: {
            type: String,
            required: true
        },
        currentUserId: {
            type: Number,
            required: true
        }
    },
    setup(props) {
        const http = useHttp();
        const shiftsFetched = ref(props.shifts);
        const weekStartDateTimeUpdated = ref(props.weekStartDateTime);
        const selectedShift = ref(null);

        async function changeWeek(direction) {
            const params = new URLSearchParams({
                oldDate: weekStartDateTimeUpdated.value,
                direction: direction,
            });
            const response = await http.get(`/crew/get-shifts?${params.toString()}`);

            shiftsFetched.value = response.shifts;
            weekStartDateTimeUpdated.value = response.newWeekStartDate;
        }

        return {
            shiftsFetched,
            weekStartDateTimeUpdated,
            selectedShift,
            changeWeek,
        };
    },
}
</script>
