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

$FunctionList = array();

$FunctionList['list'] = array( 'name'            => 'list',
                                     'operation_types' => array( 'read' ),
                                     'call_method'     => array( 'include_file' => 'extension/ezevent/modules/event/ezeventfunctioncollection.php',
                                                                 'class'        => 'eZEventFunctionCollection',
                                                                 'method'       => 'fetchList'
                                                               ),
                                     'parameter_type'  => 'standard',
                                     'parameters'      => array( array( 'name'     => 'year',
                                                                        'type'     => 'integer',
                                                                        'required' => true
                                                                      ),
                                                                 array( 'name'     => 'month',
                                                                        'type'     => 'integer',
                                                                        'required' => true
                                                                      ),
                                                                 array( 'name'     => 'day',
                                                                        'type'     => 'integer',
                                                                        'required' => false,
                                                                        'default'  => 0
                                                                      ),
                                                                 array( 'name'     => 'offset',
                                                                        'type'     => 'integer',
                                                                        'required' => false
                                                                      ),
                                                                 array( 'name'     => 'limit',
                                                                        'type'     => 'integer',
                                                                        'required' => false
                                                                      ),
                                                                 array( 'name'     => 'type',
                                                                        'type'     => 'array',
                                                                        'required' => false
                                                                      ),
                                                                 array( 'name'     => 'mode',
                                                                        'type'     => 'string',
                                                                        'required' => false,
                                                                        'default'  => 'object'
                                                                      ),
                                                                 array( 'name'     => 'group',
                                                                        'type'     => 'boolean',
                                                                        'required' => false,
                                                                        'default'  => false
                                                                      ),
                                                                 array( 'name'     => 'parent_node_id',
                                                                        'type'     => 'array',
                                                                        'required' => false,
                                                                        'default'  => null
                                                                      ),
                                                                 array( 'name'     => 'user_id',
                                                                        'type'     => 'int',
                                                                        'required' => false,
                                                                        'default'  => null
                                                                      ),
                                                                 array( 'name'     => 'attribute_filter',
                                                                        'type'     => 'array',
                                                                        'required' => false,
                                                                        'default'  => null
                                                                      ),
                                                               )
                                    );

$FunctionList['list_count'] = array( 'name'            => 'list_count',
                                     'operation_types' => array( 'read' ),
                                     'call_method'     => array( 'include_file' => 'extension/ezevent/modules/event/ezeventfunctioncollection.php',
                                                                 'class'        => 'eZEventFunctionCollection',
                                                                 'method'       => 'fetchListCount'
                                                               ),
                                     'parameter_type'  => 'standard',
                                     'parameters'      => array( array( 'name'     => 'year',
                                                                        'type'     => 'integer',
                                                                        'required' => true
                                                                      ),
                                                                 array( 'name'     => 'month',
                                                                        'type'     => 'integer',
                                                                        'required' => true
                                                                      ),
                                                                 array( 'name'     => 'day',
                                                                        'type'     => 'integer',
                                                                        'required' => false,
                                                                        'default'  => 0
                                                                      ),
                                                                array(  'name'     => 'type',
                                                                        'type'     => 'array',
                                                                        'required' => false
                                                                      ),
                                                               )
                                    );


?>
