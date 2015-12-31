<?php

namespace Wplog\Events;
use Wplog\Support\Uuid;

/**
 * Class Event
 *
 * Events are the "things" which are logged. Events are mostly the very same stuff,
 * but custom event types can be used to denote different types of events and
 * possibly to add custom event mechanics for custom event log endpoints.
 *
 * Subclassing this and setting the `type` property to something descriptive is
 * enough to define a custom type.
 *
 * @since 0.1.0
 * @package Wplog\Events
 */
abstract class Event
{
    /**
     * The type of the log event. E.g. option_change, user_logged_in.
     *
     * @since 0.1.0
     * @access protected
     * @var String
     */
    protected $type = 'event';

    /**
     * Universally unique ID for the log event.
     *
     * @since 0.1.0
     * @access protected
     * @var String
     */
    protected $uuid;

    /**
     * UTC timestamp.
     *
     * @since 0.1.0
     * @access protected
     * @var String
     */
    protected $timestamp;

    /**
     * PSR-3 severity level.
     *
     * @since 0.1.0
     * @access protected
     * @var String
     */
    protected $severity;

    /**
     * Log event message body.
     *
     * @since 0.1.0
     * @access protected
     * @var String
     */
    protected $body;

    /**
     * Additional nullable data to use with the log message.
     *
     * @since 0.1.0
     * @access protected
     * @var String
     */
    protected $context;

    /**
     * If a user initiated the log event, the ID for the user.
     *
     * @since 0.1.0
     * @access protected
     * @var String
     */
    protected $userId;

    /**
     * Event constructor. Get an UUID for the event.
     *
     * @since 0.1.0
     * @return void
     */
    public function __construct()
    {
        $this->uuid = Uuid::getUuid();

        $now = new \DateTime('now');
        $now->setTimezone(new \DateTimeZone('UTC'));
        $timestamp = $now->format('Y-m-d H:i:s');

        $this->timestamp = $timestamp;
    }

    /**
     * Get the UTC timestamp for this event.
     *
     * @since 0.1.0
     * @return String
     */
    public function getTimestamp() : string
    {
        return $this->timestamp;
    }

    /**
     * Set the UTC timestamp for this event.
     *
     * @since 0.1.0
     *
     * @param String $timestamp
     *
     * @return $this
     */
    public function setTimestamp(string $timestamp)
    {
        if (!preg_match('%^\d\d\d\d-\d\d-\d\d \d\d:\d\d:\d\d$%', $timestamp)) {
            throw new \InvalidArgumentException('Invalid timestamp format given, should be Y-m-d H:i:s');
        }

        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get the PSR severity for this event.
     *
     * @since 0.1.0
     * @return String
     */
    public function getSeverity() : string
    {
        return preg_replace('%[^a-z]%', '', strtolower($this->severity));
    }

    /**
     * Set the PSR severity for this event.
     *
     * @since 0.1.0
     *
     * @param String $severity
     *
     * @return $this
     */
    public function setSeverity(string $severity)
    {
        $severity = preg_replace('%[^a-z]%', '', strtolower($severity));

        $this->severity = $severity;

        return $this;
    }

    /**
     * Get the message body for this event.
     *
     * @since 0.1.0
     * @return String
     */
    public function getBody() : string
    {
        return $this->body;
    }

    /**
     * Set the message body for this event.
     *
     * @since 0.1.0
     *
     * @param String $body
     *
     * @return $this
     */
    public function setBody(string $body)
    {
        $this->body = strip_tags(addslashes($body));

        return $this;
    }

    /**
     * Get the additional data for this event.
     *
     * @since 0.1.0
     * @return mixed[]
     */
    public function getContext() : array
    {
        return $this->context;
    }

    /**
     * Set the additional data for this event.
     *
     * @since 0.1.0
     *
     * @param mixed[] $additionalData
     *
     * @return $this
     */
    public function setContext(array $additionalData)
    {
        $this->context = $additionalData;

        return $this;
    }

    /**
     * Get the User ID for the user that initiated this event.
     *
     * @since 0.1.0
     * @return Integer|null
     */
    public function getUserId()
    {
        return $this->userId ?? null;
    }

    /**
     * Set the user ID that initiated this event. Pass in `null` to remove user data.
     *
     * @since 0.1.0
     *
     * @param Integer|null $userId
     *
     * @return $this
     */
    public function setUserId(int $userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get the UUID for this event.
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Get the type slug for this event.
     *
     * @since 0.1.0
     * @return String
     */
    public function getType() : string
    {
        return $this->type;
    }
}
