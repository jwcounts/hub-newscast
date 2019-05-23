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
let stations = []

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
		{ title: 'Overall', text: 'overall' }
	]
	for (var d in data ) {
		if (typeof data[d] === 'object') {
			currentStation.push( { title: d, text: d.toLowerCase() } );
		}
	}
	return currentStation;
}
getJSON( "/hub/data/reports.json",
	function(err, data) {
		if (err !== null) {
			console.log(err);
		} else {
			monthSelected = data[0].value;
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
							template: '<App :month-selected="monthSelected" :options="options" :chart-data="chartData" :station-list="stations"></App>',
							data: {
								monthSelected: monthSelected,
								options: options,
								chartData: chartData,
								stations: stations
							}
						})
					}
				}
			);
		}
	}
);