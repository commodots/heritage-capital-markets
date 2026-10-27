<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'

const form = useForm({
    name: '',
    email: '',
    phone: '',
    account_type: 'Individual / Personal',
    message: '',
})

const errors = ref({})

function isValidEmail(value) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value || '')
}

function validate() {
    errors.value = {}
    if (!form.name?.trim()) errors.value.name = 'Full name is required.'
    if (!form.email?.trim()) errors.value.email = 'Email address is required.'
    else if (!isValidEmail(form.email)) errors.value.email = 'Please enter a valid email address.'
    if (!form.phone?.trim()) errors.value.phone = 'Phone number is required.'
    if (!form.account_type?.trim()) errors.value.account_type = 'Please select an account type.'
    return Object.keys(errors.value).length === 0
}

function submit() {
    if (!validate()) return
    form.post('/open-account')
}
</script>

<template>
    <PublicLayout>
        <SeoHead
            title="Open an Account"
            description="Request account-opening information from Heritage Capital Markets. Our team will contact you about the next steps and documentation."
            canonical-path="/open-account"
        />
        <section class="bg-heritage-navy pt-28 pb-20 text-white">
            <div class="container-heritage">
                <p class="mb-4 text-sm font-semibold uppercase tracking-[0.22em] text-heritage-gold">Investor Services</p>
                <h1 class="font-display text-4xl font-semibold md:text-6xl">Open an Account</h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-white/75">
                    Start your relationship with Heritage Capital Markets by submitting your details. Our team will contact you with the next steps and documentation requirements.
                </p>
            </div>
        </section>

        <section class="bg-heritage-offwhite py-16 md:py-24">
            <div class="container-heritage max-w-4xl">
                <div class="grid gap-10 lg:grid-cols-[1fr_0.65fr]">
                    <form class="bg-white p-7 shadow-sm md:p-10" @submit.prevent="submit">
                        <h2 class="font-display text-3xl font-semibold text-heritage-navy">Request account opening</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500">Complete the form and our team will follow up.</p>

                        <div v-if="$page.props.flash?.success" class="mt-6 bg-green-50 p-4 text-sm text-green-700">
                            {{ $page.props.flash.success }}
                        </div>

                        <div class="mt-8 grid gap-5 md:grid-cols-2">
                            <label class="block">
                                <span class="text-sm font-semibold text-heritage-navy">Full Name *</span>
                                <input v-model="form.name" type="text" class="mt-2 w-full border px-4 py-3 outline-none focus:border-heritage-gold" :class="errors.name ? 'border-red-500' : 'border-slate-300'" />
                                <span v-if="errors.name" class="mt-1 block text-xs text-red-600">{{ errors.name }}</span>
                            </label>

                            <label class="block">
                                <span class="text-sm font-semibold text-heritage-navy">Email *</span>
                                <input v-model="form.email" type="email" class="mt-2 w-full border px-4 py-3 outline-none focus:border-heritage-gold" :class="errors.email ? 'border-red-500' : 'border-slate-300'" />
                                <span v-if="errors.email" class="mt-1 block text-xs text-red-600">{{ errors.email }}</span>
                            </label>

                            <label class="block">
                                <span class="text-sm font-semibold text-heritage-navy">Phone *</span>
                                <input v-model="form.phone" type="tel" class="mt-2 w-full border px-4 py-3 outline-none focus:border-heritage-gold" :class="errors.phone ? 'border-red-500' : 'border-slate-300'" />
                                <span v-if="errors.phone" class="mt-1 block text-xs text-red-600">{{ errors.phone }}</span>
                            </label>

                            <label class="block">
                                <span class="text-sm font-semibold text-heritage-navy">Account Type *</span>
                                <select v-model="form.account_type" class="mt-2 w-full border px-4 py-3 outline-none focus:border-heritage-gold" :class="errors.account_type ? 'border-red-500' : 'border-slate-300'">
                                    <option>Individual / Personal</option>
                                    <option>Corporate / Institutional</option>
                                    <option>Professional / High Net Worth</option>
                                </select>
                                <span v-if="errors.account_type" class="mt-1 block text-xs text-red-600">{{ errors.account_type }}</span>
                            </label>
                        </div>

                        <label class="mt-5 block">
                            <span class="text-sm font-semibold text-heritage-navy">Additional Information</span>
                            <textarea v-model="form.message" rows="5" class="mt-2 w-full border border-slate-300 px-4 py-3 outline-none focus:border-heritage-gold" />
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
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center bg-heritage-navy text-xs font-bold text-heritage-gold">{{ item[0] }}</div>
                                <div>
                                    <h3 class="font-semibold text-heritage-navy">{{ item[1] }}</h3>
                                    <p class="mt-1 text-sm leading-6 text-slate-600">{{ item[2] }}</p>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>

                <div class="mt-16 border-t border-slate-200 pt-12">
    <p class="text-sm font-semibold uppercase tracking-[0.18em] mb-6">Or start a full application</p>
    <div class="grid gap-4 md:grid-cols-3 lg:grid-cols-5">
        <a href="/open-account/individual"  class='block border border-slate-200 rounded-xl p-6 text-center hover:border-heritage-gold hover:bg-amber-50 transition'>
            <span class='block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2'>Individual / Personal</span>
            <span class='block text-heritage-navy'>Individual Account</span>
        </a>
        <a href="/open-account/corporate" class='block border-slate-200 rounded-xl p-6 text-center hover:border-heritage-gold hover:bg-amber-50 transition'>
            <span class='block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2'>Corporate / Institutional</span>
            <span class='block font-semibold text-heritage-navy'>Corporate Account</span>
        </a>
        <a href="/open-account/joint" class='block border border-slate-200 rounded-xl p-6 text-center hover:border-heritage-gold hover:bg-amber-50 transition'>
            <span class='block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2'>Joint</span>
            <span class='block font-semibold text-heritage-navy'>Joint Account</span>
        </a>
        <a href="/open-account/minor" class='block border border-slate-200 rounded-xl p-6 text-center hover:border-heritage-gold hover:bg-amber-50 transition'>
            <span class='block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2'>Minor / Child</span>
            <span class='block font-semibold text-heritage-navy'>Minor Account</span>
        </a>
        <a href="/open-account/estate" class='block border border-slate-200 rounded-xl p-6 text-center hover:border-heritage-gold hover:bg-amber-50 transition'>
            <span class='block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2'>Estate / Trust</span>
            <span class='block font-semibold text-heritage-navy'>Estate Account</span>
        </a>
    </div>
    <p class='mt-6 text-xs text-slate-500 text-center'>Prefer to be contacted first? Use the request form above and our team will reach out.</p>
</div>
            </div>
        </section>
    </PublicLayout>
</template>
