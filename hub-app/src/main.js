import Vue from 'vue'
import App from './App'
import BootstrapVue from 'bootstrap-vue'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

Vue.use(BootstrapVue)
Vue.config.productionTip = false
let chartData = []
let options = []
let monthSelected = ''
let monthExcel = ''
let stations = []
let monthList = []

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
window.parseStations = function(data) {
	var currentStation = [
		{ title: 'Broadcast Overview', text: 'ros' },
		{ title: 'Newscasts Summary', text: 'overall' }
	]
	var digital = false;
	for (var d in data ) {
		if (typeof data[d] === 'object' && typeof data[d]['7:30A-7:45A'] === 'object') {
			currentStation.push( { title: d.toUpperCase() + ' Newscasts', text: d.toLowerCase() } );
		}
		if (typeof data[d]['digital'] === 'object') {
			digital = true;
		}
	}
	if ( digital ) {
		currentStation.push( { title: 'Digital Overview', text: 'digital' } );
	}
	return currentStation;
}
getJSON( "/hub/data/reports.json",
	function(err, data) {
		if (err !== null) {
			console.log(err);
		} else {
			monthSelected = data[0].value;
			monthExcel = '/hub/data/excel/' + data[0].download;
			monthList = data;
			options = data;
			getJSON( "/hub/data/"+monthSelected+".json",
				function(err,data) {
					if (err !== null) {
						console.log(err);
					} else {
						chartData = data;
						stations = parseStations(data);
						new Vue({
							el: '#app',
							components: { App },
							template: '<App :month-selected="monthSelected" :month-excel="monthExcel" :month-list="monthList" :options="options" :chart-data="chartData" :station-list="stations"></App>',
							data: {
								monthSelected: monthSelected,
								options: options,
								chartData: chartData,
								stations: stations,
								monthList: monthList,
								monthExcel: monthExcel
							}
						})
					}
				}
			);
		}
	}
);