<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-light hover:text-indigo-dark" :href="route('teams.index')">
        Organizations
      </inertia-link>
      <span class="text-indigo-light font-medium">/</span>
      {{ form.name }}
    </h1>
    <trashed-message v-if="team.deleted_at" class="mb-6" @restore="restore">
      This organization has been deleted.
    </trashed-message>
    <div class="bg-white rounded shadow overflow-hidden max-w-lg">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.name" :errors="$page.errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Name" />
          <text-input v-model="form.slug" :errors="$page.errors.slug" class="pr-6 pb-8 w-full lg:w-1/2" label="Slug" />
        </div>
        <div class="px-8 py-4 bg-grey-lightest border-t border-grey-lighter flex items-center">
          <button v-if="!team.deleted_at" class="text-red hover:underline" tabindex="-1" type="button" @click="destroy">
            Delete Organization
          </button>
          <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">
            Update Organization
          </loading-button>
        </div>
      </form>
    </div>

    <!-- @TODO Add team user management -->
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInput from '@/Shared/SelectInput'
import TextInput from '@/Shared/TextInput'
import TrashedMessage from '@/Shared/TrashedMessage'

export default {
  metaInfo () {
    return { title: this.form.name }
  },
  layout: (h, page) => h(Layout, [page]),
  components: {
    Icon,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage
  },
  props: {
    team: Object
  },
  remember: 'form',
  data () {
    return {
      sending: false,
      form: {
        name: this.team.name,
        slug: this.team.slug
      }
    }
  },
  mounted () {
    this.form = this.team
  },
  methods: {
    submit () {
      this.sending = true
      this.$inertia.put(this.route('teams.update', this.team.id), this.form)
        .then(() => { this.sending = false })
    },
    destroy () {
      if (confirm('Are you sure you want to delete this team?')) {
        this.$inertia.delete(this.route('teams.destroy', this.team.id))
      }
    },
    restore () {
      if (confirm('Are you sure you want to restore this team?')) {
        this.$inertia.put(this.route('teams.restore', this.team.id))
      }
    }
  }
}
</script>
