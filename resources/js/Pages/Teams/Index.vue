<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      Users
    </h1>
    <div class="mb-6 flex justify-between items-center">
      <!-- <search-filter v-model="form.search" class="w-full max-w-sm mr-4" @reset="reset">
        <label class="block text-grey-darkest">Role:</label>
        <select v-model="form.role" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="user">
            User
          </option>
          <option value="owner">
            Owner
          </option>
        </select>
        <label class="mt-4 block text-grey-darkest">Trashed:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">
            With Trashed
          </option>
          <option value="only">
            Only Trashed
          </option>
        </select>
      </search-filter> -->
      <inertia-link class="btn-indigo" :href="route('teams.create')">
        <span>Create</span>
        <span class="hidden md:inline">Team</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">
            Name
          </th>
          <th class="px-6 pb-4">
            Slug
          </th>
          <th class="px-6 pb-4">
            Owner
          </th>
          <th class="px-6 pb-4 text-center ">
            Actions
          </th>
        </tr>
        <tr v-for="team in teams" :key="team.id" class="hover:bg-grey-lightest focus-within:bg-grey-lightest">
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo" :href="route('teams.edit', team.id)">
              <img v-if="team.logo" class="block w-5 h-5 rounded-full mr-2 -my-2" :src="team.logo">
              {{ team.name }}
              <icon v-if="team.deleted_at" name="trash" class="flex-no-shrink w-3 h-3 fill-grey ml-2" />
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('teams.edit', team.id)" tabindex="-1">
              {{ team.email }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('teams.edit', team.id)" tabindex="-1">
              {{ team.owner }}
            </inertia-link>
          </td>
          <td class="border-t">
            <div class="flex items-end float-right">
              <inertia-link class="btn-indigo mt-2 px-4 py-3 pt-2 mr-2" :href="route('teams.edit', team.id)" tabindex="-1">
                <span>Edit</span>
              <!-- <icon name="cheveron-right" class="w-2 h-4 fill-grey" /> -->
              </inertia-link>
            </div>
          </td>
        </tr>
        <tr v-if="teams.length === 0">
          <td class="border-t px-6 py-4" colspan="4">
            No users found.
          </td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
import _ from 'lodash'
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import SearchFilter from '@/Shared/SearchFilter'

export default {
  metaInfo: { title: 'Users' },
  layout: (h, page) => h(Layout, [page]),
  components: {
    Icon,
    SearchFilter
  },
  props: {
    teams: Array,
    permissions: Object,
    filters: Object
  },
  data () {
    return {
      form: {
        search: this.filters.search,
        role: this.filters.role,
        trashed: this.filters.trashed
      }
    }
  },
  watch: {
    form: {
      handler: _.throttle(function () {
        const query = _.pickBy(this.form)
        this.$inertia.replace(this.route('users', Object.keys(query).length ? query : { remember: 'forget' }))
      }, 150),
      deep: true
    }
  },
  methods: {
    reset () {
      this.form = _.mapValues(this.form, () => null)
    }
  }
}
</script>
