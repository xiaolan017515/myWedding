const webpack = require("webpack");
const path = require("path");
const fs = require("fs");
const webpackMerge = require("webpack-merge");
const cleanPlugin = require('clean-webpack-plugin');
const autoprefixer = require('autoprefixer');
const ExtractTextPlugin = require("extract-text-webpack-plugin");

const Dashboard = require('webpack-dashboard');
const DashboardPlugin = require('webpack-dashboard/plugin');
const dashboard = new Dashboard();

// const htmlWebpackPlugin = require("html-webpack-plugin");
const TIME = new Date();
const ENV = process.env.NODE_ENV;
const public = path.resolve(__dirname, "public");

const common = {
    entry: {
        app: public + "/index.js",
        vendor: ["jquery", "common", "ajax-plus"]
    },
    output: {
        path: path.join(__dirname, ENV === 'dev' ? "dist" : "build"),
        // filename: ENV === 'dev' ? "[name].js" : "[name].min.js",
        filename: "[name].js",
        chunkFilename: '[name].chunk.js',
        publicPath: "http://localhost:3000/build/"
    },
    watch: true,
    module: {
        preLoaders: [

        ],
        loaders: [{
            test: /\.css|\.less$/,
            loader: ExtractTextPlugin.extract("style", "css!postcss!less")
        }, {
            test: /\.(eot|woff|svg|ttf|woff2|gif|swf)(\?|$)/,
            loader: 'file?name=[hash].[ext]'
        }, {
            test: /\.(png|jpg)$/,
            loader: 'url?limit=30000&name=[hash].[ext]'
        }, {
            test: /\.jsx?$/,
            loader: 'babel-loader',
            exclude: /node_modules/,
            query: {
                plugins: ['transform-runtime'],
                presets: ['es2015', 'stage-0', 'react']
            }
        }]
    },
    postcss: [autoprefixer({
        browsers: ['last 5 versions']
    })],
    resolve: {
        root: path.resolve(__dirname),
        extensions: ['', '.js', '.jsx', '.less', '.css'],
        alias: {
            'jquery': public + "/js/plus/jquery-2.0.3.js",
            'common': public + "/js/static/common/common.js",
            'ajax-plus': public + "/js/static/common/getViewData.js"
        }
    }
}

if (ENV === "dev" || !ENV) {
    module.exports = webpackMerge(common, {
        devtool: "eval-source-map",
        devServer: {
            // publicPath: path.resolve(__dirname, "public"),
            contentBase: "./public", //本地服务器所加载的页面所在的目录
            historyApiFallback: true,
            hot: true,
            inline: true,
            progress: true,
            quiet: true,
            colors: true, //终端中输出结果为彩色
            stats: 'errors-only',
            host: process.env.HOST,
            port: process.env.PORT
        },
        plugins: [
            new DashboardPlugin(dashboard.setData),
            new webpack.HotModuleReplacementPlugin(),
            new webpack.optimize.CommonsChunkPlugin({
                name: "vendor",
                minChunks: "Infinity"
            }),
            new webpack.ProvidePlugin({
                $: 'jquery',
                jQuery: 'jquery'
            })
            // new htmlWebpackPlugin({
            //     title: 'MY WEDDING',
            //     inject: 'body',
            //     filename: "index.html",
            //     template: public + "/template.html"
            // })
        ]
    });
}
if (ENV === "prod") {
    module.exports = webpackMerge(common, {
        plugins: [
            new cleanPlugin(["build"]),
            new DashboardPlugin(dashboard.setData),
            new webpack.optimize.UglifyJsPlugin({
                mangle: {
                    except: ['$super', '$', 'exports', 'require']
                },
                compress: {
                    warnings: false
                }
            }),
            new webpack.optimize.CommonsChunkPlugin({
                name: "vendor",
                minChunks: "Infinity"
            }),
            new webpack.BannerPlugin('更新于 ' + TIME),
            new ExtractTextPlugin("[name].min.css"), new webpack.ProvidePlugin({
                $: 'jquery',
                jQuery: 'jquery'
            })
        ]
    });
}
