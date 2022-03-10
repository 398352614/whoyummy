<template>
  <!-- 添加车辆 -->
  <div class="content_box">
    <el-form
      :model="form"
      :rules="formRules"
      ref="form"
      label-width="160px"
      label-position="right"
    >
      <div class="base_box">
        <el-card
          class="base_box_left"
          :body-style="{ padding: '0px' }"
          shadow="nerver"
        >
          <!-- 基本信息 -->
          <div slot="header" class="clearfix">
            <span>{{ $t("GeneralInfo") }}</span>
          </div>
          <div class="basic_box">
            <div class="base_left">
              <!-- 车牌号 -->
              <el-form-item :label="$t('NumberPlate')" prop="car_no">
                <el-input
                  class="NumberPlate"
                  size="small"
                  v-model="form.car_no"
                ></el-input>
              </el-form-item>
              <!-- 租赁类型 -->
              <el-form-item :label="$t('LeaseType')">
                <el-select
                  size="small"
                  :placeholder="$t('PleaseSelect')"
                  v-model="form.ownership_type"
                  clearable
                >
                  <el-option
                    :label="item.name"
                    :value="item.id"
                    v-for="(item, index) in rent_types"
                    :key="index"
                  >
                  </el-option>
                </el-select>
              </el-form-item>
              <!-- 车长 -->
              <el-form-item :label="$t('vehicleLength')" prop="car_length">
                <el-select
                  size="small"
                  v-model="form.car_length"
                  :placeholder="$t('PleaseSelect')"
                  clearable
                >
                  <el-option
                    v-for="(item, index) in car_length_list"
                    :label="item.name"
                    :value="item.id"
                    :key="index"
                  >
                  </el-option>
                </el-select>
              </el-form-item>
            </div>
            <div class="base_right">
              <!-- 汽车品牌 -->
              <el-form-item :label="$t('Brand')" prop="car_brand_id">
                <el-select
                  size="small"
                  :placeholder="$t('PleaseSelect')"
                  v-model="form.car_brand_id"
                >
                  <el-option
                    @click.native="handlerAddCarBands(item.id)"
                    :label="item.name"
                    :value="item.id"
                    v-for="(item, index) in car_bands"
                    :key="index"
                  >
                  </el-option>
                </el-select>
              </el-form-item>
              <!-- 汽车型号 -->
              <el-form-item :label="$t('Model')" prop="car_model_type">
                <el-select
                  size="small"
                  :placeholder="$t('PleaseSelect')"
                  v-model="form.car_model_type"
                >
                  <el-option
                    v-for="(item, index) in carModelTypeList"
                    :label="item.name"
                    :value="item.id"
                    :key="index"
                    @click.native="triggerCarModel(item.id)"
                  >
                  </el-option>
                </el-select>
              </el-form-item>
              <!-- GPS设备 -->
              <el-form-item :label="$t('GPS')" prop="gps_device_number">
                <el-input
                  class="NumberPlate"
                  size="small"
                  v-model="form.gps_device_number"
                ></el-input>
              </el-form-item>
            </div>
          </div>
        </el-card>
        <el-card
          class="base_box_right"
          :body-style="{ padding: '0px' }"
          shadow="nerver"
        >
          <!-- 保险信息 -->
          <div slot="header" class="clearfix">
            <span>{{ $t("InsuranceInfo") }}</span>
          </div>
          <div class="insurance_box">
            <div class="insurance_left">
              <!-- 保险公司 -->
              <el-form-item :label="$t('InsuranceCompany')">
                <el-input
                  size="small"
                  v-model="form.insurance_company"
                ></el-input>
              </el-form-item>
              <!-- 每月保险 -->
              <el-form-item :label="$t('InsurancePerMonth')">
                <el-input
                  size="small"
                  v-model="form.month_insurance"
                ></el-input>
              </el-form-item>
              <!-- 保险类型 -->
              <el-form-item :label="$t('InsuranceType')">
                <el-select
                  size="small"
                  :placeholder="$t('PleaseSelect')"
                  v-model="form.insurance_type"
                  clearable
                >
                  <el-option
                    :label="item.label"
                    :value="item.label"
                    v-for="(item, index) in month_tax_type"
                    :key="index"
                  >
                  </el-option>
                </el-select>
              </el-form-item>
            </div>
          </div>
        </el-card>
      </div>
      <div class="footer_box">
        <el-card
          class="box-card"
          :body-style="{ padding: '0px' }"
          shadow="nerver"
        >
          <!-- 附加信息 -->
          <div slot="header" class="clearfix">
            <span>{{ $t("OtherRemark") }}</span>
          </div>
          <div class="footer_inner">
            <div class="info_type_one" v-if="showExtraBox">
              <div class="info_top">
                <div class="inner_left">
                  <!-- 起租日期 -->
                  <el-form-item :label="$t('StartRent')">
                    <el-date-picker
                      v-model="form.rent_start_date"
                      type="date"
                      size="small"
                      value-format="yyyy-MM-dd"
                    >
                    </el-date-picker>
                  </el-form-item>
                  <!-- 月租金 -->
                  <el-form-item :label="$t('MonthlyRent')">
                    <el-input
                      class="MonthlyRent"
                      size="small"
                      v-model="form.rent_month_fee"
                    ></el-input>
                  </el-form-item>
                </div>
                <div class="inner_right">
                  <!-- 到期日期 -->
                  <el-form-item :label="$t('EndRent')">
                    <el-date-picker
                      v-model="form.rent_end_date"
                      type="date"
                      size="small"
                      value-format="yyyy-MM-dd"
                    >
                    </el-date-picker>
                  </el-form-item>
                  <!-- 维修自理 -->
                  <el-form-item :label="$t('SelfCare')">
                    <el-radio-group v-model="form.repair">
                      <el-radio
                        v-for="item in repair"
                        :key="item.label"
                        :value="item.value"
                        :label="item.value"
                      >
                        {{ item.label }}
                      </el-radio>
                    </el-radio-group>
                  </el-form-item>
                </div>
              </div>
              <div class="info_bottom">
                <!-- 车辆备注 -->
                <el-form-item :label="$t('VehicleRemark')" prop="remark">
                  <el-input
                    size="small"
                    type="textarea"
                    v-model="form.remark"
                  ></el-input>
                </el-form-item>
                <!-- 文件上传 -->
                <el-form-item :label="$t('Upload')">
                  <el-upload
                    :headers="Authorization"
                    :before-upload="beforeAvatarUpload"
                    :on-success="handleFileSuccess"
                    :on-remove="handleClearFile"
                    :action="BaseApiFile"
                    :file-list="fileList"
                    list-type="picture"
                    name="file"
                    :data="{
                      dir: 'driver',
                    }"
                  >
                    <el-button slot="trigger" size="small" type="primary">{{
                      $t("Select")
                    }}</el-button>
                    <div slot="tip" class="el-upload__tip">
                      {{ $t("OnlyJpgPng") }}
                    </div>
                  </el-upload>
                </el-form-item>
              </div>
            </div>
            <div v-else>
              <div class="info_type_two">
                <el-form-item :label="$t('VehicleRemark')" prop="remark">
                  <el-input
                    size="small"
                    type="textarea"
                    v-model="form.remark"
                  ></el-input>
                </el-form-item>
                <el-form-item :label="$t('Upload')">
                  <el-upload
                    :headers="Authorization"
                    :before-upload="beforeAvatarUpload"
                    :on-success="handleFileSuccess"
                    :on-remove="handleClearFile"
                    :action="BaseApiFile"
                    :file-list="fileList"
                    list-type="picture"
                    name="file"
                    :data="{
                      dir: 'driver',
                    }"
                  >
                    <el-button slot="trigger" size="small" type="primary">{{
                      $t("Select")
                    }}</el-button>
                    <div slot="tip" class="el-upload__tip"></div>
                  </el-upload>
                </el-form-item>
              </div>
            </div>
          </div>
        </el-card>
      </div>
    </el-form>
    <div class="submit_button_box">
      <el-row>
        <el-col :span="2" :offset="22">
          <el-button type="primary" @click="onSubmit" class="submit">{{
            $t("Submit")
          }}</el-button>
        </el-col>
      </el-row>
    </div>
    <el-dialog :title="$t('AddCarBrand')" :visible.sync="dialogVisible">
      <el-form :model="car_bands_form" label-width="160px">
        <el-form-item :label="$t('CarBrandZhName')">
          <el-input v-model="car_bands_form.cn_name"></el-input>
        </el-form-item>
        <el-form-item :label="$t('CarBrandEnName')">
          <el-input v-model="car_bands_form.en_name"></el-input>
        </el-form-item>
        <el-form-item style="text-align: right">
          <el-button @click="dialogVisible = false" class="cancel">{{
            $t("Cancel")
          }}</el-button>
          <el-button type="primary" @click="addCarBands" class="submit">{{
            $t("Submit")
          }}</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>

    <el-dialog :title="$t('AddCarType')" :visible.sync="visible">
      <el-form :model="car_type_form" label-width="160px">
        <el-form-item :label="$t('CarTypeZhName')">
          <el-input v-model="car_type_form.cn_name"></el-input>
        </el-form-item>
        <el-form-item :label="$t('CarTypeEnName')">
          <el-input v-model="car_type_form.en_name"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button @click="visible = false" class="cancel">{{
            $t("Cancel")
          }}</el-button>
          <el-button
            type="primary"
            @click="handerAddCarModel"
            :loading="this.$store.state.config.button_loading"
            class="submit"
            >{{ $t("Submit") }}</el-button
          >
        </el-form-item>
      </el-form>
    </el-dialog>
  </div>
</template>

<script>
import baseApi from "@/api/axios/base_api";

export default {
  name: "AddCar",
  data() {
    return {
      fileList: [],
      imageUrl: "",
      form: {
        car_no: "", // 车牌号
        car_brand_id: "", // 汽车品牌
        frame_number: "", // 车架号
        transmission: "", // 车型(手动 自动)
        current_miles: "", // 当前里程数
        ownership_type: 1, // 租赁类型
        month_road_tax: "", // 每月路税(€)

        outgoing_time: "", // 出厂日期
        car_model_type: "", // 车辆型号
        gps_device_number: "", //GPS设备
        car_length: "", //车长
        engine_number: "", // 发动机编号
        fuel_type: "", // 燃料类型
        annual_inspection_date: "", // 下次年检日期
        received_date: "", // 接收车辆日期

        insurance_company: "", // 保险公司
        insurance_type: "", // 保险类型
        month_insurance: "", // 每月保险

        rent_start_date: this.getToday(), // 起租日期
        rent_end_date: this.getToday(), // 到期日
        rent_month_fee: "", // 月租金

        repair: 1, // 维修自理
        remark: "", // 车辆备注

        relate_material_list: [],
      },
      car_length_list: [], //车长下拉框
      addedCountryList: [],
      car_bands: [], // 汽车品牌列表
      dialogVisible: false,

      car_bands_form: {
        cn_name: "",
        en_name: "",
      },

      car_operation_type: [],
      carModelTypeList: [], //汽车型号
      rent_types: [],

      car_type: [],
      car_type_form: {
        cn_name: "",
        en_name: "",
      },
      visible: false,

      fuel_type: [],

      month_tax_type: [
        {
          label: "WA",
        },
        {
          label: "WA+",
        },
        {
          label: "All risk",
        },
      ],
    };
  },
  created() {
    if (this.$route.query.id) {
      this.getCarDetail(this.$route.query.id);
    } // 编辑

    this.getRentTypes();
    this.getCarBands();
    this.getlength();
    this.getDictionary();
  },
  watch: {
    visible(value) {
      if (!value) {
        this.car_type_form.cn_name = "";
        this.car_type_form.en_name = "";
      }
    },
    dialogVisible(value) {
      if (!value) {
        this.car_bands_form.cn_name = "";
        this.car_bands_form.en_name = "";
      }
    },
  },
  computed: {
    Authorization() {
      return { Authorization: this.$store.state.token.token };
    },
    BaseApiFile() {
      return `${baseApi}admin/upload/file`;
    },
    showExtraBox() {
      return (
        this.form.ownership_type === 1 ||
        this.form.ownership_type === 3 ||
        this.form.ownership_type === 4 ||
        this.form.ownership_type === ""
      );
    }, // 联动切换表单
    formRules() {
      return {
        car_no: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        car_length: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        car_brand_id: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "change",
          },
        ],
        transmission: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "change",
          },
        ],
        month_road_tax: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ], // 税号

        outgoing_time: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "change",
          },
        ],
        car_model_type: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "change",
          },
        ],
        annual_inspection_date: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        received_date: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],

        insurance_company: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        insurance_type: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "change",
          },
        ],
        month_insurance: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],

        rent_start_date: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        rent_end_date: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        repair: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "change",
          },
        ],
      };
    },
    repair() {
      return [
        {
          value: 1,
          label: this.$t("Yes"),
        },
        {
          value: 2,
          label: this.$t("No"),
        },
      ];
    },
  },
  methods: {
    getToday() {
      // 获取当前日期
      var date = new Date();

      // 获取当前月份
      var nowMonth = date.getMonth() + 1;

      // 获取当前是几号
      var strDate = date.getDate();

      // 添加分隔符“-”
      var seperator = "-";

      // 对月份进行处理，1-9月在前面添加一个“0”
      if (nowMonth >= 1 && nowMonth <= 9) {
        nowMonth = "0" + nowMonth;
      }

      // 对月份进行处理，1-9号在前面添加一个“0”
      if (strDate >= 0 && strDate <= 9) {
        strDate = "0" + strDate;
      }

      // 最后拼接字符串，得到一个格式为(yyyy-MM-dd)的日期
      return date.getFullYear() + seperator + nowMonth + seperator + strDate;
    },
    // 车长
    getlength() {
      this.$api.getRentTypes().then((res) => {
        this.car_length_list = res.data.car_length_type_list;
        // res.data.data.forEach((item) => {
        //   this.form.car_id = item.id;
        // });
      });
    },
    // 车辆类型
    getDictionary() {
      this.$api.getDictionary().then((res) => {
        this.carModelTypeList = res.data.carModelTypeList;
      });
    },

    getRentTypes() {
      this.$api.getRentTypes().then((res) => {
        this.rent_types = res.data.car_owner_ship_type_list;
        this.car_operation_type = res.data.car_transmission_list;
        this.fuel_type = res.data.car_fuel_type_list;
      });
    },
    handleClearFile(file, fileList) {
      this.form.relate_material_list = JSON.stringify(
        fileList.map((el) => {
          if (el.response) {
            return {
              material_name: el.response.data.name,
              material_url: el.response.data.path,
            };
          } else {
            return {
              material_name: el.name,
              material_url: el.url,
            };
          }
        })
      );
    },
    async getCarDetail(id) {
      const CarDetailRes = await this.$api.getCarDetail(id);

      const CarModelRes = await this.$api.getCarModels(
        CarDetailRes.data.car_brand_id
      );

      Promise.all([CarDetailRes, CarModelRes]).then((res) => {
        if (!res[0] || !res[1]) return false;
        const DetailData = res[0].data;
        // 处理汽车型号
        // const ModelData = res[1].data;
        // this.car_type = ModelData || [];
        // this.form.car_model_type = ""; // 清除原有数据
        // this.car_type.push({
        //   id: 0,
        //   name: "+",
        // });
        // 处理表单数据
        // eslint-disable-next-line guard-for-in
        for (const i in DetailData) {
          this.form[i] = DetailData[i];
        }
        if (DetailData.relate_material_list) {
          // let relate_material_list = JSON.parse(DetailData.relate_material_list) || [];
          this.fileList = DetailData.relate_material_list.map((el) => {
            return {
              name: el.material_name,
              url: el.material_url,
              id: DetailData.id,
            };
          });
        }
      });
    },
    handerAddCarModel() {
      this.car_type_form.brand_id = this.form.car_brand_id;
      this.$api.addCarModel(this.car_type_form).then((res) => {
        if (!res) return false;

        this.getCarModels(this.form.car_brand_id);

        this.visible = false;
      });
    },
    triggerCarModel(id) {
      if (id === 0) {
        this.visible = true;
        this.form.car_model_type = "";
        return false;
      }
    },
    getCarBands() {
      this.$api.getCarBands().then((res) => {
        if (!res) return false;
        this.car_bands = res.data;
        this.car_bands.push({
          id: 0,
          name: "+",
        });
      });
    },

    addCarBands() {
      this.$api.addCarBand(this.car_bands_form).then((res) => {
        if (!res) return false;
        this.dialogVisible = false;
        this.getCarBands();
      });
    },
    handlerAddCarBands(id) {
      if (id === 0) {
        this.form.car_brand_id = "";
        this.dialogVisible = true;
        return false;
      }
      this.getCarModels(this.form.car_brand_id);
    },
    // eslint-disable-next-line camelcase
    getCarModels(car_brand_id) {
      this.$api.getCarModels(car_brand_id).then((res) => {
        if (!res) return false;
        this.car_type = res.data;
        this.form.car_model_type = ""; // 清除原有数据
        this.car_type.push({
          id: 0,
          name: "+",
        });
      });
    },

    handleFileSuccess(res, file, fileList) {
      // 上传成功
      console.log(res, file, fileList, "up");
      if (res.code === 200) {
        this.form.relate_material_list = JSON.stringify(
          fileList.map((el) => {
            if (el.response) {
              return {
                material_name: el.response.data.name,
                material_url: el.response.data.path,
              };
            } else {
              return {
                material_name: el.name,
                material_url: el.url,
              };
            }
          })
        );
      }
    },
    beforeAvatarUpload(file) {
      if (file.type === "image/jpeg" || file.type === "image/png") {
        return true;
      } else {
        this.$message({
          type: "error",
          message: "只能上传jpg/png格式文件!",
        });
        return false;
      }
      console.log("beforeAvatarUpload", file);
    },
    onSubmit() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          if (this.$route.query.id) {
            this.$api
              .editCar(this.$route.query.id, {
                ...this.form,
              })
              .then((res) => {
                if (!res) return;
                this.$router.push({
                  name: "carManagement",
                });
              });
            return;
          } else {
            this.$api
              .addCar({
                ...this.form,
              })
              .then((res) => {
                if (!res) return;
                this.$router.push({
                  name: "carManagement",
                });
              });
          }
        }
      });
    },
  },
};
</script>

<style lang="less" scoped>
@maxWidth: 220px;

.content_box {
  background-color: #fff;
  .MonthlyRent {
    max-width: @maxWidth;
  }
  .NumberPlate {
    max-width: 193px;
  }
  .base_box {
    display: flex;
    .base_box_left {
      flex: 1.8;
    }
    .base_box_right {
      flex: 1;
    }
  }
  .basic_box {
    flex: 1;
    display: flex;
    padding: 20px;
    .base_left {
      flex: 1;
    }
    .base_right {
      flex: 1;
    }
  }
  .insurance_box {
    flex: 1;
    display: flex;
    padding: 20px;
  }
  .center_box {
    margin-top: 2px;
    display: flex;
    justify-content: space-between;
    .box-card {
      flex: 1;
    }
  }
  .footer_box {
    margin-top: 20px;
  }

  .info_type_one {
    padding: 20px;
    .info_top {
      display: flex;
      justify-content: space-between;
      .inner_left {
        flex: 1;
        display: flex;
        flex-direction: column;
      }
      .inner_right {
        flex: 1;
        display: flex;
        flex-direction: column;
      }
    }
  }
  .info_type_two {
    padding: 20px;
  }
  .submit_button_box {
    padding: 20px 0;
    text-align: center;
  }
  .lisence_type {
    width: 400px;
    display: flex;
    flex-flow: row wrap;
    .lisence_type_item {
      width: 80px;
      margin: 4px 0;
    }
  }
}
</style>
