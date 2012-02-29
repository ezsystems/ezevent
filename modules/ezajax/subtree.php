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

$http = eZHTTPTool::instance();

$Module = $Params['Module'];

$NodeID        = $Params['NodeID'];
$ClassID       = $Params['Class'];


$SearchOffset = 0;
if ( isSet( $Params['SearchOffset'] ) )
{
    $SearchOffset = (int) $Params['SearchOffset'];
}

$SearchLimit = 10;
if ( isSet( $Params['SearchLimit'] ) )
{
    $SearchLimit = (int) $Params['SearchLimit'];
}

$VarName = '';
if ( isSet( $Params['VarName'] ) )
{
    $VarName = trim( $Params['VarName'] );
}

if ( $VarName )
{
    $VarName .= ' = ';
}


$params = array( 'Depth'            =>  0,
                 'DepthOperator'    => 'eq',
                 'Limit'            => $SearchLimit,
                 'Offset'           => $SearchOffset,
                 'ClassFilterType'  => 'include',
                 'ClassFilterArray' => array( $ClassID ) );

$children = eZContentObjectTreeNode::subTreeByNodeID( $params, $NodeID  );
$childrenCount = eZContentObjectTreeNode::subTreeCountByNodeID( $params, $NodeID  );

$r = '[]';
$jsonObj = new JsonContent();
if ( $children )
{
    $r = $jsonObj->encode( $children );
}


echo $VarName . '{SearchResult:' . $r . ",\nSearchCount:" . $childrenCount .
     ",\nSearchOffset:" . $SearchOffset . ",\nSearchLimit:" . $SearchLimit . "}";

eZExecution::cleanExit();

?>
