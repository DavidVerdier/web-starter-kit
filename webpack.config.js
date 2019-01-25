const Encore = require('@symfony/webpack-encore');
const StyleLintPlugin = require('stylelint-webpack-plugin');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enableLessLoader(function (options) {
        options.relativeUrls = true;
    })
    .enablePostCssLoader(function (options) {
        options.config = {
            path: './postcss.config.js'
        };
    })
    .addPlugin(new StyleLintPlugin({
        files: ['assets/less/**/*.less']
    }))
    .addLoader({
        enforce: 'pre',
        test: /\.jsx?$/,
        exclude: /(node_modules|var\/)/,
        loader: 'eslint-loader',
        options: {
            cache: true
        }
    })
    .addEntry('js/app', './assets/js/app.js')
    .addStyleEntry('css/app', [
        './assets/less/app.less'
    ])
;

module.exports = Encore.getWebpackConfig();
