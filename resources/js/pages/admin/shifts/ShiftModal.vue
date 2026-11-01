<template>
    <Modal :open-modal="openModal" @close-modal="$emit('closeModal')">
        <h2 class="mb-4 text-xl font-semibold">Shift toevoegen</h2>

        <form class="space-y-4" @submit.prevent="submitForm">
            <div>
                <label for="shift-title" class="mb-1 block text-sm font-medium">Titel</label>
                <input
                    id="shift-title"
                    v-model="form.title"
                    name="title"
                    type="text"
                    class="w-full rounded border border-gray-300 p-2"
                />
            </div>

            <div>
                <label for="shift-description" class="mb-1 block text-sm font-medium">Beschrijving</label>
                <textarea
                    id="shift-description"
                    v-model="form.description"
                    name="description"
                    rows="3"
                    class="w-full rounded border border-gray-300 p-2"
                ></textarea>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="shift-max-crew" class="mb-1 block text-sm font-medium">Maximaal aantal crewleden</label>
                    <input
                        id="shift-max-crew"
                        v-model.number="form.max_crew"
                        name="max_crew"
                        type="number"
                        min="1"
                        class="w-full rounded border border-gray-300 p-2"
                    />
                </div>

                <div>
                    <label for="shift-role" class="mb-1 block text-sm font-medium">Rol</label>
                    <select
                        id="shift-role"
                        v-model="form.shift_role"
                        @change="fetchEmployeesForRole"
                        name="shift_role"
                        class="w-full rounded border border-gray-300 p-2"
                    >
                        <option value="" disabled>Selecteer een rol</option>
                        <option v-for="role in roles" :key="role.id" :value="role.id">
                            {{ role.name }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="shift-start" class="mb-1 block text-sm font-medium">Startdatum en -tijd</label>
                    <input
                        id="shift-start"
                        v-model="form.start_datetime"
                        @change="fetchEmployeesForRole"
                        name="start_datetime"
                        type="datetime-local"
                        class="w-full min-w-0 rounded border border-gray-300 p-2 text-sm"
                    />
                </div>

                <div>
                    <label for="shift-end" class="mb-1 block text-sm font-medium">Einddatum en -tijd</label>
                    <input
                        id="shift-end"
                        v-model="form.end_datetime"
                        @change="fetchEmployeesForRole"
                        name="end_datetime"
                        type="datetime-local"
                        class="w-full min-w-0 rounded border border-gray-300 p-2 text-sm"
                    />
                </div>
            </div>

            <div v-show="form.employees.length > 0" class="grid grid-cols-2 gap-4">
                <section class="rounded border border-gray-200 bg-gray-50 p-3">
                    <h3 class="mb-3 border-b border-gray-200 pb-2 text-sm font-semibold">Beschikbaar</h3>
                    <div class="min-h-32 space-y-2">
                        <button
                            v-for="employee in availableEmployees"
                            :key="employee.id"
                            type="button"
                            @click="toggleEmployee(employee.id)"
                            class="block w-full cursor-pointer rounded border border-gray-300 bg-white px-3 py-2 text-left text-sm hover:border-slate-400 hover:bg-slate-100"
                        >
                            {{ employee.name }}
                        </button>
                        <p v-if="availableEmployees.length === 0" class="text-sm text-gray-500">
                            Geen beschikbare medewerkers.
                        </p>
                    </div>
                </section>

                <section class="rounded border border-gray-200 bg-gray-50 p-3">
                    <h3 class="mb-3 border-b border-gray-200 pb-2 text-sm font-semibold">Ingepland</h3>
                    <div class="min-h-32 space-y-2">
                        <button
                            v-for="employee in plannedEmployees"
                            :key="employee.id"
                            type="button"
                            @click="toggleEmployee(employee.id)"
                            class="block w-full cursor-pointer rounded border border-slate-300 bg-green-50 px-3 py-2 text-left text-sm hover:bg-slate-200"
                        >
                            {{ employee.name }}
                        </button>
                        <p v-if="plannedEmployees.length === 0" class="text-sm text-gray-500">
                            Nog niemand ingepland.
                        </p>
                    </div>
                </section>
            </div>
            <i class="text-red-600 block my-2 text-center">{{ errorMessage }}</i>
            <div class="flex justify-end border-t border-gray-200 pt-4">
                <button
                    type="submit"
                    :disabled="submitHttp.processing"
                    class="cursor-pointer rounded border border-gray-300 bg-slate-200 px-4 py-2 text-sm font-medium hover:bg-slate-300 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    Shift toevoegen
                </button>
            </div>
        </form>
    </Modal>
</template>

<script>
import { computed, onMounted, reactive, ref } from 'vue';
import { useHttp } from '@inertiajs/vue3';
import Modal from '@/shared-components/subcomponents/Modal.vue';

export default {
    components: {
        Modal,
    },
    props: {
        openModal: {
            type: Boolean,
            default: false
        }
    },
    emits: ['closeModal', 'fetchAllShifts'],
    setup(props, { emit }) {
        const now = new Date();
        const today = [
            now.getFullYear(),
            String(now.getMonth() + 1).padStart(2, '0'),
            String(now.getDate()).padStart(2, '0'),
        ].join('-');

        const errorMessage = ref('')

        const form = reactive({
            title: '',
            description: '',
            max_crew: '',
            shift_role: '',
            start_datetime: `${today}T09:00`,
            end_datetime: `${today}T17:00`,
            employees: []
        });

        const roles = ref([]);
        const plannedEmployeeIds = ref([]);
        const availableEmployees = computed(() => form.employees.filter(
            employee => !plannedEmployeeIds.value.includes(employee.id)
        ));
        const plannedEmployees = computed(() => form.employees.filter(
            employee => plannedEmployeeIds.value.includes(employee.id)
        ));
        const http = useHttp();
        const submitHttp = useHttp();

        async function submitForm() {

            try {
                await submitHttp.transform(() => ({
                    ...form,
                    employees: plannedEmployees.value.map(employee => ({
                        id: employee.id,
                        name: employee.name,
                    })),
                }))
                .post('/admin/add-shift', {
                    onSuccess: (response) => {
                        if (!response.success) {
                            errorMessage.value = response.message
                            return;
                        }

                        Object.assign(form, {
                            title: '',
                            description: '',
                            max_crew: '',
                            shift_role: '',
                            start_datetime: `${today}T09:00`,
                            end_datetime: `${today}T17:00`,
                            employees: [],
                        });
                        plannedEmployeeIds.value = [];
                        errorMessage.value = '';
                        emit('fetchAllShifts');
                        emit('closeModal');
                    },
                    onError: (errors) => {
                        errorMessage.value = Object.values(errors).flat().join(' ');
                    },
                });
            } catch (error) {
                //
            }
        }

        function toggleEmployee(employeeId) {
            const index = plannedEmployeeIds.value.indexOf(employeeId);

            if (index === -1) {
                plannedEmployeeIds.value.push(employeeId);
            } else {
                plannedEmployeeIds.value.splice(index, 1);
            }
        }

        async function fetchEmployeesForRole() {
            if (!form.shift_role || !form.start_datetime || !form.end_datetime) {
                return;
            }

            try {
                const params = new URLSearchParams({
                    roleId: form.shift_role,
                    start: form.start_datetime,
                    end: form.end_datetime,
                });
                const response = await http.get(`/admin/get-employees-by-roles?${params.toString()}`);
                form.employees = response
                plannedEmployeeIds.value = [];
            } catch (error) {
                //
            }
        }

        onMounted(async () => {
            try {
                const response = await http.get('/admin/get-roles');
                roles.value = response
            } catch (error) {
                //
            }

            if (!form.shift_role || !form.start_datetime || !form.end_datetime) {
                return;
            }
            fetchEmployeesForRole()
        });

        return {
            form,
            roles,
            errorMessage,
            availableEmployees,
            plannedEmployees,
            toggleEmployee,
            submitHttp,
            submitForm,
            fetchEmployeesForRole
        };
    },
};
</script>
