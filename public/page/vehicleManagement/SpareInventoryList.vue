<template>
  <!-- 备品库存 -->
  <div class="spareInventory_list">
    <!-- 查询开始 -->
    <el-card class="box-card" shadow="never">
      <div class="conditional">
        <div>
          <!-- <span class="search-title">{{ $t("sparePartNo") }}:</span> -->
          <el-input
            size="small"
            clearable
            :placeholder="$t('enterSparNo')"
            @keyup.enter.native="handleChangePageParams"
            v-model="pageParams.sp_no"
            class="search-bar"
          >
          </el-input>
          <!-- <span class="search-title">{{ $t("sparePartName") }}:</span> -->
          <el-input
            size="small"
            clearable
            :placeholder="$t('enterSpareName')"
            @keyup.enter.native="handleChangePageParams"
            v-model="pageParams.sp_name"
            class="search-bar"
          >
          </el-input>

          <el-button
            size="small"
            @click="handleChangePageParams"
            :loading="this.$store.state.config.button_loading"
            type="primary"
            >{{ $t("Search") }}</el-button
          >
        </div>
        <!-- 新增入库 -->
        <el-tooltip
          :content="$t('AdministratorActivate')"
          placement="top"
          :disabled="hasPerm('spare-parts.createStock') ? true : false"
        >
          <div>
            <el-button
              :disabled="!hasPerm('spare-parts.createStock')"
              size="small"
              @click="handlerAddClick"
              :loading="this.$store.state.config.button_loading"
              type="primary"
              class="add-but"
              >{{ $t("addStorage") }}</el-button
            >
          </div>
        </el-tooltip>
        <!-- <el-button
              size="small"
              @click="handleResetPageParams"
              :loading="this.$store.state.config.button_loading"
              type="info"
              >{{ $t("reset") }}</el-button
            > -->
      </div>
    </el-card>
    <!-- 表格 -->
    <el-card shadow="never" class="card-box">
      <el-table
        height="calc(100vh - 276px)"
        :data="hasPerm('spare-parts.stock') ? tableData : []"
        v-loading="global_loading()"
        element-loading-text="loading..."
        border
        style="width: 100%"
        :cell-style="{ padding: '5px 0px' }"
      >
        <template slot="empty" v-if="!hasPerm('spare-parts.stock')">
          <div style="font-size: 18px">
            {{ $t("AdministratorActivate") }}
          </div>
        </template>
        <!-- 序号 -->
        <el-table-column type="index" align="center" width="50" prop=" id	">
          <template slot="header">
            <span>{{ $t("Number") }}</span>
          </template>
        </el-table-column>
        <!-- 操作 -->
        <el-table-column
          :label="this.$t('Operation')"
          width="130"
          align="center"
        >
          <template slot-scope="scope">
            <el-tooltip
              :content="$t('AdministratorActivate')"
              placement="top"
              :disabled="hasPerm('spare-parts.createRecord') ? true : false"
            >
              <div>
                <el-button
                  :disabled="!hasPerm('spare-parts.createRecord')"
                  @click="receive(scope.row)"
                  type="primary"
                  size="mini"
                  class="operation"
                  plain
                  >{{ $t("receiving") }}</el-button
                >
              </div>
            </el-tooltip>
          </template>
        </el-table-column>
        <!-- 备品编号 -->
        <el-table-column prop="sp_no" align="center" :label="$t('sparePartNo')">
        </el-table-column>
        <!-- 备品名称 -->
        <el-table-column
          prop="sp_name"
          align="center"
          :label="$t('sparePartName')"
        >
        </el-table-column>
        <!-- 品牌 -->
        <el-table-column
          prop="sp_brand"
          align="center"
          :label="$t('spareBrand')"
        >
        </el-table-column>
        <!-- 规格型号 -->
        <el-table-column
          prop="sp_model"
          align="center"
          width="130"
          :label="$t('specification')"
        >
        </el-table-column>
        <!-- 单位 -->
        <el-table-column
          prop="sp_unit_name"
          align="center"
          width="120"
          :label="$t('unit')"
        >
        </el-table-column>
        <!-- 数量 -->
        <el-table-column
          prop="stock_quantity"
          align="center"
          :label="$t('Quantity')"
        >
        </el-table-column>
      </el-table>
      <!-- 页码 -->
      <base-pagination
        :pageParams="pageParams"
        @search="getSpareInventory"
        class="pagination-size"
      ></base-pagination>
      <iframe frameborder="0" name="downExcel" style="display: none"></iframe>
    </el-card>
    <!-- 弹出框 -->
    <el-dialog
      :title="type == 1 ? $t('addStorage') : $t('requisitionSpareParts')"
      :visible.sync="dialogVisible"
      width="50%"
    >
      <hr style="margin-top: -20px" />
      <el-form
        :label-position="labelPosition"
        label-width="170px"
        :model="form"
        :rules="formRules"
        ref="form"
      >
        <el-row :gutter="20">
          <el-col :span="12">
            <!-- 备品编号 -->
            <el-form-item :label="this.$t('sparePartNo') + ':'" prop="sp_no">
              <el-select
                v-model="form.sp_no"
                :placeholder="this.$t('sparePartNo')"
                style="width: 100%"
                size="small"
                @change="changeNumber"
                :disabled="this.type == 2"
              >
                <el-option
                  v-for="(item, index) in sp_no_list"
                  :key="index"
                  :label="item.sp_no"
                  :value="item.sp_no"
                ></el-option>
              </el-select>
            </el-form-item>
            <!-- 品牌 -->
            <el-form-item
              :label="this.$t('spareBrand') + ':'"
              prop="sp_brand"
              style="width: 100%"
            >
              {{ sp_brand }}
            </el-form-item>
            <!-- 单位 -->
            <el-form-item
              :label="this.$t('unit') + ':'"
              prop="sp_unit"
              style="width: 100%"
            >
              {{ sp_unit_name }}
            </el-form-item>
            <!-- 入库数量 -->
            <el-form-item
              :label="this.$t('warehouseQuantity') + ':'"
              prop="stock_quantity"
              v-if="this.type == 1"
            >
              <el-input
                v-model="form.stock_quantity"
                style="width: 100%"
                size="small"
              ></el-input>
            </el-form-item>
            <!-- 单价 -->
            <el-form-item
              :label="this.$t('unitPrice') + ':'"
              prop="receive_price"
              v-if="this.type == 2"
            >
              <el-input
                v-model="form.receive_price"
                style="width: 100%"
                size="small"
              ></el-input>
            </el-form-item>
            <!-- 使用车辆 -->
            <el-form-item
              :label="this.$t('usedVehicles') + ':'"
              prop="car_no"
              v-if="this.type == 2"
            >
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
                ></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <!-- 备品名称 -->
            <el-form-item
              :label="this.$t('sparePartName') + ':'"
              prop="sp_name"
              style="width: 100%"
            >
              {{ sp_name }}
            </el-form-item>
            <!-- 型号 -->
            <el-form-item
              :label="(this, $t('spModel')) + ':'"
              prop="sp_model"
              style="width: 100%"
            >
              {{ sp_model }} </el-form-item
            > 
            <!-- 在库数量 -->
            <el-form-item
              :label="this.$t('stockQuantity') + ':'"
              prop="stock_now"
              v-if="this.type == 1"
            >
              {{ stock_now }}
            </el-form-item>
            <!-- 可用数量 -->
            <el-form-item
              :label="this.$t('quantityAvailable') + ':'"
              prop="user"
              v-if="this.type == 2"
            >
              {{ user }}
            </el-form-item>
            <!-- 领取数量 -->
            <el-form-item
              :label="this.$t('quantityReceived') + ':'"
              prop="receive_quantity"
              v-if="this.type == 2"
            >
              <el-input
                v-model="form.receive_quantity"
                style="width: 100%"
                size="small"
              ></el-input>
            </el-form-item>
            <!-- 领用人 -->
            <el-form-item
              :label="this.$t('recipient') + ':'"
              prop="receive_person"
              v-if="this.type == 2"
            >
              <el-input
                v-model="form.receive_person"
                style="width: 100%"
                size="small"
              ></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <!-- 备注 -->
        <el-form-item
          :label="this.$t('Remark') + ':'"
          prop="receive_remark"
          v-if="this.type == 2"
        >
          <el-input
            type="textarea"
            v-model="form.receive_remark"
            style="width: 500px"
          ></el-input>
        </el-form-item>

        <!-- 领用时间 -->
        <el-form-item
          :label="$t('collectioTime') + ':'"
          class="input-style item-left"
          prop="receive_date"
          v-if="this.type == 2"
        >
          <el-date-picker
            value-format="yyyy-MM-dd HH:mm:ss"
            v-model="form.receive_date"
            type="datetime"
            :picker-options="pickerOptions"
            :placeholder="$t('collectioTime')"
            style="width: 100%"
          >
          </el-date-picker>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer" style="margin: 0 auto">
        <hr />
        <el-button @click="dialogVisible = false" class="cancel">{{
          $t("Cancel")
        }}</el-button>
        <el-button type="primary" @click="sure" class="submit">{{
          $t("Confirm")
        }}</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import BasePagination from "@/components/BasePagination/BasePagination";
import loading from "@/mixin/global_loading";

export default {
  name: "SpareInventory",
  components: {
    BasePagination,
  },
  mixins: [loading],
  data() {
    return {
      tableData: [],
      spareList: [],
      sp_no_list: [], //编号下拉框
      car_List: [], //车牌号下拉框
      user: "-", //可用
      stock_now: "-", //在库数量
      sp_name: "-", //备品名称
      sp_brand: "-", //品牌
      sp_model: "-", //型号
      sp_unit_name: "-", //单位
      search_date: ["", ""],
      dialogVisible: false,
      labelPosition: "right",
      type: 1, //判断新增还是修改
      pageParams: {
        sp_no: "", //备品编号
        sp_name: "", //备品名称
        total: 0,
        page: 1,
        per_page: 20,
      },
      form: {
        sp_no: "", //备品编号
        stock_quantity: "", //入库数量
        receive_price: "", //单价
        car_no: "", //使用车辆
        receive_quantity: "", //领取数量
        receive_person: "", //领用人
        receive_remark: "", //备注
        receive_date: new Date(), //领用时间
      },
      car_id: "", //车辆id

      // 时间
      pickerOptions: {
        disabledDate(time) {
          // return time.getTime() > Date.now();
        },
      },
    };
  },
  computed: {
    // 表单验证
    formRules() {
      return {
        sp_no: [
          // 备品编号
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        stock_quantity: [
          // 入库数量
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        receive_price: [
          // 单价
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],

        receive_quantity: [
          // 领取数量
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        car_no: [
          //车辆
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        receive_person: [
          // 领用人
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
      };
    },
  },
  created() {
    this.getSpareInventory();
  },
  mounted() {
    this.getNumber();
    this.getCarList();
  },
  methods: {
    // 监听在库数量
    // changeQuantity() {
    //   this.stock_now = this.form.stock_quantity;
    // },
    // 车牌号
    getCarList() {
      this.$api.getCarsPublic().then((res) => {
        this.car_List = res.data.data;
        res.data.data.forEach((item) => {
          this.car_id = item.id;
        });
      });
    },
    // 备品编号
    getNumber() {
      this.$api.getSpare().then((res) => {
        this.sp_no_list = res.data.data;
      });
    },
    // 选择编号 获取数据
    changeNumber(val) {
      this.sp_no_list.map((item) => {
        if (item.sp_no == val) {
          this.sp_name = item.sp_name; //备品名称
          this.sp_brand = item.sp_brand; //品牌
          this.sp_model = item.sp_model; //型号
          this.sp_unit_name = item.sp_unit_name; //单位
          this.stock_now = item.stock_now; //库存
        }
      });
    },
    // 新增入库
    handlerAddClick() {
      this.type = 1;
      this.dialogVisible = true;
      this.form.sp_no = "";
      this.sp_name = "-"; //备品名称
      this.sp_brand = "-"; //品牌
      this.sp_model = "-"; //型号
      this.sp_unit_name = "-"; //单位
      (this.stock_now = "-"), //在库数量
        (this.form = {
          stock_quantity: "", //入库数量
        });
      this.getNumber();
    },
    getSpareInventory() {
      // 列表
      this.$api.getSpareInventory(this.pageParams).then((res) => {
        this.tableData = res.data.data;
        this.pageParams.total = res.data.meta.total;
      });
    },
    handleChangePageParams() {
      // 查询
      this.pageParams.page = 1;
      this.getSpareInventory();
    },
    // 重置
    handleResetPageParams() {
      this.pageParams.sp_no = "";
      this.pageParams.sp_name = "";
      this.getSpareInventory();
    },
    // 领用
    receive(row) {
      this.type = 2;
      this.dialogVisible = true;
      this.sp_name = row.sp_name;
      this.sp_brand = row.sp_brand;
      this.sp_model = row.sp_model;
      this.sp_unit_name = row.sp_unit_name;
      (this.user = row.stock_quantity),
        (this.form = {
          ...this.form,
          sp_no: row.sp_no,
          stock_quantity: row.stock_quantity,
          receive_price: row.receive_price,
          receive_quantity: row.receive_quantity,
          receive_person: row.receive_person,
          receive_remark: row.receive_remark,
          receive_date: new Date(),
        });
    },
    // 确定
    sure() {
      // 新增入库
      if (this.type == 1) {
        this.$refs.form.validate((valid) => {
          if (valid) {
            this.$api.addSpareInventory({ ...this.form }).then((res) => {
              if (res.code === 200) {
                this.pageParams.page = 1;
                this.getSpareInventory();
                this.dialogVisible = false;
              }
            });
          }
        });
      } else if (this.type == 2) {
        this.$refs.form.validate((valid) => {
          if (valid) {
            this.$api
              .addSpareRecord({ ...this.form, car_id: this.car_id })
              .then((res) => {
                if (res.code === 200) {
                  this.pageParams.page = 1;
                  this.getSpareInventory();
                  this.dialogVisible = false;
                }
              });
          }
        });
      }
    },
  },
};
</script>
<style lang="less" scoped>
.spareInventory_list {
  .conditional {
    display: flex;
    justify-content: space-between;
  }
  .box-card {
    margin-bottom: 10px;
  }
}
</style>
<style lang="less">
.spareInventory_list {
  .conditional {
    .el-input__inner {
      border-radius: 0;
    }
  }
  .el-date-editor {
    width: 100%;
  }
  .el-select {
    width: 100%;
  }
  .el-dialog__footer {
    text-align: right;
  }
}
</style>
