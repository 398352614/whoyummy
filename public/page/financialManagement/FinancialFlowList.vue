<template>
  <!-- 资金流水 -->
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
      <!-- 交易类型 -->
      <el-select
        @keyup.enter.native="handleChangePageParams"
        clearable
        size="small"
        v-model="pageParams.mode"
        filterable
        :placeholder="$t('PleaseSelectTransactionType')"
        class="search-bar"
      >
        <el-option
          v-for="item in billModeList"
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
      :data="hasPerm('journal.index') ? tableData : []"
      :summary-method="getSummaries"
      show-summary
      v-loading="global_loading()"
      element-loading-text="loading..."
      border
      style="width: 100%"
      :cell-style="{ padding: '5px 0px' }"
    >
      <template slot="empty" v-if="!hasPerm('journal.index')">
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
                :disabled="hasPerm('journal.index') ? true : false"
              >
                <div
                  :style="hasPerm('journal.index') ? '' : 'cursor: not-allowed'"
                >
                  <el-dropdown-item
                    @click.native="getDetails(scope.row)"
                    :disabled="!hasPerm('journal.index')"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('journal.index')"
                      >{{ $t("details") }}</el-button
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
        prop="journal_no"
        :label="$t('TransactioSerialNumber')"
      >
      </el-table-column>
      <el-table-column align="center" prop="code" :label="$t('cargoOwnerNo')">
      </el-table-column>
      <el-table-column
        align="center"
        prop="pay_type_name"
        :label="$t('PaymentTypes')"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="mode_name"
        :label="$t('TransactionType')"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="actual_amount"
        :label="$t('Amount') + '(' + currency_unit_symbol + ')'"
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
      @search="getJournal"
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
        code: "",
        mode: "",
        end_date: "",
        begin_date: "",
        total: 0,
        page: 1,
        per_page: 20,
      },
      search_date: ["", ""],
      merchantsList: [],
      billModeList: [
        {
          name: this.$t("AccountRecharge"),
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
    this.getJournal();
    this.getAdvanceSetting(); //单位
    this.getDictionary();
  },
  methods: {
    getDictionary() {
      this.$api.getDictionary().then((res) => {
        this.billModeList = res.data.billModeList;
      });
    },
    // 合计
    getSummaries(param) {
      const { columns, data } = param;
      const sums = [];
      columns.forEach((column, index) => {
        if (index === 0) {
          sums[index] = this.$t("Total");
        } else if (index === 6) {
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
      sums[6] = sums[6] ? sums[6].toFixed(2) : "0.00";
      return sums;
    },
    // 获取单位值
    getAdvanceSetting() {
      this.$api.getAdvanceSetting().then((res) => {
        this.currency_unit_symbol = res.data.currency_unit_symbol; // 单价单位
      });
    },
    getJournal() {
      // 列表
      this.$api.getJournal(this.pageParams).then((res) => {
        this.tableData = res.data.data;
        this.pageParams.total = res.data.meta.total;
      });
    },
    handleChangePageParams() {
      this.pageParams.page = 1;
      this.getJournal();
    },
    getDetails(rows) {
      // 详情
      this.$router.push({
        name: "FinancialFlowDetails",
        query: { row: rows },
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
