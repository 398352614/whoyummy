<template>
  <!-- 延迟记录 -->
  <div class="delay-recording">
    <div class="search">
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
        clearable
        size="small"
        v-model="pageParams.line_name"
        @keyup.enter.native="handleChangePageParams"
        :placeholder="$t('pleaseEnterLineName')"
        class="search-bar"
      >
      </el-input>
      <el-select
        size="small"
        clearable
        v-model="pageParams.delay_type"
        @keyup.enter.native="handleChangePageParams"
        :placeholder="$t('selectDelayReason')"
        class="search-bar"
      >
        <el-option
          v-for="(item, index) in delayType"
          :key="index"
          :label="item.name"
          :value="item.id"
        ></el-option>
      </el-select>
      <el-input
        clearable
        size="small"
        v-model="pageParams.tour_no"
        @keyup.enter.native="handleChangePageParams"
        :placeholder="$t('PleasePickUpRouteNo')"
        class="search-bar"
      >
        <!-- <el-button
              @click="handleChangePageParams"
              slot="append"
              icon="el-icon-search"
            ></el-button> -->
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
      height="calc(100vh - 233px)"
      :data="tableData"
      border
      v-loading="global_loading()"
      element-loading-text="loading..."
      :cell-style="{ padding: '5px 0px' }"
    >
      <el-table-column
        :label="$t('Number')"
        type="index"
        width="50"
        align="center"
      >
      </el-table-column>
      <el-table-column
        prop="tour_no"
        :label="$t('Pickuplinecode')"
        align="center"
      >
      </el-table-column>
      <el-table-column prop="line_name" :label="$t('LineName')" align="center">
      </el-table-column>
      <el-table-column prop="driver_name" :label="$t('Driver')" align="center">
      </el-table-column>
      <el-table-column
        prop="execution_date"
        :label="$t('DistributeDate')"
        align="center"
      >
      </el-table-column>
      <el-table-column
        prop="created_at"
        :label="$t('delaySubmissionTime')"
        align="center"
        width="200"
      >
      </el-table-column>
      <el-table-column
        prop="delay_time_human"
        :label="$t('delayDuration') + '(min)'"
        align="center"
      >
        <template slot-scope="scope">
          {{ scope.row.delay_time | secondsToMinutes }}
        </template>
      </el-table-column>
      <el-table-column
        prop="delay_type_name"
        :label="$t('delayReason')"
        align="center"
      >
      </el-table-column>
    </el-table>
    <base-pagination
      ref="searchRef"
      :pageParams="pageParams"
      @search="getList"
      class="pagination-size"
    ></base-pagination>
  </div>
</template>

<script>
import BasePagination from "@/components/BasePagination/BasePagination";
import loading from "@/mixin/global_loading";

export default {
  name: "DelayRecording",
  mixins: [loading],
  components: {
    BasePagination,
  },
  data() {
    return {
      tableData: [],
      delayType: [],
      search_date: ["", ""],
      pageParams: {
        page: 1,
        per_page: 20,
        total: 0,
        tour_no: "",
        driver_id: "", // 司机ID
        delay_type: null,
        line_name: "",
        begin_date: "",
        end_date: "",
        keyword: "",
      },
      allDriverList: [],
    };
  },
  created() {
    this.getList();
    this.getDelayInit();
    this.getDrivers();
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
  filters: {
    secondsToMinutes(value) {
      // 秒转分
      return +value / 60;
    },
  },
  computed: {
    active_type() {
      return [
        {
          name: "",
          label: this.$t("All"),
        },
        {
          name: "1",
          label: this.$t("Unassigned"),
        },
        {
          name: "2",
          label: this.$t("Assigned"),
        },
        {
          name: "3",
          label: this.$t("Waitingfordelivery"),
        },
        {
          name: "4",
          label: this.$t("Distributing"),
        },
        {
          name: "5",
          label: this.$t("Delivered"),
        },
        {
          name: "6",
          label: this.$t("DeliveryCancelled"),
        },
      ];
    },
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
  methods: {
    getDelayInit() {
      this.$api.getDelayInit().then((res) => {
        this.delayType = res.data.tour_delay_list;
      });
    },
    getList() {
      this.$api.getDelay(this.pageParams).then((res) => {
        this.tableData = res.data.data;
        this.pageParams.total = res.data.meta.total;
        this.pageParams.page = res.data.meta.current_page;
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
      this.getList();
    },
  },
};
</script>
<style lang="less" scoped>
.delay-recording {
  padding: 20px 25px 10px;
  background-color: #fff;
  position: relative;
  .search {
    margin-bottom: 10px;
    .el-input__inner {
      max-width: 100%;
    }
  }
}
</style>
<style lang="less">
.el-input__inner {
  border-radius: 0 !important;
}
.el-dropdown-menu__item {
  color: #28d094 !important;
}
</style>
