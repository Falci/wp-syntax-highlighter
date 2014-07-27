/** @jsx React.DOM */
var $            = require('jquery');
var React        = require('react');
var OptionsPage  = require('./components/OptionsPage');
var optionsStore = require('./app').optionsStore;
var config       = require('./app').config;

$(document).ready(function() {
  $('.wrap-static').remove();

  React.renderComponent(
    <OptionsPage
      options={optionsStore.getOptions()}
      themes={config.getThemes()}
    />,
    document.getElementById('wp-syntax-highlighter-app')
  );
});

