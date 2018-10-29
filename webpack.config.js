const path = require('path');
const webpack = require('webpack');

// Configuration for the ExtractTextPlugin.
const extractConfig = {
  use: [
    { loader: 'raw-loader' },
    {
      loader: 'postcss-loader',
      options: {
        plugins: [ require( 'autoprefixer' ) ],
      },
    },
    {
      loader: 'sass-loader',
      query: {
        outputStyle:
          'production' === process.env.NODE_ENV ? 'compressed' : 'nested',
      },
    },
  ],
};

module.exports = {
  entry: './blocks/index.js',
  output: {
      path: path.resolve(__dirname, 'blocks/easy-forms-block/'),
      filename: 'easy-forms-block.js'
  },
  mode: 'development',
  module: {
      rules: [
          {
            test: /\.js$/,
            loader: 'babel-loader',
            query: {
              presets: ['es2015']
            }
          },
          {
            test: /\.scss$/,
            use: [
              {
                loader: "style-loader" // creates style nodes from JS strings
              },
              {
                loader: "css-loader" // translates CSS into CommonJS
              },
              {
                loader: "sass-loader" // compiles Sass to CSS
              }
            ]
          },
      ]
  },
  stats: {
      colors: true
  },
  devtool: 'source-map',
  // watch: true,
  // watchOptions: {
  //   ignored: [ '/node_modules/', '/admin/', '/includes/', '/languages/', '/public/' ]
  // },
  // plugins: [
  //   new BrowserSyncPlugin({
  //     // browse to http://localhost:3000/ during development,
  //     // ./public directory is being served
  //     host: 'mailchimp.test',
  //     port: 80,
  //     server: { baseDir: ['public_html'] }
  //   })
  // ]
};