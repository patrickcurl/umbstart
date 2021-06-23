
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import Vue from 'vue'
import VueMeta from 'vue-meta'
import PortalVue from 'portal-vue'
import { InertiaApp } from '@inertiajs/inertia-vue'
import 'vue-material-design-icons/styles.css'
import 'nprogress/nprogress.css'
import VueTailwind from 'vue-tailwind'
// import api from '@/plugins/api'
// window._ = require('lodash')
// require('./bootstrap')
// Vue.use(api)
Vue.use(VueTailwind)
// import vuetify from '@/plugins/vuetify'
// vendor
// 3rd party
// import '@mdi/font/css/materialdesignicons.css'
// import 'vuetify/dist/vuetify.min.css'
// import Vuetify from 'vuetify/lib'
// import VueProgressBar from 'vue-progressbar'

// app
// import router from '@/router'
// import store from '@/common/Store'
// import eventBus from '@/common/Event'
// import formatters from '@/common/Formatters'
// import AxiosAjaxDetect from '@/common/AxiosAjaxDetect'
// import App from '@/App'
// require('@/bootstrap')

// import { PacmanLoader } from '@saeris/vue-spinners'

Vue.mixin({ methods: { route: window.route } })
// this is the vuetify theming options
// you can change colors here based on your needs
// and please dont forget to recompile scripts
// Vue.use(Vuetify)
Vue.use(InertiaApp)
Vue.use(PortalVue)
Vue.use(VueMeta)
// this is the progress bar settings, you
// can change colors here to fit on your needs or match
// your theming above
// Vue.use(VueProgressBar, {
//   color: '#3f51b5',
//   failedColor: '#b71c1c',
//   thickness: '5px',
//   transition: {
//     speed: '0.2s',
//     opacity: '0.6s',
//     termination: 300
//   },
//   autoRevert: true,
//   inverse: false
// })
// global component registrations here
// Vue.component('pacman-loader', PacmanLoader)

// Vue.use(formatters)
// Vue.use(eventBus)
Vue.config.productionTip = false
// const bus = new Vue()
// Vue.prototype.$bus = bus
const app = document.getElementById('app')
new Vue({
  metaInfo: {
    title: 'Loading…',
    titleTemplate: '%s | Umbric CRM'
  },
  // vuetify: new Vuetify({
  //   theme: {
  //     dark: true,
  //     themes: {
  //       dark: {
  //         primary: '#3f51b5',
  //         info: '#4c86b5',
  //         success: '#17b535',
  //         secondary: '#b0bec5',
  //         accent: '#8c9eff',
  //         error: '#b71c1c'
  //       }
  //     }
  //   },
  //   icons: {
  //     iconfont: 'mdi'
  //   }
  // }),
  // vuetify,
  // eventBus,
  // router,
  // store,
  render: h => h(InertiaApp, {
    props: {
      initialPage: JSON.parse(app.dataset.page),
      // resolveComponent: name => require(`./Pages/${name}`).default
      resolveComponent: name => import(`./Pages/${name}`).then(module => module.default)
    }
  })
}).$mount(app)

//   // template: App

//   // mounted () {
//   //   // progress bar top
//   //   // AxiosAjaxDetect.init(() => {
//   //   //   self.$Progress.start()
//   //   // }, () => {
//   //   //   self.$Progress.finish()
//   //   // })
//   // },

// }).$mount(app)
