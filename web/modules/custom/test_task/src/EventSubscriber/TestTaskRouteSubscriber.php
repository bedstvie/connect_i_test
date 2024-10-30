<?php

namespace Drupal\test_task\EventSubscriber;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class TestTaskRouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Alter the entity.user.canonical route.
    if ($route = $collection->get('entity.user.canonical')) {
      $route->setRequirement('_user_accessibility', 'TRUE');
    }
  }

}
