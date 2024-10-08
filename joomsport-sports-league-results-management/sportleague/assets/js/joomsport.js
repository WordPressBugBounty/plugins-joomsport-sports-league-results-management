function delCom(num){
    jQuery.post(
        'index.php?tmpl=component&option=com_joomsport&controller=users&task=del_comment&format=row&cid='+num,
        function( result ) {
            if(result){
                alert(result);
            } else {
                var d = document.getElementById('divcomb_'+num).parentNode;
                d.removeChild(jQuery('#divcomb_'+num).get(0));
            }
        });

}


function componentPopup(){
    var href = window.location.href;
    var regex = new RegExp("[&\\?]" + name + "=");

    if(href.indexOf("tmpl=component") > -1){
        window.print();
    }

    if(href.indexOf("?") > -1)
      var hrefnew = href + "&tmpl=component";
  else
      var hrefnew = href + "?tmpl=component";

  window.open(hrefnew,'jsmywindow','width=750,height=700,scrollbars=1,resizable=1');
}

function fSubmitwTab(e){
    if(jQuery('#joomsport-container').find('div.tabs').find('li.active').find('a').attr('href')){
        jQuery('input[name="jscurtab"]').val(jQuery('#joomsport-container').find('div.tabs').find('li.active').find('a').attr('href'));
    }
    e.form.submit();
}

jQuery(document).ready(function(){
 jQuery('#comForm').on('submit', function(e) {
    e.preventDefault();
    if(jQuery('#addcomm').val()){
        var submcom = jQuery('#submcom').get(0);
            //submcom.disabled = true;
            jQuery.ajax({
                url: jQuery('#comForm').attr('action'),
                type: "post",
                data: jQuery('#comForm').serialize(),
                success: function(result){

                    if(result){
                        result = JSON.parse(result);
                        if(result.error){
                            alert(result.error);
                        }else
                        if(result.id){
                            var li = jQuery("<li>");
                            li.attr("id", 'divcomb_'+result.id);

                            var div = jQuery("<div>");
                            div.attr("class", "comments-box-inner");
                            var divInner = jQuery("<div>");
                            divInner.attr("class","jsOverflowHidden");
                            divInner.css("position", "relative");
                            divInner.appendTo(div);
                            jQuery('<div class="date">'+result.datetime+' '+result.delimg+'</div>').appendTo(divInner);
                            jQuery(result.photo).appendTo(divInner);

                            jQuery('<h4 class="nickname">'+result.name+'</h4>').appendTo(divInner);
                            jQuery('<div class="jsCommentBox">'+result.posted+'</div>').appendTo(div);
                            div.appendTo(li);
                            li.appendTo("#all_comments");
                            //var allc = jQuery('#all_comments').get(0);
                            //allc.innerHTML = allc.innerHTML + result;

                            submcom.disabled = false;
                            jQuery('#addcomm').val('');
                        }

                    }
                    jQuery('#comForm').get(0).reset();
                }
            });
        }
    });
 jQuery('div[class^="knockplName knockHover"]').hover(
    function(){
        var hclass = jQuery(this).attr("class");
        var tbody = jQuery(this).closest('tbody');

        tbody.find('[class^="knockplName knockHover"]').each(function(){
            if(jQuery(this).hasClass(hclass)){
                jQuery(this).addClass("knIsHover");
            }
        });
            //console.log('div.'+hclass);
            //jQuery('div.'+hclass).addClass("knIsHover");
        },
        function(){
            var tbody = jQuery(this).closest('tbody');
            tbody.find('[class^="knockplName knockHover"]').each(function(){
                if(jQuery(this).hasClass("knIsHover")){
                    jQuery(this).removeClass("knIsHover");
                }
            });
        }
        );

 jQuery("#aSearchFieldset").on("click",function(){
    if(jQuery("#jsFilterMatches").css("display") == 'none'){
        jQuery("#jsFilterMatches").css("display","block");
    }else{
        jQuery("#jsFilterMatches").css("display","none");
    }
});
 jQuery('#joomsport-container select').select2({minimumResultsForSearch: 20});
 var $select = jQuery('#mapformat select').select2();
    //console.log($select);
    $select.each(function(i,item){
      //console.log(item);
      jQuery(item).select2("destroy");
  });
});

jQuery(document).ready(function() {
    jQuery("body").tooltip(
    {
        selector: '[data-toggle2=tooltip]',
        html:true
    });
    jQuery('body').on('focus',".jsdatefield", function(){
        jQuery(this).datepicker({ dateFormat: 'yy-mm-dd'});
    });
});
jQuery(function() {
    jQuery( '.jstooltip' ).tooltip({
        html:true,
        position: {
            my: "center bottom-20",
            at: "center top",
            using: function( position, feedback ) {
              jQuery( this ).css( position );
              jQuery( "<div>" )
              .addClass( "arrow" )
              .addClass( feedback.vertical )
              .addClass( feedback.horizontal )
              .appendTo( this );
          }
      }
  });
});

jQuery(window).on('load',function() {
    var maxwidth = 200;
    var maxheight = 200;
    var maxheightWC = 200;

    var divwidth = jQuery('#jsPlayerListContainer').parent().width();
    var cols = Math.floor(parseInt(divwidth)/255);
    if(!cols){
        cols = 1;
    }

    var widthCols = Math.round(100/cols);
    var widthColsPix = Math.round(divwidth/cols);

    jQuery('.jsplayerCart').css({'width': widthCols+'%'});
    //jQuery('.jsplayerCart').width(parseInt(widthCols)+'%');

    jQuery('.imgPlayerCart').each(function(){
        //console.log(jQuery(this).find('img').prop('naturalHeight'));
        if(jQuery(this).find('img').prop('naturalWidth') > maxwidth){
            maxwidth = jQuery(this).find('img').prop('naturalWidth');
        }
        var widthNatural = parseInt(jQuery(this).find('img').prop('naturalWidth'));
        if(widthNatural < widthColsPix){
            coeff = 1;
        }else{
            if(widthNatural > 0){
                var coeff = (widthColsPix/(widthNatural+32));
            }else{
                coeff = 1;
            }
        }

        if(jQuery(this).find('img').prop('naturalHeight') > maxheight){
            maxheight = jQuery(this).find('img').prop('naturalHeight');
            maxheightWC = maxheight*coeff;
            console.log(widthColsPix+':'+widthNatural);
            console.log(maxheight+':'+coeff+':'+maxheightWC);
        }
    });
    maxheightWC = maxheightWC;
    console.log(maxheightWC);
    //jQuery('.imgPlayerCart').width(maxwidth);
    jQuery('.imgPlayerCart').height(maxheightWC);
    jQuery('.imgPlayerCart > .innerjsplayerCart').height(maxheightWC);
    jQuery('.imgPlayerCart > .innerjsplayerCart').css({'line-height':maxheightWC+'px'});

});

function jsToggleTH() {
    jQuery('table.jsStandings th').each( function(){
        var alternate = true;
        jQuery(this).click(function() {
            jQuery(this).find("span").each(function() {
                if (alternate) { var shrtname = jQuery(this).attr("jsattr-full") ?? jQuery(this).attr("data-jsattr-full"); var text = jQuery(this).text(shrtname); } 
                else { var shrtname = jQuery(this).attr("jsattr-short") ?? jQuery(this).attr("data-jsattr-short"); var text = jQuery(this).text(shrtname); }
            });
            alternate = !alternate;
        });
    });
}
function JSwindowSize() {
    jQuery('table.jsStandings').each( function() {
        var conths = jQuery(this).parent().width();
        var thswdth = jQuery(this).find('th');
        var scrlths = 0;
        thswdth.each(function(){ scrlths+=jQuery(this).innerWidth(); });
        jQuery(this).find("span").each(function() {
            if (scrlths > conths) { var shrtname = jQuery(this).attr("jsattr-short") ?? jQuery(this).attr("data-jsattr-short"); var text = jQuery(this).text(shrtname).addClass("short"); return jsToggleTH(); }
        });
    });
    jQuery('#joomsport-container .page-content-js .tabs > ul.nav').each( function() {
        var jstabsul = jQuery(this).width();
        var jstabsli = jQuery(this).find('li');
        var jstabssum = 0;
        jstabsli.each(function(){ jstabssum+=jQuery(this).innerWidth(); });
        if (jstabssum > jstabsul) {jstabsli.addClass('jsmintab');}
    });
}

// Shortnames on standings
function JSStandingsName() {
    jQuery('#joomsport-container table.jsStandings').each( function() {
        var TblWdt = jQuery(this).parent().width();
        var SumTdCtrWdt = 0,
        TeamName = 0;
        var TeamImg = jQuery(this).find('td.jsNoWrap .img-thumbnail').outerWidth(true);
        var TeamBlockPad = jQuery(this).find('td.jsalignleft').innerWidth() - jQuery(this).find('td.jsalignleft').width();

        jQuery.type(TeamImg) === "null" ? TeamImg = 0 : TeamImg;
        jQuery(this).find('th.jsalcenter').each( function() {
            SumTdCtrWdt+=jQuery(this).innerWidth();
        });
        TeamName = TblWdt - SumTdCtrWdt - TeamBlockPad - TeamImg;
        jQuery(this).find('td.jsalignleft > a:last-child').css({'max-width':+TeamName+'px'});
    });
}
jQuery(window).on('load',function(){
    JSwindowSize();
    JSStandingsName();
    jQuery( window ).resize(function() {
        JSStandingsName();
    });

    jQuery('.ui-dialog-buttonset > button').each(function(){
        if(jQuery(this).text() == 'Cancel') {
            jQuery(this).addClass('d-none');
        }
    });
});



function jsCutRosterNames(){
    jQuery('.PlayerCardFIO > .js_div_particName').each(function(){
        var jsOuterHeight = jQuery(this).outerHeight(),
        jsLineHeight = parseInt(jQuery(this).css('line-height')),
        jsLinesInDiv = (jsOuterHeight/jsLineHeight).toFixed(),
        jsPlayerName = jQuery(this).text();

        if (jsLinesInDiv > 2) {
            jQuery(this).text(jsPlayerName.slice(0, 20) + '...');
        }
    });
}

function jsTabsSimulation(tabelement, tabcontent){
    jQuery(tabelement).on("click", function(){
        var id = jQuery(this).attr('data-tab'),
        content = jQuery(tabcontent+'[data-tab="'+ id +'"]');

        jQuery(tabelement+'.jsactive').removeClass('jsactive');
        jQuery(this).addClass('jsactive');

        jQuery(tabcontent+'.jsactive').removeClass('jsactive');
        content.addClass('jsactive');
    });
}

jQuery(document).ready(function(){
    jQuery('#joomsport-container a[href="#stab_players"]').on("click", function(){
        setTimeout(jsCutRosterNames, 200);
    });

    jQuery(".jspBlockTitle").on("click", function(){
        if(jQuery(this).next().hasClass("jsHHide")){
            jQuery(this).next().removeClass("jsHHide");
        }else{
            jQuery(this).next().addClass("jsHHide");
        }

        if(jQuery(this).children('i').hasClass('fa-chevron-up')){
            jQuery(this).children('i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
        } else {
            jQuery(this).children('i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
        }
    });

    jsTabsSimulation('.jsMatchStatTeams .jsMatchTeam', '.jsSquadContent > div');
    jsTabsSimulation('.jsMatchStatTeams .jsMatchTeam', '.jsHHSeasonAnalytics .jspBlockSection > div');

    jQuery( ".jsHHMatchDiv .jstooltip" ).tooltip( "destroy" );
});

function jspTabs(t, i){
    var par = jQuery(t).closest(".centrikLDW");
    par.find("a").removeClass("jsTabActive");
    jQuery(t).addClass("jsTabActive");
    console.log(jQuery(t));
    par.find('.centrikLDWinnerContainer').hide();
    jQuery("#centrikLDWinnerContainer" + i).show();

    par.parent().find('.divLastMatches').hide();
    par.parent().find(".divLastMatches" + i).show();
}
function jspTabsMajor(t, i){
    var par = jQuery(t).closest(".centrikLDW");
    par.find("a").removeClass("jsTabActive");
    jQuery(t).addClass("jsTabActive");
    par.parent().find('.evTblforTabs').hide();
    jQuery(".evTbl" + i).show();
}

function jspDrowPie(circle, away, home){

    console.log(away);

    var degree = away >= 90? 0: 90 - away;
    jQuery("#"+circle).append('<div class="arc arcR1" style="transform: rotate(90deg) skewX('+degree+'deg);"></div>');
    jQuery('<style>#'+circle+' .arcR1:before{transform: skewX(-'+degree+'deg);}</style>').appendTo('head');

    if( away > 90){
        var degree = away >= 180 ? 0 : 180 - away;

        jQuery("#"+circle).append('<div class="arc arcR2" style="transform: rotate(180deg) skewX('+degree+'deg);"></div>');
        jQuery('<style>#'+circle+' .arcR2:before{transform: skewX(-'+degree+'deg);}</style>').appendTo('head');

        if( away > 180){
            var degree = away >= 270 ? 0 : 270 - away;

            jQuery("#"+circle).append('<div class="arc arcR3" style="transform: rotate(270deg) skewX('+degree+'deg);"></div>');
            jQuery('<style>#'+circle+' .arcR3:before{transform: skewX(-'+degree+'deg);}</style>').appendTo('head');
        }
        if( away > 270){
            var degree = away >= 360 ? 0 : 360 - away;

            jQuery("#"+circle).append('<div class="arc arcR4" style="transform: rotate(0deg) skewX('+degree+'deg);"></div>');
            jQuery('<style>#'+circle+' .arcR4:before{transform: skewX(-'+degree+'deg);}</style>').appendTo('head');
        }

    }



    var degree = home >= 90? 0: 90 - home;
    jQuery("#"+circle).append('<div class="arc2 arcL1" style="transform: rotate('+degree+'deg) skewX('+degree+'deg);"></div>');
    jQuery('<style>#'+circle+' .arcL1:before{transform: skewX(-'+degree+'deg);}</style>').appendTo('head');

    if( home > 90){
        var degree = home >= 180 ? 90 : home - 90;

        jQuery("#"+circle).append('<div class="arc2 arcL2" style="transform: rotate('+(360-degree)+'deg) skewX('+(90-degree)+'deg);"></div>');
        jQuery('<style>#'+circle+' .arcL2:before{transform: skewX(-'+(90-degree)+'deg);}</style>').appendTo('head');

        if( home > 180){
            var degree = home >= 270 ? 90 : home - 180;

            jQuery("#"+circle).append('<div class="arc2 arcL3" style="transform: rotate('+(270-degree)+'deg) skewX('+(90-degree)+'deg);"></div>');
            jQuery('<style>#'+circle+' .arcL3:before{transform: skewX(-'+(90-degree)+'deg);}</style>').appendTo('head');
        }
        if( home > 270){
            var degree = home >= 360 ? 90 : home - 270;

            jQuery("#"+circle).append('<div class="arc2 arcL4" style="transform: rotate('+(180-degree)+'deg) skewX('+(90-degree)+'deg);"></div>');
            jQuery('<style>#'+circle+' .arcL4:before{transform: skewX(-'+(90-degree)+'deg);}</style>').appendTo('head');
        }

    }
}
jQuery(document).ready(function(){
    let hash = location.hash.replace(/^#/, '');

    if (hash) {
        jQuery('#joomsport-container .nav-tabs a[href="#' + hash + '"]').tab('show');
        window.scrollTo(0, 0);
    }
    jQuery('#joomsport-container .nav-tabs a').on('shown.bs.tab', function (e) {
        window.location.hash = e.target.hash;
    });

    jQuery(document).ready(function() {
        jQuery('#jstable_plz').tablesorter();
    } );


});

jQuery(document).ready(function(){
    var sid = jQuery("#joomsport-container select[name='sid']").val();
    //console.log(sid);
    if(sid){
        var optgroup = jQuery('select[name="sid"] :selected').parent().attr('label');
        //console.log(optgroup);
        if(optgroup){
            var prSID = jQuery('select[name="sid"]').parent();
            var curtext = prSID.find(".select2-selection__rendered").text();
            prSID.find(".select2-selection__rendered").text(optgroup+' '+jQuery('select[name="sid"] :selected').text());
            prSID.find(".select2-selection__rendered").closest(".select2-container").css("width","auto");
        }

    }

    //season history dd
    jQuery("#history_sid").on("change", function(){
        window.location.href = jQuery(this).val();
    })

});
