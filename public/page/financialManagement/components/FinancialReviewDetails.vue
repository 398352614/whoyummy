<template>
  <div>
    <!-- 充值审核详情  -->
    <template>
      <div class="online-recharge">
        <div class="recharge-box">
          <div class="review">{{ details.verify_status_name }}</div>
          <el-form ref="form" :rules="rules" :model="form" label-width="180px">
            <el-form-item :label="$t('cargoOwnerNo')" prop="merchant_id">
              {{ details.code }}
            </el-form-item>
            <el-form-item :label="$t('DeductionMethod')" prop="name">
              {{ details.pay_type_name }}
            </el-form-item>
            <el-form-item
              :label="$t('RechargeAmount') + currency_unit_symbol"
              prop="amount"
            >
              {{ details.expect_amount }}
            </el-form-item>
            <el-form-item :label="$t('remark')" prop="remark">
              {{ details.remark }}
            </el-form-item>
            <el-form-item :label="$t('screenshot')" prop="image">
              <img :src="details.picture_list" alt="" width="200px" />
            </el-form-item>
            <el-form-item :label="$t('Created')" prop="image">
              {{ details.created_at }}
            </el-form-item>
            <el-form-item :label="$t('operator')" prop="image">
              {{ details.operator_name }}
            </el-form-item>
            <el-form-item :label="$t('ConfirmTopUPAmount')" prop="amount">
              <!-- <span>—</span> -->
              <el-input v-model="form.amount" placeholder=" "></el-input>
            </el-form-item>
            <el-form-item :label="$t('Reviewer')" prop="amount">
              {{ details.operator_name }}
            </el-form-item>
            <el-form-item :label="$t('reviewTime')" prop="amount">
              {{ details.operator_name }}
            </el-form-item>
          </el-form>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
export default {
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
      currency_unit_symbol: "",
      details: {},
      form: {
        amount: "",
      },
      rules: {
        amount: [
          { required: true, validator: valiNumberPass, trigger: "blur" },
        ],
      },
    };
  },

  created() {
    this.getAdvanceSetting();
    this.getDetailsVerify(this.$route.query.id);
  },
  methods: {
    // 获取单位值
    getAdvanceSetting() {
      this.$api.getAdvanceSetting().then((res) => {
        this.currency_unit_symbol = res.data.currency_unit_symbol; //单价单位
      });
    },
    getDetailsVerify(id) {
      this.$api.getDetailsVerify(id).then((res) => {
        this.details = res.data;
      });
    },
  },
};
</script>

<style lang="less" scoped>
.online-recharge {
  min-height: calc(100vh - 120px);
  background-color: #fff;
  .recharge-box {
    padding: 20px;
    .review {
      margin-right: 50px;
      font-size: 25px;
      font-weight: 400;
      color: red;
      text-align: right;
    }
    .el-input {
      width: 370px;
    }
  }
  .search-bar {
    margin-bottom: 10px;
  }
}
</style>