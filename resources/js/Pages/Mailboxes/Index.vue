<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      Mailboxes
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
      <inertia-link class="btn-indigo" :href="route('mailboxes.create')">
        <span>Add</span>
        <span class="hidden md:inline">Mailbox</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">
            username
          </th>
          <th class="px-6 pt-6 pb-4">
            Host
          </th>
          <th class="px-6 pt-6 pb-4">
            Port
          </th>
          <th class="px-6 pt-6 pb-4">
            Encryption
          </th>
          <th class="px-6 pt-6 pb-4">
            # Messages
          </th>
          <th class="px-6 pt-6 pb-4">
            Active
          </th>
          <th class="px-6 pt-6 pb-4" />
        </tr>
        <tr v-for="mailbox in mailboxes" :key="mailbox.id" class="hover:bg-grey-lightest focus-within:bg-grey-lightest">
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo" :href="route('mailboxes.edit', mailbox.id)">
              {{ mailbox.username }}
              <icon v-if="mailbox.deleted_at" name="trash" class="flex-no-shrink w-3 h-3 fill-grey ml-2" />
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo" :href="route('mailboxes.edit', mailbox.id)">
              {{ mailbox.host }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo" :href="route('mailboxes.edit', mailbox.id)">
              {{ mailbox.port }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo" :href="route('mailboxes.edit', mailbox.id)">
              {{ mailbox.encryption }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo" :href="route('mailboxes.edit', mailbox.id)">
              {{ mailbox.message_count }}
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo" :href="route('mailboxes.edit', mailbox.id)">
              {{ mailbox.active ? 'yes' : 'no' }}
            </inertia-link>
          </td>
          <td class="border-t w-px">
            <div class="flex items-end float-right">
              <button :disabled="sending" class="btn-indigo px-4 py-3 pt-2 mr-2" @click="fetchMail(mailbox)">
                <div v-if="sending" class="btn-spinner mr-2" />
                Fetch Emails
              </button>
              <inertia-link class="btn-indigo px-4 py-3 pt-2 mr-2" :href="route('mailboxes.edit', mailbox.id)" tabindex="-1" @click="fetch(mailbox.id)">
                <span>Edit</span>
                <!-- <icon name="cheveron-right" class="block w-6 h-6 fill-grey" /> -->
              </inertia-link>
            </div>
          </td>
        </tr>
        <tr v-if="!mailboxes || !mailboxes.data || !mailboxes.data.length === 0">
          <td class="border-t px-6 py-4" colspan="5">
            No mailboxes found.
          </td>
        </tr>
      </table>
    </div>
    <pagination :links="mailboxes.links" />
  </div>
</template>

<script>
import _ from 'lodash'
import { Icon, Layout, Pagination, SearchFilter } from '@/Shared'
export default {
  metaInfo: { title: 'Mailboxes' },
  layout: (h, page) => h(Layout, [page]),
  components: {
    Icon,
    Pagination,
    SearchFilter
  },
  props: {
    mailboxes: Array,
    filters: Object
  },
  data () {
    return {
      sending: false,
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
        this.$inertia.replace(this.route('mailboxes', Object.keys(query).length ? query : { remember: 'forget' }))
      }, 150),
      deep: true
    }
  },
  methods: {
    reset () {
      this.form = _.mapValues(this.form, () => null)
    },
    fetchMail (mailbox) {
      this.sending = true
      this.$inertia.post(this.route('mailboxes.fetch', mailbox.id), function () {
        this.sending = false
      })
    }
  }
}
</script>
