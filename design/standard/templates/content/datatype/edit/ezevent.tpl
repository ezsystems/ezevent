{if is_set( $attribute_base )|not()}

    {def $attribute_base='ContentObjectAttribute'}

{/if}

{def $related_object_list = $attribute.content.attendees
     $related_id_list     = array()
     $event_type          = 11}

{if $attribute.content.event_type}

    {set $event_type = $attribute.content.event_type}

{/if}

{* Create array with related object IDs, to make it easier accessable in the
   select statement below.
*}
{foreach $related_object_list as $related_object}

    {set $related_id_list = $related_id_list|append( $related_object.id )}

{/foreach}

{* Set the default date depending on the class settings *}
{def $defaultDate = 0}

{switch match = $attribute.contentclass_attribute.data_int1}

    {case match = 1}

        {set $defaultDate = currentdate()}

    {/case}

    {case match = 2}

        {set $defaultDate = currentdate()}

    {/case}

{/switch}

<div id="ezeventattribute" class="block">
 <table border="0" width="100%" class="ezevent_structural_table">
  <tr>
   <td>

{if $attribute.content.has_parent_event}

    <div id="ezeventattribute_selection" style="white-space: nowrap;">
     <input type="hidden" name="{$attribute_base}_event_typeofevent_{$attribute.id}" value="11" />
     <p>

    {'You are editing a single date of the recurent event "%1'|i18n( 'design/ezevent/content/datatype', , array( $attribute.content.parent_event.content_object.name ) )|wash()}<br />
    ( {'period from %1 to %2'|i18n( 'design/ezevent/content/datatype', , array( $attribute.content.parent_event.start.timestamp|l10n( 'shortdate' ), $attribute.content.parent_event.end.timestamp|l10n( 'shortdate' ) ) )} )

     </p>
    </div>

{else}

    <div id="ezeventattribute_selection" style="white-space: nowrap;">
     <label>{'Select type'|i18n( 'design/ezevent/content/datatype' )}:</label>
     <br />
     <input class="event_type_selection" onclick="ezevent_seteditmode(11)" type="radio" name="{$attribute_base}_event_typeofevent_{$attribute.id}" value="11" {if $event_type|eq(11)}checked="checked"{/if} />{'Normal'|i18n( 'design/ezevent/content/datatype' )}
     <input class="event_type_selection" onclick="ezevent_seteditmode(12)" type="radio" name="{$attribute_base}_event_typeofevent_{$attribute.id}" value="12" {if $event_type|eq(12)}checked="checked"{/if} />{'Full Day'|i18n( 'design/ezevent/content/datatype' )}
     <input class="event_type_selection" onclick="ezevent_seteditmode(15)" type="radio" name="{$attribute_base}_event_typeofevent_{$attribute.id}" value="15" {if $event_type|eq(15)}checked="checked"{/if} />{'Weekly'|i18n( 'design/ezevent/content/datatype' )}
     <input class="event_type_selection" onclick="ezevent_seteditmode(16)" type="radio" name="{$attribute_base}_event_typeofevent_{$attribute.id}" value="16" {if $event_type|eq(16)}checked="checked"{/if} />{'Monthly'|i18n( 'design/ezevent/content/datatype' )}
     <input class="event_type_selection" onclick="ezevent_seteditmode(17)" type="radio" name="{$attribute_base}_event_typeofevent_{$attribute.id}" value="17" {if $event_type|eq(17)}checked="checked"{/if} />{'Yearly'|i18n( 'design/ezevent/content/datatype' )}
    </div>

{/if}

    <table border="0" id="ezevent_table">
     <tbody>
      <tr id="ezeventattribute_table_row_1">
       <td id="ezeventattribute_table_cell_11"></td>
       <td id="ezeventattribute_table_cell_12"></td>
      </tr>
      <tr id="ezeventattribute_table_row_2">
       <td id="ezeventattribute_table_cell_21"></td>
       <td id="ezeventattribute_table_cell_22"></td>
      </tr>
      <tr id="ezeventattribute_table_row_3">
       <td id="ezeventattribute_table_cell_31" colspan="2"></td>
      </tr>
      <tr id="ezeventattribute_table_row_4">
       <td id="ezeventattribute_table_cell_41"></td>
       <td id="ezeventattribute_table_cell_42"></td>
      </tr>
      <tr id="ezeventattribute_table_row_5">
       <td id="ezeventattribute_table_cell_51" colspan="2"></td>
      </tr>
     </tbody>
    </table>
    <div id="ezeventattribute_startdate">
     <label id="ezeventattribute_startdate_label"></label>
     <div class="block float-break">
      <div class="element">
       <label>{'Year'|i18n( 'design/ezevent/content/datatype' )}:</label>
       <br />
       <input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}_year" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_event_year_{$attribute.id}" size="4" maxlength="4" value="{if $attribute.content.start.is_valid}{$attribute.content.start.year}{elseif ne( $defaultDate, 0 )}{$defaultDate|datetime( 'custom', '%Y' )}{/if}" style="width: auto;" />
      </div>
      <div class="element">
       <label>{'Month'|i18n( 'design/ezevent/content/datatype' )}:</label>
       <br />
       <input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}_month"  class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_event_month_{$attribute.id}" size="2" maxlength="2" value="{if $attribute.content.start.is_valid}{$attribute.content.start.month}{elseif ne( $defaultDate, 0 )}{$defaultDate|datetime( 'custom', '%n' )}{/if}" style="width: auto;" />
      </div>
      <div class="element">
       <label>{'Day'|i18n( 'design/ezevent/content/datatype' )}:</label>
       <br />
       <input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}_day" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_event_day_{$attribute.id}" size="2" maxlength="2" value="{if $attribute.content.start.is_valid}{$attribute.content.start.day}{elseif ne( $defaultDate, 0 )}{$defaultDate|datetime( 'custom', '%d' )}{/if}" style="width: auto;" />
      </div>
      <div class="element">
       <img class="datepicker-icon" src={"calendar_icon.png"|ezimage} id="{$attribute_base}_event_cal_{$attribute.id}" width="24" height="28" onclick="showDatePicker( '{$attribute_base}', '{$attribute.id}', 'event' );" style="cursor: pointer;" alt="datepicker" />
       <div id="{$attribute_base}_event_cal_container_{$attribute.id}" style="display: none; position: absolute;"></div>
       &nbsp;&nbsp;&nbsp;&nbsp;
      </div>
     </div>
    </div>
    <div id="ezeventattribute_starttime">
     <label id="ezeventattribute_starttime_label"></label>
     <div class="block float-break">
      <div class="element">
       <label>{'Hour'|i18n( 'design/ezevent/content/datatype' )}:</label>
       <br />
       <input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}_hour" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_event_hour_{$attribute.id}" size="2" maxlength="2" value="{if$attribute.content.start.is_valid}{$attribute.content.start.hour}{elseif ne( $defaultDate, 0 )}{$defaultDate|datetime( 'custom', '%H' )}{/if}" style="width: auto;" />
      </div>
      <div class="element">
       <label>{'Minute'|i18n( 'design/ezevent/content/datatype' )}:</label>
       <br />
       <input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}_minute" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_event_minute_{$attribute.id}" size="2" maxlength="2" value="{if $attribute.content.start.is_valid}{$attribute.content.start.minute}{elseif ne( $defaultDate, 0 )}{$defaultDate|datetime( 'custom', '%i' )}{/if}" style="width: auto;" />
      </div>
     </div>
    </div>
    <div id="ezeventattribute_enddate">
     <label id="ezeventattribute_enddate_label"></label>
     <div class="block float-break">
      <div class="element">
       <label>{'Year'|i18n( 'design/ezevent/content/datatype' )}:</label>
       <br />
       <input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}end_year" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_eventend_year_{$attribute.id}end" size="4" maxlength="4" value="{if $attribute.content.end.is_valid}{$attribute.content.end.year}{/if}" style="width: auto;" />
      </div>
      <div class="element">
       <label>{'Month'|i18n( 'design/ezevent/content/datatype' )}:</label>
       <br />
       <input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}end_month"  class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_eventend_month_{$attribute.id}end" size="2" maxlength="2" value="{if $attribute.content.end.is_valid}{$attribute.content.end.month}{/if}" style="width: auto;" />
      </div>
      <div class="element">
       <label>{'Day'|i18n( 'design/ezevent/content/datatype' )}:</label>
       <br />
       <input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}end_day" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_eventend_day_{$attribute.id}end" size="2" maxlength="2" value="{if $attribute.content.end.is_valid}{$attribute.content.end.day}{/if}" style="width: auto;" />
      </div>
      <div class="element">
       <img class="datepicker-icon" src={"calendar_icon.png"|ezimage} id="{$attribute_base}_eventend_cal_{$attribute.id}end" width="24" height="28" onclick="showDatePicker( '{$attribute_base}', '{$attribute.id}end', 'eventend' );" style="cursor: pointer;" alt="datepicker" />
       <div id="{$attribute_base}_eventend_cal_container_{$attribute.id}end" style="display: none; position: absolute;"></div>
       &nbsp;&nbsp;&nbsp;&nbsp;
      </div>
     </div>
    </div>
    <div id="ezeventattribute_endtime">
     <label id="ezeventattribute_endtime_label"></label>
     <div class="block float-break">
      <div class="element">
       <label>{'Hours'|i18n( 'design/ezevent/content/datatype' )}:</label>
       <br />
       <input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}end_hour" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_eventend_hour_{$attribute.id}end" size="2" maxlength="2" value="{if $attribute.content.end.is_valid}{$attribute.content.end.hour}{/if}" style="width: auto;" />
      </div>
      <div class="element">
       <label>{'Minutes'|i18n( 'design/ezevent/content/datatype' )}:</label>
       <br />
       <input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}end_minute" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_eventend_minute_{$attribute.id}end" size="2" maxlength="2" value="{if $attribute.content.end.is_valid}{$attribute.content.end.minute}{/if}" style="width: auto;" />
      </div>
     </div>
    </div>
   </td>
   <td style="width: 100%;">
    <div id="ezeventattribute_attendees">
     <label>{'Attendees'|i18n( 'design/ezevent/content/datatype' )}:</label>
     <div class="block float-break">
      <div class="element">
       <ul id="list_of_users">

    {foreach $related_object_list as $user}

        <li id="list_of_users_entry_{$user.contentobject_id}">
         <input type="hidden" name="{$attribute_base}_event_attendees_{$attribute.id}[]" value="{$user.contentobject_id}" />

        {fetch( 'content', 'object', hash( 'object_id', $user.contentobject_id ) ).name|wash()}

         <a href="#" onclick="return ezevent_removeUserListEntry( 'list_of_users_entry_{$user.contentobject_id}' )"><img src={"remove.png"|ezimage} alt="remove" /></a>
        </li>

    {/foreach}

       </ul>
      </div>
     </div>
     <div id="ajaxsearchbox" class="tab-container">

{def $startAttendeeSearchNodeID = 2
     $attendeeSearchClassFilter = 'folder'
               $searchTargetURL = ''}

{if ezini_hasvariable( 'AttendeeSettings', 'SearchRootNode', 'event.ini' )}

    {set $startAttendeeSearchNodeID = ezini( 'AttendeeSettings', 'SearchRootNode', 'event.ini' )}

{/if}

{if ezini_hasvariable( 'AttendeeSettings', 'ClassIdentifierFilter', 'event.ini' )}

    {set $attendeeSearchClassFilter = ezini( 'AttendeeSettings', 'ClassIdentifierFilter', 'event.ini' )}

{/if}

{set $searchTargetURL = concat( 'ezajax/subtree/', $startAttendeeSearchNodeID, '/', $attendeeSearchClassFilter )}

{'Please select more users here'|i18n( 'design/ezevent/content/datatype' )}

( <a href="#" onclick="return ezajaxSearchLink( {$searchTargetURL|ezurl( 'single' )} );">{'Refresh list'|i18n( 'design/ezevent/content/datatype' )}</a> )

      <div class="block search-results">
       <div id="ajaxsearchresult" style="overflow: hidden"></div>
      </div>
      <script type="text/javascript">
      <!--

        if ( window.ez === undefined ) document.write('<script type="text/javascript" src={'javascript/ezoe/ez_core.js'|ezdesign}><\/script>');

      -->
      </script>
      <script type="text/javascript">
      <!--

        ezajaxSearchDisplay = ez.$('ajaxsearchresult');
        var ezajaxSearchUrl = {'ezajax/search'|ezurl()},
            ezajaxSearchObject, ezajaxSearchObjectSpans, ezajaxObject = new ez.ajax(),
            baseURL = {'ezajax/subtree'|ezurl()},
            selectID = '{$attribute_base}_event_attendees_{$attribute.id}[]',
            removeIconSrc = {'remove.png'|ezimage()},
            searchLink = {$searchTargetURL|ezurl( 'single' )},
            labelTextMap = new Object();

        labelTextMap[11] = new Object();
        labelTextMap[15] = new Object();
        labelTextMap[11]['startdate'] = '{'From'|i18n( 'design/ezevent/content/datatype' )|wash()}';
        labelTextMap[11]['starttime'] = '&nbsp;';
        labelTextMap[11]['enddate']   = '{'To'|i18n( 'design/ezevent/content/datatype' )|wash()}';
        labelTextMap[11]['endtime']   = '&nbsp;';
        labelTextMap[15]['startdate'] = '{'Date'|i18n( 'design/ezevent/content/datatype' )|wash()}';
        labelTextMap[15]['starttime'] = '{'From'|i18n( 'design/ezevent/content/datatype' )|wash()}';
        labelTextMap[15]['enddate']   = '{'Period ends on'|i18n( 'design/ezevent/content/datatype' )|wash()}';
        labelTextMap[15]['endtime']   = '{'To'|i18n( 'design/ezevent/content/datatype' )|wash()}';
        labelTextMap[12] = labelTextMap[11];
        labelTextMap[16] = labelTextMap[15];
        labelTextMap[17] = labelTextMap[15];

      -->
      </script>
     </div>
    </div>
   </td>
  </tr>
 </table>
</div>
<script type="text/javascript" language="javascript">
    ezevent_initWithMode( {$event_type} );
</script>
