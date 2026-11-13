<template>
    <Teleport to="body">
        <div
            v-if="openModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
        >
            <div role="dialog" aria-modal="true" class="relative max-h-full w-full max-w-md overflow-y-auto rounded bg-white p-6 shadow-xl">
                <button
                    type="button"
                    aria-label="Sluiten"
                    @click="$emit('closeModal')"
                    class="absolute right-3 top-3 flex h-8 w-8 cursor-pointer items-center justify-center rounded text-lg text-gray-600 hover:bg-gray-100 hover:text-gray-900"
                >X</button>
                <h2 class="mb-4 break-words pr-8 text-xl font-semibold text-slate-900">
                    <slot name="title"></slot>
                </h2>
                <div class="space-y-3 text-sm leading-relaxed text-slate-600">
                    <slot name="body"></slot>
                </div>
                <div class="mt-6 flex flex-wrap justify-end gap-3 border-t border-slate-100 pt-4">
                    <slot name="buttons"></slot>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script>
export default {
    props: {
        openModal: {
            type: Boolean,
            default: false
        }
    },
    emits: ['closeModal'],
}
</script>
