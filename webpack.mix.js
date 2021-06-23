const cssImport = require('postcss-import')
const cssNesting = require('postcss-nesting')
const mix = require('laravel-mix')
const path = require('path')
const tailwindcss = require('tailwindcss')
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
// mix.options({
//   extractVueStyles: true,
//   processCssUrls: true,
//   purifyCss: false,
//   uglify: {
//     uglifyOptions: {
//       // fixes error on sweetalert2
//       compress: {
//         unused: false
//       }
//     }
//   },
//   postCss: []
// })
mix.disableSuccessNotifications()
  .webpackConfig({
    output: { chunkFilename: 'js/[name].js?id=[chunkhash]' },
    resolve: {
      extensions: ['.js', '.vue', '.json', '.css'],
      alias: {
        vue$: 'vue/dist/vue.runtime.esm.js',
        icons: path.resolve(__dirname, 'node_modules/vue-material-design-icons'),
        '@': path.resolve('resources/js')
      }
    }
  })
  .options({
    hmrOptions: {
      host: 'umb.test',
      port: 8080
    }
  })
  .copyDirectory('resources/img', 'public/img')
  .js('resources/js/app.js', 'public/js')
  .postCss('resources/css/app.css', 'public/css', [
    cssImport(),
    cssNesting(),
    tailwindcss('tailwind.js')
  ])
  // .sass('resources/sass/admin.scss', 'public/css')
  // .sass('resources/sass/front.scss', 'public/css')
  // .sourceMaps()
if (mix.inProduction()) {
  // mix.extract(['vue', 'vue-router', 'vuetify', 'moment', 'axios', 'lodash', 'dropzone'])
  mix.version()
  mix.disableNotifications()
}
