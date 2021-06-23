module.exports = {
  env: {
    browser: true,
    es6: true
  },
  extends: [
    'eslint:recommended',
    'plugin:vue/recommended',
    'standard',
    'plugin:import/warnings'
  ],
  globals: {
    Atomics: 'readonly',
    SharedArrayBuffer: 'readonly'
  },
  parserOptions: {
    parser: 'babel-eslint',
    ecmaVersion: 2018,
    sourceType: 'module'
  },
  plugins: [
    'vue'
  ],
  rules: {
    'no-undef': 0,
    indent: ['error', 2],
    quotes: ['warn', 'single'],
    semi: ['warn', 'never'],
    // 'comma-dangle': ['warn', 'always-multiline'],
    'vue/max-attributes-per-line': 0
    // "vue/require-default-prop": false,
    // "vue/singleline-html-element-content-newline": 0,
    //  "vue/return-in-computed-property": [
    //   "warning",
    //   {
    //     treatUndefinedAsUnspecified: true
    //   }
    // ],
  }
}
