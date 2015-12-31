<?php

namespace Wplog\Events\Options;

use Wplog\Events\Event;
use Wplog\Events\Listener;

/**
 * Class OptionListener
 *
 * @since 0.1.0
 * @package Wplog\Events
 */
class OptionListener extends Listener
{
    /**
     * Listener for this type of event.
     *
     * @since 0.1.0
     * @access protected
     * @var String
     */
    protected static $eventClass = OptionEvent::class;

    /**
     * Option was updated.
     *
     * @since 0.1.0
     * @access protected
     *
     * @param mixed $args Arguments from hook `updated_option`.
     *
     * @return \Wplog\Events\Event
     */
    protected function onUpdatedOption(array $args) : Event
    {
        $optionName = array_shift($args);
        $oldValue = maybe_serialize(array_shift($args));
        $newValue = maybe_serialize(array_shift($args));

        $userId = $this->getCurrentUserId();

        $message = 'Option `{option_name}` was updated from `{old_value}` to `{new_value}` by user `{uid}`.';

        $context = [
            'option_name' => $optionName,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'uid' => $userId ?? 'null'
        ];

        $eventArgs = [
            'severity' => 'notice',
            'body' => $message,
            'context' => $context,
            'user_id' => $userId
        ];

        return $this->createEvent($eventArgs);
    }
}
