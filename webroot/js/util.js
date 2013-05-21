function iconsel(el,folder){
	var img = $(el).options[$(el).selectedIndex].value;
	if( img == '0' ){
		img = "webroot/images/blank.png";
	} else {
		img = folder + "/" + img;
	}
	$('preview_'+el).src = img;
}



function addClassName(el, sClassName) {
	var s = el.className;
	var p = s.split(" ");
	var l = p.length;
	for (var i = 0; i < l; i++) {
		if (p[i] == sClassName)
			return;
	}
	p[p.length] = sClassName;
	el.className = p.join(" ");

}

function removeClassName(el, sClassName) {
	var s = el.className;
	var p = s.split(" ");
	var np = [];
	var l = p.length;
	var j = 0;
	for (var i = 0; i < l; i++) {
		if (p[i] != sClassName)
			np[j++] = p[i];
	}
	el.className = np.join(" ");
}




/*
	Unobtrusive Dynamic Select Boxes
	http://www.bobbyvandersluis.com/articles/unobtrusivedynamicselect.php
*/
function dynamicSelect(id1, id2) {
	var agt = navigator.userAgent.toLowerCase();
	var is_ie = ((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1));
	var is_mac = (agt.indexOf("mac") != -1);
	if (!(is_ie && is_mac) && document.getElementById && document.getElementsByTagName) {
		var sel1 = document.getElementById(id1);
		var sel2 = document.getElementById(id2);
		var clone = sel2.cloneNode(true);
		var clonedOptions = clone.getElementsByTagName("option");
		refreshDynamicSelectOptions(sel1, sel2, clonedOptions);
		sel1.onchange = function(){ refreshDynamicSelectOptions(sel1, sel2, clonedOptions); };
	}
}
function refreshDynamicSelectOptions(sel1, sel2, clonedOptions) {
	while (sel2.options.length){ sel2.remove(0); }
	var pattern1 = /( |^)(select)( |$)/;
	var pattern2 = new RegExp("( |^)(" + sel1.options[sel1.selectedIndex].value + ")( |$)");
	for (var i = 0; i < clonedOptions.length; i++) {
		if (clonedOptions[i].className.match(pattern1) || clonedOptions[i].className.match(pattern2)) {
			sel2.appendChild(clonedOptions[i].cloneNode(true));
		}
	}
}
