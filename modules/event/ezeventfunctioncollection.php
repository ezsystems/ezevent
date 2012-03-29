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

require_once( 'autoload.php' );

class eZEventFunctionCollection
{
    public function __construct()
    {
    }

    /**
     * Fetch list of events
     *
     * Returns a list of events at the specified day / month.
     * 
     * @param int $year 
     * @param int $month 
     * @param int $day 
     * @param int $offset 
     * @param int $limit 
     * @param array $type 
     * @param string $mode 
     * @param bool $group 
     * @return array
     */
    public function fetchList( $year, $month, $day = 0, $offset = 0, $limit = false, $type = array(), $mode = 'object', $group = false, $parent_node_id = null, $user = null, $attribute_filter = array() )
    {
        $result = eZEvent::fetchDailyList( $year, $month, $day, $offset, $limit, $type, $mode, $group, $parent_node_id, $user, $attribute_filter );
        return array( 'result' => $result );
    }

    public function fetchListCount( $year, $month, $day = 0, $type = array() )
    {
        $result = eZEvent::fetchDailyListCount( $year, $month, $day, $type );
        return array( 'result' => $result );
    }
}

?>
