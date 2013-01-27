Q.push(function() {
	
	$("#ldap_test_username, #ldap_test_password").on("keypress", function(e) {
		if (e.keyCode == 13) {
			e.preventDefault();
			$("#test_ldap").trigger("click");
		}
	});
	
	$("#test_ldap").on("click", function(e) {
		
		var data = {
			auth_ldap_host: $("input[name='auth_ldap_host']").val(),
			auth_ldap_port: $("input[name='auth_ldap_port']").val(),
			auth_ldap_base: $("textarea[name='auth_ldap_base']").val(),
			auth_ldap_filter: $("textarea[name='auth_ldap_filter']").val(),
			username: $("input[name='ldap_test_username']").val(),
			password: $("input[name='ldap_test_password']").val()
		};
		
		$("#ldap_test_response").removeClass("hidden");
		$("#ldap_test_response .response-text").text("Please wait...");
		
		$.ajax({
			type: "post",
			url: CRBS.base_url + "authentication/test_ldap",
			data: data,
			success: function(res) {
				if (res.status === "err") {
					$("#ldap_test_response .response-text").text("Error: " + res.reason);
				} else {
					$("#ldap_test_response .response-text").text("Success!");
				}
			},
			error: function(res) {
				alert('An error occurred.');
			}
		});
		
	});
	
});