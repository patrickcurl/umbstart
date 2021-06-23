<template>
  <div class="p-6 bg-indigo-darker min-h-screen flex justify-center items-center">
    <div class="w-full max-w-sm">
      <umbric-logo class="block mx-auto w-full max-w-xs fill-white" height="50" />
      <form class="mt-8 bg-white rounded-lg shadow-lg overflow-hidden" @submit.prevent="submit">
        <div class="px-10 py-12">
          <h1 class="text-center font-bold text-3xl">
            Welcome Back!
          </h1>
          <div class="mx-auto mt-6 w-24 border-b-2" />
          <text-input id="firstNameInput" v-model="form.first_name" :errors="$page.errors.first_name" class="mt-10" label="First name" type="text" autofocus autocapitalize="off" />
          <text-input id="lastNameInput" v-model="form.last_name" :errors="$page.errors.last_name" class="mt-10" label="Last name" type="text" autofocus autocapitalize="off" />
          <text-input id="emailInput" v-model="form.email" :errors="$page.errors.email" class="mt-10" label="Email" type="email" autofocus autocapitalize="off" />
          <text-input id="passwordInput" v-model="form.password" class="mt-6" :errors="$page.errors.password" label="Password" type="password" />
          <text-input id="passwordConfirmationInput" v-model="form.password_confirmation" class="mt-6" label="Confirm Password" :errors="$page.errors.password" type="password" />
          <div class="mx-auto mt-10 max-w-sm text-center flex flex-wrap justify-center">
            <div class="flex items-center mr-4 mb-4">
              <input id="createTeam" v-model="form.team_type" type="radio" name="teamType" class="hidden" :checked="form.team_type==='create'" value="create">
              <label for="createTeam" class="flex items-center cursor-pointer">
                <span class="w-4 h-4 inline-block mr-1 border border-grey" />
                Create Team</label>
            </div>
            <div class="flex items-center mr-4 mb-4">
              <input id="joinTeam" v-model="form.team_type" type="radio" name="teamType" class="hidden" value="join" :checked="form.team_type==='join'">
              <label for="joinTeam" class="flex items-center cursor-pointer">
                <span class="w-4 h-4 inline-block mr-1 border border-grey" />
                Use Invite Code</label>
            </div>
          </div>

          <text-input v-if="form.team_type === 'create'" id="teamName" v-model="form.team_name" class="mt-6" label="Team Name" type="text" :errors="$page.errors.team_name" />
          <text-input v-else id="inviteCode" v-model="form.invite_code" :errors="$page.errors.invite_code" class="mt-6" label="Team invite code" type="text" />
        </div>
        <div class="px-10 py-4 bg-grey-lightest border-t border-grey-lighter flex justify-between items-center">
          <loading-button id="formSubmit" :loading="sending" class="btn-indigo" type="submit">
            Register
          </loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import LoadingButton from '@/Shared/LoadingButton'
import UmbricLogo from '@/Shared/UmbricLogo'

import TextInput from '@/Shared/TextInput'

export default {
  metaInfo: { title: 'Register' },
  components: {
    LoadingButton,
    UmbricLogo,
    TextInput
  },
  props: {
    errors: {
      type: Object,
      default: function () {
        return {}
      }
    }
  },
  data () {
    return {
      sending: false,
      form: {
        email: null,
        password: null,
        password_confirmation: null,
        team_type: 'create',
        invite_code: null,
        team_name: null
      }
    }
  },
  methods: {
    submit () {
      this.sending = true
      this.$inertia.post(this.route('register'), {
        email: this.form.email,
        password: this.form.password,
        password_confirmation: this.form.password_confirmation,
        team_type: this.form.team_type,
        team_name: this.form.team_name || '',
        invite_code: this.form.invite_code || ''
      }).then(() => { this.sending = false })
    }
  }
}
</script>
