const webpack = require("webpack"),
    path = require("path"),
    fs = require("fs");
    // ExtractTextPlugin = require("extract-text-webpack-plugin");// 独立css

// 省域版
const urlList = "public/js";
const TIME = new Date();
const fileNamed = "index";
module.exports = {
    // devtool: "source-map", // 便于调试
    entry: urlList + "/static/"+fileNamed+".js",
    output: {
        publicPath: "../build/",
        path: path.join(__dirname, "build"),
        filename: ""+fileNamed+".min.js"
    },
    module: {
        preLoaders: [

        ],
        loaders: [
            // {
            //    test: /\.less$/,
            //    loader: ExtractTextPlugin.extract('style-loader',  "css-loader!less-loader")
            // },
            {test: /\.less$/,loader: "style-loader!css-loader!autoprefixer-loader!less-loader?sourceMap"},
            {test: /\.css$/,loader: "style-loader!css-loader!autoprefixer-loader"},
            // {test: /\.(eot|woff|svg|ttf|woff2|gif)(\?|$)/, loader: 'file-loader?limit=30000&name=[hash].[ext]'},
            {
                test: /\.(png|jpg)$/,
                loader: 'url?limit=30000&name=[hash].[ext]'
            }, {
                test: /\.js?$/,
                loader: 'babel-loader',
                exclude: /node_modules/,
                query: {
                    plugins: ['transform-runtime'],
                    presets: ['es2015', 'stage-0', 'react']
                }
            }
        ]
    },
    plugins: [
        // new webpack.optimize.UglifyJsPlugin({
        //     mangle: {
        //         except: ['$super', '$', 'exports', 'require']
        //         //以上变量‘$super’, ‘$’, ‘exports’ or ‘require’，不会被混淆
        //     },
        //     compress: {
        //         warnings: false
        //     }
        // }), // 压缩
        new webpack.DefinePlugin({
            "process.env": {
                NODE_ENV: JSON.stringify("production")
            }
        }), //去除警告
        // new webpack.optimize.CommonsChunkPlugin('common.js'),//提取多个页面之间的公共模块
        new webpack.BannerPlugin('项目打包，' + TIME + ' zhouxinjian') // 头部注释
        // new ExtractTextPlugin(""+fileNamed+".min.css"),

        //全局引入，避免每个页面重复书写
        // new webpack.ProvidePlugin({
        //     $: 'jquery'
        // })

    ],
    resolve: {
        //查找module路径
        root: path.resolve(__dirname),
        //后缀名自动补全，即require模块可以省略不写后缀名
        extensions: ['', '.js', '.jsx', '.less', '.css'],
        // 模块别名定义，方便后续直接引用别名
        alias: {
            'jquery': urlList + "/plus/jquery-2.0.3.js",
            'common': urlList + "/static/common/common.js",
            'ajax-plus': urlList + "/static/common/getViewData.js"
        }
    }
};