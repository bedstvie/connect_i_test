<?php

namespace Drupal\test_task;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\node\Entity\Node;
use Drupal\test_task_contrib\FetchObjectService;

/**
 * Decorator for Fetch Object service.
 */
class FetchObjectServiceDecorator extends FetchObjectService {

  /**
   * The route match service.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Constructs a ContribServiceDecorator.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match interface.
   */
  public function __construct(RouteMatchInterface $route_match) {
    $this->routeMatch = $route_match;
  }

  /**
   * Gets a node from the current route and returns its title.
   *
   * @return string|null
   *   The node title, or NULL if no node is found.
   */
  public function fetchNodeByRoute() {
    $node = $this->routeMatch->getParameter('node');

    if ($node instanceof Node) {
      return $node->label();
    }
    return NULL;
  }

  /**
   * Check if method exist.
   *
   * @param string $object_type
   *   Entity type.
   *
   * @return bool
   *   Flag about existing method.
   */
  public function fetchObjectMethodExist(string $object_type) {
    $method = $this->getFetchObjectMethodName($object_type);

    return method_exists($this, $method);
  }

  /**
   * Get fetch object method name.
   *
   * @param string $object_type
   *   Entity type.
   *
   * @return string
   *   Method name.
   */
  protected function getFetchObjectMethodName(string $object_type) {
    $object_type = ucfirst(strtolower($object_type));

    return 'fetch' . $object_type . 'ByRoute';
  }

  /**
   * Gets a node from the current route and returns its title.
   *
   * @param string $object_type
   *   Entity type.
   *
   * @return string|null
   *   The node title, or NULL if no node is found.
   */
  public function fetchObjectByRoute(string $object_type) {
    if ($this->fetchObjectMethodExist($object_type)) {
      $method = $this->getFetchObjectMethodName($object_type);

      return $this->{$method}();
    }

    return NULL;
  }

}
