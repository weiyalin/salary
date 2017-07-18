const elixir = require('laravel-elixir');
const path = require('path');
const webpack = require('webpack')
require('laravel-elixir-vue-2');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application as well as publishing vendor resources.
 |
 */

elixir((mix) => {

    // Elixir.webpack.mergeConfig({
    //     entry:{
    //         app: './resources/assets/js/app.js',
    //         vendors: ["vue", "element-ui", "vue-router"]
    //     },
    //     resolveLoader: {
    //         root: path.join(__dirname, 'node_modules'),
    //     },
    //     output:{
    //         publicPath:'/js/',
    //         // filename: '[name].[hash:8].js',
    //         chunkFilename:'chunks/[name]-[chunkhash:8].js'
    //     },
    //     module: {
    //         loaders: [
    //             {
    //                 test: /\.css$/,
    //                 loader: 'style!css'
    //             }
    //         ]
    //     },
    //     plugins:[
    //         // new webpack.optimize.CommonsChunkPlugin("common.js"),
    //         new webpack.optimize.CommonsChunkPlugin({name: 'vendors', filename: 'vendor.js'}),
    //         new webpack.optimize.UglifyJsPlugin({
    //             compress: {
    //                 warnings: false,
    //             }
    //         }),//TODO 貌似没有起作用
    //     ],
    //     devtool: '#eval-source-map'
    // });

    // mix.sass('app.scss')
    //     .webpack('app.js')
        // .copy('resources/assets/library/js', 'public/js')
        // .copy('resources/assets/library/css', 'public/css')
        // .copy('resources/assets/library/font', 'public/fonts')
        // .copy('resources/assets/img', 'public/img')
        // .copy('resources/assets/element-theme','public/css/element-theme')
        // .version(['js/app.js'])
    ;
        //.webpack('index.css');
});
