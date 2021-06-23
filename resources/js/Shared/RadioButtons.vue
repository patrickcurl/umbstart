<template>
  <div class="max-w-sm text-center flex flex-wrap justify-center mt-4 " v-bind="$attrs">
    <!-- <div class="" :class="vertical? 'container' : ''"> -->
    <div v-for="(choice,i) in choices" :key="i" class="flex items-center mr-4 mb-4" :class="vertical===true ? 'w-full' : ''">
      <input :id="choice.name || `radio-${i}`" ref="input" v-model="chosen" type="radio" name="radio" class="hidden" :value="choice.name" :checked="choice.name == chosen" @input="checkRef(i)" @change="checkRef(i)">
      <label :for="choice.name || `radio-${i}`" class="flex items-center cursor-pointer" :class="{ error: errors.length }">
        <span class="w-4 h-4 inline-block mr-1 border border-grey" />
        {{ choice.label }}
      </label>
    </div>
    <div v-if="errors && errors.length" class="form-error">
      {{ errors[0] }}
    </div>
    <!-- </div> -->
  </div>
</template>

<script>
export default {
  // inheritAttrs: false,
  props: {
    vertical: {
      type: Boolean,
      default: false
    },
    choices: {
      type: Array,
      default: function () {
        return [{
          name: 'radio1',
          label: 'First choice',
          value: 'first_choice',
          checked: true
        }]
      }

    },
    errors: {
      type: Array,
      default: () => []
    }
  },
  data () {
    return {
      chosen: ''
    }
  },
  watch: {
    chosen (chosen) {
      console.log(chosen)
      // this.chosen = chosen
      // console.log(this.$refs[chosen])

      // this.$emit('searchFilterBy', this.$refs.input[i])
    }
  },
  mounted () {
    this.choices.forEach(choice => {
      if (choice.checked === true) {
        this.chosen = choice.name
        this.$emit('input', this.chosen)
      }
    })
  },
  methods: {
    isChecked (name, checked) {
      return !!(this.chosen === name || checked === true)
    },

    checkRef (i) {
      console.log(this.$refs.input[i].name)
      this.chosen = this.$refs.input[i].id
      this.$emit('input', this.chosen)
    },
    focus () {
      this.$refs.input.focus()
    }
    // checked () {
    //   this.$refs.input.checked()
    // }
  }
}
</script>
