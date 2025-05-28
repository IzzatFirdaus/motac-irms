module.exports = {
  extends: [
    'stylelint-config-standard' // Or your preferred config
  ],
  plugins: [
    'stylelint-order'
  ],
  rules: {
    'order/properties-alphabetical': null, // Disable alphabetical if you prefer logical or group-based
    'order/properties-order': [ // Example for logical grouping (Recess Order-like)
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
      'text-align', 'text-decoration', 'text-transform', 'white-space', 'word-wrap', 'word-break',
      // Visual
      'color', 'background', 'background-color', 'background-image', 'background-repeat', 'background-position', 'background-size',
      'border', 'border-top', 'border-right', 'border-bottom', 'border-left',
      'border-width', 'border-top-width', 'border-right-width', 'border-bottom-width', 'border-left-width',
      'border-style', 'border-top-style', 'border-right-style', 'border-bottom-style', 'border-left-style',
      'border-color', 'border-top-color', 'border-right-color', 'border-bottom-color', 'border-left-color',
      'border-radius', 'border-top-left-radius', 'border-top-right-radius', 'border-bottom-right-radius', 'border-bottom-left-radius',
      'box-shadow', 'opacity',
      // Misc
      'overflow', 'overflow-x', 'overflow-y',
      'list-style', 'caption-side',
      'outline', 'outline-offset',
      'cursor', 'pointer-events',
      'appearance', /* Standard should ideally be last */
      '-webkit-appearance', '-moz-appearance',
      // Transitions and Animations
      'transition', 'transition-property', 'transition-duration', 'transition-timing-function', 'transition-delay',
      'animation', 'animation-name', 'animation-duration', 'animation-timing-function', 'animation-delay', 'animation-iteration-count', 'animation-direction', 'animation-fill-mode',
      // Others
      // ... add other properties or groups as needed
    ],
    // You might want to disable rules that conflict with Bootstrap's choices if you're okay with them
    // e.g., 'property-no-vendor-prefix': null, // If you rely on Autoprefixer
    // 'declaration-block-no-duplicate-properties': [true, { ignore: ["consecutive-duplicates-with-different-values"] }]
  },
};
