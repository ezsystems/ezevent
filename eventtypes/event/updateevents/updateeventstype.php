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

class updateEventsType extends eZWorkflowEventType
{
    const EZ_WORKFLOW_TYPE_UPDATEEVENTS = "updateevents";

    /**
     * Constructor of event
     *
     * Constructor creates the event definition and registers itself for the
     * triggers, it should be activated with.
     *
     * @return void
     */
    public function __construct()
    {
        // Human readable name of the event displayed in admin interface
        $this->eZWorkflowEventType(
            updateEventsType::EZ_WORKFLOW_TYPE_UPDATEEVENTS,
            "Merge event attributes"
        );

        // Let this workflow be trigger after content has been updated
        $this->setTriggerTypes( array(
            'content' => array(
                'publish' => array(
                    'before',
                    'after',
                )
            )
        ) );
    }

    /**
     * Execute workflow
     * 
     * Should return the acceptance state of the workflow, which can be one of:
     *  - eZWorkflowType::STATUS_ACCEPTED
     *
     * @param eZWorkflowProcess $process 
     * @param eZWorkflowEvent $event 
     * @return int
     */
    public function execute( $process, $event )
    {
        eZDebug::writeError( 'Event updateEventsType executed on publish.' );

        $processParameters = $process->attribute( 'parameter_list' );
        $object  = eZContentObject::fetch( $processParameters['object_id'] );
        $dataMap = $object->attribute( 'data_map' );

        if ( !isset( $dataMap['event_date'] ) )
        {
            // Object has no event date property, irrelevant for us
            return eZWorkflowType::STATUS_ACCEPTED;
        }

        $event   = $dataMap['event_date']->content();
        
        // If the event does not have a parent relation, we do not need to do
        // anything.
        if ( $event->attribute( 'parent_event_id' ) == 0 )
        {
            return eZWorkflowType::STATUS_ACCEPTED;
        }

        // If the event is no repeated event, we also do not need to do
        // anything.
        if ( !in_array( $event->attribute( 'event_type' ), array( 
                eZEvent::EVENTTYPE_WEEKLY_REPEAT,
                eZEvent::EVENTTYPE_MONTHLY_REPEAT,
                eZEvent::EVENTTYPE_YEARLY_REPEAT,
            ) ) )
        {
            return eZWorkflowType::STATUS_ACCEPTED;
        }

        // If all constraints are met, we update all related events depending
        // on the edit.
        $event->updateRelatedEvents();
        return eZWorkflowType::STATUS_ACCEPTED;
    }
}

// Register workflow in main engine
eZWorkflowEventType::registerEventType( 
    updateEventsType::EZ_WORKFLOW_TYPE_UPDATEEVENTS, 
    'updateEventsType'
);

?>
