<template>
    <v-app id="inspire">
        <v-navigation-drawer
                    v-model="drawer"
                    app
                    clipped
                    left>
                <v-list dense>
                        <div v-for="nav in navs" :key="nav.routeName">

                            <v-list-item v-if="nav.navType==='nav' && nav.visible === true"  :to="{name: `${nav.routeName}`}" :exact="false">
                                <v-list-item-action>
                                    <v-icon>{{nav.icon}}</v-icon>
                                </v-list-item-action>
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{nav.label}}
                                    </v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>

                            <v-divider v-else></v-divider>
                        </div>

                    <v-list-item @click="clickLogout('/')">
                        <v-list-item-action>
                            <v-icon>directions_walk</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>Logout</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>

                </v-list>
            </v-navigation-drawer>

            <v-app-bar app clipped-left>
                <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
                <v-toolbar-title>{{appName}}</v-toolbar-title>
            </v-app-bar>

            <v-content>
                <v-container>

                    <v-breadcrumbs :items="getBreadcrumbs">
                        <template v-slot:item="props">
                            <v-breadcrumbs-item :to="props.item.to" exact
                                                :key="props.item.label"
                                                :disabled="props.item.disabled">
                                <template v-slot:divider>
                                    <v-icon>mdi-forward</v-icon>
                                </template>
                                {{ props.item.label }}
                            </v-breadcrumbs-item>
                        </template>
                    </v-breadcrumbs>
                </div>
                <v-divider></v-divider>
                <transition name="fade">
                    <router-view></router-view>
                </transition>
                </v-container>
            </v-content>
            <v-footer fixed>
                <span>&copy; 2017</span>
            </v-footer>
             <v-snackbar
                :timeout="snackbarDuration"
                :color="snackbarColor"
                top
                v-model="showSnackbar">
            {{ snackbarMessage }}
        </v-snackbar>

        <!-- dialog confirm -->
        <v-dialog v-show="showDialog" v-model="showDialog" absolute max-width="450px">
            <v-card>
                <v-card-title>
                    <div class="headline"><v-icon v-if="dialogIcon">{{dialogIcon}}</v-icon> {{ dialogTitle }}</div>
                </v-card-title>
                <v-card-text>{{ dialogMessage }}</v-card-text>
                <v-card-actions v-if="dialogType=='confirm'">
                    <v-spacer></v-spacer>
                    <v-btn color="orange darken-1" text @click.native="dialogCancel">Cancel</v-btn>
                    <v-btn color="green darken-1" text @click.native="dialogOk">Ok</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- the progress bar -->
        <vue-progress-bar></vue-progress-bar>
        </v-app>

        <!-- loader -->
       <!-- {{--  <div v-if="showLoader" class="wask_loader bg_half_transparent">
            <pacman-loader color="yellow"></pacman-loader>
        </div> --}} -->

        <!-- snackbar -->

    </template>
<script>
export default {
  props: {
    navs: {
      type: Array,
      default: function () {
        return []
      }
    },
    appName: {
      type: String,
      default: 'Umbric'
    }
  },
  data () {
    return {
      drawer: true
    }
  },
  computed: {
    getBreadcrumbs () {
      return store.getters.getBreadcrumbs
    },
    showLoader () {
      return store.getters.showLoader
    },
    showSnackbar: {
      get () {
        return store.getters.showSnackbar
      },
      set (val) {
        if (!val) store.commit('hideSnackbar')
      }
    },
    snackbarMessage () {
      return store.getters.snackbarMessage
    },
    snackbarColor () {
      return store.getters.snackbarColor
    },
    snackbarDuration () {
      return store.getters.snackbarDuration
    },

    // dialog
    showDialog: {
      get () {
        return store.getters.showDialog
      },
      set (val) {
        if (!val) store.commit('hideDialog')
      }
    },
    dialogType () {
      return store.getters.dialogType
    },
    dialogTitle () {
      return store.getters.dialogTitle
    },
    dialogMessage () {
      return store.getters.dialogMessage
    },
    dialogIcon () {
      return store.getters.dialogIcon
    }
  },
  methods: {
    menuClick (routeName, routeType) {
      const rn = routeType || 'vue'

      if (rn === 'vue') {
        this.$router.push({ name: routeName })
      }
      if (rn === 'full_load') {
        window.location.href = routeName
      }
    },
    clickLogout (redirectPath) {
      axios.post('/logout').then((r) => {
        window.location.href = redirectPath
      })
    },
    dialogOk () {
      store.commit('dialogOk')
    },
    dialogCancel () {
      store.commit('dialogCancel')
    }
  }
}
</script>
