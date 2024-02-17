var contextmenu;
var container;
var content;
var notifyPanel;

var leftFrame;
var middleFrame;
var rightFrame;
var largeFrame;

var umodal;

var navbar;

function Create(type, cl, parent, html = null) {
	var elem = document.createElement(type);
	if(cl != null) {
		elem.className = cl;
	}
	if(html != null) {
		elem.innerHTML = html;
	}
	if(parent != null) {
		parent.appendChild(elem);
	}
	return elem;
}

function ContextMenu(data, arr, x, y) {
	if(contextmenu != null && contextmenu.parentNode != null) {
		contextmenu.parentNode.removeChild(contextmenu);
	}
	contextmenu = Create("button", "context-menu", document.body);
	contextmenu.style.left = x;
	contextmenu.style.top = y;
	contextmenu.focus();
	contextmenu.onblur = function() {
		document.body.removeChild(contextmenu);
	}
	for(var i=0; i<arr.length; i++) {
		if(arr[i].action == null) {
				Create("hr", null, contextmenu);
		} else if(typeof(arr[i].action) == "function") {
			var lnk = Create("span", "context-item", contextmenu, arr[i].title);
			lnk.func = i;
			if(arr[i].icon != null) {
				lnk.style.backgroundImage = "url('/images/ui/new/"+arr[i].icon+".png')";
			}
			lnk.onclick = function(event) {
				//if(event.which != 3) return;
				arr[this.func].action(data);
				//contextmenu.parentNode.removeChild(contextmenu);
				contextmenu.blur();
			}
		} else if(typeof(arr[i].action) == "string") {
			Create("span", arr[i].action, contextmenu, arr[i].title);
		}
		
		/*var line = Create("div", "context-item", contextmenu, title);
		line.func = arr[title];
		line.onclick = function(event) {
			//if(event.which != 3) return;
			this.func();
			//contextmenu.parentNode.removeChild(contextmenu);
			contextmenu.blur();
		}*/
		//contextmenu.appendChild(line);
	}
}

function ContextItem(title, action = null, icon = null) {
	return {title: title, icon: icon, action: action};
}


window.oncontextmenu = function(event) {
	console.log(123);
	if(event.target != null && event.target.contextItems != null) {
		ContextMenu(event, event.target.contextItems, event.clientX, event.clientY+document.body.scrollTop);
		return false;
	} else if(event.target != null && event.target.rightClick != null) {
		event.target.rightClick(event);
		return false;
	}
}

function Container() {
    if(container != null && container.parentNode != null) {
        container.innerHTML = "";
    } else {
        container = Create("div", "container", document.body);
    }
}

function UiBox(parent, title, body, footer) {
    var box = Create("div", "box", parent);
    if(title != null) {
        box.utitle = Create("div", "header", box, title);
    }

    box.ubody = Create("div", "body", box, body);

    if(footer != null) {
        box.ufooter = Create("div", "footer", box, footer);
    }
    return box;
}

function UiRow(parent) {
    return Create("div", "row", parent);
}
function UiCol(parent, size) {
    return Create("div", "col-"+size, parent);
}

function UiQRow(parent, sizes) {
	var row = UiRow(parent);
	row.rows = [];
	for(var i = 0; i < sizes.length; i++) {
		row.rows.push(UiCol(row, sizes[i]));
	}
	return row;
}

function UiColBox(parent, size, title, body, footer) {
    var col = Create("div", "col-"+size, parent);
    return UiBox(col, title, body, footer);
}

function RemoveAllFrames()
{
    if(largeFrame != null && largeFrame.parentNode != null) { largeFrame.outerHTML = null; }
	if(leftFrame != null && leftFrame.parentNode != null) { leftFrame.outerHTML = null; }
	if(middleFrame != null && middleFrame.parentNode != null) { middleFrame.outerHTML = null; }
	if(rightFrame != null && rightFrame.parentNode != null) { rightFrame.outerHTML = null; }
}

function OneFrame(title = "Единственный фрейм") {
	RemoveAllFrames();
	
	largeFrame = Create("div", "frame large", container);
	largeFrame.frameTop = Create("div", "top", largeFrame, title);
	largeFrame.frameBody = Create("div", "body", largeFrame);
}

function TwoFrames(title1 = "Первый фрейм", title2 = "Второй фрейм") {
	RemoveAllFrames();
	
	leftFrame = Create("div", "frame left", container);
	leftFrame.frameTop = Create("div", "top", leftFrame, title1);
	leftFrame.frameBody = Create("div", "body", leftFrame);
	
	rightFrame = Create("div", "frame right", container);
	rightFrame.frameTop = Create("div", "top", rightFrame, title2);
	rightFrame.frameBody = Create("div", "body", rightFrame);
}

function TwoFrames2(title1 = "Первый фрейм", title2 = "Второй фрейм") {
	RemoveAllFrames();
	
	leftFrame = Create("div", "frame leftH", container);
	leftFrame.frameTop = Create("div", "top", leftFrame, title1);
	leftFrame.frameBody = Create("div", "body", leftFrame);
	
	rightFrame = Create("div", "frame rightH", container);
	rightFrame.frameTop = Create("div", "top", rightFrame, title2);
	rightFrame.frameBody = Create("div", "body", rightFrame);
}

function ThreeFrames(title1 = "Первый фрейм", title2 = "Второй фрейм", title3 = "Третий фрейм") {
	RemoveAllFrames();
	
	leftFrame = Create("div", "frame left mini", container);
	leftFrame.frameTop = Create("div", "top", leftFrame, title1);
	leftFrame.frameBody = Create("div", "body", leftFrame);
	
	middleFrame = Create("div", "frame middle", container);
	middleFrame.frameTop = Create("div", "top", middleFrame, title2);
	middleFrame.frameBody = Create("div", "body", middleFrame);
	
	rightFrame = Create("div", "frame right", container);
	rightFrame.frameTop = Create("div", "top", rightFrame, title3);
	rightFrame.frameBody = Create("div", "body", rightFrame);
}

function UiNavBar(elems, elems2) {
	navbar = Create("div", "navbar", document.body);
	Create("div", "navbar-bg", navbar);
	for(var key in elems) {
		var cat = Create("div", "nav-cat", navbar, '<div class="nav-title">'+key+'</div>');
		if(typeof(elems[key]) == "function") {
			cat.onclick = elems[key];
			continue;
		}
		var dropdown = Create("div", "nav-dropdown", cat);
		for(var subkey in elems[key]) {
			UiParseNavBarContent(dropdown, subkey, elems[key][subkey]);
			/*if(elems[key][subkey] == null) {
				Create("hr", null, dropdown);
			} else if(typeof(elems[key][subkey]) == "function") {
				var lnk = Create("span", "nav-item", dropdown, subkey);
				lnk.onclick = elems[key][subkey];
			} else if(typeof(elems[key][subkey]) == "string") {
				Create("span", elems[key][subkey], dropdown, subkey);
			}*/
		}
	}
	if(elems2 == null) {return;}
	var navright = Create("div", "nav-right", navbar);
	for(var key in elems2) {
		var cat = Create("div", "nav-cat", navright, '<div class="nav-title">'+key+'</div>');
		if(typeof(elems2[key]) == "function") {
			cat.onclick = elems2[key];
			continue;
		}
		var dropdown = Create("div", "nav-dropdown", cat);
		for(var subkey in elems2[key]) {
			UiParseNavBarContent(dropdown, subkey, elems2[key][subkey]);
			/*if(elems2[key][subkey] == null) {
				Create("hr", null, dropdown);
			}else if(typeof(elems2[key][subkey]) == "function") {
				var lnk = Create("span", "nav-item", dropdown, subkey);
				lnk.onclick = elems2[key][subkey];
			} else if(typeof(elems2[key][subkey]) == "string") {
				Create("span", elems2[key][subkey], dropdown, subkey);
			}*/
		}
	}
}

function UiParseNavBarContent(dropdown, key, value) {
	if(value == null) {
		Create("hr", null, dropdown);
	} else if(typeof(value) == "object") {
		var lnk = Create("span", "nav-item", dropdown, key);
		var sub = Create("div", "nav-subdropdown", lnk);
		for(var subkey in value) {
			UiParseNavBarContent(sub, subkey, value[subkey]);
		}
	} else if(typeof(value) == "function") {
		var lnk = Create("span", "nav-item", dropdown, key);
		lnk.onclick = value;
	} else if(typeof(value) == "string") {
		Create("span", value, dropdown, key);
	}
}

function UiFormGroup(parent, text = null) {
	if(text != null) { return Create("div", "form-group", parent, '<span class="form-title">'+text+'</div>'); }
	return Create("div", "form-group", parent);
}

function UiInputGroup(parent, title) {
    return Create("div", "input-group", UiFormGroup(parent, title));
}

function UiBtnToolbar(parent)
{
	return Create("div", "btn-toolbar", parent);
}

function UiProgressBar(parent, style, values, text) {
	var progr = Create("div", "progressbar "+style, parent, text);
	return progr;
}

function UiCheck(parent, start, onchange) {
	var check = Create("input", null, parent);
	check.setAttribute("type", "checkbox");
	check.checked = start;
	check.onchange = onchange;
	return check;
}

function UiCheckbox(parent, text, oncheck, onuncheck, start) {
	var check = Create("div", "checkbox", parent, text);
	check.checked = start;
	check.oncheck = oncheck;
	check.onuncheck = onuncheck;
	if(start) {check.className = "checkbox active";}
	check.setCheck = function(value){
		if(value) {
			this.oncheck();
			this.className = "checkbox active";
		} else {
			this.onuncheck();
			this.className = "checkbox";
		}
		this.checked = value;
	}
	check.onclick = function() {
		this.setCheck(!this.checked);
	}
	return check;
}

function UiInputDate(parent, value, oninput) {
	var innum = Create("input", "input-date", parent);
	innum.setAttribute("type", "date");
	innum.value = value;
	innum.oninput = oninput;
	return innum;
}

function UiInputNumeric(parent, value, step, oninput) {
	var innum = Create("input", "input-number", parent);
	innum.setAttribute("type", "number");
	innum.setAttribute("step", step);
	innum.value = value;
	//innum.onfocus = function() { this.select(); }
	innum.oninput = oninput;
	return innum;
}

function UiInputString(parent, value, oninput, disabled = null) {
	var instr = Create("input", "input-string", parent);
	instr.value = value;
	//instr.onfocus = function() { this.select(); }
    instr.disabled = disabled;
	instr.oninput = oninput;
	return instr;
}

function UiInputStringL(parent, /*height,*/ value, oninput) {
	var instr = Create("textarea", "input-string", parent);
	instr.style.height = "115";
	instr.value = value;
	//innum.onfocus = function() { this.select(); }
	instr.oninput = oninput;
	return instr;
}

function UiInputRange(parent, value, min, max, step, oninput) {
	var innum = Create("input", "input-range", parent);
	innum.setAttribute("type", "range");
	innum.setAttribute("step", step);
	innum.setAttribute("min", min);
	innum.setAttribute("max", max);
	innum.value = value;
	innum.oninput = function(event) {
		this.style.backgroundSize = (this.value/this.max*100)+"% 100%";
		if(oninput != null) {
			oninput(event);
		}
	};

	return innum;
}

function UiNavBox(parent, style) {
	var navBox = Create("div", "nav-box", parent);
	if(style != null) {
		navBox.classList.add(style);
	}
	navBox.tabLinks = Create("div", "nav-links", navBox);
	navBox.activeTab = null;
	navBox.tabs = {};
	navBox.navcontent = Create("div", "nav-content", navBox);
	navBox.add = function(title, node = null, active = false) {
		var tlink = Create("div", "nav-link", navBox.tabLinks, title);
		if(node != null) {
			tlink.node = node;
			
		} else {
			tlink.node = UiBox();
		}
		tlink.onclick = function() {
			if(this.parentNode.parentNode.activeTab == this) {
				return;
			}
			if(this.parentNode.parentNode.activeTab != null) {
				this.parentNode.parentNode.activeTab.classList.remove("active");
				if(this.parentNode.parentNode.activeTab.node != null) {
					this.parentNode.parentNode.navcontent.removeChild(this.parentNode.parentNode.activeTab.node);
				}
			}
			this.classList.add("active");
			if(this.node != null) {
				this.parentNode.parentNode.navcontent.appendChild(this.node);
			}
			this.parentNode.parentNode.activeTab = this;
		}
		if(active) {
			tlink.click();
		}
		navBox.tabs[title] = tlink;
		return tlink;
	}
	return navBox;
}

function UiSelect(parent, args, def, onchange) {
	var selectbox = Create("select", "input-select", parent);
	for(var arg in args) {
		var option = Create("option", null, selectbox, args[arg]);
		option.value = args[arg];
	}
	selectbox.value = def;
	selectbox.onchange = onchange;
	return selectbox;
}

function UiCombobox(parent, text, func, args) {
	var combobox = Create("button", "combo-box", parent, );
	combobox.selected = Create("div", "combo-selected", combobox, text);
	combobox.dropdown = Create("div", "combo-dropdown", combobox);
	combobox.add = function(text, action, arg) {
		var elem = Create("div", "combo-item", this.dropdown, text);
		elem.onclick = function() {
			elem.parentNode.parentNode.blur();
			elem.parentNode.parentNode.selected.innerHTML = this.innerHTML;
			action(arg);
		}
	}
	for(var arg in args) {
		combobox.add(args[arg], func, arg);
	}
	return combobox;
}

function uModal(title, ubody = true, ufooter = true, uclose = true) {
	document.body.style.overflow = "hidden";
	
	if(umodal != null && umodal.parentNode != null) {
		umodal.parentNode.removeChild(umodal);
	}
	
	umodal = Create("div", "umodal", document.body);
	umodal.onclick = function(event) {
		if(event.target == this) {
			document.body.style.overflow = "auto";
			document.body.removeChild(umodal);
		}
	}
	umodal.uframe = Create("div", "umodal-frame box", umodal);
	if(title != null) {
		umodal.utop = Create("div", "header", umodal.uframe, title);
		if(uclose) {
			umodal.utop.uclose = Create("div", "uclose", umodal.utop, '<i class="fa fa-times" aria-hidden="true"></i>');
			umodal.utop.uclose.onclick = function() {
				document.body.style.overflow = "auto";
				document.body.removeChild(umodal);
			}
		}
	}
	if(ubody) { umodal.ubody = Create("div", "body", umodal.uframe); }
	if(ufooter) { umodal.ufooter = Create("div", "footer", umodal.uframe); }
	
	umodal.setIcon = function(icon) {
		if(umodal.utop != null) {
			umodal.utop.className = "header header-icon";
			umodal.utop.style.backgroundImage = "url('"+icon+"')";
		}
	}
}

function uModalConfirm(title, ubody, callback) {

}

function Notify(text, livetime=0, style = null) {
	console.log(text);
	if(notifyPanel == null) {
		notifyPanel = Create("div", "notifyPanel", document.body);
	}
	var notify = Create("div", "notify", null, text);
	if(typeof(style) == "string") {
		notify.classList.add(style);
	} else if(style == true) {
		notify.classList.add("success");
	} else if(style == false) {
		notify.classList.add("danger");
	} else {
		notify.classList.add("secondary");
	}
	notifyPanel.insertBefore(notify, notifyPanel.childNodes[0]);
	notify.progress = function(progress) {
		this.setAttribute("progress", Math.floor(progress*100));
		this.style.boxShadow = "inset "+Math.floor(this.clientWidth*progress)+"px 0px 0px #FFF3";
	}
	notify.error = function(text) {
		this.classList.add("danger");
		this.innerHTML = text;
		setTimeout(function(elem) {elem.remove();}, 5000, notify);
		console.log(text);
	}
	notify.complete = function(text) {
		this.style.boxShadow = "inset 0px 0px 0px #FFF3";
		this.removeAttribute("progress");
		this.innerHTML = text;
		setTimeout(function(elem) {elem.remove();}, 5000, notify);
		console.log(text);
	}
	notify.remove = function() {
		this.classList.add("remove");
		setTimeout(function(elem) {
			if(elem != null && elem.parentNode == notifyPanel) {
				notifyPanel.removeChild(elem);
			}
		}, 500, this);
	}
	if(livetime > 0) { setTimeout(function(elem) {elem.remove();}, livetime*1000, notify); }
	return notify;
}

function UiRichText(string, readyString = "", curBet = 0) {
	if(string.length == curBet) {
		return "<span type='rich-text'>"+readyString+"</span>";
	}
	if(string[curBet] == "<") {
		var tag = richTextGetTag(string, curBet);
		if(tag == null) { return ("<span type='rich-text'>"+readyString+"<span class='rich-text-error'></span></span>"); }
		curBet = tag.curBet;
		
		if(tag.tag == "b" || tag.tag == "s" || tag.tag == "i") {
			if(tag.end) {
				readyString=readyString+"</"+tag.tag+">";
			} else {
				readyString=readyString+"<"+tag.tag+" type='richtext'>";
			}
		} else if(tag.tag == "size" || tag.tag == "color") {
			if(tag.end) {
				readyString=readyString+"</span>";
			} else if(tag.tag == "size") {
				readyString=readyString+"<span style='font-size: "+tag.arg+";' type='richtext'>";
			} else if(tag.tag == "color") {
				readyString=readyString+"<span style='color: "+tag.arg+";' type='richtext'>";
			}
		}
		return UiRichText(string, readyString, curBet);
	} else if(string[curBet] == "\\" && string[curBet+1] == "n") {
		readyString = readyString+"<br>";
		curBet = curBet+2;
		return UiRichText(string, readyString, curBet);
	} else if(string[curBet] == "\n") {
		readyString = readyString+"<br>";
		curBet = curBet+1;
		return UiRichText(string, readyString, curBet);
	}
	readyString = readyString+string[curBet];
	curBet = curBet+1;
	return UiRichText(string, readyString, curBet);
}

function richTextGetTag(string, curBet)
{
	var tag = "";
	var end = false;
	var arg = "";
	var argStart = false;
	for(var i = curBet+1; i < string.length; i++) {
		if(string[i] == "<") {
			return null;
		}else if(string[i] == ">") {
			return {tag:tag.toLowerCase(), end:end, arg:arg, curBet:i+1};
		} else if(string[i] == "/") {
			end = true;
		} else if(string[i] == "=") {
			argStart = true;
		} else if(argStart) {
			arg=arg+string[i];
		} else {
			tag = tag+string[i];
		}
	}
}

function ToggleMaxSize()
{
	if(document.body.getAttribute("size") != "max") {
		document.body.setAttribute("size", "max");
	} else {
		document.body.setAttribute("size", "normal");
	}
	cpsettings.maxsize = document.body.getAttribute("size");
	SaveSettings();
}
function SetScheme(scheme)
{
	document.body.setAttribute("scheme", scheme);
	cpsettings.scheme = scheme;
	SaveSettings();
}