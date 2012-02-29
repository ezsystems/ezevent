<?php

$http = eZHTTPTool::instance();

$Module = $Params['Module'];

$r = '[]';
$children = array();
$childrenCount = 0;
$SearchOffset = 0;
$SearchLimit = 1;

if ( array_key_exists( 'NodeID', $Params ) )
{
    $children = array( eZContentObjectTreeNode::fetch( $Params['NodeID'] ) );
}

$jsonObj = new JsonContent();
if ( $children )
{
    $r = $jsonObj->encode( $children );
}


echo '{SearchResult:' . $r . ",\nSearchCount:" . $childrenCount .
     ",\nSearchOffset:" . $SearchOffset . ",\nSearchLimit:" . $SearchLimit . "}";

eZExecution::cleanExit();

?>

