<template>
	<div id="app">
		<header class="container-fluid">
			<div class="row align-items-center">
				<div class="col-md-12 col-lg-5">
					<h3>Texas News Hub Newscasts</h3>
				</div>
				<div class="col-md-12 col-lg-7">
					<div class="row align-items-center">
						<label for="monthSelect" class="col-md-4 col-lg-6 text-right">Month Select:</label>
						<div class="col-md-8 col-lg-6">
							<select class="form-control" v-model="month">
								<option v-for="(option, index) in options" :key="index" :value="option.value">
									{{ option.text }}
								</option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</header>
		<div class="container-fluid body-wrap">
			<div id="tab" class="row">
				<div v-for="(item, index) in stations" v-on:click="updateTab(item.text)" :id="item.text + '-tab'" :class="item.text == 'overall' ? 'col-sm-3 col-lg tab-active ' + item.text : 'col-sm-3 col-lg ' + item.text" :key="index">{{ item.title }}</div>
			</div>
			<div id="chart-wrap">
				<div v-for="(item, index) in stations" :id="item.text + '-service'" :class="item.text == 'overall' ? 'services service-active' : 'services'" :key="index">
					<h2>{{ item.title }}</h2>
					<template v-if="item.text == 'overall' ">
						<Overall :chart-data="chartData"></Overall>
					</template>
					<template v-else-if="item.text == 'ros' ">
						<Ros :chart-data="chartData"></Ros>
					</template>
					<template v-else>
						<bar-chart :chart-data="pullOutlet(item.text,chartData,'aqh-persons')" :options="renderOptions('aqh-persons')"></bar-chart>
						<bar-chart :chart-data="pullOutlet(item.text,chartData,'aqh-rtg')" :options="renderOptions('aqh-rtg')"></bar-chart>
						<bar-chart :chart-data="pullOutlet(item.text,chartData,'share')" :options="renderOptions('share')"></bar-chart>
						<bar-chart :chart-data="pullOutlet(item.text,chartData,'avg-wk-cume')" :options="renderOptions('avg-wk-cume')"></bar-chart>
					</template>
				</div>
			</div>
		</div>
		<footer>
			<p class="float-right"><a href="/hub/upload/" class="btn btn-success">Data Upload</a></p>
			<p class="float-right" style="margin-right: 1em;"><a :href="monthExcel" class="btn btn-primary" target="_blank">Download Current Month</a></p>
		</footer>
	</div>
</template>

<script>
import Overall from '@/components/Overall'
import Ros from '@/components/Ros'
import BarChart from '@/components/BarChart'
export default {
	name: 'App',
	props: [
		'options', 'month-selected', 'chart-data', 'station-list', 'month-list', 'month-excel'
	],
	components: {
		Overall,
		Ros,
		BarChart
	},
	data() {
		return {
			month: this.monthSelected,
			stations: this.stationList
		}
	},
	watch: {
		month: function (newMonth, oldMonth) {
			// when the dropdown is changed, change the monthSelected data in $root
			if (oldMonth !== newMonth) {
				this.$root.monthSelected = this.month;
				for (var e = 0; e < this.monthList.length; e++ ) {
					if ( this.monthList[e].value == this.month ) {
						this.$root.monthExcel = '/hub/data/excel/' + this.monthList[e].download;
					}
				}
				var root = this.$root;
				var groot = this;
				getJSON( "/hub/data/"+this.month+".json",
					function(err,data) {
						if (err !== null) {
							console.log(err);
						} else {
							root.chartData = data;
							root.stations = parseStations(data);
							groot.stations = root.stations;
							var x = document.getElementsByClassName("services");
							var i;
							for (i = 0; i < x.length; i++) {
								x[i].classList.remove('service-active');
							}
							var tab = document.getElementById('tab').getElementsByTagName('div');
							for (i=0; i < tab.length; i++) {
								tab[i].classList.remove('tab-active');
							}
							document.getElementById('overall-tab').classList.add('tab-active');
							document.getElementById('overall-service').classList.add('service-active');
						}
					}
				);
			}
		}
	},
	methods: {
		updateTab: function(service) {
			var serveList = document.getElementById(service+'-tab').classList;
			if (serveList.contains('tab-acive')) {
				return false;
			} else {
				var x = document.getElementsByClassName("services");
				var i;
				for (i = 0; i < x.length; i++) {
					x[i].classList.remove('service-active');
				}
				var tab = document.getElementById('tab').getElementsByTagName('div');
				for (i=0; i < tab.length; i++) {
					tab[i].classList.remove('tab-active');
				}
				serveList.add('tab-active');
				document.getElementById(service+'-service').classList.add('service-active');
			}
			return true;
		},
		pullOutlet: function(outlet,chartData,metric) {
			var data = chartData[outlet];
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
			for (var d in data ) {
				if ( typeof data[d] === 'object' && d !== 'Averages' && d !== 'ROS' ) {
					output.labels.push(d);
					output.datasets[0].data.push( data[d]['before'][metric] );
					output.datasets[1].data.push( data[d]['during'][metric] );
					output.datasets[2].data.push( data[d]['after'][metric] );
				}
			}
			return output;
		},
		renderOptions: function(metric) {
			var title;
			if ( metric == 'aqh-persons' ) {
				title = 'Average Quarter Hour Persons'
			} else if ( metric == 'aqh-rtg' ) {
				title = 'AQH Rating %';
			} else if ( metric == 'share' ) {
				title = 'Share %';
			} else if ( metric == 'avg-wk-cume' ) {
				title = 'Average Weekly Cume';
			}
			return {
				elements: {
					rectangle: {
						borderWidth: 2,
					}
				},
				maintainAspectRatio: false,
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
		},
		currentYear: function() {
			return new Date().getFullYear();
		}
	}
}
</script>

<style>
html {
	height: 100%;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	width: 100%;
}
body {
	position: relative;
	min-height: 100%;
}
header,
footer {
	padding: 0 1em;
	background: rgb(25,25,25);
	z-index: 9999;
}
header {
	position: fixed;
	top: 0;
	left: 0;
	right: 0;
	width: 100%;
}
footer {
	position: fixed;
	bottom: 0;
	left: 0;
	right: 0;
	width: 100%;
}
header h3 {
	color: white;
	margin: 0;
}
footer p {
	color: white;
	margin: 0;
	padding: 0.25em 0;
}
header div {
	padding: 0.125em 0;
}
form {
	margin: 2em 0;
}
.form-row {
	padding-top: 1em;
	padding-bottom: 1em;
}
label {
	font-weight: bolder;
	font-size: 150%;
}
header label {
	color: white;
	font-size: 100%;
	padding: 0;
	margin: 0;
}
label.form-check-label {
	font-weight: normal;
	font-size: 100%;
}
.alert {
	margin-bottom: 0 !important;
}
.body-wrap {
	padding-top: 9em;
	padding-bottom: 5em;
}
#tab {
	padding: 0 1em 2em;
}
#tab div {
	padding: 0.25em;
	text-align: center;
	margin: 0.5em 0;
}
#tab div:hover {
	opacity: 0.75;
	cursor: pointer;
}
#tab div.kuhf-fm {
	background-color: rgba(219,68,55,0.2);
	border-top: 2px solid rgba(219,68,55,1);
}
#tab div.kera-fm {
	background-color: rgba(59,89,152,0.2);
	border-top: 2px solid rgba(59,89,152,1);
}
#tab div.kut-fm {
	background-color: rgba(138,58,185,0.2);
	border-top: 2px solid rgba(138,58,185,1);
}
#tab div.kstx-fm {
	background-color: rgba(5,106,178,0.2);
	border-top: 2px solid rgba(5,106,178,1);
}
#tab div.overall {
	background-color: rgba(128,128,128,0.2);
	border-top: 2px solid rgba(128,128,128,1);
}
#tab div.ros {
	background-color: rgba(15,185,24,0.2);
	border-top: 2px solid rgba(15,185,24,1);
}
#tab div.tab-active {
	font-weight: bolder;
	color: white;
}
#tab div.tab-active.kuhf-fm {
	background-color: rgba(219,68,55,0.75);
}
#tab div.tab-active.kera-fm {
	background-color: rgba(59,89,152,0.75);
}
#tab div.tab-active.kut-fm {
	background-color: rgba(138,58,185,0.75);
}
#tab div.tab-active.kstx-fm {
	background-color: rgba(5,106,178,0.75);
}
#tab div.tab-active.overall {
	background-color: rgba(128,128,128,0.75);
}
#tab div.tab-active.ros {
	background-color: rgba(15,185,24,0.75);
}
#chart-wrap {
	position: relative;
	min-height: 20em;
}
#chart-wrap .services {
	width: 100%;
	position: absolute;
	visibility: hidden;
	top: 0;
	margin-bottom: 4em;
}
#chart-wrap .services.service-active {
	visibility: visible;
}
#chart-wrap .row div {
	margin-bottom: 1em;
}
@media (min-width: 576px) {
	.body-wrap {
		padding-top: 7em;
	}
	#tab div {
		border-left: 2px solid white;
		border-right: 2px solid white;
	}
}
@media (min-width: 992px) {
	.body-wrap {
		padding-top: 5em;
	}
}
</style>
