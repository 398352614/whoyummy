<template>
  <div class="new-home">
    <div class="header-cards">
      <div class="card todo-card">
        <!-- 今日待办事项 -->
        <div class="card-header">{{ $t("Today") }}</div>
        <div class="todo-card-content card-content">
          <div class="todo-card-item">
            <div class="item-content">
              <div class="img">
                <img src="../../assets/img/home-icon/point-icon.png" alt="" />
              </div>
              <div class="right">
                <!-- 待取派路线 -->
                <div class="right-title">{{ $t("PickupRoute") }}</div>
                <div class="number">{{ statisticsData.preparing_tour }}</div>
              </div>
            </div>
          </div>
          <div class="todo-card-item">
            <div class="item-content">
              <div class="img img-bg-2">
                <img
                  src="../../assets/img/home-icon/position-icon.png"
                  alt=""
                />
              </div>
              <div class="right">
                <!-- 待取派站点 -->
                <div class="right-title">
                  {{ $t("PendingDeliverySite") }}
                </div>
                <div class="number">{{ statisticsData.preparing_batch }}</div>
              </div>
            </div>
          </div>
          <div class="todo-card-item">
            <div class="item-content">
              <div class="img img-bg-3">
                <img src="../../assets/img/home-icon/plan-icon.png" alt="" />
              </div>
              <div class="right">
                <!-- 待取派运单 -->
                <div class="right-title">{{ $t("pickDeliveryWaybill") }}</div>
                <div class="number">
                  {{ statisticsData.preparing_tracking_order }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card task-card">
        <div class="card-header">{{ $t("PerformTask") }}</div>
        <div class="task-card-content card-content">
          <div class="task-card-item-left">
            <div>
              <h5>{{ $t("todaytask") }}</h5>
              <span class="import-text">{{ statisticsData.tour }}</span>
            </div>
            <div class="tesk-left-center">
              <h5>{{ $t("inProgress") }}</h5>
              <span class="text-margin">{{ statisticsData.taking_tour }}</span>
            </div>
            <div class="tesk-left-center">
              <h5>{{ $t("completed") }}</h5>
              <span class="text-margin finished-color">{{
                statisticsData.done_tour
              }}</span>
            </div>
          </div>
          <div class="task-card-item-middle"></div>
          <div class="task-card-item-right">
            <h5 class="right-title">{{ $t("exceptionalTask") }}</h5>
            <div class="progress-list">
              <div class="progress-item">
                <el-progress
                  class="progress-circle"
                  :stroke-width="10"
                  type="circle"
                  :percentage="
                    computeNumberPercent(
                      statisticsData.exception_batch,
                      statisticsData.batch
                    )
                  "
                  color="#FF8F8F"
                ></el-progress>
                <h5>{{ $t("Site") }} {{ statisticsData.exception_batch }}</h5>
              </div>
              <div class="progress-item">
                <el-progress
                  class="progress-circle"
                  :stroke-width="10"
                  type="circle"
                  :percentage="
                    computeNumberPercent(
                      statisticsData.exception_tracking_order,
                      statisticsData.tracking_order
                    )
                  "
                  color="#FF8F8F"
                ></el-progress>
                <h5>
                  {{ $t("Waybill") }}
                  {{ statisticsData.exception_tracking_order }}
                </h5>
              </div>
              <div class="progress-item">
                <el-progress
                  class="progress-circle"
                  :stroke-width="10"
                  type="circle"
                  :percentage="
                    computeNumberPercent(
                      statisticsData.exception_package,

                      statisticsData.package
                    )
                  "
                  color="#FF8F8F"
                ></el-progress>
                <h5>
                  {{ $t("Waybill") }} {{ statisticsData.exception_package }}
                </h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="second-cards">
      <div
        v-for="item in secondCardsData"
        @click="item.hasPerm ? $router.push({ name: item.route }) : ''"
        :key="item.img"
      >
        <el-tooltip
          :content="$t('AdministratorActivate')"
          placement="top"
          :disabled="item.hasPerm ? true : false"
        >
          <div
            class="second-card-item"
            :style="{ backgroundColor: item.bgColor }"
          >
            <img :src="item.img" alt="" />
            <div>
              <h5>{{ item.title }}</h5>
              <span>{{ item.subTitle }}</span>
            </div>
          </div>
        </el-tooltip>
      </div>
    </div>
    <div class="total-chart-container">
      <div class="month-total">
        <div class="total-header">
          <div class="total-left">
            <span> {{ $t("ordersthismonth") }} </span>
            <h5>{{ statisticsData.month_order }}</h5>
          </div>
          <div class="total-right">
            <img src="../../assets/img/home-icon/total-icon.png" alt="" />
          </div>
        </div>
        <div class="total-type">
          <div class="total-type-item">
            <span>{{ $t("newOrdersTodayl") }}</span>
            <span>{{ statisticsData.order }}</span>
          </div>
          <div class="total-type-item">
            <span>{{ $t("deliveryOrderToday") }}</span>
            <span>{{ statisticsData.pie_order }}</span>
          </div>
          <div class="total-type-item">
            <span>{{ $t("pickOrderToday") }}</span>
            <span>{{ statisticsData.pickup_order }}</span>
          </div>
          <div class="total-type-item">
            <span>{{ $t("pickDeliveryOrderToday") }}</span>
            <span>{{ statisticsData.pickup_pie_order }}</span>
          </div>
        </div>
      </div>
      <div class="chart">
        <div class="chart-option">
          <div>{{ $t("totalrecentorders") }}</div>
          <div>
            <el-button
              v-for="item in recentDateOption"
              :key="item.name"
              type="text"
              :style="[
                {
                  color: item.value[0] == orderDate[0] ? '#465468' : '#b6bfd0',
                },
              ]"
              @click="onChangeChartDate(item.value)"
              >{{ item.name }}</el-button
            >
            <el-date-picker
              v-model="orderDate"
              @blur="handlerSelectedDate"
              size="mini"
              type="daterange"
              format="yyyy-MM-dd"
              value-format="yyyy-MM-dd"
              range-separator="-"
              :start-placeholder="$t('StartTime')"
              :end-placeholder="$t('EndTime')"
            >
            </el-date-picker>
          </div>
        </div>
        <div id="echart-container" style="height: 202px"></div>
      </div>
    </div>
    <div class="table-card">
      <div class="business-overview">
        <div class="table-card-header">
          <span>{{ $t("cargoOwnerOverview") }}</span>
          <i class="el-icon-more"></i>
        </div>
        <el-table
          max-height="600px"
          height="290"
          :data="merchantData"
          :header-cell-style="{
            backgroundColor: '#F7F7F7',
            color: '#B6BFD0',
            padding: '5px 0',
          }"
          :cell-style="{ color: '#465468', padding: '5px 0' }"
          style="width: calc(100% - 20px); margin: 10px"
        >
          <el-table-column label="#" width="30" type="index"> </el-table-column>
          <el-table-column
            prop="merchant_name"
            :label="$t('Merchant')"
            width="120"
          >
          </el-table-column>
          <el-table-column
            prop="total_order"
            :label="$t('totalorders')"
            width="90"
          >
          </el-table-column>
          <el-table-column
            prop="cancel_order"
            :label="$t('totalfailedupsDeliveries')"
            width="210"
          >
          </el-table-column>
        </el-table>
      </div>
      <div class="reservation-task">
        <div class="table-card-header">
          <span>{{ $t("bookedTask") }}</span>
          <i class="el-icon-more"></i>
        </div>
        <el-table
          max-height="600px"
          height="290"
          :data="reservationData"
          :header-cell-style="{
            backgroundColor: '#F7F7F7',
            color: '#B6BFD0',
            padding: '5px 0',
          }"
          :cell-style="{ color: '#465468', padding: '5px 0' }"
          style="width: calc(100% - 20px); margin: 10px"
        >
          <el-table-column label="#" type="index"> </el-table-column>
          <el-table-column prop="date" :label="$t('appointmentdate')">
          </el-table-column>
          <el-table-column prop="tour" :label="$t('taskQuantity')">
          </el-table-column>
          <el-table-column prop="batch" :label="$t('siteQuantity')">
          </el-table-column>
          <el-table-column prop="tracking_order" :label="$t('waybillquantity')">
          </el-table-column>
        </el-table>
      </div>
    </div>
    <div class="process">
      <div class="process-header">
        <span>{{ $t("flowchart") }}</span>
      </div>
      <div class="process-list">
        <div class="process-1">
          <div class="process-1-content">
            <div class="item">
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('order.store') ? true : false"
              >
                <div
                  @click="
                    hasPerm('order.store') ? goToFlowRouter(1, 'orderAdd') : ''
                  "
                  class="circle-icon"
                >
                  <img
                    src="../../assets/img/home-icon/manual-order.png"
                    alt=""
                  />
                </div>
              </el-tooltip>

              <span>{{ $t("manualEntry") }}</span>
            </div>
            <div class="item">
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('order.import-list') ? true : false"
              >
                <div
                  class="circle-icon"
                  @click="
                    hasPerm('order.import-list')
                      ? goToFlowRouter(2, 'orderUpload')
                      : ''
                  "
                >
                  <img
                    src="../../assets/img/home-icon/import-icon.png"
                    alt=""
                  />
                </div>
              </el-tooltip>
              <span>{{ $t("batchImport") }}</span>
            </div>
            <div class="item">
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="APIDockingManagement"
              >
                <div
                  @click="
                    APIDockingManagement
                      ? goToFlowRouter(3, 'APIDockingManagement')
                      : ''
                  "
                  class="circle-icon"
                >
                  <img src="../../assets/img/home-icon/api.png" alt="" />
                </div>
              </el-tooltip>
              <span>{{ $t("APIdocking") }}</span>
            </div>
          </div>
        </div>

        <div class="process-2">
          <div class="process-2-order">
            <div class="order-icon">
              <div class="arrow">
                <img src="../../assets/img/home-icon/arrow-icon.png" />
              </div>
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="orderManagement"
              >
                <div
                  @click="orderManagement ? goToFlowRouter(4, 'OrderList') : ''"
                  class="circle-icon"
                >
                  <img src="../../assets/img/home-icon/file.png" alt="" />
                </div>
              </el-tooltip>
              <div class="arrow">
                <img src="../../assets/img/home-icon/arrow-icon.png" />
              </div>
            </div>
            <span>{{ $t("OrderManagement") }} </span>
            <div class="order-line">
              <div class="line-1"></div>
              <div class="line-2"></div>
            </div>
            <div class="order-bottom">
              <div>
                <el-tooltip
                  :content="$t('AdministratorActivate')"
                  placement="top"
                  :disabled="packageList"
                >
                  <div
                    @click="packageList ? goToFlowRouter(5, 'PackageList') : ''"
                    class="circle-icon"
                  >
                    <img src="../../assets/img/home-icon/package.png" alt="" />
                  </div>
                </el-tooltip>
                <span>{{ $t("parcelManagement") }}</span>
              </div>
              <div>
                <el-tooltip
                  :content="$t('AdministratorActivate')"
                  placement="top"
                  :disabled="materialList"
                >
                  <div
                    @click="
                      materialList ? goToFlowRouter(6, 'MaterialList') : ''
                    "
                    class="circle-icon"
                  >
                    <img src="../../assets/img/home-icon/material.png" alt="" />
                  </div>
                </el-tooltip>

                <span>{{ $t("materialManagement") }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="process-1 reserve">
          <div class="process-1-content">
            <div class="item">
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="waybillManagement"
              >
                <div
                  @click="
                    waybillManagement
                      ? goToFlowRouter(7, 'WaybillManagement')
                      : ''
                  "
                  class="circle-icon"
                >
                  <img src="../../assets/img/home-icon/file.png" alt="" />
                </div>
              </el-tooltip>
              <span>{{ $t("deliveryWaybill") }}</span>
            </div>
            <div class="item">
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="waybillManagement"
              >
                <div
                  @click="
                    waybillManagement
                      ? goToFlowRouter(7, 'WaybillManagement')
                      : ''
                  "
                  class="circle-icon"
                >
                  <img src="../../assets/img/home-icon/file.png" alt="" />
                </div>
              </el-tooltip>
              <span>{{ $t("pickwaybill") }} </span>
            </div>
            <div class="item">
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="waybillManagement"
              >
                <div
                  @click="
                    waybillManagement
                      ? goToFlowRouter(7, 'WaybillManagement')
                      : ''
                  "
                  class="circle-icon"
                >
                  <img src="../../assets/img/home-icon/file.png" alt="" />
                </div>
              </el-tooltip>
              <span>{{ $t("pickUPDeliveryWaybill") }}</span>
            </div>
          </div>
        </div>

        <div class="process-2 process-2-reserve-container">
          <div class="process-2-order process-2-reserve">
            <div>
              <div class="order-icon">
                <div class="arrow">
                  <img src="../../assets/img/home-icon/arrow-icon.png" />
                </div>
                <el-tooltip
                  :content="$t('AdministratorActivate')"
                  placement="top"
                  :disabled="stopList"
                >
                  <div
                    @click="stopList ? goToFlowRouter(8, 'stationList') : ''"
                    class="circle-icon"
                  >
                    <img src="../../assets/img/home-icon/position.png" alt="" />
                  </div>
                </el-tooltip>
                <div class="arrow">
                  <img src="../../assets/img/home-icon/arrow-icon.png" />
                </div>
              </div>
              <span>{{ $t("sitemanagement") }}</span>
              <div class="order-line" style="visibility: hidden">
                <div class="line-3"></div>
              </div>
              <div class="order-bottom" style="visibility: hidden">
                <div>
                  <div class="circle-icon">
                    <img src="../../assets/img/home-icon/control.png" alt="" />
                  </div>
                  <span>{{ $t("IntelligentLineScheduling") }}</span>
                </div>
              </div>
            </div>

            <div>
              <div class="order-icon">
                <el-tooltip
                  :content="$t('AdministratorActivate')"
                  placement="top"
                  :disabled="lineTasks"
                >
                  <div
                    @click="lineTasks ? goToFlowRouter(9, 'lineTask') : ''"
                    class="circle-icon"
                  >
                    <img
                      src="../../assets/img/home-icon/manual-order.png"
                      alt=""
                    />
                  </div>
                </el-tooltip>
                <div class="arrow">
                  <img src="../../assets/img/home-icon/arrow-icon.png" />
                </div>
              </div>
              <div class="process-2-reserve-task">
                <span>{{ $t("taskManagement") }}</span>
                <div class="order-line">
                  <div class="line-3"></div>
                </div>
                <div class="order-bottom">
                  <div>
                    <el-tooltip
                      :content="$t('AdministratorActivate')"
                      placement="top"
                      :disabled="
                        hasPerm('tour.intelligent-scheduling') ? true : false
                      "
                    >
                      <div
                        @click="
                          hasPerm('tour.intelligent-scheduling')
                            ? goToFlowRouter(10, 'intelligentDispatch')
                            : ''
                        "
                        class="circle-icon"
                      >
                        <img
                          src="../../assets/img/home-icon/manual-order.png"
                          alt=""
                        />
                      </div>
                    </el-tooltip>
                    <span>{{ $t("ntelligentScheduling") }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="process-1 reserve" style="left: 87px">
          <div class="process-1-content">
            <div class="item">
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="driverManagement"
              >
                <div
                  @click="
                    driverManagement
                      ? goToFlowRouter(11, 'driverManagement')
                      : ''
                  "
                  class="circle-icon"
                >
                  <img src="../../assets/img/home-icon/driver.png" alt="" />
                </div>
              </el-tooltip>
              <span>{{ $t("DriverManagement") }}</span>
            </div>
            <div class="item">
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="carManagement"
              >
                <div
                  @click="
                    carManagement ? goToFlowRouter(12, 'carManagement') : ''
                  "
                  class="circle-icon"
                >
                  <img src="../../assets/img/home-icon/setting.png" alt="" />
                </div>
              </el-tooltip>
              <span>{{ $t("VehicleManagement2") }} </span>
            </div>
            <div class="item">
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="intelligentTubeCar"
              >
                <div
                  @click="
                    intelligentTubeCar
                      ? goToFlowRouter(13, 'intelligentTubeCar')
                      : ''
                  "
                  class="circle-icon"
                >
                  <img src="../../assets/img/home-icon/truck.png" alt="" />
                </div>
              </el-tooltip>
              <span>{{ $t("intelligentTubeCar") }} </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import dayjs from "dayjs";

require("echarts/lib/chart/line");
require("echarts/lib/component/tooltip");
const echarts = require("echarts/lib/echarts");

export default {
  name: "newHome",
  data() {
    return {
      orderManagement: false,
      packageList: false,
      materialList: false,
      waybillManagement: false,
      stopList: false,
      lineTasks: false,
      driverManagement: false,
      carManagement: false,
      APIDockingManagement: false,
      intelligentTubeCar: false,
      statisticsData: {},
      orderDate: [
        dayjs().startOf("M").format("YYYY-MM-DD"),
        dayjs().format("YYYY-MM-DD"),
      ],
      recentDateOption: [
        {
          value: [
            dayjs().startOf("week").add(1, "day").format("YYYY-MM-DD"),
            dayjs().format("YYYY-MM-DD"),
          ],
          name: this.$t("ThisWeek"),
        },
        {
          value: [
            dayjs().startOf("week").subtract(6, "day").format("YYYY-MM-DD"),
            dayjs().endOf("week").subtract(6, "day").format("YYYY-MM-DD"),
          ],
          name: this.$t("LastWeek"),
        },
        {
          value: [
            dayjs().startOf("M").format("YYYY-MM-DD"),
            dayjs().format("YYYY-MM-DD"),
          ],
          name: this.$t("ThisMonth"),
        },
        {
          value: [
            dayjs().subtract(1, "M").startOf("M").format("YYYY-MM-DD"),
            dayjs().subtract(1, "M").endOf("M").format("YYYY-MM-DD"),
          ],
          name: this.$t("LastMonth"),
        },
      ],
      secondCardsData: [
        {
          // eslint-disable-next-line global-require
          img: require("../../assets/img/home-icon/create-order.png"),
          bgColor: "#29B583",
          title: this.$t("createOrder"),
          subTitle: this.$t("createOrderViaTRquests"),
          route: "orderAdd",
          hasPerm: this.hasPerm("order.store"),
        },
        {
          // eslint-disable-next-line global-require
          img: require("../../assets/img/home-icon/intelligent-scheduling.png"),
          bgColor: "#5ED6E4",
          title: this.$t("intelligentDispatch"),
          subTitle: this.$t("optimizehemap"),
          route: "intelligentDispatch",
          hasPerm: this.hasPerm("tour.intelligent-scheduling"),
        },
        {
          // eslint-disable-next-line global-require
          img: require("../../assets/img/home-icon/intelligent-card.png"),
          bgColor: "#6F8BD8",
          title: this.$t("zhineng"),
          subTitle: this.$t("viewVehicleLocation"),
          route: "intelligentTubeCar",
          hasPerm: this.hasPerm("car-management.index"),
        },
        {
          // eslint-disable-next-line global-require
          img: require("../../assets/img/home-icon/tracking-search.png"),
          bgColor: "#FFA274",
          title: this.$t("trackquery"),
          subTitle: this.$t("queryTrackByOrde"),
          route: "LogisticsQuery",
          hasPerm: this.hasPerm("trail.index"),
        },
      ],
      tableData: [
        {
          date: "2016-05-02",
          name: "王小虎",
          address: "123",
        },
        {
          date: "2016-05-04",
          name: "王小虎",
          address: "456",
        },
        {
          date: "2016-05-01",
          name: "王小虎",
          address: "789",
        },
        {
          date: "2016-05-03",
          name: "王小虎",
          address: "101",
        },
      ],
      option: {
        tooltip: {
          backgroundColor: "#465468",
          show: true,
          formatter: (params) => `
            ${params.name}<br>
            <span style="display: inline-block;
              width: 5px;
              height: 5px;
              border-radius: 50%;
              background-color: #fff;
              margin: 0 5px 3px;
              "></span>
            ${params.value}`,
        },
        legend: {
          data: [],
        },
        grid: {
          x: 50,
          y: 25,
          x2: 30,
          y2: 35,
        },
        xAxis: {
          axisLine: { show: false },
          axisTick: { show: false },
          type: "category",
          data: [],
          axisLabel: {
            color: "#B6BFD0",
          },
        },
        yAxis: {
          axisLine: { show: false },
          axisTick: { show: false },
          type: "value",
          axisLabel: {
            color: "#B6BFD0",
          },
        },
        series: [
          {
            data: [],
            type: "line",
            color: "#29B583",
            symbol: "emptyCircle",
            symbolSize: 10,
            lineStyle: {
              width: 4,
            },
          },
        ],
      },
      echarLine: {},
      merchantData: [],
      reservationData: [],
      filterFlow: [],
    };
  },
  created() {
    this.getStatisticsFlow();
    this.getStatistics();
    this.getStatisticsMerchant();
    this.getStatisticsReservation();
    this.getAuthTree();
  },
  mounted() {
    this.initOrderTotalChart();
  },
  methods: {
    // 权限
    getAuthTree() {
      this.$api.getAuthTree().then((res) => {
        let menu_list = res.data.menu_list;
        menu_list.map((item) => {
          item.children.map((el) => {
            if (el.children) {
              el.children.forEach((j) => {
                if (j.name == "订单") {
                  this.orderManagement = true;
                } else if (j.name == "包裹") {
                  this.packageList = true;
                } else if (j.name == "材料") {
                  this.materialList = true;
                } else if (j.name == "运单") {
                  this.waybillManagement = true;
                } else if (j.name == "站点") {
                  this.stopList = true;
                } else if (j.name == "任务") {
                  this.lineTasks = true;
                } else if (j.name == "司机") {
                  this.driverManagement = true;
                } else if (j.name == "车辆") {
                  this.carManagement = true;
                } else if (j.name == "API对接") {
                  this.APIDockingManagement = true;
                } else if (j.name == "智能管车") {
                  this.intelligentTubeCar = true;
                }
              });
            }
          });
        });
      });
    },
    goToFlowRouter(name, route) {
      console.log("this.filterFlow");
      console.log(this.filterFlow);
      let filterFlow = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13];
      if (filterFlow.includes(name)) {
        this.$router.push({ name: route });
      }
    },
    getStatisticsFlow() {
      this.$api.getStatisticsFlow().then((res) => {
        if (!res) return;
        res.data.forEach((e) => {
          if (e.permission === 1) {
            this.filterFlow.push(e.name);
          }
        });
      });
    },
    getStatisticsMerchant() {
      this.$api.getStatisticsMerchant().then((res) => {
        if (!res) return;
        this.merchantData = res.data;
      });
    },
    getStatisticsReservation() {
      this.$api.getStatisticsReservation().then((res) => {
        if (!res) return;
        this.reservationData = res.data;
      });
    },
    computeNumberPercent(v, t) {
      if (isNaN(v / t) || !isFinite(v / t)) {
        return 0;
      }
      // eslint-disable-next-line no-mixed-operators
      // 转小数
      return parseFloat(((v / t) * 100).toFixed(2));
    },
    getStatistics() {
      this.$api.getStatistics().then((res) => {
        if (!res) return;
        this.statisticsData = res.data;
      });
    },
    onChangeChartDate(v) {
      this.orderDate = v;
      this.getPeriodCount();
    },
    initOrderTotalChart() {
      this.echarLine = echarts.init(
        document.getElementById("echart-container")
      );
      this.echarLine.setOption(this.option);
      window.onresize = this.echarLine.resize;
      this.getPeriodCount();
    },
    async getPeriodCount() {
      const res = await this.$api.getPeriodCount({
        begin_date: this.orderDate[0],
        end_date: this.orderDate[1],
      });
      this.setEchartsData(res);
    },
    setEchartsData(res) {
      if (!res.data) return;
      const XAxis = res.data[0].graph.map((item) => item.date);
      const data = res.data[res.data.length - 1];
      this.option.xAxis.data = [...XAxis];
      const arr = [];
      arr.push({
        name: data.merchant_name,
        data: data.graph.map((item) => ({
          value: item.order,
          date: item.date,
        })),
        type: "line",
        color: "#29B583",
        symbol: "emptyCircle",
        symbolSize: 10,
        lineStyle: {
          width: 4,
        },
      });
      this.option.series = arr;
      this.echarLine.setOption(this.option);
    },
    handlerSelectedDate() {
      if (this.orderDate) {
        this.getPeriodCount();
      }
    },
  },
};
</script>

<style lang="less" scoped>
.new-home {
  overflow: hidden;
  .header-cards {
    display: grid;
    grid-template-columns: 50% 50%;
    grid-gap: 20px;
    .card {
      border-radius: 12px;
      background-color: #fff;
      padding: 20px;
      .card-header {
        font-size: 16px;
        color: #465468;
        border-bottom: 1px solid #edf0f4;
        padding-bottom: 17px;
      }
    }
    .todo-card {
      .todo-card-content {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        justify-content: space-between;
        align-items: center;
        grid-gap: 20px;
        .todo-card-item {
          .item-content {
            display: flex;
            justify-content: space-around;
            align-items: flex-end;
            .img {
              width: 40px;
              height: 40px;
              border-radius: 50px;
              background-color: #29b583;
              display: flex;
              justify-content: center;
              align-items: center;
              img {
                width: 28px;
                height: 24px;
              }
            }
            .img-bg-2 {
              background-color: #5ed6e4;
            }
            .img-bg-3 {
              background-color: #6f8bd8;
            }
            .right {
              text-align: center;
              .right-title {
                color: #b6bfd0;
                font-size: 14px;
                text-align: right;
                margin: 20px 0 40px 0;
                overflow: hidden;
              }
              .number {
                font-size: 36px;
                color: #465468;
              }
            }
          }
        }
      }
    }
    .task-card {
      .task-card-content {
        display: grid;
        grid-template-columns: 1fr 1px 1fr;
        .task-card-item-left {
          display: grid;
          grid-template-columns: 1fr 1fr 1fr;
          div {
            display: flex;
            flex-direction: column;
            align-items: center;
          }
          h5 {
            font-size: 14px;
            color: #b6bfd0;
            margin: 20px 0 40px 0;
          }
          span {
            color: #29b583;
          }
          .import-text {
            font-size: 36px;
            color: #465468;
          }
          .finished-color {
            color: #5ed6e4;
          }
          .text-margin {
            margin-bottom: 5px;
          }
        }
        .tesk-left-center {
          justify-content: center;
        }
        .task-card-item-middle {
          width: 1px;
          height: 46px;
          background-color: #f5f1f1;
          position: relative;
          top: calc(50% - 16px);
        }
        .task-card-item-right {
          .right-title {
            margin-left: 23px;
            font-size: 14px;
            color: #b6bfd0;
          }
          .progress-list {
            display: flex;
            justify-content: space-around;
            .progress-item {
              .progress-circle {
                /deep/ .el-progress-circle {
                  width: 48px !important;
                  height: 48px !important;
                }
                /deep/ .el-progress__text {
                  font-size: 12px !important;
                  color: #ff8f8f !important;
                }
              }
              text-align: center;
              h5 {
                color: #b6bfd0;
                margin: 0;
                padding: 0;
              }
            }
          }
        }
      }
    }
  }
  .second-cards {
    margin-top: 20px;
    display: grid;
    grid-template-columns: repeat(4, calc(25% - 15px));
    grid-gap: 20px;
    justify-content: center;
    align-items: center;
    .second-card-item {
      cursor: pointer;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: space-evenly;
      padding: 40px 0px;
      div {
        color: #fff;
        h5 {
          font-size: 24px;
          font-weight: 400;
          margin: 0;
        }
        span {
          font-size: 14px;
          display: -webkit-box;
          -webkit-box-orient: vertical;
          -webkit-line-clamp: 1;
          overflow: hidden;
        }
      }
    }
  }
  .total-chart-container {
    margin-top: 20px;
    display: grid;
    grid-template-columns: calc(25% - 15px) calc(75% - 5px);
    grid-gap: 20px;
    .month-total {
      background-color: #fff;
      border-radius: 12px;
      .total-header {
        border-radius: 6px;
        background-color: #eaf3f0;
        padding: 10px 20px;
        margin: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        .total-left {
          span {
            color: #29b583;
          }
          h5 {
            font-size: 36px;
            font-weight: 300;
            color: #465468;
            margin: 10px 0 0 0;
          }
        }
      }
      .total-type {
        color: #465468;
        font-size: 14px;
        .total-type-item {
          margin: 15px 10px;
          display: flex;
          justify-content: space-between;
          align-items: center;
          span:nth-child(1) {
            margin: 0 0 0 20px;
          }
        }
      }
    }
    .chart {
      border-radius: 12px;
      background-color: #fff;
      padding: 10px 20px;
      color: #b6bfd0;
      /deep/ .el-date-editor .el-range-input {
        color: #b6bfd0;
      }
      .chart-option {
        display: flex;
        justify-content: space-between;
        align-items: center;
      }
    }
  }
  .table-card {
    margin-top: 20px;
    display: grid;
    grid-template-columns: calc(25% - 15px) calc(75% - 5px);
    grid-gap: 20px;
    .table-card-header {
      padding: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid #edf0f4;
      span {
        color: #465468;
        font-size: 16px;
      }
      i {
        color: #d3d8e2;
        font-size: 26px;
        cursor: pointer;
      }
    }
    /deep/ .el-table .cell {
      padding: 0;
    }
    .business-overview {
      background-color: #fff;
      border-radius: 12px;
    }
    .reservation-task {
      background-color: #fff;
      border-radius: 12px;
    }
  }
  .process {
    background-color: #fff;
    margin-top: 20px;
    .process-header {
      padding: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid #edf0f4;
      color: #465468;
      font-size: 16px;
    }
    .process-list {
      padding: 50px;
      display: flex;
      align-items: center;
      justify-content: center;
      min-width: 1100px;
      span {
        color: #465468;
        font-size: 16px;
      }
      .circle-icon {
        cursor: pointer;
        background-color: #f7f7f7;
        margin: 0 auto 10px auto;
        border-radius: 50%;
        width: 42px;
        height: 42px;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        img {
          width: 26px;
          height: 24px;
        }
      }
      .process-1 {
        display: flex;
        align-items: center;
        width: 163px;
        .process-1-content {
          .item {
            width: 64px;
            margin: 25px auto;
            text-align: center;
            div {
              &::after {
                content: "";
                display: inline-block;
                right: -110px;
                top: 20px;
                position: absolute;
                width: 107px;
                height: 101.5px;
                border: 3px solid #d0d5df;
                border-left: none;
                border-bottom: none;
              }
            }
          }
          .item:last-child {
            div {
              &::after {
                content: "";
                display: inline-block;
                right: -107px;
                top: 22px;
                position: absolute;
                width: 107px;
                height: 101.5px;
                border: 3px solid #d0d5df;
                border-left: none;
                border-bottom: none;
                border-right: none;
              }
            }
          }
        }
      }
      .process-2 {
        position: relative;
        top: 50px;
        .process-2-order {
          text-align: center;
          .order-icon {
            display: flex;
            align-items: flex-start;
            .arrow {
              img {
                width: 26px;
                height: 24px;
              }
              top: -8px;
              width: 100px;
              position: relative;
              display: inline-block;
              border-bottom: 3px solid #d0d5df;
            }
          }
          .order-line {
            margin: 0 auto;
            width: 125px;
            .line-1 {
              margin: 0 auto;
              width: 3px;
              height: 12px;
              background-color: #d0d5df;
            }
            .line-2 {
              height: 9px;
              border: 3px solid #d0d5df;
              border-bottom: none;
            }
          }
          .order-bottom {
            display: flex;
            justify-content: space-around;
          }
        }
      }
      .reserve {
        position: relative;
        left: 98px;
        .process-1-content {
          .item {
            div {
              &::after {
                content: "";
                left: -110px;
                border-right: none;
                border-left: 3px solid #d0d5df;
              }
            }
          }
          .item:last-child {
            div {
              &::after {
                content: "";
                left: -107px;
                border-right: none;
                border-bottom: none;
              }
            }
          }
        }
      }
      .process-2-reserve-container {
        left: -12px;
        .process-2-reserve {
          display: flex;
          .order-line {
            width: 3px !important;
            .line-3 {
              height: 20px;
              margin: 2px 0;
              background-color: #d0d5df;
            }
          }
          .process-2-reserve-task {
            display: grid;
            justify-content: flex-start;
            position: relative;
            left: -10px;
          }
        }
      }
    }
  }
}
</style>
