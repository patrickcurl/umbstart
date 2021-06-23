// import { create } from 'apisauce'
import axios from 'axios'
import { Model } from 'vue-api-query'
// function getHeaders () {
//   // const csrf = document.head.querySelector('meta[name="csrf-token"]').content
//   // const headers = {
//   //   Accept: 'application/json',
//   //   'Content-Type': 'application/json',
//   //   'X-Requested-With': 'XMLHttpRequest'
//   // }

//   // if (csrf) {
//   //   headers['X-CSRF-TOKEN'] = csrf
//   // } else {
//   //   console.error(
//   //     'CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token'
//   //   )
//   // }
// }

export default {
  install (Vue, options) {
    // const headers = getHeaders()
    // const api = create({
    //   baseURL: process.env.MIX_API_URL,
    //   headers: headers
    // })
    // Vue.prototype.$api = api
    Vue.prototype.$http = axios
    Model.$http = axios
    Vue.$model = Model
  }
}
