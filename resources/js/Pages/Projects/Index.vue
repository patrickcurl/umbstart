<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      Organizations
    </h1>
    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-sm mr-4" @reset="reset">
        <label class="block text-grey-darkest">Trashed:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">
            With Trashed
          </option>
          <option value="only">
            Only Trashed
          </option>
        </select>
      </search-filter>
      <inertia-link class="btn-indigo" :href="route('teams.create')">
        <span>Create</span>
        <span class="hidden md:inline">Organization</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">
            Name
          </th>
          <th class="px-6 pt-6 pb-4">
            City
          </th>
          <th class="px-6 pt-6 pb-4" colspan="2">
            Phone
          </th>
        </tr>
        <tr v-for="team in teams.data" :key="team.id" class="hover:bg-grey-lightest focus-within:bg-grey-lightest">
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo" :href="route('teams.edit', team.id)">
              {{ team.name }}
              <icon v-if="team.deleted_at" name="trash" class="flex-no-shrink w-3 h-3 fill-grey ml-2" />
            </inertia-link>
          </td>
          <td class="border-t w-px">
            <inertia-link class="px-4 flex items-center" :href="route('teams.edit', team.id)" tabindex="-1">
              <icon name="cheveron-right" class="block w-6 h-6 fill-grey" />
            </inertia-link>
          </td>
        </tr>
        <tr v-if="teams.data.length === 0">
          <td class="border-t px-6 py-4" colspan="4">
            No teams found.
          </td>
        </tr>
      </table>
    </div>
    <pagination :links="teams.links" />
  </div>
</template>

<script>
import _ from 'lodash'
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import Pagination from '@/Shared/Pagination'
import SearchFilter from '@/Shared/SearchFilter'

export default {
  metaInfo: { title: 'Organizations' },
  layout: (h, page) => h(Layout, [page]),
  components: {
    Icon,
    Pagination,
    SearchFilter
  },
  props: {
    teams: Object,
    filters: Object
  },
  data () {
    return {
      form: {
        search: this.filters.search || '',
        trashed: this.filters.trashed || ''
      }
    }
  },
  watch: {
    form: {
      handler: _.throttle(function () {
        const query = _.pickBy(this.form)
        this.$inertia.replace(this.route('teams', Object.keys(query).length ? query : { remember: 'forget' }))
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
