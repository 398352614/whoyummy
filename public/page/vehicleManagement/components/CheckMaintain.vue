<template>
  <!-- 维修 保养 新增 -->
  <div class="accident_box">
    <el-form :model="form" label-width="180px">
      <div>
        <div class="accident_box_title" style="margin-top: -10px">
          {{ $t("VehicleInfo") }}
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
                  :label="$t('vehicleMileage') + ':'"
                  prop="distance"
                >
                  {{ form.distance }}
                </el-form-item></el-col
              >
              <el-col :span="6">
                <el-form-item
                  :label="this.$t('maintenanceType') + ':'"
                  prop="maintain_type"
                >
                  {{ form.maintain_type_name }}
                </el-form-item>
              </el-col>

              <el-col :span="6">
                <el-form-item
                  :label="$t('maintenanceTime') + ':'"
                  class="input-style"
                  prop="maintain_date"
                >
                  {{ form.maintain_date }}
                </el-form-item></el-col
              >
              <el-col :span="6">
                <el-form-item
                  :label="$t('maintenanceShopName') + ':'"
                  prop="maintain_factory"
                >
                  {{ form.maintain_factory }}
                </el-form-item></el-col
              >
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
                    {{ scope.row.maintain_name }}
                  </template>
                </el-table-column>
                <!-- 配件名称 -->
                <el-table-column :label="$t('partsName')" align="center">
                  <template slot-scope="scope">
                    {{ scope.row.fitting_name }}
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
                    {{ scope.row.fitting_brand }}
                  </template>
                </el-table-column>
                <!-- 配件型号 -->
                <el-table-column :label="$t('partsModel')" align="center">
                  <template slot-scope="scope">
                    {{ scope.row.fitting_model }}
                  </template>
                </el-table-column>
                <!-- 数量 -->
                <el-table-column :label="'*' + $t('Quantity')" align="center">
                  <template slot-scope="scope">
                    {{ scope.row.fitting_quantity }}
                  </template>
                </el-table-column>
                <!-- 单位 -->
                <el-table-column :label="'*' + $t('unit')" align="center">
                  <template slot-scope="scope">
                    {{ scope.row.fitting_unit }}
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
                    {{ scope.row.fitting_price }}
                  </template>
                </el-table-column>
                <!-- 材料费 -->
                <el-table-column
                  :label="$t('materialFee') + '(' + currency_unit_symbol + ')'"
                  align="center"
                  width="140"
                >
                  <template slot-scope="scope">
                    {{ scope.row.material_price }}
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
                    {{ scope.row.hour_price }}
                  </template>
                </el-table-column>
                <!-- <el-table-column :label="$t('Operation')" align="center">
                  <template slot-scope="scope">
                    <el-button
                      @click.native.prevent="
                        deleteRow(scope.$index, form.maintain_detail)
                      "
                      type="danger"
                      class="remove-btn"
                      >{{ $t("Remove") }}</el-button
                    >
                  </template>
                </el-table-column> -->
              </el-table>
              <!-- <div class="add-row">
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
              </div> -->
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
              {{ form.is_ticket_name }}
            </el-form-item>
            <el-form-item
              :label="this.$t('problemDescription') + ':'"
              prop="maintain_description"
            >
              {{ form.maintain_description }}
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
            <img
              v-if="form.maintain_picture"
              :src="form.maintain_picture"
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
  // name: "CheckMaintain",
  data() {
    return {
      material_price: 0, //材料费合计
      hour_price: 0, //工时费合计
      form: {
        car_id: "", //车辆id
        car_no: "", //车牌号
        distance: "", //车辆行驶里程
        maintain_type: "", //维保类型
        maintain_date: "", //维保时间
        maintain_factory: "", //维修厂名称
        maintain_price: 0, //总费用
        is_ticket: "", //是否收票
        maintain_description: " ", //问题描述
        maintain_picture: "", //上传附件
        maintain_detail: [], //费用明细
      },
      currency_unit_symbol: "", //单位
    };
  },
  created() {
    this.getMaintenanceDetail(this.$route.query.id);
    this.getAdvanceSetting();
  },
  methods: {
    // 查看
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
  }
  .dialog_footer {
    float: right;
    margin: 20px;
    background-color: #fff;
  }
}
</style>