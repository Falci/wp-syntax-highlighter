<?php

namespace WpSyntaxHighlighter;

use Encase\Container;

class LanguageDetectorTest extends \WP_UnitTestCase {

  function setUp() {
    parent::setUp();

    $this->pluginSlug = 'wp-syntax-highlighter';
    $this->pluginFile = getcwd() . '/' . $this->pluginSlug . '.php';

    $defaultOptions = array(
      'theme' => 'default',
      'tabReplace' => false,
      'highlightSyntaxHighlighter' => true,
      'highlightGeshi' => true
    );

    $container = new Container();
    $container->object('pluginSlug', $this->pluginSlug);
    $container->object('pluginFile', $this->pluginFile);
    $container->object('pluginVersion', '0.1.0');
    $container->factory('script', 'WordPress\Script');
    $container->singleton('scriptLoader', 'WordPress\ScriptLoader');
    $container->factory('stylesheet', 'WordPress\Stylesheet');
    $container->singleton('stylesheetLoader', 'WordPress\StylesheetLoader');
    $container->singleton('languageLoader', 'WpSyntaxHighlighter\LanguageLoader');
    $container->object('optionName', 'wp_syntax_highlighter_options');
    $container->object('defaultOptions', $defaultOptions);
    $container->singleton('optionStore', 'WpSyntaxHighlighter\OptionStore');
    $container->singleton('optionSanitizer', 'WpSyntaxHighlighter\OptionSanitizer');
    $container->singleton('languageDetector', 'WpSyntaxHighlighter\LanguageDetector');
    $container->object('themes', array('foo'));

    $this->container = $container;
    $this->detector = $container->lookup('languageDetector');
  }

  function test_it_has_a_container() {
    $this->assertEquals($this->container, $this->detector->container);
  }

  function test_it_can_build_pattern_for_syntax_highlighter() {
    $pattern = $this->detector->patternFor('SyntaxHighlighter');
    $this->assertTrue(strpos($pattern, 'class') !== false);
  }

  function test_it_can_build_pattern_for_geshi() {
    $pattern = $this->detector->patternFor('Geshi');
    $this->assertTrue(strpos($pattern, 'lang') !== false);
  }

  function test_it_can_notify_language_loader() {
    $this->detector->notify('foo');
    $actual = $this->container->lookup('languageLoader')->getLanguages();
    $this->assertEquals(array('foo'), $actual);
  }

  function test_it_knows_if_type_is_detectable() {
    $optionStore = $this->container->lookup('optionStore');
    $optionStore->setOption('highlightSyntaxHighlighter', true);

    $actual = $this->detector->isDetectable('SyntaxHighlighter');
    $this->assertTrue($actual);
  }

  function test_it_knows_if_type_is_not_detectable() {
    $optionStore = $this->container->lookup('optionStore');
    $optionStore->load();
    $optionStore->setOption('highlightSyntaxHighlighter', false);

    $actual = $this->detector->isDetectable('SyntaxHighlighter');
    $this->assertFalse($actual);
  }

  function test_it_can_detect_presence_of_languages() {
    $optionStore = $this->container->lookup('optionStore');
    $optionStore->load();
    $optionStore->setOption('highlightSyntaxHighlighter', true);

    $content = '
<pre class="brush: php; title: ; notranslate" title="">
function load($num = null) {

<pre class="brush: ruby; title: ; notranslate" title="">
function load($num = null) {
';

    $pattern = $this->detector->patternFor('SyntaxHighlighter');
    $matches = $this->detector->detect($pattern, $content);

    $this->assertEquals(array('php', 'ruby'), $matches);
  }

  function test_it_can_detect_absence_of_languages() {
    $optionStore = $this->container->lookup('optionStore');
    $optionStore->load();
    $optionStore->setOption('highlightSyntaxHighlighter', true);

    $content = 'foo';

    $pattern = $this->detector->patternFor('SyntaxHighlighter');
    $matches = $this->detector->detect($pattern, $content);

    $this->assertFalse($matches);
  }

  function test_it_can_check_and_detect_presence_of_languages() {
    $optionStore = $this->container->lookup('optionStore');
    $optionStore->load();
    $optionStore->setOption('highlightSyntaxHighlighter', true);

    $content = '
<pre class="brush: php; title: ; notranslate" title="">
function load($num = null) {

<pre class="brush: ruby; title: ; notranslate" title="">
function load($num = null) {
';

    $this->detector->checkAndDetect('SyntaxHighlighter', $content);
    $actual = $this->container->lookup('languageLoader')->getLanguages();

    $this->assertEquals(array('php', 'ruby'), $actual);
  }

  function test_it_can_scan_content_for_languages() {
    $optionStore = $this->container->lookup('optionStore');
    $optionStore->load();
    $optionStore->setOption('highlightSyntaxHighlighter', true);

    $content = '<pre class="brush: php; title: ; notranslate" title="">
function load($num = null) {

<pre lang="brush: ruby; title: ; notranslate" title="">
function load($num = null) {
';

    $this->detector->scanContent($content);
    $actual = $this->container->lookup('languageLoader')->getLanguages();

    $this->assertEquals(array('php', 'ruby'), $actual);
  }

  function test_it_can_scan_content_without_languages() {
    $optionStore = $this->container->lookup('optionStore');
    $optionStore->load();
    $optionStore->setOption('highlightSyntaxHighlighter', true);

    $content = 'foo';

    $this->detector->scanContent($content);
    $actual = $this->container->lookup('languageLoader')->getLanguages();

    $this->assertEquals(array(), $actual);
  }
}
