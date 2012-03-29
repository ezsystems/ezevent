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

$ObjectID     = $Params['ObjectID'];
$Timestamp    = $Params['Timestamp'];
$EditLanguage = $Params['Language'];

$object = eZContentObject::fetch( $ObjectID );
if ( !$object )
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

$dateTime = new eZDateTime();
$dateTime->setTimeStamp( $Timestamp );

if ( !$dateTime->isValid() )
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

if ( !$EditLanguage || $EditLanguage == "" )
{
    $ini = eZINI::instance( );
    $EditLanguage = $ini->variable( 'RegionalSettings', 'ContentObjectLocale' );
}



// Split the event into three event, to selectively edit the selected event
$newContentObjectId = eZEvent::splitEventAt(
    $ObjectID,
    $Timestamp
);
if ( $newContentObjectId )
{
    // Forward to the edit view
    return $Module->redirect(
        'content', 'edit',
        array(
            $newContentObjectId,
            'f',
            $EditLanguage
        )
    );
}
else
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}
?>
