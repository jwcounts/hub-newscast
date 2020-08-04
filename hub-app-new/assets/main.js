const dlUrl = '/hub/data/';
const tabs = document.querySelectorAll('.tabs ul li');
const timeFrames = [ '7:30A-7:45A', '8:30A-8:45A', '10:30A-10:45A', '1P-1:15P', '4:30P-4:45P', '5:30P-5:45P' ];
const digitalTimes = [ '12:30', '13:30', '14:30', '18:00', '19:00', '21:30', '22:30', '23:30' ];
const graphs = [ 'aqh-persons', 'aqh-rtg', 'share', 'avg-wk-cume' ];
var currentReport = {};
var currentData = [];
var allReports = [];
var stations = { 'kera-fm': false, 'kstx-fm': false, 'kuhf-fm': false, 'kut-fm': false };
function GetIEVersion() {
	var sAgent = window.navigator.userAgent;
	var Idx = sAgent.indexOf("MSIE");
	if (Idx > 0) {
		return parseInt(sAgent.substring(Idx+ 5, sAgent.indexOf(".", Idx)));
	} else if (!!navigator.userAgent.match(/Trident\/7\./)) {
		return 11;
	} else {
	  return 0;
	}
}
function numberWithCommas(x) {
	return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
window.getJSON = function(url, callback) {
	var xhr = new XMLHttpRequest();
	xhr.open('GET', url, true);
	xhr.responseType = 'json';
	xhr.onload = function() {
		var status = xhr.status;
		if (status === 200) {
			callback(null, xhr.response);
		} else {
			callback(status, xhr.response);
		}
	};
	xhr.send();
};
function checkStations(data) {
	var digital = false;
	var digiTab = document.getElementById('digital').classList;
	var digiService = document.getElementById('digital-service').classList;
	var overallTab = document.getElementById('overall').classList;
	var overallService = document.getElementById('overall-service').classList;
	for ( var d in stations ) {
		var currTab = document.getElementById(d).classList;
		var currService = document.getElementById(d+'-service').classList;
		if ( typeof data[d] !== 'object') {
			currTab.add('inactive');
			if ( currTab.contains('is-active') ) {
				currTab.remove('is-active');
				currService.remove('service-active');
				overallTab.add('is-active');
				overallService.add('service-active');
			}
			currService.add('service-inactive');
			currService.remove('service-active');
			stations[d] = false;
		} else {
			stations[d] = true;
			if ( typeof data[d]['7:30A-7:45A'] === 'object' ) {
				currTab.remove('inactive');
				currService.remove('service-inactive');
			}
			if (typeof data[d]['digital'] === 'object') {
				digital = true;
			}
		}
	}

	if ( digital ) {
		digiService.remove('service-inactive');
		digiTab.remove('inactive');
	} else {
		digiService.add('service-inactive');
		digiTab.add('inactive');
		if (digiTab.contains('is-active')) {
			digiTab.remove('is-active');
			digiService.remove('service-active');
			overallTab.add('is-active');
			overallService.add('service-active');
		}
	}
	return stations;
}
function graphData(data,metric) {
	var output = {
		labels: [],
		datasets: [{
			label: 'Before',
			backgroundColor: "rgba( 0, 255, 0, 0.2 )",
			borderColor: "rgba( 0, 255, 0, 1 )",
			stack: 'Stack 0',
			data: []
		}, {
			label: 'During',
			backgroundColor: "rgba( 255, 0, 0, 0.2 )",
			borderColor: "rgba( 255, 0, 0, 1 )",
			stack: 'Stack 1',
			data: []
		}, {
			label: 'After',
			backgroundColor: "rgba( 0, 0, 255, 0.2 )",
			borderColor: "rgba( 0, 0, 255, 1 )",
			stack: 'Stack 2',
			data: []
		}]
	};
	if ( typeof data !== 'object' ) {
		return output;
	}
	for (var d in timeFrames ) {
		if ( typeof data[ timeFrames[d] ] === 'object' ) {
			output.labels.push( timeFrames[d] );
			output.datasets[0].data.push( data[ timeFrames[d] ]['before'][metric] );
			output.datasets[1].data.push( data[ timeFrames[d] ]['during'][metric] );
			output.datasets[2].data.push( data[ timeFrames[d] ]['after'][metric] );
		}
	}
	return output;
}
function graphUpdate(data,metric,old) {
	old.labels = [];
	old.datasets[0].data = [];
	old.datasets[1].data = [];
	old.datasets[2].data = [];
	if ( typeof data !== 'object' ) {
		return old;
	}
	for (var d in timeFrames ) {
		if ( typeof data[ timeFrames[d] ] === 'object' ) {
			old.labels.push( timeFrames[d] );
			old.datasets[0].data.push( data[ timeFrames[d] ]['before'][metric] );
			old.datasets[1].data.push( data[ timeFrames[d] ]['during'][metric] );
			old.datasets[2].data.push( data[ timeFrames[d] ]['after'][metric] );
		}
	}
	return old;
}
function digitalGraph(data) {
	var output = {
		labels: [],
		datasets: []
	};
	var c = 0;
	var stationColor = {
		'kuhf-fm': '219,68,55',
		'kera-fm': '59,89,152',
		'kut-fm': '138,58,185',
		'kstx-fm': '5,106,178'
	};
	for (var d in stations ) {
		output.datasets[c] = {
			label: d.toUpperCase(),
			backgroundColor: "rgba( "+stationColor[d]+", 0.2 )",
			borderColor: "rgba( "+stationColor[d]+", 1 )",
			stack: 'Stack '+c,
			data: []
		};
		if ( typeof data[d] === 'object' && typeof data[d]['digital'] === 'object') {
			for (var t in digitalTimes) {
				if (c == 0 ) {
					var time = digitalTimes[t].split(':');
					time[0] = time[0] - 5;
					output.labels.push( time[0] + ':' + time[1] );
				}
				output.datasets[c]['data'].push( data[d]['digital'][ digitalTimes[t] ] );
			}
			c++;
		}
	}
	return output;
}
function update(report) {
	getJSON( dlUrl + allReports[report].value + ".json", function(err,data) {
		if (err !== null) {
			console.log(err);
		} else {
			if (GetIEVersion() > 0) {
				data = JSON.parse(data);
			}
			currentData = data;
			currentReport = allReports[report];
			stations = checkStations(data);
			setMonth(currentReport.text);
			document.getElementById('excelDownload').setAttribute('href', dlUrl + 'excel/' + currentReport.download );
			for ( var s in stations ) {
				for ( var g in graphs ) {
					var graphWrap = s+'-'+graphs[g];
					var oldData = window[graphWrap+'-graph'].data;
					window[graphWrap+'-graph'].data = graphUpdate(currentData[s], graphs[g], oldData);
					window[graphWrap+'-graph'].update();
				}
			}
			var oldData = window['digital-downloads-graph'].data;
			window['digital-downloads-graph'].data = digitalGraph(currentData);
			window['digital-downloads-graph'].update();
			document.getElementById('overall-totals').innerHTML = overallGen(currentData);
			document.getElementById('ros-totals').innerHTML = rosGen(currentData);
			document.getElementById('digital-totals').innerHTML = digitalGen(currentData);
		}
	});
}
function setMonth(text) {
	var spl = text.split(' ');
	document.getElementById('digital-time').innerHTML = spl[0] + ' ' + spl[1];
}
function renderOptions(metric) {
	var title;
	if ( metric == 'aqh-persons' ) {
		title = 'Average Quarter Hour Persons'
	} else if ( metric == 'aqh-rtg' ) {
		title = 'AQH Rating %';
	} else if ( metric == 'share' ) {
		title = 'Share %';
	} else if ( metric == 'avg-wk-cume' ) {
		title = 'Average Weekly Cume';
	} else if ( metric == 'digital' ) {
		title = 'Newscast Downloads by Daypart';
	}
	return {
		elements: {
			rectangle: {
				borderWidth: 2,
			}
		},
		maintainAspectRatio: true,
		responsive: true,
		legend: {
			position: 'bottom',
		},
		title: {
			display: true,
			text: title,
			fontSize: 16
		},
		scales: {
			xAxes: [{
				stacked: true,
			}],
			yAxes: [{
				stacked: true
			}]
		}
	}
}
function overallGen(data) {
	var output = "";
	var overall = false;
	var outlets = []
	var metrics = [];
	var averages = [];
	var avgMeta = [];
	var m, s;
	var times = timeFrames.slice();
	times.push('Averages');
	if ( typeof data !== 'object' ) {
		return output;
	}
	for ( var d in stations ) {
		if ( typeof data[d] === 'object' ) {
			overall = true;
			outlets.push(d);
		}
	}
	if ( overall ) {
		output += "<table class=\"table is-bordered is-striped is-fullwidth\">" +
		"<thead>" +
			"<tr>" +
				"<th scope=\"col\">Station</th>" +
				"<th scope=\"col\">Metric</th>";
		for ( var t in times ) {
			output += "<th scope=\"col\">" + times[t] + "</th>";
		}
		output += "</thead><tbody>";
		for (var t in data[outlets[0]][times[0]]['during']) {
			metrics.push({text: t, title: t.replace('-', ' ').toUpperCase()});
		}
		for (m=0; m<metrics.length; m++) {
			averages[metrics[m].text] = [];
			avgMeta[metrics[m].text] = 0;
			for (f=0; f<times.length; f++) {
				if ( times[f] !== 'Averages' ) {
					averages[metrics[m].text][times[f]] = 0;
				}
			}
		}
		for (s=0; s<outlets.length; s++) {
			for (m=0; m<metrics.length; m++) {
				output += '<tr>';
				if ( m == 0 ) {
					output += '<th rowspan="4" scope="row" class="text-center align-middle">'+outlets[s].toUpperCase()+'</th>';
				}
				output += '<td><strong>'+metrics[m].title+'</strong></td>';

				for (f=0; f<times.length; f++) {
					if ( times[f] == 'Averages' ) {
						output += '<td class="text-center">'+numberWithCommas( data[outlets[s]][times[f]][metrics[m].text] )+'</td>';
						avgMeta[metrics[m].text] = Number( avgMeta[metrics[m].text] ) + Number( data[outlets[s]][times[f]][metrics[m].text] );
					} else {
						output += '<td class="text-center">'+numberWithCommas( data[outlets[s]][times[f]]['during'][metrics[m].text] )+'</td>';
						averages[metrics[m].text][times[f]] = Number( averages[metrics[m].text][times[f]] ) + Number( data[outlets[s]][times[f]]['during'][metrics[m].text] );
					}
				}
				output += '</tr>';
			}
		}
		for (m=0; m<metrics.length; m++) {
			output += '<tr>';
			if ( m == 0 ) {
				output += '<th rowspan="4" scope="row" class="text-center align-middle">All Stations<br />(Average)</th>';
			}
			output += '<td><strong>'+metrics[m].title+'</strong></td>';

			for (f=0; f<times.length; f++) {
				if ( times[f] == 'Averages' ) {
					output += '<td class="text-center">'+numberWithCommas( ( avgMeta[metrics[m].text]/outlets.length ).toFixed(1) )+'</td>';
				} else {
					output += '<td class="text-center">'+numberWithCommas( ( averages[metrics[m].text][times[f]]/outlets.length ).toFixed(1) )+'</td>';
				}
			}
			output += '</tr>';
		}
		output += "</tbody></table>";
	}
	return output;
}
function rosGen(data) {
	var output = "";
	var ros = false;
	var outlets = [];
	var metrics = [];
	var averages = [];
	var m, s;
	if ( typeof data !== 'object' ) {
		return output;
	}
	for ( var d in stations ) {
		if ( typeof data[d] === 'object' && typeof data[d]['ROS'] === 'object' ) {
			ros = true;
			outlets.push(d);
		}
	}
	if ( ros ) {
		output += "<table class=\"table is-bordered is-striped is-fullwidth\">" +
		"<thead>" +
			"<tr>" +
				"<th scope=\"col\">Station</th>";
		for ( var t in data[ outlets[0] ]['ROS'] ) {
			metrics.push({text: t, title: t.replace('-', ' ').toUpperCase()});
			output += "<th scope=\"col\">" + t.replace('-', ' ').toUpperCase() + "</th>";
		}
		output += "</tr>" +
		"</thead>" +
		"<tbody>";
		for (m=0; m<metrics.length; m++) {
			averages[metrics[m].text] = [];
		}
		for (s=0; s<outlets.length; s++) {
			output += '<tr>';
			for (m=0; m<metrics.length; m++) {
				if ( m == 0 ) {
					output += '<th scope="row" class="text-center align-middle">'+outlets[s].toUpperCase()+'</th>';
				}
				output += '<td class="text-center">'+numberWithCommas( data[outlets[s]]['ROS'][metrics[m].text] )+'</td>';
				averages[metrics[m].text] = Number( averages[metrics[m].text] ) + Number( data[outlets[s]]['ROS'][metrics[m].text] );
			}
			output += '</tr>';
		}
		output += '<tr>';
		for (m=0; m<metrics.length; m++) {
			if ( m == 0 ) {
				output += '<th scope="row" class="text-center align-middle">All Stations (Average)</th>';
			}
			output += '<td class="text-center">'+numberWithCommas( ( Number( averages[metrics[m].text] )/outlets.length ).toFixed(1) )+'</td>';
		}
		output += "</tr>" + "</tbody>" + "</table>";
	}
	return output;
}
function digitalGen(data) {
	var output = "";
	var metrics = [];
	var outlets = [];
	var averages = [];
	var m, s;
	var full = false;
	if ( typeof data !== 'object' ) {
		return output;
	}
	for (var c in stations) {
		if (typeof data[c] === 'object' && typeof data[c]['digital'] === 'object') {
			if (!full) {
				for (var o in data[c]['digital']) {
					metrics.push({text: o, title: o.replace('-', ' ').toUpperCase()});
				}
				full = true;
			}
			outlets.push(c);
		}
	}
	output += "<table class=\"table is-bordered is-striped is-fullwidth\">" +
		"<thead>" +
			"<tr>" +
				"<th scope=\"col\">Station</th>";
		for ( var t in digitalTimes ) {
			var time = digitalTimes[t].split(':');
			time[0] = time[0] - 5;
			output += "<th scope=\"col\">" + time[0] + ':' + time[1] + "</th>";
		}
		output += "<th scope=\"col\">Total</th>" +
			"<th scope=\"col\">Average</th>" +
		"</tr></thead><tbody>";
	for (m=0; m<metrics.length; m++) {
		averages[metrics[m].text] = [];
	}
	for (s=0; s<outlets.length; s++) {
		output += '<tr>';
		for (m=0; m<metrics.length; m++) {
			if ( m == 0 ) {
				output += '<th scope="row" class="text-center align-middle">'+outlets[s].toUpperCase()+'</th>';
			}
			output += '<td class="text-center">'+numberWithCommas( data[outlets[s]]['digital'][metrics[m].text] )+'</td>';
			averages[metrics[m].text] = Number( averages[metrics[m].text] ) + Number( data[outlets[s]]['digital'][metrics[m].text] );
		}
		output += '</tr>';
	}
	output += '<tr>';
	for (m=0; m<metrics.length; m++) {
		if ( m == 0 ) {
			output += '<th scope="row" class="text-center align-middle">All Stations</th>';
		}
		output += '<td class="text-center">'+numberWithCommas( Number( averages[metrics[m].text] ).toFixed(0) )+'</td>';
	}
	output += '</tr></tbody></table>';
	return output;
}
(function(){
	[].forEach.call(tabs, function (tab) {
		tab.addEventListener('click', function(){
			if (this.classList.contains('is-active')) {
				return false;
			} else {
				[].forEach.call(tabs, function (ta) {
					ta.classList.remove('is-active');
				});
				this.classList.add('is-active');
				var tabId = this.getAttribute('id');
				var services = document.querySelectorAll('.services');
				[].forEach.call(services, function (serve) {
					var sId = serve.getAttribute('id');
					if ( tabId+'-service' === sId ) {
						serve.classList.add('service-active');
					} else {
						serve.classList.remove('service-active');
					}
				});
			}
		});
	});
	getJSON( dlUrl + "reports.json", function(err,data) {
		if (err !== null) {
			console.log(err);
		} else {
			if (GetIEVersion() > 0) {
				data = JSON.parse(data);
			}
			currentReport = data[0];
			allReports = data;
			var selector = document.querySelector( '#weekSelect' );
			for (var i = 0; i < data.length; i++ ) {
				var option = document.createElement('option');
				option.text = data[i].text;
				option.value = i;
				if ( i === 0 ) {
					option.selected = 'selected';
				}
				selector.add(option);
			}
			selector.addEventListener("change",function(){
				update(selector.value);
			});
			getJSON( dlUrl + currentReport.value + ".json", function(err,data) {
				if (err !== null) {
					console.log(err);
				} else {
					if (GetIEVersion() > 0) {
						data = JSON.parse(data);
					}
					currentData = data;
					stations = checkStations(data);
					setMonth(currentReport.text);
					document.getElementById('excelDownload').setAttribute('href', dlUrl + 'excel/' + currentReport.download );
					for ( var s in stations ) {
						for ( var g in graphs ) {
							var graphWrap = s+'-'+graphs[g];
							var container = document.getElementById(graphWrap);
							var canvas = document.createElement('canvas');
							container.appendChild(canvas).setAttribute('id',graphWrap+'-graph');
							var ctx = document.getElementById(graphWrap+'-graph').getContext('2d');
							window[graphWrap+'-graph'] = new Chart(ctx, {
								type: 'bar',
								data: graphData(currentData[s], graphs[g]),
								options: renderOptions(graphs[g])
							});
						}
					}
					var container = document.getElementById('digital-downloads');
					var canvas = document.createElement('canvas');
					container.appendChild(canvas).setAttribute('id','digital-downloads-graph');
					var ctx = document.getElementById('digital-downloads-graph').getContext('2d');
					window['digital-downloads-graph'] = new Chart(ctx, {
						type: 'bar',
						data: digitalGraph(currentData),
						options: renderOptions('digital')
					});
					document.getElementById('overall-totals').innerHTML = overallGen(currentData);
					document.getElementById('ros-totals').innerHTML = rosGen(currentData);
					document.getElementById('digital-totals').innerHTML = digitalGen(currentData);
				}
			});
		}
	});
}());