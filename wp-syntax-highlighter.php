<?php
/*
Plugin Name: wp-syntax-highlighter
Description: WordPress Syntax Highlighter using Highlight.js
Version: 0.2.2
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
  $loader->register(__FILE__, '0.5.1', 'wp_syntax_highlighter_load');
}

function wp_syntax_highlighter_load() {
  $plugin = \WpSyntaxHighlighter\Plugin::create(__FILE__);
  $plugin->enable();
}

wp_syntax_highlighter_main();
