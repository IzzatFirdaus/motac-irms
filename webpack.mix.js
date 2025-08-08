// -----------------------------------------------------------------------------
// MOTAC IRMS (Integrated Resource Management System) - Laravel Mix Configuration
// -----------------------------------------------------------------------------
// This config is tailored for MOTAC/Malaysia local development.
// It handles assets, vendor libraries, custom UI theme, and is locale-aware.
// -----------------------------------------------------------------------------
// Usage: npm run dev / npm run prod / npm run watch
// Most commands will pick up .env settings (e.g. ASSET_URL for publicPath).
// -----------------------------------------------------------------------------

const mix = require('laravel-mix');
const { EnvironmentPlugin, IgnorePlugin } = require('webpack');
const glob = require('glob');
const path = require('path');

// -----------------------------------------------------------------------------
// Mix Options
// -----------------------------------------------------------------------------
mix.options({
  resourceRoot: process.env.ASSET_URL || '/', // Base for assets; use CDN/base if needed
  processCssUrls: false,                      // Don't process/replace css url()s
  postCss: [require('autoprefixer')]
});

// -----------------------------------------------------------------------------
// Webpack Configuration
// -----------------------------------------------------------------------------
mix.webpackConfig({
  output: {
    publicPath: process.env.ASSET_URL ? `${process.env.ASSET_URL}/` : '/', // For correct asset loading
    libraryTarget: 'umd'
  },
  plugins: [
    // Ignore premium or non-npm vendor plugins
    new IgnorePlugin({
      checkResource(resource, context) {
        return [
          path.join(__dirname, 'resources/assets/vendor/libs/@form-validation')
        ].some(pathToIgnore => resource.startsWith(pathToIgnore));
      }
    }),
    // Inject BASE_URL into frontend scripts
    new EnvironmentPlugin({
      BASE_URL: process.env.ASSET_URL ? `${process.env.ASSET_URL}/` : '/'
    })
  ],
  module: {
    rules: [
      // Use Babel for modern JS compatibility
      {
        test: /\.es6$|\.js$/,
        include: [
          path.join(__dirname, 'node_modules/bootstrap/'),
          path.join(__dirname, 'node_modules/popper.js/'),
          path.join(__dirname, 'node_modules/shepherd.js/')
        ],
        loader: 'babel-loader',
        options: {
          presets: [['@babel/preset-env', { targets: 'last 2 versions, ie >= 10' }]],
          plugins: [
            '@babel/plugin-transform-destructuring',
            '@babel/plugin-proposal-object-rest-spread',
            '@babel/plugin-transform-template-literals'
          ],
          babelrc: false
        }
      }
    ]
  },
  // Some vendor libraries are expected globally (no need to bundle again)
  externals: {
    jquery: 'jQuery',
    moment: 'moment',
    jsdom: 'jsdom',
    velocity: 'Velocity',
    hammer: 'Hammer',
    pace: '"pace-progress"',
    chartist: 'Chartist',
    'popper.js': 'Popper',
    './blueimp-helper': 'jQuery',
    './blueimp-gallery': 'blueimpGallery',
    './blueimp-gallery-video': 'blueimpGallery'
  }
});

// -----------------------------------------------------------------------------
// Helper for Globbing Vendor and App Asset Directories
// -----------------------------------------------------------------------------
function mixAssetsDir(query, cb) {
  (glob.sync('resources/assets/' + query) || []).forEach(f => {
    f = f.replace(/[\\\/]+/g, '/');
    cb(f, f.replace('resources/assets/', 'public/assets/'));
  });
}

// -----------------------------------------------------------------------------
// SASS Compilation Options (for MOTAC theme precision)
// -----------------------------------------------------------------------------
const sassOptions = {
  precision: 5
};

// -----------------------------------------------------------------------------
// VENDOR ASSETS: Compile/copy all vendor theme assets (SCSS, JS, fonts, images)
// -----------------------------------------------------------------------------

// Core theme SCSS to CSS
mixAssetsDir('vendor/scss/**/!(_)*.scss', (src, dest) =>
  mix.sass(src, dest.replace(/(\\|\/)scss(\\|\/)/, '$1css$2').replace(/\.scss$/, '.css'), { sassOptions })
);

// Core theme JS (ES6 or plain)
mixAssetsDir('vendor/js/**/*.js', (src, dest) => mix.js(src, dest));

// Vendor libraries JS and SCSS
mixAssetsDir('vendor/libs/**/*.js', (src, dest) => mix.js(src, dest));
mixAssetsDir('vendor/libs/**/!(_)*.scss', (src, dest) =>
  mix.sass(src, dest.replace(/\.scss$/, '.css'), { sassOptions })
);
mixAssetsDir('vendor/libs/**/*.{png,jpg,jpeg,gif}', (src, dest) => mix.copy(src, dest));

// Copy full directory for premium form validation plugin (not in npm)
mixAssetsDir('vendor/libs/@form-validation/umd', (src, dest) => mix.copyDirectory(src, dest));

// Fonts (custom and vendor)
mixAssetsDir('vendor/fonts/*/*', (src, dest) => mix.copy(src, dest));
mixAssetsDir('vendor/fonts/!(_)*.scss', (src, dest) =>
  mix.sass(src, dest.replace(/(\\|\/)scss(\\|\/)/, '$1css$2').replace(/\.scss$/, '.css'), { sassOptions })
);

// -----------------------------------------------------------------------------
// APPLICATION ASSETS: Compile app-specific JS, copy CSS
// -----------------------------------------------------------------------------

// Application JS (combine all app scripts in js/ to single bundle)
mixAssetsDir('js/**/*.js', (src, dest) => mix.scripts(src, dest));
mixAssetsDir('css/**/*.css', (src, dest) => mix.copy(src, dest));

// Laravel user management module (compiled as a separate app bundle)
mix.js('resources/js/laravel-user-management.js', 'public/js/');

// -----------------------------------------------------------------------------
// Copy vendor icons/fonts for UI (flag icons, FontAwesome, KaTeX for Quill)
// -----------------------------------------------------------------------------
mix.copy('node_modules/flag-icons/flags/1x1/*', 'public/assets/vendor/fonts/flags/1x1');
mix.copy('node_modules/flag-icons/flags/4x3/*', 'public/assets/vendor/fonts/flags/4x3');
mix.copy('node_modules/@fortawesome/fontawesome-free/webfonts/*', 'public/assets/vendor/fonts/fontawesome');
mix.copy('node_modules/katex/dist/fonts/*', 'public/assets/vendor/libs/quill/fonts');

// -----------------------------------------------------------------------------
// Versioning: For cache-busting in dev and production
// -----------------------------------------------------------------------------
mix.version();

// -----------------------------------------------------------------------------
// Browsersync: Auto reload for local dev (http://localhost:8000)
// -----------------------------------------------------------------------------
mix.browserSync('http://127.0.0.1:8000/');

// -----------------------------------------------------------------------------
// Documentation:
//  - To run dev build:    npm run dev
//  - For production:      npm run prod
//  - To watch changes:    npm run watch
//  - For Browsersync:     php artisan serve & npm run watch
//  - See .env for ASSET_URL or APP_URL/asset root override
// -----------------------------------------------------------------------------
