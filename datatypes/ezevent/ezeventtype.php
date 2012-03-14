<?php
//
// Created on: <2008-07-16 12:41:28 dis>
//
// SOFTWARE NAME: eZ Event extension for eZ Publish
// SOFTWARE RELEASE: 1.x
// COPYRIGHT NOTICE: Copyright (C) 1999-2012 eZ Systems AS
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

class eZEventType extends eZDataType
{
    const DATA_TYPE_STRING          = 'ezevent';
    const DEFAULT_FIELD             = 'data_int1';
    const PARTICIPANT_FIELD         = 'data_int2';
    const ADJUSTMENT_FIELD          = 'data_text5';

    const DEFAULT_EMTPY             = 0;
    const DEFAULT_CURRENT_DATE      = 1;
    const DEFAULT_ADJUSTMENT        = 2;

    public function __construct()
    {
        $this->eZDataType(
            self::DATA_TYPE_STRING,
            ezpI18n::tr( 'ezevent/datatypes', "Event", 'Datatype name' ),
            array(
                'serialize_supported' => true
            )
        );
    }

    public function validateDateTimeHTTPInput( $day, $month, $year, $hour, $minute, $contentObjectAttribute, $acceptEmpty = true )
    {
        if ( $year === '' &&
                $month === '' &&
                $day === '' &&
                $hour === '' &&
                $minute === '' )
        {
            if ( $acceptEmpty )
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        $state = eZDateTimeValidator::validateDate( $day, $month, $year );
        if ( $state == eZInputValidator::STATE_INVALID )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'ezevent/datatypes',
                                                                 'Date is not valid.' ) );
            return false;
        }

        $state = eZDateTimeValidator::validateTime( $hour, $minute );
        if ( $state == eZInputValidator::STATE_INVALID )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'ezevent/datatypes',
                                                                 'Time is not valid.' ) );
            return false;
        }
        if ( !$state )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'ezevent/datatypes',
                                                                    'Missing datetime input.' ) );
        }
        return $state;
    }

    /**
     * Validate HTTP input
     *
     * @param mixed $http
     * @param mixed $base
     * @param mixed $contentObjectAttribute
     * @access public
     * @return void
     */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $contentObjectAttributeId = $contentObjectAttribute->attribute( 'id' );

        $eventType   = $http->postVariable( $base . '_event_typeofevent_' . $contentObjectAttributeId );
        $eventModeFullDay = $http->hasPostVariable( $base . '_event_eventmode_' . $contentObjectAttributeId );

        $year   = $http->postVariable( $base . '_event_year_' . $contentObjectAttributeId );
        $month  = $http->postVariable( $base . '_event_month_' . $contentObjectAttributeId );
        $day    = $http->postVariable( $base . '_event_day_' . $contentObjectAttributeId );
        $hour   = ( $eventModeFullDay )? 12 : $http->postVariable( $base . '_event_hour_' . $contentObjectAttributeId );
        $minute = ( $eventModeFullDay )? 0 : $http->postVariable( $base . '_event_minute_' . $contentObjectAttributeId );

        $classAttribute = $contentObjectAttribute->contentClassAttribute();

        $endYear   = $http->postVariable( $base . '_eventend_year_' . $contentObjectAttributeId .'end' );
        $endMonth  = $http->postVariable( $base . '_eventend_month_' . $contentObjectAttributeId .'end' );
        $endDay    = $http->postVariable( $base . '_eventend_day_' . $contentObjectAttributeId .'end' );
        $endHour   = ( $eventModeFullDay )? 12 : $http->postVariable( $base . '_eventend_hour_' . $contentObjectAttributeId .'end' );
        $endMinute = ( $eventModeFullDay )? 0 : $http->postVariable( $base . '_eventend_minute_' . $contentObjectAttributeId .'end' );

        $errorOccured = false;

        switch( $eventType )
        {
            case eZEvent::EVENTTYPE_FULL_DAY:
            {
                $hour = 0;
                $minute = 0;
                $endHour = 0;
                $endMinute = 0;
            }
            case eZEvent::EVENTTYPE_NORMAL:
            {
                if ( !$this->validateDateTimeHTTPInput( $day, $month, $year, $hour, $minute, $contentObjectAttribute, false ) )
                {
                    $errorOccured = true;
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'ezevent/datatypes',
                                                                        'Invalid start date.' ) );
                }

                if  ( $endDay != '' && $endMonth != '' &&  $endYear != '' )
                {
                    if ( !$this->validateDateTimeHTTPInput( $endDay, $endMonth, $endYear, $endHour, $endMinute, $contentObjectAttribute, false  ) )
                    {
                        $errorOccured = true;
                        $contentObjectAttribute->setValidationError( ezpI18n::tr( 'ezevent/datatypes',
                                                                            'Invalid end date.' ) );
                    }

                    $startDateTime = new eZDateTime();
                    $startDateTime->setMDYHMS( (int)$month, (int)$day, (int)$year, (int)$hour, (int)$minute, 0 );
                    $endDateTime = new eZDateTime();
                    $endDateTime->setMDYHMS( (int)$endMonth, (int)$endDay, (int)$endYear, (int)$endHour, (int)$endMinute, 0 );
                    if ( $endDateTime->timeStamp() < $startDateTime->timeStamp() )
                    {
                        $errorOccured = true;
                        $contentObjectAttribute->setValidationError( ezpI18n::tr( 'ezevent/datatypes',
                                                                            'End date before start date.' ) );
                    }
                }

                break;
            }
            case eZEvent::EVENTTYPE_WEEKLY_REPEAT:
            case eZEvent::EVENTTYPE_MONTHLY_REPEAT:
            case eZEvent::EVENTTYPE_YEARLY_REPEAT:
            {
                if ( !$this->validateDateTimeHTTPInput( $day, $month, $year, 0, 0, $contentObjectAttribute, false ) )
                {
                    $errorOccured = true;
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'ezevent/datatypes',
                                                                        'Invalid start date.' ) );
                }

                $state = eZDateTimeValidator::validateTime( $hour, $minute );
                if ( $state == eZInputValidator::STATE_INVALID )
                {
                    $errorOccured = true;
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'ezevent/datatypes',
                                                                        'Start time is not valid.' ) );
                }
                $state = eZDateTimeValidator::validateTime( $hour, $minute );
                if ( $state == eZInputValidator::STATE_INVALID )
                {
                    $errorOccured = true;
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'ezevent/datatypes',
                                                                        'End time is not valid.' ) );
                }
                if ( $endHour*60+$endMinute < $hour*60+$minute )
                {
                    $errorOccured = true;
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'ezevent/datatypes',
                                                                        'End time before start time.' ) );
                }
                if  ( $endDay != '' && $endMonth != '' &&  $endYear != '' )
                {
                    if ( !$this->validateDateTimeHTTPInput( $endDay, $endMonth, $endYear, $endHour, $endMinute, $contentObjectAttribute, false  ) )
                    {
                        $errorOccured = true;
                        $contentObjectAttribute->setValidationError( ezpI18n::tr( 'ezevent/datatypes',
                                                                            'Invalid end date.' ) );
                    }

                    $startDateTime = new eZDateTime();
                    $startDateTime->setMDYHMS( (int)$month, (int)$day, (int)$year, (int)$hour, (int)$minute, 0 );
                    $endDateTime = new eZDateTime();
                    $endDateTime->setMDYHMS( (int)$endMonth, (int)$endDay, (int)$endYear, (int)$endHour, (int)$endMinute, 0 );
                    if ( $endDateTime->timeStamp() < $startDateTime->timeStamp() )
                    {
                        $errorOccured = true;
                        $contentObjectAttribute->setValidationError( ezpI18n::tr( 'ezevent/datatypes',
                                                                            'End date before start date.' ) );
                    }
                }
                if  ( $endDay != '' && $endMonth != '' &&  $endYear != '' )
                {
                    if ( !$this->validateDateTimeHTTPInput( $endDay, $endMonth, $endYear, $endHour, $endMinute, $contentObjectAttribute, false  ) )
                    {
                        $errorOccured = true;
                        $contentObjectAttribute->setValidationError( ezpI18n::tr( 'ezevent/datatypes',
                                                                            'Invalid end date.' ) );
                    }

                    $startDateTime = new eZDateTime();
                    $startDateTime->setMDYHMS( (int)$month, (int)$day, (int)$year, 0, 0, 0 );
                    $endDateTime = new eZDateTime();
                    $endDateTime->setMDYHMS( (int)$endMonth, (int)$endDay, (int)$endYear, 0, 0, 0 );
                    if ( $endDateTime->timeStamp() < $startDateTime->timeStamp() )
                    {
                        $errorOccured = true;
                        $contentObjectAttribute->setValidationError( ezpI18n::tr( 'ezevent/datatypes',
                                                                            'End date before start date.' ) );
                    }
                }
                break;
            }
        }


        if ( $errorOccured == true )
        {
            return eZInputValidator::STATE_INVALID;
        }
        else
        {
            return eZInputValidator::STATE_ACCEPTED;
        }
    }

    /**
     * Receives and stores HTTP input values
     *
     * @param SomeHttpClass $http
     * @param string $base
     * @param eZContentObjectAttribute? $contentObjectAttribute
     * @return void
     */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $contentObjectAttributeId = $contentObjectAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $base . '_event_year_' . $contentObjectAttributeId ) and
             $http->hasPostVariable( $base . '_event_month_' . $contentObjectAttributeId ) and
             $http->hasPostVariable( $base . '_event_day_' . $contentObjectAttributeId ) and
             $http->hasPostVariable( $base . '_event_hour_' . $contentObjectAttributeId ) and
             $http->hasPostVariable( $base . '_event_minute_' . $contentObjectAttributeId ) )
        {
            $eventType = $http->postVariable( $base . '_event_typeofevent_' . $contentObjectAttributeId );

            $year      = $http->postVariable( $base . '_event_year_' . $contentObjectAttributeId );
            $month     = $http->postVariable( $base . '_event_month_' . $contentObjectAttributeId );
            $day       = $http->postVariable( $base . '_event_day_' . $contentObjectAttributeId );
            $hour      = $http->postVariable( $base . '_event_hour_' . $contentObjectAttributeId );
            $minute    = $http->postVariable( $base . '_event_minute_' . $contentObjectAttributeId );

            $endYear   = $http->postVariable( $base . '_eventend_year_' . $contentObjectAttributeId . 'end' );
            $endMonth  = $http->postVariable( $base . '_eventend_month_' . $contentObjectAttributeId . 'end' );
            $endDay    = $http->postVariable( $base . '_eventend_day_' . $contentObjectAttributeId . 'end' );
            $endHour   = $http->postVariable( $base . '_eventend_hour_' . $contentObjectAttributeId . 'end' );
            $endMinute = $http->postVariable( $base . '_eventend_minute_' . $contentObjectAttributeId . 'end' );

            if ( $hour == '' )
            {
                $hour = 0;
            }
            if ( $minute == '' )
            {
                $minute = 0;
            }

            if ( $endHour == '' )
            {
                $endHour = 0;
            }
            if ( $endMinute == '' )
            {
                $endMinute = 0;
            }

            $dateTime = new eZDateTime();
            $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
            if ( ( $year === '' && $month === '' && $day === '' &&
                   $hour === '' && $minute === '' ) ||
                 !checkdate( $month, $day, $year ) || $year < 1970 )
            {
                $dateTime->setTimeStamp( 0 );
            }
            else
            {
                $dateTime->setMDYHMS( (int)$month, (int)$day, (int)$year, (int)$hour, (int)$minute, 0 );
            }

            $endDateTime = new eZDateTime();
            $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
            if ( ( $endYear == '' && $endMonth == '' && $endDay == '' &&
                   $endHour == '' && $endMinute == '' ) ||
                 !checkdate( $endMonth, $endDay, $endYear ) || $endYear < 1970 )
            {
                $endDateTime->setTimeStamp( 0 );
            }
            else
            {
                $endDateTime->setMDYHMS( (int)$endMonth, (int)$endDay, (int)$endYear, (int)$endHour, (int)$endMinute, 0 );
            }

            $this->fetchAttendiesObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute );


            // Fetch event to also keep data, not accessible through form
            $event = eZEvent::fetchForObject(
                $contentObjectAttributeId,
                $contentObjectAttribute->attribute( 'version' )
            );

            $event->setAttribute( 'start_date', $dateTime->timeStamp() );
            $event->setAttribute( 'end_date', $endDateTime->timeStamp() );
            $event->setAttribute( 'event_type', $eventType );
            $event->store();

            $contentObjectAttribute->Content = null;

            return true;
        }
        return false;
    }

    /**
     * Return datatype content for template
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @return eZEvent
     */
    function objectAttributeContent( $contentObjectAttribute )
    {

        $event = eZEvent::fetchForObject(
            $contentObjectAttribute->attribute( 'id' ),
            $contentObjectAttribute->attribute( 'version' )
        );
        if ( !$event )
        {
            // Create a new event, if there is no event yet assiciated with the
            // requested content abject attribute.
            $data = array(
                'contentobject_attribute_id' => $contentObjectAttribute->attribute( 'id' ),
                'version'                    => $contentObjectAttribute->attribute( 'version' ),
                'start_date'                 => 0,
                'end_date'                   => 0,
                'event_type'                 => eZEvent::EVENTTYPE_NORMAL
            );

            $event = new eZEvent( $data );
            $event->store();
        }
        $contentObjectAttribute->Content = $event;
        return $event;
    }

    function isIndexable()
    {
        return true;
    }

    function isInformationCollector()
    {
        return false;
    }

    function metaData( $contentObjectAttribute )
    {
        $event = eZEvent::fetchForObject(
            $contentObjectAttribute->attribute( 'id' ),
            $contentObjectAttribute->attribute( 'version' )
        );
        if ( $event )
            return $event->attribute( 'start_date' );
        else
            return 0;
    }

    /**
     * Create string representation of datatype
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @return string
     */
    function toString( $contentObjectAttribute )
    {
        $event = eZEvent::fetchForObject(
            $contentObjectAttribute->attribute( 'id' ),
            $contentObjectAttribute->attribute( 'version' )
        );
        if ( $event )
        {
            $type      = $event->attribute( 'event_type' );
            $startDate = $event->attribute( 'start_date' );
            $endDate   = $event->attribute( 'end_date' );

            return  $type . "|" . $startDate . "|" . $endDate;
        }

        return '';
    }

    /**
     * Create event from its string representation
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @param string $string
     * @return bool
     */
    function fromString( $contentObjectAttribute, $string )
    {
        list( $eventType, $startDate, $endDate  ) = explode( "|", $string, 3 );
        $data = array(
            'contentobject_attribute_id' => $contentObjectAttribute->attribute( 'id' ),
            'version'                    => $contentObjectAttribute->attribute( 'version' ),
            'start_date'                 => $startDate,
            'end_date'                   => $endDate,
            'event_type'                 => $eventType,
        );

        $event = new eZEvent( $data );
        $event->store();

        return true;
    }

    function initializeClassAttribute( $classAttribute )
    {
        if ( $classAttribute->attribute( self::DEFAULT_FIELD ) == null )
            $classAttribute->setAttribute( self::DEFAULT_FIELD, 0 );
        if ( $classAttribute->attribute( self::PARTICIPANT_FIELD ) == null )
            $classAttribute->setAttribute( self::PARTICIPANT_FIELD, 0 );
        $classAttribute->store();
    }

    static function parseXML( $xmlText )
    {
        $dom = new DOMDocument;
        $success = $dom->loadXML( $xmlText );
        return $dom;
    }

    function classAttributeContent( $classAttribute )
    {
        $xmlText = $classAttribute->attribute( 'data_text5' );
        if ( trim( $xmlText ) == '' )
        {
            $classAttrContent = eZEventType::defaultClassAttributeContent();
            return $classAttrContent;
        }
        $doc = eZEventType::parseXML( $xmlText );
        $root = $doc->documentElement;
        $type = $root->getElementsByTagName( 'year' )->item( 0 );
        if ( $type )
        {
            $content['year'] = $type->getAttribute( 'value' );
        }
        $type = $root->getElementsByTagName( 'month' )->item( 0 );
        if ( $type )
        {
            $content['month'] = $type->getAttribute( 'value' );
        }
        $type = $root->getElementsByTagName( 'day' )->item( 0 );
        if ( $type )
        {
            $content['day'] = $type->getAttribute( 'value' );
        }
        $type = $root->getElementsByTagName( 'hour' )->item( 0 );
        if ( $type )
        {
            $content['hour'] = $type->getAttribute( 'value' );
        }
        $type = $root->getElementsByTagName( 'minute' )->item( 0 );
        if ( $type )
        {
            $content['minute'] = $type->getAttribute( 'value' );
        }
        return $content;
    }

    function defaultClassAttributeContent()
    {
        return array( 'year' => '',
                      'month' => '',
                      'day' => '',
                      'hour' => '',
                      'minute' => '' );
    }

    /**
     * Intilize object attribute
     *
     * What is this intended to do?
     *
     * Parent class documentation states: "Initializes attribute with some
     * data." without any real context.
     *
     * @param mixed $contentObjectAttribute
     * @param mixed $currentVersion
     * @param mixed $originalContentObjectAttribute
     * @access public
     * @return void
     */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
            $origEvent = eZEvent::fetchForObject(
                $originalContentObjectAttribute->attribute( 'id' ),
                $originalContentObjectAttribute->attribute( 'version' )
            );
            $event = null;
            if ( $origEvent )
            {
                // For some reason it could happen, that a version already exists. To avoid a
                // transaction error, this is checked here. Perhaps there is a better solution
                // if the case can be reproduced when a version that should not exist already
                // exists. Perhaps when a draft is deleted from the system?
                // And: Is it a good way to just reuse the already existing version or should
                // it better be removed?
                $event = eZEvent::fetchForObject(
                    $contentObjectAttribute->attribute( 'id' ),
                    $contentObjectAttribute->attribute( 'version' )
                );
                if ( !$event )
                {
                    $event = clone $origEvent;
                    $event->setAttribute( 'id',                         null );
                    $event->setAttribute( 'contentobject_attribute_id', $contentObjectAttribute->attribute( 'id' ) );
                    $event->setAttribute( 'version',                    $contentObjectAttribute->attribute( 'version' ) );
                }
            }
            else
            {
                $data = array(
                    'contentobject_attribute_id' => $contentObjectAttribute->attribute( 'id' ),
                    'version'                    => $contentObjectAttribute->attribute( 'version' ),
                );
                $event = new eZEvent( $data );
            }

            $event->store();
        }
        $this->initializeAttendiesObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute );
    }

    function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $default = $base . "_ezevent_default_" . $classAttribute->attribute( 'id' );

        if ( $http->hasPostVariable( $default ) )
        {
            $defaultValue = $http->postVariable( $default );
            $classAttribute->setAttribute( self::DEFAULT_FIELD,  $defaultValue );
            if ( $defaultValue == self::DEFAULT_ADJUSTMENT )
            {
                $doc = new DOMDocument( '1.0', 'utf-8' );
                $root = $doc->createElement( 'adjustment' );
                $contentList = eZEventType::contentObjectArrayXMLMap();
                foreach ( $contentList as $key => $value )
                {
                    $postValue = $http->postVariable( $base . '_ezevent_' . $value . '_' . $classAttribute->attribute( 'id' ) );
                    unset( $elementType );
                    $elementType = $doc->createElement( $key );
                    $elementType->setAttribute( 'value', $postValue );
                    $root->appendChild( $elementType );
                }
                $doc->appendChild( $root );
                $docText = $doc->saveXML();
                $classAttribute->setAttribute( self::ADJUSTMENT_FIELD , $docText );
            }
        }
        return true;
    }

    static function contentObjectAttendiesArrayXMLMap()
    {
        return array( 'identifier' => 'identifier',
                      'in-trash' => 'in_trash',
                      'contentobject-id' => 'contentobject_id',
                      'contentobject-version' => 'contentobject_version',
                      'node-id' => 'node_id',
                      'parent-node-id' => 'parent_node_id',
                      'contentclass-id' => 'contentclass_id',
                      'contentclass-identifier' => 'contentclass_identifier',
                      'is-modified' => 'is_modified',
                      'contentobject-remote-id' => 'contentobject_remote_id' );
    }

    static function contentObjectArrayXMLMap()
    {
        return array( 'year' => 'year',
                      'month' => 'month',
                      'day' => 'day',
                      'hour' => 'hour',
                      'minute' => 'minute' );
    }

    function title( $contentObjectAttribute, $name = null )
    {
        $event = eZEvent::fetchForObject(
            $contentObjectAttribute->attribute( 'id' ),
            $contentObjectAttribute->attribute( 'version' )
        );
        $locale = eZLocale::instance();
        $retVal = $event->attribute( "start_date" ) == 0 ? '' : $locale->formatDateTime( $event->attribute( "start_date" ) );
        return $retVal;
    }

    /*!
     NOT IMPLEMENTED YET!
    */
    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        $event = eZEvent::fetchForObject(
            $contentObjectAttribute->attribute( 'id' ),
            $contentObjectAttribute->attribute( 'version' )
        );
        return $event->attribute( "start_date" ) != 0;
    }

    function sortKey( $contentObjectAttribute )
    {
        $event = eZEvent::fetchForObject(
            $contentObjectAttribute->attribute( 'id' ),
            $contentObjectAttribute->attribute( 'version' )
        );

        return ( $event ? (int) $event->attribute( "start_date" ) : 0 );
    }

    function sortKeyType()
    {
        return 'int';
    }

    function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $defaultValue = $classAttribute->attribute( self::DEFAULT_FIELD );
        $defaultValueNode = $attributeParametersNode->ownerDocument->createElement( 'default-value' );

        switch ( $defaultValue )
        {
            case self::DEFAULT_CURRENT_DATE:
            {
                $defaultValueNode->setAttribute( 'type', 'current-date' );
            } break;
            case self::DEFAULT_ADJUSTMENT:
            {
                $defaultValueNode->setAttribute( 'type', 'adjustment' );

                $adjustDOMValue = new DOMDocument( '1.0', 'utf-8' );
                $adjustValue = $classAttribute->attribute( self::ADJUSTMENT_FIELD );
                $success = $adjustDOMValue->loadXML( $adjustValue );

                if ( $success )
                {
                    $adjustmentNode = $adjustDOMValue->getElementsByTagName( 'adjustment' )->item( 0 );

                    if ( $adjustmentNode )
                    {
                        $importedAdjustmentNode = $defaultValueNode->ownerDocument->importNode( $adjustmentNode, true );
                        $defaultValueNode->appendChild( $importedAdjustmentNode );
                    }
                }
            } break;
            case self::DEFAULT_EMTPY:
            {
                $defaultValueNode->setAttribute( 'type', 'empty' );
            } break;
            default:
            {
                eZDebug::writeError( 'Unknown type of DateTime default value. Empty type used instead.',
                                    'eZEventType::serializeContentClassAttribute()' );
                $defaultValueNode->setAttribute( 'type', 'empty' );
            } break;
        }

        $attributeParametersNode->appendChild( $defaultValueNode );
    }

    function unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $defaultValue = '';
        $defaultNode = $attributeParametersNode->getElementsByTagName( 'default-value' )->item( 0 );
        if ( $defaultNode )
        {
            $defaultValue = strtolower( $defaultNode->getAttribute( 'type' ) );
        }
        switch ( $defaultValue )
        {
            case 'current-date':
            {
                $classAttribute->setAttribute( self::DEFAULT_FIELD, self::DEFAULT_CURRENT_DATE );
            } break;
            case 'adjustment':
            {
                $adjustmentValue = '';
                $adjustmentNode = $defaultNode->getElementsByTagName( 'adjustment' )->item( 0 );
                if ( $adjustmentNode )
                {
                    $adjustmentDOMValue = new DOMDocument( '1.0', 'utf-8' );
                    $importedAdjustmentNode = $adjustmentDOMValue->importNode( $adjustmentNode, true );
                    $adjustmentDOMValue->appendChild( $importedAdjustmentNode );
                    $adjustmentValue = $adjustmentDOMValue->saveXML();
                }

                $classAttribute->setAttribute( self::DEFAULT_FIELD, self::DEFAULT_ADJUSTMENT );
                $classAttribute->setAttribute( self::ADJUSTMENT_FIELD, $adjustmentValue );
            } break;
            case 'empty':
            {
                $classAttribute->setAttribute( self::DEFAULT_FIELD, self::DEFAULT_EMTPY );
            } break;
            default:
            {
                eZDebug::writeError( 'Type of DateTime default value is not set. Empty type used as default.',
                                    'eZEventType::unserializeContentClassAttribute()' );
                $classAttribute->setAttribute( self::DEFAULT_FIELD, self::DEFAULT_EMTPY );
            } break;
        }
    }

    /*!
     NOT IMPLEMENTED YET!
    */
    function serializeContentObjectAttribute( $package, $objectAttribute )
    {
        $node  = $this->createContentObjectAttributeDOMNode( $objectAttribute );
        $stamp = $objectAttribute->attribute( 'data_int' );

        if ( $stamp )
        {
            //include_once( 'lib/ezlocale/classes/ezdateutils.php' );
            $dateTimeNode = $node->ownerDocument->createElement( 'date_time', eZDateUtils::rfc1123Date( $stamp ) );
            $node->appendChild( $dateTimeNode );
        }

        return $node;
    }

    /*!
     NOT IMPLEMENTED YET!
    */
    function unserializeContentObjectAttribute( $package, $objectAttribute, $attributeNode )
    {
        $dateTimeNode = $attributeNode->getElementsByTagName( 'date_time' )->item( 0 );
        if ( is_object( $dateTimeNode ) )
        {
            //include_once( 'lib/ezlocale/classes/ezdateutils.php' );
            $timestamp = eZDateUtils::textToDate( $dateTimeNode->textContent );
            $objectAttribute->setAttribute( 'data_int', $timestamp );
        }
    }




    function fetchAttendiesObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $content = $contentObjectAttribute->content();

        $postVariableName = $base . "_event_attendees_" . $contentObjectAttribute->attribute( "id" );
        $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();

        $selectedObjectIDArray = $http->hasPostVariable( $postVariableName ) ? $http->postVariable( $postVariableName ) : false;

        $attendees = array();//$content->attribute( 'attendees' );
        // If we got an empty object id list
        if ( $selectedObjectIDArray === false or ( isset( $selectedObjectIDArray[0] ) and $selectedObjectIDArray[0] == 'no_relation' ) )
        {
            $this->storeObjectAttributeContent( $contentObjectAttribute, array() );
            $contentObjectAttribute->store();
            return true;
        }

        foreach ( $selectedObjectIDArray as $objectID )
        {
            // Check if the given object ID has a numeric value, if not go to the next object.
            if ( !is_numeric( $objectID ) )
            {
                eZDebug::writeError( "Rela  ted object ID (objectID): '$objectID', is not a numeric value.",
                                        "eZEventType::fetchObjectAttributeHTTPInput" );

                continue;
            }
            $attendees[] = $this->appendObject( $objectID, $contentObjectAttribute );
            $this->storeObjectAttributeContent( $contentObjectAttribute, $attendees );
            $contentObjectAttribute->store();
        }
        return true;

    }


    function appendObject( $objectID, $contentObjectAttribute )
    {
        $object = eZContentObject::fetch( $objectID );
        $class = $object->attribute( 'content_class' );
        $sectionID = $object->attribute( 'section_id' );
        $relationItem = array( 'identifier' => false,
                               'in_trash' => false,
                               'contentobject_id' => $object->attribute( 'id' ),
                               'contentobject_version' => $object->attribute( 'current_version' ),
                               'contentobject_remote_id' => $object->attribute( 'remote_id' ),
                               'node_id' => $object->attribute( 'main_node_id' ),
                               'parent_node_id' => $object->attribute( 'main_parent_node_id' ),
                               'contentclass_id' => $class->attribute( 'id' ),
                               'contentclass_identifier' => $class->attribute( 'identifier' ),
                               'is_modified' => false );
        $relationItem['object'] = $object;
        return $relationItem;
    }


    /*!
    */
    function storeObjectAttribute( $attribute )
    {
        $xmlText = $attribute->attribute( 'data_text' );

        if ( trim( $xmlText ) == '' )
        {
            $objectAttributeContent = eZEventType::defaultObjectAttributeContent();
            return $objectAttributeContent;
        }

        $doc = eZEventType::parseXML( $xmlText );
        $attendees = eZEventType::createObjectContentStructure( $doc );

        $contentClassAttributeID = $attribute->ContentClassAttributeID;
        $contentObjectID = $attribute->ContentObjectID;
        $contentObjectVersion = $attribute->Version;

        $obj = $attribute->object();
        //get eZContentObjectVersion
        $currVerobj = $obj->version( $contentObjectVersion );

        // create translation List
        // $translationList will contain for example eng-GB, ita-IT etc.
        $translationList = $currVerobj->translations( false );

        // get current language_code
        $langCode = $attribute->attribute( 'language_code' );
        // get count of LanguageCode in translationList
        $countTsl = count( $translationList );
        // order by asc
        sort( $translationList );

        if ( ( $countTsl == 1 ) or ( $countTsl > 1 and $translationList[0] == $langCode ) )
        {
             eZContentObject::fetch( $contentObjectID )->removeContentObjectRelation( false, $contentObjectVersion, $contentClassAttributeID, eZContentObject::RELATION_ATTRIBUTE );
        }
        foreach( $attendees as $relationItem )
        {
            // Installing content object, postUnserialize is not called yet,
            // so object's ID is unknown.
            if ( !$relationItem['contentobject_id'] || !isset( $relationItem['contentobject_id'] ) )
                continue;

            $subObjectID = $relationItem['contentobject_id'];
            $subObjectVersion = $relationItem['contentobject_version'];

            eZContentObject::fetch( $contentObjectID )->addContentObjectRelation( $subObjectID, $contentObjectVersion, $contentClassAttributeID, eZContentObject::RELATION_ATTRIBUTE );

        }
        return eZEventType::storeObjectAttributeContent( $attribute, $attendees );
    }



    function storeObjectAttributeContent( $objectAttribute, $content )
    {
        if ( is_array( $content ) )
        {
            $doc = eZEventType::createObjectDOMDocument( $content );
            eZEventType::storeObjectDOMDocument( $doc, $objectAttribute );
            return true;
        }
        return false;
    }


    function storeObjectDOMDocument( $doc, $objectAttribute )
    {
        $docText = eZEventType::domString( $doc );
        $objectAttribute->setAttribute( 'data_text', $docText );
        $objectAttribute->Content = null;
    }

    /*!
     \static
     \return the XML structure in \a $domDocument as text.
             It will take of care of the necessary charset conversions
             for content storage.
    */
    static function domString( $domDocument )
    {
        $ini = eZINI::instance();
        $xmlCharset = $ini->variable( 'RegionalSettings', 'ContentXMLCharset' );
        if ( $xmlCharset == 'enabled' )
        {
            //include_once( 'lib/ezi18n/classes/eztextcodec.php' );
            $charset = eZTextCodec::internalCharset();
        }
        else if ( $xmlCharset == 'disabled' )
            $charset = true;
        else
            $charset = $xmlCharset;
        if ( $charset !== true )
        {
            //include_once( 'lib/ezi18n/classes/ezcharsetinfo.php' );
            $charset = eZCharsetInfo::realCharsetCode( $charset );
        }
        $domString = $domDocument->saveXML();
        return $domString;
    }

    function createObjectDOMDocument( $attendees )
    {
        $doc = new DOMDocument( '1.0', 'utf-8' );
        $root = $doc->createElement( 'related-objects' );
        $relationList = $doc->createElement( 'relation-list' );
        $attributeDefinitions = eZEventType::contentObjectAttendiesArrayXMLMap();

        foreach ( $attendees as $relationItem )
        {
            unset( $relationElement );
            $relationElement = $doc->createElement( 'relation-item' );

            foreach ( $attributeDefinitions as $attributeXMLName => $attributeKey )
            {
                if ( isset( $relationItem[$attributeKey] ) && $relationItem[$attributeKey] !== false )
                {
                    $value = $relationItem[$attributeKey];
                    $relationElement->setAttribute( $attributeXMLName, $value );
                }
            }

            $relationList->appendChild( $relationElement );
        }
        $root->appendChild( $relationList );
        $doc->appendChild( $root );
        return $doc;
    }

    function initializeAttendiesObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
            $dataText = $originalContentObjectAttribute->attribute( 'data_text' );
            $contentObjectAttribute->setAttribute( 'data_text', $dataText );
        }
    }

    static function createObjectContentStructure( $doc )
    {
        $attendees = eZEventType::defaultObjectAttributeContent();
        $root = $doc->documentElement;
        $relationList = $root->getElementsByTagName( 'relation-list' )->item( 0 );
        if ( $relationList )
        {
            $contentObjectArrayXMLMap = eZEventType::contentObjectAttendiesArrayXMLMap();
            $relationItems = $relationList->getElementsByTagName( 'relation-item' );
            foreach ( $relationItems as $relationItem )
            {
                $hash = array();

                foreach ( $contentObjectArrayXMLMap as $attributeXMLName => $attributeKey )
                {
                    $attributeValue = $relationItem->hasAttribute( $attributeXMLName ) ? $relationItem->getAttribute( $attributeXMLName ) : false;
                    $hash[$attributeKey] = $attributeValue;
                }
                $attendees[] = $hash;
            }
        }
        return $attendees;

    }
    static function defaultObjectAttributeContent()
    {
        return array( );
    }

    /*!
     Clean up temporary date information
    */
    function deleteStoredObjectAttribute( $objectAttribute, $version = null )
    {
    }

    /*!
     Make temporary date set the published
    */
    function onPublish( $contentObjectAttribute, $contentObject, $publishedNodes )
    {
    }

}

eZDataType::register( eZEventType::DATA_TYPE_STRING, "eZEventType" );

?>
