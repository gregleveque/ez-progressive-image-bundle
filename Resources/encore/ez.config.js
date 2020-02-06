const path = require('path')

module.exports = Encore => {
  Encore.addStyleEntry(
    'ez-progressive-image-css',
    path.resolve(__dirname, '../public/scss/images.scss')
  )

  Encore.addEntry(
    'ez-progressive-image-js',
    path.resolve(__dirname, '../public/js/lazysizes.min.js')
  )
}