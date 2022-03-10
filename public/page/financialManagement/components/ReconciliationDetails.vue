<template>
  <!-- 账单详情 -->
  <div class="reconciliation-details">
    <div>
      <div class="reconciliation">
        <h2>{{ $t("BillDetails") }}</h2>
        <div>
          <!-- <el-button
            size="small"
            @click="downloadBill"
            type="primary"
            class="common but"
            :loading="this.$store.state.config.button_loading"
            >{{ $t("DownloadBill") }}</el-button
          > -->
        </div>
      </div>
      <div class="reconciliation-title">
        <span
          >{{ $t("PayerName") }}: {{ reconciliationDetails.payer_name }}
        </span>
        <span
          >{{ $t("StatementNumber") }}:{{ reconciliationDetails.verify_no }}
        </span>
        <span
          >{{ $t("StartBillingPeriod") }}:
          {{ reconciliationDetails.begin_date }}
        </span>
        <span
          >{{ $t("ClosingPeriod") }}:{{ reconciliationDetails.end_date }}
        </span>
      </div>
      <div class="table">
        <!-- 自定义分页 .slice(
              (this.pageParams.currentPage - 1) * this.pageParams.pagesize,
              this.pageParams.currentPage * this.pageParams.pagesize
            ) -->
        <el-table
          :data="reconciliationDetails.bill_list"
          border
          :height="
            reconciliationDetails.bill_list.length <= 0 || null
              ? ''
              : 'calc(100vh - 350px)'
          "
          style="width: 100%"
          :summary-method="getSummaries"
          show-summary
          ref="table"
        >
          <el-table-column type="index" align="center" width="55">
            <template slot="header">
              <span>{{ $t("Number") }}</span>
            </template>
          </el-table-column>
          <el-table-column
            align="center"
            prop="create_date"
            :label="$t('RecordDate')"
          >
          </el-table-column>
          <el-table-column
            align="center"
            prop="object_no"
            :label="$t('SystemNumber')"
          >
          </el-table-column>
          <el-table-column
            align="center"
            prop="payer_name"
            :label="$t('PayerName')"
          >
          </el-table-column>
          <el-table-column
            align="center"
            prop="fee_name"
            :label="$t('TypesOfFee')"
          >
          </el-table-column>
          <el-table-column
            align="center"
            prop="pay_type_name"
            :label="$t('paymentMethod')"
          >
          </el-table-column>
          <el-table-column
            align="center"
            prop="expect_amount"
            :label="$t('fees') + '(' + currency_unit_symbol + ')'"
          >
          </el-table-column>
          <el-table-column
            align="center"
            prop="rest_amount"
            :label="$t('PendingPayment') + '(' + currency_unit_symbol + ')'"
          >
          </el-table-column>
          <!-- <el-table-column
            align="center"
            prop="status_name"
            :label="$t('PaymentStatus')"
          >
          </el-table-column> -->
        </el-table>
        <!-- 自定义分页 -->
        <!-- <base-pagination
          :pageParams="pageParams"
          class="pagination-size"
          @current-change="current_change"
          :total="reconciliationDetails.bill_list.length"
        ></base-pagination> -->
      </div>
      <div
        class="reconciliation-title"
        style="margin-bottom: 20px"
        v-if="reconciliationDetails.status == 2"
      >
        <!-- <span>
          {{ $t("payment") }}: {{ reconciliationDetails.pay_type_name }}
        </span> -->
        <span>
          {{ $t("ReconciliationTime") }}:
          {{ reconciliationDetails.verify_time }}
        </span>
        <span>{{ $t("remark") }}: {{ reconciliationDetails.remark }}</span>
      </div>
      <div class="dialog-footer" v-if="this.$route.query.type == 2">
        <el-button
          type="primary"
          @click="settlement"
          v-if="reconciliationDetails.status != 2"
          >{{ $t("Reconciliation1") }}</el-button
        >
        <!-- <el-button @click="cancel">{{ $t("Cancel") }}</el-button> -->
      </div>
    </div>
    <el-dialog
      :title="$t('Reconciliation1')"
      :visible.sync="dialogVisible"
      width="50%"
    >
      <el-table max-height="600px" :data="tableDate" style="width: 100%" border>
        <!--支付方式 -->
        <el-table-column :label="$t('payment')" align="center" width="200">
          <template slot-scope="scope">
            <el-select
              v-model="scope.row.pay_type"
              :placeholder="$t(' ')"
              size="small"
            >
              <el-option
                v-for="item in payTypeList"
                :key="item.id"
                :label="item.name"
                :value="item.id"
              >
              </el-option>
            </el-select>
          </template>
        </el-table-column>
        <!-- 费用 -->
        <el-table-column
          :label="$t('cost') + '(' + currency_unit_symbol + ')'"
          align="center"
        >
          <template slot-scope="scope">
            {{ scope.row.actual_amount }}
          </template>
        </el-table-column>
        <!-- 备注 -->
        <el-table-column :label="$t('remark')" align="center">
          <template slot-scope="scope">
            <el-input v-model="scope.row.remark" size="small"></el-input>
          </template>
        </el-table-column>
      </el-table>
      <span slot="footer" class="dialog-footer">
        <el-button type="primary" @click="addSettlement">{{
          $t("Confirm")
        }}</el-button>
        <el-button @click="dialogVisible = false">{{ $t("Cancel") }}</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
export default {
  data() {
    return {
      pageParams: {
        total: 10, //默认数据总数
        pagesize: 10, //每页的数据条数
        currentPage: 1, //默认开始页面
      },
      dialogVisible: false,
      currency_unit_symbol: "",
      fees: "",
      reconciliationDetails: {},
      payTypeList: [], //付款方式
      form: {
        id: this.$route.query.id,
        status: 2,
        bill_list: [],
      },
      tableDate: [{ pay_type: 4, actual_amount: "", remark: "" }],
    };
  },
  // 表格合计不展示问题
  updated() {
    this.$nextTick(() => {
      this.$refs["table"].doLayout();
    });
  },
  created() {
    this.getAdvanceSetting();
    this.getDictionary();
    this.getSettlementDetails(this.$route.query.id);
  },
  methods: {
    current_change(currentPage) {
      this.pageParams.currentPage = currentPage;
    },
    getDictionary() {
      this.$api.getDictionary().then((res) => {
        this.payTypeList = res.data.payTypeList;
      });
    },
    // 合计
    getSummaries(param) {
      const { columns, data } = param;
      const sums = [];
      columns.forEach((column, index) => {
        if (index === 0) {
          sums[index] = this.$t("Total");
        } else if (index === 6 || index === 7) {
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
      this.tableDate[0].actual_amount = sums[6];
      return sums;
    },
    // 获取单位值
    getAdvanceSetting() {
      this.$api.getAdvanceSetting().then((res) => {
        this.currency_unit_symbol = res.data.currency_unit_symbol; // 单价单位
      });
    },
    getSettlementDetails(id) {
      this.$api.getSettlementDetails(id).then((res) => {
        this.reconciliationDetails = res.data;
      });
    },
    // 对账
    settlement() {
      // this.dialogVisible = true;
      this.tableDate = [{ pay_type: 4, actual_amount: "", remark: "" }];
      this.$confirm(
        this.$t("WhetherReconciliationCompleted"),
        this.$t("Notice"),
        {
          confirmButtonText: this.$t("Confirm"),
          cancelButtonText: this.$t("Cancel"),
          type: "warning",
        }
      ).then(() => {
        let tableDate = this.tableDate;
        tableDate.map((el) => {
          this.form = {
            ...this.form,
            pay_type: el.pay_type,
            actual_amount: el.actual_amount,
            remark: el.remark,
          };
        });
        this.$api.addSettlement(this.form).then((res) => {
          if (res.code == 200) {
            // this.dialogVisible = false;
            this.getSettlementDetails(this.$route.query.id);
          }
        });
      });
    },
    // 取消返回列表
    cancel() {
      this.$router.go(-1);
    },
    // 确定对账
    addSettlement() {
      this.$confirm(
        this.$t("WhetherReconciliationCompleted"),
        this.$t("Notice"),
        {
          confirmButtonText: this.$t("Confirm"),
          cancelButtonText: this.$t("Cancel"),
          type: "warning",
        }
      ).then(() => {
        let tableDate = this.tableDate;
        tableDate.map((el) => {
          this.form = {
            ...this.form,
            pay_type: el.pay_type,
            actual_amount: el.actual_amount,
            remark: el.remark,
          };
        });
        this.$api.addSettlement(this.form).then((res) => {
          if (res.code == 200) {
            this.dialogVisible = false;
            this.getSettlementDetails(this.$route.query.id);
          }
        });
      });
    },
    // 下载账单
    downloadBill() {},
  },
};
</script>

<style lang="less" scoped>
.reconciliation-details {
  padding: 20px;
  padding-left: 80px;
  background: #fff;
  height: calc(100vh - 160px);
  .reconciliation {
    display: flex;
    justify-content: space-between;
  }
  .reconciliation-title {
    span {
      padding-right: 80px;
      font-size: 15px;
    }
  }
  .table {
    margin: 20px auto;
  }
}
</style>