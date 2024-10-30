<?php

namespace Drupal\test_task_contrib;

/**
 * Defines an interface for the contrib service.
 */
interface FetchObjectServiceInterface {

  /**
   * Gets a user from the current route and returns their label.
   *
   * @return string|null
   *   The user label, or NULL if no user is found.
   */
  public function fetchUserByRoute();

}
