var path = require('path')
var webpack = require('webpack')
var CopyWebpackPlugin = require('copy-webpack-plugin')
var ExtractTextPlugin = require("extract-text-webpack-plugin")
module.exports = {
    entry: {
        app: './resources/assets/js/app.js', //后台
        //home: './resources/assets/js/home.js', //后台
        vendors: ["vue", "element-ui", "vue-router"]
    },
    output: {
        path: path.resolve(__dirname, './public/dist'),
        publicPath: '/dist/',
        filename: '[name].js',
        chunkFilename: "chunks/[name]-[hash:8].js"
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                loader: 'vue-loader',
                options: {
                    loaders: {
                        css: ExtractTextPlugin.extract({
                            use: 'css-loader',
                            fallback: 'vue-style-loader' // <- this is a dep of vue-loader, so no need to explicitly install if using npm3
                        })
                    }
                },
            },
            {
                test: /\.js$/,
                loader: 'babel-loader',
                // exclude: /node_modules/,
                query: {presets: ['es2015']},
            },
            {
                test: /\.css$/,
                loader: ExtractTextPlugin.extract({fallback: "style-loader", use: "css-loader"})
            },
            {
                test: /\.(eot|svg|ttf|woff|woff2)(\?\S*)?$/,
                loader: 'file-loader',
                options: {
                    name: '../css/[name].[hash:8].[ext]'
                }
            },
            {
                test: /\.(png|jpe?g|gif|svg)(\?\S*)?$/,
                loader: 'file-loader',
                options: {
                    name: '../img/[name].[hash:8].[ext]'
                }
            },
            {
                test: /\.scss$/,
                loader: 'style!css?modules&importLoaders=2&sourceMap&localIdentName=[local]___[hash:base64:5]!autoprefixer?browsers=last 2 version!sass?outputStyle=expanded&sourceMap'
            }
        ]
    },
    plugins: [
        //把指定文件夹下的文件复制到指定的目录
        new CopyWebpackPlugin([{
            from: __dirname + '/resources/assets/library/js',
            to: __dirname + '/public/dist/js',
        }]),
        new CopyWebpackPlugin([{
            from: __dirname + '/resources/assets/library/css',
            to: __dirname + '/public/dist/css',
        }]),
        new CopyWebpackPlugin([{
            from: __dirname + '/resources/assets/sass/app.css',
            to: __dirname + '/public/dist/css/main.css',
        }]),
        new CopyWebpackPlugin([{
            from: __dirname + '/resources/assets/img',
            to: __dirname + '/public/dist/img',
        }]),
        new CopyWebpackPlugin([{
            from: __dirname + '/resources/assets/library/font',
            to: __dirname + '/public/dist/fonts',
        }]),
        new CopyWebpackPlugin([{
            from: __dirname + '/resources/assets/element-theme',
            to: __dirname + '/public/dist/css/element-theme',
        }]),
        new ExtractTextPlugin('/css/app.css'),
        new webpack.optimize.CommonsChunkPlugin({name: 'vendors', filename: 'vendor.js'}),
    ],
    resolve: {
        alias: {vue: 'vue/dist/vue.js'}
    },
    devServer: {
        historyApiFallback: true,
        noInfo: true,
        stats: 'minimal',
    },
    devtool: '#eval-source-map'
}

if (process.env.NODE_ENV === 'production') {
    module.exports.devtool = '#source-map'
    module.exports.plugins = (module.exports.plugins || []).concat([
        new webpack.DefinePlugin({
            'process.env': {
                NODE_ENV: '"production"'
            }
        }),
        new webpack.optimize.UglifyJsPlugin({
            comments: false,
            compress: {
                warnings: false
            },
        }),
        new webpack.LoaderOptionsPlugin({
            minimize: true
        }),
        new webpack.NoEmitOnErrorsPlugin(),
    ])
}