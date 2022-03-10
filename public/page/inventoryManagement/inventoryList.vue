<template>
  <!-- 库存列表 -->
  <div class="inventory">
    <div class="conditional">
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
      <!-- 请选择线路 -->
      <el-select
        size="small"
        clearable
        v-model="pageParams.line_name"
        @keyup.enter.native="handleChangePageParams"
        :placeholder="$t('PleaseSelectTheRoute')"
        class="search-bar"
      >
        <el-option
          v-for="(item, index) in lineList"
          :key="index"
          :label="item.name"
          :value="item.name"
        ></el-option>
      </el-select>
      <!-- 超过最迟派送日期 -->
      <el-select
        size="small"
        clearable
        v-model="pageParams.expiration_status"
        @keyup.enter.native="handleChangePageParams"
        :placeholder="$t('ExceededTheLatest')"
        class="search-bar"
      >
        <el-option
          v-for="(item, index) in expirationStatusList"
          :key="index"
          :label="item.name"
          :value="item.id"
        ></el-option>
      </el-select>
      <!-- 所属网点 -->
      <el-select
        size="small"
        clearable
        v-model="pageParams.warehouse_id"
        @keyup.enter.native="handleChangePageParams"
        :placeholder="$t('Network')"
        class="search-bar"
      >
        <el-option
          v-for="(item, index) in warehouseList"
          :key="index"
          :label="item.name"
          :value="item.id"
        ></el-option>
      </el-select>
      <!-- 请输入包裹单号 -->
      <el-input
        @keyup.enter.native="handleChangePageParams"
        v-model="pageParams.express_first_no"
        clearable
        :placeholder="$t('PleaseEnterParcelNo')"
        size="small"
        class="search-bar"
      >
      </el-input>
      <el-input
        @keyup.enter.native="handleChangePageParams"
        v-model="pageParams.order_no"
        clearable
        :placeholder="$t('PleaseEnterOrderNo')"
        size="small"
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
    <el-table
      :height="heightSearch ? 'calc(100vh - 276px)' : 'calc(100vh - 244px)'"
      :data="hasPerm('stock.index') ? tableData : []"
      v-loading="global_loading()"
      element-loading-text="loading..."
      border
      style="width: 100%"
      :cell-style="{ padding: '7px 0px' }"
    >
      <template slot="empty" v-if="!hasPerm('stock.index')">
        <div style="font-size: 18px">
          {{ $t("AdministratorActivate") }}
        </div>
      </template>
      <el-table-column type="index" align="center" width="50">
        <template slot="header">
          <span>{{ $t("Number") }}</span>
        </template>
      </el-table-column>
      <!-- <el-table-column
        prop="date"
        align="center"
        :label="$t('Operation')"
        width="100">
        <template slot-scope="scope">
            <el-dropdown size="mini" trigger="hover" placement="bottom">
                <el-button size="mini" type="primary">
                    {{$t('Operation')}}
                </el-button>
                <el-dropdown-menu slot="dropdown">
                    <el-dropdown-item>
                        <el-button class="operation" @click="getReportDetails(scope.row.id)" size="small" type="text">{{$t('Details')}}</el-button>
                    </el-dropdown-item>
                    <el-dropdown-item>
                        <el-button class="operation" @click="getTourExcel(scope.row.id)" size="small" type="text">{{$t('导出取件报告')}}</el-button>
                    </el-dropdown-item>
                </el-dropdown-menu>
            </el-dropdown>
        </template>
      </el-table-column> -->
      <el-table-column
        prop="express_first_no"
        align="center"
        :label="$t('parcelNo')"
        width="200"
      >
      </el-table-column>
      <!-- 跳转订单号 -->
      <el-table-column
        prop="order_no"
        align="center"
        :label="$t('orderNo')"
        width="200"
      >
        <template slot-scope="scope">
          <el-link
            class="link"
            :underline="false"
            @click="
              hasPerm('order.index')
                ? $router.push({
                    name: 'OrderList',
                    params: { order_no: scope.row.order_no },
                  })
                : ''
            "
            >{{ scope.row.order_no }}</el-link
          >
        </template>
      </el-table-column>
      <el-table-column
        prop="tracking_order_no"
        align="center"
        :label="$t('waybillNo')"
        width="200"
      >
      </el-table-column>
      <!-- 所属网点 -->
      <el-table-column
        header-align="center"
        align="center"
        prop="warehouse_name"
        :label="$t('Network')"
        width="170"
      >
      </el-table-column>
      <el-table-column
        prop="line_name"
        align="center"
        :label="$t('warehouseOutRoute')"
        width="200"
      >
      </el-table-column>
      <el-table-column
        prop="weight"
        align="center"
        width="150"
        :label="$t('parcelWeight')"
      >
      </el-table-column>
      <el-table-column
        prop="execution_date"
        align="center"
        :label="$t('estimatedWarehouseDate')"
        width="250"
      >
      </el-table-column>
      <el-table-column
        prop="expiration_status_name"
        align="center"
        :label="$t('exceedTheLatestOutDate')"
        width="200"
      >
      </el-table-column>
      <el-table-column
        prop="create_date"
        align="center"
        :label="$t('warehousingDate')"
        width="150"
      >
      </el-table-column>
    </el-table>
    <base-pagination
      :pageParams="pageParams"
      @search="getStock"
      class="pagination-size"
    ></base-pagination>
  </div>
</template>

<script>
import BasePagination from "@/components/BasePagination/BasePagination";
import loading from "@/mixin/global_loading";

export default {
  name: "InventoryList",
  components: {
    BasePagination,
  },
  mixins: [loading],
  data() {
    return {
      heightSearch: false,
      tableData: [],
      warehouseList: [], //网点
      pageParams: {
        warehouse_id: "", //网点
        line_name: "",
        express_first_no: "",
        order_no: "",
        begin_date: "",
        end_date: "",
        expiration_status: "",
        total: 0,
        page: 1,
        per_page: 20,
      },

      search_date: ["", ""],
      lineList: [],
      expirationStatusList: [],
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
    if (this.$route.query.expiration_status) {
      // 接收路由参数
      this.$set(
        this.pageParams,
        "expiration_status",
        Number(this.$route.query.expiration_status)
      );
      this.getStock();
    }
  },
  created() {
    this.searchFormWidth();
    this.getStock();
    this.getDrivers();
    this.getWarehouse();
    this.getexpirationStatusList();
    // if (this.$route.query.expiration_status) {
    //   // 接收路由参数
    //   this.$set(
    //     this.pageParams,
    //     "expiration_status",
    //     Number(this.$route.query.expiration_status)
    //   );
    // }
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
  methods: {
    // 获取窗口的宽度,调整表格高度
    searchFormWidth() {
      let w = window.innerWidth;
      if (w <= 1570) {
        this.heightSearch = true;
      } else {
        this.heightSearch = false;
      }
    },
    getWarehouse() {
      // 网点列表
      this.$api.getWarehouse().then((res) => {
        this.warehouseList = res.data.data;
      });
    },
    // 获取超过最迟派送日期下拉框
    getexpirationStatusList() {
      this.$api.getDictionary().then((res) => {
        this.expirationStatusList = res.data.expirationStatusList;
      });
    },
    getStock() {
      // 库存列表
      this.$api.getStock(this.pageParams).then((res) => {
        this.tableData = res.data.data;
        this.pageParams.total = res.data.meta.total;
      });
    },

    handleChangePageParams() {
      this.pageParams.page = 1;
      this.getStock();
    },

    getDrivers() {
      this.$api.getLineListOfOrderPublic({ per_page: 200 }).then((res) => {
        this.lineList = res.data;
      });
    },

    getReportDetails(id) {
      // 跳转任务报告详情页面
      this.$router.push({ name: "TaskReportDetails", query: { id } });
    },
  },
};
</script>
<style lang="less" scoped>
.inventory {
  padding: 20px 25px 10px;
  background: #ffffff;
  border: 1px solid rgba(228, 235, 241, 1);
  .conditional {
    padding: 0px 0 10px;
  }
}
</style>
<style lang="less">
.inventory {
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
}
</style>
