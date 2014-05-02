<?php

namespace WordPress;

class AssetLoader {

  protected $scheduled = array();
  protected $didLoad = false;

  public function needs() {
    return array();
  }

  public function schedule($slug, $options = null) {
    $asset = $this->assetFor($slug);
    $asset->slug = $slug;

    if (!is_null($options)) {
      $asset->options = $options;
    }

    $this->scheduled[$slug] = $asset;

    return $this;
  }

  public function dependency($slug, $dependencies) {
    $asset = $this->find($slug);
    $asset->dependencies = $dependencies;

    return $this;
  }

  public function localize($slug, $callable) {
    $asset = $this->find($slug);
    $asset->localizer = $callable;

    return $this;
  }

  public function load() {
    if ($this->loaded() === true) {
      return;
    }

    $this->register();
    $this->enqueue();
    $this->didLoad = true;
  }

  function register() {
    foreach ($this->scheduled as $slug => $asset) {
      $asset->register();
    }
  }

  public function loaded() {
    return $this->didLoad;
  }

  public function isScheduled($key) {
    return array_key_exists($key, $this->scheduled);
  }

  public function find($key) {
    return $this->scheduled[$key];
  }

  function enqueue() {
    add_action($this->enqueueAction(), $this->enqueueCallback());
  }

  function doEnqueue() {
    foreach ($this->scheduled as $slug => $asset) {
      $asset->enqueue();
    }
  }

  function enqueueCallback() {
    return array($this, 'doEnqueue');
  }

  /* abstract, implementation included for easier testing */
  function assetFor($slug) {
    return $this->container->lookup($this->assetType($slug));
  }

  function assetType() {
    return 'asset';
  }

  function enqueueAction() {
    return 'wp_enqueue_scripts';
  }

}
