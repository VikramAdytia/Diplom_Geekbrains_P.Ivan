var cpsettings = {
	scheme: "light",
	maxsize: false
};
var filter = {};
var filterBlock = null;
var logsblock;
var logType;

var logsType = {
	"connect": "Подключения/отключения",
	"chat": "Глобальный чат",
	"teamchat": "Командный чат",
	"pm": "Приватный чат",
	"tp": "Телепортации",
	"grant": "Выдача и снятие привилегий",
	"kit": "Получение китов"
}

window.onload = function() {
	LoadLocalData();
    MainNavBar();
	LoadMainPage();
}

function LoadLocalData() {
	if(localStorage.cpsettings == null) {
		localStorage.cpsettings = JSON.stringify(cpsettings);
	} else {
		cpsettings = JSON.parse(localStorage.cpsettings);
	}
	document.body.setAttribute("size", cpsettings.maxsize);
	document.body.setAttribute("scheme", cpsettings.scheme);
	//ToggleMaxSize(cpsettings.maxsize);
	//SetScheme(cpsettings.scheme);

}
function SaveSettings() {
	localStorage.cpsettings = JSON.stringify(cpsettings);
}
function SaveServers() {
	localStorage.servers = JSON.stringify(servers);
}

function MainNavBar() {
	var navbarcontentleft = {};
	var navbarcontentright = {
		"Настройки": {
			"Полноэкранный режим": ToggleMaxSize,
			"Тема": {
				"Светлая": function() {SetScheme("light");},
				"Тёмная": function() {SetScheme("dark");},
				"Тёмно-синяя": function() {SetScheme("darkblue");},
				"Ржаво-красная": function() {SetScheme("rust");},
				"Вырвиглазно-магическая": function() {SetScheme("magic");},
			}
		}
	};
	UiNavBar(navbarcontentleft, navbarcontentright);
}

function LoadMainPage() {
    Container();
    var row = Create("div", "row", container);
    logsblock = UiColBox(row, 70, "Логи");
	logsblock.ubody.style.cssText = "text-overflow: ellipsis;white-space: pre-line;";

	/*for(var s in servers) {
		var but = Create("div", "serverblock", serverslist.ubody, '<div class="servertitle">'+servers[s].title+'</div><div class="serveradress">'+servers[s].adress+':'+servers[s].port+'</div>');
		but.server = s;
		but.onclick = function() {
			LoadServerPage(this.server);
		}
	}*/

	//Create("button", "btn btn-success", Create("div", "ml-auto", UiBtnToolbar(serverslist.ufooter)), 'Добавить новый сервер').onclick = OpenNewServerModal;

	
    filterBlock = UiColBox(row, 30, "Выбор логов", null, "");

	filter.server = UiInputNumeric(UiInputGroup(filterBlock.ubody, "Сервер (0 для поиска по всем серверам)"), 0, 1, function() { });
	filter.logtype = UiCombobox(UiInputGroup(filterBlock.ubody, "Тип логов"), "Чё будем смотреть?", RebuildFilter, logsType);

	filter.steamid = UiInputString(UiInputGroup(filterBlock.ubody, "SteamID игрока"), "", function() { });

	filter.steamid2block = UiInputGroup(filterBlock.ubody, "SteamID второго игрока");
	filter.steamid2 = UiInputString(filter.steamid2block, "", function() { });
	filter.steamid2block.parentNode.style.display = "none";

	filter.teamblock = UiInputGroup(filterBlock.ubody, "Айди команды");
	filter.teamid = UiInputString(filter.teamblock, "", function() { });
	filter.teamblock.parentNode.style.display = "none";

	filter.datefrom = UiInputDate(UiInputGroup(filterBlock.ubody, "Дата (от)"), "", function() { });
	filter.dateto = UiInputDate(UiInputGroup(filterBlock.ubody, "Дата (до)"), "", function() { });

	filter.datefrom.valueAsNumber = new Date().getTime()/*-86400000*/;
	filter.dateto.valueAsNumber = new Date().getTime()+86400000;

	filter.addbutton = Create("button", "btn btn-success", Create("div", "ml-auto", UiBtnToolbar(filterBlock.ufooter)), 'Загрузить');
	filter.addbutton.onclick = function() {
		
		this.disabled = true;
		LoadLogs();
	};
	filter.addbutton.disabled = true;
}

function RebuildFilter(type)
{
	filter.addbutton.disabled = false;
	logType = type;
	filter.steamid2block.parentNode.style.display = "none";
	filter.teamblock.parentNode.style.display = "none";

	switch(type) {
		case "teamchat":
			filter.teamblock.parentNode.style.display = "block";
		break;
		case "pm":
		case "tp":
			filter.steamid2block.parentNode.style.display = "block";
		break;
	}
}

function LoadLogs()
{
	logsblock.ubody.innerHTML = "";

	var url = "/api/players/getLogs?log="+logType;
	//var url = "/api/players/get"+logType+".json";

	//RdGetRequest(url, ParseLogs);
	//return;

	url = url+"&datefrom="+ Math.floor(new Date(filter.datefrom.value).getTime() / 1000);
	url = url+"&dateto="+ Math.floor(new Date(filter.dateto.value).getTime() / 1000);

	if(filter.server.value != 0) {
		url = url+"&server="+filter.server.value;
	}
	if(filter.steamid.value != "") {
		url = url+"&steamid="+filter.steamid.value;
	}
	if((logType=="pm" || logType=="tp") && filter.steamid2.value != "") {
		url = url+"&steamid2="+filter.steamid.value;
	}
	if(logType=="teamchat" && filter.teamid.value != "") {
		url = url+"&team="+filter.teamid.value;
	}

	RdGetRequest(url, ParseLogs);

	//Notify(url, 5, false);
}

function ParseLogs(data) {
	filter.addbutton.disabled = false;

	var logs = data.data;

	for(var l in logs) {
		var log = logs[l];

		switch(logType) {
			case "connect":
				var str = "[s"+log.server+"] ";
				if(log.status) {
					str = str+log.steamid+"/"+log.ip+"/"+log.name + " подключился";
				} else {
					str = str+log.steamid+" отключился";
				}
				addLine(str, log.time);
			break;
			case "chat":
				addLine( "[s"+log.server+"] "+log.steamid+"/"+log.name + ":"+log.message, log.time);
			break;
			case "teamchat":
				addLine( "[s"+log.server+"] ["+log.team+"] "+log.steamid+"/"+log.name + ":"+log.message, log.time);
			break;
			case "pm":
				addLine( "[s"+log.server+"] "+log.steamid1+"/"+log.name1+" => " + log.steamid2+"/"+log.name2+":"+log.message, log.time);
			break;
			case "tp":
				addLine( "[s"+log.server+"] "+log.steamid1+" => " + log.steamid2, log.time);
			break;
			case "grant":
				var str = "[s"+log.server+"] ";
				if(log.seconds == 0) {
					str = str+"У игрока   "+log.steamid+" снята ";
				} else {
					str = str+"Игроку "+log.steamid+" выдана ";
				}
				if(log.isgroup) {
					str = str+"группа ";
				} else {
					str = str+"привилегия ";
				}
				str = str +log.service;
				if(log.seconds != 0) {
					var d = new Date(log.seconds*1000);
					str = str+" на " + (d.getUTCDate()-1)+"д"+d.getUTCHours()+"ч"+d.getUTCMinutes()+"м";
				}
				addLine( str, log.time);
			break;
			case "kit":
				addLine( "[s"+log.server+"] "+log.steamid+" получил кит " + log.kit, log.time);
			break;
		}

	}
}

function addLine(str, time)
{
	var date = new Date(time * 1000);
	var text = "[" + date.toLocaleDateString() + " " + date.toLocaleTimeString()+"] "+str+"\n";
	logsblock.ubody.innerHTML = logsblock.ubody.innerHTML+text;
}

window.onerror = function(msg, url, lineNo, columnNo, error) {
	Notify(msg+"<br>"+url+": "+lineNo, 5, false);
}

window.addEventListener('storage', function(event) {
	if(event.key == "cpsettings") {
		cpsettings = JSON.parse(localStorage.cpsettings);
		document.body.setAttribute("size", cpsettings.maxsize);
		document.body.setAttribute("scheme", cpsettings.scheme);
	}
});