/** @jsx React.DOM */

require('./styles/app.css');

var ArrowApi = require('./ext/ArrowApi').ArrowApi;
var Config   = require('./config');
var Options  = require('./stores/options');

/* app initialization */
var pluginSlug    = 'wp_syntax_highlighter';
var config        = new Config(pluginSlug);
var pluginOptions = config.getOptions();
var api           = new ArrowApi(pluginOptions);
var optionsStore  = new Options(pluginOptions, api);

module.exports = {
  pluginSlug   : pluginSlug,
  api          : api,
  config       : config,
  optionsStore : optionsStore
};
