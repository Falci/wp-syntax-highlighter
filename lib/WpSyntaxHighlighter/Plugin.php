<?php

namespace WpSyntaxHighlighter;

use Encase\Container;
use WordPress\Script;
use WordPress\ScriptLoader;
use WordPress\Stylesheet;
use WordPress\StylesheetLoader;
use WordPress\Logger;

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
      ->object('pluginVersion', time())
      ->object('languages', Languages::$names)
      ->object('themes', Themes::$names)
      ->object('defaultOptions', $this->getDefaultOptions())
      ->object('optionName', 'wp_syntax_highlighter_options')

      ->factory('script', 'WordPress\Script')
      ->factory('stylesheet', 'WordPress\Stylesheet')
      ->singleton('scriptLoader', 'WordPress\ScriptLoader')
      ->singleton('stylesheetLoader', 'WordPress\StylesheetLoader')

      ->factory('shortcode', 'WpSyntaxHighlighter\Shortcode')
      ->singleton('languageLoader', 'WpSyntaxHighlighter\LanguageLoader')
      ->singleton('shortcodeLinker', 'WpSyntaxHighlighter\ShortcodeLinker')

      ->singleton('twigHelper', 'WordPress\TwigHelper')
      ->initializer('twigHelper', array($this, 'initTwigHelper'))

      ->singleton('optionStore', 'WpSyntaxHighlighter\OptionStore')
      ->singleton('optionPage', 'WpSyntaxHighlighter\OptionPage')
      ->singleton('optionSanitizer', 'WpSyntaxHighlighter\OptionSanitizer')
      ->singleton('adminScriptLoader', 'WordPress\ScriptLoader')

      ->singleton('languageDetector', 'WpSyntaxHighlighter\LanguageDetector');

    $this->container = $container;
  }

  function lookup($key) {
    return $this->container->lookup($key);
  }

  function enable() {
    add_action('init', array($this, 'initFrontEnd'));
    add_action('admin_init', array($this, 'initOptionStore'));
    add_action('admin_menu', array($this, 'initOptionPage'));
  }

  function getDefaultOptions() {
    return array(
      'theme' => 'default',
      'tabReplace' => false,
      'highlightSyntaxHighlighter' => true,
      'highlightGeshi' => true
    );
  }

  function initOptionStore() {
    $this->lookup('optionStore')->register();
  }

  function initAdminScripts() {
    $options = array(
      'version' => Version::$version,
      'in_footer' => true
    );

    $loader = $this->lookup('adminScriptLoader');
    $loader->stream('wp-syntax-highlighter-options', $options);
  }

  function initOptionPage() {
    $this->lookup('optionPage')->register();
    $this->initAdminScripts();
  }

  function initTwigHelper($twigHelper, $container) {
    $twigHelper->setBaseDir($container->lookup('pluginDir'));
  }

  function initFrontEnd() {
    $this->lookup('shortcodeLinker')->link();
    $this->lookup('languageDetector')->enable();

    add_action('wp_footer', array($this, 'loadLanguages'));
  }

  function loadLanguages() {
    $this->loadTheme();
    $this->lookup('languageLoader')->load(
      array($this, 'getPluginOptions')
    );
  }

  function loadTheme() {
    $theme = $this->getTheme();
    $options = array(
      'version' => $this->lookup('pluginVersion'),
      'media' => 'all'
    );

    $this->lookup('stylesheetLoader')->stream($theme, $options);
  }

  function getTheme() {
    return $this->lookup('optionStore')->getOption('theme');
  }

  function getPluginOptions($script) {
    $options = $this->lookup('optionStore')->getOptions();
    $options['languages'] = $this->lookup('languageLoader')->getLanguages();

    return $options;
  }

  function filterContent($content) {
//if (is_singular() && is_main_query() && !$this->filtered) {
  //$this->loadScripts();
  //$this->filtered = true;
//}

//return $content;
  }

}
