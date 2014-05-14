<?php

namespace WpSyntaxHighlighter;

class LanguageDetector {

  public $languageLoader;
  public $optionsStore;

  function needs() {
    return array('languageLoader', 'optionsStore');
  }

  function enable() {
    add_filter('the_content', array($this, 'scanContent'));
  }

  /* No actual filtering of content to maintain compatibility */
  /* only scanning for presence of languages */
  function scanContent($content) {
    $this->checkAndDetect('SyntaxHighlighter', $content);
    $this->checkAndDetect('Geshi', $content);

    return $content;
  }

  function checkAndDetect($name, $content) {
    if ($this->isDetectable($name)) {
      $pattern  = $this->patternFor($name);
      $detected = $this->detect($pattern, $content);

      if ($detected !== false) {
        foreach ($detected as $language) {
          $this->notify(strtolower($language));
        }
      }
    }
  }

  function detect($pattern, $content) {
    $matches = array();
    $result = preg_match_all($pattern, $content, $matches);
    if ($result !== false && count($matches[1]) > 0) {
      return $matches[1];
    } else {
      return false;
    }
  }

  function isDetectable($name) {
    $option = "highlight$name";
    return $this->optionsStore->getOption($option);
  }

  function patternFor($type) {
    if ($type == 'SyntaxHighlighter') {
      $attribute = 'class';
    } else {
      $attribute = 'lang';
    }

    $pattern  = '/\<pre\s+' . $attribute;
    $pattern .= '=["\']\s*brush:\s*([a-zA-Z]+);.*/';

    return $pattern;
  }

  function notify($language) {
    $this->languageLoader->add($language);
  }

}
