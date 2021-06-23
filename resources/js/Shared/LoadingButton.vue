<template>
  <button id="buttonId" :disabled="loading" class="flex items-center">
    <div v-if="loading" class="btn-spinner mr-2" />
    <slot />
  </button>
</template>

<script>
export default {
  props: {
    loading: Boolean,
    buttonId: String
  }
}
</script>
