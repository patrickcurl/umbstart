<template>
  <div>
    <div class="mb-4">
      <inertia-link class="flex items-center group py-3" :href="route('images.index')">
        <email-check class="w-4 h-4 mr-2" :class="isUrl('/images') ? 'fill-white' : 'fill-indigo-light group-hover:fill-white'" />
        <!-- <icon name="custom" :source="icons.email" class="w-4 h-4 mr-2" :class="isUrl('/images') ? 'fill-white' : 'fill-indigo-light group-hover:fill-white'" /> -->
        <div :class="isUrl('/images') ? 'text-white' : 'text-indigo-lighter group-hover:text-white'">
          Tag Admin
        </div>
      </inertia-link>
    </div>
    <div class="mb-4">
      <inertia-link class="flex items-center group py-3" :href="route('reports')">
        <icon name="printer" class="w-4 h-4 mr-2" :class="isUrl('reports') ? 'fill-white' : 'fill-indigo-light group-hover:fill-white'" />
        <div :class="isUrl('reports') ? 'text-white' : 'text-indigo-lighter group-hover:text-white'">
          Tag View
        </div>
      </inertia-link>
    </div>

    <div class="mb-4">
      <inertia-link class="flex items-center group py-3" :href="route('teams.index')">
        <icon name="office" class="w-4 h-4 mr-2" :class="isUrl('teams') ? 'fill-white' : 'fill-indigo-light group-hover:fill-white'" />
        <div :class="isUrl('teams') ? 'text-white' : 'text-indigo-lighter group-hover:text-white'">
          Organizations
        </div>
      </inertia-link>
    </div>
    <div class="mb-4">
      <inertia-link class="flex items-center group py-3" :href="route('users')">
        <icon name="office" class="w-4 h-4 mr-2" :class="isUrl('users') ? 'fill-white' : 'fill-indigo-light group-hover:fill-white'" />
        <div :class="isUrl('users') ? 'text-white' : 'text-indigo-lighter group-hover:text-white'">
          Users
        </div>
      </inertia-link>
    </div>
    <div class="mb-4">
      <inertia-link class="flex items-center group py-3" :href="route('mailboxes.index')">
        <email-check class="w-4 h-4 mr-2" :class="isUrl('/images') ? 'fill-white' : 'fill-indigo-light group-hover:fill-white'" />
        <!-- <icon name="custom" icon="email-check" class="w-4 h-4 mr-2" :class="isUrl('/mailboxes') ? 'fill-white' : 'fill-indigo-light group-hover:fill-white'" /> -->
        <div :class="isUrl('/mailboxes') ? 'text-white' : 'text-indigo-lighter group-hover:text-white'">
          Mailboxes
        </div>
      </inertia-link>
    </div>
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
const EmailCheck = () => import(/* webpackChunkName: "mdiIcons" */ 'mdi-vue/EmailCheck')
export default {
  components: {
    Icon,
    EmailCheck
  },
  props: {
    url: {
      type: String,
      default: ''
    }
  },
  data () {
    return {
    }
  },

  methods: {
    isUrl (...urls) {
      if (urls[0] === '') {
        return this.url === ''
      }

      return urls.filter(url => this.url.startsWith(url)).length
    }
  }
}
</script>
