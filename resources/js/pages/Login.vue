<template>
    <form class="mx-auto mt-12 max-w-sm rounded border border-gray-300 p-6 bg-gray-100">
        <h1 class="mb-4 text-xl font-semibold">Login</h1>

        <div class="mb-4">
            <label for="email" class="mb-1 block">Email</label>
            <input
                id="email"
                v-model="email"
                name="email"
                type="email"
                autocomplete="email"
                class="w-full rounded border border-gray-300 p-2 bg-white"
            />
        </div>

        <div>
            <label for="password" class="mb-1 block">Password</label>
            <input
                id="password"
                v-model="password"
                name="password"
                type="password"
                autocomplete="current-password"
                class="w-full rounded border border-gray-300 p-2 bg-white"
            />
        </div>

        <i class="text-red-600 block my-2 text-center">{{ errorMessage }}</i>

        <button
            type="submit"
            @click.prevent="login"
            class="mx-auto mt-4 block w-2/3 rounded border border-gray-300 bg-slate-400 hover:bg-slate-500 hover: p-2"
        >
            Login
        </button>
    </form>
</template>

<script>
import { ref } from 'vue';
import { useHttp } from '@inertiajs/vue3';

export default {
    setup() {
        const email = ref('');
        const password = ref('');
        const errorMessage = ref('')
        const http = useHttp();

        const login = () => {
            if (email.value === '' || password.value === '') {
                errorMessage.value = "Velden kunnen niet leeg zijn"
                return
            }

            http.transform(() => ({
                email: email.value,
                password: password.value,
            })).post('/authenticate', {
                onSuccess: (response) => {
                    console.log(response);
                    if (response.success) {

                        errorMessage.value = ''
                        // redirect to the logged in page
                    
                    }
                
                    errorMessage.value = response.message

                },
            }).catch((error) => {
                // console.error(error);
                errorMessage.value = 'Er is iets mis gegaan'
            });
        };

        return {
            email,
            password,
            errorMessage,
            login,
        };
    }
}
</script>
