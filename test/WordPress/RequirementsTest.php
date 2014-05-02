<?php

namespace WordPress;

# this class is not autoloaded
require_once(__DIR__ .  '/../../lib/WordPress/Requirements.php');

class PHPRequirementTest extends \PHPUnit_Framework_TestCase {

  function setUp() {
    $this->requirement = new PHPRequirement();
  }

  function test_it_knows_if_minimum_php_requirement_is_not_met() {
    $this->requirement->minimumVersion = '10.1.1';
    $actual = $this->requirement->check();
    $this->assertFalse($actual);
  }

  function test_it_knows_if_minimum_php_requirement_is_met() {
    $this->requirement->minimumVersion = '5.3.2';
    $actual = $this->requirement->check();
    $this->assertTrue($actual);
  }

  function test_it_has_error_message_for_unmet_php_requirement() {
    $this->requirement->minimumVersion = '10.1.1';
    $actual = $this->requirement->message();
    $this->assertRegExp('/10.1.1/', $actual);
    $this->assertRegExp('/' . phpversion() . '/', $actual);
  }
}

class WordPressRequirementTest extends \PHPUnit_Framework_TestCase {

  function setUp() {
    $this->requirement = new WordPressRequirement();
  }

  function changeWordPressVersion($version) {
    global $wp_version;
    $wp_version = $version;
    return $version;
  }

  function test_it_knows_if_minimum_wordpress_requirement_is_not_met() {
    $version = $this->changeWordPressVersion('1.0.0');
    $this->requirement->minimumVersion = '3.8.1';
    $actual = $this->requirement->check();

    $this->assertFalse($actual);
  }

  function test_it_knows_if_minimum_wordpress_requirement_is_met() {
    $version = $this->changeWordPressVersion('5.0.0');
    $this->requirement->minimumVersion = '4.2.1';
    $actual = $this->requirement->check();

    $this->assertTrue($actual);
  }

  function test_it_has_error_message_for_unmet_wordpress_requirement() {
    $version = $this->changeWordPressVersion('5.0.0');
    $this->requirement->minimumVersion = '10.1.1';
    $actual = $this->requirement->message();
    $this->assertRegExp('/10.1.1/', $actual);
    $this->assertRegExp('/5.0.0/', $actual);
  }
}

class PHPExtensionRequirementTest extends \PHPUnit_Framework_TestCase {

  function setUp() {
    $this->requirement = new PHPExtensionRequirement();
  }

  function test_it_knows_if_required_extensions_are_absent() {
    $this->requirement->extensions = array('foo', 'bar');
    $actual = $this->requirement->check();

    $this->assertFalse($actual);
  }

  function test_it_knows_if_required_extensions_are_present() {
    $this->requirement->extensions = array('ctype', 'json');
    $actual = $this->requirement->check();

    $this->assertTrue($actual);
  }

  function test_it_has_error_message_for_unmet_extension() {
    $this->requirement->extensions = array('foo', 'bar');
    $this->requirement->check();
    $actual = $this->requirement->message();

    $this->assertRegExp('/foo/', $actual);
    $this->assertRegExp('/bar/', $actual);
  }
}

class MinRequirementsUnitTest extends \PHPUnit_Framework_TestCase {

  function setUp() {
    $this->requirements = new MinRequirements();
  }

  function changeWordPressVersion($version) {
    global $wp_version;
    $wp_version = $version;
    return $version;
  }

  function test_it_knows_if_minimum_requirements_are_not_satisfied() {
    $wordpressVersion = $this->changeWordPressVersion('1.0.1');
    $actual = $this->requirements->satisfied();

    $this->assertFalse($actual);
  }

  function test_it_knows_if_minimum_requirements_are_satisfied() {
    $wordpressVersion = $this->changeWordPressVersion('10.0.1');
    $actual = $this->requirements->satisfied();

    $this->assertTrue($actual);
  }

  function test_it_has_results_of_minimum_requirements_check() {
    $wordpressVersion = $this->changeWordPressVersion('1.0.1');
    $actual = $this->requirements->satisfied();
    $results = $this->requirements->getResults();

    $this->assertFalse($results[1]['satisfied']);

    $message = $results[1]['requirement']->message();
    $this->assertRegExp('/1.0.1/', $message);
  }

}

class ModernRequirementsUnitTest extends \PHPUnit_Framework_TestCase {

  function setUp() {
    $this->requirements = new ModernRequirements();
  }

  function changeWordPressVersion($version) {
    global $wp_version;
    $wp_version = $version;
    return $version;
  }

  function test_it_knows_if_modern_requirements_are_not_satisfied() {
    $wordpressVersion = $this->changeWordPressVersion('1.0.1');
    $actual = $this->requirements->satisfied();

    $this->assertFalse($actual);
  }

  function test_it_knows_if_modern_requirements_are_satisfied() {
    $wordpressVersion = $this->changeWordPressVersion('10.0.1');
    $actual = $this->requirements->satisfied();

    $this->assertTrue($actual);
  }
}

class FauxPluginTest extends \WP_UnitTestCase {

  function setUp() {
    parent::setUp();

    $this->pluginFile = getcwd() . '/foo.php';
    $this->plugin = new FauxPlugin(
      'wp-syntax-highlighter', array('foo')
    );
  }

  function test_it_stores_plugin_name() {
    $this->assertEquals('wp-syntax-highlighter', $this->plugin->pluginName);
  }

  function test_it_stores_requirements_results() {
    $this->assertEquals(array('foo'), $this->plugin->results);
  }

  function test_it_can_wrap_html_in_div() {
    $html = $this->plugin->toDiv('<p>foo</p>', 'updated');
    $matcher = array(
      'tag' => 'div',
      'attributes' => array('class' => 'updated')
    );

    $this->assertTag($matcher, $html);
  }

  function test_it_can_wrap_correct_content_html_in_div() {
    $html = $this->plugin->toDiv('<p>foo</p>', 'updated');
    $matcher = array(
      'tag' => 'p',
      'content' => 'foo'
    );

    $this->assertTag($matcher, $html);
  }

  function test_it_can_convert_result_to_notice() {
    $requirement = new PHPRequirement();
    $requirement->check();

    $result = array(
      'satisfied' => true,
      'requirement' => $requirement
    );

    $html = $this->plugin->resultToNotice($result);
    $matcher = array(
      'tag' => 'li'
    );

    $this->assertTag($matcher, $html);
  }

  function test_it_can_convert_results_to_notice() {
    $results = array();

    $requirement = new PHPRequirement();
    $requirement->check();
    $result = array(
      'satisfied' => true,
      'requirement' => $requirement
    );
    array_push($results, $result);

    $requirement = new PHPRequirement();
    $requirement->check();
    $result = array(
      'satisfied' => false,
      'requirement' => $requirement
    );
    array_push($results, $result);

    $matcher = array(
      'tag' => 'div',
      'attributes' => array('class' => 'error')
    );

    $this->plugin->results = $results;
    $html = $this->plugin->resultsToNotice();

    $this->assertTag($matcher, $html);
  }

  function test_it_knows_if_page_is_not_an_error_scraper() {
    $this->assertFalse($this->plugin->isErrorScraper());
  }

  function test_it_knows_if_page_is_an_error_scraper() {
    $_GET['action'] = 'error_scrape';
    $this->assertTrue($this->plugin->isErrorScraper());
  }

  function test_it_displays_error_message_if_error_scraper() {
    $_GET['action'] = 'error_scrape';
    ob_start();
    $this->plugin->showError('foo');
    $result = ob_get_clean();

    $this->assertEquals('foo', $result);
  }

  function test_it_triggers_error_if_not_error_scraper() {
    set_error_handler(array($this, 'onError'));
    $this->errorCaught = false;
    $this->plugin->showError('foo');

    $this->assertTrue($this->errorCaught);
  }

  function onError() {
    $this->errorCaught = true;
    restore_error_handler();
  }

  function test_it_displays_error_on_activation_if_scraping() {
    $results = array();

    $requirement = new PHPRequirement();
    $requirement->check();
    $result = array(
      'satisfied' => true,
      'requirement' => $requirement
    );
    array_push($results, $result);

    $requirement = new PHPRequirement();
    $requirement->check();
    $result = array(
      'satisfied' => false,
      'requirement' => $requirement
    );
    array_push($results, $result);

    $matcher = array(
      'tag' => 'div',
      'attributes' => array('class' => 'error')
    );

    $this->plugin->results = $results;
    $_GET['action'] = 'error_scrape';

    ob_start();
    $this->plugin->onActivate();
    $html = ob_get_clean();

    $this->assertTag($matcher, $html);
  }

  function test_it_triggers_error_on_activation_if_not_scraping() {
    $results = array();

    $requirement = new PHPRequirement();
    $requirement->check();
    $result = array(
      'satisfied' => true,
      'requirement' => $requirement
    );
    array_push($results, $result);

    $requirement = new PHPRequirement();
    $requirement->check();
    $result = array(
      'satisfied' => false,
      'requirement' => $requirement
    );
    array_push($results, $result);

    $matcher = array(
      'tag' => 'div',
      'attributes' => array('class' => 'error')
    );

    $this->plugin->results = $results;
    set_error_handler(array($this, 'onError'));
    $this->errorCaught = false;

    ob_start();
    $this->plugin->onActivate();
    $html = ob_get_clean();

    $this->assertTrue($this->errorCaught);
  }

}
