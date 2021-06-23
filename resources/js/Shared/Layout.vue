<template>
  <div>
    <portal-target name="dropdown" slim />
    <div class="flex flex-col">
      <div class="h-screen flex flex-col" @click="hideDropdownMenus">
        <div class="md:flex">
          <div class="bg-indigo-darkest md:flex-no-shrink md:w-64 px-6 py-4 flex items-center justify-between md:justify-center">
            <inertia-link class="mt-1" href="/">
              <umbric-logo-png class="fill-white" width="120" height="28" />
            </inertia-link>
            <dropdown class="md:hidden" placement="bottom-end">
              <svg class="fill-white w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z" /></svg>
              <div slot="dropdown" class="mt-2 px-8 py-4 shadow-lg bg-indigo-darker rounded">
                <main-menu :url="url()" />
              </div>
            </dropdown>
          </div>
          <div class="bg-white border-b w-full p-4 md:py-0 md:px-12 text-sm md:text-base flex justify-between items-center">
            <div class="w-full">
              <!-- <search-filter v-model="form.search" :max-width="200" :taxonomy="filtered ? filtered.label : 'Search for...'" max-size="100" class="float-left" @reset="reset">
                <div v-for="(filter,i) in filters" :key="i" class="w-full bg-gray-500 h-12">
                  <button class="font-semibold  py-2 w-full px-4 border hover:border-transparent rounded" :class="[filter && filtered && filter.value === filtered.value ? 'opacity-45 bg-blue text-white hover:text-white hover:bg-orange-dark' : 'bg-transparent hover:bg-blue  hover:text-white hover:bg-white text-blue border-blue-500']" @click="filterVal(filter)">
                    {{ filter.label }}
                  </button>
                </div>
              </search-filter> -->
              <div class="flex flex-wrap my-2 mx-auto">
                <text-input v-model="form.search" :errors="$page.errors.search" class="pr-4 pt-6 pb-2 w-1/3" placeholder="Search for..." />
                <radio-buttons v-model="searchType" :choices="radioboxes" vertical class="pt-1" />
              </div>
            </div>
            <dropdown class="mt-1" placement="bottom-end">
              <div class="flex items-center cursor-pointer select-none group">
                <div class="text-grey-darkest group-hover:text-indigo-dark focus:text-indigo-dark mr-1 whitespace-no-wrap">
                  <span>{{ $page.auth.user.first_name }}</span>
                  <span class="hidden md:inline">{{ $page.auth.user.last_name }}</span>
                </div>
                <icon class="w-5 h-5 group-hover:fill-indigo-dark fill-grey-darkest focus:fill-indigo-dark" name="cheveron-down" />
              </div>
              <div slot="dropdown" class="mt-2 py-2 shadow-lg bg-white rounded text-sm">
                <inertia-link class="block px-6 py-2 hover:bg-indigo hover:text-white" :href="route('users.edit', $page.auth.user.id)">
                  My Profile
                </inertia-link>

                <inertia-link class="block px-6 py-2 hover:bg-indigo hover:text-white" :href="route('logout')" method="post">
                  Logout
                </inertia-link>
              </div>
            </dropdown>
          </div>
        </div>
        <div class="flex flex-grow overflow-hidden">
          <main-menu :url="url()" class="bg-indigo-darker flex-no-shrink w-64 p-8 hidden md:block overflow-y-auto" />
          <div class="w-full overflow-hidden px-4 py-4 md:p-3 overflow-y-auto" scroll-region>
            <flash-messages />
            <slot />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Dropdown, Icon, FlashMessages, MainMenu, RadioButtons, TextInput, UmbricLogoPng } from '@/Shared'
import { pickBy as _pickBy, throttle as _throttle } from 'lodash-es'
export default {
  components: {
    Dropdown,
    FlashMessages,
    Icon,
    MainMenu,
    UmbricLogoPng,
    RadioButtons,
    TextInput
  },
  remember: {
    data: ['form', 'filters']
  },
  data () {
    return {
      showUserMenu: false,
      showFilterDropdown: false,
      show: false,
      searchType: 'images',
      radioboxes: [
        { name: 'images', value: 'images', label: 'Images', checked: true },
        { name: 'users', value: 'users', label: 'Users', checked: false }
        // { name: 'organizations', value: 'organizations', label: 'for Organizations', checked: false }
      ],
      // accounts: null,
      filters: [
        { label: 'Users', value: 'users' },
        { label: 'Roles', value: 'roles' },
        { label: 'Organizations', value: 'teams' },
        { label: 'Mailboxes', value: 'mailboxes' },
        { label: 'Messages', value: 'messages' },
        { label: 'Images', value: 'images' }
      ],
      form: {
        search: this.$page.filters.search,
        type: this.$page.filters.type,
        role: this.$page.filters.role,
        trashed: this.$page.filters.trashed
      },
      filtered: ''
    }
  },

  watch: {
    form: {
      handler: _throttle(function () {
        const query = _pickBy(this.form)
        if (typeof this.form.search === 'string' && this.form.search.length > 1) {
          console.log(this.form.search)
          // console.log(this.route('impersonate.filter', query))
          // const searchType = this.filtered.value || 'tags'
          this.$inertia.visit(this.route(`${this.searchType}.index`, this.form.search), {
            method: 'get',
            preserveState: true,
            preserveScroll: true,
            only: ['images', 'permissions']
          })
          // this.$inertia.replace(this.route(`${searchType}`), {
          //   method: 'get',
          //   data: query,
          //   replace: false,
          //   preserveState: true,
          //   preserveScroll: false,
          //   only: []
          // }).then(() => {
          //   this.sending = false
          // })
          // console.log({
          //   url: `search.${searchType}`,
          //   data: query
          // })
          // .then(() => {
          //   this.sending = false
          //   console.log(JSON.stringify(this.$page))
          // })
        }
      }, 150),
      deep: true
    }

  },

  methods: {
    url () {
      return location.pathname.substr(1)
    },
    isFiltered (filter) {
      return this.filtered && this.filtered.value && this.filtered.value === filter.value
    },
    filterVal (filter) {
      this.filtered = filter
      this.hideDropdownMenus()
      this.$emit('close-search-dropdown')
    },
    toggle () {
      this.showUserMenu = !this.showUserMenu
      this.$emit('close-search-dropdown')
    },
    hideDropdownMenus () {
      this.showUserMenu = false
    },
    reset () {
      this.form = _.mapValues(this.form, () => null)
      this.filtered = null
    }
  }
}
</script>
