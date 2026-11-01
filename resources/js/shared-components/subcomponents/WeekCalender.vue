<template>
    <div class="grid min-h-0 flex-1 grid-cols-7 divide-x divide-gray-300 border border-gray-300">
        <section v-for="day in days" :key="day" class="flex min-w-0 flex-col">
            <h2 class="shrink-0 border-b border-gray-300 p-3 text-center font-medium">{{ day }}</h2>
            <div class="min-h-0 flex-1"></div>
        </section>
    </div>
</template>

<script>
export default {
    setup() {
        const days = [
            'Maandag',
            'Dinsdag',
            'Woensdag',
            'Donderdag',
            'Vrijdag',
            'Zaterdag',
            'Zondag',
        ];

        return { days };
    },
};
</script>
