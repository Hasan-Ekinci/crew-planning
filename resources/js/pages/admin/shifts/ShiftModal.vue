<template>
    <Modal :open-modal="openModal" size="l" @close-modal="doCloseModal">
        <h2 class="mb-6 break-words border-b border-slate-100 pb-4 pr-8 text-2xl font-semibold tracking-tight text-slate-900">
            {{ isEditing ? (isUpdateForm ? 'Shift bewerken' : 'Shift toevoegen') : form.title }}
        </h2>

        <div v-if="!isEditing" class="space-y-6">
            <section v-if="form.description">
                <h3 class="mb-2 text-sm font-semibold text-slate-900">Beschrijving</h3>
                <p class="whitespace-pre-line break-words text-sm leading-relaxed text-slate-600">{{ form.description }}</p>
            </section>

            <dl class="grid gap-5 rounded-xl border border-slate-200 bg-slate-50 p-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm text-slate-500">Start</dt>
                    <dd class="mt-1 text-sm font-medium leading-6 text-slate-900">{{ formatDateTime(form.start_datetime) }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-slate-500">Einde</dt>
                    <dd class="mt-1 text-sm font-medium leading-6 text-slate-900">{{ formatDateTime(form.end_datetime) }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-slate-500">Rol</dt>
                    <dd class="mt-1 break-words text-sm font-medium leading-6 text-slate-900">{{ selectedRole?.name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-slate-500">Bezetting</dt>
                    <dd class="mt-1 text-sm font-medium leading-6 text-slate-900">{{ plannedEmployees.length }} van {{ form.max_crew }} crewleden</dd>
                </div>
            </dl>

            <section>
                <div class="mb-3 flex items-center justify-between gap-3">
                    <h3 class="text-sm font-semibold text-slate-900">Ingeplande medewerkers</h3>
                    <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">{{ plannedEmployees.length }}</span>
                </div>
                <ul v-if="plannedEmployees.length > 0" class="grid gap-2 sm:grid-cols-2">
                    <li
                        v-for="employee in plannedEmployees"
                        :key="employee.id"
                        class="flex min-w-0 items-center gap-3 rounded-lg border border-slate-200 px-3 py-2.5 text-sm text-slate-700"
                    >
                        <span aria-hidden="true" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-50 font-semibold text-blue-700">{{ employee.name.slice(0, 1) }}</span>
                        <span class="min-w-0 break-words">{{ employee.name }}</span>
                    </li>
                </ul>
                <p v-else class="rounded-lg bg-slate-50 px-4 py-5 text-center text-sm text-slate-500">Nog niemand ingepland.</p>
            </section>

            <div class="flex justify-end border-t border-slate-100 pt-5">
                <button
                    type="button"
                    @click="isEditing = true"
                    class="min-h-11 w-full cursor-pointer rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 sm:w-auto"
                >
                    Bewerken
                </button>
            </div>
        </div>

        <form v-else class="space-y-5" @submit.prevent="submitForm">
            <div>
                <label for="shift-title" class="mb-1.5 block text-sm font-medium text-slate-700">Titel</label>
                <input
                    id="shift-title"
                    v-model="form.title"
                    name="title"
                    type="text"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                />
            </div>

            <div>
                <label for="shift-description" class="mb-1.5 block text-sm font-medium text-slate-700">Beschrijving</label>
                <textarea
                    id="shift-description"
                    v-model="form.description"
                    name="description"
                    rows="3"
                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                ></textarea>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="shift-max-crew" class="mb-1.5 block text-sm font-medium text-slate-700">Maximaal aantal crewleden</label>
                    <input
                        id="shift-max-crew"
                        v-model.number="form.max_crew"
                        name="max_crew"
                        type="number"
                        min="1"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    />
                </div>

                <div>
                    <label for="shift-role" class="mb-1.5 block text-sm font-medium text-slate-700">Rol</label>
                    <select
                        id="shift-role"
                        v-model="form.shift_role"
                        @change="fetchEmployeesForRole"
                        name="shift_role"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
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
                    <label for="shift-start" class="mb-1.5 block text-sm font-medium text-slate-700">Startdatum en -tijd</label>
                    <input
                        id="shift-start"
                        v-model="form.start_datetime"
                        @change="fetchEmployeesForRole"
                        name="start_datetime"
                        type="datetime-local"
                        class="w-full min-w-0 rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    />
                </div>

                <div>
                    <label for="shift-end" class="mb-1.5 block text-sm font-medium text-slate-700">Einddatum en -tijd</label>
                    <input
                        id="shift-end"
                        v-model="form.end_datetime"
                        @change="fetchEmployeesForRole"
                        name="end_datetime"
                        type="datetime-local"
                        class="w-full min-w-0 rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    />
                </div>
            </div>

            <div v-show="form.employees.length > 0" class="grid gap-4 sm:grid-cols-2">
                <section class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <h3 class="mb-3 border-b border-slate-200 pb-2 text-sm font-semibold text-slate-700">Beschikbaar</h3>
                    <div class="min-h-32 space-y-2">
                        <button
                            v-for="employee in availableEmployees"
                            :key="employee.id"
                            type="button"
                            @click="toggleEmployee(employee.id)"
                            class="block min-h-11 w-full cursor-pointer break-words rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-left text-sm text-slate-700 transition-colors hover:border-blue-300 hover:bg-blue-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                        >
                            {{ employee.name }}
                        </button>
                        <p v-if="availableEmployees.length === 0" class="text-sm text-gray-500">
                            Geen beschikbare medewerkers.
                        </p>
                    </div>
                </section>

                <section class="rounded-xl border border-emerald-200 bg-emerald-50 p-3">
                    <h3 class="mb-3 border-b border-emerald-200 pb-2 text-sm font-semibold text-emerald-800">Ingepland</h3>
                    <div class="min-h-32 space-y-2">
                        <button
                            v-for="employee in plannedEmployees"
                            :key="employee.id"
                            type="button"
                            @click="toggleEmployee(employee.id)"
                            class="block min-h-11 w-full cursor-pointer break-words rounded-lg border border-emerald-200 bg-white px-3 py-2.5 text-left text-sm text-emerald-800 transition-colors hover:bg-emerald-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600"
                        >
                            {{ employee.name }}
                        </button>
                        <p v-if="plannedEmployees.length === 0" class="text-sm text-gray-500">
                            Nog niemand ingepland.
                        </p>
                    </div>
                </section>
            </div>
            <p v-if="errorMessage" role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm leading-relaxed text-red-700">{{ errorMessage }}</p>
            <div class="flex justify-end border-t border-slate-100 pt-5">
                <button
                    type="submit"
                    :disabled="submitHttp.processing"
                    class="min-h-11 w-full cursor-pointer rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:cursor-not-allowed disabled:opacity-50 sm:w-auto"
                >
                    Opslaan
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
        },
        existingShift: {
            type: Object,
            default: () => {}
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
            id: null,
            title: '',
            description: '',
            max_crew: '',
            shift_role: '',
            start_datetime: `${today}T09:00`,
            end_datetime: `${today}T17:00`,
            employees: []
        });

        const isUpdateForm = ref(!!props.existingShift?.id)
        const isEditing = ref(!isUpdateForm.value);
        const roles = ref([]);
        const selectedRole = computed(() => roles.value.find(role => role.id === form.shift_role));
        const plannedEmployeeIds = ref([]);

        if (isUpdateForm.value) {
            Object.assign(form, {
                id: props.existingShift.id,
                title: props.existingShift.title,
                description: props.existingShift.description ?? '',
                max_crew: props.existingShift.max_crew,
                shift_role: props.existingShift.shift_role,
                start_datetime: props.existingShift.start_datetime.replace(' ', 'T').slice(0, 16),
                end_datetime: props.existingShift.end_datetime.replace(' ', 'T').slice(0, 16),
                employees: (props.existingShift.users ?? []).map(user => ({
                    id: user.id,
                    name: user.name,
                })),
            });
            plannedEmployeeIds.value = form.employees.map(employee => employee.id);
        }

        const availableEmployees = computed(() => form.employees.filter(
            employee => !plannedEmployeeIds.value.includes(employee.id)
        ));
        const plannedEmployees = computed(() => form.employees.filter(
            employee => plannedEmployeeIds.value.includes(employee.id)
        ));
        const http = useHttp();
        const submitHttp = useHttp();

        function formatDateTime(value) {
            return new Date(value).toLocaleString('nl-NL', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            });
        }

        function resetForm() {
            Object.assign(form, {
                id: null,
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
        }

        function doCloseModal() {
            resetForm()
            emit('closeModal');
        }

        async function submitForm() {

            const call = await submitHttp.transform(() => ({
                ...form,
                employees: plannedEmployees.value.map(employee => ({
                    id: employee.id,
                    name: employee.name,
                })),
            }))

            if (isUpdateForm) {
                call.post('/admin/add-shift', {
                    onSuccess: (response) => {
                        if (!response.success) {
                            errorMessage.value = response.message
                            return;
                        }

                        resetForm()

                        emit('fetchAllShifts', 'current');
                        emit('closeModal');
                    },
                    onError: (errors) => {
                        errorMessage.value = Object.values(errors).flat().join(' ');
                    },
                });
            } else {
                call.put('/admin/update-shift', {
                    onSuccess: (response) => {
                        if (!response.success) {
                            errorMessage.value = response.message
                            return;
                        }

                        resetForm()

                        emit('fetchAllShifts', 'current');
                        emit('closeModal');
                    },
                    onError: (errors) => {
                        errorMessage.value = Object.values(errors).flat().join(' ');
                    },
                });
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
                    shiftId: form.id ?? ''
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
            const assignedEmployees = [...plannedEmployees.value];
            await fetchEmployeesForRole();

            if (isUpdateForm.value) {
                for (const employee of assignedEmployees) {
                    if (!form.employees.some(available => available.id === employee.id)) {
                        form.employees.push(employee);
                    }
                }
                plannedEmployeeIds.value = assignedEmployees.map(employee => employee.id);
            }
        });

        return {
            form,
            isUpdateForm,
            isEditing,
            roles,
            selectedRole,
            errorMessage,
            availableEmployees,
            plannedEmployees,
            formatDateTime,
            toggleEmployee,
            submitHttp,
            doCloseModal,
            submitForm,
            fetchEmployeesForRole
        };
    },
};
</script>
