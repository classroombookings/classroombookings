<script type="text/javascript">
_jsQ.push(function(){
	
	$("label.tristate").cbtristate();

	// Toggle all
	$("h6.toggle").css("cursor", "pointer").click(function(){
		$(this).closest("div").next("div.columns").find("label.tristate img").trigger("click");
		return false;
	});
	
});
</script>