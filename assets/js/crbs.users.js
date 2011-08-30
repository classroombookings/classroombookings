(function($){
	
	/*function table_filter (phrase, _id){
		var words = phrase.toLowerCase().split(" ");
		var table = document.getElementById(_id);
		var ele;
		for (var r = 1; r < table.rows.length; r++){
			ele = table.rows[r].innerHTML.replace(/<[^>]+>/g,"");
			var displayStyle = 'none';
			for (var i = 0; i < words.length; i++) {
				if (ele.toLowerCase().indexOf(words[i])>=0){
					displayStyle = '';
				} else {
					displayStyle = 'none';
					break;
				}
			}
			table.rows[r].style.display = displayStyle;
		}
	}*/
	function table_filter(phrase, id) {
		var words = phrase.toLowerCase().split(" ");
		var el;
		// Only get visible rows if phrase isn't empty
		var rows = $("table#" + id + " tbody tr");
		rows.each(function(){
			var row = $(this);
			el = row.html().replace(/<[^>]+>/g,"");
			row.hide();
			for (var i=0; i<words.length; i++){
				var m = (el.toLowerCase().indexOf(words[i]) > 0);
				if (m) {
					row.show();
				} else {
					row.hide();
					break;
				}
			}
		});
	}
	
	
	_jsQ.push(function(){
		
		$("input#search").keyup(function(){
			window.clearTimeout(window._search);
			var val = $("input#search").val();
			if (val.length > 0){
				window._search = window.setTimeout(function(){ table_filter(val, "users"); }, 250);
			} else {
				$("table#users tbody tr").show();
				$("select#group").trigger("change");
			}
		}).focus();
		
		$("select#group").change(function(){
			var group_id = $("select#group").val();
			if (group_id == -1) {
				$("table#users tbody tr").show();
			} else {
				$("table#users tbody tr:not(.groupid-" + group_id + ")").hide();
				$("table#users tbody tr.groupid-" + group_id).show();
			}
		}).trigger("change");
		
		$("table#users").delegate("tr", "click", function(e){
			// Don't do default action on link click
			if (e.target.tagName != "A") {
				var a = $(this).find("a[rel=edit]");
				window.location.href = a.attr("href");
			}
		});

	});
	

})(jQuery);