function foo(evt)
{
// 	debug(getCssRuleAttrib(".rulespacer", "width"));
// 	debug(getCssRuleAttrib(".ruleshadow .br", "height"));
// 	debug(getCssRuleAttrib(".smallrule", "height"));
// 	if(setCssRuleAttrib(".smallrule", "height", "106px"))
// 		debug("setting '.smallrule' height succeeded");
// 	else
// 		debug("setting '.smallrule' height failed");
	
	var inputs = document.getElementsByTagName("INPUT");
	for(var i = 0; i < inputs.length; i++) {
		debug("input[" + i + "]: type='" + inputs[i].type + "', className='" + inputs[i].className + "'" + "', id='" + inputs[i].id + "'");
	}
	debug("----------------------------------------------------------");
	var obj = getEvtElement(evt)
	for(e in obj)
		debug(e + " = " + obj[e]);
// 	debug(evt.target);
}
function init()
{
	// add a loop to apply 'restore()' to the plus buttons (if any)
	// and to apply 'minimize()' to the minus buttons (if any)
	var inputs = document.getElementsByTagName("INPUT");
// 	for(i in inputs[0]) debug(i);
	for(var i = 0; i < inputs.length; i++) {
// 		debug(inputs[i].type);
		if((inputs[i].type == "button" || inputs[i].type == "submit") && inputs[i].className == "minimize")
			inputs[i].onmousedown = minimize;
		else if((inputs[i].type == "button" || inputs[i].type == "submit") && inputs[i].className == "restore")
			inputs[i].onmousedown = restore;
	}
}
function minimize(evt, elementId, curval)
{
	var minheight = 22.00; // (pixels) this is static for now. I want to make it dynamic later
	var step = 0.25;
	var e; // event element (should be the plus button)
	if(elementId == undefined) e = getEvtElement(evt);
	else if(elementId) e = document.getElementById(elementId);
	else { alert("exiting!"); return; }
	var re = e.parentNode.parentNode; // rule element
	if(curval == undefined) { // do pre-restore stuff here
		curval = re.offsetHeight; // get current height
		e.className = "restore";
		e.onmousedown = function() { return false };
// 		e.parentNode.style.height = e.parentNode.offsetHeight + "px"; // causes large rule headers to gain height on every "minimize"
		var rbe = getRBElement(re);
		if(rbe != undefined)
			rbe.className = "rulebody nodisplay";
	}
	curval = parseFloat(curval);
	curval = curval - ((curval - minheight) * step);
	if(curval > (minheight + 1.00)){
		re.style.height = Math.round(curval).toString() + "px";
		setTimeout("minimize(null, '" + e.id + "', '" + curval + "')", 20);
	} else { // do post-restore stuff here
		e.onmousedown = restore;
		re.style.height = "auto";
		re.removeAttribute("style");
	}
}
function restore(evt, elementId, curval)
{
	// IE7 has a issue that I think can be addressed by making the <p> tag a specific size
	var maxheight = 104; // (pixels) this is static for now. I want to make it dynamic later
	var step = 0.25;
	var e; // event element (should be the plus button)
	if(elementId == undefined) e = getEvtElement(evt);
	else if(elementId) e = document.getElementById(elementId);
	else { alert("exiting!"); return; }
	var re = e.parentNode.parentNode; // rule element
	if(curval == undefined) { // do pre-restore stuff here
		curval = re.offsetHeight; // get current height
		e.className = "minimize";
		e.onmousedown = function() { return false };
		var rbe = getRBElement(re);
		if(rbe != undefined)
			rbe.className = "rulebody";
	}
	curval = parseFloat(curval);
	if(curval < maxheight - 1){
		curval = ((maxheight - curval) * step) + curval;
		re.style.height = Math.round(curval).toString() + "px";
		setTimeout("restore(null, '" + e.id + "', '" + curval + "')", 1);
	} else { // do post-restore stuff here
		e.onmousedown = minimize;
		re.style.height = maxheight + "px";
	}
}
function getRBElement(ruleElement) // get rule body element
{
	var rbe;
	for(var n = 0; n < ruleElement.childNodes.length; n++) { // loop through rule elements
		if(ruleElement.childNodes[n].className && ruleElement.childNodes[n].className.indexOf("rulebody") != -1){ // look for rulebody
			rbe = ruleElement.childNodes[n]; // we found it
			break; // break the loop
		} // end if
	} // end for
	return rbe;
}
function getEvtElement(evt)
{
	var element;
	try {
		element = evt.target;
	} catch (e) {
		try {
			element = event.srcElement;
		} catch (e) {
			alert("Could not determine your browsers event object!");
		}
	}
	return element;
}
// syntax for this function: getCssRuleAttribute("#foo", "width")
function getCssRuleAttrib(rulename, attrib)
{
	var rules, rn, iRule = -1, iSheet = -1;
	for(var sheet = 0; sheet < document.styleSheets.length; sheet++) {
		rules = getRules(sheet);
		for(var rule = 0; rule < rules.length; rule++) {
			rn = rules[rule].selectorText.split(',');
			for(var i = 0; i < rn.length; i++) {
				if(rn[i].trim() == rulename && rules[rule].style[attrib]) {
					iRule = rule;
					break;
				}
			}
			if(iRule >= 0) break;
		}
		if(iRule >= 0) break;
	}
	var style;
	if(iRule >= 0) {
		style = rules[iRule].style[attrib];
		style = (style == undefined ? 0 : style);
	}
	return style;
}
// syntax for this function: setCssRuleAttribute("#foo", "width", "200px")
function setCssRuleAttrib(rulename, attrib, newValue)
{
	var rules, rn, iRule = -1, iSheet = -1;
	for(var sheet = 0; sheet < document.styleSheets.length; sheet++) {
		rules = getRules(sheet);
		for(var rule = 0; rule < rules.length; rule++) {
			if(rules[rule].selectorText == rulename) {
				iRule = rule;
				iSheet = sheet;
				break;
			}
		}
		if(iRule >= 0) break;
	}
	if(iRule >= 0) {
		rules[iRule].style[attrib] = newValue;
		return true;
	} else
		return false;
}
function debug(dbgtxt)
{
	var e = document.getElementById("debug");
	e.setAttribute("style", "display:normal");
	e.innerHTML += dbgtxt + "\n";
}
function getRules(sheetNum) {
	if(sheetNum == undefined) sheetNum = 0;
	var cssRules = new Array();
	if(document.styleSheets[sheetNum] == undefined) return;
	if (document.styleSheets[sheetNum].cssRules) {
		cssRules = document.styleSheets[sheetNum].cssRules;
	} else if (document.styleSheets[sheetNum].rules) {
		cssRules = document.styleSheets[sheetNum].rules;
	}
	return cssRules;
}
String.prototype.pad = function (padLen, padChar) {
	var string = this;
	var append = new String ();
	
	padLen = isNaN (padLen) ? 0 : padLen - string.length;
	padChar = typeof (padChar) == 'string' ? padChar : ' ';
	
	while ((padLen -= padChar.length) > 0)
		append += padChar;
	append += padChar.substr (0, padLen + padChar.length);
	
	return append.concat (string);
}
String.prototype.trim = function() {
// 	return this.replace(/^[\s\t]+|[\s\t]+$/g,"");
	return this.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
}
function echocss()
{
	// debug() is a function that just writes or echos to the html document
	
	var style = "";
	var rules = getRules(sheet);
	for(var sheet = 0; sheet < document.styleSheets.length; sheet++){
		rules = getRules(sheet);
		for(var rule = 0; rule < rules.length; rule++) {
			
			debug(rules[rule].selectorText + " { "); // rule name
			for(var i = 0; i < rules[rule].style.length; i++) {
				style = rules[rule].style; // style for this rule
				if(style[style[i]] == undefined)
					debug(style[i] + ": 0px; ");
				else
					debug(style[i] + ": " + style[style[i]] + "; ");
			}
			debug("}\n");
			
		}
		debug("----------------------------------\n");
	}
}
