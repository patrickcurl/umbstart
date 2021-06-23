<template>
  <div :key="keyword" class="bg-indigo-lightest font-sans">
    <h1 class="px-4 pt-2 font-bold text-3xl">
      Images
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
      <!-- <inertia-link class="btn-indigo" :href="route('imateams.create')">
        <span>Upload</span>
        <span class="hidden md:inline">Team</span>
      </inertia-link> -->
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <!-- <div class="grid max-w-4xl mx-auto p-8">
        <section v-for="image in images" :key="image.id" class="bg-white rounded h-full text-grey-darkest no-underline shadow-md">
          <img class="w-full block rounded-b" :src="image.source">
        </section>
      </div> -->
      <div class="container my-12 mx-auto px-4 md:px-12">
        <div class="flex flex-wrap  mx-auto lg:mx-4">
          <!-- Column -->
          <div v-for="(image,i) in images.data" :key="i" class="md:flex content-center flex-wrap shadow-lg my-1 px-1 w-full md:w-1/2 lg:my-4 lg:px-4 lg:w-1/3">
            <!-- Article -->
            <article class="overflow-hidden rounded-lg shadow-lg md:flex-1">
              <a href="#">
                <img alt="Placeholder" class="block h-auto w-full" :src="image.src">
              </a>
              <header class="flex items-center justify-between leading-tight p-2 md:p-4">
                <h1 class="text-lg">
                  <a class="no-underline hover:underline text-black" href="#">
                    {{ image.message ? image.message.subject : '' }}
                  </a>
                </h1>
              </header>
              <footer class="flex flex-wrap items-center justify-between leading-none p-2 md:p-4">
                <button class="w-full block py-2 px-2 mb-2 rounded bg-indigo text-white" @click="refresh(image.message_id)">
                  Refresh Tags
                </button>
                <!-- <ul v-if="image.tags" class="list-reset flex"> -->
                <div v-for="tag in image.tags" :key="tag.id" class="clearfix chip px-1 my-1 pt-1 pb-1  bg-indigo-darker border-indigo-lightest rounded hover:bg-indigo  select-none align-middle flex">
                  <span class="float-left mr-0  rounded  text-indigo-lightest cursor-pointer" @click="filterTags(tag.name)">{{ tag.name }}</span>
                  <span class="pl-1 float-right px-0 cursor-pointer text-orange" @click="deleteTag(tag.id)">x</span>
                  <!--  <div class="inline-block border mb-1 px-2 py-12 mr-1 border-blue rounded  px-3 bg-blue text-white">
                      <a class="inline-block" href="#">{{ tag.name }}</a>
                      <a class="float-right" href="#">x</a>
                    </div> -->
                </div>
                <div class="w-full block py-2 px-2 mb-2 rounded bg-indigo">
                  <text-input v-model="newTags[i]" />
                  <button class="btn text-indigo-lightest bg-indigo-light py-1 px-1 mt-1 rounded " @click="addTag(i, image.id)">
                    Add Tag
                  </button>
                </div>
                <!-- </ul> -->
              </footer>
            </article>
            <!-- END Article -->
          </div>
          <!-- END Column -->
          <div class="w-full">
            <pagination :links="images.links" />
          </div>
          <!-- Column -->
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import _ from 'lodash'
import { Layout, TextInput, Pagination } from '@/Shared'
export default {
  metaInfo: { title: 'Users' },
  layout: (h, page) => h(Layout, [page]),
  components: {
    TextInput,
    Pagination
  },
  props: {
    images: Object,
    permissions: Object,
    filters: Object,
    keyword: String
  },
  data () {
    return {
      newTags: []
    }
  },
  methods: {
    refresh (messageId) {
      this.$inertia.post(this.route('images.tag.refresh', messageId))
    },
    deleteTag (tagId) {
      this.$inertia.delete(this.route('images.tag.destroy', tagId))
    },
    filterTags (tagName) {
      this.$inertia.visit(this.route('images.tag.search', tagName))
    },
    addTag (i, imageId) {
      var data = new FormData()
      // console.log(this.images[i].newtag)
      data.append('name', this.newtags[i])
      data.append('image_id', imageId)
      this.$inertia.post(this.route('images.tag.store', imageId), data)
      // console.log(e.key)
    }
  }
}
</script>
