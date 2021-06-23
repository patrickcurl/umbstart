<template>
  <div>
    <h1 class="font-bold text-2xl w-full" style="">
      Dashboard
    </h1>
    <div class="w-full flex align-center items-center">
      <vue-word-cloud
        :words="words"
        :color="([, weight]) => weight > 10 ? 'DeepPink' : weight > 5 ? 'RoyalBlue' : 'Indigo'"
        font-family="Roboto"
        class="w-full flex align-center items-center"
        style="width:640px;height:480px;position:relative;margin-top:-100px;"
      >
        <template slot-scope="{text, weight, word}">
          <div :title="weight" style="cursor: pointer;" @click="onWordClick(word)">
            {{ text }}
          </div>
        </template>
      </vue-word-cloud>
    </div>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import VueWordCloud from 'vuewordcloud'

export default {
  metaInfo: { title: 'Dashboard' },
  layout: (h, page) => h(Layout, [page]),
  components: {
    [VueWordCloud.name]: VueWordCloud
  },
  props: {
    tags: Array
  },
  data () {
    return {

    }
  },
  computed: {
    words () {
      const words = []
      this.tags.forEach(tag => {
        words.push(tag.name)
      })
      return words
    }
  },
  methods: {
    onWordClick (word) {
      this.$inertia.get(this.route('tags', word))
    }
  }
}
</script>
