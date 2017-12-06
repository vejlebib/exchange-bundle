<?php
/**
 * @file
 * Contains the model for an exchange booking.
 */

namespace Itk\ExchangeBundle\Model;

/**
 * Class ExchangeBooking
 * @package Itk\ExchangeBundle\Model
 */
class ExchangeBooking
{
    public $event_name;
    public $is_all_day_event;
    public $start_time;
    public $end_time;
    public $body;
    public $location;

    /**
     * ExchangeBooking constructor.
     *
     * @param string $event_name
     *   The name of the booking.
     * @param bool $is_all_day_event
     *   Whether or not the event is an all day event.
     * @param int $start_time
     *   The booking start time.
     * @param int $end_time
     *   The booking end time.
     * @param string $body
     *   The booking body.
     * @param string $location
     *   The booking location.
     */
    public function __construct(
        $event_name = '',
        $is_all_day_event = false,
        $start_time = 0,
        $end_time = 0,
        $body = '',
        $location = ''
    ) {
        $this->event_name = $event_name;
        $this->is_all_day_event = $is_all_day_event;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
        $this->body = $body;
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return $this->event_name;
    }

    /**
     * @param string $event_name
     */
    public function setEventName($event_name)
    {
        $this->event_name = $event_name;
    }

    /**
     * @return int
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * @param int $start_time
     */
    public function setStartTime($start_time)
    {
        $this->start_time = $start_time;
    }

    /**
     * @return int
     */
    public function getEndTime()
    {
        return $this->end_time;
    }

    /**
     * @param int $end_time
     */
    public function setEndTime($end_time)
    {
        $this->end_time = $end_time;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return bool
     */
    public function isAllDayEvent()
    {
        return $this->is_all_day_event;
    }

    /**
     * @param bool $is_all_day_event
     */
    public function setIsAllDayEvent($is_all_day_event)
    {
        $this->is_all_day_event = $is_all_day_event;
    }
}
