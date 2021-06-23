<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-light hover:text-indigo-dark" :href="route('mailboxes.index')">
        Mailboxes
      </inertia-link>
      <span class="text-indigo-light font-medium">/</span> Create
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-lg">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.username" :errors="$page.errors.username" class="pr-6 pb-8 w-full lg:w-1/2" label="Username" />
          <text-input v-model="form.password" :errors="$page.errors.password" class="pr-6 pb-8 w-full lg:w-1/2" label="Password" />
          <text-input v-model="form.host" :errors="$page.errors.host" class="pr-6 pb-8 w-full lg:w-1/2" label="Host" />
          <text-input v-model="form.port" :errors="$page.errors.port" class="pr-6 pb-8 w-full lg:w-1/2" label="Port" />
          <select-input v-model="form.encryption" :errors="$page.errors.encryption" class="pr-6 pb-8 w-full lg:w-1/2" label="Encryption">
            <option v-for="enc in encryptionTypes" :key="enc" :value="enc">
              {{ enc }}
            </option>
          </select-input>
          <div class="px-8 py-4 bg-grey-lightest border-t border-grey-lighter flex justify-end items-center">
            <loading-button :loading="sending" class="btn-indigo" type="submit">
              Add Mailbox
            </loading-button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
export default {
  metaInfo: { title: 'Add Mailbox' },
  layout: (h, page) => h(Layout, [page]),
  components: {
    LoadingButton,
    SelectInput,
    TextInput
  },
  remember: 'form',
  data () {
    return {
      encryptionTypes: ['none', 'ssl', 'tls', 'notls', 'starttls'],
      sending: false,
      form: {
        username: null,
        password: null,
        host: null,
        port: null,
        encryption: null
      }
    }
  },
  methods: {
    submit () {
      this.sending = true
      this.$inertia.post(this.route('mailboxes.store'), this.form)
        .then(() => {
          this.sending = false
        })
    }
  }
}
</script>
