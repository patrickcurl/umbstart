<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-light hover:text-indigo-dark" :href="route('teams')">
        Organizations
      </inertia-link>
      <span class="text-indigo-light font-medium">/</span> Create
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-lg">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.name" :errors="$page.errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Name" />
          <text-input v-model="form.slug" :errors="$page.errors.slug" class="pr-6 pb-8 w-full lg:w-1/2" label="Slug" />
          <text-input v-model="form.display_name" :errors="$page.errors.display_name" class="pr-6 pb-8 w-full lg:w-1/2" label="Display name" />
          <div class="px-8 py-4 bg-grey-lightest border-t border-grey-lighter flex justify-end items-center">
            <loading-button :loading="sending" class="btn-indigo" type="submit">
              Create Organization
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

export default {
  metaInfo: { title: 'Create Organization' },
  layout: (h, page) => h(Layout, [page]),
  components: {
    LoadingButton,

    TextInput
  },
  remember: 'form',
  data () {
    return {
      sending: false,
      form: {
        name: null,
        slug: null,
        display_name: null,
        description: null
      }
    }
  },
  methods: {
    submit () {
      this.sending = true
      this.$inertia.post(this.route('teams.store'), this.form)
        .then(() => {
          this.sending = false
        })
    }
  }
}
</script>
