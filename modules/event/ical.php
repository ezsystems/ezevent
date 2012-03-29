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

$Module = $Params['Module'];
$http = eZHTTPTool::instance();

$eventID        = $Params['EventID'];

$url = '';

$event = eZEvent::fetch( $eventID );

if ( !$event )
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

$icalEvent = new eZiICalEvent( $event);

$httpCharset = eZTextCodec::httpCharset();
$locale = eZLocale::instance();
$languageCode = $locale->httpLocaleCode();

$headerList = array( 'Expires'              => 'Mon, 26 Jul 1997 05:00:00 GMT',
                     'Last-Modified'        => gmdate( 'D, d M Y H:i:s' ) . ' GMT',
                     'Cache-Control'        => 'no-cache, must-revalidate',
                     'Pragma'               => 'no-cache',
                     'X-Powered-By'         => 'eZ Publish',
                     'Content-Type'         => 'text/calendar; charset=' . $httpCharset,
                     'Content-Disposition'  => 'attachment; filename=event.ics',
                     'Served-by'            => $_SERVER["SERVER_NAME"],
                     'Content-language'     => $languageCode );
foreach( $headerList as $key => $value )
{
    header( $key . ': ' . $value );
}


echo trim( $icalEvent->toIcal() );


eZDB::checkTransactionCounter();
eZExecution::cleanExit();
?>
