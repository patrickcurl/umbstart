export default {
  install (Vue, options) {
    Vue.prototype.$route = (...args) => route(...args).url()
  }
}
