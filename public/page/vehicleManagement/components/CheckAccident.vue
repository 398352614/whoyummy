<template>
  <!-- 事故新增 -->
  <div class="accident_box">
    <el-form :model="form" label-width="170px">
      <div>
        <div class="accident_box_title" style="margin-top: -10px">
          {{ $t("GeneralInfo") }}
        </div>
        <div class="accident_box_form">
          <div class="box_form">
            <el-row :gutter="20">
              <el-col :span="6">
                <el-form-item :label="$t('NumberPlate') + ':'" prop="car_no">
                  {{ form.car_no }}
                </el-form-item></el-col
              >
              <el-col :span="6">
                <el-form-item
                  :label="$t('driverName') + ':'"
                  prop="driver_fullname"
                >
                  {{ form.driver_fullname }}
                </el-form-item></el-col
              >
              <el-col :span="6">
                <el-form-item
                  :label="$t('DriverPhone') + ':'"
                  prop="driver_phone"
                  >{{ form.driver_phone }}
                </el-form-item></el-col
              >
              <el-col :span="6">
                <el-form-item
                  :label="this.$t('processingMode') + ':'"
                  prop="deal_type"
                >
                  {{ form.deal_type_name }}
                </el-form-item>
              </el-col>
              <el-col :span="6">
                <el-form-item
                  :label="$t('accidentLocation') + ':'"
                  prop="accident_location"
                >
                  {{ form.accident_location }}
                </el-form-item></el-col
              >
              <el-col :span="6">
                <el-form-item
                  :label="$t('accidentTime') + ':'"
                  class="input-style"
                  prop="accident_date"
                >
                  {{ form.accident_date }}
                </el-form-item></el-col
              >
              <el-col :span="6">
                <el-form-item
                  :label="$t('activePassive') + ':'"
                  prop="accident_duty"
                >
                  {{ form.accident_duty_name }}
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item
                  :label="this.$t('storyDescription') + ':'"
                  prop="accident_description"
                >
                  {{ form.accident_description }}
                </el-form-item>
              </el-col>
            </el-row>
          </div>
        </div>
      </div>
      <div>
        <div class="accident_box_title">
          {{
            this.form.deal_type == 1
              ? $t("accidentHandling")
              : $t("accidentCompany")
          }}
        </div>
        <div class="accident_box_form">
          <div class="box_form">
            <el-form-item
              :label="
                $t('claimAmount') + '(' + currency_unit_symbol + ')' + ':'
              "
              prop="insurance_price"
              v-if="form.deal_type == 2"
            >
              {{ form.insurance_price }}
            </el-form-item>
            <el-form-item
              :label="
                $t('advancePayment') + '(' + currency_unit_symbol + ')' + ':'
              "
              prop="insurance_payment"
              v-if="form.deal_type == 1"
            >
              {{ form.insurance_payment }}
            </el-form-item>
            <el-form-item
              :label="this.$t('whetherCompensated') + ':'"
              prop="insurance_indemnity"
              v-if="form.deal_type == 1"
            >
              {{ form.insurance_indemnity_name }}
            </el-form-item>
            <div
              style="display: flex"
              v-if="this.form.deal_type == 1 && form.insurance_indemnity == 1"
            >
              <el-form-item
                :label="
                  $t('compensation') + '(' + currency_unit_symbol + ')' + ':'
                "
                prop="insurance_price"
              >
                {{ form.insurance_price }}
              </el-form-item>
              <el-form-item
                :label="$t('claimTime') + ':'"
                class="input-style"
                prop="insurance_date"
              >
                {{ form.insurance_date }}
              </el-form-item>
            </div>

            <el-form-item
              :label="this.$t('claimDescription') + ':'"
              prop="insurance_description"
            >
              {{ form.insurance_description }}
            </el-form-item>
          </div>
        </div>
      </div>

      <div style="height: 300px">
        <div class="accident_box_title">{{ $t("uploadAttachment") }}</div>
        <div class="accident_box_form">
          <el-form-item
            :label="this.$t('uploadPicture') + ':'"
            prop="accident_picture"
            style="margin-top: 20px; height: 160px"
          >
            <img
              v-if="form.accident_picture"
              :src="form.accident_picture"
              class="avatar"
            />
          </el-form-item>
        </div>
      </div>
    </el-form>
  </div>
</template>

<script>
export default {
  // name: "CheckAccident",
  data() {
    return {
      form: {
        car_id: "", //车辆id
        car_no: "", //车牌号
        driver_id: "", //司机id
        driver_fullname: "", //司机名称
        driver_phone: "", //司机电话
        deal_type: 1, //处理方式
        deal_type_name: "",
        accident_location: "", //事故地点
        accident_date: "", //事故时间
        accident_duty: "", //主被动
        accident_description: "", //故事描述
        insurance_payment: "", //保险垫付款
        insurance_indemnity: "", //保险是否赔付
        insurance_price: "", //赔付金额
        insurance_date: "", //赔付时间
        insurance_description: "", //赔付描述
        insurance_payment: "", //故事照片
      },
      currency_unit_symbol: "", //单价单位
    };
  },
  created() {
    this.getDetail(this.$route.query.id);
  },
  mounted() {
    this.getAdvanceSetting(); //单位
  },
  methods: {
    // 查看
    getDetail(id) {
      this.$api.getAccidentDetail(id).then((res) => {
        if (!res) return;
        this.form = res.data;
      });
    },
    // 获取单位值
    getAdvanceSetting() {
      this.$api.getAdvanceSetting().then((res) => {
        this.currency_unit_symbol = res.data.currency_unit_symbol; //单价单位
      });
    },
  },
};
</script>

<style lang="less" scoped>
.accident_box {
  // width: 100%;
  // height: 100vh;
  background-color: #fff;
  padding: 20px;

  // margin-top: -20px;
  .accident_box_title {
    font-size: 15px;
    font-weight: 600;
    margin: 20px;
  }
  .accident_box_form {
    width: 95%;
    margin: 0 auto;
    // height: 300px;
    border: 1px solid rgb(220, 223, 230);
    .box_form {
      width: 95%;
      margin-top: 30px;
    }

    .avatar {
      width: 100px;
      height: 100px;
      display: block;
    }
    .avatar-uploader .el-upload:hover {
      border-color: #409eff;
    }
  }
  .dialog_footer {
    float: right;
    margin-top: 20px;
    background-color: #fff;
  }
}
</style>