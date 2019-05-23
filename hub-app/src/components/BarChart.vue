<template>
	<div>
		<bar-chart :chart-data="chartdata" :options="options"></bar-chart>
	</div>
</template>

<script>
import { Bar, mixins } from 'vue-chartjs'
const { reactiveProp } = mixins

export default {
	extends: Bar,
	mixins: [reactiveProp],
	props: ['chartdata', 'options'],
	components: {
		BarChart
	},
	data () {
		return {
			datacollection: null,
			options: {
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
					text: '',
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
	},
	mounted () {
		this.renderChart(this.chartData, this.options)
	}
}
</script>