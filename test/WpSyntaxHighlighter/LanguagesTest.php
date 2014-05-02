<?php

namespace WpSyntaxHighlighter;

class LanguagesTest extends \PHPUnit_Framework_TestCase {

  function setUp() {
    $this->languages = new Languages();
  }

  function test_it_has_languages() {
    $actual = $this->languages->names;
    $this->assertGreaterThan(10, count($actual));
  }


}
