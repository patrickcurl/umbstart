<template>
  <div class="p-6 bg-indigo-darker min-h-screen flex justify-center items-center">
    <div class="w-full max-w-sm">
      <umbric-logo-png class="block mx-auto w-full max-w-xs fill-white" height="50" />
      <div class="px-10 py-12 bg-white mt-4">
        <h1 class="text-center font-bold text-3xl">
          {{ title }}
        </h1>
        <div class="mx-auto mt-6 w-24 border-b-2" />
        <div>{{ description }}</div>
      </div>
    </div>
  </div>
  </div>
</template>

<script>
import { UmbricLogoPng } from '@/Shared'
export default {
  components: {
    UmbricLogoPng
  },
  props: {
    status: Number,
    default: 500
  },
  computed: {
    title () {
      return {
        503: '503: Service Unavailable',
        500: '500: Server Error',
        404: '404: Page Not Found',
        403: '403: Forbidden'
      }[this.status]
    },
    description () {
      return {
        503: 'Sorry, we are doing some maintenance. Please check back soon.',
        500: 'Whoops, something went wrong on our servers.',
        404: 'Sorry, the page you are looking for could not be found.',
        403: 'Sorry, you are forbidden from accessing this page.'
      }[this.status]
    }
  }
}
</script>
