<template>
    <div ref="apexChart">
    </div>
</template>

<script>
import ApexCharts from 'apexcharts';

export default {
    name: 'ApexChart',

    props: {
        options: Object,
        data: Array,
    },

    data() {
        return {
            chart: {},
        };
    },

    mounted() {
        console.log("Apex Chart mounted.");

        var options1 = {
          chart: {
            type: "line",
            height: 60,
            sparkline: {
              enabled: !0
            }
          },
          series: [{
              data: this.data
          }],
          stroke: {
            width: 2,
            curve: "smooth"
          },
          markers: {
            size: 0
          },
          colors: ["#727cf5"],
          tooltip: {
            fixed: {
              enabled: !1
            },
            x: {
              show: !1
            },
            y: {
              title: {
                formatter: function(e) {
                  return "&euro;"
                }
              }
            },
            marker: {
              show: !1
            }
          }
        };

        console.log("Options:");
        console.log(this.options);

        this.chart = new ApexCharts(this.$el, this.options);
        this.chart.render();
    },
};
</script>
