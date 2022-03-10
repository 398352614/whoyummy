<template>
  <div class="home">
    <!-- <div class="top-container">
      <div class="top-tit">当日总览</div>
      <div class="top-container-row">
        <div>
          <span class="green"><i class="el-icon-document"></i></span>
          <span class="green">订单管理</span>
        </div>
        <div>
          <span>{{ orderData.total_order }}</span>
          <span>订单总数</span>
        </div>
        <div>
          <span>{{ orderData.taking_order }}</span>
          <span>取派中订单</span>
        </div>
        <div>
          <span>{{ orderData.signed_order }}</span>
          <span>已完成订单</span>
        </div>
        <div>
          <span class="grey">{{ orderData.cancel_order }}</span>
          <span>取消取派订单</span>
        </div>
        <div>
          <span class="green">{{ orderData.preparing_order }}</span>
          <span>待取派订单</span>
        </div>
        <div></div>
      </div>
      <div class="top-container-row">
        <div>
          <span class="green"><i class="el-icon-truck"></i></span>
          <span class="green">司机车辆管理</span>
        </div>
        <div>
          <span>{{ orderData.total_car }}</span>
          <span>车辆总数</span>
        </div>
        <div>
          <span class="red">{{ orderData.working_car }}</span>
          <span>占用中车辆</span>
        </div>
        <div>
          <span>{{ orderData.free_car }}</span>
          <span>空闲中车辆</span>
        </div>
        <div>
          <span>{{ orderData.total_driver }}</span>
          <span>司机总数</span>
        </div>
        <div>
          <span class="green">{{ orderData.working_driver }}</span>
          <span>出勤中司机</span>
        </div>
        <div>
          <span>{{ orderData.free_driver }}</span>
          <span>休息中司机</span>
        </div>
      </div>
    </div> -->

    <div class="test_overview">
      <div class="test_left">
        <div class="test_left_tit">今日任务概览</div>
        <div class="test_left_container">
          <div>
            <span>{{ todayOverviewData.tracking_order_count }}</span>
            <span>待取派运单</span>
          </div>
          <div>
            <span>{{ todayOverviewData.batch_count }}</span>
            <span>待取派站点</span>
          </div>
          <div>
            <span>{{ todayOverviewData.no_out_tracking_order_count }}</span>
            <span>不可出库运单数</span>
          </div>
          <div>
            <span>{{ todayOverviewData.tour_count }}</span>
            <span>待取派线路数</span>
          </div>
        </div>
      </div>
      <div class="kong-bai"></div>
      <div class="test_right">
        <div class="test_right_tit">
          快捷操作
          <!-- <span>!!!!有超过最迟派送日期包裹入库，请及时处理</span> -->
        </div>

        <div class="test_right_container">
          <div
            v-for="item in shortCut"
            :key="item.id"
            @click="quickJump(item.route)"
          >
            <span class="iconfont" v-html="item.icon"></span>
            <span>{{ item.name }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="result_overview">
      <div class="result_overview_tit">
        <span>任务结果概览</span>
        <el-button size="small" @click="handleClickResultOverview(null)"
          >全部</el-button
        >
        <el-button size="small" @click="handleClickResultOverview(0)"
          >今天</el-button
        >
        <el-button size="small" @click="handleClickResultOverview(1)"
          >昨天</el-button
        >
      </div>
      <div class="result_overview_container">
        <div>
          <span>{{ overviewData.tracking_order_cancel_count }}</span>
          <span>取派失败运单数</span>
        </div>
        <div>
          <span>{{ overviewData.tracking_order_success_count }}</span>
          <span>取派成功运单数</span>
        </div>
        <div>
          <span>{{ overviewData.batch_success_count }}</span>
          <span>取派成功站点数</span>
        </div>
        <div>
          <span>{{ overviewData.batch_cancel_count }}</span>
          <span>取派失败站点数</span>
        </div>
        <div>
          <span>{{ overviewData.package_success_count }}</span>
          <span>取派成功包裹数</span>
        </div>
        <div>
          <span>{{ overviewData.package_cancel_count }}</span>
          <span>取派失败包裹数</span>
        </div>
      </div>
    </div>

    <div class="data-table-display">
      <!-- <div class="pie-chart">
        <div class="tit">当日商家订单总览</div>
        <div id="echartsPie" style="width: 100%; height: 400px;"></div>
      </div> -->

      <!-- <div class="kong-bai"></div> -->
      <div class="order_analysis">
        <div class="order_analysis_tit">订单分析</div>
        <div class="order_analysis_container">
          <div>
            <span>{{ analysisData.today_order_count }}</span>
            <span>今日订单总量</span>
          </div>
          <div>
            <span>{{ analysisData.order_count }}</span>
            <span>取派订单数</span>
          </div>
          <div>
            <span>{{ analysisData.order_cancel_count }}</span>
            <span>取派失败订单总数</span>
          </div>
          <div>
            <span>{{ analysisData.order_success_count }}</span>
            <span>取派成功订单数</span>
          </div>
        </div>
      </div>

      <div class="line-chart">
        <div class="line-chart-top">
          <div class="tit">近期总单量</div>
          <div class="bottom_panel">
            <div class="panel_button" @click="clearActive">
              <el-button
                size="mini"
                :class="[active ? 'active' : '']"
                @click="handlerClickThisWeek"
                >{{ $t("ThisWeek") }}</el-button
              >
              <el-button size="mini" @click="handlerClickLastAWeek">{{
                $t("LastWeek")
              }}</el-button>
              <el-button size="mini" @click="handlerClickThisMonth">{{
                $t("ThisMonth")
              }}</el-button>
              <el-button size="mini" @click="handlerClickLastAMonth">{{
                $t("LastMonth")
              }}</el-button>
              <el-date-picker
                v-model="date_value"
                @blur="handlerSelectedDate"
                size="mini"
                type="daterange"
                format="yyyy-MM-dd"
                value-format="yyyy-MM-dd"
                :range-separator="$t('To')"
                :start-placeholder="$t('StartTime')"
                :end-placeholder="$t('EndTime')"
              >
              </el-date-picker>
            </div>
          </div>
        </div>
        <div id="echarts" style="width: 100%; height: 400px"></div>
      </div>
    </div>

    <div class="top-container merchant-list">
      <div class="top-tit">商家数据概览</div>
      <div>
        <el-table
 max-height="600px"          element-loading-text="loading..."
          align="center"
          header-align="center"
          border
          :data="merchantData"
        >
          <el-table-column
            align="center"
            header-align="center"
            :label="$t('货主')"
            prop="merchant_name"
          >
          </el-table-column>
          <el-table-column
            align="center"
            header-align="center"
            prop="total_order"
            :label="$t('订单总数')"
          >
          </el-table-column>
          <el-table-column
            align="center"
            header-align="center"
            prop="pickup_order"
            :label="$t('取件订单总数')"
          >
          </el-table-column>
          <el-table-column
            align="center"
            header-align="center"
            prop="pie_order"
            :label="$t('派件订单总数')"
          >
          </el-table-column>
          <el-table-column
            align="center"
            header-align="center"
            prop="pickup_pie_order"
            :label="$t('取派订单总数')"
          >
          </el-table-column>
          <el-table-column
            align="center"
            header-align="center"
            prop="cancel_order"
            :label="$t('取派失败总数')"
          >
          </el-table-column>
          <el-table-column
            align="center"
            header-align="center"
            prop="additional_package"
            :label="$t('顺带包裹总数')"
          >
          </el-table-column>
          <el-table-column
            align="center"
            header-align="center"
            prop="total_recharge"
            :label="$t('现金充值合计金额') + '(' + unit + ')'"
          >
          </el-table-column>
        </el-table>
      </div>
    </div>
  </div>
</template>
<script>
// eslint-disable-next-line
require("echarts/lib/chart/line");
require("echarts/lib/chart/pie");
require("echarts/lib/component/tooltip");
const echarts = require("echarts/lib/echarts");

export default {
  name: "Home",
  data() {
    return {
      active: false,
      date_value: [], // 时间
      orderData: {},
      merchantData: [],
      myChart: "",
      pieChart: null,
      overviewData: {},
      analysisData: {},
      shortCut: {},
      todayOverviewData: {}
    };
  },
  computed: {
    option() {
      return {
        tooltip: {
          show: true,
          formatter: (params) => `
            ${params.name}<br>
            <span style="display: inline-block;
              width: 5px;
              height: 5px;
              border-radius: 50%;
              background-color: #5393ff;
              margin: 0 5px 3px;
              "></span>
            ${params.value}`,
        },
        legend: {
          data: [],
        },
        xAxis: {
          type: "category",
          data: [],
        },
        yAxis: {
          type: "value",
          // interval: 1, // y 轴的步长
        },
        series: [],
      };
    },

    pieOption() {
      return {
        series: [
          {
            name: "访问来源",
            type: "pie", // 设置图表类型为饼图
            radius: "55%", // 饼图的半径，外半径为可视区尺寸（容器高宽中较小一项）的 55% 长度。
            data: [
              // 数据数组，name 为数据项名称，value 为数据项值
            ],
          },
        ],
      };
    },

    unit() {
      return this.$store.state.globalData.unit;
    },
  },
  created() {
    this.getOrderData();
    this.getStatisticsMerchant();
    // this.getStatisticsMerchantTotal();
    this.getOrderAnalysis();
    this.getResultOverview();
    this.getShortCut();
    this.getTodayOverview();
    setTimeout(() => {
      this.myChart = this.getEchartInstance("echarts");
      // this.pieChart = this.getEchartInstance('echartsPie')
      this.getCurrentWeekDate();
    }, 200);
  },
  mounted() {
    this.active = true;
  },
  methods: {
    clearActive() {
      this.active = false;
    },
    handlerClickThisWeek() {
      this.getCurrentWeekDate();
    },
    handlerClickLastAWeek() {
      this.getLastAWeekDate();
    },
    handlerClickThisMonth() {
      this.getThisAMonthDate();
    },
    handlerClickLastAMonth() {
      this.getLastAMonthDate();
    },
    handlerSelectedDate() {
      if (this.date_value) {
        this.getPeriodCount({
          begin_date: this.date_value[0],
          end_date: this.date_value[1],
        });
      }
    },
    getEchartInstance(val) {
      return echarts.init(document.getElementById(val));
    },
    async getCurrentWeekDate() {
      const res = await this.$api.getCurrentWeek();
      this.setEchartsData(res);
    }, // 当周数据
    async getLastAWeekDate() {
      const res = await this.$api.getLastAWeek();
      this.setEchartsData(res);
    }, // 上周数据
    async getThisAMonthDate() {
      const res = await this.$api.getCurrentMonth();
      this.setEchartsData(res);
    }, // 本月数据
    async getLastAMonthDate() {
      const res = await this.$api.getLastAMonth();
      this.setEchartsData(res);
    }, // 上月数据

    getOrderData() {
      // 订单司机数据
      this.$api.getHomeOrderData().then((res) => {
        if (!res) return;
        this.orderData = res.data;
      });
    },

    getOrderAnalysis() {
      // 订单分析
      this.$api.getOrderAnalysis().then((res) => {
        if (!res) return;
        this.analysisData = res.data;
      });
    },

    handleClickResultOverview(val) {
      if (val == null) {
        this.getResultOverview();
      } else {
        const start = new Date();
        start.setTime(start.getTime() - 3600 * 1000 * 24 * val);
        this.getResultOverview(start.format("yyyy-MM-dd"));
      }
    },

    getResultOverview(date = "") {
      // 任务结果概览
      this.$api
        .getResultOverview({
          execution_date: date,
        })
        .then((res) => {
          if (!res) return;
          this.overviewData = res.data;
        });
    },

    getShortCut() {
      // 快捷操作列表
      this.$api
        .getShortCut()
        .then((res) => {
          if (!res) return;
          this.shortCut = res.data.map(el => {
            return {
              icon: '&#xe637;',
              ...el
            }
          });
        });
    },

    getTodayOverview() {
      // 快捷操作列表
      this.$api
        .getTodayOverview()
        .then((res) => {
          if (!res) return;
          this.todayOverviewData = res.data;
        });
    },

    quickJump(route) {
      this.$router.push({ name: route });
    },

    getStatisticsMerchantTotal() {
      // 货主总统计
      this.$api.getStatisticsMerchantTotal().then((res) => {
        if (!res) return;
        // this.merchantData = res.data;
        let nums = res.data.map((el) => {
          return el.order;
        });
        let total = nums.reduce((t, s) => {
          return t + s;
        });
        this.pieOption.series[0].data = res.data.map((el) => {
          return {
            name: `${el.merchant_name + ": " + el.order}(${(
              (el.order / total) *
              100
            ).toFixed(2)}%)`,
            value: el.order,
          };
        });
        this.pieChart.setOption(this.pieOption);
      });
    },

    getStatisticsMerchant() {
      // 货主分统计
      this.$api.getStatisticsMerchant().then((res) => {
        if (!res) return;
        this.merchantData = res.data;
      });
    },

    async getPeriodCount(params) {
      const res = await this.$api.getPeriodCount(params);
      this.setEchartsData(res);
      // // eslint-disable-next-line
      // let XAxis = res.data.map((item) => item.date);

      // // eslint-disable-next-line
      // let YAxis = res.data.map((item) => item.ordercount);
      // this.option.xAxis.data = [];
      // this.option.series[0].data = [];

      // this.option.xAxis.data = [...XAxis];
      // this.option.series[0].data = [...YAxis];
      // this.myChart.setOption(this.option);
    }, // 查询折线图数据

    setEchartsData(res) {
      if (!res.data) return;
      // eslint-disable-next-line
      let XAxis = res.data[0].graph.map((item) => item.date);

      // eslint-disable-next-line
      // let YAxis1 = res.data.map((item) => item.actual_count);
      // let YAxis2 = res.data.map((item) => item.expect_count);
      this.option.xAxis.data = [];
      // this.option.series[0].data = [];

      this.option.xAxis.data = [...XAxis];
      // this.option.series[0].data = [...YAxis1];
      // this.option.series[1].data = [...YAxis2];
      this.option.legend.data = [];
      let arr = [];
      res.data.forEach((el) => {
        this.option.legend.data.push(el.merchant_name);
        arr.push({
          name: el.merchant_name,
          data: el.graph.map((item) => {
            return { value: item.order, date: item.date };
          }),
          type: "line",
        });
      });
      // this.option.legend.data = arr.map(item => {
      //   return item.name
      // })
      console.log(this.option.legend.data, "this.option.legend.data");
      this.option.series = arr;
      this.myChart.setOption(this.option);
    },
  },
};
</script>

<style lang="less" scoped>
.home {
  .iconfont {
    color: #545454;
    // font-size: 16px;
    display: inline-block;
    margin-right: 5px;
  }
  .top-container {
    background-color: #fff;
    .top-tit {
      color: #545454;
      font-size: 16px;
      font-weight: Bold;
      padding: 20px 0px 20px 10px;
      border-bottom: 1px solid #e4ebf1;
    }
    .top-container-row {
      border-top: 1px solid #e4ebf1;
      border-bottom: 1px solid #e4ebf1;
      padding: 20px;
      display: flex;
      // flex-wrap: wrap;
      div {
        width: 150px;
        height: 80px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        &:first-child {
          border-right: 1px solid #e4ebf1;
          span {
            font-size: 20px;
          }
        }
        span {
          &:first-child {
            font-size: 36px;
            font-weight: bold;
            color: #545454;
            line-height: 42px;
          }
          &:last-child {
            font-size: 14px;
            font-weight: 400;
            color: #818181;
            line-height: 42px;
          }
        }
      }
      .grey {
        color: #9f9e9e !important;
      }
      .red {
        color: #e76767 !important;
      }
      .green {
        color: #28d094 !important;
      }
      .yellow {
        color: #ff955b !important;
      }
    }
  }

  .test_overview {
    display: flex;
    .test_left {
      // flex-shrink: 0;
      background: #fff;
      flex-grow: 1;
      display: table-cell;
      .test_left_tit {
        height: 60px;
        line-height: 60px;
        color: #545454;
        font-size: 16px;
        font-weight: Bold;
        padding: 0 0px 0 10px;
        margin-right: 30px;
      }
      .test_left_container {
        width: 100%;
        padding: 20px;
        display: flex;
        flex-wrap: wrap;
        div {
          width: 40%;
          height: 80px;
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: space-between;
          border: 1px solid #ebeef5;
          padding: 10px 0;
          margin-bottom: 20px;
          &:not(:first-child) {
            // border-left-style: none;
          }
          span {
            &:first-child {
              font-size: 36px;
              font-weight: bold;
              color: #545454;
              line-height: 42px;
            }
            &:last-child {
              font-size: 14px;
              font-weight: 400;
              color: #818181;
              line-height: 42px;
            }
          }
        }
      }
    }
    .kong-bai {
      width: 20px;
    }
    .test_right {
      display: table-cell;
      background: #fff;
      // flex-shrink: 0;
      flex-grow: 2;
      .test_right_tit {
        height: 60px;
        line-height: 60px;
        color: #545454;
        font-size: 16px;
        font-weight: Bold;
        padding: 0 0px 0 10px;
        margin-right: 30px;
      }
      .test_right_container {
        width: 100%;
        padding: 20px;
        display: flex;
        flex-wrap: wrap;
        div {
          width: 180px;
          height: 80px;
          display: flex;
          // flex-direction: column;
          align-items: center;
          justify-content: center;
          border: 1px solid #ebeef5;
          padding: 10px 0;
          cursor: pointer;
          &:not(:first-child) {
            // border-left-style: none;
          }
          span {
            &:first-child {
              font-size: 36px;
              // font-weight: bold;
              color: #545454;
              line-height: 42px;
              // font-size: 20px;
              display: inline-block;
            }
            &:last-child {
              font-size: 16px;
              font-weight: Bold;
              line-height: 42px;
              color: #545454;
              margin-left: 5px;
            }
          }
        }
      }
    }
  }

  .result_overview {
    background: #fff;
    margin-top: 20px;
    .result_overview_tit {
      span {
        height: 60px;
        line-height: 60px;
        color: #545454;
        font-size: 16px;
        font-weight: Bold;
        padding: 0 0px 0 10px;
        margin-right: 30px;
      }
    }
    .result_overview_container {
      // border-top: 1px solid #E4EBF1;
      // border-bottom: 1px solid #E4EBF1;
      padding: 20px;
      display: flex;
      // flex-wrap: wrap;
      div {
        width: 150px;
        height: 80px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        border: 1px solid #ebeef5;
        padding: 10px 0;
        &:not(:first-child) {
          border-left-style: none;
        }
        span {
          &:first-child {
            font-size: 36px;
            font-weight: bold;
            color: #545454;
            line-height: 42px;
          }
          &:last-child {
            font-size: 14px;
            font-weight: 400;
            color: #818181;
            line-height: 42px;
          }
        }
      }
    }
  }

  .data-table-display {
    margin-top: 20px;
    // display: flex;
    // justify-content: space-between;
    .pie-chart {
      min-width: 30%;
      // flex-grow: 1;
      background-color: #fff;
      .tit {
        height: 60px;
        line-height: 60px;
        color: #545454;
        font-size: 16px;
        font-weight: Bold;
        padding: 0 0px 0 10px;
        border-bottom: 1px solid #e4ebf1;
      }
    }
    .kong-bai {
      // min-width: 30px;
      // max-width: 40px;
      // width: 4%;
      width: 20px;
      height: 100%;
    }
    .order_analysis {
      background-color: #fff;
      border-bottom: 1px solid #e4ebf1;
      padding-bottom: 30px;
      .order_analysis_tit {
        height: 60px;
        line-height: 60px;
        color: #545454;
        font-size: 16px;
        font-weight: Bold;
        padding: 0 0px 0 10px;
      }
      .order_analysis_container {
        width: 100%;
        display: flex;
        justify-content: space-around;
        align-items: center;
        div {
          width: 120px;
          flex-shrink: 0;
          // display: table-cell;
          border: 1px solid #ebeef5;
          padding: 15px 30px;
          display: flex;
          flex-direction: column;
          justify-content: center;
          align-items: center;
          // span {
          //   &:first-child {
          //     font-size: 20px;
          //     font-weight: bold;
          //   }
          // }
          span {
            &:first-child {
              font-size: 36px;
              font-weight: bold;
              color: #545454;
              line-height: 42px;
            }
            &:last-child {
              font-size: 14px;
              font-weight: 400;
              color: #818181;
              line-height: 42px;
            }
          }
        }
      }
    }
    .line-chart {
      width: 100%;
      // flex-grow: 2;
      background-color: #fff;
      .line-chart-top {
        height: 60px;
        line-height: 60px;
        display: flex;
        justify-content: space-between;
        padding-left: 20px;
        align-items: center;
        // border-bottom: 1px solid #E4EBF1;
        .tit {
          color: #1890ff;
          font-size: 16px;
          font-weight: Bold;
          border-bottom: 2px solid #1890ff;
          margin-left: 50px;
          padding: 0 20px;
        }
        .el-date-editor--daterange.el-input__inner {
          max-width: 200px !important;
        }
        .bottom_panel {
          .panel_button {
            // position: absolute;
            // top: 0;
            // right: 20px;
            // z-index: 3;
          }
        }
        .active {
          color: #28d094;
          border-color: rgb(191, 241, 223);
          background-color: rgb(234, 250, 244);
        }
      }
    }
  }

  .merchant-list {
    margin-top: 20px;
  }
}
.tool-tip-color {
  display: inline-block;
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background-color: aqua;
}
</style>
