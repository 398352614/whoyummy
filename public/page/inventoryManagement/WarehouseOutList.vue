<template>
  <!-- 出库管理 -->
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
      >
      </el-date-picker>
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
      <!-- 所属网点 -->
      <el-select
        size="small"
        clearable
        v-model="pageParams.warehouse_id"
        @keyup.enter.native="handleChangePageParams"
        :placeholder="$t('outboundOutlets')"
        class="search-bar"
      >
        <el-option
          v-for="(item, index) in warehouseList"
          :key="index"
          :label="item.name"
          :value="item.id"
        ></el-option>
      </el-select>
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
      height="calc(100vh - 235px)"
      :data="hasPerm('stock-out-log.index') ? tableData : []"
      v-loading="global_loading()"
      element-loading-text="loading..."
      border
      style="width: 100%"
      :cell-style="{ padding: '7px 0px' }"
    >
      <template slot="empty" v-if="!hasPerm('stock-out-log.index')">
        <div style="font-size: 18px">
          {{ $t("AdministratorActivate") }}
        </div>
      </template>
      <el-table-column type="index" align="center" width="50">
        <template slot="header">
          <span>{{ $t("Number") }}</span>
        </template>
      </el-table-column>
      <el-table-column
        prop="express_first_no"
        align="center"
        :label="$t('parcelNo')"
      >
      </el-table-column>
      <el-table-column prop="order_no" align="center" :label="$t('orderNo')">
      </el-table-column>
      <el-table-column
        prop="tracking_order_no"
        align="center"
        :label="$t('waybillNo')"
      >
      </el-table-column>
      <el-table-column
        prop="line_name"
        align="center"
        :label="$t('warehouseOutRoute')"
        width="180"
      >
      </el-table-column>
      <el-table-column
        prop="weight"
        align="center"
        width="130"
        :label="$t('parcelWeight')"
      >
      </el-table-column>
      <el-table-column
        prop="warehouse_name"
        align="center"
        :label="$t('outboundOutlets')"
        width="180"
      >
      </el-table-column>
      <el-table-column
        prop="create_date"
        align="center"
        :label="$t('deliveryTime')"
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
  name: "WarehouseOut",
  components: {
    BasePagination,
  },
  mixins: [loading],
  data() {
    return {
      tableData: [],
      warehouseList: [], //网点
      pageParams: {
        warehouse_id: "", //网点
        order_no: "",
        express_first_no: "",
        line_name: "",
        begin_date: "",
        end_date: "",
        keyword: "",
        total: 0,
        page: 1,
        per_page: 20,
      },
      search_date: ["", ""],
      lineList: [],
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
  created() {
    this.getStock();
    this.getDrivers();
    this.getWarehouse();
  },
  methods: {
    getWarehouse() {
      // 网点列表
      this.$api.getWarehouse().then((res) => {
        this.warehouseList = res.data.data;
      });
    },
    getStock() {
      // 列表
      this.$api.getStockOutLog(this.pageParams).then((res) => {
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
