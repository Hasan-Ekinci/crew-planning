<template>
    <div class="flex min-h-screen bg-slate-50 text-slate-900">
        <aside
            class="flex w-56 shrink-0 flex-col border-r border-slate-200 p-4"
            :class="isAdminDashboard ? 'bg-slate-50' : 'bg-emerald-50/50'"
        >
            <div class="-mx-4 -mt-4 mb-5 border-b border-slate-200 px-7 py-4">
                <p class="text-lg font-semibold tracking-tight text-slate-900">Crew Planning</p>
            </div>

            <nav aria-label="Main navigation" class="space-y-2">
                <Link
                    v-for="item in navigationItems"
                    :key="item.name"
                    :href="item.route"
                    view-transition
                    :aria-current="activePage === item.name ? 'page' : undefined"
                    :class="[
                        'relative flex min-h-12 items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition-colors duration-200 focus-visible:outline-2 focus-visible:outline-offset-2 motion-reduce:transition-none',
                        isAdminDashboard ? 'focus-visible:outline-blue-600' : 'focus-visible:outline-emerald-600',
                        activePage === item.name
                            ? (isAdminDashboard ? 'text-blue-700' : 'text-emerald-800')
                            : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
                    ]"
                >
                    <span
                        v-if="activePage === item.name"
                        aria-hidden="true"
                        class="pointer-events-none absolute inset-0 rounded-xl border shadow-sm"
                        :class="isAdminDashboard ? 'border-blue-100 bg-blue-50' : 'border-emerald-200 bg-emerald-100/60'"
                        style="view-transition-name: sidebar-active"
                    ></span>
                    <slot name="navigation-icon" :item="item"></slot>
                    <span class="relative min-w-0 break-words">{{ item.label }}</span>
                </Link>
            </nav>

            <div class="mt-auto space-y-3 pt-4">
                <AccountSettingsMenuItem :active="activePage === 'accountSettings'" :is-admin-dashboard="isAdminDashboard" />
                <LogoutMenuItem />
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="border-b border-slate-200 bg-white p-4">
                <h1 class="text-xl font-semibold tracking-tight">{{ title }}</h1>
            </header>

            <main class="flex min-h-0 flex-1 flex-col p-6" style="view-transition-name: page-content">
                <slot />
            </main>
        </div>
    </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import AccountSettingsMenuItem from '@/shared-components/subcomponents/AccountSettingsMenuItem.vue';
import LogoutMenuItem from '@/shared-components/subcomponents/LogoutMenuItem.vue';

export default {
    components: {
        Link,
        AccountSettingsMenuItem,
        LogoutMenuItem,
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
