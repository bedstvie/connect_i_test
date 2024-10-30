<?php

namespace Drupal\test_task\Controller;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller for loading delayed AJAX content.
 */
class BackendContentBlock extends ControllerBase {

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The time service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * Constructs a MyController object.
   *
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   */
  public function __construct(RendererInterface $renderer, TimeInterface $time) {
    $this->renderer = $renderer;
    $this->time = $time;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('renderer'),
      $container->get('datetime.time')
    );
  }

  /**
   * Loads content for the AJAX request.
   */
  public function loadContent() {
    // Get the current time on the server.
    $request_time = $this->time->getRequestTime();

    $time_type = ($request_time & 1) ? $this->t('odd') : $this->t('even');
    $content = [
      '#markup' => $this->t('Server time contains an @type number.', ['@type' => $time_type]),
    ];

    return new JsonResponse([
      'content' => $this->renderer->renderInIsolation($content),
    ]);
  }

}
