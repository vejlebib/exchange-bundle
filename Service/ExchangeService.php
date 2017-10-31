<?php
/**
 * @file
 * Wrapper service for the more specialized exchanges services.
 */

namespace Itk\ExchangeBundle\Service;

use Doctrine\ORM\EntityManager;
use Os2Display\CoreBundle\Events\CronEvent;

/**
 * Class ExchangeService
 * @package Itk\ExchangeBundle\Service
 */
class ExchangeService
{
    protected $exchangeWebService;
    protected $serviceEnabled;
    protected $entityManager;
    protected $resourceMail;

    /**
     * ExchangeService constructor.
     *
     * @param ExchangeWebService $exchangeWebService
     * @param EntityManager $entityManager
     * @param $serviceEnabled
     */
    public function __construct(
        ExchangeWebService $exchangeWebService,
        EntityManager $entityManager,
        $serviceEnabled,
        $resourceMail
    ) {
        $this->exchangeWebService = $exchangeWebService;
        $this->serviceEnabled = $serviceEnabled;
        $this->entityManager = $entityManager;
        $this->resourceMail = $resourceMail;
    }

    /**
     * ik.onCron event listener.
     *
     * Updates calendar slides.
     *
     * @param CronEvent $event
     */
    public function onCron(CronEvent $event)
    {
        $this->updateCalendarSlides();
    }

    /**
     * Get the ExchangeBookings for a resource in an interval.
     *
     * @param $resourceMail
     *   The resource mail.
     * @param $startTime
     *   The start time.
     * @param $endTime
     *   The end time.
     *
     * @return array
     *   Array of ExchangeBookings.
     */
    public function getExchangeBookingsForInterval(
        $resourceMail,
        $startTime,
        $endTime
    ) {
        // Start by getting the bookings from exchange.
        $calendar = $this->exchangeWebService->getResourceBookings($resourceMail, $startTime, $endTime);

        return $calendar->getBookings();
    }

    /**
     * Update the calendar events for calendar slides.
     */
    public function updateCalendarSlides()
    {
        // Only run if enabled.
        if (!$this->serviceEnabled) {
            return;
        }

        // For each calendar slide
        $slides = $this->entityManager
            ->getRepository('Os2DisplayCoreBundle:Slide')
            ->findBySlideType('calendar');

        $todayStart = time() - 3600;
        // Round down to nearest hour
        $todayStart = $todayStart - ($todayStart % 3600);

        $end = strtotime('+7 days', mktime(23, 59, 29));

        $bookings = [];

        try {
            $resourceBookings = $this->getExchangeBookingsForInterval($this->resourceMail, $todayStart, $end);

            if (count($resourceBookings) > 0) {
                $bookings = array_merge($bookings, $resourceBookings);
            }
        } catch (\Exception $e) {
            // Ignore exceptions. The show must keep running, even though we have no connection to exchange.
        }

        // Sort bookings by start time.
        usort($bookings, function ($a, $b) {
            return strcmp($a->getStartTime(), $b->getStartTime());
        });

        // Get data for interest period
        foreach ($slides as $slide) {
            // Save in externalData field
            $slide->setExternalData($bookings);
        }

        $this->entityManager->flush();
    }
}
