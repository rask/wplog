<?php

namespace Wplog\Events\Posts;

use Wplog\Events\Event;
use Wplog\Events\Listener;

/**
 * Class PostMetaListener
 *
 * @since 0.1.0
 * @package Wplog\Events\Posts
 */
class PostMetaListener extends Listener
{
    /**
     * Listener for this type of event.
     *
     * @since 0.1.0
     * @access protected
     * @var String
     */
    protected static $eventClass = PostEvent::class;

    /**
     * Fired when a WP hook is triggered. Should generate arguments for a new event.
     *
     * @since 0.1.0
     *
     * @param String $hook The hook which triggered this listener method.
     * @param mixed[] $args Arguments from the fired WP hook.
     *
     * @return mixed
     */
    public function onHookTrigger($hook, $args) : Event
    {
        $event = null;

        switch ($hook) {

            case 'updated_post_meta':
                $event = $this->onUpdatedPostMeta($args);
                break;
        }

        return $event;
    }

    /**
     * Post meta updated, log it.
     *
     * @since 0.1.0
     * @access protected
     *
     * @param mixed[] $args Hook arguments.
     *
     * @return \Wplog\Events\Event
     */
    protected function onUpdatedPostMeta(array $args) : Event
    {
        $metaId = (int) array_shift($args);
        $postId = (int) array_shift($args);
        $metaKey = array_shift($args);
        $metaValue = maybe_serialize(array_shift($args));

        $message = 'Post meta (meta ID {mid} updated for post {pid}: {key} set to {value}';

        $context = [
            'mid' => $metaId,
            'pid' => $postId,
            'key' => $metaKey,
            'value' => $metaValue
        ];

        return $this->createEvent([
            'severity' => 'info',
            'body' => $message,
            'context' => $context,
            'user_id' => $this->getCurrentUserId()
        ]);
    }
}
