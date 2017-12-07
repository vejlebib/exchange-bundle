<?php
/**
 * @file
 * Contains the Os2Display ExchangeService.
 */

namespace Itk\ExchangeBundle\Service;

use Itk\ExchangeBundle\Model\ExchangeBooking;
use Itk\ExchangeBundle\Model\ExchangeCalendar;

/**
 * Class ExchangeWebService.
 */
class ExchangeWebService
{
    private $client;

    /**
     * ExchangeWebService constructor.
     *
     * @param \Itk\ExchangeBundle\Service\ExchangeSoapClientService $client The soap client service
     */
    public function __construct(ExchangeSoapClientService $client)
    {
        $this->client = $client;
    }

    /**
     * Get an array of calendar items.
     *
     * @param array $itemIds The item ids that should be got. Each item is an array with id and changeKey.
     *
     * @return array The elements that where returned from ews
     */
    private function getBookings($itemIds)
    {
        // Build XML body.
        // To add more fields look at:
        // https://msdn.microsoft.com/en-us/library/office/aa494315(v=exchg.140).aspx
        // for available fields.
        $items = [
            '<GetItem xmlns="http://schemas.microsoft.com/exchange/services/2006/messages">',
            '<ItemShape>',
            '<t:BaseShape>IdOnly</t:BaseShape>',
            '<t:BodyType>Text</t:BodyType>',
            '<t:AdditionalProperties>',
            '<t:FieldURI FieldURI="calendar:IsAllDayEvent" />',
            '<t:FieldURI FieldURI="calendar:End" />',
            '<t:FieldURI FieldURI="calendar:Start" />',
            '<t:FieldURI FieldURI="calendar:Location" />',
            '<t:FieldURI FieldURI="item:Subject" />',
            '<t:FieldURI FieldURI="item:Body" />',
            '</t:AdditionalProperties>',
            '</ItemShape>',
            '<ItemIds>',
        ];

        // Insert items.
        foreach ($itemIds as $item) {
            $items[] = '<t:ItemId Id="'.$item['id'].'" ChangeKey="'.$item['changeKey'].'"/>';
        }

        // Insert closing elements.
        $items[] = '</ItemIds>';
        $items[] = '</GetItem>';

        // Convert to string.
        $body = implode(
            '',
            $items
        );

        $xml = $this->client->request('GetItem', $body);
        $doc = new \DOMDocument();
        $doc->loadXML($xml);

        $xpath = new \DOMXPath($doc);
        $xpath->registerNamespace('t', 'http://schemas.microsoft.com/exchange/services/2006/types');
        $items = $xpath->query('//t:CalendarItem');

        $results = [];

        // Convert $items to $results array.
        if ($items->length) {
            foreach ($items as $item) {
                $results[] = $this->nodeToArray($doc, $item);
            }
        }

        return $results;
    }

    /**
     * Get bookings on a resource.
     *
     * @param $resource   The resource to list
     * @param $from   Unix timestamp for the start date to query Exchange
     * @param $to   Unix timestamp for the end date to query Exchange
     *
     * @return exchangeCalendar Exchange calender with all bookings in the interval
     */
    public function getResourceBookings($resource, $from, $to)
    {
        $calendar = new ExchangeCalendar($resource, $from, $to);

        // Build XML body.
        $requstBody = implode('', [
            '<FindItem  Traversal="Shallow" xmlns="http://schemas.microsoft.com/exchange/services/2006/messages">',
            '<ItemShape>',
            '<t:BaseShape>IdOnly</t:BaseShape>',
            '</ItemShape>',
            '<CalendarView StartDate="'.date('c', $from).'" EndDate="'.date('c', $to).'"/>',
            '<ParentFolderIds>',
            '<t:DistinguishedFolderId Id="calendar">',
            '<t:Mailbox>',
            '<t:EmailAddress>'.$resource.'</t:EmailAddress>',
            '</t:Mailbox>',
            '</t:DistinguishedFolderId>',
            '</ParentFolderIds>',
            '</FindItem>',
        ]);

        // Send request to EWS.
        // To add impersonation: $xml = $this->client->request('FindItem', $body, $resourceToImpersonate);
        $xml = $this->client->request('FindItem', $requstBody);

        // Parse the response.
        $doc = new \DOMDocument();
        $doc->loadXML($xml);

        $xpath = new \DOMXPath($doc);
        $xpath->registerNamespace('t', 'http://schemas.microsoft.com/exchange/services/2006/types');

        // Find the calendar items.
        $calendarItems = $xpath->query('//t:CalendarItem');

        $itemIds = [];

        // Extract item ids.
        foreach ($calendarItems as $calendarItem) {
            $id = $this->nodeToArray($doc, $calendarItem);
            $itemIds[] = [
                'id' => $id['ItemId']['@Id'],
                'changeKey' => $id['ItemId']['@ChangeKey'],
            ];
        }

        $calendarItems = $this->getBookings($itemIds);

        // Iterate $calendarItems.
        foreach ($calendarItems as $item) {
            // Get data from item.
            $subject = array_key_exists('Subject', $item) ? $item['Subject'] : null;
            $isAllDayEvent = array_key_exists('IsAllDayEvent', $item) ? $item['IsAllDayEvent'] : null;
            $location = array_key_exists('Location', $item) ? $item['Location'] : null;
            $startTime = array_key_exists('Start', $item) ? strtotime($item['Start']) : null;
            $endTime = array_key_exists('End', $item) ? strtotime($item['End']) : null;
            $body = array_key_exists('Body', $item) ? $item['Body'] : '';

            // Make sure body is a string.
            if (!is_string($body)) {
                $body = '';
            }

            // Change from string to boolean.
            $isAllDayEvent = 'true' == $isAllDayEvent;

            // Create exchange booking.
            $booking = new ExchangeBooking();
            $booking->setEventName($subject);
            $booking->setIsAllDayEvent($isAllDayEvent);
            $booking->setLocation($location);
            $booking->setStartTime($startTime);
            $booking->setEndTime($endTime);
            $booking->setBody($body);

            $calendar->addBooking($booking);
        }

        return $calendar;
    }

    /**
     * Convert a XML node to en array.
     *
     * From: http://php.net/manual/en/class.domnode.php#115448
     *
     * @param DOMDocument $dom  The dom document
     * @param DOMNode     $node The dom node
     *
     * @return array|bool The node as an array or false if empty
     */
    private function nodeToArray($dom, $node)
    {
        if (!is_a($dom, 'DOMDocument') || !is_a($node, 'DOMNode')) {
            return false;
        }
        $array = false;
        if (empty(trim($node->localName))) {// Discard empty nodes
            return false;
        }
        if (XML_TEXT_NODE == $node->nodeType) {
            return $node->nodeValue;
        }
        foreach ($node->attributes as $attr) {
            $array['@'.$attr->localName] = $attr->nodeValue;
        }
        foreach ($node->childNodes as $childNode) {
            if (1 == $childNode->childNodes->length && XML_TEXT_NODE == $childNode->firstChild->nodeType) {
                $array[$childNode->localName] = $childNode->nodeValue;
            } else {
                if (false !== ($a = self::nodeToArray($dom, $childNode))) {
                    $array[$childNode->localName] = $a;
                }
            }
        }

        return $array;
    }
}
