<template>
    <section class="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex shrink-0 flex-wrap items-center justify-between gap-4 border-b border-slate-200 p-4 sm:px-5">
            <div class="flex min-w-0 flex-wrap items-center gap-4">
                <div class="flex shrink-0 items-center gap-1 rounded-lg border border-slate-200 bg-white p-1">
                    <button
                        type="button"
                        aria-label="Previous week"
                        title="Previous week"
                        class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-md text-lg text-slate-600 transition-colors duration-200 hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-blue-600 motion-reduce:transition-none"
                        @click="changeWeek('previous')"
                    >
                        <span aria-hidden="true">&larr;</span>
                    </button>
                    <button
                        type="button"
                        aria-label="Next week"
                        title="Next week"
                        class="flex h-9 w-9 cursor-pointer items-center justify-center rounded-md text-lg text-slate-600 transition-colors duration-200 hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-2 focus-visible:outline-blue-600 motion-reduce:transition-none"
                        @click="changeWeek('next')"
                    >
                        <span aria-hidden="true">&rarr;</span>
                    </button>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Week overview</p>
                    <h2 class="mt-1 text-lg font-semibold tracking-tight text-slate-900">{{ weekDateRange }}</h2>
                </div>
            </div>
            <slot name="actions"></slot>
        </div>
        <div class="min-h-0 flex-1 overflow-auto">
            <div class="flex min-h-full min-w-[56rem] flex-col">
                <div class="grid shrink-0 grid-cols-7 divide-x divide-slate-100 border-b border-slate-200">
                    <div
                        v-for="day in days"
                        :key="day.name"
                        class="min-w-0 px-3 py-4 text-center"
                        :class="day.isWeekend ? 'bg-slate-50' : 'bg-white'"
                    >
                        <h3 class="text-xs font-medium text-slate-500">{{ day.name }}</h3>
                        <time
                            :datetime="day.dateTime"
                            :aria-current="day.isToday ? 'date' : undefined"
                            class="mx-auto mt-2 flex h-9 w-9 items-center justify-center rounded-full text-lg font-semibold"
                            :class="day.isToday ? 'bg-blue-600 text-white' : 'text-slate-900'"
                        >
                            {{ day.dayOfMonth }}
                        </time>
                    </div>
                </div>
                <div class="relative grid min-h-[24rem] flex-1 grid-cols-7 content-start gap-y-3 py-4">
                    <div aria-hidden="true" class="pointer-events-none absolute inset-0 grid grid-cols-7 divide-x divide-slate-100">
                        <div
                            v-for="day in days"
                            :key="day.name"
                            :class="day.isToday ? 'bg-blue-50/40' : (day.isWeekend ? 'bg-slate-50/80' : '')"
                        ></div>
                    </div>
                    <component
                        v-for="(shift, index) in calendarShifts"
                        :key="shift.id"
                        :is="readOnly ? 'div' : 'button'"
                        :type="readOnly ? undefined : 'button'"
                        @click="setSelectedShift(shift)"
                        :style="{ gridColumn: `${shift.startColumn} / ${shift.endColumn}`, gridRow: index + 1 }"
                        class="relative mx-2 min-w-0 rounded-xl border border-l-4 border-blue-200 border-l-blue-500 bg-blue-50 px-3 py-3 text-left shadow-sm"
                        :class="readOnly ? '' : 'cursor-pointer transition-[background-color,border-color,box-shadow] duration-200 hover:border-blue-300 hover:border-l-blue-600 hover:bg-blue-100/70 hover:shadow-md focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 motion-reduce:transition-none'"
                    >
                        <p class="break-words text-sm font-semibold text-blue-950">{{ shift.title }}</p>
                        <p class="mt-1 text-xs leading-5 text-blue-800">{{ formatShiftTime(shift) }}</p>
                        <span class="mt-3 flex items-center gap-1.5 text-xs font-medium text-blue-700">
                            <UsersIcon class="shrink-0" />
                            <span>{{ shift.users?.length ?? 0 }} / {{ shift.max_crew }} crew members</span>
                        </span>
                    </component>
                    <p v-if="calendarShifts.length === 0" class="pointer-events-none absolute inset-0 flex items-center justify-center text-sm text-slate-500">
                        No shifts this week.
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import { computed } from 'vue';
import UsersIcon from '@/shared-components/icons/UsersIcon.vue';

export default {
    components: {
        UsersIcon,
    },
    props: {
        shifts: {
            type: Array,
            default: () => [],
        },
        weekStartDateTime: {
            type: String,
            required: true,
        },
        readOnly: {
            type: Boolean,
            default: false,
        },
    },
    emits: ['changeWeek', 'setSelectedShift'],
    setup(props, { emit }) {
        const weekdays = [
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday',
        ];

        function dayNumber(date) {
            return Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()) / 86400000;
        }

        const days = computed(() => {
            const start = new Date(props.weekStartDateTime.replace(' ', 'T'));
            const today = dayNumber(new Date());

            return weekdays.map((name, index) => {
                const date = new Date(start);
                date.setDate(start.getDate() + index);

                return {
                    name,
                    dayOfMonth: date.getDate(),
                    dateTime: [
                        date.getFullYear(),
                        String(date.getMonth() + 1).padStart(2, '0'),
                        String(date.getDate()).padStart(2, '0'),
                    ].join('-'),
                    isToday: dayNumber(date) === today,
                    isWeekend: index >= 5,
                };
            });
        });

        const weekDateRange = computed(() => {
            const start = new Date(props.weekStartDateTime.replace(' ', 'T'));
            const end = new Date(start);
            end.setDate(end.getDate() + 6);
            const options = { day: 'numeric', month: 'short', year: 'numeric' };

            return `${start.toLocaleDateString('en-GB', options)} – ${end.toLocaleDateString('en-GB', options)}`;
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

        function formatShiftTime(shift) {
            const options = { hour: '2-digit', minute: '2-digit' };

            if (dayNumber(shift.start) !== dayNumber(shift.end)) {
                options.weekday = 'short';
            }

            return `${shift.start.toLocaleString('en-GB', options)} – ${shift.end.toLocaleString('en-GB', options)}`;
        }

        function setSelectedShift(shift) {
            if (props.readOnly) {
                return;
            }

            emit('setSelectedShift', shift)
        }

        return { 
            days,
            calendarShifts,
            weekDateRange,
            changeWeek,
            formatShiftTime,
            setSelectedShift
         };
    },
};
</script>
