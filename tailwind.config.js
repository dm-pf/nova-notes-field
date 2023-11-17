const path = require('path');

module.exports = {
  content: [
    path.resolve(__dirname, 'resources/**/*.{vue,js,ts,jsx,tsx,scss}'),
    './node_modules/laravel-nova-ui/**/*{js,ts,vue}',
  ],
  prefix: 'o1-',
  darkMode: 'class',
  safelist: [],
};
