<template>
  <!-- 代收货款 -->
  <div class="cash-recharge-list">
    <el-tabs type="border-card">
      <el-tab-pane :label="$t('PaymentSummary')"></el-tab-pane>
      <el-tab-pane :label="$t('PaymentRecovery')"></el-tab-pane>
      <el-tab-pane :label="$t('Payment')"></el-tab-pane>
    </el-tabs>
    <div class="conditional">
      <div class="conditional-box">
        <div>
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
          <el-input
            @keyup.enter.native="handleChangePageParams"
            v-model="pageParams.key_word"
            clearable
            :placeholder="$t('pleaseEnterTheOwnerNumber')"
            size="small"
            class="search-bar"
          >
          </el-input>
          <el-input
            @keyup.enter.native="handleChangePageParams"
            v-model="pageParams.key_word"
            clearable
            :placeholder="$t('PleaseEnterOrderNo')"
            size="small"
            class="search-bar"
          >
          </el-input>
          <el-select
            @keyup.enter.native="handleChangePageParams"
            clearable
            size="small"
            v-model="pageParams.merchant_id"
            filterable
            :placeholder="$t('PleaseSelectBillingStatus')"
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

          <el-button
            size="small"
            @click="handleChangePageParams"
            type="primary"
            :loading="this.$store.state.config.button_loading"
            >{{ $t("Search") }}</el-button
          >
        </div>
        <div>
          <!-- <el-button
            size="small"
            @click="handleChangePageParams"
            type="primary"
            class="common"
            :loading="this.$store.state.config.button_loading"
            >{{ $t("GenerateStatement") }}</el-button
          > -->
          <el-button
            size="small"
            @click="handleChangePageParams"
            type="primary"
            class="common"
            :loading="this.$store.state.config.button_loading"
            >{{ $t("Export") }}</el-button
          >
        </div>
      </div>
      <el-table
        height="calc(100vh - 295px)"
        :data="tableData"
        v-loading="global_loading()"
        element-loading-text="loading..."
        border
        style="width: 100%"
        :cell-style="{ padding: '5px 0px' }"
      >
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
                <el-dropdown-item @click.native="getSettlement(scope.row.id)">
                  <el-button class="operation" size="small" type="text">{{
                    $t("Recycle")
                  }}</el-button>
                </el-dropdown-item>
              </el-dropdown-menu>
            </el-dropdown>
          </template>
        </el-table-column>
        <el-table-column
          align="center"
          prop="line_name"
          :label="$t('orderNo')"
          width="250"
        >
        </el-table-column>
        <el-table-column
          align="center"
          prop="status_name"
          :label="$t('OrderStatus')"
          width="250"
        >
        </el-table-column>
        <el-table-column
          align="center"
          prop="RecordTime"
          :label="$t('RecordTime')"
          width="250"
        >
        </el-table-column>
        <el-table-column
          align="center"
          prop="line_name"
          :label="$t('PaymentStatus')"
          width="250"
        >
        </el-table-column>
        <el-table-column
          align="center"
          prop="line_name"
          :label="$t('shipperName')"
          width="250"
        >
        </el-table-column>
        <el-table-column
          align="center"
          prop="line_name"
          :label="$t('cargoOwnerNo')"
          width="250"
        >
        </el-table-column>

        <el-table-column
          align="center"
          prop="total_recharge_amount"
          :label="$t('CollectMoney')"
          width="250"
        >
        </el-table-column>
        <el-table-column
          align="center"
          prop="line_name"
          :label="$t('NameAccountHolder')"
          width="250"
        >
        </el-table-column>
        <el-table-column
          align="center"
          prop="line_name"
          :label="$t('bankName')"
          width="250"
        >
        </el-table-column>
        <el-table-column
          align="center"
          prop="verify_recharge_amount"
          width="250"
          :label="$t('BankAccount')"
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
      </el-table>
    </div>

    <base-pagination
      :pageParams="pageParams"
      @search="getReport"
      class="pagination-size"
    ></base-pagination>
    <div>
      <el-dialog
        :title="$t('Settlement')"
        :visible.sync="dialogVisible"
        width="50%"
      >
        <span style="font-size: 15px">{{
          $t("ConfirmCollectCollectTotalAmount")
        }}</span>
        <span style="color: red; font-size: 20px">
          {{ "100.00" }}
        </span>
        <span>{{ currency_unit_symbol }} ?</span>
        <span slot="footer" class="dialog-footer">
          <el-button @click="dialogVisible = false">{{
            $t("Cancel")
          }}</el-button>
          <el-button type="primary" @click="dialogVisible = false">{{
            $t("Confirm")
          }}</el-button>
        </span>
      </el-dialog>
    </div>
  </div>
</template>

<script>
import BasePagination from "@/components/BasePagination/BasePagination";
import loading from "@/mixin/global_loading";
// import Dispatch from "../settingManagement/Dispatch.vue";

export default {
  name: "CashRechargeList",
  components: {
    BasePagination,
    // Dispatch,
  },
  mixins: [loading],
  data() {
    return {
      currency_unit_symbol: "", //单位
      dialogVisible: false,
      tableData: [],
      gridData: [
        {
          post_code_start: 1,
        },
      ],
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
    getSettlement() {
      this.dialogVisible = true;
    },
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
  border: 1px solid rgba(228, 235, 241, 1);
  background: #ffffff;
  padding: 0px 0px 10px;
  .el-tabs--border-card {
    box-shadow: none;
    border-style: none;
  }
  .conditional {
    margin-top: -30px;
    padding: 20px;
    .conditional-box {
      padding: 0px 0 10px;
      display: flex;
      justify-content: space-between;
    }
    .dialog-footer {
      text-align: center;
    }
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
