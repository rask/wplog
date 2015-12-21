<?php

namespace Wplog\Events\Posts;

use Wplog\Events\Event;
use Wplog\Events\Listener;

/**
 * Class PostListener
 *
 * @since 0.1.0
 * @package Wplog\Events
 */
class PostListener extends Listener
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

            case 'wp_insert_post':
                $event = $this->onWpInsertPost($args);
                break;

        }

        return $event;
    }

    /**
     * Generate an event object for post insertions.
     *
     * @since 0.1.0
     * @access protected
     *
     * @param mixed[] $args Arguments from the hook.
     *
     * @return \Wplog\Events\Event
     */
    private function onWpInsertPost(array $args) : Event
    {
        $postId = array_shift($args);
        $postObj = array_shift($args);
        $update = array_shift($args);

        if ($update) {
            $message = 'Post {postid} ({posttitle}) was updated.';
        } else {
            $message = 'A new post with ID {postid} ({posttitle})';
        }

        $context = [
            'postid' => (int) $postId,
            'posttitle' => $postObj->post_title ?? 'no title set'
        ];

        return $this->createEvent([
            'context' => $context,
            'body' => $message,
            'user_id' => $this->getCurrentUserId(),
            'severity' => 'info',
        ]);
    }
}
