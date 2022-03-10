<template>
  <!-- 充值审核列表 -->
  <div class="cash-recharge-list">
    <div class="conditional">
      <span style="margin-top: 6px">{{ $t("Created") }} </span>
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
      <!-- 货主编号 -->
      <el-input
        @keyup.enter.native="handleChangePageParams"
        v-model="pageParams.code"
        clearable
        :placeholder="$t('pleaseEnterTheOwnerNumber')"
        size="small"
        class="search-bar"
      >
      </el-input>
      <!-- 审核状态 -->
      <el-select
        @keyup.enter.native="handleChangePageParams"
        clearable
        size="small"
        v-model="pageParams.verify_status"
        filterable
        :placeholder="$t('PleaseSelectReviewStatus')"
        class="search-bar"
      >
        <el-option
          v-for="item in billVerifyStatusList"
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
    <el-table
      height="calc(100vh - 235px)"
      ref="table"
      :data="hasPerm('bill.merchant-recharge-index') ? tableData : []"
      :summary-method="getSummaries"
      show-summary
      v-loading="global_loading()"
      element-loading-text="loading..."
      border
      style="width: 100%"
      :cell-style="{ padding: '5px 0px' }"
    >
      <template slot="empty" v-if="!hasPerm('bill.merchant-recharge-index')">
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
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('bill.merchant-recharge-index') ? true : false"
              >
                <div
                  :style="
                    hasPerm('bill.merchant-recharge-index') ? '' : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    @click.native="getDetailsVerify(scope.row.id)"
                    :disabled="!hasPerm('bill.merchant-recharge-index')"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('bill.merchant-recharge-index')"
                      >{{ $t("details") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('bill.merchant-recharge-verify') ? true : false"
              >
                <div
                  :style="hasPerm('bill.merchant-recharge-verify') ? '' : 'cursor: not-allowed'"
                >
                  <el-dropdown-item
                    @click.native="getReview(scope.row.id)"
                    v-if="scope.row.verify_status == 1"
                    :disabled="!hasPerm('bill.merchant-recharge-verify')"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('bill.merchant-recharge-verify')"
                      >{{ $t("review") }}</el-button
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
        prop="verify_status_name"
        :label="$t('reviewStatus')"
      >
        <template slot-scope="scope">
          <!-- 判断未审核改变颜色 -->
          <span v-if="scope.row.verify_status == 1" style="color: #e89e42">{{
            scope.row.verify_status_name
          }}</span>
          <span v-else>{{ scope.row.verify_status_name }}</span>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        prop="payer_name"
        :label="$t('shipperName')"
      >
      </el-table-column>
      <el-table-column align="center" prop="code" :label="$t('cargoOwnerNo')">
      </el-table-column>
      <el-table-column
        align="center"
        prop="pay_mode_name"
        :label="$t('RechargeMethod')"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="pay_type_name"
        :label="$t('PaymentTypes')"
      >
      </el-table-column>
      <el-table-column align="center" prop="bill_no" :label="$t('BillNumber')">
      </el-table-column>
      <el-table-column
        align="center"
        prop="expect_amount"
        :label="$t('TransactionAmount') + '(' + currency_unit_symbol + ')'"
        width="180"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="actual_amount"
        :label="$t('ConfirmAmount') + '(' + currency_unit_symbol + ')'"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="merchant_group_name"
        :label="$t('UserGroup')"
      >
      </el-table-column>
      <el-table-column align="center" prop="created_at" :label="$t('Created')">
      </el-table-column>
    </el-table>
    <base-pagination
      :pageParams="pageParams"
      @search="geVerify"
      class="pagination-size"
    ></base-pagination>
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
    return {
      currency_unit_symbol: "", //单位
      tableData: [],
      pageParams: {
        sign: 1,
        code: "",
        end_date: "",
        begin_date: "",
        verify_status: "",
        mode: 1,
        total: 0,
        page: 1,
        per_page: 20,
      },
      search_date: ["", ""],
      merchantsList: [],
      billVerifyStatusList: [],
      modeList: [
        {
          id: 1,
          name: "账号充值",
        },
        {
          id: 2,
          name: "运费支付",
        },
      ],
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
  updated() {
    this.$nextTick(() => {
      this.$refs["table"].doLayout();
    });
  },
  created() {
    this.getDictionary();
    this.getAdvanceSetting(); //单位
    this.geVerify();
    // this.getMerchants();
  },
  methods: {
    getDictionary() {
      this.$api.getDictionary().then((res) => {
        this.billVerifyStatusList = res.data.billVerifyStatusList;
      });
    },
    // 合计
    getSummaries(param) {
      const { columns, data } = param;
      const sums = [];
      columns.forEach((column, index) => {
        if (index === 0) {
          sums[index] = this.$t("Total");
        } else if (index === 7 || index === 8) {
          const values = data.map((item) => Number(item[column.property]));
          if (!values.every((value) => isNaN(value))) {
            sums[index] = values.reduce((prev, curr) => {
              const value = Number(curr);
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
      sums[7] = sums[7] ? sums[7].toFixed(2) : "0.00";
      sums[8] = sums[8] ? sums[8].toFixed(2) : "0.00";

      return sums;
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
        console.log(this.pageParams);
        console.log("res");
        console.log(res.data.data);
        this.tableData = res.data.data;
        this.pageParams.total = res.data.meta.total;
      });
    },
    handleChangePageParams() {
      this.pageParams.page = 1;
      this.geVerify();
    },
    getDetailsVerify(id) {
      // 详情
      this.$router.push({
        name: "FinancialReview",
        query: { id: id, type: 1 },
      });
    },

    getReview(id) {
      // 审核
      this.$router.push({
        name: "FinancialReview",
        query: { id: id, type: 2 },
      });
    },
    getMerchants() {
      // 货主列表
      this.$api.getMerchantsPublic().then((res) => {
        this.merchantsList = res.data.data;
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