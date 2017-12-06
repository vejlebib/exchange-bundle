<?php
/**
 * @file
 * Contains controller actions for the bundle.
 */

namespace Itk\ExchangeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class DefaultController
 * @package Itk\ExchangeBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * Test controller action.
     *
     * @param $email
     *   The email of the resource to get calendar data from.
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function testAction($email)
    {
        $start = strtotime('-7 days', mktime(23, 59, 29));
        $end = strtotime('+7 days', mktime(23, 59, 29));

        $calendar = $this->get('itk.exchange_service')
            ->getExchangeBookingsForInterval(
                $email,
                $start,
                $end
            );

        return new JsonResponse($calendar);
    }
}
