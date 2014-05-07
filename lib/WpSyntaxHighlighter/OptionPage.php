<?php

namespace WpSyntaxHighlighter;

use WordPress\Logger;

class OptionPage {

  function needs() {
    return array('twigHelper', 'optionStore', 'pluginSlug');
  }

  function getPageTitle() {
    return 'WP Syntax Highlighter | Settings';
  }

  function getMenuTitle() {
    return 'WP Syntax Highlighter';
  }

  function getCapability() {
    return 'manage_options';
  }

  function getMenuSlug() {
    return $this->pluginSlug;
  }

  function register() {
    add_options_page(
      $this->getPageTitle(),
      $this->getMenuTitle(),
      $this->getCapability(),
      $this->getMenuSlug(),
      array($this, 'show')
    );
  }

  function show() {
    $context = $this->getTemplateContext();
    $this->twigHelper->display('options_form', $context);
  }

  function getTemplateContext() {
    $context = array(
      'settings_fields' => $this->getSettingsFields($this->pluginSlug),
      'themes' => $this->container->lookup('themes'),
      'theme' => $this->optionStore->getOption('theme'),
      'highlightSyntaxHighlighter' => $this->getChecked('highlightSyntaxHighlighter'),
      'highlightGeshi' => $this->getChecked('highlightGeshi')
    );

    return $context;
  }

  function getChecked($option) {
    $value = $this->optionStore->getOption($option);
    return $value ? 'checked' : '';
  }

  function getSettingsFields($slug) {
    ob_start();
    settings_fields($slug);
    return ob_get_clean();
  }
}
