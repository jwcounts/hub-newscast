<template>
	<div class="row">
		<div id="ros-totals" class="col-12">
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
</template>

<script>
export default {
	name: 'Ros',
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
			for (var o in chartData[outlets[0]]['ROS']) {
				times.push(o.replace('-', ' ').toUpperCase());
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
			for (var c in chartData) {
				outlets.push(c);
			}
			for (var t in chartData[outlets[0]]['ROS']) {
				metrics.push({text: t, title: t.replace('-', ' ').toUpperCase()});
			}
			if (metrics.length == 0) {
				return "No broadcast data has been uploaded for this month yet.";
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
					output += '<td class="text-center">'+numberWithCommas( chartData[outlets[s]]['ROS'][metrics[m].text] )+'</td>';
					averages[metrics[m].text] = Number( averages[metrics[m].text] ) + Number( chartData[outlets[s]]['ROS'][metrics[m].text] );
				}
				output += '</tr>';
			}
			output += '<tr>';
			for (m=0; m<metrics.length; m++) {
				if ( m == 0 ) {
					output += '<th scope="row" class="text-center align-middle">All Stations<br />(Average)</th>';
				}
				output += '<td class="text-center">'+numberWithCommas( ( Number( averages[metrics[m].text] )/outlets.length ).toFixed(1) )+'</td>';
			}
			output += '</tr>';
			return output;
		}
	}
}
</script>
