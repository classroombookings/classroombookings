var sel_year;
var sel_month;
var sel_date;


/* XHR function to load a room timetable into the container */

// Load room timetable via AJAX and set classes on LIs
function roomajax(url, id){
	crbsajax(url, 'tt', function(){
		$('#sb-roomlist tr').removeClass("current");
		$('table#sb-roomlist tr.room-' + id).addClass("current");
		navheaderlinks();
	});
}


// Update links after new HTML has been inserted into the DOM via XHR
function updatelinks(){
	navheaderlinks();
	calnavlinks();	
}


// Set up calendar month navigation links
// In a function as it's called during a callback
function calnavlinks(){
	
	// Attach XHR event to calendar month selection arrows
	$('a[rel*=calmonth]').bind("click", function(e){
		e.preventDefault();
		crbsajax($(e.currentTarget).attr("href"), 'cal', calnavlinks);
	});
	// Attach XHR event to calendar dates
	$('a[rel*=caldate]').bind("click", function(e){
		e.preventDefault();
		crbsajax($(e.currentTarget).attr("href"), 'tt', navheaderlinks);
		$('a[rel*=caldate]').removeClass("current");
		$(e.currentTarget).addClass("current");
		// If room view, highlight whole week
		if(tt_view == 'room'){
			var td = $(e.currentTarget).parent().get(0);
			var tr = $(td).parent().get(0);
			$(tr).find("a").addClass("current");
		}
	});

		
	// Add hover event to apply classes to whole week if room view is configured
	if(tt_view == 'room'){
		$('a[rel*=caldate]').mouseover(function(e){
			var td = $(e.target).parent().get(0);
			var tr = $(td).parent().get(0);
			$(tr).find("a").addClass("hover");
		});
		$('a[rel*=caldate]').mouseout(function(e){
			$('a[rel*=caldate]').removeClass("hover");
		});
	}
	
}




// Set up javascript for clicking on the links in the bookings area navigating header
function navheaderlinks(){
	
	$('a[rel*=navheader]').bind("click", function(e){
		
		e.preventDefault();
		
		// Send the XHR to load the timetable in the main tt container
		crbsajax($(e.currentTarget).attr("href"), 'tt', updatelinks);
		
		// Get date from ID of link
		seldate = $(e.currentTarget).attr("id");
		seldate = seldate.split('-');
		
		update_cal = false;
		if(seldate[1] != sel_month){
			//console.log('seldate1 is ' + seldate[1]);
			//console.log('sel_date is ' + sel_month);
			// Need to do XHR call later
			update_cal = true;
		}
		
		// Update the Javascript vars to what is chosen
		sel_year = seldate[0];
		sel_month = seldate[1];
		sel_date = seldate[2];
		
		// Convert the 2-digit day number into single (how it's echo'd in the cal)
		daynum = sel_date.replace(/^0/, '');
		
		// Load the calendar month in sidebar
		if(update_cal == true){
			// Months are different, load calendar for chosen month
			url = siteurl + 'bookings/calendar/' + sel_year + '/' + sel_month;
			crbsajax(url, 'cal', function(){
				// On callback, 1) highlight day, and 2) ajaxify the links
				highlight_day(daynum);
				calnavlinks();
			});
		} else {
			// Just highlight the day
			highlight_day(daynum);
		}
		
	});
	
}




/**
 * Highlight a day in the calendar
 */
function highlight_day(daynum, weekstart){
	
	$('a[rel*=caldate]').removeClass("current");
	$('a#cal_' + daynum).addClass("current");
	
	if(tt_view == 'room'){
		var td = $('a#cal_' + daynum).parent().get(0);
		var tr = $(td).parent().get(0);
		$(tr).find("a").addClass("current");
	}
	
}



// When loaded (via LAB.js)

// Room/Date tabs in sidebar
$("#tabs").tabs({ cookie:{ expires: 7, name: 'tab.bookings' } });
$("div#tabs").show();


// Room information box
$('a[rel*=boxy]').bind("click", function(e){
	e.preventDefault();
	var url = $(e.currenTarget).attr("href");
	Boxy.load($(this).attr("href"), {cache: true, title: $(this).attr("original-title")});
});


/* XHR calls for loading rooms */

// Room element clicking
$('table#sb-roomlist tr[rel*=room] td').bind("click", function(e){
	if(e.target.className != "i"){
		var a = $(e.currentTarget).parent().find('a[rel*=room]');
		var url = a.attr("href");
		var id = a.attr("id");
		id = id.split('-');
		id = id[1];
		console.log(url);
		console.log(id);
		roomajax(url, id);
	}
});


// Hover classes for table rows
$('table#sb-roomlist tr[rel*=room] td').bind("mouseover", function(e){
	$(e.currentTarget).parent().addClass("hover");
});
$('table#sb-roomlist tr[rel*=room] td').bind("mouseout", function(e){
	$(e.currentTarget).parent().removeClass("hover");
});


// Room A element click event
$('a[rel*=room]').bind("click", function(e){
	e.preventDefault();
	roomajax($(e.currentTarget).attr("href"), $(e.currentTarget).parent());
});

// Set up calendar navigation links (months & dates)
calnavlinks();

// Navigation header links
navheaderlinks();