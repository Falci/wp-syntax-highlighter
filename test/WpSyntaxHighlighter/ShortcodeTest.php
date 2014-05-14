<?php

namespace WpSyntaxHighlighter;

use Encase\Container;

class ShortcodeTest extends \WP_UnitTestCase {

  public $container;
  public $pluginMeta;
  public $loader;
  public $shortcode;

  function setUp() {
    parent::setUp();

    $this->pluginMeta = new PluginMeta('wp-syntax-highlighter.php');

    $container = new Container();
    $container
      ->object('pluginMeta', $this->pluginMeta)
      ->object('assetLoader', new \Arrow\AssetManager\AssetManager($container))
      ->singleton('languageLoader', 'WpSyntaxHighlighter\LanguageLoader')
      ->singleton('shortcode', 'WpSyntaxHighlighter\Shortcode');

    $this->container = $container;
    $this->loader = $container->lookup('languageLoader');
    $this->shortcode = $container->lookup('shortcode');
  }

  function test_it_has_a_container() {
    $this->assertEquals($this->container, $this->shortcode->container);
  }

  function test_it_stores_a_language() {
    $this->shortcode->setLanguage('foo');
    $this->assertEquals('foo', $this->shortcode->getLanguage());
  }

  function test_it_can_wrap_content() {
    $actual = $this->shortcode->wrap('foo');
    $matcher = array(
      'tag' => 'pre',
      'child' => array(
        'tag' => 'code',
        'content' => 'foo'
      )
    );

    $this->assertTag($matcher, $actual);
  }

  function test_it_can_notify_language_loader() {
    $this->shortcode->setLanguage('foo');
    $this->shortcode->notify();

    $this->assertEquals(array('foo'), $this->loader->getLanguages());
  }

  function test_it_can_render_language_shortcode() {
    $this->shortcode->setLanguage('foo');
    $actual = $this->shortcode->render(array(), 'lorem ipsum');
    $matcher = array(
      'tag' => 'pre',
      'child' => array(
        'tag' => 'code',
        'content' => 'lorem ipsum'
      )
    );

    $this->assertTag($matcher, $actual);
  }

  function test_it_adds_rendered_languages_to_language_loader() {
    $this->shortcode->setLanguage('foo');
    $this->shortcode->render('lorem');
    $this->shortcode->render('ipsum');

    $actual = $this->loader->getLanguages();
    $this->assertEquals(array('foo'), $actual);
  }
}
