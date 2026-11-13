<script setup>
import { computed, ref, onMounted } from 'vue'
import { router, useForm, usePage } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'

const props = defineProps({
  accountType: { type: String, required: true },
  accountTypes: { type: Array, default: () => [] }
})

const page = usePage()
const labels = {
  individual: 'Individual Account',
  corporate: 'Corporate Account',
  joint: 'Joint Account',
  minor: 'Minor Account',
  estate: 'Estate Account'
}

const stepTitles = ['Applicant details', 'Contact & Next of Kin', 'Bank & Account details', 'Documents & Declaration']
const step = ref(1)
const applicationId = ref(null)
const reference = ref('')
const errorMessage = ref('')

const form = useForm({
  surname: '', first_name: '', other_names: '', gender: '', date_of_birth: '', mother_maiden_name: '',
  telephone: '', email: '', occupation: '', nationality: 'Nigerian', state_of_origin: '', lga: '',
  id_type: 'NIN', id_number: '', id_expiry: '', residential_address: '', contact_address: '',
  next_of_kin_name: '', next_of_kin_relationship: '', next_of_kin_telephone: '', next_of_kin_occupation: '',
  bank_name: '', bank_account_number: '', bank_account_name: '', account_type: 'Savings', bvn: '', chn: '',
  company_name: '', rc_number: '', nature_of_business: '', joint_account_name: '', second_applicant_name: '',
  minor_name: '', guardian_name: '', guardian_relationship: '', estate_name: '', executor_name: '', probate_reference: '',
  additional_information: '',
  declaration_accepted: false,
  passport_photo: null, identity_document: null, utility_bill: null, birth_certificate: null, signature: null,
})

const fieldsByStep = {
  individual: [
    ['surname', 'first_name', 'other_names', 'gender', 'date_of_birth', 'mother_maiden_name', 'occupation', 'nationality', 'state_of_origin', 'lga', 'id_type', 'id_number', 'id_expiry'],
    ['telephone', 'email', 'residential_address', 'contact_address', 'next_of_kin_name', 'next_of_kin_relationship', 'next_of_kin_telephone', 'next_of_kin_occupation'],
    ['bank_name', 'bank_account_number', 'bank_account_name', 'account_type', 'bvn', 'chn', 'additional_information'],
  ],
  corporate: [
    ['company_name', 'rc_number', 'nature_of_business'],
    ['telephone', 'email', 'residential_address'],
    ['bank_name', 'bank_account_number', 'bank_account_name', 'additional_information'],
  ],
  joint: [
    ['joint_account_name', 'second_applicant_name', 'surname', 'first_name', 'other_names'],
    ['telephone', 'email', 'residential_address', 'contact_address', 'next_of_kin_name', 'next_of_kin_relationship', 'next_of_kin_telephone', 'next_of_kin_occupation'],
    ['bank_name', 'bank_account_number', 'bank_account_name', 'account_type', 'bvn', 'chn', 'additional_information'],
  ],
  minor: [
    ['minor_name', 'guardian_name', 'guardian_relationship', 'date_of_birth'],
    ['telephone', 'email', 'residential_address', 'contact_address'],
    ['bank_name', 'bank_account_number', 'bank_account_name', 'additional_information'],
  ],
  estate: [
    ['estate_name', 'executor_name', 'probate_reference'],
    ['telephone', 'email', 'residential_address', 'contact_address'],
    ['bank_name', 'bank_account_number', 'bank_account_name', 'additional_information'],
  ],
}

const visibleSections = computed(() => fieldsByStep[props.accountType].flat())
const currentStepFields = computed(() => fieldsByStep[props.accountType][step.value - 1] || [])

const fieldLabels = {
  surname: 'Surname', first_name: 'First Name', other_names: 'Other Names', gender: 'Gender', date_of_birth: 'Date of Birth', mother_maiden_name: 'Mother’s Maiden Name', telephone: 'Telephone', email: 'Email Address', occupation: 'Occupation', nationality: 'Nationality', state_of_origin: 'State of Origin', lga: 'LGA', id_type: 'ID Type', id_number: 'ID Number (e.g. NIN digits)', id_expiry: 'ID Expiry Date', residential_address: 'Residential Address', contact_address: 'Contact Address', next_of_kin_name: 'Next of Kin Full Name', next_of_kin_relationship: 'Next of Kin Relationship', next_of_kin_telephone: 'Next of Kin Telephone', next_of_kin_occupation: 'Next of Kin Occupation', bank_name: 'Settlement Bank Name', bank_account_number: 'Bank Account Number (10 digits)', bank_account_name: 'Bank Account Name (Must match BVN)', account_type: 'Account Category', bvn: 'Bank Verification Number (BVN - 11 digits)', chn: 'Clearing House Number (CHN if applicable)', company_name: 'Registered Company Name', rc_number: 'CAC Registration Number', nature_of_business: 'Nature of Business', joint_account_name: 'Joint Account Name', second_applicant_name: 'Second Applicant Full Name', minor_name: 'Minor’s Full Name', guardian_name: 'Parent/Guardian Full Name', guardian_relationship: 'Relationship to Minor', estate_name: 'Estate/Deceased Full Name', executor_name: 'Administrator/Executor Full Name', probate_reference: 'Probate/Letters of Administration Reference', additional_information: 'Additional Information / Special Instructions'
}

const requiredFieldsByStep = {
  individual: [
    ['surname', 'first_name', 'date_of_birth', 'nationality', 'id_type', 'id_number', 'id_expiry'],
    ['telephone', 'email', 'residential_address'],
    ['bank_name', 'bank_account_number', 'bank_account_name', 'bvn'],
  ],
  corporate: [
    ['company_name', 'rc_number', 'nature_of_business'],
    ['telephone', 'email', 'residential_address'],
    ['bank_name', 'bank_account_number', 'bank_account_name'],
  ],
  joint: [
    ['joint_account_name', 'second_applicant_name', 'surname', 'first_name'],
    ['telephone', 'email', 'residential_address'],
    ['bank_name', 'bank_account_number', 'bank_account_name'],
  ],
  minor: [
    ['minor_name', 'guardian_name', 'guardian_relationship', 'date_of_birth'],
    ['telephone', 'email', 'residential_address'],
    ['bank_name', 'bank_account_number', 'bank_account_name'],
  ],
  estate: [
    ['estate_name', 'executor_name', 'probate_reference'],
    ['telephone', 'email', 'residential_address'],
    ['bank_name', 'bank_account_number', 'bank_account_name'],
  ],
}

const fieldErrors = ref({})

onMounted(() => {
  const savedApplication = page.props.accountApplication

  if (savedApplication?.id) {
    applicationId.value = savedApplication.id
    reference.value = savedApplication.reference
    if (savedApplication.form_data) Object.assign(form, savedApplication.form_data)
    if (savedApplication.current_step) step.value = Math.min(4, Math.max(1, Number(savedApplication.current_step)))
  }
})

function clearFieldError(key) {
  if (fieldErrors.value[key]) delete fieldErrors.value[key]
}

function validateCurrentStep() {
  fieldErrors.value = {}
  let valid = true
  const type = props.accountType
  const requiredKeys = requiredFieldsByStep[type][step.value - 1] || []

  for (const key of requiredKeys) {
    const val = form[key]
    if (val === null || val === undefined || val === '') {
      fieldErrors.value[key] = `${fieldLabels[key] || key} is required.`
      valid = false
    }
  }

  if (form.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    fieldErrors.value['email'] = 'Enter a valid email address.'
    valid = false
  }
  if (form.bvn && !/^\d{11}$/.test(form.bvn)) {
    fieldErrors.value['bvn'] = 'BVN must be exactly 11 digits.'
    valid = false
  }
  if (form.bank_account_number && !/^\d{10}$/.test(form.bank_account_number)) {
    fieldErrors.value['bank_account_number'] = 'Bank account number must be 10 digits.'
    valid = false
  }
  if (type === 'individual' && form.id_type === 'NIN' && form.id_number && !/^\d{11}$/.test(form.id_number)) {
    fieldErrors.value.id_number = 'NIN must be exactly 11 digits.'
    valid = false
  }

  return valid
}

function setFile(key, event) {
  form[key] = event.target.files?.[0] ?? null
  clearFieldError(key)
}

function saveStep(nextStep) {
  errorMessage.value = ''
  if (!validateCurrentStep()) {
    errorMessage.value = 'Please complete all required fields correctly before continuing.'
    return
  }

  const payload = new FormData()
  payload.append('step', String(step.value))
  if (applicationId.value) payload.append('application_id', String(applicationId.value))

  const safeData = {}
  for (const key of visibleSections.value) {
    safeData[key] = form[key] ?? ''
  }
  payload.append('form_data', JSON.stringify(safeData))

  for (const key of ['passport_photo', 'identity_document', 'utility_bill', 'birth_certificate', 'signature']) {
    if (form[key] instanceof File) payload.append(`documents[${key}]`, form[key])
  }

  router.post(`/open-account/${props.accountType}/save-step`, payload, {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: (pageProps) => {
      // Pull app info safely from flash or shared controller response
      const saved = pageProps.props.accountApplication
      if (saved?.id) {
        applicationId.value = saved.id
        reference.value = saved.reference
      }
      if (nextStep) step.value = nextStep
    },
    onError: (errors) => {
      errorMessage.value = Object.values(errors)[0] || 'Please review your entries and try again.'
    }
  })
}

function submitApplication() {
  errorMessage.value = ''

  if (!form.declaration_accepted) {
    fieldErrors.value.declaration_accepted = 'You must accept the declaration to submit.'
    errorMessage.value = 'Please accept the declaration checkbox.'
    return
  }

  if (!applicationId.value) {
    errorMessage.value = 'Application session reference not found. Please go back to Step 3 and click Save & Continue.'
    return
  }

  const payload = new FormData()
  payload.append('application_id', String(applicationId.value))
  payload.append('declaration_accepted', form.declaration_accepted ? '1' : '0')

  for (const key of ['passport_photo', 'identity_document', 'utility_bill', 'birth_certificate', 'signature']) {
    if (form[key] instanceof File) payload.append(`documents[${key}]`, form[key])
  }

  router.post(`/open-account/${props.accountType}/submit`, payload, {
    forceFormData: true,
    preserveScroll: true,
    onError: (errors) => {
      errorMessage.value = Object.values(errors)[0] || 'Submission failed. Please verify required fields.'
    },
  })
}
</script>

<template>
  <PublicLayout>
    <SeoHead :title="`Open ${labels[accountType]}`"
      description="Complete the Heritage Capital Markets account opening application."
      :canonical-path="`/open-account/${accountType}`" />
    <section class="bg-heritage-navy px-6 py-14 text-white md:py-20">
      <div class="container-heritage max-w-5xl">
        <p class="text-xs font-semibold uppercase tracking-[.22em] text-heritage-gold">Investor Services · Account
          Opening</p>
        <h1 class="mt-3 font-display text-4xl font-semibold md:text-5xl">{{ labels[accountType] }}</h1>
        <p class="mt-4 max-w-3xl text-white/75">Complete the form in stages. You can save your progress and return to
          the remaining steps in this browser session.</p>
      </div>
    </section>
    <section class="bg-heritage-offwhite px-6 py-12 md:py-16">
      <div class="container-heritage max-w-5xl">
        <div class="mb-8 grid grid-cols-2 gap-3 md:grid-cols-4">
          <div v-for="(title, i) in stepTitles" :key="title" class="border p-4"
            :class="step === i + 1 ? 'border-heritage-gold bg-white' : 'border-slate-200 bg-white/60'">
            <p class="text-xs font-bold text-heritage-gold">STEP 0{{ i + 1 }}</p>
            <p class="mt-1 text-sm font-semibold text-heritage-navy">{{ title }}</p>
          </div>
        </div>
        <form class="bg-white p-6 shadow-sm md:p-10"
          @submit.prevent="step < 4 ? saveStep(step + 1) : submitApplication()">
          <div v-if="reference" class="mb-5 border border-green-200 bg-green-50 p-3 text-sm text-green-800">Application
            reference: <strong>{{ reference }}</strong></div>
          <div v-if="errorMessage" class="mb-5 border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ errorMessage
          }}</div>
          <h2 class="font-display text-2xl font-semibold text-heritage-navy">{{ stepTitles[step - 1] }}</h2>
          <p class="mt-2 text-sm text-slate-500">Fields and evidence requirements must be confirmed by Heritage before
            production release.</p>
          <div v-if="step === 1" class="mt-6 grid gap-5 md:grid-cols-2">
            <template v-for="key in currentStepFields" :key="key">
              <label v-if="fieldLabels[key]" class="block"><span class="text-sm font-semibold text-heritage-navy">{{
                fieldLabels[key] }}</span>
                <select v-if="key === 'gender'" v-model="form[key]"
                  class="mt-2 w-full border px-3 py-3 focus:border-heritage-gold focus:outline-none"
                  :class="fieldErrors[key] ? 'border-red-500' : 'border-slate-300'" @input="clearFieldError(key)">
                  <option value="">Select gender</option>
                  <option>Female</option>
                  <option>Male</option>
                  <option>Prefer not to say</option>
                </select>
                <select v-else-if="key === 'id_type'" v-model="form[key]"
                  class="mt-2 w-full border px-3 py-3 focus:border-heritage-gold focus:outline-none"
                  :class="fieldErrors[key] ? 'border-red-500' : 'border-slate-300'" @input="clearFieldError(key)">
                  <option>NIN</option>
                  <option>International Passport</option>
                  <option>Driver’s Licence</option>
                  <option>Voter’s Card</option>
                </select>
                <input v-else v-model="form[key]" @input="clearFieldError(key)"
                  :type="key === 'email' ? 'email' : (key.includes('date') || key === 'id_expiry' ? 'date' : 'text')"
                  class="mt-2 w-full border px-3 py-3 focus:border-heritage-gold focus:outline-none"
                  :class="fieldErrors[key] ? 'border-red-500' : 'border-slate-300'" />
                <span v-if="fieldErrors[key]" class="mt-1 block text-xs text-red-600">{{ fieldErrors[key] }}</span>
              </label>
            </template>
          </div>
          <div v-else-if="step === 2" class="mt-6 grid gap-5 md:grid-cols-2">
            <template v-for="key in currentStepFields" :key="key"><label v-if="fieldLabels[key]"
                class="block"><span class="text-sm font-semibold text-heritage-navy">{{ fieldLabels[key] }}</span><input
                  v-model="form[key]" :type="key === 'email' ? 'email' : 'text'" class="mt-2 w-full border px-3 py-3"
                  :class="fieldErrors[key] ? 'border-red-500' : 'border-slate-300'" /><span v-if="fieldErrors[key]"
                  class="mt-1 block text-xs text-red-600">{{ fieldErrors[key] }}</span></label></template>
          </div>
          <div v-else-if="step === 3" class="mt-6 grid gap-5 md:grid-cols-2">
            <template
              v-for="key in currentStepFields" :key="key">
              <label class="block" :class="key === 'additional_information' ? 'md:col-span-2' : 'md:col-span-1'">
                <span class="text-sm font-semibold text-heritage-navy">{{ fieldLabels[key] || key }}</span>

                <!-- Textarea for Additional Info -->
                <textarea v-if="key === 'additional_information'" v-model="form[key]" rows="4"
                  placeholder="Provide any additional account instructions or notes here..."
                  class="mt-2 w-full border border-slate-300 px-3 py-3 outline-none focus:border-heritage-gold"
                  @input="clearFieldError(key)"></textarea>

                <!-- Dropdown for Account Type -->
                <select v-else-if="key === 'account_type'" v-model="form[key]"
                  class="mt-2 w-full border border-slate-300 px-3 py-3 outline-none focus:border-heritage-gold"
                  @change="clearFieldError(key)">
                  <option value="Savings">Savings Account</option>
                  <option value="Current">Current Account</option>
                  <option value="Nominee">Nominee / Trading Account</option>
                </select>

                <!-- Standard Input with Specific Placeholders -->
                <input v-else v-model="form[key]" type="text"
                  :placeholder="key === 'bank_account_number' ? 'Enter 10-digit account number' : (key === 'bvn' ? 'Enter 11-digit BVN' : '')"
                  class="mt-2 w-full border px-3 py-3 outline-none focus:border-heritage-gold"
                  :class="fieldErrors[key] ? 'border-red-500' : 'border-slate-300'" @input="clearFieldError(key)" />

                <span v-if="fieldErrors[key]" class="mt-1 block text-xs text-red-600">{{ fieldErrors[key] }}</span>
              </label>
            </template>
          </div>
          <div v-else class="mt-6 space-y-5">
            <p class="text-sm text-slate-600">Upload applicable supporting documents. Files are limited to PDF, JPG and
              PNG,
              maximum 5 MB each.</p>
            <label
              v-for="item in [{ key: 'passport_photo', label: 'Passport Photograph' }, { key: 'identity_document', label: 'Means of Identification' }, { key: 'utility_bill', label: 'Recent Utility Bill' }, { key: 'birth_certificate', label: 'Birth Certificate (if applicable)' }, { key: 'signature', label: 'Signature' }]"
              :key="item.key" class="block"><span class="text-sm font-semibold text-heritage-navy">{{ item.label
              }}</span><input type="file" accept=".pdf,.jpg,.jpeg,.png" class="mt-2 block w-full border p-3 text-sm"
                :class="fieldErrors[item.key] ? 'border-red-500' : 'border-slate-300'"
                @change="setFile(item.key, $event)" /><span v-if="fieldErrors[item.key]"
                class="mt-1 block text-xs text-red-600">{{ fieldErrors[item.key] }}</span></label>
            <label class="flex gap-3 text-sm leading-6 text-slate-700"><input v-model="form.declaration_accepted"
                type="checkbox" class="mt-1" @input="clearFieldError('declaration_accepted')" required />I confirm that
              the information supplied
              is accurate and consent to
              Heritage Capital Markets
              processing it for account-opening review.</label><span v-if="fieldErrors.declaration_accepted"
              class="mt-1 block text-xs text-red-600">{{ fieldErrors.declaration_accepted }}</span>
          </div>
          <div class="mt-8 flex flex-wrap justify-between gap-3 border-t border-slate-200 pt-6">
            <button v-if="step > 1" type="button" class="border border-slate-300 px-5 py-3 text-sm font-semibold"
              @click="step--">Back</button>
            <a v-else href="/open-account"
              class="border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700">Change
              account type</a>
            <button type="submit" :disabled="form.processing" class="btn-gold disabled:opacity-60">{{ form.processing ?
              'Saving…'
              : (step < 4 ? 'Save & Continue' : 'Submit Application') }}</button>
          </div>
        </form>
      </div>
    </section>
  </PublicLayout>
</template>
