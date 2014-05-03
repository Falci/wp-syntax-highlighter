<?php

namespace WpSyntaxHighlighter;

use Encase\Container;
use WordPress\Script;
use WordPress\ScriptLoader;
use WordPress\Stylesheet;
use WordPress\StylesheetLoader;

class Plugin {

  static $instance = null;
  static function create($file) {
    if (is_null(self::$instance)) {
      self::$instance = new Plugin($file);
    }

    return self::$instance;
  }

  static function getInstance() {
    return self::$instance;
  }

  public $container;

  function __construct($pluginFile) {
    $container = new Container();
    $container
      ->object('pluginFile', $pluginFile)
      ->object('pluginSlug', 'wp_syntax_highlighter')
      ->object('pluginDir', untrailingslashit(plugin_dir_path($pluginFile)))
      ->object('pluginVersion', Version::$version)
      ->object('languages', Languages::$names)

      ->factory('script', 'WordPress\Script')
      ->factory('stylesheet', 'WordPress\Stylesheet')
      ->singleton('scriptLoader', 'WordPress\ScriptLoader')
      ->singleton('stylesheetLoader', 'WordPress\StylesheetLoader')

      ->factory('shortcode', 'WpSyntaxHighlighter\Shortcode')
      ->singleton('languageLoader', 'WpSyntaxHighlighter\LanguageLoader')
      ->singleton('shortcodeLinker', 'WpSyntaxHighlighter\ShortcodeLinker');

    $this->container = $container;
  }

  function lookup($key) {
    return $this->container->lookup($key);
  }

  function enable() {
    add_action('init', array($this, 'linkShortcodes'));
  }

  function linkShortcodes() {
    $this->lookup('shortcodeLinker')->link();
  }

  function filterContent($content) {
    //if (is_singular() && is_main_query() && !$this->filtered) {
      //$this->loadScripts();
      //$this->filtered = true;
    //}

    //return $content;
  }

}
