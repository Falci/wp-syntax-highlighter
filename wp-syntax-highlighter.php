<?php
/*
Plugin Name: wp-syntax-highlighter
Description: WordPress Syntax Highlighter using Highlight.js
Version: 0.1.2
Author: Darshan Sawardekar
Author URI: http://pressing-matters.io/
Plugin URI: http://wordpress.org/plugins/wp-syntax-highlighter
License: GPLv2
*/

require_once(__DIR__ . '/vendor/dsawardekar/wp-requirements/lib/Requirements.php');

function wp_syntax_highlighter_main() {
  $requirements = new WP_Requirements();

  if ($requirements->satisfied()) {
    wp_syntax_highlighter_register();
  } else {
    $plugin = new WP_Faux_Plugin('WP Syntax Highlighter', $requirements->getResults());
    $plugin->activate(__FILE__);
  }
}

function wp_syntax_highlighter_register() {
  require_once(__DIR__ . '/vendor/dsawardekar/arrow/lib/Arrow/ArrowPluginLoader.php');

  $loader = ArrowPluginLoader::getInstance();
  $loader->register('wp-syntax-highlighter', '0.3.1', 'wp_syntax_highlighter_load');
}

function wp_syntax_highlighter_load() {
  require_once(__DIR__ . '/vendor/autoload.php');

  $plugin = \WpScrollUp\Plugin::create(__FILE__);
  $plugin->enable();
}

wp_syntax_highlighter_main();
