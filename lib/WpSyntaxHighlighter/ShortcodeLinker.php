<?php

namespace WpSyntaxHighlighter;

class ShortcodeLinker {

  public $languages;
  protected $shortcodes;

  function needs() {
    return array('languages');
  }

  function link() {
    foreach ($this->languages as $language) {
      $this->linkLanguage($language);
    }
  }

  function linkLanguage($language) {
    $shortcode = $this->shortcodeFor($language);

    if (!shortcode_exists($language)) {
      add_shortcode($language, array($shortcode, 'render'));
      return true;
    } else {
      return false;
    }
  }

  function getShortcodes() {
    return $shortcodes;
  }

  function shortcodeFor($language) {
    $shortcode = $this->container->lookup('shortcode');
    $shortcode->setLanguage($language);

    return $shortcode;
  }

}
