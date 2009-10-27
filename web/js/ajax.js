function crbsajax(url, el){
	$.ajax({
		url: url,
		success: function(r){
			$('#ajaxload').hide();
			$('#'+el).html(r);
		},
		beforeSend: function(){$('#ajaxload').show();},
		error: function(){
			$('#ajaxload').hide();
			alert('An error occured while fetching remote data.');
		}
	});
}