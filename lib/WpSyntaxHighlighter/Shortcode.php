<?php

namespace WpSyntaxHighlighter;

class Shortcode {

  protected $language = null;

  function needs() {
    return array('languageLoader');
  }

  function setLanguage($language) {
    $this->language = $language;
  }

  function getLanguage() {
    return $this->language;
  }

  function wrap($content) {
    return "<pre><code>$content</code></pre>";
  }

  function notify() {
    $this->languageLoader->add($this->getLanguage());
  }

  function render($params, $content = '') {
    $this->notify();
    return $this->wrap($content);
  }

}
