<?php

namespace WpSyntaxHighlighter;

use WordPress\Logger;

class OptionStore {

  public $pluginSlug;
  public $optionName;
  public $optionSanitizer;
  public $defaultOptions = array();

  protected $didLoad = false;
  protected $options = null;
  protected $didSanitize = false;

  public function needs() {
    return array(
      'pluginSlug',
      'optionSanitizer',
      'optionName',
      'defaultOptions'
    );
  }

  public function getPluginSlug() {
    return $this->pluginSlug;
  }

  public function getOptionName() {
    return $this->optionName;
  }

  public function getDefaultOptions() {
    return $this->defaultOptions;
  }

  public function getOptionSanitizer() {
    return $this->optionSanitizer;
  }

  public function loaded() {
    return $this->didLoad;
  }

  public function load() {
    if ($this->didLoad) {
      return $this->options;
    }

    $json          = get_option($this->getOptionName());
    $this->didLoad = true;
    $this->options = $this->parse($json);

    return $this->options;
  }

  public function reload() {
    $this->didLoad = false;
    $this->load();
  }

  public function save() {
    $json       = $this->toJSON($this->options);
    update_option($this->getOptionName(), $json);
  }

  public function clear() {
    delete_option($this->getOptionName());

    $this->didLoad = false;
    $this->options = null;
  }

  public function getOptions() {
    $this->load();
    return $this->options;
  }

  public function getOption($name) {
    $this->load();

    if (array_key_exists($name, $this->options)) {
      $value = $this->options[$name];
    } else {
      $value = $this->defaultOptions[$name];
    }

    return $value;
  }

  public function setOption($name, $value) {
    $this->options[$name] = $value;
  }

  public function register() {
    register_setting(
      $this->getPluginSlug(),
      $this->getOptionName(),
      array($this, 'sanitize')
    );
  }

  public function sanitize($options) {
    /* prevents double sanitization */
    if ($this->isSanitized($options)) {
      return $options;
    } elseif ($this->canRestoreDefaults($options)) {
      $options = $this->getDefaultOptions();
    }

    if (array_key_exists('restoreDefaults', $options)) {
      unset($options['restoreDefaults']);
    }

    $target    = $this->getOptions();
    $sanitized = $this->optionSanitizer->sanitize($options, $target);

    if (!$this->optionSanitizer->hasErrors()) {
      $json = $this->toJSON($sanitized);
    } else {
      $json = $this->toJSON($target);
      $this->notifyErrors($this->optionSanitizer->getErrors());
    }

    $this->didSanitize = true;

    return $json;
  }

  /* Helpers */
  function canRestoreDefaults($options) {
    return array_key_exists('restoreDefaults', $options) &&
     $options['restoreDefaults'] == '1';
  }

  function isSanitized($options) {
    return is_string($options) && $this->didSanitize;
  }

  function notifyErrors($errors) {
    foreach ($errors as $error) {
      add_settings_error(
        $this->getPluginSlug(), null, $error->message, 'error'
      );
    }
  }

  function parse($json) {
    if ($json !== false) {
      $options = $this->toOptions($json);
    } else {
      $options = $this->defaultOptions;
    }

    if (is_null($options)) {
      $options = $this->defaultOptions;
    }

    return $options;
  }

  function toJSON(&$options) {
    return json_encode($options);
  }

  function toOptions($json) {
    return json_decode($json, true);
  }

}
