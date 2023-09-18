<script type="text/javascript">
window.doorbellOptions = {
	"id": "176",
	"appKey": "qrCT2XcCRmW3KyRq13HnT6QLvQt0U4TtP9YAIOTnmRu3mLEM2mJCEF8YbQMjHYqh",
	"hideButton": true,
	"properties": <?= json_encode([
		'username' => $this->userauth->user->username,
	]) ?>,
	onLoad: function() {
		document.getElementById('feedback_link').removeAttribute('style');
		htmx.on('#feedback_link', 'click', function(evt) {
			evt.preventDefault();
			doorbell.show();
		});
	},
};
(function(w, d, t) {
	var hasLoaded = false;
	function l() { if (hasLoaded) { return; } hasLoaded = true; window.doorbellOptions.windowLoaded = true; var g = d.createElement(t);g.id = 'doorbellScript';g.type = 'text/javascript';g.async = true;g.src = 'https://embed.doorbell.io/button/'+window.doorbellOptions['id']+'?t='+(new Date().getTime());(d.getElementsByTagName('head')[0]||d.getElementsByTagName('body')[0]).appendChild(g); }
	if (w.attachEvent) { w.attachEvent('onload', l); } else if (w.addEventListener) { w.addEventListener('load', l, false); } else { l(); }
	if (d.readyState == 'complete') { l(); }
}(window, document, 'script'));
</script>
