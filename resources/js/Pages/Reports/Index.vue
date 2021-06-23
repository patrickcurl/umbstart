<template>
  <div>
    <!-- <h1 class="font-bold text-2xl w-full" style="">
      Dashboard
    </h1> -->
    <div class="w-full mt-10 mx-auto px-10">
      <t-table class-name="table-auto" :headers="headers" :data="tags.data">
        <template v-slot:row="props">
          <tr :class="[props.trClass, props.rowIndex % 2 === 0 ? 'bg-gray-100' : '']">
            <td :class="props.tdClass">
              <inertia-link :href="`images/${props.row.name}`">
                {{ props.row.name }}
              </inertia-link>
            </td>
            <td :class="props.tdClass">
              {{ props.row.weight }}
              </span>
            </td>
            <td :class="props.tdClass">
              <t-button size="sm" variant="secondary">
                Edit
              </t-button>
            </td>
          </tr>
        </template>
        <template v-slot:row="props">
          <tr :class="[props.trClass, props.rowIndex % 2 === 0 ? 'bg-gray-100' : '']">
            <td :class="props.tdClass">
              {{ props.row.id }}
            </td>
            <td :class="props.tdClass">
              <a :href="`mailto: ${props.row.name}`">{{ props.row.name }}</a>
            </td>
            <td :class="props.tdClass">
              {{ props.row.weight }}
            </td>
            <td :class="props.tdClass">
              {{ props.row.team }}
            </td>
            <td :class="props.tdClass">
              {{ props.row.count }}
            </td>
          </tr>
        </template>
      </t-table>
      <div class="w-full">
        <pagination :links="tags.links" />
      </div>
      <!-- <vue-word-cloud
        :words="words"
        :color="([, weight]) => weight > 10 ? 'DeepPink' : weight > 5 ? 'RoyalBlue' : 'Indigo'"
        font-family="Roboto"
        class="w-full flex align-center items-center"
        style="width:640px;height:480px;position:relative;margin-top:-100px;"
      >
        <template slot-scope="{text, weight, word}">
          <div :title="weight" style="cursor: pointer;">
            <inertia-link :href="'/tags/' + word">
              {{ text }}
            </inertia-link>
          </div>
        </template>
      </vue-word-cloud> -->
    </div>
  </div>
</template>
<script>
import { Layout, Pagination } from '@/Shared'
import VueWordCloud from 'vuewordcloud'
import TTable from 'vue-tailwind/src/components/TTable.vue'
export default {
  metaInfo: { title: 'Dashboard' },
  layout: (h, page) => h(Layout, [page]),
  components: {
    [VueWordCloud.name]: VueWordCloud,
    TTable,
    Pagination
  },
  props: {
    tags: Array
  },
  data () {
    return {

    }
  },
  computed: {
    headers () {
      return [
        { id: 'id', value: 'id', text: 'Id', className: 'border px-4 py-2' },
        { id: 'name', value: 'name', text: 'Name', className: 'border px-4 py-2' },
        { id: 'weight', value: 'weight', text: 'Weight', className: 'text-center border px-4 py-2' },
        { id: 'team', value: 'team', text: 'Team', className: 'border px-4 py-2' },
        { id: 'count', value: 'count', text: 'Count', className: 'border px-4 py-2' }
      ]
    }
    // words () {
    //   const words = []
    //   this.tags.forEach(tag => {
    //     words.push(tag.name)
    //   })
    //   return words
    // }
  },
  methods: {
    onWordClick (word) {
      this.$inertia.get(this.route('tags.search', word))
    }
  }
}
</script>
