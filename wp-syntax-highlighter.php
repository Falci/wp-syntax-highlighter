<?php
/*
Plugin Name: wp-syntax-highlighter
Description: WordPress Syntax Highlighter using Highlight.js
Version: 0.3.0
Author: Darshan Sawardekar
Author URI: http://pressing-matters.io/
Plugin URI: http://wordpress.org/plugins/wp-syntax-highlighter
License: GPLv2
*/

require_once(__DIR__ . '/vendor/dsawardekar/arrow/lib/Arrow/ArrowPluginLoader.php');

function wp_syntax_highlighter_main() {
  $options = array(
    'plugin' => 'WpSyntaxHighlighter\Plugin',
    'arrowVersion' => '0.7.0'
  );

  ArrowPluginLoader::load(__FILE__, $options);
}

wp_syntax_highlighter_main();
