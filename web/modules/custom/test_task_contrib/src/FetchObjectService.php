<?php

namespace Drupal\test_task_contrib;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\user\Entity\User;

/**
 * Fetch object from route.
 */
class FetchObjectService implements FetchObjectServiceInterface {

  /**
   * The route match service.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Constructs a FetchObjectService object.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match interface.
   */
  public function __construct(RouteMatchInterface $route_match) {
    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public function fetchUserByRoute() {
    $user = $this->routeMatch->getParameter('user');

    if ($user instanceof User) {
      return $user->label();
    }
    return NULL;
  }

}
