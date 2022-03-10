<template>
  <!-- 事故新增 -->
  <div class="accident_box">
    <el-form :model="form" :rules="formRules" ref="form" label-width="180px">
      <div>
        <div class="accident_box_title" style="margin-top: -10px">
          {{ $t("GeneralInfo") }}
        </div>
        <div class="accident_box_form">
          <div class="box_form">
            <el-row :gutter="5">
              <el-col :span="6">
                <el-form-item :label="$t('NumberPlate')" prop="car_no">
                  <!-- <el-input size="small" v-model="form.car_no"></el-input> -->
                  <el-select
                    v-model="form.car_no"
                    :placeholder="this.$t('pleaseNumber')"
                    style="width: 100%"
                    size="small"
                  >
                    <el-option
                      v-for="(item, index) in car_List"
                      :key="index"
                      :label="item.car_no"
                      :value="item.car_no"
                    ></el-option> </el-select></el-form-item
              ></el-col>
              <el-col :span="6">
                <el-form-item :label="$t('driverName')" prop="driver_fullname">
                  <el-select
                    v-model="form.driver_fullname"
                    :placeholder="this.$t('PleaseSelectDriver')"
                    style="width: 100%"
                    size="small"
                  >
                    <el-option
                      v-for="(item, index) in allDriverList"
                      :key="index"
                      :label="item.fullname"
                      :value="item.fullname"
                    ></el-option>
                  </el-select>
                  <!-- <el-input
                    size="small"
                    v-model="form.driver_fullname"
                  ></el-input>  -->
                </el-form-item></el-col
              >
              <el-col :span="6">
                <el-form-item :label="$t('DriverPhone')" prop="driver_phone">
                  <el-input
                    size="small"
                    v-model="form.driver_phone"
                  ></el-input> </el-form-item
              ></el-col>
              <el-col :span="6">
                <el-form-item
                  :label="this.$t('processingMode')"
                  prop="deal_type"
                >
                  <el-select
                    v-model="form.deal_type"
                    :placeholder="this.$t('pleaseProcessingMethod')"
                    style="width: 100%"
                    size="small"
                  >
                    <el-option
                      v-for="(item, index) in carAccidentDealType"
                      :key="index"
                      :label="item.name"
                      :value="item.id"
                    ></el-option>
                  </el-select>
                </el-form-item>
              </el-col>
              <el-col :span="6">
                <el-form-item
                  :label="$t('accidentLocation')"
                  prop="accident_location"
                >
                  <el-input
                    size="small"
                    v-model="form.accident_location"
                  ></el-input> </el-form-item
              ></el-col>
              <el-col :span="6">
                <el-form-item
                  :label="$t('accidentTime')"
                  class="input-style"
                  prop="accident_date"
                >
                  <el-date-picker
                    value-format="yyyy-MM-dd"
                    v-model="form.accident_date"
                    type="date"
                    :picker-options="pickerOptions"
                    :placeholder="$t('accidentTime')"
                    style="width: 100%"
                    size="small"
                  >
                  </el-date-picker> </el-form-item
              ></el-col>
              <el-col :span="6">
                <el-form-item :label="$t('activePassive')" prop="accident_duty">
                  <el-select
                    v-model="form.accident_duty"
                    :placeholder="this.$t('PleaseSelect')"
                    style="width: 100%"
                    size="small"
                  >
                    <el-option
                      v-for="(item, index) in carAccidentDuty"
                      :key="index"
                      :label="item.name"
                      :value="item.id"
                    ></el-option>
                  </el-select>
                </el-form-item>
              </el-col>
              <el-col :span="24">
                <el-form-item
                  :label="this.$t('storyDescription')"
                  prop="accident_description"
                >
                  <el-input
                    v-model="form.accident_description"
                    type="textarea"
                    :rows="3"
                  ></el-input>
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
              <el-input
                v-model="form.insurance_price"
                style="width: 200px"
              ></el-input>
            </el-form-item>
            <el-form-item
              :label="
                $t('advancePayment') + '(' + currency_unit_symbol + ')' + ':'
              "
              prop="insurance_payment"
              v-if="form.deal_type == 1"
            >
              <el-input
                v-model="form.insurance_payment"
                style="width: 200px"
              ></el-input>
            </el-form-item>
            <el-form-item
              :label="this.$t('whetherCompensated')"
              prop="insurance_indemnity"
              v-if="form.deal_type == 1"
            >
              <el-radio-group v-model="form.insurance_indemnity">
                <el-radio :label="1">{{ $t("Yes") }} </el-radio>
                <el-radio :label="2">{{ $t("No") }}</el-radio>
              </el-radio-group>
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
                <el-input v-model="form.insurance_price"></el-input>
              </el-form-item>
              <el-form-item
                :label="$t('claimTime')"
                class="input-style"
                prop="insurance_date"
              >
                <el-date-picker
                  value-format="yyyy-MM-dd"
                  v-model="form.insurance_date"
                  type="date"
                  :picker-options="pickerOptions"
                  :placeholder="$t('PleaseSelect')"
                >
                </el-date-picker>
              </el-form-item>
            </div>

            <el-form-item
              :label="this.$t('claimDescription')"
              prop="insurance_description"
            >
              <el-input
                v-model="form.insurance_description"
                type="textarea"
                :rows="3"
              ></el-input>
            </el-form-item>
          </div>
        </div>
      </div>

      <div style="height: 300px">
        <div class="accident_box_title">{{ $t("uploadAttachment") }}</div>
        <div class="accident_box_form">
          <el-form-item
            :label="this.$t('uploadPicture')"
            prop="accident_picture"
            style="margin-top: 20px; height: 160px"
          >
            <el-upload
              class="avatar-uploader"
              :headers="Authorization"
              :before-upload="beforeAvatarUpload"
              :on-success="handleAvatarSuccess"
              :action="BaseApiImg"
              :on-remove="handlerClearDeiver"
              :limit="3"
              name="image"
              :data="{
                dir: 'driver',
              }"
              style="height: 100px"
            >
              <img
                v-if="form.accident_picture"
                :src="form.accident_picture"
                class="avatar"
              />
              <i
                v-else
                class="el-icon-plus avatar-uploader-icon"
                style="width: 100px; height: 40px"
              ></i>
              <div class="el-upload_tip" slot="tip">
                {{ $t("pleaseUploadPiece") }}
              </div>
            </el-upload>
          </el-form-item>
          <div class="dialog_footer">
            <el-button type="primary" @click="onSubmit" class="submit">{{
              $t("Submit")
            }}</el-button>
          </div>
        </div>
      </div>
    </el-form>
  </div>
</template>

<script>
import baseApi from "@/api/axios/base_api";
export default {
  // name: "AddAccident",
  data() {
    return {
      form: {
        car_id: "", //车辆id
        car_no: "", //车牌号
        driver_id: "", //司机id
        driver_fullname: "", //司机名称
        driver_phone: "", //司机电话
        deal_type: 1, //处理方式
        accident_location: "", //事故地点
        accident_date: "", //事故时间
        accident_duty: 1, //主被动
        accident_description: "", //故事描述
        insurance_payment: "", //保险垫付款
        insurance_indemnity: 2, //保险是否赔付
        insurance_price: "", //赔付金额
        insurance_date: "", //赔付时间
        insurance_description: "", //赔付描述
        insurance_payment: "", //故事照片
      },
      allDriverList: [], //司机下拉框
      car_List: [], //车牌号下拉框
      currency_unit_symbol: "", //单价单位

      carAccidentDealType: [], //处理方式
      carAccidentDuty: [], //主被动
      pickerOptions: {
        disabledDate(time) {
          return time.getTime() > Date.now();
          // return time.getTime() > Date.now() - 8.64e7;
        },
      },
    };
  },

  computed: {
    // 货主信息
    Authorization() {
      return { Authorization: this.$store.state.token.token };
    },
    // 图片
    BaseApiImg() {
      return `${baseApi}admin/upload/image`;
    },
    // 表单验证
    formRules() {
      return {
        car_no: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ], // 车牌号
        driver_fullname: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ], // 司机姓名
        driver_phone: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ], // 司机手机
        deal_type: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ], // 处理方式
        accident_location: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ], // 事故地点
        accident_date: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ], // 事故时间
        accident_duty: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ], // 主被动
        insurance_payment: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ], // 垫付款
        insurance_indemnity: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ], // 保险是否已赔
        insurance_price: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ], // 赔付金额
        insurance_date: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ], // 赔付时间
        // accident_picture: [
        //   {
        //     required: true,
        //     message: this.$t("Required"),
        //     trigger: "blur",
        //   },
        // ], // 上传照片
      };
    },
    // 修改
    isEdit() {
      return !!this.$route.query.id;
    },
  },
  created() {
    this.getDictionary(); //数据字典
    this.getCarList(); // 车牌号
    this.getAdvanceSetting(); //单位
    this.getAllDrivers(); // 司机下拉框
    if (this.isEdit) {
      this.getDetail(this.$route.query.id);
    }
  },
  methods: {
    // 数据字典
    getDictionary() {
      this.$api.getDictionary().then((res) => {
        this.carAccidentDealType = res.data.carAccidentDealType; //处理方式
        this.carAccidentDuty = res.data.carAccidentDuty; //主被动
      });
    },
    // 修改 查看
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
    // 提交
    onSubmit() {
      this.$refs.form.validate((valid) => {
        if (this.isEdit) {
          this.$api
            .putAccident(this.$route.query.id, { ...this.form })
            .then((res) => {
              if (!res) return;
              this.$router.replace({
                name: "Accident",
                params: {
                  Accident: 1,
                },
              });
            });
          return;
        }
        if (valid) {
          this.$api.addAccident({ ...this.form }).then((res) => {
            if (res.code == 200) {
              this.$router.replace({
                name: "Accident",
                params: {
                  Accident: 1,
                },
              });
            }
          });
        }
      });
    },
    // 司机下拉框
    getAllDrivers() {
      this.$api
        .getDriversPublic({
          // page: 1,
          // per_page: 200,
        })
        .then((res) => {
          this.allDriverList = res.data.data;
          res.data.data.forEach((item) => {
            this.form.driver_id = item.id;
          });
        });
    },
    // 车牌号
    getCarList() {
      this.$api.getCarsPublic().then((res) => {
        this.car_List = res.data.data;
        res.data.data.forEach((item) => {
          this.form.car_id = item.id;
        });
      });
    },
    // 添加图片
    handleAvatarSuccess(response) {
      if (response.code === 200) {
        this.form.accident_picture = response.data.path;
      }
    },
    // 清除图片
    handlerClearDeiver() {
      // eslint-disable-next-line
      this.form.accident_picture = "";
    },
    beforeAvatarUpload(file) {
      console.log("beforeAvatarUpload", file);
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
    .avatar-uploader .el-upload {
      border: 1px dashed #d9d9d9;
      border-radius: 6px;
      cursor: pointer;
      position: relative;
      overflow: hidden;
      .avatar-uploader-icon {
        font-size: 15px;
        color: #8c939d;
        line-height: 30px !important;
        margin-top: -40px;
        text-align: center;
      }
      .avatar {
        width: 50px;
        height: 50px;
        display: block;
      }
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