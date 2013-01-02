<?php

/**
 * @file
 * Contains \Drupal\Component\Plugin\PluginBag.
 */

namespace Drupal\Component\Plugin;

/**
 * Defines an object which stores multiple plugin instances to lazy load them.
 *
 * The \ArrayAccess implementation is only for backwards compatibility, it is
 * deprecated and should not be used by new code.
 */
abstract class PluginBag implements \ArrayAccess, \Iterator, \Countable {

  /**
   * Stores all instantiated plugins.
   *
   * @var array
   */
  protected $pluginInstances = array();

  /**
   * Stores the IDs of all potential plugin instances.
   *
   * @var array
   */
  protected $instanceIDs = array();

  /**
   * Initializes a plugin and stores the result in $this->pluginInstances.
   *
   * @param string $instance_id
   *   The ID of the plugin instance to initialize.
   */
  abstract protected function initializePlugin($instance_id);

  /**
   * Clears all instantiated plugins.
   */
  public function clear() {
    $this->pluginInstances = array();
  }

  /**
   * Determines if a plugin instance exists.
   *
   * @param string $instance_id
   *   The ID of the plugin instance to check.
   *
   * @return bool
   *   TRUE if the plugin instance exists, FALSE otherwise.
   */
  public function has($instance_id) {
    return isset($this->pluginInstances[$instance_id]) || isset($this->instanceIDs[$instance_id]);
  }

  /**
   * Retrieves a plugin instance, initializing it if necessary.
   *
   * @param string $instance_id
   *   The ID of the plugin instance being retrieved.
   */
  public function get($instance_id) {
    if (!isset($this->pluginInstances[$instance_id])) {
      $this->initializePlugin($instance_id);
    }
    return $this->pluginInstances[$instance_id];
  }

  /**
   * Stores an initialized plugin.
   *
   * @param string $instance_id
   *   The ID of the plugin instance being stored.
   * @param mixed $value
   *   An instantiated plugin.
   */
  public function set($instance_id, $value) {
    $this->pluginInstances[$instance_id] = $value;
  }

  /**
   * Removes an initialized plugin.
   *
   * The plugin can still be used, it will be reinitialized.
   *
   * @param string $instance_id
   *   The ID of the plugin instance to remove.
   */
  public function remove($instance_id) {
    unset($this->pluginInstances[$instance_id]);
  }

  /**
   * Implements \ArrayAccess::offsetExists().
   *
   * This is deprecated, use \Drupal\Component\Plugin\PluginBag::has().
   */
  public function offsetExists($offset) {
    return isset($this->pluginInstances[$offset]) || isset($this->instanceIDs[$offset]);
  }

  /**
   * Implements \ArrayAccess::offsetGet().
   *
   * This is deprecated, use \Drupal\Component\Plugin\PluginBag::get().
   */
  public function offsetGet($offset) {
    if (!isset($this->pluginInstances[$offset])) {
      $this->initializePlugin($offset);
    }
    return $this->pluginInstances[$offset];
  }

  /**
   * Implements \ArrayAccess::offsetSet().
   *
   * This is deprecated, use \Drupal\Component\Plugin\PluginBag::set().
   */
  public function offsetSet($offset, $value) {
    $this->pluginInstances[$offset] = $value;
  }

  /**
   * Implements \ArrayAccess::offsetUnset().
   *
   * This is deprecated, use \Drupal\Component\Plugin\PluginBag::remove().
   */
  public function offsetUnset($offset) {
    unset($this->pluginInstances[$offset]);
  }

  /**
   * Implements \Iterator::current().
   */
  public function current() {
    return $this->offsetGet($this->key());
  }

  /**
   * Implements \Iterator::next().
   */
  public function next() {
    next($this->instanceIDs);
  }

  /**
   * Implements \Iterator::key().
   */
  public function key() {
    return key($this->instanceIDs);
  }

  /**
   * Implements \Iterator::valid().
   */
  public function valid() {
    $key = key($this->instanceIDs);
    return $key !== NULL && $key !== FALSE;
  }

  /**
   * Implements \Iterator::rewind().
   */
  public function rewind() {
    reset($this->instanceIDs);
  }

  /**
   * Implements \Countable::count().
   */
  public function count() {
    return count($this->instanceIDs);
  }

}
