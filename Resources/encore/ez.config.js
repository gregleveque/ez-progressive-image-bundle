import path from 'path'

module.exports = Encore => Encore.addStyleEntry(
  'ez-progressive-image-css',
  path.resolve(__dirname, '../public/scss/images.scss')
)