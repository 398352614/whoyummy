<template>
  <!-- 任务报告 -->
  <div class="task-report">
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
        v-model="pageParams.driver_id"
        @keyup.enter.native="handleChangePageParams"
        :placeholder="$t('PleaseSelectDriver')"
        class="search-bar"
      >
        <el-option
          v-for="(item, index) in allDriverList"
          :key="index"
          :label="item.fullname"
          :value="item.id"
        ></el-option>
      </el-select>
      <el-input
        @keyup.enter.native="handleChangePageParams"
        v-model="pageParams.tour_no"
        clearable
        :placeholder="$t('PleasePickUpRouteNo')"
        size="small"
        class="search-bar"
      ></el-input>
      <el-input
        @keyup.enter.native="handleChangePageParams"
        v-model="pageParams.line_name"
        clearable
        :placeholder="$t('pleaseEnterLineName')"
        size="small"
        class="search-bar"
      ></el-input>
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
      :data="hasPerm('report.index') ? tableData : []"
      v-loading="global_loading()"
      element-loading-text="loading..."
      border
      style="width: 100%"
      :cell-style="{ padding: '5px 0px' }"
    >
      <template slot="empty" v-if="!hasPerm('report.index')">
        <div style="font-size: 18px">
          {{ $t("AdministratorActivate") }}
        </div>
      </template>
      <el-table-column type="index" width="50" align="center">
        <template slot="header">
          <span>{{ $t("Number") }}</span>
        </template>
      </el-table-column>
      <el-table-column
        prop="date"
        align="center"
        :label="$t('Operation')"
        width="120"
      >
        <template slot-scope="scope">
          <el-dropdown size="mini" trigger="hover" placement="bottom">
            <el-button size="mini" type="primary" plain class="operation">
              {{ $t("Operation") }}
              <i class="el-icon-arrow-down el-icon--right"></i>
            </el-button>
            <el-dropdown-menu slot="dropdown">
              <!-- 详情 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('report.index') ? true : false"
              >
                <div
                  :style="hasPerm('report.index') ? '' : 'cursor: not-allowed'"
                >
                  <el-dropdown-item
                    @click.native="getReportDetails(scope.row.id)"
                    :disabled="!hasPerm('report.index')"
                  >
                    <el-button
                      :disabled="!hasPerm('report.index')"
                      class="operation"
                      size="small"
                      type="text"
                      >{{ $t("Details") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 导出取件报告 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('report.report-excel') ? true : false"
              >
                <div
                  :style="
                    hasPerm('report.report-excel') ? '' : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    @click.native="getTourExcel(scope.row.id)"
                    :disabled="!hasPerm('report.report-excel')"
                  >
                    <el-button
                      :disabled="!hasPerm('report.report-excel')"
                      class="operation"
                      size="small"
                      type="text"
                      >{{ $t("exportPickUpReport") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
            </el-dropdown-menu>
          </el-dropdown>
        </template>
      </el-table-column>
      <el-table-column
        prop="tour_no"
        align="center"
        :label="$t('LineNumber')"
        width="150"
      >
        <template slot-scope="scope">
          <el-link
            class="link"
            :underline="false"
            @click="
              hasPerm('tracking-order.index')
                ? $router.push({
                    name: 'WaybillManagement',
                    params: { tour_no: scope.row.tour_no },
                  })
                : ''
            "
            >{{ scope.row.tour_no }}</el-link
          >
        </template>
      </el-table-column>
      <el-table-column
        prop="expect_batch_count"
        align="center"
        :label="$t('Stops')"
      >
      </el-table-column>
      <el-table-column
        prop="line_name"
        align="center"
        :label="$t('LineName')"
        width="150"
      >
      </el-table-column>
      <el-table-column
        prop="driver_name"
        align="center"
        :label="$t('Driver')"
        width="150"
      >
      </el-table-column>
      <el-table-column
        prop="execution_date"
        align="center"
        :label="$t('DistributeDate')"
        width="180"
      >
      </el-table-column>
      <el-table-column
        prop="actual_pickup_quantity"
        align="center"
        width="200"
        :label="$t('PickupQty')"
      >
      </el-table-column>
      <el-table-column
        prop="actual_pie_quantity"
        align="center"
        :label="$t('DeliveryQty')"
        width="200"
      >
      </el-table-column>
      <el-table-column
        prop="cancel_batch_count"
        align="center"
        :label="$t('NumberOfDispatchFailures')"
        width="200"
      >
        <template slot-scope="scope">
          <span
            :style="scope.row.cancel_batch_count > 0 ? 'color: #e89e42' : ''"
          >
            {{ scope.row.cancel_batch_count }}
          </span>
        </template>
      </el-table-column>
      <el-table-column
        prop="receiver_house_number"
        align="center"
        width="150"
        :label="$t('Completeness')"
      >
        <template slot-scope="scope">
          <el-progress
            :percentage="
              scope.row.actual_batch_count
                | progress(scope.row.expect_batch_count)
            "
            :color="scope.row.cancel_batch_count > 0 ? '#e89e42' : ''"
          ></el-progress>
        </template>
      </el-table-column>
    </el-table>
    <base-pagination
      :pageParams="pageParams"
      @search="getReport"
      class="pagination-size"
    ></base-pagination>
    <iframe frameborder="0" name="downExcel" style="display: none"></iframe>
  </div>
</template>

<script>
import BasePagination from "@/components/BasePagination/BasePagination";
import loading from "@/mixin/global_loading";

export default {
  name: "taskReport",
  components: {
    BasePagination,
  },
  mixins: [loading],
  data() {
    return {
      tableData: [],
      pageParams: {
        line_name: "",
        tour_no: "",
        status: "",
        group_id: "",
        driver_id: "",
        is_lock: "",
        begin_date: "",
        end_date: "",
        from_source: "",
        keyword: "",
        total: 0,
        page: 1,
        per_page: 20,
      },
      search_date: ["", ""],
      allDriverList: [],
    };
  },
  filters: {
    progress(value1, value2) {
      if (value1 || value2) {
        return Math.round((value1 / value2) * 100);
      }
      return 0;
    },
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
  created() {
    this.getReport();
    this.getDrivers();
  },
  methods: {
    getReport() {
      // 列表
      this.$api.getReport(this.pageParams).then((res) => {
        this.tableData = res.data.data;
        this.pageParams.total = res.data.meta.total;
      });
    },

    getDrivers() {
      this.$api
        .getDriversPublic({
          page: 1,
          per_page: 200,
        })
        .then((res) => {
          this.allDriverList = res.data.data;
        });
    },

    handleChangePageParams() {
      this.pageParams.page = 1;
      this.getReport();
    },
    getReportDetails(id) {
      // 跳转任务报告详情页面
      this.$router.push({ name: "TaskReportDetails", query: { id } });
    },

    getTourExcel(id) {
      // 取件报告导出
      this.$api.getTourExcel(id).then((res) => {
        let iframe = window.frames["downExcel"];
        let href = res.data.path; //接口路径地址，返回数据类型为application/binary，后台控制显示信息，前端仅为下载功能
        iframe.location.href = href;
      });
    },
  },
};
</script>
<style lang="less" scoped>
.task-report {
  padding: 20px 25px 10px;
  background: #ffffff;
  border: 1px solid rgba(228, 235, 241, 1);
  .conditional {
    padding: 0px 0 10px;
  }
}
</style>
<style lang="less">
.task-report {
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
