<?php

namespace WpSyntaxHighlighter;

class LanguagesTest extends \PHPUnit_Framework_TestCase {

  function test_it_has_languages() {
    $actual = Languages::$names;
    $this->assertGreaterThan(10, count($actual));
  }


}
