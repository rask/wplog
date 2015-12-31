<?php

namespace Wplog\Events\Auth;

use Wplog\Events\Event;
use Wplog\Events\Listener;

/**
 * Class AuthListener
 *
 * @since 0.1.0
 * @package Wplog\Events\Auth
 */
class AuthListener extends Listener
{
    /**
     * User logged in and triggered WP hook `wp_login`, make an event for it.
     *
     * @since 0.1.0
     * @access protected
     *
     * @param mixed[] $args Hook arguments.
     *
     * @return \Wplog\Events\Event
     */
    protected function onWpLogin(array $args) : Event
    {
        $userLogin = array_shift($args);
        $user = array_shift($args);

        $event = new AuthEvent();

        $event->setSeverity('info');
        $event->setUserId($user->ID);
        $event->setBody('User {loginname} logged in.');
        $event->setContext(['loginname' => $userLogin]);

        return $event;
    }

    /**
     * A user is logged out. Log it.
     *
     * We can't hook to `wp_logout` as we cannot determine the
     * logging out user at that point anymore. `clear_auth_cookie` allows us to use
     * `wp_get_current_user` just before auth cookies are cleared.
     *
     * @param mixed[] $args Arguments. `clear_auth_cookie` provides none.
     *
     * @return \Wplog\Events\Event
     */
    protected function onClearAuthCookie(array $args) : Event
    {
        $user = wp_get_current_user();

        $event = new AuthEvent();

        if (!$user) {
            $event->setSeverity('notice');
            $event->setBody('A user logged out, but the user could not be determined.');
        } else {
            $event->setSeverity('info');
            $event->setBody('User {loginname} logged out.');
            $event->setUserId($user->ID);
            $event->setContext(['loginname' => $user->user_login]);
        }

        return $event;
    }
}
