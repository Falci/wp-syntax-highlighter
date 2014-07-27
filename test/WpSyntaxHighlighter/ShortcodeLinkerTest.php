<?php

namespace WpSyntaxHighlighter;

use Encase\Container;

class ShortcodeLinkerTest extends \WP_UnitTestCase {

  public $pluginMeta;
  public $container;
  public $loader;
  public $shortcode;
  public $linker;

  function setUp() {
    parent::setUp();

    $this->pluginMeta = new PluginMeta('wp-syntax-highlighter.php');

    $container = new Container();
    $container
      ->object('pluginMeta', $this->pluginMeta)
      ->packager('optionsPackager', 'Arrow\Options\Packager')
      ->singleton('languageLoader', 'WpSyntaxHighlighter\LanguageLoader')
      ->factory('shortcode', 'WpSyntaxHighlighter\Shortcode')
      ->singleton('linker', 'WpSyntaxHighlighter\ShortcodeLinker');

    $this->container = $container;
    $this->loader    = $container->lookup('languageLoader');
    $this->shortcode = $container->lookup('shortcode');
    $this->linker    = $container->lookup('linker');
  }

  function test_it_has_a_container() {
    $this->assertEquals($this->container, $this->shortcode->container);
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

    $this->assertTrue(shortcode_exists('javascript'));
    $this->assertTrue(shortcode_exists('coffeescript'));
  }

}
