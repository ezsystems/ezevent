function $( element )
{
    return document.getElementById( element );
}

var searchTeamroomLink = null;

function ezevent_initWithMode( mode )
{
    ezevent_seteditmode( mode );
    ezajaxSearchLink( searchTeamroomLink );
}


// switch full day / normal
function ezevent_is_full_day( cchecked )
{
    ezevent_setDisplay( Array( 'starttime', 'endtime' ), ( cchecked )?  "none" : "inline" );
}

// show_enddate
function ezevent_has_enddate( cchecked )
{
    ezevent_setDisplay( Array( 'enddatetime' ), ( cchecked )? "inline" : "none" );
}

function ezevent_setedittype( etype )
{
    switch ( etype )
    {
        case 11:
            // Normal date ( or several days )
            ezevent_setDisplay( Array( 'label_startdate', 'label_enddate' ), "inline" );
            ezevent_setDisplay( Array( 'label_date_from', 'label_period_till' ), "none" );
            break;
/*        case 12:
            moved to etype
            // full day
            ezevent_setDisplay( Array( 'startdate', 'enddate'  ), "inline" );
            ezevent_setDisplay( Array( 'starttime', 'endtime'  ), "none" );

            ezevent_setDisplay( Array( 'label_startdate', 'label_enddate' ), "inline" );
            ezevent_setDisplay( Array( 'label_date_from', 'label_period_till' ), "none" );
            break;
*/
        case 15:// weekly
        case 16:// monthly
        case 17:// yearly
            ezevent_setDisplay( Array( 'label_startdate', 'label_enddate' ), "none" );
            ezevent_setDisplay( Array( 'label_date_from', 'label_period_till' ), "inline" );
            break;
    }

}

function ezevent_setDisplay( blockIDList, display )
{
    for (var i = 0; i < blockIDList.length; ++i)
    {
        ezevent_showHideBlock( blockIDList[i], display );
    }
}

function ezevent_showHideBlock( blockID, display )
{
    //var block = $( 'ezeventattribute_' + blockID );
    var block = document.getElementById('ezeventattribute_' + blockID);
    if ( block )
    {
        block.style.display =  display;
    }
}

function unixtimetodate( timestamp )
{
    var date = new Date( timestamp * 1000 );
    dateString = date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds() + ' ' + date.getFullYear() + '/' + date.getMonth() + '/' + date.getDay();
    return dateString;
}

var gLinkTarget = '';
var gLinkinProgress = false;

function toggleSubElements( element, subelemID )
{
    element.onclick = function(){ var subelem = document.getElementById( subelemID );
                                  subelem.style.display = ( subelem.style.display == 'none' ) ? 'block' : 'none' ;
                                  return false
                                 } ;
    return false;
}

function ezajaxSearchLink( url, lTarget )
{
    if ( lTarget )
    {
        gLinkTarget = lTarget;
    }

    ezajaxObject.load( url, false, ezajaxSearchLinkBack );
    return false;
}

function ezajaxSearchLinkBack( r )
{
    if ( gLinkinProgress == true )
      return false;

    gLinkinProgress = true;
    if ( gLinkTarget != '' )
    {
        //lTarget = $( gLinkTarget );
        lTarget = document.getElementById(gLinkTarget);
        gLinkTarget = '';
    }
    else
    {
        lTarget = ezajaxSearchDisplay;
    }
   // In this case we trust the source, so we can use eval
   eval( 'ezajaxSearchObject = ' +  r.responseText );
   var search = ezajaxSearchObject.SearchResult, root = ezajaxSearchUrl.split('ezajax/search')[0], temp = '';

   if ( !search.length )
   {
        if ( lTarget.el )
        {
            lTarget.el.innerHTML = 'No Search Result Found';
        }
        else
        {
            lTarget.innerHTML = 'No Search Result Found';
        }
   }
   else
   {
       temp = '<ul id="available_users">';
       for (var i = 0, l = search.length; i < l; i++)
       {
            if ( search[i].class_identifier == 'user' )
            {
                //var userList = $( 'list_of_users' );
                var user = document.getElementById('list_of_users_entry_' + search[i].id);
                if ( user )
                {
                    // user has been already added
                    continue;
                }

                temp += '<li id="result_' +  search[i].id +'_li"><a href="#" onclick="return ezevent_addUserListEntry( \'' +  search[i].id + '\', \''+ search[i].name +'\' );">' + search[i].name + '<\/a><div  id="result_' +  search[i].id +'"></div><\/li>';
            }
            else
            {
                temp += '<li><a href="' + baseURL + '/' + search[i].node_id + '/user/0/30" onclick="ezajaxSearchLink( this.href, \'result_' +  search[i].node_id + '\' );toggleSubElements(this,\'result_' +  search[i].node_id + '\');return false;">' + search[i].name + '<\/a><div  id="result_' +  search[i].node_id +'"></div><\/li>';
            }
        }
        temp += '</ul>';
        if ( lTarget.el )
        {
            lTarget.el.innerHTML = temp;
        }
        else
        {
            lTarget.innerHTML = temp;
        }
        ezajaxSearchObjectSpans = ez.$$('span', lTarget);
    }
    gLinkinProgress = false;
}

function  ezevent_addUserListEntry( id, uname )
{
    //var userList = $( 'list_of_users' );
    var user = document.getElementById( 'list_of_users_entry_' + id);
    if ( user )
    {
        // user has been already added
        return false;
    }
    //var userList = $( 'list_of_users' );
    var userList = document.getElementById('list_of_users');
    if ( !userList )
    {
        return false;
    }

    temp  = '<li id="list_of_users_entry_' + id +'">';
    temp += '<input type="hidden" name="'+ selectID +'" value="' + id + '">';
    temp += uname;
    temp += '<a href="#" onClick="return ezevent_removeUserListEntry(\''+ id +'\', \'' + uname +'\' )"><img src="'+ removeIconSrc +'"></a>';
    temp += '</li>';
    if ( userList.el )
    {
        userList.el.innerHTML = userList.innerHTML + temp;
    }
    else
    {
        userList.innerHTML = userList.innerHTML + temp;
    }

    var item = document.getElementById( 'result_' + id + '_li' );
    if ( item )
    {
        var tmp = item.parentNode.removeChild( item );
    }
    return false;
}

function  ezevent_removeUserListEntry( id, uname )
{
    var temp = '<li id="result_' +  id +'_li"><a href="#" onclick="return ezevent_addUserListEntry( ' +  id + ', \''+ uname +'\' );">' + uname + '<\/a><div  id="result_' +  id +'"></div><\/li>';
    var itemul = document.getElementById("available_users");
    if ( itemul )
    {
        itemul.innerHTML = itemul.innerHTML + temp;
    }


    //var item = $( id );
    var item = document.getElementById( 'list_of_users_entry_' + id);
    if ( item )
    {
        var tmp = item.parentNode.removeChild( item );
    }
    return false;
}
