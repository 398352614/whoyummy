<template>
  <!-- 维修 保养 新增 -->
  <div class="accident_box">
    <el-form :model="form" :rules="formRules" ref="form" label-width="180px">
      <div>
        <div class="accident_box_title" style="margin-top: -10px">
          {{ $t("VehicleInfo") }}
        </div>
        <div class="accident_box_form">
          <div class="box_form">
            <el-row :gutter="20">
              <el-col :span="6">
                <el-form-item :label="$t('NumberPlate') + ':'" prop="car_no">
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
                <el-form-item
                  :label="$t('vehicleMileage') + ':'"
                  prop="distance"
                >
                  <el-input
                    size="small"
                    v-model="form.distance"
                  ></el-input> </el-form-item
              ></el-col>
              <el-col :span="6">
                <el-form-item
                  :label="this.$t('maintenanceType') + ':'"
                  prop="maintain_type"
                >
                  <el-select
                    v-model="form.maintain_type"
                    :placeholder="this.$t('pleaseSelect')"
                    style="width: 100%"
                    size="small"
                  >
                    <el-option
                      v-for="(item, index) in carMaintainType"
                      :key="index"
                      :label="item.name"
                      :value="item.id"
                    ></el-option>
                  </el-select>
                </el-form-item>
              </el-col>

              <el-col :span="6">
                <el-form-item
                  :label="$t('maintenanceTime') + ':'"
                  class="input-style"
                  prop="maintain_date"
                >
                  <el-date-picker
                    value-format="yyyy-MM-dd"
                    v-model="form.maintain_date"
                    type="date"
                    :picker-options="pickerOptions"
                    :placeholder="$t('maintenanceTime')"
                    style="width: 100%"
                    size="small"
                  >
                  </el-date-picker> </el-form-item
              ></el-col>
              <el-col :span="6">
                <el-form-item
                  :label="$t('maintenanceShopName') + ':'"
                  prop="maintain_factory"
                >
                  <el-input
                    size="small"
                    v-model="form.maintain_factory"
                  ></el-input> </el-form-item
              ></el-col>
            </el-row>
          </div>
        </div>
      </div>
      <div>
        <div class="accident_box_title">{{ $t("chargeDetails") }}</div>
        <div class="accident_box_form">
          <div class="box_form">
            <el-form-item style="margin-left: -130px">
              <el-table
                max-height="600px"
                :data="form.maintain_detail"
                style="width: 100%"
                border
              >
                <!-- <el-table-column type="index"></el-table-column> -->
                <!-- 维修项目 -->
                <el-table-column
                  :label="'*' + $t('repairItems')"
                  align="center"
                >
                  <template slot-scope="scope">
                    <el-input
                      v-model="scope.row.maintain_name"
                      size="mini"
                    ></el-input>
                  </template>
                </el-table-column>
                <!-- 配件名称 -->
                <el-table-column :label="$t('partsName')" align="center">
                  <template slot-scope="scope">
                    <el-input
                      v-model="scope.row.fitting_name"
                      size="mini"
                    ></el-input>
                  </template>
                </el-table-column>
                <el-table-column align="center" :label="$t('partsBrand')">
                  <!-- <template slot="header">
                    {{ $t("配件品牌") }}
                    <el-tooltip
                      class="item"
                      effect="dark"
                      :content="$t('Externallabelingforpushtothirdparties')"
                      placement="top"
                    >
                      <i class="el-icon-info"></i>
                    </el-tooltip>
                  </template> -->
                  <template slot-scope="scope">
                    <el-input
                      v-model="scope.row.fitting_brand"
                      size="mini"
                    ></el-input>
                  </template>
                </el-table-column>
                <!-- 配件型号 -->
                <el-table-column :label="$t('partsModel')" align="center">
                  <template slot-scope="scope">
                    <el-input
                      v-model="scope.row.fitting_model"
                      size="mini"
                    ></el-input>
                  </template>
                </el-table-column>
                <!-- 数量 -->
                <el-table-column :label="'*' + $t('Quantity')" align="center">
                  <template slot-scope="scope">
                    <el-input
                      v-model="scope.row.fitting_quantity"
                      size="mini"
                      @input="wacth"
                    ></el-input>
                  </template>
                </el-table-column>
                <!-- 单位 -->
                <el-table-column :label="'*' + $t('unit')" align="center">
                  <template slot-scope="scope">
                    <el-input
                      v-model="scope.row.fitting_unit"
                      size="mini"
                    ></el-input>
                  </template>
                </el-table-column>
                <!-- 单价 -->
                <el-table-column
                  :label="
                    '*' + $t('unitPrice') + '(' + currency_unit_symbol + ')'
                  "
                  align="center"
                  width="140"
                >
                  <template slot-scope="scope">
                    <el-input
                      v-model="scope.row.fitting_price"
                      size="mini"
                      @input="wacth"
                    ></el-input>
                  </template>
                </el-table-column>
                <!-- 材料费 -->
                <el-table-column
                  :label="$t('materialFee') + '(' + currency_unit_symbol + ')'"
                  align="center"
                  width="160"
                >
                  <template slot-scope="scope">
                    <el-input
                      v-model="scope.row.material_price"
                      size="mini"
                      @input="wacth"
                      disabled
                    ></el-input>
                  </template>
                </el-table-column>
                <!-- 工时费 -->
                <el-table-column
                  :label="
                    '*' + $t('manHourFee') + '(' + currency_unit_symbol + ')'
                  "
                  align="center"
                  width="160"
                >
                  <template slot-scope="scope">
                    <el-input
                      v-model="scope.row.hour_price"
                      size="mini"
                      @input="wacth"
                    ></el-input>
                  </template>
                </el-table-column>
                <el-table-column :label="$t('Operation')" align="center">
                  <template slot-scope="scope">
                    <!-- 移除 -->
                    <el-button
                      @click.native.prevent="
                        deleteRow(scope.$index, form.maintain_detail)
                      "
                      type="danger"
                      class="remove-btn"
                      >{{ $t("Remove") }}</el-button
                    >
                  </template>
                </el-table-column>
              </el-table>
              <div class="add-row">
                <el-button
                  @click="addRow"
                  style="
                    width: 100%;
                    border-style: dashed;
                    margin-top: 10px;
                    color: #97097f8;
                  "
                  >{{ $t("Add") }}</el-button
                >
              </div>
            </el-form-item>
            <div style="display: flex">
              <el-form-item
                :label="
                  $t('totalMaterialFee') +
                  '(' +
                  currency_unit_symbol +
                  ')' +
                  ':'
                "
                prop="material_price"
              >
                {{ form.material_price_total }}
              </el-form-item>
              <el-form-item
                :label="
                  $t('totalManpowerFee') +
                  '(' +
                  currency_unit_symbol +
                  ')' +
                  ':'
                "
                prop="hour_price"
              >
                <!-- <el-input v-model="form.hour_price"></el-input> -->
                {{ form.hour_price_total }}
              </el-form-item>
              <el-form-item
                :label="$t('totalFee') + '(' + currency_unit_symbol + ')' + ':'"
                prop="maintain_price"
              >
                {{ form.maintain_price }}
              </el-form-item>
            </div>
            <el-form-item
              :label="this.$t('invoiceRequired') + ':'"
              prop="is_ticket"
            >
              <el-radio-group v-model="form.is_ticket">
                <el-radio :label="1">{{ $t("Yes") }}</el-radio>
                <el-radio :label="2">{{ $t("No") }}</el-radio>
              </el-radio-group>
            </el-form-item>
            <el-form-item
              :label="this.$t('problemDescription') + ':'"
              prop="maintain_description"
            >
              <el-input
                v-model="form.maintain_description"
                type="textarea"
                :rows="5"
              ></el-input>
            </el-form-item>
          </div>
        </div>
      </div>

      <div style="height: 300px">
        <div class="accident_box_title">{{ $t("uploadAttachment") }}</div>
        <div class="accident_box_form">
          <el-form-item
            :label="this.$t('uploadPicture') + ':'"
            prop="maintain_picture"
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
                v-if="form.maintain_picture"
                :src="form.maintain_picture"
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
  // name: "AddMaintenance",
  data() {
    return {
      form: {
        car_id: "", //车辆id
        car_no: "", //车牌号
        distance: "", //车辆行驶里程
        maintain_type: "", //维保类型
        maintain_date: "", //维保时间
        maintain_factory: "", //维修厂名称
        maintain_price: 0, //总费用
        material_price_total: 0, //材料费合计
        hour_price_total: 0, //工时费合计
        is_ticket: "", //是否收票
        maintain_description: " ", //问题描述
        maintain_picture: "", //上传附件
        maintain_detail: [], //费用明细
      },
      car_List: [], //车牌号下拉框
      currency_unit_symbol: "", //单位
      carMaintainType: [], //处理方式
      // 时间处理
      pickerOptions: {
        disabledDate(time) {
          return time.getTime() > Date.now(); //时间选择之前或者今天
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
          // 车牌号
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        maintain_type: [
          //维保类型
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        maintain_date: [
          //维保时间
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        is_ticket: [
          //是否收票
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        // maintain_picture: [
        //   //上传附件
        //   {
        //     required: true,
        //     message: this.$t("Required"),
        //     trigger: "blur",
        //   },
        // ],
      };
    },
    //判断编辑
    isEdit() {
      return !!this.$route.query.id;
    },
  },
  created() {
    if (this.isEdit) {
      this.getMaintenanceDetail(this.$route.query.id);
    }
  },
  mounted() {
    this.getCarList();
    this.getAdvanceSetting();
    this.getDictionary(); //数据字典
  },
  methods: {
    // 数据字典
    getDictionary() {
      this.$api.getDictionary().then((res) => {
        this.carMaintainType = res.data.carMaintainType;
      });
    },
    // 查看 编辑
    getMaintenanceDetail(id) {
      this.$api.getMaintenanceDetail(id).then((res) => {
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
    // 计算费用
    wacth() {
      var material_price = 0; //材料费合计
      var hour_price = 0; //小时费合计
      var maintain_price = 0; //总费用
      this.form.maintain_detail.forEach((item) => {
        item.material_price =
          Number(item.fitting_quantity) * Number(item.fitting_price); //材料费
        material_price += Number(item.material_price); //材料费合计
        hour_price += Number(item.hour_price); //工时费合计
        maintain_price = material_price + hour_price; //总费用
      });
      this.form.material_price_total = material_price; //材料费合计
      this.form.hour_price_total = hour_price; //工时费合计
      this.form.maintain_price = maintain_price; //总费用
    },
    // 提交
    onSubmit() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          if (this.isEdit) {
            this.$api
              .editMaintenance(this.$route.query.id, { ...this.form })
              .then((res) => {
                if (!res) return;
                this.$router.replace({
                  name: "Maintenance",
                  params: {
                    Maintenance: 1,
                  },
                });
              });
            return;
          }
          this.$api.addMaintenance({ ...this.form }).then((res) => {
            if (!res) return;
            this.$router.push({
              name: "Maintenance",
              params: {
                Maintenance: 1,
              },
            });
          });
        }
      });
    },
    // 表格新增
    addRow() {
      // this.wacth();
      this.form.maintain_detail.push({
        maintain_name: "", //维修项目
        fitting_name: "", //配件名称
        fitting_brand: "", //配件品牌
        fitting_model: "", //配件型号
        fitting_quantity: "", //数量
        fitting_unit: "", //单位
        fitting_price: 1, //单价
        material_price: "", //材料费
        hour_price: "", //工时费
      });
    },
    // 表格移除
    deleteRow(index, rows) {
      rows.splice(index, 1);
      this.wacth();
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
        this.form.maintain_picture = response.data.path;
      }
    },
    // 清除图片
    handlerClearDeiver() {
      // eslint-disable-next-line
      this.form.maintain_picture = "";
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
    margin: 20px;
    background-color: #fff;
  }
}
</style>