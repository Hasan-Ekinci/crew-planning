<template>
    <Link
        href="/account-settings"
        view-transition
        :aria-current="active ? 'page' : undefined"
        :class="[
            'relative flex min-h-12 items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition-colors duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 motion-reduce:transition-none',
            isAdminDashboard ? 'focus-visible:outline-blue-600' : 'focus-visible:outline-emerald-600',
            active
                ? (isAdminDashboard ? 'text-blue-700' : 'text-emerald-800')
                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
        ]"
    >
        <span
            v-if="active"
            aria-hidden="true"
            class="pointer-events-none absolute inset-0 rounded-xl border shadow-sm"
            :class="isAdminDashboard ? 'border-blue-100 bg-blue-50' : 'border-emerald-200 bg-emerald-100/60'"
            style="view-transition-name: sidebar-active"
        ></span>
        <SettingsIcon class="relative shrink-0" />
        <span class="relative min-w-0 break-words">Account settings</span>
    </Link>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import SettingsIcon from '@/shared-components/icons/SettingsIcon.vue';

export default {
    components: {
        Link,
        SettingsIcon,
    },
    props: {
        active: {
            type: Boolean,
            default: false
        },
        isAdminDashboard: {
            type: Boolean,
            required: true
        }
    },
}
</script>
