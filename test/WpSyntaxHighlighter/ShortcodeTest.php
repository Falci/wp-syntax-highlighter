<?php

namespace WpSyntaxHighlighter;

use Encase\Container;

class ShortcodeTest extends \WP_UnitTestCase {

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
    $container->singleton('shortcode', 'WpSyntaxHighlighter\Shortcode');

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
