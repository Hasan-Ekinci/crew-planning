<template>
    <div
        v-if="openModal"
        class="bg-black/40 absolute w-screen h-screen left-0 top-0 flex items-center justify-center p-4"
    >
        <div class="relative max-h-full w-full max-w-lg overflow-y-auto rounded bg-white p-6">
            <div
                @click="$emit('closeModal')"
                class="absolute right-3 top-3 flex h-8 w-8 cursor-pointer select-none items-center justify-center rounded text-lg text-gray-600 hover:bg-gray-100 hover:text-gray-900"
            >X</div>
            <slot></slot>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        openModal: {
            type: Boolean,
            default: false
        }
    }
}
</script>
