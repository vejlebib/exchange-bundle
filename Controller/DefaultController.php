<?php
/**
 * @file
 * Contains controller actions for the bundle.
 */

namespace Os2Display\ExchangeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class DefaultController.
 */
class DefaultController extends Controller
{
    /**
     * Test controller action.
     *
     * @param string $email The email of the resource to get calendar data from
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function testAction($email)
    {
        $now = time();

        $start = strtotime('-7 days', $now);
        $end = strtotime('+7 days', $now);

        $calendar = $this->get('os2display.exchange.service')
            ->getExchangeBookingsForInterval(
                $email,
                $start,
                $end
            );

        return new JsonResponse($calendar);
    }
}
