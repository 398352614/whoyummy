<template>
  <!-- 账单明细表 -->
  <div class="cash-recharge-list">
    <div class="conditional">
      <div class="conditional-box">
        <div class="box-search">
          {{ $t("RecordTime") }}
          <el-date-picker
            size="small"
            v-model="search_date"
            type="daterange"
            value-format="yyyy-MM-dd"
            :range-separator="$t('To')"
            :start-placeholder="$t('StartTime')"
            :end-placeholder="$t('EndTime')"
            :picker-options="pickerOptionsSearch"
            class="search-bar-date"
          >
          </el-date-picker>
          <!-- 账单单号 -->
          <el-input
            @keyup.enter.native="handleChangePageParams"
            v-model="pageParams.bill_no"
            clearable
            :placeholder="$t('PleaseEnterBillNumber')"
            size="small"
            class="search-bar"
          >
          </el-input>
          <!-- 对账单号 -->
          <el-input
            @keyup.enter.native="handleChangePageParams"
            v-model="pageParams.verify_no"
            clearable
            :placeholder="$t('PleaseEnterStatementNumber')"
            size="small"
            class="search-bar"
          >
          </el-input>
          <!-- 系统编号 -->
          <el-input
            @keyup.enter.native="handleChangePageParams"
            v-model="pageParams.object_no"
            clearable
            :placeholder="$t('PleaseEnterSystemNumber')"
            size="small"
            class="search-bar"
          >
          </el-input>
          <!-- 支付状态 -->
          <el-select
            @keyup.enter.native="handleChangePageParams"
            clearable
            size="small"
            v-model="pageParams.status"
            :placeholder="$t('PleaseSelectPaymentStatus')"
            class="search-bar"
          >
            <el-option
              v-for="item in billStatusList"
              :key="item.id"
              :label="item.name"
              :value="item.id"
            >
            </el-option>
          </el-select>

          <el-button
            size="small"
            @click="handleChangePageParams"
            type="primary"
            :loading="this.$store.state.config.button_loading"
            >{{ $t("Search") }}</el-button
          >
        </div>
        <el-tooltip
          :content="$t('AdministratorActivate')"
          placement="top"
          :disabled="hasPerm('bill-verify.store') ? true : false"
        >
          <div class="box-ton">
            <!-- 生成对账单 -->
            <el-button
              :disabled="!hasPerm('bill-verify.store')"
              size="small"
              @click="getGenerateStatement"
              type="primary"
              :class="[
                hasPerm('bill-verify.store') ? 'common' : 'common-disabled',
                'top-btn',
              ]"
              :loading="this.$store.state.config.button_loading"
              >{{ $t("GenerateStatement") }}</el-button
            >
            <!-- <el-button
            size="small"
            @click="handleChangePageParams"
            type="primary"
            class="common"
            :loading="this.$store.state.config.button_loading"
            >{{ $t("Export") }}</el-button
          > -->
          </div>
        </el-tooltip>
      </div>
      <el-table
        :height="heightSearch ? 'calc(100vh - 288px)' : 'calc(100vh - 245px)'"
        :data="hasPerm('bill.index') ? tableData : []"
        v-loading="global_loading()"
        element-loading-text="loading..."
        border
        style="width: 100%"
        :summary-method="getSum"
        show-summary
        ref="table"
        :cell-style="{ padding: '5px 0px' }"
      >
        <template slot="empty" v-if="!hasPerm('bill.index')">
          <div style="font-size: 18px">
            {{ $t("AdministratorActivate") }}
          </div>
        </template>
        <el-table-column type="index" align="center" width="50">
          <template slot="header">
            <span>{{ $t("Number") }}</span>
          </template>
        </el-table-column>
        <!-- 账单单号 -->
        <el-table-column
          align="center"
          prop="bill_no"
          :label="$t('BillNumber')"
          width="160"
        >
        </el-table-column>
        <!-- 对账单号 -->
        <el-table-column
          align="center"
          prop="verify_no"
          :label="$t('StatementNumber')"
          width="160"
        >
        </el-table-column>
        <!-- 系统编号 -->
        <el-table-column
          align="center"
          prop="object_no"
          :label="$t('SystemNumber')"
          width="160"
        >
          <template slot-scope="scope">
            <el-link
              :underline="false"
              @click="
                hasPerm('order.index')
                  ? $router.push({
                      name: 'OrderList',
                      params: { order_no: scope.row.object_no },
                    })
                  : ''
              "
              class="link"
              >{{ scope.row.object_no }}</el-link
            >
          </template>
        </el-table-column>
        <!-- 付款名称 -->
        <el-table-column
          align="center"
          prop="payer_name"
          :label="$t('PayerName')"
          width="120"
        >
        </el-table-column>
        <!-- 付款类型-->
        <el-table-column
          align="center"
          prop="payer_type_name"
          :label="$t('PayerType')"
          width="120"
        >
        </el-table-column>
        <!-- 费用类型 -->
        <el-table-column
          align="center"
          prop="fee_name"
          :label="$t('TypesOfFee')"
          width="120"
        >
        </el-table-column>
        <!-- 付款方式 -->
        <el-table-column
          align="center"
          prop="pay_type_name"
          :label="$t('paymentMethod')"
          width="150"
        >
        </el-table-column>
        <!-- 费用 -->
        <el-table-column
          align="center"
          prop="expect_amount"
          :label="$t('fees') + '(' + currency_unit_symbol + ')'"
          width="100"
        >
        </el-table-column>
        <!-- 待支付费用 -->
        <el-table-column
          align="center"
          prop="rest_amount"
          :label="$t('PendingPayment') + '(' + currency_unit_symbol + ')'"
          width="160"
        >
        </el-table-column>
        <!-- 已支付 -->
        <el-table-column
          align="center"
          prop="actual_amount"
          :label="$t('Paid') + '(' + currency_unit_symbol + ')'"
          width="120"
        >
        </el-table-column>
        <!-- 支付状态 -->
        <!-- <el-table-column
          align="center"
          prop="status_name"
          :label="$t('PaymentStatus')"
          width="150"
        >
        </el-table-column> -->
        <!-- 对账状态 -->
        <el-table-column
          align="center"
          prop="verify_status_name"
          :label="$t('ReconciliationStatus')"
          width="180"
        >
        </el-table-column>
        <!-- 备注 -->
        <el-table-column
          align="center"
          prop="remark"
          :label="$t('remark')"
          width="150"
        >
        </el-table-column>
        <!-- 录单时间 -->
        <el-table-column
          align="center"
          prop="created_at"
          :label="$t('RecordDate')"
          width="180"
        >
        </el-table-column>
      </el-table>
    </div>

    <base-pagination
      :pageParams="pageParams"
      @search="geVerify"
      class="pagination-size"
    ></base-pagination>
    <div>
      <el-dialog
        :title="$t('GenerateStatement')"
        :visible.sync="dialogVisible"
        width="75%"
      >
        <div class="search">
          {{ $t("RecordTime") }}
          <el-date-picker
            size="small"
            v-model="generate_search_date"
            type="daterange"
            value-format="yyyy-MM-dd"
            range-separator="-"
            :start-placeholder="$t('StartTime')"
            :end-placeholder="$t('EndTime')"
            :picker-options="pickerOptionsSearch"
            class="search-bar-date"
          >
          </el-date-picker>
          <!-- <el-input
            @keyup.enter.native="getGenerate"
            v-model="generateParams.payer_name"
            clearable
            :placeholder="$t('PleaseEnterPaymentMethod')"
            size="small"
            class="search-bar"
          >
          </el-input> -->
          <el-select
            @keyup.enter.native="getGenerate"
            v-model="generateParams.payer_name"
            size="small"
            class="search-bar"
            clearable
            filterable
            :placeholder="$t('PleaseEnterPaymentMethod')"
          >
            <el-option
              v-for="item in merchantData"
              :key="item.name"
              :label="item.name"
              :value="item.name"
            >
            </el-option>
          </el-select>
          <el-button
            size="small"
            @click="getGenerate"
            type="primary"
            :loading="this.$store.state.config.button_loading"
            >{{ $t("Search") }}</el-button
          >
        </div>
        <!-- 表格 -->
        <el-table
          max-height="530px"
          :data="generateList"
          style="width: 100%"
          border
          :summary-method="getSummaries"
          show-summary
        >
          <el-table-column type="index" align="center" width="50">
            <template slot="header">
              <span>{{ $t("Number") }}</span>
            </template>
          </el-table-column>
          <!-- 录单日期 -->
          <el-table-column
            :label="$t('RecordDate')"
            align="center"
            prop="created_at"
          >
          </el-table-column>
          <!-- 系统编号 -->
          <el-table-column
            :label="$t('SystemNumber')"
            align="center"
            prop="object_no"
          >
          </el-table-column>
          <!-- 付款方名称 -->
          <el-table-column
            :label="$t('PayerName')"
            align="center"
            prop="payer_name"
          >
          </el-table-column>
          <!-- 费用类型 -->
          <el-table-column
            :label="$t('TypesOfFee')"
            align="center"
            prop="mode_name"
          >
          </el-table-column>
          <!-- 付款方式 -->
          <el-table-column
            :label="$t('paymentMethod')"
            align="center"
            prop="pay_type_name"
          >
          </el-table-column>
          <!-- 费用 -->
          <el-table-column
            :label="$t('fees') + '(' + currency_unit_symbol + ')'"
            align="center"
            prop="expect_amount"
            width="100"
          >
          </el-table-column>
          <!-- 待支付费用 -->
          <el-table-column
            :label="$t('PendingPayment') + '(' + currency_unit_symbol + ')'"
            align="center"
            prop="rest_amount"
          >
          </el-table-column>
          <!-- 已支付费用 -->
          <el-table-column
            :label="$t('Paid') + '(' + currency_unit_symbol + ')'"
            align="center"
            prop="actual_amount"
          >
          </el-table-column>
          <!-- 支付状态 -->
          <!-- <el-table-column
            :label="$t('PaymentStatus')"
            align="center"
            prop="status_name"
          >
          </el-table-column> -->
        </el-table>
        <div slot="footer" class="dialog-footer">
          <el-button type="primary" @click="addGenerateStatement">{{
            $t("GenerateStatement")
          }}</el-button>
          <el-button @click="dialogVisible = false">{{
            $t("Cancel")
          }}</el-button>
        </div>
      </el-dialog>
    </div>
  </div>
</template>

<script>
import BasePagination from "@/components/BasePagination/BasePagination";
import loading from "@/mixin/global_loading";
// import Dispatch from "../settingManagement/Dispatch.vue";

export default {
  name: "BillingSchedule",
  components: {
    BasePagination,
    // Dispatch,
  },
  mixins: [loading],
  data() {
    return {
      heightSearch: false,
      currency_unit_symbol: "", //单位
      dialogVisible: false,
      tableData: [],
      generateList: [],
      billStatusList: [],
      merchantData: [], //货主下拉
      formData: {
        bill_list: [],
      },
      generateParams: {
        mode: 2,
        begin_date: "",
        end_date: "",
        payer_name: "",
        verify_status: 1,
        per_page: 0,
      },
      pageParams: {
        mode: 2,
        begin_date: "",
        end_date: "",
        total: 0,
        page: 1,
        per_page: 20,
      },
      search_date: ["", ""],
      generate_search_date: ["", ""],
      paymentStatusList: [],
    };
  },
  watch: {
    search_date: {
      handler() {
        if (this.search_date) {
          this.pageParams.begin_date = this.search_date[0];
          this.pageParams.end_date = this.search_date[1];
        } else {
          this.pageParams.begin_date = "";
          this.pageParams.end_date = "";
        }

        this.pageParams.page = 1;
      },
    },
    generate_search_date: {
      handler() {
        if (this.generate_search_date) {
          this.generateParams.begin_date = this.generate_search_date[0];
          this.generateParams.end_date = this.generate_search_date[1];
        } else {
          this.generateParams.begin_date = "";
          this.generateParams.end_date = "";
        }
      },
    },
  },
  computed: {
    pickerOptionsSearch() {
      return {
        shortcuts: [
          {
            text: this.$t("zuiJingYiZhou"),
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
              picker.$emit("pick", [start, end]);
            },
          },
          {
            text: this.$t("zuiJingYiYue"),
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
              picker.$emit("pick", [start, end]);
            },
          },
          {
            text: this.$t("LastThreeMonths"),
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
              picker.$emit("pick", [start, end]);
            },
          },
        ],
      };
    },
  },
  activated() {
    this.geVerify();
  },
  mounted() {
    //  onresize  事件会在窗口或框架被调整大小时发生
    window.onresize = () => {
      if (!this.timer) {
        this.timer = true;
        let that = this;
        setTimeout(function () {
          that.searchFormWidth();
          that.timer = false;
        }, 400);
      }
    };
  },
  destroyed() {
    // 组件销毁后解绑事件
    window.onresize = null;
  },
  // 表格合计不展示问题
  updated() {
    this.$nextTick(() => {
      this.$refs["table"].doLayout();
    });
  },
  created() {
    this.geVerify();
    this.searchFormWidth();
    this.getAdvanceSetting(); //单位
    this.getDictionary();
    this.getMerchants();
  },
  methods: {
    // 获取窗口的宽度,调整表格高度
    searchFormWidth() {
      let w = window.innerWidth;
      if (w <= 1575) {
        this.heightSearch = true;
      } else {
        this.heightSearch = false;
      }
    },
    // 合计 表格
    getSum(param) {
      const { columns, data } = param;
      const sums = [];
      columns.forEach((column, index) => {
        if (index === 0) {
          sums[index] = this.$t("Total");
        } else if (index === 8 || index === 9 || index === 10) {
          const values = data.map((item) => Number(item[column.property]));
          if (!values.every((value) => isNaN(value))) {
            sums[index] = values.reduce((prev, curr) => {
              const value = Number(curr).toFixed(2);
              if (!isNaN(value)) {
                return prev + curr;
              } else {
                return prev;
              }
            }, 0);
          } else {
            // sums[index] = 'N/A'
          }
        } else {
          sums[index] = "";
        }
      });
      sums[8] = sums[8] ? sums[8].toFixed(2) : "0.00";
      sums[9] = sums[9] ? sums[9].toFixed(2) : "0.00";
      sums[10] = sums[10] ? sums[10].toFixed(2) : "0.00";

      return sums;
    },
    // 合计 弹出框
    getSummaries(param) {
      const { columns, data } = param;
      const sums = [];
      columns.forEach((column, index) => {
        if (index === 0) {
          sums[index] = this.$t("Total");
        } else if (index === 6 || index === 7 || index === 8) {
          const values = data.map((item) => Number(item[column.property]));
          if (!values.every((value) => isNaN(value))) {
            sums[index] = values.reduce((prev, curr) => {
              const value = Number(curr).toFixed(2);
              if (!isNaN(value)) {
                return prev + curr;
              } else {
                return prev;
              }
            }, 0);
          } else {
            // sums[index] = 'N/A'
          }
        } else {
          sums[index] = "";
        }
      });
      sums[6] = sums[6] ? sums[6].toFixed(2) : "0.00";
      sums[7] = sums[7] ? sums[7].toFixed(2) : "0.00";
      sums[8] = sums[8] ? sums[8].toFixed(2) : "0.00";

      return sums;
    },
    getDictionary() {
      this.$api.getDictionary().then((res) => {
        this.billStatusList = res.data.billStatusList; //支付状态
      });
    },
    //货主列表-查询
    getMerchants() {
      let obj = {
        status: 1,
      };
      this.$api.getMerchants(obj).then((res) => {
        this.merchantData = res.data.data;
      });
    },
    // 获取单位值
    getAdvanceSetting() {
      this.$api.getAdvanceSetting().then((res) => {
        this.currency_unit_symbol = res.data.currency_unit_symbol; // 单价单位
      });
    },
    geVerify() {
      // 列表
      this.$api.geVerify(this.pageParams).then((res) => {
        this.tableData = res.data.data;
        this.pageParams.total = res.data.meta.total;
      });
    },
    // 查询
    handleChangePageParams() {
      this.pageParams.page = 1;
      this.geVerify();
    },
    // 生成对账单 详情
    getGenerate() {
      this.formData.bill_list = [];
      this.$api.geVerify(this.generateParams).then((res) => {
        this.generateList = res.data.data;
        let data = res.data.data;
        data.map((item) => {
          let obj = {
            bill_no: item.bill_no,
          };
          this.formData.bill_list.push(obj);
        });
      });
    },
    getGenerateStatement() {
      this.dialogVisible = true;
      this.generateList = [];
      this.generateParams = {
        ...this.generateParams,
        begin_date: "",
        end_date: "",
        payer_name: "",
        verify_status: 1,
      };
      this.generate_search_date = ["", ""];
      // this.getGenerate();
    },
    // 生成对账单
    addGenerateStatement() {
      this.$api.addGenerateStatement(this.formData).then((res) => {
        if (res.code == 200) {
          this.dialogVisible = false;
        }
      });
    },
  },
};
</script>
<style lang="less" scoped>
.cash-recharge-list {
  padding: 20px 25px 10px;
  background: #ffffff;
  border: 1px solid rgba(228, 235, 241, 1);
  .conditional {
    padding: 0px 0 10px;
    .conditional-box {
      .box-search {
        float: left;
        margin-bottom: 10px;
      }
      .box-ton {
        float: right;
        margin-bottom: 10px;
      }
    }
  }
  .search {
    margin-bottom: 20px;
  }
  .dialog-footer {
    text-align: center;
  }
}
</style>
<style lang="less">
.cash-recharge-list {
  .conditional {
    .el-input__inner {
      border-radius: 0;
    }
  }
}
</style>
