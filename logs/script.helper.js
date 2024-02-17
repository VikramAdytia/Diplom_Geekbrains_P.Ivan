function RdGetRequest(dataUrl, onLoad = null, onProgress = null, onError = null) {
	var xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = function() { 
        if (xmlHttp.readyState == 4) {
			if(onLoad != null && xmlHttp.status == 200) { onLoad(JSON.parse(xmlHttp.responseText)); } else if(onError != null && xmlHttp.status != 200) { onError(xmlHttp.status); }
		}
    }
	xmlHttp.onerror = function(event) { if(onError != null) { onError(xmlHttp.status); } }
	xmlHttp.onprogress = function(event) { if(onProgress != null) { onProgress(event.loaded, event.total); } }
    xmlHttp.open("GET", dataUrl, true);
    xmlHttp.send(null);
}

function RdPostRequest(dataUrl, data, onLoad = null, onProgress = null, onError = null) {
	var xmlHttp = new XMLHttpRequest();
	var json = JSON.stringify(data);
    xmlHttp.onreadystatechange = function() { 
        if (xmlHttp.readyState == 4) {
			console.log(xmlHttp.responseText);
			if(onLoad != null && xmlHttp.status == 200) { onLoad(JSON.parse(xmlHttp.responseText)); } else if(onError != null && xmlHttp.status != 200) { onError(xmlHttp.status); }
		}
    }
	xmlHttp.onerror = function(event) { if(onError != null) { onError(xmlHttp.status); } }
	xmlHttp.onprogress = function(event) { if(onProgress != null) { onProgress(event.loaded, event.total); } }
    xmlHttp.open("POST", dataUrl, true);
	xmlHttp.setRequestHeader('Content-type', 'application/json; charset=utf-8');
    xmlHttp.send(json);
}

function RandomRange(from, to) {
	return Math.round(Math.random()*(to - from))+from;
}

function FirstKey(array) {
	for(var key in array) {
		return key;
	}
}

function FirstValue(array) {
	for(var key in array) {
		return array[key];
	}
}

function b64DecodeUnicode(str) {
    return decodeURIComponent(atob(str).split('').map(function(c) {
        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
    }).join(''));
}