var role_entity_mgr = (function($) {
	
	
	var init = function() {
		
		// Attach autocomplete lookup to inputs
		$(".autocomplete").autocomplete({
			serviceUrl: CRBS.site_url + "roles/entity_search",
			onSelect: function(suggestion) {
				// Get role ID from the data attribute of the input
				suggestion.data["r_id"] = $(this).data("r_id");
				add_item(suggestion.data);
			}
		});
		
		// Cick event for removing an entity from a role
		$(".role-row").on("click", ".role-entity-remove a", function(e) {
			e.preventDefault();
			
			// Get needed info
			var data = $(this).closest("li").data();
			console.log(data);
			data["r_id"] = $(this).closest("ul").data("r_id");
			
			remove_item(data, $(this).closest("li"));
		});
		
	}
	
	
	var add_item = function(data) {
		
		// Grab handle to input element used 
		var $input = $("input.autocomplete[data-r_id='" + data.r_id + "']");
		
		// Make ajax request
		$.ajax({
			type: "post",
			url: CRBS.site_url + "roles/assign",
			data: data,
			success: function(res) {
				if (res.status === "success") {
					var $ul = $("ul[data-r_id='" + data.r_id + "']");
					
					// Check: is the entity already in the list?
					var exists = $ul.children("li[data-e_id='" + data.e_id + "'][data-e_type='" + data.e_type + "']");
					
					// Only add the new entry if it's not already there
					if (exists.length === 0) {
						var new_entity_html = ich.role_entity(data);
						new_entity_html.appendTo($ul);
					}
					
					// Clear input box
					$input.val("");
					alerts.success(data.e_name + " has been assigned.");
				} else {
					alert("Error: " + res.reason);
				}
			},
			error: function(r) {
				alert("An error occurred.");
			}
		});
		
	}
	
	
	var remove_item = function(data, $li) {
		
		// Make ajax request
		$.ajax({
			type: "post",
			url: CRBS.site_url + "roles/unassign",
			data: data,
			success: function(r) {
				$li.remove();
				alerts.success("The role has been unassigned.");
			},
			error: function(r) {
				alert("An error occurred.");
			}
		});
		
	}
	
	
	return {
		init: init
	}
	
	
})(jQuery);

Q.push(role_entity_mgr.init);

Q.push(function() {
	
	$(".sortable").sortable({
		handle: ".handle"
	}).bind("sortupdate", function() {
		var $roles = $(".role-list > .role-row");
		var order = {};
		var i = 0;
		$roles.each(function(e) {
			order[$(this).data("r_id")] = i;
			i++;
		});
		
		
		$.ajax({
			url: CRBS.site_url + "roles/set_order",
			type: "post",
			data: {
				order: order,
			},
			success: function() {
				alerts.success("New role order has been saved.");
			}
		});
	});
	
});