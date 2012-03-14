{if is_set($attribute_base)|not()}
    {def $attribute_base='ContentObjectAttribute'}
{/if}

{def
    $related_object_list    = $attribute.content.attendees
    $related_id_list        = array()
    $event_type             = 11
    $event_mode             = 'full-day'
}
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
{def
    $defaultDate         = 0
}
{switch match=$attribute.contentclass_attribute.data_int1}
    {case match=1}
        {set $defaultDate = currentdate()}
    {/case}
    {case match=2}
        {set $defaultDate = currentdate()}
    {/case}
{/switch}

<div id="ezeventattribute" class="block">
    <div class="ezeventattribute-left">
                {if $attribute.content.has_parent_event}
                    <input type="hidden" name="{$attribute_base}_event_typeofevent_{$attribute.id}" value="11" />
                    <p>{'You are editing a single date of the recurent event "%name"<br />(period from %start to %end)'|i18n( 'design/ezteamroom/content/datatype',,hash( '%name', $attribute.content.parent_event.content_object.name|wash() , '%start', $attribute.content.parent_event.start.timestamp|l10n(shortdate), '%end', $attribute.content.parent_event.end.timestamp|l10n(shortdate) ) )}</p>
                {else}
                <div id="ezeventattribute_selection" class="float-break">
                    <label>{'Select type'|i18n( 'design/ezteamroom/content/datatype' )}:</label>
                    <ul>
                        <li>
                            <input onchange="ezevent_is_full_day(this.checked)" type="checkbox" name="{$attribute_base}_event_eventmode_{$attribute.id}" value="full-day" {if $event_mode|eq('full-day')}checked="checked"{/if} />{'Full Day'|i18n( 'design/ezteamroom/content/datatype' )}
                        </li>
{*                        <li>
                            <input onchange="ezevent_is_full_day(this.checked)" type="checkbox" name="{$attribute_base}_event_eventmode_{$attribute.id}" value="full-day" {if $event_mode|eq('full-day')}checked="checked"{/if} />{'Recurrence'|i18n( 'design/ezteamroom/content/datatype' )}
                        </li>*}
                    </ul>
                </div>
                <br />
                <div id="ezeventattribute_selection" class="float-break">
                    <label>{'Select recurrence'|i18n( 'design/ezteamroom/content/datatype' )}:</label>
                    <ul>
                        <li>
                            <input onclick="ezevent_setedittype(11)" type="radio" name="{$attribute_base}_event_typeofevent_{$attribute.id}" value="11" {if $event_type|eq(11)}checked="checked"{/if} />{'None'|i18n( 'design/ezteamroom/content/datatype' )}
                        </li>
                        <li>
                            <input onclick="ezevent_setedittype(15)" type="radio" name="{$attribute_base}_event_typeofevent_{$attribute.id}" value="15" {if $event_type|eq(15)}checked="checked"{/if} />{'Weekly'|i18n( 'design/ezteamroom/content/datatype' )}
                        </li>
                        <li>
                            <input onclick="ezevent_setedittype(16)" type="radio" name="{$attribute_base}_event_typeofevent_{$attribute.id}"  value="16" {if $event_type|eq(16)}checked="checked"{/if} />{'Monthly'|i18n( 'design/ezteamroom/content/datatype' )}
                        </li>
                        <li>
                            <input onclick="ezevent_setedittype(17)" type="radio" name="{$attribute_base}_event_typeofevent_{$attribute.id}"  value="17" {if $event_type|eq(17)}checked="checked"{/if} />{'Yearly'|i18n( 'design/ezteamroom/content/datatype' )}
                        </li>
                    </ul>
                </div>

                <br />

                {/if}

                <div id="ezeventattribute_label_startdate">
                    <label>{'Startdate'|i18n( 'design/ezteamroom/content/datatype' )}</label>
                </div>
                <div id="ezeventattribute_label_date_from">
                    <label>{'Date'|i18n( 'design/ezteamroom/content/datatype' )}</label>
                </div>
                <div class="float-break"></div>
                <div id="ezeventattribute_startdatetime" class="dateblock" >
                    <div id="ezeventattribute_startdate" class="datesubblock">
                            <div class="element">
                                <label>{'Year'|i18n( 'design/ezteamroom/content/datatype' )}:</label>
                                <input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}_year" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_event_year_{$attribute.id}" size="5" value="{if $attribute.content.start.is_valid}{$attribute.content.start.year}{elseif ne( $defaultDate, 0 )}{$defaultDate|datetime( 'custom', '%Y' )}{/if}" />
                            </div>
                            <div class="element">
                                <label>{'Month'|i18n( 'design/ezteamroom/content/datatype' )}:</label>
                                <input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}_month"  class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_event_month_{$attribute.id}" size="3" value="{if $attribute.content.start.is_valid}{$attribute.content.start.month}{elseif ne( $defaultDate, 0 )}{$defaultDate|datetime( 'custom', '%n' )}{/if}" />
                            </div>
                            <div class="element">
                                <label>{'Day'|i18n( 'design/ezteamroom/content/datatype' )}:</label>
                                <input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}_day" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_event_day_{$attribute.id}" size="3" value="{if $attribute.content.start.is_valid}{$attribute.content.start.day}{elseif ne( $defaultDate, 0 )}{$defaultDate|datetime( 'custom', '%d' )}{/if}" />
                            </div>
                            <div class="element">
                                <img class="datepicker-icon" src={"calendar_icon.png"|ezimage} id="{$attribute_base}_event_cal_{$attribute.id}" width="24" height="28" onclick="showDatePicker( '{$attribute_base}', '{$attribute.id}', 'event' );" style="cursor: pointer;" />
                                <div id="{$attribute_base}_event_cal_container_{$attribute.id}" style="display: none; position: absolute;"></div>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                            </div>
                    </div>

                    <div id="ezeventattribute_starttime" class="datesubblock">
                            <div class="element">
                                <label>{'Hour'|i18n( 'design/ezteamroom/content/datatype' )}:</label>
                                <input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}_hour" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_event_hour_{$attribute.id}" size="3" value="{if$attribute.content.start.is_valid}{$attribute.content.start.hour}{elseif ne( $defaultDate, 0 )}{$defaultDate|datetime( 'custom', '%H' )}{/if}" />
                            </div>
                            <div class="element">
                                <label>{'Minute'|i18n( 'design/ezteamroom/content/datatype' )}:</label>
                                <input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}_minute" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_event_minute_{$attribute.id}" size="3" value="{if $attribute.content.start.is_valid}{$attribute.content.start.minute}{elseif ne( $defaultDate, 0 )}{$defaultDate|datetime( 'custom', '%i' )}{/if}" />
                            </div>
                    </div>
                </div>

                <div class="float-break"></div>

                <div id="ezeventattribute_label_enddate">
                    <input id="ezeventattribute_checkbox_has_enddate" onchange="ezevent_has_enddate(this.checked)" type="checkbox" value="1" name="{$attribute_base}_event_has_enddate_{$attribute.id}" {if $attribute.content.end.is_valid}checked="checked"{/if} />
                    <label>{'Enddate'|i18n( 'design/ezteamroom/content/datatype' )}</label>
                </div>
                <div id="ezeventattribute_label_period_till">
                    <input id="ezeventattribute_checkbox_has_enddate" onchange="ezevent_has_enddate(this.checked)" type="checkbox" value="1" name="{$attribute_base}_event_has_enddate_{$attribute.id}" {if $attribute.content.end.is_valid}checked="checked"{/if} />
                    <label>{'Recur till'|i18n( 'design/ezteamroom/content/datatype' )}</label>
                </div>
                <div class="float-break"></div>
                <div id="ezeventattribute_enddatetime" class="dateblock">
                    <div id="ezeventattribute_enddate" class="datesubblock">
                            <div class="element">
                                <label>{'Year'|i18n( 'design/ezteamroom/content/datatype' )}:</label>
                                <input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}end_year" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_eventend_year_{$attribute.id}end" size="5" value="{if $attribute.content.end.is_valid}{$attribute.content.end.year}{/if}" />
                            </div>
                            <div class="element">
                                <label>{'Month'|i18n( 'design/ezteamroom/content/datatype' )}:</label>
                                <input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}end_month"  class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_eventend_month_{$attribute.id}end" size="3" value="{if $attribute.content.end.is_valid}{$attribute.content.end.month}{/if}" />
                            </div>
                            <div class="element">
                                <label>{'Day'|i18n( 'design/ezteamroom/content/datatype' )}:</label>
                                <input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}end_day" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_eventend_day_{$attribute.id}end" size="3" value="{if $attribute.content.end.is_valid}{$attribute.content.end.day}{/if}" />
                            </div>
                            <div class="element">
                                <img class="datepicker-icon" src={"calendar_icon.png"|ezimage} id="{$attribute_base}_eventend_cal_{$attribute.id}end" width="24" height="28" onclick="showDatePicker( '{$attribute_base}', '{$attribute.id}end', 'eventend' );" style="cursor: pointer;" />
                                <div id="{$attribute_base}_eventend_cal_container_{$attribute.id}end" style="display: none; position: absolute;"></div>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                            </div>
                    </div>

                    <div id="ezeventattribute_endtime" class="datesubblock">
                            <div class="element">
                                <label>{'Hour'|i18n( 'design/ezteamroom/content/datatype' )}:</label>
                                <input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}_hour" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_eventend_hour_{$attribute.id}end" size="3" value="{if $attribute.content.end.is_valid}{$attribute.content.end.hour}{/if}" />
                            </div>
                            <div class="element">
                                <label>{'Minutes'|i18n( 'design/ezteamroom/content/datatype' )}:</label>
                                <input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}_minute" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_eventend_minute_{$attribute.id}end" size="3" value="{if $attribute.content.end.is_valid}{$attribute.content.end.minute}{/if}" />
                            </div>
                    </div>

                </div>
    </div>
    <div class="ezeventattribute-right">

                {def $current_teamroom_id = $assigned_node_array.0.parent_node_obj.parent.node_id}

                <div id="ezeventattribute_attendees">
                    <label>{'Attendees'|i18n( 'design/ezteamroom/content/datatype' )}:</label>

                    <div class="block float-break">
                        <div class="element">
                            <ul id="list_of_users">
                            {def $user_obj = null}
                            {foreach $related_object_list as $user}
                                {set $user_obj = fetch( 'content', 'object', hash( 'object_id', $user.contentobject_id ) )}
                                <li id="list_of_users_entry_{$user.contentobject_id}">
                                        <input type="hidden" name="{$attribute_base}_event_attendees_{$attribute.id}[]" value="{$user.contentobject_id}">
                                    {$user_obj.name|wash()}
                                    <a href="#" onclick="return ezevent_removeUserListEntry( '{$user.contentobject_id}', '{$user_obj.name}' )"><img src={"remove.png"|ezimage} /></a>
                                </li>
                            {/foreach}
                            </ul>
                        </div>
                    </div>

                    <div id="ajaxsearchbox" class="tab-container">
                        {'Please select more users here'|i18n( 'design/ezteamroom/content/datatype' )}
                        <div class="block search-results">
                            <div id="ajaxinitsearchresult" style="overflow: hidden">
                                <div id="result_{$current_teamroom_id}"></div>
                            </div>
                        </div>
            {*
                        <br />
                        <a href="#" onclick="ezajaxSearchLink( {"ezajax/subtree/2/teamroom/0/30"|ezurl('single')}, 'ajaxsearchresult' );toggleSubElements( this, 'ajaxsearchresult' );return false;">{"other teamrooms"|i18n('design/ezteamroom/content/datatype')}</a>
                        <div class="block search-results">
                            <div id="ajaxsearchresult" style="overflow: hidden">
                        </div>
            *}
                    </div>

                    <script type="text/javascript">
                        <!--
                        if ( window.ez === undefined ) document.write('<script type="text/javascript" src={'javascript/ezoe/ez_core.js'|ezdesign}><\/script>');
                        -->
                    </script>

                    <script type="text/javascript">
                    <!--
                        var ezajaxSearchUrl = {"ezajax/search"|ezurl};
                        ezajaxSearchDisplay = ez.$('ajaxinitsearchresult');
                        var ezajaxSearchObject, ezajaxSearchObjectSpans, ezajaxObject = new ez.ajax();
                        var baseURL = {"ezajax/subtree"|ezurl};
                        var selectID = "{$attribute_base}_event_attendees_{$attribute.id}[]";
                        var removeIconSrc = {"remove.png"|ezimage};
                        var searchTeamroomLink = {concat("ezajax/subtree/",$current_teamroom_id,'/user/0/50')|ezurl('single')};

                        var ezevent_current_editmode = '{$event_mode}';
                        var ezevent_current_edittype = {$event_type};
                    -->
                    </script>
                </div>

    </div>
    <div class="float-break"></div>
</div>

<script type="text/javascript" language="javascript">
    ezevent_setedittype( {$event_type} );
    ezevent_is_full_day( {if $event_mode|eq('full-day')}true{else}false{/if} )
    ezevent_has_enddate( {if $attribute.content.end.is_valid}true{else}false{/if} );


    var tr_users = document.getElementById('result_{$current_teamroom_id}');
    ezajaxSearchLink( {concat('/ezajax/subtree/', $current_teamroom_id, '/user/0/50')|ezurl('single')}, 'result_{$current_teamroom_id}' );

{*     ezevent_initWithMode( {$event_mode}); *}
</script>
{undef $current_teamroom_id}
