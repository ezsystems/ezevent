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

$nodeID        = $Params['NodeID'];
$forwardTarget = $Params['Target'];
$forwardMonth  = $Params['Month'];
$forwardYear   = $Params['Year'];

$ini = eZINI::instance( 'event.ini' );

$url = '';

$node = eZContentObjectTreeNode::fetch( $nodeID );
if ( !$node )
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}

$object = $node->attribute( 'object');

if ( $object->attribute( 'class_identifier') != 'event' )
{
    if ( $http->hasSessionVariable( "LastAccessesURI" ) )
    {
        return $Module->redirectTo( $http->sessionVariable( "LastAccessesURI" ) );
    }
}

if ( $ini->variable( "ForwardSettings", "RedirectToParentCalendar" ) == 'enabled' )
{
    $parentNode = $node->attribute( 'parent');
    $url = $parentNode->attribute( 'url_alias');

    if ( $forwardYear && $forwardMonth )
    {
        $url .= "/(year)/" . $forwardYear . "/(month)/" . $forwardMonth;
    }
    elseif ( $forwardMonth )
    {
        $url .= "/(month)/" . $forwardMonth;
    }
    elseif ( $forwardYear )
    {
        $url .= "/(year)/" . $forwardYear;
    }
}


if ( $forwardTarget )
{
    $urlMap = $ini->variable( "ForwardSettings", "ForwardUrlMap" );
    if ( array_key_exists( $forwardTarget, $urlMap ) )
    {
        $url = $urlMap[$forwardTarget] . '/' . $url;
    }
}

return $Module->redirectTo( $url );

?>
