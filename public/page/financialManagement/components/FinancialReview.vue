<template>
  <div>
    <!-- 财务审核 -->
    <template>
      <div class="online-recharge">
        <div class="recharge-box">
          <div class="review">{{ details.verify_status_name }}</div>
          <el-form ref="form" :rules="rules" :model="form" label-width="180px">
            <el-form-item :label="$t('cargoOwnerNo')">
              {{ details.code }}
            </el-form-item>
            <el-form-item :label="$t('RechargeMethod')">
              {{ details.pay_type_name }}
            </el-form-item>
            <el-form-item :label="$t('RechargeAmount') + currency_unit_symbol">
              {{ details.expect_amount }}
            </el-form-item>
            <el-form-item :label="$t('remark')">
              {{ details.remark }}
            </el-form-item>
            <el-form-item :label="$t('screenshot')">
              <img :src="details.picture_list" alt="" width="200px" />
            </el-form-item>
            <el-form-item :label="$t('Created')">
              {{ details.created_at }}
            </el-form-item>
            <el-form-item :label="$t('operator')">
              {{ details.operator_name }}
            </el-form-item>
            <div
              v-if="this.$route.query.type == 1 && details.verify_status != 1"
            >
              <el-form-item :label="$t('ConfirmTopUPAmount') + '(' + currency_unit_symbol + ')'">
                <!-- <span>-</span> -->
                {{ details.actual_amount }}
              </el-form-item>
              <el-form-item :label="$t('Reviewer')">
                {{ details.operator_name }}
              </el-form-item>
              <el-form-item :label="$t('reviewTime')">
                {{ details.updated_at }}
              </el-form-item>
            </div>
            <el-form-item
              :label="$t('ConfirmTopUPAmount') + '(' + currency_unit_symbol + ')'"
              prop="actual_amount"
              v-if="this.$route.query.type == 2"
            >
              <!-- <span>—</span> -->
              <el-input v-model="form.actual_amount" placeholder=" "></el-input>
            </el-form-item>

            <el-form-item
              style="margin-left: 20px"
              v-if="this.$route.query.type == 2"
            >
              <el-button type="primary" @click="reviewedSuccessfully"
                >{{ $t("reviewedSuccessfully") }}
              </el-button>
              <el-button type="warning" @click="refuse"
                >{{ $t("Refuse") }}
              </el-button>
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
        callback(new Error(this.$t("Thededuction")));
      } else if (value > 1000000) {
        callback(new Error(this.$t("Thededuction")));
      } else {
        if (value <= 0) {
          callback(new Error(this.$t("Thededuction")));
        } else {
          callback();
        }
      }
    };
    return {
      currency_unit_symbol: "",
      details: {},
      form: {
        id: "",
        actual_amount: "",
        verify_status: "",
      },
      rules: {
        actual_amount: [
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
    // 详情
    getDetailsVerify(id) {
      this.$api.getDetailsVerify(id).then((res) => {
        this.details = res.data;
      });
    },
    reviewedSuccessfully() {
      this.$confirm(this.$t("IsConfirmedPassReview") + "?", this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      }).then(() => {
        this.form.id = this.$route.query.id;
        this.form.verify_status = 2;
        this.$api.getReview(this.form).then((res) => {
          if (res.code == 200) {
            this.$router.push({ name: "FinancialReviewList" });
          }
        });
      });
    },
    refuse() {
      this.$confirm(this.$t("DoConfirmRejection") + "?", this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      })
        .then(() => {
          this.form.id = this.$route.query.id;
          this.form.verify_status = 3;
          this.$api.getReview(this.form).then((res) => {
            if (res.code == 200) {
              this.$router.push({ name: "FinancialReviewList" });
            }
          });
        })
        .catch(() => {});
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