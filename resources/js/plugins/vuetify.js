import Vue from 'vue'
import Vuetify from 'vuetify/lib'
import 'roboto-fontface/css/roboto/roboto-fontface.css'
// import 'vuetify/dist/vuetify.min.css'
import colors from 'vuetify/lib/util/colors'
// ? https://vuetifyjs.com/en/framework/icons#using-custom-icons

import 'material-design-icons-iconfont/dist/material-design-icons.css' //! iconfont: 'md'
// ? https://material.io/resources/icons/?style=baseline
import '@mdi/font/css/materialdesignicons.css' //! iconfont: 'mdi'
// ? https://materialdesignicons.com/
import '@fortawesome/fontawesome-free/css/all.css' //! iconfont: 'fa'
// ? https://fontawesome.com/icons?d=gallery&m=free
import 'font-awesome/css/font-awesome.min.css' //! iconfont: 'fa4'
// ? https://fontawesome.com/v4.7.0/icons/

//! ----------------mdiSvg Usage On Component ---------------------------------
// On Script Tag <script></script>
//! import '@mdi/js/' //! iconfont: mdiSvg
//! import {mdiAccount} from  '@mdi/js/'
//! data() {
//!     return {
//!         svgPath: mdiAccount
//!     }
//! },
// On template Tag <template></template>
//! <v-icon>{{ svgPath  }}</v-icon>
//! ----------------------------------------------------------------------------
Vue.use(Vuetify)

const opts = {
  theme: {
    dark: true,
    themes: {
      dark: {
        primary: '#3f51b5',
        info: '#4c86b5',
        success: '#17b535',
        secondary: '#b0bec5',
        accent: '#8c9eff',
        error: '#b71c1c',
      },
    },
  },
  icons: {
    iconfont: 'mdi',
  },
}

export default new Vuetify({
  icons: {
    iconfont: 'mdi' || 'mdiSvg' || 'md' || 'fa' || 'fa4',
  },
  theme: {
    dark: true,
    themes: {
      dark: {
        primary: '#3f51b5',
        info: '#4c86b5',
        success: '#17b535',
        secondary: '#b0bec5',
        accent: '#8c9eff',
        error: '#b71c1c',
      },
    },
  },
  options: {
    customProperties: true,
  },
})
