<template>
	<div class="row">
		<div id="overall-totals" class="col-12">
			<table class="table table-striped">
				<thead>
					<tr>
						<th scope="col" class="text-center">Station</th>
						<th scope="col" class="text-center">Metric</th>
						<th v-for="(item, index) in renderTimes(chartData)" :key="index" scope="col" class="text-center">{{ item }}</th>
					</tr>
				</thead>
				<tbody v-html="renderMetrics(chartData)"></tbody>
			</table>
		</div>
	</div>
</template>

<script>
export default {
	name: 'Overall',
	props: [
		'chart-data'
	],
	methods: {
		renderTimes: function(chartData) {
			var times = [];
			var outlets = [];
			for (var d in chartData) {
				outlets.push(d);
			}
			for (var o in chartData[outlets[0]]) {
				if ( o != 'ROS' ) {
					times.push(o);
				}
			}
			return times;
		},
		renderMetrics: function(chartData) {
			function numberWithCommas(x) {
				return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			}
			var metrics = [];
			var times = [];
			var outlets = [];
			var averages = [];
			var avgMeta = [];
			var m, s, f;
			var output = '';
			for (var c in chartData) {
				outlets.push(c);
			}
			for (var o in chartData[outlets[0]]) {
				if ( o !== 'ROS' ) {
					times.push(o);
				}
			}
			for (var t in chartData[outlets[0]][times[0]]['during']) {
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
							output += '<td class="text-center">'+numberWithCommas( chartData[outlets[s]][times[f]][metrics[m].text] )+'</td>';
							avgMeta[metrics[m].text] = Number( avgMeta[metrics[m].text] ) + Number( chartData[outlets[s]][times[f]][metrics[m].text] );
						} else {
							output += '<td class="text-center">'+numberWithCommas( chartData[outlets[s]][times[f]]['during'][metrics[m].text] )+'</td>';
							averages[metrics[m].text][times[f]] = Number( averages[metrics[m].text][times[f]] ) + Number( chartData[outlets[s]][times[f]]['during'][metrics[m].text] );
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
			return output;
		}
	}
}
</script>
