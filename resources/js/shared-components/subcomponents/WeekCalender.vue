<template>
    <div class="grid shrink-0 grid-cols-[1fr_auto_1fr] items-center">
        <button
            type="button"
            class="flex cursor-pointer items-center justify-self-start gap-2 rounded px-3 py-2 text-sm hover:bg-gray-100"
            @click="changeWeek('previous')"
        >
            <span aria-hidden="true">&larr;</span>
            vorige week
        </button>
        <p class="text-center text-sm font-medium">{{ weekDateRange }}</p>
        <button
            type="button"
            class="flex cursor-pointer items-center justify-self-end gap-2 rounded px-3 py-2 text-sm hover:bg-gray-100"
            @click="changeWeek('next')"
        >
            volgende week
            <span aria-hidden="true">&rarr;</span>
        </button>
    </div>
    <div class="flex min-h-0 flex-1 flex-col border border-gray-300">
        <div class="grid shrink-0 grid-cols-7 divide-x divide-gray-300 border-b border-gray-300">
            <h2 v-for="day in days" :key="day" class="min-w-0 p-3 text-center font-medium">{{ day }}</h2>
        </div>
        <div class="min-h-0 flex-1 overflow-auto">
            <div class="relative grid min-h-full grid-cols-7 content-start gap-y-2 py-3">
                <div aria-hidden="true" class="pointer-events-none absolute inset-0 grid grid-cols-7 divide-x divide-gray-300">
                    <div v-for="day in days" :key="day"></div>
                </div>
                <article
                    v-for="(shift, index) in calendarShifts"
                    :key="shift.id"
                    @click="setSelectedShift(shift)"
                    :style="{ gridColumn: `${shift.startColumn} / ${shift.endColumn}`, gridRow: index + 1 }"
                    class="relative mx-1 min-w-0 rounded border border-blue-300 bg-blue-100 px-3 py-2 text-sm text-blue-900 cursor-pointer hover:bg-slate-300"
                >
                    <p class="break-words font-medium">{{ shift.title }}</p>
                </article>
            </div>
        </div>
    </div>
</template>

<script>
import { computed } from 'vue';

export default {
    props: {
        shifts: {
            type: Array,
            default: () => [],
        },
        weekStartDateTime: {
            type: String,
            required: true,
        },
    },
    emits: ['changeWeek', 'setSelectedShift'],
    setup(props, { emit }) {
        const days = [
            'Maandag',
            'Dinsdag',
            'Woensdag',
            'Donderdag',
            'Vrijdag',
            'Zaterdag',
            'Zondag',
        ];

        function dayNumber(date) {
            return Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()) / 86400000;
        }

        const weekDateRange = computed(() => {
            const start = new Date(props.weekStartDateTime.replace(' ', 'T'));
            const end = new Date(start);
            end.setDate(end.getDate() + 6);
            const options = { day: '2-digit', month: '2-digit', year: 'numeric' };

            return `${start.toLocaleDateString('nl-NL', options)} <> ${end.toLocaleDateString('nl-NL', options)}`;
        });

        const calendarShifts = computed(() => {
            const firstDay = dayNumber(new Date(props.weekStartDateTime.replace(' ', 'T')));

            return props.shifts
                .map(shift => ({
                    ...shift,
                    start: new Date(shift.start_datetime.replace(' ', 'T')),
                    end: new Date(shift.end_datetime.replace(' ', 'T')),
                }))
                .filter(shift => shift.start < shift.end)
                .sort((a, b) => a.start - b.start)
                .map(shift => ({
                    ...shift,
                    startColumn: Math.max(0, dayNumber(shift.start) - firstDay) + 1,
                    endColumn: Math.min(6, dayNumber(new Date(shift.end.getTime() - 1)) - firstDay) + 2,
                }))
                .filter(shift => shift.startColumn < shift.endColumn);
        });

        function changeWeek(direction) {
            emit('changeWeek', direction);
        }

        function setSelectedShift(shift) {
            emit('setSelectedShift', shift)
        }

        return { 
            days,
            calendarShifts,
            weekDateRange,
            changeWeek,
            setSelectedShift
         };
    },
};
</script>
