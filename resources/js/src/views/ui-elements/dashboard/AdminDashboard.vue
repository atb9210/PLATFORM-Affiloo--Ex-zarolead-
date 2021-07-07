<template>
  <div id="dashboard-analytics">
    <p class="flex items-center mb-4">
      <vs-button class="mr-2" color="primary" type="filled" icon-pack="feather" icon="icon-filter" @click="showFilters = !showFilters"></vs-button>
    </p>

    <div v-if="showFilters" class="mb-12">
      <div class="vx-row mb-2">
        <div class="vx-col w-3-12">
          <vs-button class="mr-3 mb-2" @click="filterDatesShortcut('today')" icon-pack="feather" icon="icon-calendar">Today</vs-button>
        </div>
        <div class="vx-col w-3-12">
          <vs-button class="mr-3 mb-2" @click="filterDatesShortcut('yesterday')" icon-pack="feather" icon="icon-calendar">Yesterday</vs-button>
        </div>
        <div class="vx-col w-3-12">
          <vs-button class="mr-3 mb-2" @click="filterDatesShortcut('this_month')" icon-pack="feather" icon="icon-calendar">This Month</vs-button>
        </div>
        <div class="vx-col w-3-12">
          <vs-button class="mr-3 mb-2" @click="filterDatesShortcut('last_month')" icon-pack="feather" icon="icon-calendar">Last Month</vs-button>
        </div>
      </div>

      <div class="vx-row mb-2">
        <div class="vx-col md:w-1/2 lg:w-1/4">
          <label>From</label>
          <vs-input type="date" class="w-full" icon-pack="feather" icon="icon-calendar" icon-no-border v-model="filters.date_from" />
        </div>
        <div class="vx-col md:w-1/2 lg:w-1/4">
          <label>To</label>
          <vs-input type="date" class="w-full" icon-pack="feather" icon="icon-calendar" icon-no-border v-model="filters.date_to" />
        </div>
        <div class="vx-col md:w-full lg:w-1/2">
          <label>Form</label>
          <v-select :options="formOptions" v-model="filters.form" />
        </div>
      </div>
      <div class="vx-row mt-6">
        <div class="vx-col w-full">
          <vs-button class="mr-3 mb-2" @click="applyFilters">Apply</vs-button>
          <vs-button color="warning" type="border" class="mb-2" @click="resetFilters">Reset</vs-button>
        </div>
      </div>
    </div>

    <div class="vx-row">

      <!-- Orders daily chart -->
      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
        <statistics-card-line icon="PackageIcon" :statistic="ordersCount" statisticTitle="Orders" :chartData="ordersSeries" color="danger" type="area"></statistics-card-line>
      </div>

      <!-- Visits daily chart -->
      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
        <statistics-card-line icon="EyeIcon" :statistic="visits" statisticTitle="Visits" :chartData="visitsSeries" color="primary" type="area"></statistics-card-line>
      </div>

      <!-- Leads daily chart -->
      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
        <statistics-card-line icon="UsersIcon" :statistic="leads" statisticTitle="Leads" :chartData="leadsSeries" color="warning" type="area"></statistics-card-line>
      </div>

      <!-- CPA daily chart -->
      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
        <statistics-card-line icon="DollarSignIcon" :statistic="'€ ' + cpa" statisticTitle="CPA" :chartData="cpaSeries" color="success" type="area"></statistics-card-line>
      </div>

      <!-- Cost daily chart -->
      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
        <statistics-card-line icon="AlertCircleIcon" :statistic="'€ ' + cost" statisticTitle="Total Cost" :chartData="costSeries" color="danger" type="area"></statistics-card-line>
      </div>

      <!-- Revenue daily chart -->
      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
        <statistics-card-line icon="DollarSignIcon" :statistic="'€ ' + revenue" statisticTitle="Revenue" :chartData="revenueSeries" color="primary" type="area"></statistics-card-line>
      </div>

      <!-- ROI daily chart -->
      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
        <statistics-card-line icon="PercentIcon" :statistic="roi + '%'" statisticTitle="ROI" :chartData="roiSeries" color="warning" type="area"></statistics-card-line>
      </div>

      <!-- Profit daily chart -->
      <div class="vx-col w-full sm:w-1/2 md:w-1/2 lg:w-1/4 xl:w-1/4 mb-base">
        <statistics-card-line icon="DollarSignIcon" :statistic="'€ ' + profit" statisticTitle="Profit" :chartData="profitSeries" color="success" type="area"></statistics-card-line>
      </div>

    </div>

    <div class="vx-row">

      <!-- Monthly overview -->
      <div class="vx-col w-full sm:w-full md:w-full lg:w-full mb-base">
        <vx-card title="Monthly Overview">
          <vue-apex-charts ref="chart" type="line" height="350" :options="lineAreaChartSpline.chartOptions" :series="lineAreaChartSpline.series"></vue-apex-charts> 
        </vx-card>
      </div>

      <!-- Goals overview -->
      <!--
      <div class="vx-col w-full lg:w-1/2 mb-base">
        <vx-card title="Goals Overview (fake)">
            <template slot="actions">
                <change-time-duration-dropdown />
            </template>
            <div slot="no-body">
                <vue-apex-charts type="radialBar" height="420" :options="analyticsData.productOrdersRadialBar.chartOptions" :series="productsOrder.series" />
            </div>
            <ul>
                <li v-for="orderData in productsOrder.analyticsData" :key="orderData.orderType" class="flex mb-3 justify-between">
                    <span class="flex items-center">
                            <span class="inline-block h-4 w-4 rounded-full mr-2 bg-white border-3 border-solid" :class="`border-${orderData.color}`"></span>
                            <span class="font-semibold">{{ orderData.orderType }}</span>
                    </span>
                    <span>{{ orderData.counts }}</span>
                </li>
            </ul>
        </vx-card>
      </div>
      -->

    </div>

    <div class="vx-row">

      <!-- Top products -->
      <div class="vx-col lg:w-1/2 mb-base">
        <vx-card title="Top Products">
          <div slot="no-body" class="mt-4">
            <vs-table stripe :data="productsReports" max-items="10" pagination>

              <template slot="thead">
                <vs-th sort-key="product">Product</vs-th>
                <vs-th sort-key="leads">Leads</vs-th>
                <vs-th sort-key="total_cost">Cost</vs-th>
                <vs-th sort-key="total_commission">Commission</vs-th>
                <vs-th sort-key="profit">Profit</vs-th>
                <vs-th sort-key="roi">ROI</vs-th>
              </template>

              <template slot-scope="{data}">
                <vs-tr :key="indextr" v-for="(tr, indextr) in data">

                  <vs-td class="column-no-padding" :data="data[indextr].product">
                    {{data[indextr].product}}
                  </vs-td>

                  <vs-td class="column-no-padding" :data="data[indextr].leads">
                    {{data[indextr].leads}}
                  </vs-td>

                  <vs-td class="column-no-padding" :data="data[indextr].total_commission">
                    {{data[indextr].total_commission.toFixed(2)}} €
                  </vs-td>

                  <vs-td class="column-no-padding" :data="data[indextr].total_cost">
                    {{data[indextr].total_cost.toFixed(2)}} €
                  </vs-td>

                  <vs-td class="column-no-padding" :data="data[indextr].profit">
                    <vs-chip transparent :color="(data[indextr].profit > 0) ? 'success' : ((data[indextr].profit < 0) ? 'danger' : '')">{{data[indextr].profit.toFixed(2)}} €</vs-chip>
                  </vs-td>

                  <vs-td class="column-no-padding" :data="data[indextr].roi">
                    <vs-chip transparent :color="(data[indextr].roi > 0) ? 'success' : ((data[indextr].profit < 0) ? 'danger' : '')">{{data[indextr].roi.toFixed(2)}}%</vs-chip>
                  </vs-td>

                </vs-tr>
              </template>
            </vs-table>
          </div>
        </vx-card>
      </div>

      <!-- Orders -->
      <div class="vx-col lg:w-1/2 mb-base">
        <vx-card title="Orders">
          <div slot="no-body" class="mt-4">
            <vs-table stripe :data="orders" max-items="10" pagination>

              <template slot="thead">
                <vs-th>Date</vs-th>
                <vs-th>Product</vs-th>
                <vs-th>Qty</vs-th>
                <vs-th>Revenue</vs-th>
                <vs-th>Status</vs-th>
              </template>

              <template slot-scope="{data}">
                <vs-tr :key="indextr" v-for="(tr, indextr) in data">

                  <vs-td class="column-no-padding" :data="data[indextr].created_at">
                    <span>{{formatDate(new Date(data[indextr].created_at))}}</span>
                  </vs-td>

                  <vs-td class="column-no-padding" :data="data[indextr].product">
                    <span>{{data[indextr].product}}</span>
                  </vs-td>

                  <vs-td class="column-no-padding" :data="data[indextr].quantity">
                    <span>{{data[indextr].quantity}}</span>
                  </vs-td>

                  <vs-td class="column-no-padding" :data="data[indextr].commission">
                    <span>{{data[indextr].commission}} €</span>
                  </vs-td>

                  <vs-td class="column-no-padding" :data="data[indextr].status">
                    <span class="flex items-center px-2 py-1 rounded">
                      <vs-chip :color="statusColors[data[indextr].status]">{{data[indextr].status}}</vs-chip>
                    </span>
                  </vs-td>

                </vs-tr>
              </template>
            </vs-table>
          </div>
        </vx-card>
      </div>

    </div>

    <div class="vx-row">

      <!-- Advertisers reports -->
      <div class="vx-col lg:w-1/2 mb-base">
        <vx-card title="Advertisers">
          <div slot="no-body" class="mt-4">
            <vs-table stripe :data="advertisersReports" max-items="10" pagination>

              <template slot="thead">
                <vs-th sort-key="user">Advertiser</vs-th>
                <vs-th sort-key="leads">Leads</vs-th>
                <vs-th sort-key="total_cost">Cost</vs-th>
                <vs-th sort-key="commission">Revenue</vs-th>
                <vs-th sort-key="approved_commission">Approved</vs-th>
                <vs-th sort-key="approved_commission_percentage">% Approved</vs-th>
                <vs-th sort-key="net_profit">Net Profit</vs-th>
              </template>

              <template slot-scope="{data}">
                <vs-tr :key="indextr" v-for="(tr, indextr) in data">

                  <vs-td class="column-no-padding" :data="data[indextr].user">
                    {{data[indextr].user}}
                  </vs-td>

                  <vs-td class="column-no-padding" :data="data[indextr].leads">
                    {{data[indextr].leads}}
                  </vs-td>

                  <vs-td class="column-no-padding" :data="data[indextr].total_cost">
                    {{data[indextr].total_cost.toFixed(2)}} €
                  </vs-td>

                  <vs-td class="column-no-padding" :data="data[indextr].commission">
                    {{data[indextr].commission.toFixed(2)}} €
                  </vs-td>

                  <vs-td class="column-no-padding" :data="data[indextr].approved_commission">
                    <span>{{data[indextr].approved_commission.toFixed(2)}} €</span>
                  </vs-td>

                  <vs-td class="column-no-padding" :data="data[indextr].approved_commission_percentage">
                    {{data[indextr].approved_commission_percentage.toFixed(2)}} %
                  </vs-td>

                  <vs-td class="column-no-padding" :data="data[indextr].net_profit">
                    <vs-chip transparent :color="(data[indextr].net_profit > 0) ? 'success' : ((data[indextr].net_profit < 0) ? 'danger' : '')">{{data[indextr].net_profit.toFixed(2)}} €</vs-chip>
                  </vs-td>

                </vs-tr>
              </template>
            </vs-table>
          </div>
        </vx-card>
      </div>

      <!-- Mediabuyers reports -->
      <div class="vx-col lg:w-1/2 mb-base">
        <vx-card title="Mediabuyers">
          <div slot="no-body" class="mt-4">
            <vs-table stripe :data="mediabuyersReports" max-items="10" pagination>

              <template slot="thead">
                <vs-th sort-key="user">User</vs-th>
                <vs-th sort-key="leads">Leads</vs-th>
                <vs-th sort-key="commission">Commission</vs-th>
                <vs-th sort-key="cost">Cost+Extra</vs-th>
                <!--<vs-th sort-key="extra_cost">Extra</vs-th>-->
                <vs-th sort-key="profit">Profit</vs-th>
                <vs-th sort-key="roi">ROI</vs-th>
                <vs-th>Approv</vs-th>
                <vs-th>Net</vs-th>
              </template>

              <template slot-scope="{data}">
                <vs-tr :key="indextr" v-for="(tr, indextr) in data">

                  <vs-td class="column-no-padding" :data="data[indextr].user">
                    <span style="font-size: 0.8em;">{{data[indextr].user}}</span>
                  </vs-td>

                  <vs-td class="column-no-padding" :data="data[indextr].leads">
                    {{data[indextr].leads}}
                  </vs-td>

                  <vs-td class="column-no-padding" :data="data[indextr].commission">
                    {{data[indextr].commission.toFixed(2)}} €
                  </vs-td>

                  <vs-td class="column-no-padding" :data="data[indextr].cost">
                    {{(data[indextr].cost + data[indextr].extra_cost).toFixed(2)}} €
                  </vs-td>

                  <!--
                  <vs-td class="column-no-padding" :data="data[indextr].extra_cost">
                    {{data[indextr].extra_cost.toFixed(2)}} €
                  </vs-td>
                  -->

                  <vs-td class="column-no-padding" :data="data[indextr].profit">
                    <vs-chip transparent :color="(data[indextr].profit > 0) ? 'success' : ((data[indextr].profit < 0) ? 'danger' : '')">€ {{data[indextr].profit.toFixed(2)}}</vs-chip>
                  </vs-td>

                  <vs-td class="column-no-padding" :data="data[indextr].roi">
                    <vs-chip transparent :color="(data[indextr].roi > 0) ? 'success' : ((data[indextr].profit < 0) ? 'danger' : '')">{{data[indextr].roi.toFixed(2)}}%</vs-chip>
                  </vs-td>

                  <vs-td class="column-no-padding">
                    0 €
                  </vs-td>

                  <vs-td class="column-no-padding">
                    0 €
                  </vs-td>

                </vs-tr>
              </template>
            </vs-table>
          </div>
        </vx-card>
      </div>

    </div>

    <div class="vx-row">

      <!-- Top traffic sources -->
      <div class="vx-col lg:w-1/2 mb-base">
        <vx-card title="Top Traffic Sources">
          <div slot="no-body" class="mt-4">
            <vs-table stripe :data="trafficSourcesReports" max-items="10" pagination>

              <template slot="thead">
                <vs-th sort-key="traffic_source">Source</vs-th>
                <vs-th sort-key="leads">Leads</vs-th>
                <vs-th sort-key="total_cost">Cost</vs-th>
                <vs-th sort-key="total_commission">Commission</vs-th>
                <vs-th sort-key="profit">Profit</vs-th>
                <vs-th sort-key="roi">ROI</vs-th>
              </template>

              <template slot-scope="{data}">
                <vs-tr :key="indextr" v-for="(tr, indextr) in data">

                  <vs-td class="column-no-padding" :data="data[indextr].product">
                    {{data[indextr].product}}
                  </vs-td>

                  <vs-td class="column-no-padding" :data="data[indextr].leads">
                    {{data[indextr].leads}}
                  </vs-td>

                  <vs-td class="column-no-padding" :data="data[indextr].total_commission">
                    {{data[indextr].total_commission.toFixed(2)}} €
                  </vs-td>

                  <vs-td class="column-no-padding" :data="data[indextr].total_cost">
                    {{data[indextr].total_cost.toFixed(2)}} €
                  </vs-td>

                  <vs-td class="column-no-padding" :data="data[indextr].profit">
                    <vs-chip transparent :color="(data[indextr].profit > 0) ? 'success' : ((data[indextr].profit < 0) ? 'danger' : '')">{{data[indextr].profit.toFixed(2)}} €</vs-chip>
                  </vs-td>

                  <vs-td class="column-no-padding" :data="data[indextr].roi">
                    <vs-chip transparent :color="(data[indextr].roi > 0) ? 'success' : ((data[indextr].profit < 0) ? 'danger' : '')">{{data[indextr].roi.toFixed(2)}}%</vs-chip>
                  </vs-td>

                </vs-tr>
              </template>
            </vs-table>
          </div>
        </vx-card>
      </div>

    </div>

  </div>
</template>

<script>
import VueApexCharts from 'vue-apexcharts'
import StatisticsCardLine from '@/components/statistics-cards/StatisticsCardLine.vue'
import analyticsData from '../card/analyticsData.js'
import ChangeTimeDurationDropdown from '@/components/ChangeTimeDurationDropdown.vue'
import VxTimeline from '@/components/timeline/VxTimeline'
import vSelect from 'vue-select'

export default {
  components: {
    VueApexCharts,
    StatisticsCardLine,
    ChangeTimeDurationDropdown,
    VxTimeline,
    'v-select': vSelect,
  },
  data () {
    return {
      objects: [],

      showFilters: false,
      filters: {
        date_from: '',
        date_to: '',
        form: '',
      },
      formOptions: [],

      orders: [],
      ordersCount: 0,

      visits: 0,
      leads: 0,
      cpa: 0,
      cost: 0,
      revenue: 0,
      profit: 0,
      roi: 0,

      maxNumDots: 10,
      ordersSeries:  [{ data: [1, 2, 3, 4, 4, 4, 4], name: 'Orders' }],
      visitsSeries:  [{ data: [1, 2, 3, 4, 5], name: 'Visits' }],
      leadsSeries:   [{ data: [4, 3, 2, 1], name: 'Leads' }],
      cpaSeries:     [{ data: [2, 4, 6, 4], name: 'CPA' }],
      costSeries:    [{ data: [30, 35, 48, 7], name: 'Cost' }],
      revenueSeries: [{ data: [30, 35, 48, 7], name: 'Revenue' }],
      profitSeries:  [{ data: [30, 35, 48, 7], name: 'Profit' }],
      roiSeries:     [{ data: [30, 35, 48, 7], name: 'ROI' }],

      productsReports: [],
      trafficSourcesReports: [],
      advertisersReports: [],
      mediabuyersReports: [],

      // TODO: Are these still useful?
      monthlyVisits: [],
      monthlyLeads: [],
      monthlyCost: [],
      monthlyCpa: [],
      lastMonths: 8,

      maxChartLines: 10,
      themeColors: ['#EA5455', '#7367F0', '#28C76F', '#FF9F43', '#1E1E1E'],
      lineAreaChartSpline: {
        series: [],
        chartOptions: {
          chart: {
            type: 'line',
          },
          dataLabels: {
            enabled: false
          },
          stroke: {
            curve: 'smooth'
          },
          colors: this.themeColors,
          xaxis: {
            categories: [],
          },
          tooltip: {
            x: {
              format: 'yyyy-MM-dd'
            }
          },
        }
      },

      statusColors: {
        'waiting': 'primary',
        'confirmed': 'success',
        'not responding': 'warning',
        'refused': 'danger',
        'not sent': 'danger',
        'double': 'warning',
      },

      thisMonthReports: [],

      // TODO: delete all these variables below
      checkpointReward: {},
      subscribersGained: {},
      ordersRecevied: {},
      salesBarSession: {},
      supportTracker: {},
      productsOrder: {},
      salesRadar: {},

      timelineData: [
        {
          color: 'primary',
          icon: 'PlusIcon',
          title: 'Client Meeting',
          desc: 'Bonbon macaroon jelly beans gummi bears jelly lollipop apple',
          time: '25 mins Ago'
        },
        {
          color: 'warning',
          icon: 'MailIcon',
          title: 'Email Newsletter',
          desc: 'Cupcake gummi bears soufflé caramels candy',
          time: '15 Days Ago'
        },
        {
          color: 'danger',
          icon: 'UsersIcon',
          title: 'Plan Webinar',
          desc: 'Candy ice cream cake. Halvah gummi bears',
          time: '20 days ago'
        },
        {
          color: 'success',
          icon: 'LayoutIcon',
          title: 'Launch Website',
          desc: 'Candy ice cream cake. Halvah gummi bears Cupcake gummi bears soufflé caramels candy.',
          time: '25 days ago'
        },
        {
          color: 'primary',
          icon: 'TvIcon',
          title: 'Marketing',
          desc: 'Candy ice cream cake. Halvah gummi bears Cupcake gummi bears.',
          time: '28 days ago'
        }
      ],

      analyticsData,
      dispatchedOrders: [],
    }
  },
  methods: {
    loadDailyCharts() {
      let dateObj = new Date()
      let startDay = new Date(this.filters.date_from)
      let endDay = new Date(this.filters.date_to)

      let diffTime = Math.abs(endDay - startDay);
      let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
      let numDots = diffDays + 1

      let currentDay = new Date(this.filters.date_from)

      let categories = []
      let objIndex = this.objects.length - 1

      let visitsSeries = []
      let leadsSeries = []
      let cpaSeries = []
      let costSeries = []
      let revenueSeries = []
      let profitSeries = []
      let roiSeries = []

      this.visits = 0
      this.leads = 0
      this.cpa = 0
      this.cost = 0.0
      this.revenue = 0.0
      this.profit = 0.0
      this.roi = 0.0

      for(let i = 0; i < numDots; i++) {
        let formattedDate = this.formatDate(currentDay)

        let visits = 0
        let leads = 0
        let cpa = 0.0
        let cost = 0.0
        let revenue = 0.0
        let profit = 0.0
        let roi = 0.0

        if (objIndex >= 0 && objIndex < this.objects.length && this.objects[objIndex].day == formattedDate) {
          visits = parseInt(this.objects[objIndex].visits)
          leads = parseInt(this.objects[objIndex].leads)
          cost = (this.filters.form != '' && this.filters.form.id != 0) ? parseFloat(this.objects[objIndex].total_cost) : parseFloat(this.objects[objIndex].cost)

          cpa = ((leads > 0) ? (cost / leads) : cost)

          revenue = parseFloat(this.objects[objIndex].commission)
          profit = parseFloat(this.objects[objIndex].profit)
          roi = ((cost > 0) ? (profit / cost) : 9.99) * 100

          objIndex--
        }

        this.visits += visits
        this.leads += leads
        this.cost += cost
        this.revenue += revenue
        this.profit += profit

        cpa = cpa.toFixed(2)
        cost = cost.toFixed(2)
        revenue = revenue.toFixed(2)
        profit = profit.toFixed(2)
        roi = roi.toFixed(2)

        visitsSeries.push(visits)
        leadsSeries.push(leads)
        cpaSeries.push(cpa)
        costSeries.push(cost)
        revenueSeries.push(revenue)
        profitSeries.push(profit)
        roiSeries.push(roi)

        currentDay.setDate(currentDay.getDate() + 1)
      }
      if (numDots == 1) {
        visitsSeries.push(visitsSeries[0])
        leadsSeries.push(leadsSeries[0])
        cpaSeries.push(cpaSeries[0])
        costSeries.push(costSeries[0])
        revenueSeries.push(revenueSeries[0])
        profitSeries.push(profitSeries[0])
        roiSeries.push(roiSeries[0])
      }

      this.roi = ((this.cost > 0) ? (this.profit / this.cost) : 9.99) * 100
      this.cpa = ((this.leads > 0) ? (this.cost / this.leads) : this.cost)

      this.cpa = this.cpa.toFixed(2)
      this.cost = this.cost.toFixed(2)
      this.revenue = this.revenue.toFixed(2)
      this.profit = this.profit.toFixed(2)
      this.roi = this.roi.toFixed(2)

      this.visitsSeries = [{ data: visitsSeries, name: 'Visits' }]
      this.leadsSeries = [{ data: leadsSeries, name: 'Leads' }]
      this.cpaSeries = [{ data: cpaSeries, name: 'CPA' }]
      this.costSeries = [{ data: costSeries, name: 'Cost' }]
      this.revenueSeries = [{ data: revenueSeries, name: 'Revenue' }]
      this.profitSeries = [{ data: profitSeries, name: 'Profit' }]
      this.roiSeries = [{ data: roiSeries, name: 'ROI' }]
    },
    loadMonthlyCharts() {
      // X axis
      let categories = []

      // TODO: to delete
      /*
      let monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
      ]
      let dateObj = new Date()
      for(let monthIndex = this.lastMonths - 1; monthIndex >= 0; monthIndex--) {
        categories.push(monthNames[dateObj.getMonth() - monthIndex])
      }
      */

      let costStats = []
      let revenueStats = []
      let profitStats = []

      let dateObj = new Date()
      let daysInMonth = new Date(dateObj.getFullYear(), dateObj.getMonth() + 1, 0).getDate();
      dateObj.setDate(1)
      for (let dayIndex = 0; dayIndex < daysInMonth; dayIndex++) {
        let formattedDate = this.formatDate(dateObj)
        categories.push(formattedDate)

        costStats.push(0)
        revenueStats.push(0)
        profitStats.push(0)

        for (let i = 0; i < this.thisMonthReports.length; i++) {
          let reports = this.thisMonthReports[i]
          if (reports.day == formattedDate) {
            let cost = parseFloat(reports.cost) + parseFloat(reports.extra_cost)
            let revenue = parseFloat(reports.commission)
            let profit = parseFloat(reports.profit)
            costStats[dayIndex] = cost
            revenueStats[dayIndex] = revenue
            profitStats[dayIndex] = profit
          }
        }

        dateObj.setDate(dateObj.getDate() + 1)
      }

      this.lineAreaChartSpline.chartOptions = {
        chart: {
          type: 'line',
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          curve: 'smooth'
        },
        colors: this.themeColors,
        xaxis: {
          categories: categories,
        },
        tooltip: {
          x: {
            format: 'yyyy-MM-dd'
          }
        },
      }

      // Y axis
      this.lineAreaChartSpline.series = []
      let series = []
      series.push({
        name: "cost",
        data: costStats,
      })
      series.push({
        name: "revenue",
        data: revenueStats,
      })
      series.push({
        name: "profit",
        data: profitStats,
      })

      // TODO: to delete
      /*
      series.push({
        name: "visits",
        data: this.monthlyVisits,
      })
      series.push({
        name: "leads",
        data: this.monthlyLeads,
      })
      series.push({
        name: "Cost",
        data: this.monthlyCost,
      })
      series.push({
        name: "visits",
        data: this.monthlyCpa,
      })
      */

      this.lineAreaChartSpline.series = series
    },
    formatDate(dateObj) {
      let dd = String(dateObj.getDate()).padStart(2, '0');
      let mm = String(dateObj.getMonth() + 1).padStart(2, '0'); //January is 0!
      let yyyy = dateObj.getFullYear();
      
      let formattedDate = yyyy + '-' + mm + '-' + dd;
      return formattedDate;
    },
    resetFilters() {
      this.filters = {
        date_from: '',
        date_to: '',
        form: '',
      }
      this.filterDatesShortcut('this_month')
    },
    async applyFilters() {
      // Retrieve user's report
      this.$vs.loading()

      let headers = {
        Authorization: 'Bearer ' + localStorage.getItem('accessToken')
      }

      let data = {}
      if (this.filters.date_from != '') {
        data['filter_date_from'] = this.filters.date_from
      }
      if (this.filters.date_to != '') {
        data['filter_date_to'] = this.filters.date_to
      }

      // Retrieve user's report
      if (this.filters.form != '' && this.filters.form.id != 0) {
        const userReportResponse = await this.$http.get('/api/v1/forms/' + this.filters.form.id + '/stats', { headers, params: data })
        this.objects = userReportResponse.data.data
        this.loadDailyCharts()
      } else {
        const myReportsResponse = await this.$http.get('/api/v1/my-reports', { headers, params: data })
        this.objects = myReportsResponse.data.data
        for (let i = 0; i < this.objects.length; i++) {
          if(this.objects[i].notes === null) {
            this.objects[i].notes = ''
          }
        }
        this.loadDailyCharts()
      }

      // Retrieve products reports
      this.productsReports = []
      const productsReportsResponse = await this.$http.get('/api/v1/reports/products', { headers, params: data })
      for (let i = 0; i < productsReportsResponse.data.data.length; i++) {
        let obj = productsReportsResponse.data.data[i]
        let total_commission = parseFloat(obj.total_commission)
        let total_cost = parseFloat(obj.total_cost)
        let profit = total_commission - total_cost
        let roi = (total_cost > 0) ? ((profit / total_cost)*100) : ((profit > 0) ? 9999.99 : 0)
        this.productsReports.push({
          product: obj.product.name,
          visits: obj.visits,
          leads: obj.leads,
          total_commission: total_commission,
          total_cost: total_cost,
          profit: profit,
          roi: roi,
        })
      }
      this.productsReports.sort(function(a, b) {
        if (a.roi < b.roi) return 1
        if (a.roi > b.roi) return -1
        return 0
      })

      // Retrieve traffic sources reports
      this.trafficSourcesReports = []
      const tsReportsResponse = await this.$http.get('/api/v1/reports/traffic-sources', { headers, params: data })
      for (let i = 0; i < tsReportsResponse.data.data.length; i++) {
        let obj = tsReportsResponse.data.data[i]
        let total_commission = parseFloat(obj.total_commission)
        let total_cost = parseFloat(obj.total_cost)
        let profit = total_commission - total_cost
        let roi = (total_cost > 0) ? ((profit / total_cost)*100) : ((profit > 0) ? 9999.99 : 0)
        this.trafficSourcesReports.push({
          traffic_source: obj.traffic_source.name,
          visits: obj.visits,
          leads: obj.leads,
          total_commission: total_commission,
          total_cost: total_cost,
          profit: profit,
          roi: roi,
        })
      }
      this.trafficSourcesReports.sort(function(a, b) {
        if (a.roi < b.roi) return 1
        if (a.roi > b.roi) return -1
        return 0
      })

      // Retrieve advertisers reports
      let d = new Date(data['filter_date_from'])
      let advertisersReportsData = {
        month: d.getMonth() + 1,
        year: d.getFullYear(),
      }
      this.advertisersReports = []
      const advertisersReportsResponse = await this.$http.get('/api/v1/reports/advertisers', { headers, params: advertisersReportsData })
      for (let i = 0; i < advertisersReportsResponse.data.data.length; i++) {
        let obj = advertisersReportsResponse.data.data[i]
        let total_cost = parseFloat(obj.total_cost)
        let commission = parseFloat(obj.commission)
        let approved_commission = parseFloat(obj.approved_commission)
        let approved_commission_percentage = parseFloat((commission > 0) ? ((approved_commission / commission)*100) : 0)
        let net_profit = approved_commission - total_cost
        this.advertisersReports.push({
          id: obj.id,
          user: obj.user.name,
          month: obj.month,
          year: obj.year,
          visits: obj.visits,
          leads: obj.leads,
          total_cost: total_cost,
          commission: commission,
          approved_commission: approved_commission,
          approved_commission_percentage: approved_commission_percentage,
          net_profit: net_profit,
        })
      }

      // Retrieve mediabuyers reports
      data['filter_roles'] = [3, 7]
      this.mediabuyersReports = []
      const mediabuyersReportsResponse = await this.$http.get('/api/v1/reports/users', { headers, params: data })
      for (let i = 0; i < mediabuyersReportsResponse.data.data.length; i++) {
        let obj = mediabuyersReportsResponse.data.data[i]
        let leads = parseInt(obj.leads)
        let cost = parseFloat(obj.cost)
        let extra_cost = parseFloat(obj.extra_cost)
        let commission = parseFloat(obj.commission)
        let profit = parseFloat(obj.profit)
        let roi = (((cost + extra_cost) > 0) ? (profit / (cost + extra_cost)) : 9.99) * 100
        this.mediabuyersReports.push({
          user: obj.user.email,
          leads: leads,
          commission: commission,
          cost: cost,
          extra_cost: extra_cost,
          profit: profit,
          roi: roi,
        })
      }

      this.$vs.loading.close()
    },
    filterDatesShortcut(period) {
      let invalidPeriod = false;

      switch (period) {
        case 'today': {
          let today = new Date();
          this.filters.date_from = this.formatDate(today);
          this.filters.date_to = this.formatDate(today);
        } break;
        case 'yesterday': {
          let dateObj = new Date();
          dateObj.setDate(dateObj.getDate() - 1);
          this.filters.date_from = this.formatDate(dateObj);
          this.filters.date_to = this.formatDate(dateObj);
        } break;
        case 'this_month': {
          let dateObj = new Date();
          let fromObj = new Date(dateObj.getFullYear(), dateObj.getMonth(), 1);
          let toObj = new Date(dateObj.getFullYear(), dateObj.getMonth() + 1, 0);
          this.filters.date_from = this.formatDate(fromObj);
          this.filters.date_to = this.formatDate(toObj);
        } break;
        case 'last_month': {
          let dateObj = new Date();
          let fromObj = new Date(dateObj.getFullYear(), dateObj.getMonth() - 1, 1);
          let toObj = new Date(dateObj.getFullYear(), dateObj.getMonth(), 0);
          this.filters.date_from = this.formatDate(fromObj);
          this.filters.date_to = this.formatDate(toObj);
        } break;
        default: {
          invalidPeriod = true;
        } break;
      }

      if(!invalidPeriod) {
        this.applyFilters();
      }
    },
  },
  async created() {
    this.resetFilters()

    let headers = {
      Authorization: 'Bearer ' + localStorage.getItem('accessToken')
    }

    // Retrieve forms
    this.$vs.loading()
    this.$http.get('/api/v1/forms', { headers })
      .then((response) => {
        this.formOptions = []
        this.formOptions.push({
          id: 0,
          label: 'All forms',
        })
        response.data.data.forEach((form) => {
          this.formOptions.push({
            id: form.id,
            label: form.name,
          })
        })
        this.$vs.loading.close()
      })
      .catch((error) => {
        this.$vs.loading.close()
      })

    // Retrieve last 'lastMonths' month monthly reports (including current month)
    // TODO: to delete (no need to pick month by month reports anymore???)
    this.$vs.loading()
    let todayDateObj = new Date();
    for (let monthIndex = this.lastMonths - 1; monthIndex >= 0; monthIndex--) {
      let fromObj = new Date(todayDateObj.getFullYear(), todayDateObj.getMonth() - monthIndex, 1);
      let toObj = new Date(todayDateObj.getFullYear(), todayDateObj.getMonth() + 1 - monthIndex, 0);
      let data = {
        filter_date_from: this.formatDate(fromObj),
        filter_date_to: this.formatDate(toObj),
      }

      this.$http.get('/api/v1/my-reports', { headers, params: data })
        .then((response) => {
          let reports = response.data.data
          this.thisMonthReports = reports
          let visits = 0
          let leads = 0
          let cost = 0.0
          let cpa = 0.0
          for (let i = 0; i < reports.length; i++) {
            visits += parseInt(reports[i].visits)
            leads += parseInt(reports[i].leads)
            cost += parseFloat(reports[i].cost)
          }
          cpa = ((leads > 0) ? (cost / leads) : (cost))

          cost = cost.toFixed(2)
          cpa = cpa.toFixed(2)

          // TODO: Are these still useful?
          this.monthlyVisits.push(visits)
          this.monthlyLeads.push(leads)
          this.monthlyCost.push(cost)
          this.monthlyCpa.push(cpa)

          if (monthIndex == 0) {
            this.loadMonthlyCharts()
            this.$vs.loading.close()
          }
        })
        .catch((error) => {
          this.$vs.loading.close()
          this.$vs.notify({
            title: 'Error',
            text: error.message,
            iconPack: 'feather',
            icon: 'icon-alert-circle',
            color: 'danger'
          })
        })
    }

    // Fetch orders of the current user
    let fromObj = new Date(todayDateObj.getFullYear(), todayDateObj.getMonth(), 1);
    let toObj = new Date(todayDateObj.getFullYear(), todayDateObj.getMonth() + 1, 0);
    let data = {
      filter_date_from: this.formatDate(fromObj),
      filter_date_to: this.formatDate(toObj),
    }

    const ordersResponse = await this.$http.get('/api/v1/orders', { headers: { Authorization: 'Bearer ' + localStorage.getItem('accessToken') }, params: data })
    this.orders = ordersResponse.data.data
    this.ordersCount = this.orders.length

    // Calculate orders stats
    let ordersStats = {}
    let dateObj = new Date()
    let daysInMonth = new Date(dateObj.getFullYear(), dateObj.getMonth() + 1, 0).getDate();
    dateObj.setDate(1)
    for (let dayIndex = 0; dayIndex < daysInMonth; dayIndex++) {
      ordersStats[this.formatDate(dateObj)] = 0
      dateObj.setDate(dateObj.getDate() + 1)
    }

    for (let i = 0; i < this.orders.length; i++) {
      let order = this.orders[i]
      let day = this.formatDate(new Date(order.created_at))

      ordersStats[day] += 1
    }

    let ordersSeries = []
    for (const [key, value] of Object.entries(ordersStats)) {
      ordersSeries.push(value)
    }
    this.ordersSeries = [{ data: ordersSeries, name: 'Orders' }]

    // TODO: to delete from now on
    //  User Reward Card
    this.$http.get('/api/user/checkpoint-reward')
      .then((response) => { this.checkpointReward = response.data })
      .catch((error)   => { console.log(error) })

      // Subscribers gained - Statistics
    this.$http.get('/api/card/card-statistics/subscribers')
      .then((response) => { this.subscribersGained = response.data })
      .catch((error)   => { console.log(error) })

      // Orders - Statistics
    this.$http.get('/api/card/card-statistics/orders')
      .then((response) => { this.ordersRecevied = response.data })
      .catch((error)   => { console.log(error) })

      // Sales bar - Analytics
    this.$http.get('/api/card/card-analytics/sales/bar')
      .then((response) => { this.salesBarSession = response.data })
      .catch((error)   => { console.log(error) })

      // Support Tracker
    this.$http.get('/api/card/card-analytics/support-tracker')
      .then((response) => { this.supportTracker = response.data })
      .catch((error)   => { console.log(error) })

      // Products Order
    this.$http.get('/api/card/card-analytics/products-orders')
      .then((response) => { this.productsOrder = response.data })
      .catch((error)   => { console.log(error) })

      // Sales Radar
    this.$http.get('/api/card/card-analytics/sales/radar')
      .then((response) => { this.salesRadar = response.data })
      .catch((error)   => { console.log(error) })

      // Dispatched Orders
    this.$http.get('/api/table/dispatched-orders')
      .then((response) => { this.dispatchedOrders = response.data })
      .catch((error)   => { console.log(error) })
  }
}
</script>

<style lang="scss">
.column-no-padding {
  padding-top: 0px !important;
  padding-bottom: 0px !important;
  white-space: nowrap;
}
/*! rtl:begin:ignore */
#dashboard-analytics {
  .greet-user{
    position: relative;

    .decore-left{
      position: absolute;
      left:0;
      top: 0;
    }
    .decore-right{
      position: absolute;
      right:0;
      top: 0;
    }
  }

  @media(max-width: 576px) {
    .decore-left, .decore-right{
      width: 140px;
    }
  }
}
/*! rtl:end:ignore */
</style>
