<template>
    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full text-left text-sm">
            <caption class="sr-only">{{ caption }}</caption>
            <thead class="border-b border-slate-200 bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                <tr>
                    <th v-for="column in columns" :key="column.key" scope="col" class="px-5 py-4">
                        {{ column.label }}
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr
                    v-for="row in rows"
                    :key="row.id"
                    class="transition-colors duration-200 hover:bg-slate-50 motion-reduce:transition-none"
                >
                    <component
                        :is="columnIndex === 0 ? 'th' : 'td'"
                        v-for="(column, columnIndex) in columns"
                        :key="column.key"
                        :scope="columnIndex === 0 ? 'row' : undefined"
                        class="px-5 py-4"
                        :class="columnIndex === 0 ? 'font-medium' : 'text-slate-600'"
                    >
                        <slot :name="`cell-${column.key}`" :row="row">{{ row[column.key] }}</slot>
                    </component>
                </tr>
                <tr v-if="rows.length === 0">
                    <td :colspan="columns.length" class="px-5 py-10 text-center text-slate-500">
                        {{ emptyMessage }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    props: {
        columns: {
            type: Array,
            required: true
        },
        rows: {
            type: Array,
            default: () => []
        },
        caption: {
            type: String,
            required: true
        },
        emptyMessage: {
            type: String,
            default: 'No results found.'
        }
    }
}
</script>
