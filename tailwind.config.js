module.exports = {
  future: {
    // removeDeprecatedGapUtilities: true,
    // purgeLayersByDefault: true,
    // defaultLineHeights: true,
    // standardFontWeights: true
  },
  purge: [],
  theme: {
    extend: {}
  },
  variants: {
    extend: {
      backgroundColor: ['group-focus', 'focus-within', 'active', 'checked'],
      textColor: ['focus-within'],
      borderColor: ['focus-within', 'checked'],
    }
  },
  plugins: []
}
