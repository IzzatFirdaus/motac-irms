/**
 * MOTAC IRMS - Stylelint Configuration
 * ------------------------------------
 * Enforces CSS/SCSS code style for the MOTAC Integrated Resource Management System (Malaysia).
 * This config follows a logical property grouping (Recess Order-like) and supports Bootstrap, custom themes, and modern CSS.
 * Adjust or extend the property order if your system grows or new patterns emerge.
 */

module.exports = {
  extends: [
    'stylelint-config-standard' // Base standard config for broad compatibility
  ],
  plugins: [
    'stylelint-order'
  ],
  rules: {
    // Disable strict alphabetical order in favor of logical groupings
    'order/properties-alphabetical-order': null,

    // Logical grouping of properties for readability and maintainability (inspired by Bootstrap and Recess Order)
    'order/properties-order': [
      // Positioning
      'position', 'top', 'right', 'bottom', 'left', 'z-index',

      // Box Model
      'display', 'flex', 'flex-grow', 'flex-shrink', 'flex-basis', 'flex-flow', 'flex-direction', 'flex-wrap',
      'justify-content', 'align-items', 'align-content', 'align-self',
      'order',
      'float', 'clear',
      'width', 'min-width', 'max-width',
      'height', 'min-height', 'max-height',
      'margin', 'margin-top', 'margin-right', 'margin-bottom', 'margin-left',
      'padding', 'padding-top', 'padding-right', 'padding-bottom', 'padding-left',

      // Typography
      'font-family', 'font-size', 'font-weight', 'font-style', 'line-height',
      'text-align', 'text-decoration', 'text-transform', 'white-space', 'word-wrap', 'word-break', 'letter-spacing', 'color',

      // Backgrounds & Visuals
      'background', 'background-color', 'background-image', 'background-repeat', 'background-position', 'background-size',
      'border', 'border-top', 'border-right', 'border-bottom', 'border-left',
      'border-width', 'border-top-width', 'border-right-width', 'border-bottom-width', 'border-left-width',
      'border-style', 'border-top-style', 'border-right-style', 'border-bottom-style', 'border-left-style',
      'border-color', 'border-top-color', 'border-right-color', 'border-bottom-color', 'border-left-color',
      'border-radius', 'border-top-left-radius', 'border-top-right-radius', 'border-bottom-right-radius', 'border-bottom-left-radius',
      'box-shadow', 'opacity',

      // Miscellaneous
      'overflow', 'overflow-x', 'overflow-y',
      'list-style', 'caption-side',
      'outline', 'outline-offset',
      'cursor', 'pointer-events',
      'appearance', '-webkit-appearance', '-moz-appearance',

      // Transitions & Animations
      'transition', 'transition-property', 'transition-duration', 'transition-timing-function', 'transition-delay',
      'animation', 'animation-name', 'animation-duration', 'animation-timing-function', 'animation-delay', 'animation-iteration-count', 'animation-direction', 'animation-fill-mode',

      // Others
      // Add more property groups as needed for MOTAC IRMS
    ],

    // Allow vendor prefixes as Autoprefixer is used in the build process.
    'property-no-vendor-prefix': null,
    'value-no-vendor-prefix': null,
    'selector-no-vendor-prefix': null,
    'media-feature-name-no-vendor-prefix': null,

    // Allow duplicate properties with different values for Bootstrap/fallbacks
    'declaration-block-no-duplicate-properties': [true, {
      ignore: ['consecutive-duplicates-with-different-values']
    }],

    // Allow custom properties, useful for theming (e.g., MOTAC colors)
    'property-no-unknown': [true, {
      ignoreProperties: ['/^--/']
    }]
  }
};
