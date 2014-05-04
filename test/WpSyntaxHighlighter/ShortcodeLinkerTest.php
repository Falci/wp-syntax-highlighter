<?php

namespace WpSyntaxHighlighter;

use Encase\Container;

class ShortcodeLinkerTest extends \WP_UnitTestCase {

  function setUp() {
    parent::setUp();

    $this->pluginSlug = 'wp-syntax-highlighter';
    $this->pluginFile = getcwd() . '/' . $this->pluginSlug . '.php';

    $container = new Container();
    $container->object('pluginSlug', $this->pluginSlug);
    $container->object('pluginFile', $this->pluginFile);
    $container->object('pluginVersion', '0.1.0');
    $container->factory('script', 'WordPress\Script');
    $container->singleton('scriptLoader', 'WordPress\ScriptLoader');
    $container->factory('stylesheet', 'WordPress\Stylesheet');
    $container->singleton('stylesheetLoader', 'WordPress\StylesheetLoader');
    $container->singleton('languageLoader', 'WpSyntaxHighlighter\LanguageLoader');
    $container->factory('shortcode', 'WpSyntaxHighlighter\Shortcode');
    $container->singleton('linker', 'WpSyntaxHighlighter\ShortcodeLinker');
    $container->object('languages', array('foo', 'bar'));

    $this->container = $container;
    $this->loader    = $container->lookup('languageLoader');
    $this->shortcode = $container->lookup('shortcode');
    $this->linker    = $container->lookup('linker');
  }

  function test_it_has_a_container() {
    $this->assertEquals($this->container, $this->shortcode->container);
  }

  function test_it_has_language_names() {
    $this->assertEquals(array('foo', 'bar'), $this->linker->languages);
  }

  function test_it_can_create_shortcode_object_for_language() {
    $shortcode = $this->linker->shortcodeFor('foo');
    $this->assertEquals('foo', $shortcode->getLanguage());
  }

  function test_it_can_link_shortcodes_for_language() {
    $this->linker->linkLanguage('foo');
    $this->assertTrue(shortcode_exists('foo'));
  }

  function test_it_does_not_link_shortcode_if_conflicting() {
    add_shortcode('foo', array($this, 'doFooShortcode'));
    $actual = $this->linker->linkLanguage('foo');
    $this->assertFalse($actual);
  }

  function doFooShortcode($params) {

  }

  function test_it_can_link_languages_to_shortcodes() {
    $this->linker->link();

    $this->assertTrue(shortcode_exists('foo'));
    $this->assertTrue(shortcode_exists('bar'));
  }

}
