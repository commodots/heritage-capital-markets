<script setup>
import { computed, ref, onMounted } from 'vue'
import { router, useForm, usePage } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'

const props = defineProps({
  accountType: { type: String, required: true },
  accountTypes: { type: Array, default: () => [] },
  formSchema: { type: Object, required: true },
})

const page = usePage()
const stepTitles = computed(() => props.formSchema.stepTitles)
const fieldLabels = computed(() => props.formSchema.fieldLabels)
const personFieldLabels = computed(() => props.formSchema.personFieldLabels)
const personFields = computed(() => props.formSchema.personFields)
const step = ref(1)
const applicationId = ref(null)
const reference = ref('')
const errorMessage = ref('')

function emptyPerson() {
  return {
    name: '', residential_address: '', lga: '', city: '', state: '', country: '',
    telephone: '', email: '', date_of_birth: '', nationality: 'Nigerian',
    id_type: 'NIN', id_number: '', id_expiry: '',
    state_of_origin: '', lga_of_origin: '', occupation: '', next_of_kin_name: '',
    next_of_kin_relationship: '', next_of_kin_address: '', next_of_kin_telephone: '',
    next_of_kin_email: '', signature_mandate: '',
  }
}

const form = useForm({
  surname: '', first_name: '', other_names: '', gender: '', date_of_birth: '', mother_maiden_name: '',
  telephone: '', email: '', occupation: '', nationality: 'Nigerian', state_of_origin: '', lga: '',
  id_type: 'NIN', id_number: '', id_expiry: '', residential_address: '', contact_address: '',
  next_of_kin_name: '', next_of_kin_relationship: '', next_of_kin_telephone: '',
  next_of_kin_email: '', next_of_kin_address: '', next_of_kin_occupation: '',
  bank_name: '', bank_branch: '', bank_account_number: '', bank_account_name: '',
  account_type: 'Savings', account_opened_at: '', direct_cash_settlement: '', bvn: '', chn: '',
  company_name: '', rc_number: '', date_of_registration: '', nature_of_business: '',
  business_address: '', directors: [emptyPerson()],
  joint_account_name: '', joint_address: '', joint_contact_number: '',
  joint_applicants: [emptyPerson(), emptyPerson()],
  minor_name: '', guardian_name: '', guardian_relationship: '', guardian_telephone: '',
  guardian_occupation: '', estate_name: '', deceased_date_of_birth: '', estate_address: '',
  administrators: [emptyPerson()],
  additional_information: '',
  declaration_accepted: false,
  passport_photo: null, identity_document: null, utility_bill: null, birth_certificate: null, signature: null,
})

const fieldErrors = ref({})
const nationalities = [
  'Nigerian', 'Ghanaian', 'Kenyan', 'South African', 'Ugandan', 'Tanzanian',
  'Cameroonian', 'Sierra Leonean', 'Liberian', 'Senegalese', 'British',
  'American', 'Canadian', 'Australian', 'Chinese', 'Indian', 'French',
  'German', 'Irish', 'Italian', 'Dutch', 'Spanish', 'Japanese', 'Other',
]
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

function isRequiredField(key) {
  return (props.formSchema.requiredFieldsByStep[step.value - 1] || []).includes(key)
}

function isRequiredPersonField(key) {
  return props.formSchema.requiredPersonFields.includes(key)
}

const visibleSections = computed(() => props.formSchema.fieldsByStep.flat())
const currentStepFields = computed(() => props.formSchema.fieldsByStep[step.value - 1] || [])

function addPerson(listKey) {
  if (form[listKey].length < 20) form[listKey].push(emptyPerson())
}

function removePerson(listKey, index) {
  if (form[listKey].length > 1) form[listKey].splice(index, 1)
}

function isPhoneValid(value) {
  const normalized = value.replace(/[\s()-]/g, '')
  return /^(?:0[789]\d{9}|\+234[789]\d{9}|\+[1-9]\d{7,14})$/.test(normalized)
}

function validateCurrentStep() {
  fieldErrors.value = {}
  let valid = true
  const type = props.accountType
  const requiredKeys = props.formSchema.requiredFieldsByStep[step.value - 1] || []

  for (const key of requiredKeys) {
    const val = form[key]
    if (val === null || val === undefined || val === '' || (Array.isArray(val) && val.length === 0)) {
      fieldErrors.value[key] = `${fieldLabels.value[key] || key} is required.`
      valid = false
    }
  }

  for (const key of ['email', 'next_of_kin_email']) {
    if (form[key] && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form[key])) {
      fieldErrors.value[key] = 'Enter a valid email address.'
      valid = false
    }
  }
  for (const key of ['telephone', 'next_of_kin_telephone', 'guardian_telephone', 'joint_contact_number']) {
    if (form[key] && !isPhoneValid(form[key])) {
      fieldErrors.value[key] = 'Enter a valid telephone number, e.g. 08012345678 or +2348012345678.'
      valid = false
    }
  }
  if (form.bvn && !/^\d{11}$/.test(form.bvn)) {
    fieldErrors.value.bvn = 'BVN must be exactly 11 digits.'
    valid = false
  }
  if (form.bank_account_number && !/^\d{10}$/.test(form.bank_account_number)) {
    fieldErrors.value.bank_account_number = 'Bank account number must be 10 digits.'
    valid = false
  }
  if (['individual', 'minor'].includes(type) && form.id_type === 'NIN' && form.id_number && !/^\d{11}$/.test(form.id_number)) {
    fieldErrors.value.id_number = 'NIN must be exactly 11 digits.'
    valid = false
  }

  for (const listKey of ['directors', 'administrators', 'joint_applicants']) {
    if (!currentStepFields.value.includes(listKey)) continue
    for (const [index, person] of form[listKey].entries()) {
      for (const field of personFields.value) {
        const errorKey = `${listKey}.${index}.${field}`
        if (person[field] && ['email', 'next_of_kin_email'].includes(field) && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(person[field])) {
          fieldErrors.value[errorKey] = 'Enter a valid email address.'
          valid = false
        }
        if (person[field] && ['telephone', 'next_of_kin_telephone'].includes(field) && !isPhoneValid(person[field])) {
          fieldErrors.value[errorKey] = 'Enter a valid telephone number.'
          valid = false
        }
      }
      if (person.id_type === 'NIN' && person.id_number && !/^\d{11}$/.test(person.id_number)) {
        fieldErrors.value[`${listKey}.${index}.id_number`] = 'NIN must be exactly 11 digits.'
        valid = false
      }
      for (const field of props.formSchema.requiredPersonFields) {
        if (!person[field]?.trim()) {
          fieldErrors.value[`${listKey}.${index}.${field}`] = `${personFieldLabels.value[field]} is required.`
          valid = false
        }
      }
    }
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
    <SeoHead :title="`Open ${formSchema.accountLabel}`"
      description="Complete the Heritage Capital Markets account opening application."
      :canonical-path="`/open-account/${accountType}`" />
    <section class="bg-heritage-navy px-6 py-14 text-white md:py-20">
      <div class="container-heritage max-w-5xl">
        <p class="text-xs font-semibold uppercase tracking-[.22em] text-heritage-gold">Investor Services · Account
          Opening</p>
        <h1 class="mt-3 font-display text-4xl font-semibold md:text-5xl">{{ formSchema.accountLabel }}</h1>
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
          <p class="mt-2 text-sm text-slate-500">Complete the required fields marked with <span class="font-bold text-red-600">*</span>.</p>
          <div v-if="step < 4" class="mt-6 grid gap-5 md:grid-cols-2">
            <template v-for="key in currentStepFields" :key="key">
              <section v-if="['directors', 'administrators', 'joint_applicants'].includes(key)"
                class="space-y-4 md:col-span-2">
                <div class="flex flex-wrap items-center justify-between gap-3">
                  <h3 class="font-semibold text-heritage-navy">
                    {{ fieldLabels[key] }} <span v-if="isRequiredField(key)" class="text-red-600">*</span>
                  </h3>
                  <button v-if="key === 'directors' || key === 'administrators'" type="button"
                    class="border border-heritage-navy px-4 py-2 text-sm font-semibold text-heritage-navy"
                    :disabled="form[key].length >= 20" @click="addPerson(key)">
                    Add {{ key === 'administrators' ? 'administrator' : 'director' }}
                  </button>
                </div>
                <article v-for="(person, index) in form[key]" :key="index" class="rounded-lg border border-slate-200 p-4">
                  <div class="mb-4 flex items-center justify-between">
                    <h4 class="font-semibold text-heritage-navy">
                      {{ key === 'joint_applicants' ? `Party ${index + 1}` : `${key === 'administrators' ? 'Administrator' : 'Director'} ${index + 1}` }}
                    </h4>
                    <button v-if="key !== 'joint_applicants' && form[key].length > 1" type="button"
                      class="text-xs font-semibold text-red-700" @click="removePerson(key, index)">Remove</button>
                  </div>
                  <div class="grid gap-4 md:grid-cols-2">
                    <label v-for="field in personFields" :key="field" class="block">
                      <span class="text-sm font-semibold text-heritage-navy">
                        {{ personFieldLabels[field] }} <span v-if="isRequiredPersonField(field)" class="text-red-600">*</span>
                      </span>
                      <select v-if="field === 'nationality'" v-model="person[field]" :required="isRequiredPersonField(field)" class="mt-2 w-full border border-slate-300 px-3 py-3">
                        <option v-for="nationality in nationalities" :key="nationality" :value="nationality">{{ nationality }}</option>
                      </select>
                      <select v-else-if="field === 'id_type'" v-model="person[field]" :required="isRequiredPersonField(field)" class="mt-2 w-full border border-slate-300 px-3 py-3">
                        <option value="NIN">NIN</option><option value="International Passport">International Passport</option>
                        <option value="Driver’s Licence">Driver’s Licence</option><option value="Voter’s Card">Voter’s Card</option>
                      </select>
                      <textarea v-else-if="field.includes('address')" v-model="person[field]" rows="2" :required="isRequiredPersonField(field)" class="mt-2 w-full border border-slate-300 px-3 py-3" />
                      <input v-else v-model="person[field]" :type="field === 'email' || field.endsWith('_email') ? 'email' : field.includes('telephone') ? 'tel' : (field.includes('date') || field === 'id_expiry') ? 'date' : 'text'"
                        :required="isRequiredPersonField(field)"
                        :placeholder="field.includes('telephone') ? '08012345678 or +2348012345678' : ''"
                        class="mt-2 w-full border px-3 py-3"
                        :class="fieldErrors[`${key}.${index}.${field}`] ? 'border-red-500' : 'border-slate-300'"
                        @input="clearFieldError(`${key}.${index}.${field}`)" />
                      <span v-if="fieldErrors[`${key}.${index}.${field}`]" class="mt-1 block text-xs text-red-600">
                        {{ fieldErrors[`${key}.${index}.${field}`] }}
                      </span>
                    </label>
                  </div>
                </article>
                <p v-if="fieldErrors[key]" class="text-xs text-red-600">{{ fieldErrors[key] }}</p>
              </section>
              <label v-else-if="fieldLabels[key]" class="block"
                :class="['additional_information', 'residential_address', 'contact_address', 'next_of_kin_address', 'business_address', 'joint_address', 'estate_address'].includes(key) ? 'md:col-span-2' : ''">
                <span class="text-sm font-semibold text-heritage-navy">
                  {{ fieldLabels[key] }} <span v-if="isRequiredField(key)" class="text-red-600">*</span>
                </span>
                <select v-if="key === 'gender'" v-model="form[key]" class="mt-2 w-full border border-slate-300 px-3 py-3" @change="clearFieldError(key)">
                  <option value="">Select gender</option><option>Female</option><option>Male</option><option>Prefer not to say</option>
                </select>
                <select v-else-if="key === 'nationality'" v-model="form[key]" required class="mt-2 w-full border border-slate-300 px-3 py-3" @change="clearFieldError(key)">
                  <option v-for="nationality in nationalities" :key="nationality" :value="nationality">{{ nationality }}</option>
                </select>
                <select v-else-if="key === 'id_type'" v-model="form[key]" :required="isRequiredField(key)" class="mt-2 w-full border border-slate-300 px-3 py-3" @change="clearFieldError(key)">
                  <option value="NIN">NIN</option><option value="International Passport">International Passport</option>
                  <option value="Driver’s Licence">Driver’s Licence</option><option value="Voter’s Card">Voter’s Card</option>
                </select>
                <select v-else-if="key === 'direct_cash_settlement'" v-model="form[key]" :required="isRequiredField(key)" class="mt-2 w-full border border-slate-300 px-3 py-3" @change="clearFieldError(key)">
                  <option value="">Select an option</option><option value="yes">Yes</option><option value="no">No</option>
                </select>
                <select v-else-if="key === 'account_type'" v-model="form[key]" class="mt-2 w-full border border-slate-300 px-3 py-3" @change="clearFieldError(key)">
                  <option value="Savings">Savings</option><option value="Current">Current</option>
                </select>
                <textarea v-else-if="key.includes('address') || key === 'additional_information'" v-model="form[key]" rows="3" :required="isRequiredField(key)"
                  class="mt-2 w-full border border-slate-300 px-3 py-3" @input="clearFieldError(key)" />
                <input v-else v-model="form[key]" :type="['date_of_birth','deceased_date_of_birth','id_expiry','date_of_registration','account_opened_at'].includes(key) ? 'date' : (key.includes('email') ? 'email' : (key.includes('telephone') || key === 'joint_contact_number' ? 'tel' : 'text'))"
                  :required="isRequiredField(key)"
                  :inputmode="['bvn','id_number','bank_account_number'].includes(key) ? 'numeric' : (key.includes('telephone') || key === 'joint_contact_number' ? 'tel' : undefined)"
                  :maxlength="key === 'bvn' || (key === 'id_number' && form.id_type === 'NIN') ? 11 : (key === 'bank_account_number' ? 10 : undefined)"
                  :placeholder="key === 'bvn' ? 'Enter 11-digit BVN' : (key === 'id_number' && form.id_type === 'NIN' ? 'Enter 11-digit NIN' : (key === 'bank_account_number' ? 'Enter 10-digit account number' : (key.includes('telephone') || key === 'joint_contact_number' ? '08012345678 or +2348012345678' : '')))"
                  class="mt-2 w-full border px-3 py-3"
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
