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
				times.push(o);
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
			var m, s, f;
			var output = '';
			for (var c in chartData) {
				outlets.push(c);
			}
			for (var o in chartData[outlets[0]]) {
				times.push(o);
			}
			for (var t in chartData[outlets[0]][times[0]]['during']) {
				metrics.push({text: t, title: t.replace('-', ' ').toUpperCase()});
			}
			for (s=0; s<outlets.length; s++) {
				for (m=0; m<metrics.length; m++) {
					output += '<tr>';
					if ( m == 0 ) {
						output += '<th rowspan="4" scope="row" class="text-center align-middle">'+outlets[s]+'</th>';
					}
					output += '<td><strong>'+metrics[m].title+'</strong></td>';
					for (f=0; f<times.length; f++) {
						if ( times[f] == 'Totals' ) {
							output += '<td class="text-center">'+numberWithCommas( chartData[outlets[s]][times[f]][metrics[m].text] )+'</td>';
						} else {
							output += '<td class="text-center">'+numberWithCommas( chartData[outlets[s]][times[f]]['during'][metrics[m].text] )+'</td>';
						}
					}
					output += '</tr>';
				}
			}
			return output;
		}
	}
}
</script>
