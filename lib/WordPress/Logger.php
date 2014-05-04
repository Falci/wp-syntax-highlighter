<?php

namespace WordPress;

class Logger {

  static $instance = null;
  static function log() {
    $instance = self::getInstance();
    call_user_func_array(array($instance, 'logMessage'), func_get_args());
  }

  static function getInstance() {
    if (is_null(self::$instance)) {
      self::$instance = new Logger();
    }

    return self::$instance;
  }

  function logMessage() {
    $args = func_get_args();
    $params = $this->toParams($args);
    error_log(
      $params['name'] . ':' . $this->capture($params['args'])
    );
  }

  function capture($args) {
    ob_start();
    var_dump($args);
    return ob_get_clean();
  }

  function toParams($args) {
    $name = array_shift($args);
    return array(
      'name' => $name,
      'args' => $args
    );
  }

}
