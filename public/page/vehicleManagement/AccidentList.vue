<template>
  <!-- 事故列表 -->
  <div class="accident-list">
    <!-- 查询开始 -->
    <el-card class="box-card" shadow="never">
      <div class="conditional">
        <div class="search-input">
          <span class="search-title"> {{ $t("accidentTime") }}</span>
          <el-date-picker
            size="small"
            v-model="search_date"
            type="daterange"
            value-format="yyyy-MM-dd"
            :range-separator="$t('To')"
            :start-placeholder="$t('StartTime')"
            :end-placeholder="$t('EndTime')"
            class="search-bar-date"
            :picker-options="pickerOptionsSearch"
          >
          </el-date-picker>
          <!-- <span class="search-title">{{ $t("NumberPlate") }}:</span> -->
          <el-select
            clearable
            :placeholder="this.$t('pleaseNumber')"
            size="small"
            @keyup.enter.native="handleChangePageParams"
            v-model="pageParams.car_no"
            class="search-bar"
          >
            <el-option
              v-for="(item, index) in car_List"
              :key="index"
              :label="item.car_no"
              :value="item.car_no"
            ></el-option>
          </el-select>
          <!-- <span class="search-title">{{ $t("activePassive") }}:</span> -->
          <el-select
            clearable
            :placeholder="this.$t('PleaseChooseActivePassive')"
            size="small"
            @keyup.enter.native="handleChangePageParams"
            v-model="pageParams.accident_duty"
            class="search-bar"
          >
            <el-option
              v-for="(item, index) in carAccidentDuty"
              :key="index"
              :label="item.name"
              :value="item.id"
            ></el-option>
          </el-select>

          <!-- <span class="search-title">{{ $t("processingMode") }}:</span> -->
          <el-select
            clearable
            :placeholder="this.$t('pleaseProcessingMethod')"
            size="small"
            @keyup.enter.native="handleChangePageParams"
            v-model="pageParams.deal_type"
            class="search-bar"
          >
            <el-option
              v-for="(item, index) in carAccidentDealType"
              :key="index"
              :label="item.name"
              :value="item.id"
            ></el-option>
          </el-select>
          <el-button
            size="small"
            @click="handleChangePageParams"
            :loading="this.$store.state.config.button_loading"
            type="primary"
            >{{ $t("Search") }}</el-button
          >
        </div>
        <!-- <el-button
            size="small"
            type="info"
            @click="handleResetPageParams"
            :loading="this.$store.state.config.button_loading"
            >{{ $t("reset") }}</el-button
          > -->
        <div class="search-right">
          <!-- 新增 -->
          <el-tooltip
            :content="$t('AdministratorActivate')"
            placement="top"
            :disabled="hasPerm('car-accident.store') ? true : false"
          >
            <div>
              <el-button
                :disabled="!hasPerm('car-accident.store')"
                size="small"
                @click="handlerAddClick"
                :loading="this.$store.state.config.button_loading"
                type="primary"
                class="but"
                >{{ $t("Add") }}</el-button
              >
            </div>
          </el-tooltip>
          <!-- 删除 -->
          <el-tooltip
            :content="$t('AdministratorActivate')"
            placement="top"
            :disabled="hasPerm('car-accident.destroy') ? true : false"
          >
            <div>
              <el-button
                :disabled="!hasPerm('car-accident.destroy')"
                size="small"
                type="danger"
                @click="handlerDeleteClick"
                :loading="this.$store.state.config.button_loading"
                class="but"
                >{{ $t("Delete") }}</el-button
              >
            </div>
          </el-tooltip>
        </div>
      </div>
    </el-card>
    <!-- 表格 -->
    <el-card shadow="never" class="card-box">
      <!-- <el-popconfirm
        placement="top"
        confirm-button-text="确定"
        @confirm="handlerDeleteClick"
        cancel-button-text="取消"
        icon="el-icon-info"
        icon-color="red"
        title="确定删除吗？"
      >
        <el-button
          size="small"
          :loading="this.$store.state.config.button_loading"
          style="margin-left: 30px; float: right"
          slot="reference"
          >{{ $t("删除") }}</el-button
        >
      </el-popconfirm> -->

      <el-table
        height="calc(100vh - 277px)"
        :data="hasPerm('car-accident.index') ? tableData : []"
        v-loading="global_loading()"
        ref="multipleSelection"
        element-loading-text="loading..."
        border
        style="width: 100%"
        @selection-change="handleSelectionChange"
        :cell-style="{ padding: '5px 0px' }"
      >
        <template slot="empty" v-if="!hasPerm('car-accident.index')">
          <div style="font-size: 18px">
            {{ $t("AdministratorActivate") }}
          </div>
        </template>
        <el-table-column align="center" type="selection" width="50">
        </el-table-column>
        <el-table-column type="index" align="center" width="50">
          <template slot="header">
            <span>{{ $t("Number") }}</span>
          </template>
        </el-table-column>
        <!-- 操作 -->
        <el-table-column width="120" align="center" :label="$t('Operation')">
          <template slot-scope="scope">
            <el-dropdown>
              <el-button size="mini" type="primary" class="operation" plain>
                {{ $t("Operation") }}
                <i class="el-icon-arrow-down el-icon--right"></i>
              </el-button>
              <el-dropdown-menu slot="dropdown">
                <!-- 详情 -->
                <el-tooltip
                  :content="$t('AdministratorActivate')"
                  placement="top"
                  :disabled="hasPerm('car-accident.index') ? true : false"
                >
                  <div
                    :style="
                      hasPerm('car-accident.index') ? '' : 'cursor: not-allowed'
                    "
                  >
                    <el-dropdown-item
                      :disabled="!hasPerm('car-accident.index')"
                      @click.native="getAccidentDetail(scope.row.id)"
                    >
                      <el-button
                        class="operation"
                        size="small"
                        type="text"
                        :disabled="!hasPerm('car-accident.index')"
                        >{{ $t("Show") }}</el-button
                      >
                    </el-dropdown-item>
                  </div>
                </el-tooltip>
                <!-- 编辑 -->
                <el-tooltip
                  :content="$t('AdministratorActivate')"
                  placement="top"
                  :disabled="hasPerm('car-accident.update') ? true : false"
                >
                  <div
                    :style="
                      hasPerm('car-accident.update')
                        ? ''
                        : 'cursor: not-allowed'
                    "
                  >
                    <el-dropdown-item
                      @click.native="putAccident(scope.row)"
                      :disabled="!hasPerm('car-accident.update')"
                    >
                      <el-button
                        class="operation"
                        size="small"
                        type="text"
                        :disabled="!hasPerm('car-accident.update')"
                        >{{ $t("Edit") }}</el-button
                      >
                    </el-dropdown-item>
                  </div>
                </el-tooltip>
              </el-dropdown-menu>
            </el-dropdown>
          </template>
        </el-table-column>
        <el-table-column
          prop="accident_no"
          align="center"
          :label="$t('accidentResponseNo')"
          width="180"
        >
        </el-table-column>
        <el-table-column
          prop="car_no"
          align="center"
          :label="$t('NumberPlate')"
          width="180"
        >
        </el-table-column>
        <el-table-column
          prop="driver_fullname"
          align="center"
          :label="$t('driverName')"
          width="130"
        >
        </el-table-column>
        <el-table-column
          prop="accident_date"
          align="center"
          width="130"
          :label="$t('accidentTime')"
        >
        </el-table-column>
        <el-table-column
          prop="accident_location"
          align="center"
          width="180"
          :label="$t('accidentLocation')"
        >
        </el-table-column>
        <el-table-column
          prop="accident_duty_name"
          align="center"
          :label="$t('activePassive')"
          width="180"
        >
        </el-table-column>
        <el-table-column
          prop="insurance_price"
          align="center"
          width="210"
          :label="$t('compensation') + '(' + currency_unit_symbol + ')'"
        >
        </el-table-column>
        <el-table-column
          prop="insurance_payment"
          align="center"
          :label="$t('advancePayment') + '(' + currency_unit_symbol + ')'"
          width="180"
        >
        </el-table-column>
        <el-table-column
          prop="deal_type_name"
          align="center"
          width="180"
          :label="$t('processingMode')"
        >
        </el-table-column>
        <el-table-column
          prop="operator"
          align="center"
          :label="$t('operator')"
          width="100"
        >
        </el-table-column>
        <el-table-column
          prop="created_at"
          align="center"
          :label="$t('Created')"
          width="200"
        >
        </el-table-column>
      </el-table>
      <base-pagination
        :pageParams="pageParams"
        @search="getAccident"
        class="pagination-size"
      ></base-pagination>
      <iframe frameborder="0" name="downExcel" style="display: none"></iframe>
    </el-card>
  </div>
</template>

<script>
import BasePagination from "@/components/BasePagination/BasePagination";
import loading from "@/mixin/global_loading";

export default {
  name: "Accident",
  components: {
    BasePagination,
  },
  mixins: [loading],
  data() {
    return {
      multipleSelection: [],
      tableData: [],
      pageParams: {
        car_no: "", //车牌号
        accident_duty: "", //主被动
        deal_type: "", //处理方式
        begin_date: "", //开始时间
        end_date: "", //结束时间
        total: 0,
        page: 1,
        per_page: 20,
      },
      currency_unit_symbol: "", //单位
      car_List: [],
      search_date: ["", ""],
      carAccidentDuty: [], //主被动
      carAccidentDealType: [], //处理方式
    };
  },
  watch: {
    search_date: {
      handler() {
        if (this.search_date) {
          this.pageParams.begin_date = this.search_date[0];
          this.pageParams.end_date = this.search_date[1];
        } else {
          this.pageParams.begin_date = "";
          this.pageParams.end_date = "";
        }

        this.pageParams.page = 1;
      },
    },
  },
  computed: {
    pickerOptionsSearch() {
      return {
        shortcuts: [
          {
            text: this.$t("zuiJingYiZhou"),
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
              picker.$emit("pick", [start, end]);
            },
          },
          {
            text: this.$t("zuiJingYiYue"),
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
              picker.$emit("pick", [start, end]);
            },
          },
          {
            text: this.$t("LastThreeMonths"),
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
              picker.$emit("pick", [start, end]);
            },
          },
        ],
      };
    },
  },
  activated() {
    if (this.$route.params.Accident) {
      this.getAccident();
    }
  },
  created() {
    this.getAccident(); //列表
    this.getCarList(); // 车牌号
    this.getAdvanceSetting(); // 获取单位值
    this.getDictionary(); // 数据字典
  },
  methods: {
    // 数据字典
    getDictionary() {
      this.$api.getDictionary().then((res) => {
        this.carAccidentDealType = res.data.carAccidentDealType; //处理方式
        this.carAccidentDuty = res.data.carAccidentDuty; //主被动
      });
    },
    // 获取单位值
    getAdvanceSetting() {
      this.$api.getAdvanceSetting().then((res) => {
        this.currency_unit_symbol = res.data.currency_unit_symbol; //单价单位
      });
    },
    // 车牌号
    getCarList() {
      this.$api.getCarsPublic().then((res) => {
        this.car_List = res.data.data;
      });
    },
    // 列表选中
    handleSelectionChange(val) {
      this.multipleSelection = val;
    },
    // 跳转到新增
    handlerAddClick() {
      this.$router.push({
        name: "AddAccident",
      });
    },
    getAccident() {
      // 列表
      this.$api.getAccident(this.pageParams).then((res) => {
        this.tableData = res.data.data;
        this.pageParams.total = res.data.meta.total;
      });
    },
    // 查询
    handleChangePageParams() {
      this.pageParams.page = 1;
      this.getAccident();
    },
    // 重置
    handleResetPageParams() {
      this.pageParams.car_no = "";
      this.pageParams.accident_duty = "";
      this.pageParams.deal_type = "";
      this.pageParams.begin_date = "";
      this.pageParams.end_date = "";
      this.search_date = "";
      this.getAccident();
    },
    // 查看
    getAccidentDetail(id) {
      this.$router.push({
        name: "CheckAccident",
        query: {
          id: id,
        },
      });
    },
    // 编辑
    putAccident(row) {
      this.$router.push({
        name: "AddAccident",
        query: {
          id: row.id,
        },
      });
    },
    // 批量删除
    handlerDeleteClick() {
      var data = this.multipleSelection;
      if (!data.length) {
        this.$message({
          type: "error",
          message: this.$t("PleaseSelectLeastMessage"),
        });
        return;
      }
      let ids = data.map((el) => {
        return el.id;
      });
      this.$confirm(this.$t("ConfirmToCancel") + "?", this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      })
        .then(() => {
          this.$api.delAccident(ids.join(",")).then((res) => {
            if (res.code === 200) {
              // this.$message({
              //   message: "删除成功!",
              //   type: "success",
              // });
            }
            this.getAccident();
          });
        })
        .catch(() => {});
      // this.$api.delAccident(ids.join(",")).then((res) => {
      //   this.getAccident();
      // });
    },
  },
};
</script>
<style lang="less" scoped>
.accident-list {
  .box-card {
    margin-bottom: 10px;
    .conditional {
      .search-input {
        float: left;
        margin-bottom: 20px;
      }
      .search-right {
        display: flex;
        float: right;
        gap: 5px;
        margin-bottom: 10px;
      }
      .but {
        margin-left: 0px;
      }
    }
  }
}
</style>
<style lang="less">
.accident-list {
  .conditional {
    .el-input__inner {
      border-radius: 0;
    }
  }
}
</style>
