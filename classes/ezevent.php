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

class eZEvent extends eZPersistentObject
{
    const EVENTTYPE_NORMAL          = 11;
    const EVENTTYPE_FULL_DAY        = 12;
    const EVENTTYPE_SIMPLE          = 14;
    const EVENTTYPE_WEEKLY_REPEAT   = 15;
    const EVENTTYPE_MONTHLY_REPEAT  = 16;
    const EVENTTYPE_YEARLY_REPEAT   = 17;

    /**
     * Roughly the amount of seconds the repetition intervals cover.
     *
     * Should not be used for modifications, as these amounts are not exact,
     * but my be used for rough checks.
     *
     * @var array
     */
    protected static $timespans = array(
        self::EVENTTYPE_WEEKLY_REPEAT  => 604800,
        self::EVENTTYPE_MONTHLY_REPEAT => 2592000,
        self::EVENTTYPE_YEARLY_REPEAT  => 31557600,
    );


    private $ContentObject = false;
    /**
     * Return persitent object definition array
     *
     * Return the definition array for persistent object.
     *
     * @return array
     */
    public static function definition()
    {
        return array(
            // Fields in database
            'fields' => array(
                'id' => array(
                    'name'     => 'ID',
                    'datatype' => 'integer',
                    'default'  => null,
                    'required' => false,
                ),
                'parent_event_id'      => array(
                    'name'      => 'ParentEventID',
                    'datatype'  => 'string',
                    'default'   => 0,
                    'required'  => false,
                ),
                'contentobject_attribute_id' => array(
                    'name'     => 'ContentObjectAttributeID',
                    'datatype' => 'integer',
                    'default'  => 0,
                    'required' => true,
                ),
                'version' => array(
                    'name'     => 'Version',
                    'datatype' => 'integer',
                    'default'  => 0,
                    'required' => true,
                ),
                'start_date'    => array(
                    'name'     => 'StartDate',
                    'datatype' => 'string',
                    'default'  => 0,
                    'required' => true,
                ),
                'end_date'      => array(
                    'name'     => 'EndDate',
                    'datatype' => 'string',
                    'default'  => 0,
                    'required' => false,
                ),
                'event_type'    => array(
                    'name'     => 'Type',
                    'datatype' => 'integer',
                    'default'  => eZEvent::EVENTTYPE_NORMAL,
                    'required' => true,
                ),
                'is_parent' => array(
                    'name'     => 'isParent',
                    'datatype' => 'integer',
                    'default'  => 0,
                    'required' => false,
                ),
                'is_temp'   => array(
                    'name'     => 'isTemp',
                    'datatype' => 'integer',
                    'default'  => 0,
                    'required' => false,
                ),
            ),
            'function_attributes'       => array(
                'duration'              => 'duration',
                'start'                 => 'startDate',
                'end'                   => 'endDate',
                'current_start_date'    => 'currentStartDate',
                'current_end_date'      => 'currentEndDate',
                'current_start'         => 'currentStart',
                'current_end'           => 'currentEnd',
                'parent_event'          => 'parentEvent',
                'has_parent_event'      => 'hasParentEvent',
                'attendees'             => 'eventAttendees',
                'content_object'        => 'contentObject',
            ),
            'keys'                => array(
                'id',
                'contentobject_attribute_id',
                'version',
            ),
            'increment_key'       => 'id',
            'class_name'          => 'eZEvent',
            'sort'                => array(
                'startdate' => 'asc',
            ),
            'name'                => 'ezevent',
        );
    }

    /**
     * Create new eZEvent object
     *
     * @param array $row
     * @return eZEvent
     */
    public static function create( array $row )
    {
        return new eZEvent( $row );
    }

    /**
     * Fetch eZEvent object from database
     *
     * Fetch event with the given ID from database
     *
     * @param int $eventID
     * @param bool $asObject
     * @return eZEvent
     */
    public static function fetch( $eventID, $asObject = true )
    {
        $conditions = array(
            'id' => $eventID,
        );

        return eZPersistentObject::fetchObject(
            eZEvent::definition(),
            null,
            $conditions,
            $asObject
        );
    }

    /**
     * Fetch event for content object
     *
     * Fetch the event for a given content object. Pass the content object
     * attribute ID, which the datatype is used for and the version of the
     * content object to the function.
     *
     * @param int $contentObjectAttributeID
     * @param int $version
     * @param bool $asObject
     * @return eZEvent
     */
    public static function fetchForObject( $contentObjectAttributeID, $version, $asObject = true )
    {
        $conditions = array(
            'contentobject_attribute_id' => $contentObjectAttributeID,
            'version'                    => $version,
        );

        return eZPersistentObject::fetchObject(
            eZEvent::definition(),
            null,
            $conditions,
            $asObject
        );
    }

    public static function fetchList( $offset = false, $length = false, $sorts = false, $conds = null )
    {
        $filter = null;
        $limit = array( 'offset' => $offset, 'length' => $length );

        $resultlist = eZPersistentObject::fetchObjectList( eZEvent::definition(),
                                                           $filter,
                                                           $conds,
                                                           $sorts,
                                                           $limit,
                                                           true,
                                                           false,
                                                           null );
        return $resultlist;
    }

    public static function fetchListCount($conds = null)
    {
        $custom = array(
                    array( 'operation' => 'count( event_id )',
                            'name' => 'count' ) );
        $rows = eZPersistentObject::fetchObjectList( eZEvent::definition(),
                                                     array(),
                                                     $conds,
                                                     null,
                                                     null,
                                                     false,
                                                     false,
                                                     $custom );
        return $rows[0]['count'];
    }


    function removeItem( $eventID  )
    {
        $item = eZEvent::fetch( $eventID );
        if ( is_object( $item ) )
        {
            $item->remove();
        }
    }

    /**
     * Cleanup up event table
     *
     * @return void
     */
    function cleanup( )
    {
        $db = eZDB::instance();
        $db->query( "TRUNCATE TABLE ez_event" );
        $db->commit();
    }


    function duration( )
    {
        if ( $this->attribute( 'start_date' ) == 0 || $this->attribute( 'end_date' ) == 0 )
            return false;
        return ( $this->attribute( 'end_date' ) - $this->attribute( 'start_date' ) );
    }

    function startDate( )
    {
        $dateTime = new eZDateTime();
        $dateTime->setTimeStamp( $this->attribute( 'start_date' ) );
        return $dateTime;
    }

    function endDate( )
    {
        $dateTime = new eZDateTime();
        $dateTime->setTimeStamp( $this->attribute( 'end_date' ) );
        return $dateTime;
    }

    function currentStartDate()
    {
        return $this->attribute( 'start_date' );
    }

    function currentEndDate()
    {
        if ( $this->attribute( 'event_type' ) == self::EVENTTYPE_WEEKLY_REPEAT ||
             $this->attribute( 'event_type' ) == self::EVENTTYPE_MONTHLY_REPEAT ||
             $this->attribute( 'event_type' ) == self::EVENTTYPE_YEARLY_REPEAT )
        {
            $startDate = $this->currentStart();

            $endDateTime = new eZDateTime();
            $endDateTime->setTimeStamp( $this->attribute( 'end_date' ) );
            $endDateTime->setDay( $startDate->day() );
            $endDateTime->setMonth( $startDate->month() );
            $endDateTime->setYear( $startDate->year() );
            return $endDateTime->timeStamp( );

        }
        else
        {
            return $this->attribute( 'end_date' );
        }
    }

    function currentStart()
    {
        $dateTime = new eZDateTime();
        $dateTime->setTimeStamp( $this->currentStartDate() );
        return $dateTime;
    }

    function currentEnd()
    {
        $dateTime = new eZDateTime();
        $dateTime->setTimeStamp( $this->currentEndDate() );
        return $dateTime;
    }

    /**
     * Fetch the content object associated with the event
     *
     * @access public
     * @return eZContentObject
     */
    function contentObject()
    {
        if ( !$this->ContentObject )
        {
            // Fetch associated content object
            $db = eZDB::instance();
            $sqlResult = $db->arrayQuery( "
                SELECT
                    ezcontentobject_attribute.contentobject_id
                FROM
                    ezcontentobject_attribute
                WHERE
                        ezcontentobject_attribute.id = " . $this->attribute( 'contentobject_attribute_id' ) . "
                    AND ezcontentobject_attribute.version = " . $this->attribute( 'version' ) . "
            " );
            $row = reset( $sqlResult );
            $this->ContentObject = eZContentObject::fetch( $row['contentobject_id'] );
        }
        return $this->ContentObject;
    }

    /**
     * Fetch the parent event of this event if exist
     *
     * @access public
     * @return eZEvent
     */
    function parentEvent()
    {
        if ( $this->attribute( 'parent_event_id' ) )
        {
            return eZEvent::fetch( $this->attribute( 'parent_event_id' ) );
        }
        return false;
    }

    /**
     * Check if event is a parent event
     *
     * @access public
     * @return eZEvent
     */
    function hasParentEvent()
    {
        if ( $this->attribute( 'parent_event_id' ) )
        {
            return true;
        }
        return false;
    }



    /**
     * Clone object
     *
     * Method called, when an eZContentObject and its attributes are
     * dublicated. Resets properties, which need to be reassigned properly.
     *
     * @return void
     */
    function __clone()
    {
        $this->setAttribute( 'id', null );
        $this->setAttribute( 'contentobject_attribute_id', null );
        $this->setAttribute( 'version', null );
        $this->setAttribute( 'is_parent', null );
        $this->setAttribute( 'is_temp', null );
    }

    function __toString( )
    {
        $locale = eZLocale::instance();
        $retVal = $this->attribute( "start_date" ) == 0 ? '' : $locale->formatDateTime( $this->attribute( "start_date" ) );
        return $retVal;
    }

    /**
     * Calculate the next event date
     *
     * Calculated the next date of the event based on the repetition type.
     *
     * @param int $date
     * @param int $type
     * @return int
     */
    protected static function calculateNextDate( $date, $type, $counter = 1 )
    {
        switch ( $type )
        {
            case self::EVENTTYPE_WEEKLY_REPEAT:
                return strtotime( "+$counter week", $date );

            case self::EVENTTYPE_MONTHLY_REPEAT:
                return strtotime( "+$counter month", $date );

            case self::EVENTTYPE_YEARLY_REPEAT:
                return strtotime( "+$counter year", $date );

            default:
                return $date;
        }
    }

    /**
     * Apply repetition to events
     *
     * Events with a repetition setting (every week, every month, .. ) are
     * dublicated by this method in the specified time span. This method will
     * increase the number of return values by the original SQL array this way.
     *
     * @param array $sqlResults
     * @return array
     */
    protected static function applyRepetitionInMonth( array $sqlResults, $year, $month, $day = 0 )
    {
        $startDate = mktime(
            1, 0, 0,
            $month, $day+1, $year
        );
        $endDate   = mktime(
            1, 0, 0,
            $month,
            // If a day has been specified use 1:00 at the next day, otherwise
            // fall back to the last day in month.
            ( ( $day === 0 ) ? date( 't', mktime( 1, 0, 0, $month, 1, $year ) ) : $day ) + 1,
            $year
        );
        $events = array();

        // Dublicate events and keep a search key, to search the events
        // depending on their start data again after dublication
        $searchKeys = array();
        foreach ( $sqlResults as $row )
        {
            $counter = 1;
            $origDate = $row['start_date'];
            // Increase date, until we are in the requested time span
            while ( in_array( $row['event_type'], array(
                        self::EVENTTYPE_SIMPLE,
                        self::EVENTTYPE_WEEKLY_REPEAT,
                        self::EVENTTYPE_MONTHLY_REPEAT,
                        self::EVENTTYPE_YEARLY_REPEAT,
                    ) ) &&
                    ( $row['start_date'] < $startDate ) )
            {
                $tmpDate = self::calculateNextDate( $origDate, $row['event_type'], $counter++ );
                if ( ( ( $row['event_type'] == self::EVENTTYPE_MONTHLY_REPEAT ) &&
                       ( date( "d", $row['start_date'] ) == date( "d", $tmpDate ) ) ) ||
                     ( $row['event_type'] != self::EVENTTYPE_MONTHLY_REPEAT ) )
                {
                    $row['start_date'] = $tmpDate;
                }
                else
                {
                    break;
                }
            }

            // Create events, as long as we stay in the requested time span
            $before = 0;
            do {
                if ( ( $row['event_type'] != self::EVENTTYPE_MONTHLY_REPEAT ) ||
                     ( ( date( "n", $row['start_date'] ) == $month ) &&
                       ( date( "Y", $row['start_date'] ) == $year ) &&
                       ( $before != 0 ) &&
                       ( $row['event_type'] == self::EVENTTYPE_MONTHLY_REPEAT ) &&
                       ( date( "d", $row['start_date'] ) == date( "d", $before ) ) ) ||
                     ( ( date( "n", $row['start_date'] ) == $month ) &&
                       ( date( "Y", $row['start_date'] ) == $year ) &&
                       ( $before == 0 ) &&
                       ( $row['event_type'] == self::EVENTTYPE_MONTHLY_REPEAT ) ) )
                {
                    $events[]     = $row;
                    $searchKeys[] = $row['start_date'];
                }

                $row['start_date'] = self::calculateNextDate( $before = $origDate, $row['event_type'], $counter++ );
            } while ( ( $row['start_date'] < $endDate ) &&
                      ( $row['start_date'] < $row['end_date'] ) &&
                      ( $row['start_date'] != $before ) );
        }

        // Search events again depending on their start date
        array_multisort(
            $events,
            $searchKeys, SORT_NUMERIC, SORT_ASC
        );
        return $events;
    }

    /**
     * Copy content object
     *
     * This function is meant to copy a content object. It is a copy of the
     * function copyObject in kernel/content/copy.php, without the redirects.
     *
     * As parameters it takes the actual content object, which should be
     * dublicated and a flag if all versions should also be copied. The new
     * parent node ID defines where the new content object will be located. By
     * default the main parent node id of the given object will be reused.
     *
     * @param eZContentObject $object
     * @param bool $allVersions
     * @param int $newParentNodeID
     * @return eZContentObject
     */
    protected static function copyContentObject( eZContentObject $object, $allVersions = true, $newParentNodeID = null )
    {
        if ( $newParentNodeID === null )
        {
            $newParentNodeID = $object->mainNode()->attribute( 'parent_node_id' );
        }

        // check if we can create node under the specified parent node
        if( ( $newParentNode = eZContentObjectTreeNode::fetch( $newParentNodeID ) ) === null )
        {
            return eZDebug::writeError( "Could not fetch requested parent node $newParentNodeID." );
        }

        $classID = $object->attribute('contentclass_id');

        if ( !$newParentNode->checkAccess( 'create', $classID ) )
        {
            $objectID = $object->attribute( 'id' );
            return eZDebug::writeError(
                "Cannot copy object $objectID to node $newParentNodeID, " .
                "the current user does not have create permission for class ID $classID",
                'content/copy'
            );
        }

        $db = eZDB::instance();
        $db->begin();
        $newObject = $object->copy( $allVersions );
        // We should reset section that will be updated in updateSectionID().
        // If sectionID is 0 than the object has been newly created
        $newObject->setAttribute( 'section_id', 0 );
        $newObject->store();

        $curVersion        = $newObject->attribute( 'current_version' );
        $curVersionObject  = $newObject->attribute( 'current' );
        $newObjAssignments = $curVersionObject->attribute( 'node_assignments' );
        unset( $curVersionObject );

        // Remove old node assignments
        foreach( $newObjAssignments as $assignment )
        {
            $assignment->purge();
        }

        // And create a new one
        $nodeAssignment = eZNodeAssignment::create( array(
            'contentobject_id'      => $newObject->attribute( 'id' ),
            'contentobject_version' => $curVersion,
            'parent_node'           => $newParentNodeID,
            'is_main'               => 1,
        ) );
        $nodeAssignment->store();

        // Publish the newly created object
        //
        // We do that manually to not trigger the workflow recursively. There
        // might be caching issues with this.
        $version = $newObject->version( 1 );

        $version->setAttribute( 'status', 3 );
        $version->store();

        $newObject->setAttribute( 'status', 1 );

        $class = $newObject->contentClass();
        $newObjectName = $class->contentObjectName( $object );

        $newObject->setName( $newObjectName, 1 );
        $newObject->setAttribute( 'current_version', 1 );
        $time = time();
        $newObject->setAttribute( 'modified', $time );
        $newObject->setAttribute( 'published', $time );
        $newObject->setAttribute( 'section_id', 1 );
        $newObject->store();
        /*
        eZOperationHandler::execute( 'content', 'publish', array(
            'object_id' => $newObject->attribute( 'id' ),
            'version'   => $curVersion,
        ) ); // */

        // Update "is_invisible" attribute for the newly created node.
//        $newNode = $newObject->attribute( 'main_node' );

        $node = eZContentObjectTreeNode::fetch( $newParentNodeID );

        $newNode = $node->addChild( $newObject->attribute( 'id' ), true );
        $newNode->setAttribute( 'contentobject_version', $curVersion );
        $newNode->setAttribute( 'contentobject_is_published', 1 );
        $newNode->setName( $newObject->attribute( 'name' ) );
        $newNode->setAttribute( 'main_node_id', $newNode->attribute( 'node_id' ) );
        $newNode->setAttribute( 'sort_field', $nodeAssignment->attribute( 'sort_field' ) );
        $newNode->setAttribute( 'sort_order', $nodeAssignment->attribute( 'sort_order' ) );

        $newNode->updateSubTreePath();
        $newNode->store();


        eZContentObjectTreeNode::updateNodeVisibility( $newNode, $newParentNode );

        $db->commit();

        // Clear datamap cache in the new object, to ensure we get the updated
        // one, when used.
        //
        // This does not fully work yet, see http://issues.ez.no/13552
        $newObject->resetDataMap();

        // @TODO: We need to refetch the object because of buggy datamap
        // caching in eZContentObject. See bug http://issues.ez.no/13552. This
        // line should NOT be required and causes additional unecessary queries
        // to the database.
        $newObject = eZContentObject::fetch( $newObject->attribute( 'id' ) );

        return $newObject;
    }

    /**
     * Split repreated event up
     *
     * If a single selected event out of a repeated event should be edited
     * selectively, the whole event needs to be split up. This methods creates
     * the new event and content objects for this in the database and returns
     * the content object ID for the new event at the given event date, which
     * can then be edited selectively.
     *
     * @param int $contentObjectId
     * @param int $eventDate
     * @return int
     */
    public static function splitEventAt( $contentObjectId, $eventDate )
    {
        $object  = eZContentObject::fetch( $contentObjectId );
        $dataMap = $object->dataMap();
        $event   = $dataMap['event_date']->content();

        // Check if this is an repeated event at all, otherwise just do
        // nothing.
        if ( !in_array( $eventType = $event->attribute( 'event_type' ), array(
                self::EVENTTYPE_WEEKLY_REPEAT,
                self::EVENTTYPE_MONTHLY_REPEAT,
                self::EVENTTYPE_YEARLY_REPEAT,
            ) ) )
        {
            return $contentObjectId;
        }

        // Apply repetition to event, to get a list of all actual event dates,
        // to select the user selected event.
        $date = $startDate = $event->StartDate;
        $startSectionEndDate = $date - 86400;
        while ( $date < ( $eventDate - 86400 ) )
        {
            $startSectionEndDate = $date + 86400;
            $date                = self::calculateNextDate( $date, $eventType );
        }

        // Calculate the time span offsets for the time span after the current
        // event
        $endDate             = $event->EndDate;
        $endSectionStartDate = self::calculateNextDate( $date, $eventType );

        // If the event which is requested to be split up already has an
        // assigned parent id, we resuse this for all created children, to keep
        // the tree structure as flat as possible, to make relation resolving
        // easier later.
        if ( $event->attribute( 'parent_event_id' ) )
        {
            $parentId = $event->attribute( 'parent_event_id' );
        }
        else
        {
            // Otherwise we use the original event as the new parent event
            // node.
            $parentId = $event->attribute( 'id' );
            $event->setAttribute( 'is_parent', true );
            $event->store();
        }

        /* Debug info only
        $dates = array(
            $startDate,
            $startSectionEndDate,
            $eventDate,
            $date,
            $endSectionStartDate,
            $endDate,
        );
        array_walk( $dates, create_function( '$ts', 'var_dump( date( DATE_RFC2822, $ts ) );' ) );
        // */

        // Check if we need a start period, and create a new content object for
        // it.
        $split = false;
        if ( $startDate < $startSectionEndDate )
        {
            // Indicator, that the event has actually been split up
            $split = true;

            // Update event to only contain the event start timespan
            $startEvent = new eZEvent( $data = array(
                'id'                         => null,
                'contentobject_attribute_id' => (int) $event->attribute( 'contentobject_attribute_id' ),
                'version'                    => (int) $event->attribute( 'version' ),
                'start_date'                 => (int) $event->attribute( 'start_date' ),
                'end_date'                   => (int) $startSectionEndDate,
                'parent_event_id'            => $parentId,
                'event_type'                 => (int) $event->attribute( 'event_type' ),
                'is_parent'                  => false,
            ) );
            //var_dump( "Start:", $data );
            $startEvent->store();
        }

        // Check if we need a end period and create a new content object for
        // it.
        if ( $endDate > $endSectionStartDate )
        {
            // Indicator, that the event has actually been split up
            $split = true;

            // Update event to only contain the event start timespan
            $endEvent = new eZEvent( $data = array(
                'id'                         => null,
                'contentobject_attribute_id' => (int) $event->attribute( 'contentobject_attribute_id' ),
                'version'                    => (int) $event->attribute( 'version' ),
                'start_date'                 => (int) $endSectionStartDate,
                'end_date'                   => (int) $event->attribute( 'end_date' ),
                'parent_event_id'            => $parentId,
                'event_type'                 => (int) $event->attribute( 'event_type' ),
                'is_parent'                  => false,
            ) );
            //var_dump( "End:", $data );
            $endEvent->store();
        }

        // Create a new event node for the split away event object. This is now
        // a single non repeated event node with a  new content object assigned.
        $newEventObject = self::copyContentObject( $object );

        // Check that the object copy operation has been successfull.
        if ( !$newEventObject instanceof eZContentObject )
        {
            return eZDebug::writeError( "Event splitting failed at start event." );
        }

        // Update event to only contain the event start timespan
        $dataMap        = $newEventObject->dataMap();
        $eventAttribute = $dataMap['event_date']->content();
        $eventAttribute->setAttribute( 'start_date', $date );
        $eventAttribute->setAttribute( 'end_date', null );
        $eventAttribute->setAttribute( 'parent_event_id', $parentId );
        $eventAttribute->setAttribute( 'event_type', eZEvent::EVENTTYPE_NORMAL );
        $eventAttribute->store();

        return $newEventObject->attribute( 'id' );
    }

    /**
     * Receive a list of related events
     *
     * Return an array of related events, which are yet open for modifications.
     * This means, that events in the past may be split away, and are not
     * returned.
     *
     * @return array( eZEvent )
     */
    protected function getRelatedEvents()
    {
        if ( ( $parent = $this->attribute( 'parent_event_id' ) ) == 0 )
        {
            return array();
        }

        // Get all events, which do have the same parent ID and relate to
        // published content objects.
        $db = eZDB::instance();
        $sqlResult = $db->arrayQuery( "
            SELECT
                ezevent.contentobject_attribute_id,
                ezevent.version
            FROM
                ezevent
            RIGHT JOIN
                ezcontentobject_attribute
            ON
                    ezcontentobject_attribute.id = ezevent.contentobject_attribute_id
                AND ezcontentobject_attribute.version = ezevent.version
            RIGHT JOIN
                ezcontentobject
            ON
                    ezcontentobject.status = 1
                AND ezcontentobject.id = ezcontentobject_attribute.contentobject_id
                AND ezcontentobject.current_version = ezevent.version
            WHERE
                    ezevent.parent_event_id = $parent
                AND ezevent.is_parent = 0
                AND ezevent.is_temp = 0
                AND (
                            ( ezevent.end_date > " . ( $currentDate = time() ) . " )
                    OR
                            ( ezevent.end_date = 0 )
                        AND ( ezevent.start_date > " . $currentDate . " )
                    )
            ORDER BY
                ezevent.end_date ASC
        " );

        // Convert result to event objects
        $events = array();
        foreach ( $sqlResult as $row )
        {
            // @TODO: These additional queries probably can be spared.
            $events[] = eZEvent::fetchForObject( $row['contentobject_attribute_id'], $row['version'] );
        }

        // Split events which exceed todays date
        foreach ( $events as $nr => $event )
        {
            if ( ( $event->attribute( 'start_date' ) < $currentDate ) &&
                 ( $event->attribute( 'end_date' )   > $currentDate ) &&
                 ( in_array( $event->attribute( 'event_type' ), array(
                    self::EVENTTYPE_WEEKLY_REPEAT,
                    self::EVENTTYPE_MONTHLY_REPEAT,
                    self::EVENTTYPE_YEARLY_REPEAT,
                 ) ) ) )
            {
                // Reset the event to its prior version to omit updates done
                // automatically by the content object form, which should still
                // not be applied to events in the past.
                //
                // The changes will be reapplied later on...
                $eventId = $event->attribute( 'id' );
                $event = eZEvent::fetchForObject(
                    $event->attribute( 'contentobject_attribute_id' ),
                    ( $eventVersion = $event->attribute( 'version' ) ) - 1
                );
                $event->setAttribute( 'id', $eventId );
                $event->setAttribute( 'version', $eventVersion );
                $event->store();

                // Copy content object of the event
                $newObject = self::copyContentObject( $event->contentObject() );

                // Check that the object copy operation has been successfull.
                if ( !$newObject instanceof eZContentObject )
                {
                    return eZDebug::writeError( "Event splitting failed at start event." );
                }

                // Calculate the next start date after the current date
                $newStartDate = $event->attribute( 'start_date' );
                while ( $newStartDate < $currentDate )
                {
                    $newStartDate = self::calculateNextDate( $newStartDate, $event->attribute( 'event_type' ) );
                }

                // Update event to only contain the event start timespan
                $dataMap        = $newObject->dataMap();
                $eventAttribute = $dataMap['event_date']->content();
                $eventAttribute->setAttribute( 'id', null );
                $eventAttribute->setAttribute( 'start_date',        $newStartDate );
                $eventAttribute->setAttribute( 'end_date',          $event->attribute( 'end_date' ) );
                $eventAttribute->setAttribute( 'parent_event_id',   $event->attribute( 'parent_event_id' ) );
                $eventAttribute->setAttribute( 'event_type',        $event->attribute( 'event_type' ) );
                $eventAttribute->store();

                // Update old past events end date
                $event->setAttribute( 'end_date', $currentDate - 1 );
                $event->store();

                // Only list newly created events in related events
                $events[$nr] = $eventAttribute;

                // Refetch related events
                return $this->getRelatedEvents();
            }
        }

        return $events;
    }

    /**
     * Update related events.
     *
     * If a repeated event has been edited, which is related to other events by
     * a parent child relations, the other events are updated, too. The types
     * of updates are:
     *
     * - End date modification
     *
     *   If the end date of the last event is changed, the parent event is also
     *   updated, to reflect this change.
     *
     *   If the modified event is not the last event, all subsequent events
     *   will be removed, as we consider the overall timespan to be reduced.
     *
     * - Minor start date modification
     *
     *   If the start date is modified by a time span lower the the reptition
     *   interval, all related repeated events are updated to reflect the
     *   offset (weekly repeated event is moved from tuesday to wednesday, for
     *   example).
     *
     * - Major start date modification
     *
     *   The parent event start date is changed and all events before the
     *   current event are removed, as we consider this a reduction of the
     *   overall time span. A modification is "major", if the modification is
     *   bigger the the repetition interval timespan.
     *
     * - General hourly modification
     *
     *   If the modification is lesser the one day on start or end date, this
     *   modification is applied to all repeated events, since we can consider
     *   this as resheduled meeting time.
     *
     * All modifications are not applied to events, which already happened.
     *
     * @return void
     */
    public function updateRelatedEvents()
    {
        // Get prior version of event for comparision
        $oldEvent = self::fetchForObject(
            $this->attribute( 'contentobject_attribute_id' ),
            $this->attribute( 'version' ) - 1
        );

        // If there is no older version, just bail out, as there seems nothing
        // to do
        if ( !$oldEvent instanceof eZEvent )
        {
            return false;
        }

        // Check for start date modification types
        switch ( true )
        {
            case $oldEvent->attribute( 'start_date' ) == $this->attribute( 'start_date' ):
                // Not modified
                // @Tested: OK
                break;

            case abs( $diff = ( $oldEvent->attribute( 'start_date' ) - $this->attribute( 'start_date' ) ) ) < 86400:
                // General hourly modification
                // @Tested: OK
            case abs( $diff = ( $oldEvent->attribute( 'start_date' ) - $this->attribute( 'start_date' ) ) ) < self::$timespans[$this->attribute( 'event_type' )]:
                // Minor start date modification
                // @Tested: OK
                $related = $this->getRelatedEvents();
                foreach ( $related as $event )
                {
                    $event->setAttribute(
                        'start_date',
                        $event->attribute( 'start_date' ) - $diff
                    );
                    $event->store();
                }
                break;

            default:
                // Major start date modification
                // @Tested: OK
                $related = $this->getRelatedEvents();
                foreach ( $related as $event )
                {
                    if ( $event->attribute( 'end_date' ) < $this->attribute( 'start_date' ) )
                    {
                        $event->remove();
                    }
                    elseif ( $event->attribute( 'start_date' ) < $this->attribute( 'start_date' ) )
                    {
                        $event->setAttribute(
                            'start_date',
                            $this->attribute( 'start_date' )
                        );
                        $event->store();
                    }
                    else
                    {
                        // Future events do not need any modification
                    }
                }
        }

        // Check for end date modification types
        switch ( true )
        {
            case $oldEvent->attribute( 'end_date' ) == $this->attribute( 'end_date' ):
                // Not modified
                // @Tested: OK
                break;

            case $oldEvent->attribute( 'end_date' ) < $this->attribute( 'end_date' ):
                // End date increased
                // @Tested: OK
                $newEndDate    = $this->attribute( 'end_date' );
                $minStartDate  = $newEndDate;
                $lastEvent     = null;
                $lastEventDate = 0;

                $related = $this->getRelatedEvents();
                foreach ( $related as $event )
                {
                    if ( ( $oldEvent->attribute( 'end_date' ) <= $event->attribute( 'start_date' ) ) &&
                         ( $event->attribute( 'start_date' )  <= $newEndDate ) )
                    {
                        $minStartDate = min( $minStartDate, $event->attribute( 'start_date' ) );
                        if ( $event->attribute( 'end_date' ) > $lastEventDate )
                        {
                            $lastEventDate = $event->attribute( 'end_date' );
                            $lastEvent     = $event;
                        }
                    }
                }

                if ( $minStartDate < $newEndDate )
                {
                    // We found an event after the modified one. We change the
                    // end date of this event instead.
                    $this->setAttribute( 'end_date', $minStartDate - 86400 );
                    $this->store();

                    $lastEvent->setAttribute( 'end_date', $newEndDate );
                    $lastEvent->store();
                }

                // Also remove all events after the new event end date.
                foreach ( $related as $event )
                {
                    if ( $event->attribute( 'start_date' ) > $newEndDate )
                    {
                        $event->remove();
                    }
                }
                break;

            default:
                // End date reduction
                // @Tested: OK
                $related = $this->getRelatedEvents();
                foreach ( $related as $event )
                {
                    if ( $event->attribute( 'start_date' ) > $this->attribute( 'end_date' ) )
                    {
                        $event->remove();
                    }
                }
        }

        return true;
    }

    /**
     * Fetch list of events
     *
     * Fetch a list of events during the specified time span. The day of the
     * month may be left empty to get all events within a specified month. If
     * the day is specified, only the events for one moth will be returned.
     *
     * You may optionally specify a list of root nodes in the content tree, as
     * a limit from where eventy are shown. For example a list of
     * calendars, which are aggregated into one calendar.
     *
     * Additionally the ID of an user may be specified, who then needs to have
     * a relation (link) to the event content object, which means, that (s)he
     * attends the events.
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $offset
     * @param int $limit
     * @param array $type
     * @param string $mode
     * @param bool $group
     * @param array $parentNode
     * @param int $user
     * @return void
     */
    public static function fetchDailyList( $year, $month, $day = 0, $offset = 0, $limit = false, $type = array(), $mode = 'object', $group = false, $parentNode = null, $user = null, $attribute_filter = array() )
    {
        $result = array();
        $db = eZDB::instance();

        $query = eZEvent::generateSQLQuery( $db, $year, $month, $day, $offset, $limit, $type, false, $parentNode, $user,$attribute_filter );

        $params = array( 'offset' => $offset, 'limit' => $limit );
        $sqlResult = $db->arrayQuery( $query, $params );
        if ( is_array( $sqlResult ) && count( $sqlResult ) )
        {
            $sqlResult = self::applyRepetitionInMonth( $sqlResult, $year, $month, $day );
            if ( $group )
            {
                $currentDay = 1;
            }
            if ( $mode == 'object' )
            {
                foreach( $sqlResult as $item )
                {
                    $object = eZContentObject::fetch( (int) $item['id'], false );
                    $object = new eZContentObject( $object );
                    $object->startDate = $item['start_date'];
                    if ( $object && $object->attribute( 'can_read' ) )
                    {
                        if ( $group )
                        {
                            $currentDay = date('j', $item['start_date'] );
                            $result[$currentDay][] = $object;
                        }
                        else
                        {
                            $result[] = $object;
                        }
                    }
                    unset( $object );
                }
                ksort($result);
            }
            elseif ( $mode == 'event' )
            {
                foreach( $sqlResult as $item )
                {
                    $event = eZEvent::fetch( $item['event_id'] );
                    $event->startDate = $item['start_date'];
                    if ( $group )
                    {
                        $currentDay = date('j', $item['start_date'] );
                        $result[$currentDay][] = $event;
                    }
                    else
                    {
                        $result[] = $event;
                    }
                }
            }
            elseif ( $mode == 'array' )
            {
                foreach( $sqlResult as $item )
                {
                    if ( $group )
                    {
                        $currentDay = date('j', $item['start_date'] ) . ".".$month;
                        $result[$currentDay][] = $item;
                    }
                    else
                    {
                        $result[] = $item;
                    }
                }
            }
        }

        return $result;
    }

    public static function fetchDailyListCount( $year, $month, $day = 0, $type = array() )
    {
        $result = array();
        $db = eZDB::instance();

        $query = eZEvent::generateSQLQuery( $db, $year, $month, $day, 0, false, $type, true );

        $params = array( 'column' => 'cnt');
        $sqlResult = $db->arrayQuery( $query, $params );
        if ( is_array( $sqlResult ) && count( $sqlResult ) == 1 )
        {
            $result = $sqlResult[0];
        }

        return $result;
    }

    /**
     * Generate SQL query
     *
     * Generate SQL query to fetch a list of events during the specified time
     * span.
     *
     * You may optionally specify a list of root nodes in the content tree, as
     * a limit from where eventy are shown. For example a list of
     * calendars, which are aggregated into one calendar.
     *
     * Additionally the ID of an user may be specified, who then needs to have
     * a relation (link) to the event content object, which means, that (s)he
     * attends the events.
     *
     * @param eZDb $db
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $offset
     * @param int $limit
     * @param string $type
     * @param bool $getCount
     * @param array $parentNode
     * @param int $user
     * @return string
     */
    private static function generateSQLQuery( $db, $year, $month, $day, $offset, $limit, $type, $getCount = false, $parentNode = array(), $user = null, $attribute_filter = array() )
    {
        if ( count( $attribute_filter ) )
        {
            $sortingInfo = array( 'sortCount'           => 0,
                                'sortingFields'       => " id ASC",
                                'attributeJoinCount'  => 0,
                                'attributeFromSQL'    => "",
                                'attributeTargetSQL'  => "",
                                'attributeWhereSQL'   => "" );
            $attributeFilter = eZEvent::createAttributeFilterSQLStrings( $attribute_filter, $sortingInfo );
        }


        $typeSQL = eZEvent::generateTypeSQLPart( $db, $type );
        $startDate = mktime(0, 0, 0, $month, ( $day == 0 ) ? 1 : $day, $year);
        $endDate = mktime(23, 59, 59, $month, ( $day == 0 ) ? date( "t", $startDate ) : $day, $year);

        $weekDay = date( 'N', $startDate ) - 1;
        $dayOfMonth = date('j', $startDate);
        $monthOfYear = date('n', $startDate);

        $useVersionName     = true;
        $versionNameTables  = eZEvent::createVersionNameTablesSQLString ( $useVersionName );
        $versionNameTargets = eZEvent::createVersionNameTargetsSQLString( $useVersionName );
        $versionNameJoins   = eZEvent::createVersionNameJoinsSQLString  ( $useVersionName );

        if ( $getCount )
        {
            $select = "SELECT count( ezevent.event_id ) cnt\n";
        }
        else
        {
            $select = "SELECT ezcontentobject.id $versionNameTargets, ezcontentobject_tree.node_id, ezevent.id event_id, ezevent.start_date start_date, ezevent.end_date end_date, ezevent.event_type event_type\n";
//            $select = "SELECT ezcontentobject.*, ezevent.id event_id, ezevent.start_date start_date, ezevent.end_date end_date, ezevent.event_type event_type\n";
        }

        if ( $day != 0 )
        {
            $queryWeekday    = " AND WEEKDAY(FROM_UNIXTIME(ezevent.start_date)) = $weekDay ";
            $queryDayOfMonth = " AND DAYOFMONTH(FROM_UNIXTIME(ezevent.start_date)) = $dayOfMonth ";
            if ( $db->databaseName() == 'oracle' )
            {
                $queryWeekday    = " AND TO_CHAR( TO_DATE( '19700101000000','YYYYMMDDHH24MISS' ) + NUMTODSINTERVAL( ezevent.start_date, 'SECOND' ), 'D' ) = $weekDay ";
                $queryDayOfMonth = " AND TO_CHAR( TO_DATE( '19700101000000','YYYYMMDDHH24MISS' ) + NUMTODSINTERVAL( ezevent.start_date, 'SECOND' ), 'DD') = $dayOfMonth ";
            }
        }
        else
        {
            $queryWeekday = " ";
            $queryDayOfMonth = " ";
        }

        $queryMonthOfYear = " AND MONTH(FROM_UNIXTIME(ezevent.start_date)) = $monthOfYear ";
        if ( $db->databaseName() == 'oracle' )
        {
            $queryYear = $monthOfYear;
            if ( $monthOfYear < 10 )
            {
                $queryYear = "0$monthOfYear";
            }
            $queryMonthOfYear = " AND TO_CHAR( TO_DATE( '19700101000000','YYYYMMDDHH24MISS' ) + NUMTODSINTERVAL( ezevent.start_date, 'SECOND' ), 'mm') = $queryYear";
        }

        $subtreeLikeStatements = '';

        // Apply additional filter, if a subtree limitation has been set
        $subtreeLimitation = '';
        if ( $parentNode !== null && count( $parentNode ) )
        {
            if ( !is_array( $parentNode ) )
            {
                $parentNode = array( $parentNode );
            }

            $subtreeLikeStatements = array();
            foreach ( $parentNode as $subtreeNodeId )
            {
                $subtreeLikeStatements[] = "ezcontentobject_tree.path_string LIKE '%/" . (int) $subtreeNodeId . "/%'";
            }
            $subtreeLikeStatements = 'AND ( ' . implode( " OR\n", $subtreeLikeStatements ) . ' ) ';
        }

        // Apply additional filter to only include users, which have an event relation
        $userLimitation = '';
        if ( $user !== null )
        {
            $userLimitation = "RIGHT JOIN
                    ezcontentobject_link
                ON (
                    ( ezcontentobject_link.from_contentobject_id = ezcontentobject.id )
                    AND
                    ( ezcontentobject_link.to_contentobject_id = " . (int) $user . " )
                    AND
                    ( ezcontentobject_link.relation_type = 8 )
                )
            ";
        }

        $attrFrom = isset($attributeFilter['from']) ?  $attributeFilter['from'] : "";
        $attrWhere = isset($attributeFilter['where']) ? " " . $attributeFilter['where'] : "";

        $languageFilter = ' AND ' . eZContentLanguage::languagesSQLFilter( 'ezcontentobject' );

        // Merge general query
        return "$select
                FROM ezevent, ezcontentobject_attribute $attrFrom $versionNameTables,  ezcontentobject
                RIGHT JOIN
                    ezcontentobject_tree
                ON (
                    ( ezcontentobject_tree.contentobject_id = ezcontentobject.id )
                    $subtreeLikeStatements
                )
                $subtreeLimitation
                $userLimitation
                WHERE ezevent.start_date != 0
                AND
                (
                    (
                        ezevent.event_type = 11
                        AND
                        (
                            ( ezevent.end_date != 0 AND ezevent.start_date >= $startDate AND ezevent.end_date <= $endDate )
                            OR
                            ( ezevent.end_date != 0 AND ezevent.start_date <= $endDate AND ezevent.end_date >= $startDate )
                            OR
                            ( ezevent.end_date = 0  AND ezevent.start_date >= $startDate AND ezevent.start_date <= $endDate )
                        )
                    )
                    OR
                    ( ezevent.event_type = 12 AND ( ezevent.start_date >= $startDate AND ezevent.start_date <= $endDate ) )
                    OR
                    ( ezevent.event_type = 15 $queryWeekday
                        AND ezevent.start_date <= $endDate AND (  ezevent.end_date = 0 OR ezevent.end_date >= $startDate )
                    )
                    OR
                    ( ezevent.event_type = 16 $queryDayOfMonth
                        AND ezevent.start_date <= $endDate AND (  ezevent.end_date = 0 OR ezevent.end_date >= $startDate )
                    )
                    OR
                    ( ezevent.event_type = 17 $queryDayOfMonth $queryMonthOfYear
                        AND ezevent.start_date <= $endDate AND (  ezevent.end_date = 0 OR ezevent.end_date >= $startDate )
                    )
                )
                AND $attrWhere
                ezevent.is_parent = 0
                AND ezevent.is_temp = 0
                $languageFilter
                $versionNameJoins
                AND ezcontentobject.status=1
                AND ezcontentobject_attribute.id = ezevent.contentobject_attribute_id
                AND ezcontentobject_attribute.version = ezevent.version
                AND ezcontentobject_attribute.language_id = ezcontentobject_name.language_id
                AND ezcontentobject.id = ezcontentobject_attribute.contentobject_id
                AND ezcontentobject.current_version = ezevent.version
                AND ezcontentobject.id = ezcontentobject_tree.contentobject_id
                AND ezcontentobject_tree.main_node_id = ezcontentobject_tree.node_id
                ORDER BY ezevent.start_date";
    }

    private static function generateTypeSQLPart( $db, $typeInformation )
    {
        $typeIDArray = array();
        if ( !is_array( $typeInformation ) || count( $typeInformation ) != 2 )
        {
            return false;
        }

        $typeType = $typeInformation[0];
        $typeList = $typeInformation[1];

        if ( !is_array( $typeList ) || count( $typeInformation ) == 0 )
        {
            return false;
        }
        else
        {
            foreach( $typeList as $type )
            {
                switch( $type )
                {
                    case "normal":
                    {
                        $typeIDArray[] = eZEvent::EVENTTYPE_NORMAL;
                    } break;
                    case "full_day":
                    {
                        $typeIDArray[] = eZEvent::EVENTTYPE_FULL_DAY;
                    } break;
//                     case "no_time":
//                     {
//                         $typeIDArray[] = eZEvent::EVENTTYPE_NO_TIME;
//                     } break;
//                     case "to_be_defined":
//                     {
//                         $typeIDArray[] = eZEvent::EVENTTYPE_TO_BE_DEFINED;
//                     } break;
                    case "simple":
                    {
                        $typeIDArray[] = eZEvent::EVENTTYPE_WEEKLY_REPEAT;
                    } break;
                    case "weekly":
                    {
                        $typeIDArray[] = eZEvent::EVENTTYPE_WEEKLY_REPEAT;
                    } break;
                    case "monthly":
                    {
                        $typeIDArray[] = eZEvent::EVENTTYPE_MONTHLY_REPEAT;
                    } break;
                    case "yearly":
                    {
                        $typeIDArray[] = eZEvent::EVENTTYPE_YEARLY_REPEAT;
                    } break;
                }
            }

            if ( count( $typeIDArray ) )
            {
                return $db->generateSQLINStatement( $typeIDArray, 'ezevent.event_type', ( $typeType == 'exclude' ) ? true : false );
            }
            else
            {
                return false;
            }
        }
    }

    static function createAttributeFilterSQLStrings( &$attributeFilter, &$sortingInfo )
    {
        // Check for attribute filtering

        $filterSQL = array( 'from'    => '',
                            'where'   => '' );

        $invalidFilterSQL = false;
        $totalAttributesFiltersCount = 0;
        $invalidAttributesFiltersCount = 0;

        if ( isset( $attributeFilter ) && $attributeFilter !== false )
        {
            $filterArray = $attributeFilter;

            // Check if first value of array is a string.
            // To check for and/or filtering
            $filterJoinType = 'AND';
            if ( is_string( $filterArray[0] ) )
            {
                if ( strtolower( $filterArray[0] ) == 'or' )
                {
                    $filterJoinType = 'OR';
                }
                else if ( strtolower( $filterArray[0] ) == 'and' )
                {
                    $filterJoinType = 'AND';
                }
                unset( $filterArray[0] );
            }

            $attibuteFilterJoinSQL = "";
            $filterCount = $sortingInfo['sortCount'];
            $justFilterCount = 0;

            $db = eZDB::instance();
            if ( is_array( $filterArray ) )
            {
                // Handle attribute filters and generate SQL
                $totalAttributesFiltersCount = count( $filterArray );

                foreach ( $filterArray as $filter )
                {
                    $isFilterValid = true; // by default assumes that filter is valid

                    $filterAttributeID = $filter[0];
                    $filterType = $filter[1];
                    $filterValue = is_array( $filter[2] ) ? '' : $db->escapeString( $filter[2] );

                    $useAttributeFilter = false;
                    switch ( $filterAttributeID )
                    {
                        case 'path':
                        {
                            $filterField = 'path_string';
                        } break;
                        case 'published':
                        {
                            $filterField = 'ezcontentobject.published';
                        } break;
                        case 'modified':
                        {
                            $filterField = 'ezcontentobject.modified';
                        } break;
                        case 'modified_subnode':
                        {
                            $filterField = 'modified_subnode';
                        } break;
                        case 'section':
                        {
                            $filterField = 'ezcontentobject.section_id';
                        } break;
                        case 'depth':
                        {
                            $filterField = 'depth';
                        } break;
                        case 'class_identifier':
                        {
                            $filterField = 'ezcontentclass.identifier';
                        } break;
                        case 'class_name':
                        {
                            $classNameFilter = eZContentClassName::sqlFilter();
                            $filterField = $classNameFilter['nameField'];
                            $filterSQL['from'] .= ", $classNameFilter[from]";
                            $filterSQL['where'] .= "$classNameFilter[where] AND ";
                        } break;
                        case 'priority':
                        {
                            $filterField = 'ezcontentobject_tree.priority';
                        } break;
                        case 'name':
                        {
                            $filterField = 'ezcontentobject.name';
                        } break;
                        case 'owner':
                        {
                            $filterField = 'ezcontentobject.owner_id';
                        } break;
                        default:
                        {
                            $useAttributeFilter = true;
                        } break;
                    }

                    if ( $useAttributeFilter )
                    {
                        if ( !is_numeric( $filterAttributeID ) )
                            $filterAttributeID = eZContentObjectTreeNode::classAttributeIDByIdentifier( $filterAttributeID );

                        if ( $filterAttributeID === false )
                        {
                            $isFilterValid = false;
                            if( $filterJoinType === 'AND' )
                            {
                                // go out
                                $invalidAttributesFiltersCount = $totalAttributesFiltersCount;
                                break;
                            }

                            ++$invalidAttributesFiltersCount;
                        }
                        else
                        {
                            // Check datatype for filtering
                            $filterDataType = eZContentObjectTreeNode::sortKeyByClassAttributeID( $filterAttributeID );
                            if ( $filterDataType === false )
                            {
                                $isFilterValid = false;
                                if( $filterJoinType === 'AND' )
                                {
                                    // go out
                                    $invalidAttributesFiltersCount = $totalAttributesFiltersCount;
                                    break;
                                }

                                // check next filter
                                ++$invalidAttributesFiltersCount;
                            }
                            else
                            {
                                $sortKey = false;
                                if ( $filterDataType == 'string' )
                                {
                                    $sortKey = 'sort_key_string';
                                }
                                else
                                {
                                    $sortKey = 'sort_key_int';
                                }

                                $filterField = "a$filterCount.$sortKey";

                                // Use the same joins as we do when sorting,
                                // if more attributes are filtered by we will append them
                                if ( $filterCount >= $sortingInfo['attributeJoinCount'] )
                                {
                                    $filterSQL['from']  .= ", ezcontentobject_attribute a$filterCount ";
                                    $filterSQL['where'] .= "
                                       a$filterCount.contentobject_id = ezcontentobject.id AND
                                       a$filterCount.contentclassattribute_id = $filterAttributeID AND
                                       a$filterCount.version = ezevent.version AND ";
                                    $filterSQL['where'] .= eZContentLanguage::sqlFilter( "a$filterCount", 'ezcontentobject' ).' AND ';
                                }
                                else
                                {
                                    $filterSQL['where'] .= "
                                      a$filterCount.contentobject_id = ezcontentobject.id AND
                                      a$filterCount.contentclassattribute_id = $filterAttributeID AND
                                      a$filterCount.version = ezevent.version AND ";
                                    $filterSQL['where'] .= eZContentLanguage::sqlFilter( "a$filterCount", 'ezcontentobject' ). ' AND ';
                                }
                            }
                        }
                    }

                    if( $isFilterValid )
                    {
                        $hasFilterOperator = true;
                        // Controls quotes around filter value, some filters do this manually
                        $noQuotes = false;
                        // Controls if $filterValue or $folder[2] is used, $filterValue is already escaped
                        $unEscape = false;

                        switch ( $filterType )
                        {
                            case '=' :
                            {
                                $filterOperator = '=';
                            }break;

                            case '!=' :
                            {
                                $filterOperator = '<>';
                            }break;

                            case '>' :
                            {
                                $filterOperator = '>';
                            }break;

                            case '<' :
                            {
                                $filterOperator = '<';
                            }break;

                            case '<=' :
                            {
                                $filterOperator = '<=';
                            }break;

                            case '>=' :
                            {
                                $filterOperator = '>=';
                            }break;

                            case 'like':
                            case 'not_like':
                            {
                                $filterOperator = ( $filterType == 'like' ? 'LIKE' : 'NOT LIKE' );
                                // We escape the string ourselves, this MUST be done before wildcard replace
                                $filter[2] = $db->escapeString( $filter[2] );
                                $unEscape = true;
                                // Since * is used as wildcard we need to transform the string to
                                // use % as wildcard. The following rules apply:
                                // - % -> \%
                                // - * -> %
                                // - \* -> *
                                // - \\ -> \

                                $filter[2] = preg_replace( array( '#%#m',
                                                                  '#(?<!\\\\)\\*#m',
                                                                  '#(?<!\\\\)\\\\\\*#m',
                                                                  '#\\\\\\\\#m' ),
                                                           array( '\\%',
                                                                  '%',
                                                                  '*',
                                                                  '\\\\' ),
                                                           $filter[2] );
                            } break;

                            case 'in':
                            case 'not_in' :
                            {
                                $filterOperator = ( $filterType == 'in' ? 'IN' : 'NOT IN' );
                                // Turn off quotes for value, we do this ourselves
                                $noQuotes = true;
                                if ( is_array( $filter[2] ) )
                                {
                                    reset( $filter[2] );
                                    while ( list( $key, $value ) = each( $filter[2] ) )
                                    {
                                        // Non-numerics must be escaped to avoid SQL injection
                                        $filter[2][$key] = is_numeric( $value ) ? $value : "'" . $db->escapeString( $value ) . "'";
                                    }
                                    $filterValue = '(' .  implode( ",", $filter[2] ) . ')';
                                }
                                else
                                {
                                    $hasFilterOperator = false;
                                }
                            } break;

                            case 'between':
                            case 'not_between' :
                            {
                                $filterOperator = ( $filterType == 'between' ? 'BETWEEN' : 'NOT BETWEEN' );
                                // Turn off quotes for value, we do this ourselves
                                $noQuotes = true;
                                if ( is_array( $filter[2] ) )
                                {
                                    // Check for non-numerics to avoid SQL injection
                                    if ( !is_numeric( $filter[2][0] ) )
                                        $filter[2][0] = "'" . $db->escapeString( $filter[2][0] ) . "'";
                                    if ( !is_numeric( $filter[2][1] ) )
                                        $filter[2][1] = "'" . $db->escapeString( $filter[2][1] ) . "'";

                                    $filterValue = $filter[2][0] . ' AND ' . $filter[2][1];
                                }
                            } break;

                            default :
                            {
                                $hasFilterOperator = false;
                                eZDebug::writeError( "Unknown attribute filter type: $filterType", "eZContentObjectTreeNode::subTree()" );
                            }break;

                        }
                        if ( $hasFilterOperator )
                        {
                            if ( ( $filterCount - $sortingInfo['sortCount'] ) > 0 )
                                $attibuteFilterJoinSQL .= " $filterJoinType ";

                            // If $unEscape is true we get the filter value from the 2nd element instead
                            // which must have been escaped by filter type
                            $filterValue = $unEscape ? $filter[2] : $filterValue;

                            $attibuteFilterJoinSQL .= "$filterField $filterOperator ";
                            $attibuteFilterJoinSQL .= $noQuotes ? "$filterValue " : "'" . $filterValue . "' ";

                            $filterCount++;
                            $justFilterCount++;
                        }
                    }
                } // end of 'foreach ( $filterArray as $filter )'

                if( $totalAttributesFiltersCount == $invalidAttributesFiltersCount )
                {
                    eZDebug::writeNotice( "Attribute filter returned false" );
                    $filterSQL = $invalidFilterSQL;
                }
                else
                {
                    if ( $justFilterCount > 0 )
                        $filterSQL['where'] .= "\n                            ( " . $attibuteFilterJoinSQL . " ) AND ";
                }
            } // endif 'if ( is_array( $filterArray ) )'
        }

        return $filterSQL;
    }


    /*!
        \a static
    */
    static function createVersionNameTablesSQLString( $useVersionName )
    {
        $versionNameTables = '';

        if ( $useVersionName )
        {
            $versionNameTables = ', ezcontentobject_name ';
        }

        return $versionNameTables;
    }

    /*!
        \a static
    */
    static function createVersionNameTargetsSQLString( $useVersionName )
    {
        $versionNameTargets = '';

        if ( $useVersionName )
        {
            $versionNameTargets = ', ezcontentobject_name.name name,  ezcontentobject_name.real_translation ';
        }


        return $versionNameTargets;
    }

    /*!
        \a static
    */
    static function createVersionNameJoinsSQLString( $useVersionName, $includeAnd = true, $onlyTranslated = false, $lang = false, $treeTableName = 'ezcontentobject_tree' )
    {
        $versionNameJoins = '';
        if ( $useVersionName )
        {
            if ( $includeAnd )
            {
                $versionNameJoins .= ' AND ';
            }
           $versionNameJoins .= " $treeTableName.contentobject_id = ezcontentobject_name.contentobject_id and
                                   $treeTableName.contentobject_version = ezcontentobject_name.content_version and ";
            $versionNameJoins .= eZContentLanguage::sqlFilter( 'ezcontentobject_name', 'ezcontentobject' );
        }
        return $versionNameJoins;
    }

    public function isInEdit()
    {
        $parentEvent = false;
        if ( !($parentEvent = $this->parentEvent() ) )
        {
            $parentEvent = $this;
        }
        $object = $parentEvent->contentObject();
        $draftVersions = $object->versions( true, array( 'conditions' => array( 'status' => array( array( eZContentObjectVersion::STATUS_DRAFT, eZContentObjectVersion::STATUS_INTERNAL_DRAFT ) ) ) ) );
        if ( count ( $draftVersions ) == 0 )
        {
            return false;
        }
        return $draftVersions;

    }

    public function eventAttendees()
    {
        $contentObjectAttribute = eZContentObjectAttribute::fetch( $this->attribute( 'contentobject_attribute_id' ), $this->attribute( 'version' ) );
        if ( !$contentObjectAttribute )
        {
            $objectAttributeContent = eZEventType::defaultObjectAttributeContent();
            return $objectAttributeContent;
        }
        $xmlText = $contentObjectAttribute->attribute( 'data_text' );

        if ( trim( $xmlText ) == '' )
        {
            $objectAttributeContent = eZEventType::defaultObjectAttributeContent();
            return $objectAttributeContent;
        }
        $doc = eZEventType::parseXML( $xmlText );
        $content = eZEventType::createObjectContentStructure( $doc );

        return $content;
    }




}

?>
