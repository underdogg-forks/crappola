const mix = require("laravel-mix");
const assetRoot = "public_html";
const cssImport = require("postcss-import");
const cssNested = require("postcss-nested");
const tailwind = require("tailwindcss");
const autoprefixer = require("tailwindcss");

mix.copyDirectory(
    "./node_modules/@fortawesome/fontawesome-free/webfonts",
    assetRoot + "/webfonts"
);
/*
.copyDirectory(
    'resources/img',
    'public_html/img'
)
*/
/*
.copyDirectory(
        "resources/fontawesome/webfonts",
        "public_html/fonts/vendor/webfonts"
);
*/
mix.sass("resources/sass/ivplapp.scss", "/assets/dist/css")
    .combine(
        [
            "node_modules/jquery/dist/jquery.js",
            "node_modules/jquery-ui-dist/jquery-ui.js",
            "node_modules/bootstrap/dist/js/bootstrap.bundle.js",
            "node_modules/@coreui/coreui/dist/js/coreui.js",
            "node_modules/autosize/dist/autosize.js",
            "node_modules/moment/moment.js",
            "node_modules/bootstrap-notify/bootstrap-notify.js",
            "node_modules/jquery-slimscroll/jquery.slimscroll.js",
        ],
        assetRoot + "/assets/dist/js/dependencies.js"
    )
    .setPublicPath("public_html");

/*
mix.sass('resources/sass/app.scss', 'style/css/app.css')
    .sass('resources/sass/style.scss', 'style/css/style.css')
	.tailwind()
    .setPublicPath('public_html');
*/
/* .postCss('resources/css/app.css', 'css', [
        cssImport(),
        tailwind(),
        cssNested(),
        autoprefixer(),
    ])
    .options({
        processCssUrls: false,
        terser: {
            extractComments: false,
        },
        cssNano: {
            mergeRules: {
                exclude: true,
            },
        }
    }) */
