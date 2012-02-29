<?php

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