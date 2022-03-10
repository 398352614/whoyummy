<template>
  <!-- 第三方充值列表 -->
  <div class="cash-recharge-list">
    <div class="conditional">
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
      <el-select
        @keyup.enter.native="handleChangePageParams"
        clearable
        size="small"
        v-model="pageParams.status"
        filterable
        :placeholder="$t('pleaseSelectActualTurnIn')"
        class="search-bar"
      >
        <el-option
          v-for="item in verifyStatus"
          :key="item.id"
          :label="item.name"
          :value="item.id"
        >
        </el-option>
      </el-select>
      <el-select
        @keyup.enter.native="handleChangePageParams"
        clearable
        size="small"
        v-model="pageParams.merchant_id"
        filterable
        :placeholder="$t('PleaseSelectRechargeCargoOwner')"
        class="search-bar"
      >
        <el-option
          v-for="item in merchantsList"
          :key="item.id"
          :label="item.name"
          :value="item.id"
        >
        </el-option>
      </el-select>
      <el-input
        @keyup.enter.native="handleChangePageParams"
        v-model="pageParams.key_word"
        clearable
        :placeholder="$t('PickupDriver')"
        size="small"
        class="search-bar"
      >
      </el-input>
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
      :data="hasPerm('recharge.index') ? tableData : []"
      v-loading="global_loading()"
      element-loading-text="loading..."
      border
      style="width: 100%"
      :cell-style="{ padding: '5px 0px' }"
    >
      <template slot="empty" v-if="!hasPerm('recharge.index')">
        <div style="font-size: 18px">
          {{ $t("AdministratorActivate") }}
        </div>
      </template>
      <el-table-column type="index" align="center" width="50">
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
                :disabled="hasPerm('recharge.index') ? true : false"
              >
                <div
                  :style="
                    hasPerm('recharge.index') ? '' : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('recharge.index')"
                    @click.native="getReportDetails(scope.row.id)"
                  >
                    <el-button
                      :disabled="!hasPerm('recharge.index')"
                      class="operation"
                      size="small"
                      type="text"
                      >{{ $t("details") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 审核 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('recharge.verify') ? true : false"
              >
                <div
                  :style="
                    hasPerm('recharge.verify') ? '' : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('recharge.verify')"
                    v-if="scope.row.status === 1"
                    @click.native="financialExamine(scope.row.id)"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('recharge.verify')"
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
        prop="line_name"
        :label="$t('lineName')"
        width="250"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="status_name"
        :label="$t('actualTurnInStatus')"
        width="250"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="total_recharge_amount"
        :label="$t('routeRechargeAmount') + '(' + currency_unit_symbol + ')'"
        width="250"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="verify_recharge_amount"
        width="250"
        :label="
          $t('actualRouteTurnInAmount') + '(' + currency_unit_symbol + ')'
        "
      >
        <template slot-scope="scope">
          <span
            v-if="
              scope.row.verify_recharge_amount ==
              scope.row.total_recharge_amount
            "
            >{{ scope.row.verify_recharge_amount }}</span
          >
          <span style="color: red" v-else>{{
            scope.row.verify_recharge_amount
          }}</span>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        prop="driver_name"
        :label="$t('PickupDriver')"
        width="200"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="execution_date"
        :label="$t('DistributeDate')"
        width="200"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="verify_remark"
        :label="$t('reviewNotes')"
        width="200"
      >
      </el-table-column>
    </el-table>
    <base-pagination
      :pageParams="pageParams"
      @search="getReport"
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
        key_word: "",
        status: "",
        merchant_id: "",
        out_user_id: "",
        status: "",
        begin_date: "",
        end_date: "",
        keyword: "",
        total: 0,
        page: 1,
        per_page: 20,
      },
      search_date: ["", ""],
      merchantsList: [],
      verifyStatus: [
        {
          name: this.$t("notReviewed"),
          id: 1,
        },
        {
          name: this.$t("reviewed"),
          id: 2,
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
    // unit() {
    //   return this.$store.state.globalData.unit;
    // },
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
    this.getReport();
    this.getMerchants();
  },
  created() {
    this.getReport();
    this.getMerchants();
    this.getAdvanceSetting(); //单位
  },
  methods: {
    // 获取单位值
    getAdvanceSetting() {
      this.$api.getAdvanceSetting().then((res) => {
        this.currency_unit_symbol = res.data.currency_unit_symbol; // 单价单位
      });
    },
    getReport() {
      // 列表
      this.$api.getRecharge(this.pageParams).then((res) => {
        this.tableData = res.data.data;
        this.pageParams.total = res.data.meta.total;
      });
    },
    handleChangePageParams() {
      this.pageParams.page = 1;
      this.getReport();
    },
    getReportDetails(id) {
      // 详情
      this.$router.push({ name: "FinancialDetails", query: { id: id } });
    },

    financialExamine(id) {
      // 审核
      this.$router.push({ name: "FinancialExamine", query: { id: id } });
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
