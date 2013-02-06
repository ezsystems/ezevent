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

$Module = array(
    'name' => 'event'
);

$ViewList['split'] = array(
   'functions' => array( 'split' ),
   'script' => 'edit.php',
    'params' => array( 'ObjectID', 'Timestamp', 'Language' )
);

$ViewList['forward'] = array(
   'functions' => array( 'forward' ),
   'script' => 'forward.php',
    'params' => array( 'NodeID', 'Target', 'Month', 'Year' )
);

$ViewList['ical'] = array(
   'functions' => array( 'export' ),
   'script' => 'ical.php',
    'params' => array( 'EventID' )
);



$FunctionList['split'] = array( );
$FunctionList['forward'] = array( );
$FunctionList['export'] = array( );

?>

