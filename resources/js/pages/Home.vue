<template>
    <main>{{ message }}</main>
</template>

<script>
import { ref } from 'vue';

export default {
    setup() {
        const message = ref('Haskoo')

        return {
            message
        }
    }
}
</script>