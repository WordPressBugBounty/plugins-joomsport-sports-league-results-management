var modJsLiveMatchesTimer;

function modJsLiveMatchesTimerStart() {
    modJsLiveMatchesTimer = setInterval(updLiveMatchScore, 10000);
}

function modJsLiveMatchesTimerStop() {
    clearInterval(modJsLiveMatchesTimer);
}

function updLiveMatchScore() {
    const items = new Array();

    jQuery(".fa-heart-o").each(function(){
        const itemID = jQuery(this).attr("data-id");

        items.push(itemID);
    });

    jQuery(".fa-heart").each(function(){
        const itemID = jQuery(this).attr("data-id");

        items.push(itemID);
    });

    const data = {
        'action': 'joomsport_liveshrtc_reload',
        'matches': items
    };

    jQuery.get(ajaxurl, data, function(response) {
<<<<<<< HEAD
        var res = JSON.parse(response);
        if(res){
            for(var key in res){
                jQuery("#modJsUpdScore"+key).html(res[key]);
                //console.log(res[key]);
=======
        const res = JSON.parse(response);

        if(res){
            for(var key in res){
                jQuery(`.modJsUpdScore${key}`).html(res[key]);

                console.log(res[key]);
>>>>>>> e5959cf01954888403473013feb2d27a1dc861a1
            }
        }
    });
}

function reCheckFavourites(){
    let favMatches = JSON.parse(localStorage.getItem("favMatches"));

    if(!Array.isArray(favMatches)){
        favMatches = new Array();
    }

    jQuery(".fa-heart-o").each(function(){
        const itemID = jQuery(this).attr("data-id");
        const index = favMatches.indexOf(itemID);

        if (index > -1) {
            jQuery(this).removeClass("fa-heart-o").addClass("fa-heart");
        }
    });

    jQuery(".modJsFavMatchCounter").html(parseInt(favMatches.length));
}

function chngFilterLiveMatches(select){
    const val = jQuery(select).val();
    const $widgetContainer = jQuery(select).closest('.modJSLiveMatches');

    $widgetContainer.find("#modJSLiveMatchesPrev").prop('disabled', true);
    $widgetContainer.find("#modJSLiveMatchesNext").prop('disabled', true);
    $widgetContainer.find("#modJSLiveMatchesContainer").fadeOut(300);

    const played = $widgetContainer.find("#modJSLiveMatchesFiltersSelect").val();

    modJsLiveMatchesTimerStop();

    const data = {
        'action': 'joomsport_liveshrtc_reload_matches',
        'jdate': val,
        'played': played,
<<<<<<< HEAD
        'emblems': jQuery("#show_emblems").val(),
        'sport': jQuery("#show_sport").val(),
=======
        'emblems': $widgetContainer.find("#show_emblems").val(),
        'sport': $widgetContainer.find("#show_sport").val(),
>>>>>>> e5959cf01954888403473013feb2d27a1dc861a1
        'linked': jQuery("#is_linked").val(),
    };

    jQuery.post(ajaxurl, data, function(response) {
        $widgetContainer.find("#modJSLiveMatchesContainer").html(response);
        $widgetContainer.find("#modJSLiveMatchesContainer").fadeIn(300);
        $widgetContainer.find("#modJSLiveMatchesPrev").prop('disabled', false);
        $widgetContainer.find("#modJSLiveMatchesNext").prop('disabled', false);

        reCheckFavourites();
        modJsLiveMatchesTimerStart();
    });

    $widgetContainer.find('.modJSLiveMatchesTabUL > li').removeClass("activeTab");
    $widgetContainer.find("#modJSLiveMatchesTabAll").addClass("activeTab");
}

jQuery(document).ready(function() {
    function filterDate($filterDate, dateType) {
        const curDate = $filterDate.val();
        const dateObj = new Date(curDate.substr(0,4), curDate.substr(5,2)-1, curDate.substr(8,2));

<<<<<<< HEAD
    jQuery("#modJSLiveMatchesPrev").on("click", function () {
        curDate = jQuery("#mod_filter_date").val();
        dateObj = new Date(curDate.substr(0,4), curDate.substr(5,2)-1, curDate.substr(8,2));
        //console.log(curDate.substr(0,4), curDate.substr(5,2), curDate.substr(8,2));
        dateObj.setDate(dateObj.getDate()-1);
        month = dateObj.getMonth()+1;
        if(month < 10){
            month = "0" + month;
        }
        day = dateObj.getDate();
        if(day < 10){
            day = "0" + day;
        }

        console.log(dateObj.getFullYear()  + "-" + month + "-" + day);
        var datestring = dateObj.getFullYear()  + "-" + month + "-" + day;
        jQuery("#mod_filter_date").val(datestring);
        jQuery("#mod_filter_date").trigger("change");
    });
    jQuery("#modJSLiveMatchesNext").on("click", function () {
        curDate = jQuery("#mod_filter_date").val();
        dateObj = new Date(curDate.substr(0,4), curDate.substr(5,2)-1, curDate.substr(8,2));
        //console.log(curDate.substr(0,4), curDate.substr(5,2), curDate.substr(8,2));
        dateObj.setDate(dateObj.getDate()+1);
        month = dateObj.getMonth()+1;
=======
        dateObj.setDate(dateType === 'prev' ? dateObj.getDate() - 1 : dateObj.getDate() + 1);

        let month = dateObj.getMonth() + 1;
        let day = dateObj.getDate();

>>>>>>> e5959cf01954888403473013feb2d27a1dc861a1
        if(month < 10){
            month = `0${month}`;
        }

        if(day < 10){
            day = `0${day}`;
        }

<<<<<<< HEAD
        //console.log(dateObj.getFullYear()  + "-" + month + "-" + day);
        var datestring = dateObj.getFullYear()  + "-" + month + "-" + day;
        jQuery("#mod_filter_date").val(datestring);
        jQuery("#mod_filter_date").trigger("change");
    });

    jQuery("#modJSLiveMatchesFiltersSelect").on("change", function () {
        jQuery("#mod_filter_date").trigger("change");
    });
=======
        const fullDateString = dateObj.getFullYear()  + "-" + month + "-" + day;
>>>>>>> e5959cf01954888403473013feb2d27a1dc861a1

        $filterDate.val(fullDateString);
        $filterDate.trigger("change");
    }

    function modFavChangeItemState(itemID, state) {
        let favMatches = JSON.parse(localStorage.getItem("favMatches"));

        if(!Array.isArray(favMatches)){
            favMatches = new Array();
        }

        const index = favMatches.indexOf(itemID);

        if(state === 'add' && index == -1) {
            favMatches.push(itemID);
        } else if(state === 'remove' && index > -1) {
            favMatches.splice(index, 1);
        }
<<<<<<< HEAD
        //console.log(favMatches);
=======

        console.log(favMatches);

>>>>>>> e5959cf01954888403473013feb2d27a1dc861a1
        localStorage.setItem("favMatches", JSON.stringify(favMatches));

        jQuery(".modJsFavMatchCounter").html(parseInt(favMatches.length));
    }

    jQuery('.modJSLiveMatches').each(function() {
        const $prevBtn = jQuery(this).find("#modJSLiveMatchesPrev");
        const $nextBtn = jQuery(this).find("#modJSLiveMatchesNext");
        const $filterDate = jQuery(this).find(".mod_filter_date");
        const $filterStatus = jQuery(this).find("#modJSLiveMatchesFiltersSelect");
        const $tabs = jQuery(this).find('.modJSLiveMatchesTabUL > li');
        const $tabAll = jQuery(this).find("#modJSLiveMatchesTabAll");
        const $tabFav = jQuery(this).find("#modJSLiveMatchesTabFav");
        const $matchContainer = jQuery(this).find("#modJSLiveMatchesContainer");

        $prevBtn.on("click", function () {
            filterDate($filterDate, 'prev');
        });

<<<<<<< HEAD
        if (index > -1) { //if found
            favMatches.splice(index, 1);
        }
        //console.log(favMatches);
        localStorage.setItem("favMatches", JSON.stringify(favMatches));
        jQuery("#modJsFavMatchCounter").html(parseInt(favMatches.length));
=======
        $nextBtn.on("click", function () {
            filterDate($filterDate, 'next');
        });
>>>>>>> e5959cf01954888403473013feb2d27a1dc861a1

        $filterStatus.on("change", function () {
            console.log($filterDate);

            $filterDate.trigger("change");
        });

        $tabAll.on("click", function(){
            $filterDate.trigger("change");
        });

        $tabFav.on("click", function(){
            let favMatches = JSON.parse(localStorage.getItem("favMatches"));

            if(!Array.isArray(favMatches)){
                favMatches = new Array();
            }

            modJsLiveMatchesTimerStop();

            const data = {
                'action': 'joomsport_liveshrtc_favreload',
                'matches': favMatches
            };

            jQuery.post(ajaxurl, data, function(response) {
                $matchContainer.html(response);
                $matchContainer.fadeIn(300);
                $prevBtn.prop('disabled', false);
                $nextBtn.prop('disabled', false);

                reCheckFavourites();
                modJsLiveMatchesTimerStart();
            });
        });

        $tabs.on("click", function(){
            $tabs.removeClass("activeTab");
            jQuery(this).addClass('activeTab');
        });
    });

    jQuery("body").on("click", ".fa-heart-o", function () {
        const itemID = jQuery(this).attr("data-id");

        if(itemID){
            jQuery(`[data-id="${itemID}"].fa-heart-o`).removeClass("fa-heart-o").addClass("fa-heart");

            modFavChangeItemState(itemID, 'add');
        }
    });

    jQuery("body").on("click", ".fa-heart", function () {
        const itemID = jQuery(this).attr("data-id");

        if(itemID){
            jQuery(`[data-id="${itemID}"].fa-heart`).removeClass("fa-heart").addClass("fa-heart-o");

            modFavChangeItemState(itemID, 'remove');
        }
    });

    reCheckFavourites();
    modJsLiveMatchesTimerStart();
});