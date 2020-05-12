<template>
	<div>
		<div class="row">
			<div id="digital-totals" class="col-12">
				<table class="table table-striped">
					<thead>
						<tr>
							<th scope="col" class="text-center">Station</th>
							<th v-for="(item, index) in renderTimes(chartData)" :key="index" scope="col" class="text-center">{{ item }}</th>
						</tr>
					</thead>
					<tbody v-html="renderMetrics(chartData)"></tbody>
				</table>
			</div>
		</div>
		<div class="row">
			<div id="digital-graphs" class="col-12">
				<bar-chart :chart-data="pullOutlet(chartData)" :options="renderOptions()"></bar-chart>
			</div>
		</div>
	</div>
</template>

<script>
import BarChart from '@/components/BarChart'
export default {
	name: 'Digital',
	props: [
		'chart-data'
	],
	components: {
		BarChart
	},
	methods: {
		renderTimes: function(chartData) {
			var times = [];
			var outlets = [];
			var full = false;
			for (var d in chartData) {
				outlets.push(d);
				if (typeof chartData[d]['digital'] === 'object' && !full) {
					for (var o in chartData[d]['digital']) {
						times.push(o.toUpperCase());
					}
					full = true;
				}
			}

			return times;
		},
		renderMetrics: function(chartData) {
			function numberWithCommas(x) {
				return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			}
			var metrics = [];
			var outlets = [];
			var averages = [];
			var m, s, f;
			var output = '';
			var full = false;
			for (var c in chartData) {
				if (typeof chartData[c]['digital'] === 'object') {
					if (!full) {
						for (var o in chartData[c]['digital']) {
							metrics.push({text: o, title: o.replace('-', ' ').toUpperCase()});
						}
						full = true;
					}
					outlets.push(c);
				}
			}
			for (m=0; m<metrics.length; m++) {
				averages[metrics[m].text] = [];
			}
			for (s=0; s<outlets.length; s++) {
				output += '<tr>';
				for (m=0; m<metrics.length; m++) {
					if ( m == 0 ) {
						output += '<th scope="row" class="text-center align-middle">'+outlets[s].toUpperCase()+'</th>';
					}
					output += '<td class="text-center">'+numberWithCommas( chartData[outlets[s]]['digital'][metrics[m].text] )+'</td>';
					averages[metrics[m].text] = Number( averages[metrics[m].text] ) + Number( chartData[outlets[s]]['digital'][metrics[m].text] );
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
			output += '</tr>';
			return output;
		},
		pullOutlet: function(chartData) {
			var output = {
				labels: [],
				datasets: []
			};
			var c = 0;
			var station = {
				'kuhf-fm': '219,68,55',
				'kera-fm': '59,89,152',
				'kut-fm': '138,58,185',
				'kstx-fm': '5,106,178'
			};
			for (var d in chartData ) {
				output.datasets[c] = {
					label: d.toUpperCase(),
					backgroundColor: "rgba( "+station[d]+", 0.2 )",
					borderColor: "rgba( "+station[d]+", 1 )",
					stack: 'Stack '+c,
					data: []
				};
				if ( typeof chartData[d] === 'object' && typeof chartData[d]['digital'] === 'object') {
					for (var t in chartData[d]['digital']) {
						if (c == 0 && t !== 'Total' && t !== 'Average' ) {
							output.labels.push(t);
						}
						if (t !== 'Total' && t !== 'Average' ) {
							output.datasets[c]['data'].push( chartData[d]['digital'][t] );
						}
					}
					c++;
				}
			}
			return output;
		},
		renderOptions: function() {
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
					text: 'Newscast Downloads by Daypart',
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
	}
}
</script>
