<?php

namespace MailPoet\API\JSON\v1;

use MailPoet\API\JSON\Endpoint as APIEndpoint;
use MailPoet\WP\Functions as WPFunctions;
use MailPoet\Config\AccessControl;
use MailPoet\Config\Activator;
use MailPoet\Settings\SettingsController;
use MailPoet\Config\Populator;

if (!defined('ABSPATH')) exit;

class Setup extends APIEndpoint {
  private $wp;
  public $permissions = [
    'global' => AccessControl::PERMISSION_MANAGE_SETTINGS,
  ];

  function __construct(WPFunctions $wp) {
    $this->wp = $wp;
  }

  function reset() {
    try {
      $settings = new SettingsController();
      $activator = new Activator($settings, new Populator($settings, $this->wp));
      $activator->deactivate();
      $activator->activate();
      $this->wp->doAction('mailpoet_setup_reset');
      return $this->successResponse();
    } catch (\Exception $e) {
      return $this->errorResponse([
        $e->getCode() => $e->getMessage(),
      ]);
    }
  }
}
