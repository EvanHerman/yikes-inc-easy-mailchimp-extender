const path = require('path');
const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );

const ENTRY = path.resolve( __dirname, 'src/index.js' );
const BUNDLE = path.resolve( __dirname, 'build' );
 
module.exports = {
  ...defaultConfig,
  entry: ENTRY,
  output: {
      filename: 'easy-forms-blocks.js',
      path: BUNDLE,
  },
  module: {
    ...defaultConfig.module,
    rules: [
      ...defaultConfig.module.rules,
      {
        test: /\.s[ac]ss$/i,
				use: [
				// Creates `style` nodes from JS strings
					MiniCssExtractPlugin.loader,
					// Translates CSS into CommonJS
					'css-loader',
					// Compiles Sass to CSS
					'sass-loader',
				],
      }
    ]
  },
  plugins: [
    ...defaultConfig.plugins,
    new MiniCssExtractPlugin( {
			filename: 'style.css',
		} ),
  ]
};