<script setup>
import { useForm } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const form = useForm({
    name: '',
    email: '',
    phone: '',
    account_type: 'Individual / Personal',
    message: '',
})

function submit() {
    form.post('/open-account')
}
</script>

<template>
    <PublicLayout>
        <section class="bg-[#061A33] pt-28 pb-20 text-white">
            <div class="container-heritage">
                <p class="mb-4 text-sm font-semibold uppercase tracking-[0.22em] text-[#F5B82E]">Investor Services</p>
                <h1 class="font-display text-4xl font-semibold md:text-6xl">Open an Account</h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-white/75">
                    Start your relationship with Heritage Capital Markets by submitting your details. Our team will contact you with the next steps and documentation requirements.
                </p>
            </div>
        </section>

        <section class="bg-[#F7F8FA] py-16 md:py-24">
            <div class="container-heritage max-w-4xl">
                <div class="grid gap-10 lg:grid-cols-[1fr_0.65fr]">
                    <form class="bg-white p-7 shadow-sm md:p-10" @submit.prevent="submit">
                        <h2 class="font-display text-3xl font-semibold text-[#061A33]">Request account opening</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500">Complete the form and our team will follow up.</p>

                        <div v-if="$page.props.flash?.success" class="mt-6 bg-green-50 p-4 text-sm text-green-700">
                            {{ $page.props.flash.success }}
                        </div>

                        <div class="mt-8 grid gap-5 md:grid-cols-2">
                            <label class="block">
                                <span class="text-sm font-semibold text-[#061A33]">Full Name *</span>
                                <input v-model="form.name" type="text" class="mt-2 w-full border border-slate-300 px-4 py-3 outline-none focus:border-[#F5B82E]" required />
                                <span v-if="form.errors.name" class="mt-1 block text-xs text-red-600">{{ form.errors.name }}</span>
                            </label>

                            <label class="block">
                                <span class="text-sm font-semibold text-[#061A33]">Email *</span>
                                <input v-model="form.email" type="email" class="mt-2 w-full border border-slate-300 px-4 py-3 outline-none focus:border-[#F5B82E]" required />
                                <span v-if="form.errors.email" class="mt-1 block text-xs text-red-600">{{ form.errors.email }}</span>
                            </label>

                            <label class="block">
                                <span class="text-sm font-semibold text-[#061A33]">Phone *</span>
                                <input v-model="form.phone" type="tel" class="mt-2 w-full border border-slate-300 px-4 py-3 outline-none focus:border-[#F5B82E]" required />
                            </label>

                            <label class="block">
                                <span class="text-sm font-semibold text-[#061A33]">Account Type *</span>
                                <select v-model="form.account_type" class="mt-2 w-full border border-slate-300 px-4 py-3 outline-none focus:border-[#F5B82E]">
                                    <option>Individual / Personal</option>
                                    <option>Corporate / Institutional</option>
                                    <option>Professional / High Net Worth</option>
                                </select>
                            </label>
                        </div>

                        <label class="mt-5 block">
                            <span class="text-sm font-semibold text-[#061A33]">Additional Information</span>
                            <textarea v-model="form.message" rows="5" class="mt-2 w-full border border-slate-300 px-4 py-3 outline-none focus:border-[#F5B82E]" />
                        </label>

                        <button type="submit" class="btn-gold mt-6" :disabled="form.processing">
                            {{ form.processing ? 'Submitting...' : 'Submit Request' }}
                        </button>
                    </form>

                    <aside class="lg:pt-6">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-[#9A6B00]">What happens next?</p>
                        <div class="mt-6 space-y-6">
                            <div v-for="(item, index) in [
                                ['01', 'Submit your request', 'Tell us how you would like to engage with Heritage Capital Markets.'],
                                ['02', 'Our team contacts you', 'A member of our team will review your request and get in touch.'],
                                ['03', 'Complete documentation', 'We will guide you through the applicable account-opening requirements.'],
                            ]" :key="item[0]" class="flex gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center bg-[#061A33] text-xs font-bold text-[#F5B82E]">{{ item[0] }}</div>
                                <div>
                                    <h3 class="font-semibold text-[#061A33]">{{ item[1] }}</h3>
                                    <p class="mt-1 text-sm leading-6 text-slate-600">{{ item[2] }}</p>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
