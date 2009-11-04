function crbsajax(url, el, f){
	$.ajax({
		url: url,
		success: function(r){
			$('#ajaxload').hide();
			$('#'+el).html(r);
			if(f){f();}
		},
		beforeSend: function(){
			$('#alert').html("");
			$('#ajaxload').show();
		},
		error: function(){
			$('#ajaxload').hide();
			alert('An error occured while fetching remote data.');
		}
	});
}