<?php

namespace WpSyntaxHighlighter;

use Encase\Container;

class LanguageLoaderTest extends \WP_UnitTestCase {

  public $pluginMeta;
  public $container;
  public $loader;

  function setUp() {
    parent::setUp();

    $this->pluginMeta = new PluginMeta('wp-syntax-highlighter.php');

    $container = new Container();
    $container
      ->object('pluginMeta', $this->pluginMeta)
      ->packager('optionsPackager', 'Arrow\Options\Packager')
      ->singleton('loader', 'WpSyntaxHighlighter\LanguageLoader');

    $this->container = $container;
    $this->loader = $container->lookup('loader');
  }

  function test_it_has_a_container() {
    $this->assertEquals($this->container, $this->loader->container);
  }

  function test_it_can_build_slug_for_language_name() {
    $actual = $this->loader->slugFor('foo');
    $this->assertEquals('languages/foo', $actual);
  }

  function test_it_can_schedule_languages_in_script_loader() {
    $this->loader->add('foo');
    $this->loader->add('bar');

    $this->loader->load();

    $this->assertTrue(wp_script_is('languages/foo', 'registered'));
  }

  function test_it_will_not_schedule_same_language_again() {
    $this->loader->add('foo');
    $this->loader->add('bar');
    $this->loader->add('foo');
    $this->loader->add('bar');

    $this->loader->load();

    $this->assertEquals(array('foo', 'bar'), $this->loader->getLanguages());
  }

  function test_it_can_schedule_highlight_js_core() {
    $this->loader->add('foo');
    $this->loader->add('bar');

    $this->loader->load();
    $this->assertTrue(wp_script_is('highlight', 'registered'));
  }

  function test_it_can_schedule_highlight_js_runner() {
    $this->loader->add('foo');
    $this->loader->add('bar');

    $this->loader->load();
    $this->assertTrue(wp_script_is('highlight-options', 'registered'));
  }

}
