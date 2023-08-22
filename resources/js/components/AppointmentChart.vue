<script>
import { Line, mixins } from "vue-chartjs";

export default {
    extends: Line,
    mixins: [mixins.reactiveProp],
    props: ["options"],
    mounted() {
        this.renderChart(this.chartData, this.options);
    },
};
</script>

<template>
    <line-chart :chart-data="chartData" :options="options" />
</template>
