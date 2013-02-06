<?php
//
// Created on: <2008-07-16 12:41:28 dis>
//
// SOFTWARE NAME: eZ Event extension for eZ Publish
// SOFTWARE RELEASE: 1.x
// COPYRIGHT NOTICE: Copyright (C) 1999-2013 eZ Systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//

class eZiICalEvent
{
    private $Event;

    const ICAL_DATETIME_FORMAT  = "Ymd\THis";
    const ICAL_DATE_FORMAT      = "Ymd";

    function __construct( $event )
    {
        if ( $event  && $event instanceof eZEvent )
        {
            if ( $event->attribute( 'parent_event_id' ) )
            {
                $this->Event = $event->attribute( 'parent_event' );
            }
            else
            {
                $this->Event = $event;
            }
        }
        else
        {
            $this->Event = false;
        }
    }

    public function toIcal()
    {
        $icalContent = array();
        $icalContent[] = "BEGIN:VCALENDAR";
        $icalContent[] = "PRODID:-//eZ Publish eZEvent 4.0.1//EN";
        $icalContent[] = "VERSION:2.0";

        $icalContent = array_merge( $icalContent, $this->addEventInformation() );

        $icalContent[] = "END:VCALENDAR";
        return implode( "\n", $icalContent );
    }

    private function addEventInformation()
    {
        if ( $this->Event == false )
        {
            return "";
        }
        $eventContent = array();

        $eventContent[] = "BEGIN:VEVENT";

        $eventObject = $this->Event->attribute( 'content_object' );
        $eventOwner = $eventObject->attribute( 'owner' );
        $eventOwnerUser = eZUser::fetch( $eventOwner->attribute( 'id' ) );

        $publishedDate  = date( eZiICalEvent::ICAL_DATETIME_FORMAT, $eventObject->attribute( 'published' ) );
        $modifiedDate  = date( eZiICalEvent::ICAL_DATETIME_FORMAT, $eventObject->attribute( 'modified' ) );

        $eventContent[] = "ORGANIZER;CN=\"" . $eventOwner->attribute( 'name' ) . "\":MAILTO:" . $eventOwnerUser->attribute( 'email' );
        $eventContent[] = eZiICalEvent::createDateEntry( "CREATED", $publishedDate );
        $eventContent[] = "UID:ezevent-" . $eventObject->attribute( 'id' ) . "/" . $this->Event->attribute( 'id' );
        $eventContent[] = eZiICalEvent::createDateEntry( "LAST-MODIFIED:", $modifiedDate );
        $eventContent[] = "SUMMARY:". trim( chunk_split  ( $eventObject->attribute( 'name' ), 70, "\r\n\t" ) );

        $eventContent = array_merge( $eventContent, $this->addDateInformation() );

        if ( $this->addRRuleInformation() )
            $eventContent[] = $this->addRRuleInformation();
        if ( $this->addTranspInformation() )
            $eventContent[] = $this->addTranspInformation();

        $eventContent[] = "END:VEVENT";

        return $eventContent;
    }

    private function addDateInformation()
    {
        $dateContent = array();
        switch( $this->Event->attribute( 'event_type' ) )
        {
            case eZEvent::EVENTTYPE_NORMAL:
            case eZEvent::EVENTTYPE_WEEKLY_REPEAT:
            case eZEvent::EVENTTYPE_MONTHLY_REPEAT:
            {
                $eventStartDate = date( eZiICalEvent::ICAL_DATETIME_FORMAT, $this->Event->attribute( 'current_start_date' ) );
                $eventEndDate   = date( eZiICalEvent::ICAL_DATETIME_FORMAT, $this->Event->attribute( 'current_end_date' ) );
                $dateContent[] = eZiICalEvent::createDateEntry( "DTSTART", $eventStartDate );
                $dateContent[] = eZiICalEvent::createDateEntry( "DTEND", $eventEndDate );
                break;
            }

            case eZEvent::EVENTTYPE_TO_BE_DEFINED:
            case eZEvent::EVENTTYPE_NO_TIME:
            case eZEvent::EVENTTYPE_FULL_DAY:
            case eZEvent::EVENTTYPE_SIMPLE:
            case eZEvent::EVENTTYPE_YEARLY_REPEAT:
            {
                $eventStartDate = date( eZiICalEvent::ICAL_DATE_FORMAT, $this->Event->attribute( 'current_start_date' ) );
                $dateContent[] = eZiICalEvent::createDateEntry( "DTSTART", $eventStartDate );
                break;
            }
        }
        return $dateContent;
    }

    private function addRRuleInformation()
    {
        $eventStartDate = $this->Event->attribute( 'current_start_date' );
        $eventEndDate   = $this->Event->attribute( 'current_end_date' );

        switch( $this->Event->attribute( 'event_type' ) )
        {
            case eZEvent::EVENTTYPE_WEEKLY_REPEAT:
            {
                $numberOfWeeks = floor( ( $this->Event->attribute( 'end_date' ) - $this->Event->attribute( 'start_date' ) ) / 604800);
                return "RRULE:FREQ=WEEKLY;COUNT=" . $numberOfWeeks . ";BYDAY=" . eZiICalEvent::getWeekday( $this->Event->attribute( 'start_date' ) );
                break;
            }
            case eZEvent::EVENTTYPE_MONTHLY_REPEAT:
            {
                $numberOfMonths = ( $this->Event->attribute( 'end' )->year() - $this->Event->attribute( 'start' )->year() ) * 12
                                + ( $this->Event->attribute( 'end' )->month() - $this->Event->attribute( 'start' )->month() );
                return "RRULE:FREQ=MONTHLY;COUNT=" . $numberOfMonths . ";BYMONTHDAY=" . date( 'j',$eventStartDate );
                break;
            }
            case eZEvent::EVENTTYPE_YEARLY_REPEAT:
            {
                return "RRULE:FREQ=YEARLY;BYMONTHDAY=" . date( 'd',$eventStartDate ) . ";BYMONTH=" . date( 'm',$eventStartDate );
                break;
            }
        }
        return false;
    }

    private function addTranspInformation()
    {
        switch( $this->Event->attribute( 'event_type' ) )
        {
            case eZEvent::EVENTTYPE_FULL_DAY:
            case eZEvent::EVENTTYPE_SIMPLE:
            case eZEvent::EVENTTYPE_NORMAL:
            case eZEvent::EVENTTYPE_WEEKLY_REPEAT:
            case eZEvent::EVENTTYPE_MONTHLY_REPEAT:
                return "TRANSP:OPAQUE";

            case eZEvent::EVENTTYPE_NO_TIME:
            case eZEvent::EVENTTYPE_TO_BE_DEFINED:
            case eZEvent::EVENTTYPE_YEARLY_REPEAT:
                return "TRANSP:TRANSPARENT";
        }
        return false;
    }

    static private function createDateEntry( $propertyName, $timestamp, $setTimezone = true )
    {
        if ( $setTimezone )
        {
            return $propertyName . ";TZID=" . date_default_timezone_get() . ":" . $timestamp;
        }
        else
        {
            return $propertyName . ":" . $timestamp;
        }


    }

    static private function getWeekday( $date )
    {
        $weekdays = array( "SU", "MO", "TU", "WE", "TH", "FR", "SA" );
        return $weekdays[(int)date( 'w', $date )];
    }
}

?>
