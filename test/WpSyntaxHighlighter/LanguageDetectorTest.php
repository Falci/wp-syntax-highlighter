<?php

namespace WpSyntaxHighlighter;

use Encase\Container;

class LanguageDetectorTest extends \WP_UnitTestCase {

  public $pluginMeta;
  public $container;
  public $detector;

  function setUp() {
    parent::setUp();

    $this->pluginMeta = new PluginMeta('wp-syntax-highlighter.php');

    $container = new Container();
    $container
      ->object('pluginMeta', $this->pluginMeta)
      ->object('assetManager', new \Arrow\AssetManager\AssetManager($container))
      ->object('optionsManager', new OptionsManager($container))
      ->singleton('languageLoader', 'WpSyntaxHighlighter\LanguageLoader')
      ->singleton('languageDetector', 'WpSyntaxHighlighter\LanguageDetector');

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
    $optionsStore = $this->container->lookup('optionsStore');
    $optionsStore->setOption('highlightSyntaxHighlighter', true);

    $actual = $this->detector->isDetectable('SyntaxHighlighter');
    $this->assertTrue($actual);
  }

  function test_it_knows_if_type_is_not_detectable() {
    $optionsStore = $this->container->lookup('optionsStore');
    $optionsStore->load();
    $optionsStore->setOption('highlightSyntaxHighlighter', false);

    $actual = $this->detector->isDetectable('SyntaxHighlighter');
    $this->assertFalse($actual);
  }

  function test_it_can_detect_presence_of_languages() {
    $optionsStore = $this->container->lookup('optionsStore');
    $optionsStore->load();
    $optionsStore->setOption('highlightSyntaxHighlighter', true);

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
    $optionsStore = $this->container->lookup('optionsStore');
    $optionsStore->load();
    $optionsStore->setOption('highlightSyntaxHighlighter', true);

    $content = 'foo';

    $pattern = $this->detector->patternFor('SyntaxHighlighter');
    $matches = $this->detector->detect($pattern, $content);

    $this->assertFalse($matches);
  }

  function test_it_can_check_and_detect_presence_of_languages() {
    $optionsStore = $this->container->lookup('optionsStore');
    $optionsStore->load();
    $optionsStore->setOption('highlightSyntaxHighlighter', true);

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
    $optionsStore = $this->container->lookup('optionsStore');
    $optionsStore->load();
    $optionsStore->setOption('highlightSyntaxHighlighter', true);

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
    $optionsStore = $this->container->lookup('optionsStore');
    $optionsStore->load();
    $optionsStore->setOption('highlightSyntaxHighlighter', true);

    $content = 'foo';

    $this->detector->scanContent($content);
    $actual = $this->container->lookup('languageLoader')->getLanguages();

    $this->assertEquals(array(), $actual);
  }
}
