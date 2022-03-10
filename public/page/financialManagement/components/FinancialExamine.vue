<template>
  <!-- 现金充值审核 -->
  <div class="financial-examine">
    <div class="tit">{{ $t("DetailsOnLine") }}</div>
    <div class="line-details">
      <span
        >{{ $t("rechargeCargoOwner") }}: &nbsp;&nbsp;{{
          info.merchant_name
        }}</span
      >
      <span>{{ $t("lineName") }}: &nbsp;&nbsp;{{ info.line_name }}</span>
      <span>{{ $t("PickupDriver") }}: &nbsp;&nbsp;{{ info.driver_name }}</span>
      <span
        >{{ $t("SingleRecharge") }}: &nbsp;&nbsp;{{ info.recharge_count }}</span
      >
    </div>

    <div class="tit">{{ $t("FinancialDetails") }}</div>
    <el-table
      max-height="600px"
      :data="info.recharge_list"
      v-loading="global_loading()"
      element-loading-text="loading..."
      border
      style="width: 100%"
    >
      <el-table-column
        align="center"
        prop="transaction_number"
        :label="$t('serialNo')"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="out_user_id"
        :label="$t('customerID')"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="recharge_amount"
        :label="$t('rechargeAmount') + '(' + currency_unit_symbol + ')'"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="recharge_time"
        :label="$t('rechargeTime')"
      >
      </el-table-column>
    </el-table>

    <div class="tit">{{ $t("ConfirmationPaymentAmount") }}</div>
    <el-form
      :rules="rules"
      ref="amountInfo"
      :model="amountInfo"
      label-width="150px"
    >
      <div>
        <el-form-item
          :label="$t('AmountPayable') + '(' + currency_unit_symbol + ')'"
        >
          {{ info.total_recharge_amount }}
        </el-form-item>
        <el-form-item
          :label="$t('DetermineActualPaymentAmount')"
          prop="verify_recharge_amount"
        >
          <el-input
            v-model.trim="amountInfo.verify_recharge_amount"
            :placeholder="$t('PleaseEnterTheAmount')"
          ></el-input
          >&nbsp;({{ currency_unit_symbol }})
        </el-form-item>
        <el-form-item :label="$t('Remark')" prop="verify_remark">
          <el-input
            type="textarea"
            :rows="4"
            :placeholder="$t('enterContent')"
            v-model="amountInfo.verify_remark"
            style="width: 90%"
          >
          </el-input>
        </el-form-item>
      </div>
    </el-form>

    <div class="submit-but">
      <el-button
        type="primary"
        @click="submit"
        :loading="this.$store.state.config.button_loading"
        class="submit"
        >{{ $t("Submit") }}</el-button
      >
    </div>
  </div>
</template>

<script>
import loading from "@/mixin/global_loading";

export default {
  // name: "FinancialExamine",
  mixins: [loading],
  data() {
    return {
      currency_unit_symbol: "", //单位
      tableData: [],
      info: {},
      amountInfo: {},
      rules: {
        verify_recharge_amount: [
          {
            required: true,
            trigger: "blur",
            message: this.$t("Required"),
          },
        ],
        verify_remark: [
          {
            required: true,
            trigger: "blur",
            message: this.$t("Required"),
          },
        ],
      },
    };
  },
  created() {
    this.getRechargeDetails();
    this.getAdvanceSetting();
  },
  methods: {
    // 获取单位值
    getAdvanceSetting() {
      this.$api.getAdvanceSetting().then((res) => {
        this.currency_unit_symbol = res.data.currency_unit_symbol; // 单价单位
      });
    },
    getRechargeDetails() {
      // 列表
      this.$api.getRechargeDetails(this.$route.query.id).then((res) => {
        this.info = res.data;
        this.tableData.push(res.data);
      });
    },
    submit() {
      this.$refs.amountInfo.validate((valid) => {
        if (!valid) return;
        this.amountInfo.verify_remark = this.amountInfo.verify_remark.trim();
        console.log(
          this.amountInfo.verify_recharge_amount,
          this.info.total_recharge_amount
        );
        if (
          +this.amountInfo.verify_recharge_amount !=
          +this.info.total_recharge_amount
        ) {
          this.$confirm(
            `${this.$t("routeTurnInAmount")}<span style="color: red;">${
              this.amountInfo.verify_recharge_amount + this.currency_unit_symbol
            }</span>${this.$t("OrAmountPayable")}<span style="color: red;">${
              this.info.total_recharge_amount + this.currency_unit_symbol
            }</span>${this.$t("pleaseMakeReviewCorrect")}`,
            this.$t("Notice"),
            { dangerouslyUseHTMLString: true },
            {
              confirmButtonText: this.$t("Confirm"),
              cancelButtonText: this.$t("Cancel"),
              type: "warning",
            }
          )
            .then(() => {
              this.$api
                .putRecharge({ ...this.amountInfo, id: this.$route.query.id })
                .then((res) => {
                  if (res.code === 200) {
                    this.$router.push({ name: "CashRechargeList" });
                  }
                });
            })
            .catch(() => {});
        } else {
          this.$api
            .putRecharge({ ...this.amountInfo, id: this.$route.query.id })
            .then((res) => {
              if (res.code === 200) {
                this.$router.push({ name: "CashRechargeList" });
              }
            });
        }
      });
    },
  },
};
</script>
<style lang="less" scoped>
.financial-examine {
  padding: 20px 25px 10px;
  background: #ffffff;
  border: 1px solid rgba(228, 235, 241, 1);
  .tit {
    font-size: 18px;
    font-weight: 400;
    padding: 20px 0;
  }
  .line-details {
    span {
      margin-right: 20px;
    }
  }
  .image-slot {
    width: 100px;
    height: 100px;
    display: flex;
    justify-content: center;
    align-items: center;
    border: 1px solid red;
  }
  .submit-but {
    text-align: right;
  }
}
</style>
<style lang="less">
.financial-examine {
  .el-date-editor {
    width: 100%;
  }
  .el-input {
    max-width: 328px;
  }
}
</style>
