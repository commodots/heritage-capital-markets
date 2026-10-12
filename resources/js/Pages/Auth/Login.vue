<script setup>
import { Head, useForm } from '@inertiajs/vue3'

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const submit = () => {
    form.post('/login')
}

const clearLoginErrors = () => {
    form.clearErrors('email', 'password')
}
</script>

<template>
    <Head title="Administrator Login" />

    <div class="flex min-h-screen items-center justify-center bg-heritage-navy px-6">
        <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-2xl">

            <div class="mb-8 text-center">
                <h1 class="text-2xl font-bold text-heritage-navy">
                    Heritage Capital Markets
                </h1>

                <p class="mt-2 text-sm text-slate-500">
                    Administrator Login
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-5">

                <div>
                    <label class="mb-2 block text-sm font-medium">
                        Email
                    </label>

                    <input
                        v-model="form.email"
                        @input="clearLoginErrors"
                        type="email"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3"
                        required
                    />

                    <div
                        v-if="form.errors.email"
                        role="alert"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.email }}
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium">
                        Password
                    </label>

                    <input
                        v-model="form.password"
                        @input="clearLoginErrors"
                        type="password"
                        class="w-full rounded-lg border border-slate-300 px-4 py-3"
                        required
                    />

                    <div
                        v-if="form.errors.password"
                        role="alert"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ form.errors.password }}
                    </div>
                </div>

                <label class="flex items-center gap-2 text-sm">
                    <input
                        v-model="form.remember"
                        type="checkbox"
                    />

                    Remember me
                </label>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-lg bg-heritage-gold px-4 py-3 font-semibold text-heritage-navy"
                >
                    {{ form.processing ? 'Signing in...' : 'Sign In' }}
                </button>

            </form>
        </div>
    </div>
</template>