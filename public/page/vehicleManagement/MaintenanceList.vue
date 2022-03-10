<template>
  <!-- 保养 维修列表 -->
  <div class="maintenance-list">
    <!-- 查询 -->
    <el-card class="box-card card-box" shadow="never">
      <div class="conditional">
        <span class="search-title"> {{ $t("maintenanceTime") }}</span>
        <el-date-picker
          size="small"
          v-model="search_date"
          type="daterange"
          value-format="yyyy-MM-dd"
          :range-separator="$t('To')"
          :start-placeholder="$t('StartTime')"
          :end-placeholder="$t('EndTime')"
          :picker-options="pickerOptionsSearch"
          class="search-bar-date"
          style="margin-bottom: 10px"
        >
        </el-date-picker>
        <!-- <span class="search-title">{{ $t("NumberPlate") }}:</span> -->
        <el-select
          clearable
          :placeholder="this.$t('pleaseNumber')"
          size="small"
          @keyup.enter.native="getMaintenance"
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
        <!-- <span class="search-title">{{ $t("maintenanceType") }}:</span> -->
        <el-select
          clearable
          :placeholder="this.$t('PleaseSelectMaintenanceType')"
          size="small"
          @keyup.enter.native="getMaintenance"
          v-model="pageParams.maintain_type"
          class="search-bar"
        >
          <el-option
            v-for="(item, index) in maintain_type_list"
            :key="index"
            :label="item.name"
            :value="item.id"
          ></el-option>
        </el-select>

        <!-- <span class="search-title">{{ $t("invoiceStatus") }}:</span> -->
        <el-select
          clearable
          :placeholder="this.$t('PleaseSelectReceiptStatus')"
          size="small"
          @keyup.enter.native="getMaintenance"
          v-model="pageParams.is_ticket"
          class="search-bar"
        >
          <el-option
            v-for="(item, index) in is_ticket_list"
            :key="index"
            :label="item.name"
            :value="item.id"
          ></el-option>
        </el-select>
        <!-- <span class="search-title">{{ $t("maintenanceShopName") }}</span> -->
        <el-input
          size="small"
          clearable
          :placeholder="$t('enterMaintenanceShop')"
          @keyup.enter.native="getMaintenance"
          v-model="pageParams.maintain_factory"
          class="search-bar"
        >
        </el-input>
        <el-button
          size="small"
          @click="getMaintenance"
          :loading="this.$store.state.config.button_loading"
          type="primary"
          >{{ $t("Search") }}</el-button
        >

        <!-- <el-button
              size="small"
              @click="handleResetPageParams"
              :loading="this.$store.state.config.button_loading"
              type="info"
              >{{ $t("reset") }}</el-button
            > -->
        <div class="search-but">
          <!-- 新增按钮 -->
          <el-tooltip
            :content="$t('AdministratorActivate')"
            placement="top"
            :disabled="hasPerm('car-maintain.store') ? true : false"
          >
            <div>
              <el-button
                :disabled="!hasPerm('car-maintain.store')"
                size="small"
                @click="handlerAddClick"
                :loading="this.$store.state.config.button_loading"
                type="primary"
                class="but"
                >{{ $t("Add") }}</el-button
              >
            </div>
          </el-tooltip>
          <!-- 收票 -->
          <el-tooltip
            :content="$t('AdministratorActivate')"
            placement="top"
            :disabled="hasPerm('car-maintain.ticket') ? true : false"
          >
            <div>
              <el-button
                :disabled="!hasPerm('car-maintain.ticket')"
                :class="[
                  hasPerm('car-maintain.ticket') ? 'common' : 'common-disabled',
                  'but',
                ]"
                type="primary"
                size="small"
                @click="handlerChargeClick"
                :loading="this.$store.state.config.button_loading"
                slot="reference"
                >{{ $t("receipt") }}</el-button
              >
            </div>
          </el-tooltip>
          <!-- 导出 -->
          <el-tooltip
            :content="$t('AdministratorActivate')"
            placement="top"
            :disabled="hasPerm('car-maintain.export') ? true : false"
          >
            <div>
              <el-button
                :disabled="!hasPerm('car-maintain.export')"
                :class="[
                  hasPerm('car-maintain.export') ? 'common' : 'common-disabled',
                  'but',
                ]"
                type="primary"
                size="small"
                @click="handlerExportClick"
                :loading="this.$store.state.config.button_loading"
                >{{ $t("Export") }}</el-button
              >
            </div>
          </el-tooltip>
          <!-- 删除 -->
          <el-tooltip
            :content="$t('AdministratorActivate')"
            placement="top"
            :disabled="hasPerm('car-maintain.destroy') ? true : false"
          >
            <div>
              <el-button
                :disabled="!hasPerm('car-maintain.destroy')"
                size="small"
                type="danger"
                @click="handlerDeleteClick"
                :loading="this.$store.state.config.button_loading"
                slot="reference"
                class="but"
                >{{ $t("Delete") }}</el-button
              >
            </div>
          </el-tooltip>
        </div>
      </div>
    </el-card>
    <el-card shadow="never" class="card-box">
      <!-- 表格 -->
      <el-table
        :height="heightSearch ? 'calc(100vh - 328px)' : 'calc(100vh - 296px)'"
        :data="hasPerm('car-maintain.index') ? tableData : []"
        ref="multipleSelection"
        v-loading="global_loading()"
        element-loading-text="loading..."
        border
        @selection-change="handleSelectionChange"
        style="width: 100%; margin-top: 20px"
        :cell-style="{ padding: '5px 0px' }"
      >
        <template slot="empty" v-if="!hasPerm('car-maintain.index')">
          <div style="font-size: 18px">
            {{ $t("AdministratorActivate") }}
          </div>
        </template>
        <el-table-column align="center" type="selection" width="50">
        </el-table-column>
        <!-- 操作 -->
        <el-table-column width="120" align="center" :label="$t('Operation')">
          <template slot-scope="scope">
            <el-dropdown>
              <el-button size="mini" type="primary" plain class="operation">
                {{ $t("Operation") }}
                <i class="el-icon-arrow-down el-icon--right"></i>
              </el-button>
              <el-dropdown-menu slot="dropdown">
                <!-- 详情 -->
                <el-tooltip
                  :content="$t('AdministratorActivate')"
                  placement="top"
                  :disabled="hasPerm('car-maintain.index') ? true : false"
                >
                  <div
                    :style="
                      hasPerm('car-maintain.index') ? '' : 'cursor: not-allowed'
                    "
                  >
                    <el-dropdown-item
                      @click.native="getMaintenanceDetail(scope.row.id)"
                      :disabled="!hasPerm('car-maintain.index')"
                    >
                      <el-button
                        class="operation"
                        size="small"
                        type="text"
                        :disabled="!hasPerm('car-maintain.index')"
                        >{{ $t("Show") }}</el-button
                      >
                    </el-dropdown-item>
                  </div>
                </el-tooltip>
                <!-- 编辑 -->
                <el-tooltip
                  :content="$t('AdministratorActivate')"
                  placement="top"
                  :disabled="hasPerm('car-maintain.update') ? true : false"
                >
                  <div
                    :style="
                      hasPerm('car-maintain.update')
                        ? ''
                        : 'cursor: not-allowed'
                    "
                  >
                    <el-dropdown-item
                      @click.native="editMaintenance(scope.row)"
                      :disabled="!hasPerm('car-maintain.update')"
                    >
                      <el-button
                        class="operation"
                        size="small"
                        type="text"
                        :disabled="!hasPerm('car-maintain.update')"
                        >{{ $t("Edit") }}</el-button
                      >
                    </el-dropdown-item>
                  </div>
                </el-tooltip>
              </el-dropdown-menu>
            </el-dropdown>
          </template>
        </el-table-column>
        <!-- <el-table-column
      type="index"
      align="center"
      width="80">
        <template slot="header">
          <span>{{$t('Number')}}</span>
        </template>
      </el-table-column> -->
        <el-table-column
          prop="maintain_no"
          align="center"
          :label="$t('serialNo')"
          width="150"
        >
        </el-table-column>
        <el-table-column
          prop="car_no"
          align="center"
          :label="$t('NumberPlate')"
          width="150"
        >
        </el-table-column>
        <el-table-column
          prop="maintain_type_name"
          align="center"
          :label="$t('maintenanceType')"
          width="150"
        >
        </el-table-column>
        <el-table-column
          prop="distance"
          align="center"
          width="150"
          :label="$t('vehicleMileage')"
        >
        </el-table-column>
        <el-table-column
          prop="maintain_price"
          align="center"
          width="150"
          :label="$t('totalCost') + '(' + currency_unit_symbol + ')'"
        >
        </el-table-column>
        <el-table-column
          prop="is_ticket_name"
          align="center"
          :label="$t('invoiceStatus')"
          width="150"
        >
        </el-table-column>
        <el-table-column
          prop="maintain_date"
          align="center"
          width="150"
          :label="$t('maintenanceTime')"
        >
        </el-table-column>
        <el-table-column
          prop="maintain_factory"
          align="center"
          :label="$t('maintenanceShopName')"
          width="200"
        >
        </el-table-column>
        <el-table-column
          prop="operator"
          align="center"
          :label="$t('operator')"
          width="130"
        >
        </el-table-column>
        <el-table-column
          prop="created_at"
          align="center"
          :label="$t('Created')"
          width="180"
        >
        </el-table-column>
      </el-table>
      <base-pagination
        :pageParams="pageParams"
        @search="getMaintenance"
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
  name: "Maintenance",
  components: {
    BasePagination,
  },
  mixins: [loading],
  data() {
    return {
      heightSearch: false,
      tableData: [],
      multipleSelection: [],
      visible: false,
      pageParams: {
        car_no: "", //车牌号
        maintain_type: "", //维保类型
        is_ticket: "", //收票状态
        begin_date: "", //开始时间
        end_date: "", //结束时间
        maintain_factory: "", //维修厂名称
        total: 0,
        page: 1,
        per_page: 20,
      },
      search_date: ["", ""],
      currency_unit_symbol: "", //单位
      car_List: [],
      maintain_type_list: [
        //维保类型
        {
          id: "1",
          name: this.$t("maintenance"),
        },
        {
          id: "2",
          name: this.$t("repair"),
        },
      ],
      is_ticket_list: [
        //处理方式
        {
          id: "1",
          name: this.$t("invoiceReceived"),
        },
        {
          id: "2",
          name: this.$t("invoiceNotReceived"),
        },
      ],
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
    if (this.$route.params.Maintenance) {
      this.getMaintenance();
    }
  },
  mounted() {
    //  onresize  事件会在窗口或框架被调整大小时发生
    window.onresize = () => {
      if (!this.timer) {
        this.timer = true;
        let that = this;
        setTimeout(function () {
          that.searchFormWidth();
          that.timer = false;
        }, 400);
      }
    };
  },
  destroyed() {
    // 组件销毁后解绑事件
    window.onresize = null;
  },
  created() {
    this.searchFormWidth();
    this.getMaintenance();
    this.getCarList();
    this.getAdvanceSetting();
  },
  methods: {
    // 获取窗口的宽度,调整表格高度
    searchFormWidth() {
      let w = window.innerWidth;
      if (w <= 1652) {
        this.heightSearch = true;
      } else {
        this.heightSearch = false;
      }
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
    // 跳转到新增
    handlerAddClick() {
      this.$router.push({
        name: "AddMaintenance",
      });
    },
    // 编辑
    editMaintenance(row) {
      this.$router.push({
        name: "AddMaintenance",
        query: {
          id: row.id,
        },
      });
    },
    // 查看
    getMaintenanceDetail(id) {
      this.$router.push({
        name: "CheckMaintain",
        query: {
          id: id,
        },
      });
    },
    // 收票
    handlerChargeClick() {
      var data = this.multipleSelection;
      if (!data.length) {
        this.$message({
          type: "error",
          message: this.$t("pleaseSelectOrder"),
        });
        return;
      }
      let ids = data.map((el) => {
        return el.id;
      });
      this.$confirm(
        this.$t("confirmInvoiceRequired") + "?",
        this.$t("Notice"),
        {
          confirmButtonText: this.$t("Confirm"),
          cancelButtonText: this.$t("Cancel"),
          type: "warning",
        }
      )
        .then(() => {
          this.$api.putMaintenance(ids.join(",")).then((res) => {
            if (res.code === 200) {
              this.$message({
                message: this.$t("invoiceReceived") + "!",
                type: "success",
              });
            }
            this.getMaintenance();
          });
        })
        .catch(() => {});
    },
    // 批量删除
    handlerDeleteClick() {
      var data = this.multipleSelection;
      if (!data.length) {
        this.$message({
          type: "error",
          message: this.$t("pleaseSelectOrder"),
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
          this.$api.delMaintenance(ids.join(",")).then((res) => {
            if (res.code === 200) {
              // this.$message({
              //   message: "删除成功!",
              //   type: "success",
              // });
            }
            this.getMaintenance();
          });
        })
        .catch(() => {});
    },
    // 批量导出
    handlerExportClick() {
      this.$confirm(this.$t("AreSureWantExport"), this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      }).then(() => {
        // var data = this.multipleSelection;
        // if (!data.length) {
        //   this.$message({
        //     type: "error",
        //     message: this.$t("pleaseSelectOrder"),
        //   });
        //   return;
        // }
        // let ids = data.map((el) => {
        //   return el.id;
        // });
        this.$api.exportMaintenance(this.pageParams).then((res) => {
          if (!res) return;
          let iframe = window.frames["downExcel"];
          let href = res.data.path; //接口路径地址，返回数据类型为application/binary，后台控制显示信息，前端仅为下载功能
          iframe.location.href = href;
          this.getMaintenance();
        });
      });
    },
    // 列表
    getMaintenance() {
      this.$api.getMaintenance(this.pageParams).then((res) => {
        this.tableData = res.data.data;
        this.pageParams.total = res.data.meta.total;
      });
    },

    // 重置
    handleResetPageParams() {
      this.pageParams.car_no = ""; //车牌号
      this.pageParams.maintain_type = ""; //维保类型
      this.pageParams.is_ticket = ""; //收票状态
      // this.pageParams.begin_date = ""; //开始时间
      // this.pageParams.end_date = ""; //结束时间
      this.pageParams.maintain_factory = ""; //维修厂名称
      this.search_date = ""; //维修时间

      this.getMaintenance();
    },
    // 列表选中
    handleSelectionChange(val) {
      this.multipleSelection = val;
    },
  },
};
</script>
<style lang="less" scoped>
.maintenance-list {
  .box-card {
    margin-bottom: 10px;
    // padding-bottom: 10px;
  }
  .search {
    display: flex;
    justify-content: space-between;
  }
  .search-but {
    text-align: right;
    float: right;
    display: flex;
    gap: 5px;
    .but {
      margin-left: 0px;
      margin-bottom: 10px;
    }
  }
}
</style>
<style lang="less">
.maintenance-list {
  .conditional {
    .el-input__inner {
      border-radius: 0;
    }
  }
}
</style>
