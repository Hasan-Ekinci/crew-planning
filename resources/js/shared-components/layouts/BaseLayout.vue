<template>
    <div class="flex min-h-screen">
        <aside :class="['w-56 shrink-0 border-r border-gray-300 p-4', !isAdminDashboard ? 'bg-red-100' : 'bg-blue-100']">
            <nav class="space-y-0">
                <Link
                    v-for="item in navigationItems"
                    :key="item.name"
                    :href="item.route"
                    :class="['block rounded px-3 py-2 hover:bg-gray-200 hover:-mr-4 hover:rounded-r-none', activePage === item.name ? '-mr-4 rounded-r-none bg-white' : '']"
                >
                    {{ item.label }}
                </Link>
            </nav>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="border-b border-gray-300 p-4">
                <h1 class="text-xl font-semibold">{{ title }}</h1>
            </header>

            <main class="flex min-h-0 flex-1 flex-col p-6">
                <slot />
            </main>
        </div>
    </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
    components: {
        Link,
    },
    props: {
        activePage: {
            type: String,
            required: true
        },
        title: {
            type: String,
            required: true,
        },
        navigationItems: {
            type: Object,
            default: () => {},
        },
        isAdminDashboard: {
            type: Boolean,
            required: true
        }
    }
};
</script>
