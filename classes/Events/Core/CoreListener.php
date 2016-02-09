<?php

namespace Wplog\Events\Core;

use Mockery\Exception;
use Wplog\Events\Event;
use Wplog\Events\Listener;

/**
 * Class CoreListener
 *
 * @since 0.1.0
 * @package Wplog\Events\Core
 */
class CoreListener extends Listener
{
    /**
     * When an upgrader process has completed.
     *
     * @since 0.1.0
     *
     * @param array $args Hook arguments.
     *
     * @return \Wplog\Events\Event
     */
    public function onUpgraderProcessComplete(array $args) : Event
    {
        $upgraderInstance = array_shift($args);
        $data = array_shift($args);

        if ($data['action'] !== 'update' || $data['type'] !== 'core') {
            return new NullEvent();
        }

        $user = wp_get_current_user();

        $event = new CoreEvent();

        $event->setSeverity('notice');
        $event->setUserId($user->ID ?? 'system');
        $event->setBody('WordPress core was upgraded.');

        return $event;
    }
}
