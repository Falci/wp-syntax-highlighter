<?php

namespace WpSyntaxHighlighter;

class ShortcodeLinker {

  public $pluginMeta;
  protected $shortcodes;

  function needs() {
    return array('pluginMeta');
  }

  function link() {
    foreach ($this->pluginMeta->getLanguages() as $language) {
      $this->linkLanguage($language);
    }
  }

  function linkLanguage($language) {
    $safeName = $this->getSafeName($language);
    if (!shortcode_exists($safeName)) {
      $shortcode = $this->shortcodeFor($language);
      add_shortcode($safeName, array($shortcode, 'render'));
      return true;
    } else {
      return false;
    }
  }

  function getSafeName($language) {
    if (strlen($language) >= 2) {
      return $language;
    } else {
      return $language . 'lang';
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
