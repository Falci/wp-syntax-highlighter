<?php

namespace WpSyntaxHighlighter;

use Encase\Container;

class LanguageLoaderTest extends \WP_UnitTestCase {

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
    $container->singleton('loader', 'WpSyntaxHighlighter\LanguageLoader');

    $this->container = $container;
    $this->loader = $container->lookup('loader');
  }

  function test_it_has_a_container() {
    $this->assertEquals($this->container, $this->loader->container);
  }

  function test_it_has_a_script_loader() {
    $this->assertInstanceOf('WordPress\ScriptLoader', $this->loader->scriptLoader);
  }

  function test_it_has_plugin_version() {
    $this->assertEquals('0.1.0', $this->loader->pluginVersion);
  }

  function test_it_can_build_slug_for_language_name() {
    $actual = $this->loader->slugFor('foo');
    $this->assertEquals('languages/foo', $actual);
  }

  function test_it_can_build_script_options() {
    $options = $this->loader->getScriptOptions();
    $this->assertEquals('0.1.0', $options['version']);
    $this->assertTrue($options['in_footer']);
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
    $this->assertTrue(wp_script_is('highlight-run', 'registered'));
  }

}
