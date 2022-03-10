<template>
  <!-- 对账账单 -->
  <div class="cash-recharge-list">
    <div class="conditional">
      <div class="conditional-box">
        <span style="margin-top: 6px">{{ $t("CreationTime") }} </span>
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
        <!-- 付款方名称 -->
        <el-input
          @keyup.enter.native="handleChangePageParams"
          v-model="pageParams.payer_name"
          clearable
          :placeholder="$t('PleaseEnterPaymentMethod')"
          size="small"
          class="search-bar"
        >
        </el-input>
        <!-- 对账状态 -->
        <el-select
          size="small"
          @keyup.enter.native="handleChangePageParams"
          v-model="pageParams.status"
          :placeholder="$t('PleaseSelectReconciliationStatus')"
          :clearable="true"
          class="search-bar"
        >
          <el-option
            v-for="item in verifyStatusList"
            :key="item.id"
            :label="item.name"
            :value="item.id"
          >
          </el-option>
        </el-select>
        <!-- 付款方式 -->
        <el-select
          size="small"
          @keyup.enter.native="handleChangePageParams"
          v-model="pageParams.pay_type"
          :placeholder="$t('PleaseSelectPaymentMethod')"
          :clearable="true"
          class="search-bar"
        >
          <el-option
            v-for="item in payTypeList"
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
      <!-- <div>
        <el-button
          size="small"
          @click="handleChangePageParams"
          type="primary"
          class="common but"
          :loading="this.$store.state.config.button_loading"
          >{{ $t("Export") }}</el-button
        >
      </div> -->
    </div>
    <el-table
      :data="hasPerm('bill-verify.index') ? tableData : []"
      border
      ref="table"
      :height="heightSearch ? 'calc(100vh - 277px)' : 'calc(100vh - 235px)'"
      style="width: 100%"
      v-loading="global_loading()"
      :summary-method="getSummaries"
      show-summary
      element-loading-text="loading..."
      :cell-style="{ padding: '5px 0px' }"
    >
      <template slot="empty" v-if="!hasPerm('bill-verify.index')">
        <div style="font-size: 18px">
          {{ $t("AdministratorActivate") }}
        </div>
      </template>
      <el-table-column type="index" align="center" width="55">
        <template slot="header">
          <span>{{ $t("Number") }}</span>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        prop="date"
        :label="$t('Operation')"
        width="120"
      >
        <template slot-scope="scope">
          <el-dropdown size="small" trigger="hover" placement="bottom">
            <el-button size="mini" type="primary" plain class="operation">
              {{ $t("Operation") }}
              <i class="el-icon-arrow-down el-icon--right"></i>
            </el-button>
            <el-dropdown-menu slot="dropdown">
              <!-- 详情 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('bill-verify.index') ? true : false"
              >
                <div
                  :style="
                    hasPerm('bill-verify.index') ? '' : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    @click.native="getDetails(scope.row.id)"
                    :disabled="!hasPerm('bill-verify.index')"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('bill-verify.index')"
                      >{{ $t("details") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 审核 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('bill-verify.verify') ? true : false"
              >
                <div
                  :style="
                    hasPerm('bill-verify.verify') ? '' : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('bill-verify.verify')"
                    @click.native="getSettlement(scope.row.id)"
                    v-if="scope.row.status != 2"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('bill-verify.verify')"
                      >{{ $t("Reconciliation1") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 删除 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('bill-verify.destroy') ? true : false"
              >
                <div
                  :style="
                    hasPerm('bill-verify.destroy') ? '' : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    @click.native="delSettlement(scope.row.id)"
                    :disabled="!hasPerm('bill-verify.destroy')"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('bill-verify.destroy')"
                      >{{ $t("Delete") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
            </el-dropdown-menu>
          </el-dropdown>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        prop="verify_no"
        :label="$t('StatementNumber')"
        width="150"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="payer_name"
        :label="$t('PayerName')"
        width="120"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="payer_type_name"
        :label="$t('PayerType')"
        width="120"
      >
      </el-table-column>
      <!-- <el-table-column
        align="center"
        prop="pay_type_name"
        :label="$t('PaymentMethod')"
        width="140"
      >
      </el-table-column> -->
      <el-table-column
        align="center"
        prop="expect_amount"
        :label="$t('BillAmount') + '(' + currency_unit_symbol + ')'"
        width="150"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="rest_amount"
        :label="$t('AmountToBeReconciled') + '(' + currency_unit_symbol + ')'"
        width="180"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="actual_amount"
        :label="$t('ActualPaymentAmount') + '(' + currency_unit_symbol + ')'"
        width="200"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="status_name"
        :label="$t('ReconciliationStatus')"
        width="180"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="remark"
        :label="$t('remark')"
        width="120"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="verify_time"
        :label="$t('ReconciliationTime')"
        width="160"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="created_at"
        :label="$t('Created')"
        width="160"
      >
      </el-table-column>
    </el-table>

    <div>
      <base-pagination
        :pageParams="pageParams"
        @search="getReconciliation"
        class="pagination-size"
      ></base-pagination>
    </div>
  </div>
</template>

<script>
import BasePagination from "@/components/BasePagination/BasePagination";
import loading from "@/mixin/global_loading";

export default {
  name: "CashRechargeList",
  components: {
    BasePagination,
  },
  mixins: [loading],
  data() {
    // 数值范围
    var valiNumberPass = (rule, value, callback) => {
      //包含小数的数字
      let reg = /^[+-]?(0|([1-9]\d*))(\.\d+)?$/g;
      if (value === "") {
        callback(new Error(this.$t("Required")));
      } else if (!reg.test(value)) {
        callback(new Error(this.$t("TheAmountMustBeGreater")));
      } else if (value > 1000000) {
        callback(new Error(this.$t("TheAmountMustBeGreater")));
      } else {
        if (value <= 0) {
          callback(new Error(this.$t("TheAmountMustBeGreater")));
        } else {
          callback();
        }
      }
    };
    return {
      currency_unit_symbol: "", //单位
      heightSearch: false,
      tableData: [], //表格
      merchantsList: [], //货主组
      verifyStatusList: [], //对账状态
      payTypeList: [], //付款方式
      quotaRecordDate: [
        {
          name: 11,
        },
      ],
      pageParams: {
        total: 0,
        page: 1,
        per_page: 20,
      },
      form: {
        id: "",
        credit: "",
      },
      rules: {
        credit: [
          { required: true, validator: valiNumberPass, trigger: "blur" },
        ],
      },
      search_date: ["", ""],
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
    this.getDictionary();
    this.getAdvanceSetting();
    this.getReconciliation();
    this.searchFormWidth();
  },
  methods: {
    // 合计
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
        this.verifyStatusList = res.data.verifyStatusList;
        this.payTypeList = res.data.payTypeList;
      });
    },
    // 获取窗口的宽度,调整表格高度
    searchFormWidth() {
      let w = window.innerWidth;
      if (w <= 1468) {
        this.heightSearch = true;
      } else {
        this.heightSearch = false;
      }
    },
    // 获取单位值
    getAdvanceSetting() {
      this.$api.getAdvanceSetting().then((res) => {
        this.currency_unit_symbol = res.data.currency_unit_symbol; // 单价单位
      });
    },
    // 查询
    handleChangePageParams() {
      this.pageParams.page = 1;
      this.getReconciliation();
    },
    // 列表
    getReconciliation() {
      this.$api.getReconciliation(this.pageParams).then((res) => {
        this.tableData = res.data.data;
        this.pageParams.total = res.data.meta.total;
      });
    },
    // 详情
    getDetails(id) {
      this.$router.push({
        name: "ReconciliationDetails",
        query: {
          id: id,
          type: 1,
        },
      });
    },
    // 对账
    getSettlement(id) {
      this.$router.push({
        name: "ReconciliationDetails",
        query: {
          id: id,
          type: 2,
        },
      });
    },
    // 删除
    delSettlement(id) {
      this.$confirm(this.$t("ConfirmToCancel"), this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      }).then(() => {
        this.$api.delSettlement(id).then((res) => {
          if (res.code == 200) {
            this.getReconciliation();
          }
        });
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
  .conditional-box {
    float: left;
    margin-bottom: 10px;
  }
  .but {
    float: right;
    margin-bottom: 10px;
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