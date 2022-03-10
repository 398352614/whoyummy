<template>
  <!-- 站点列表 -->
  <div class="content_box">
    <el-tabs v-model="pageParams.status" @tab-click="handlerTabClick">
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
          @keyup.enter.native="selectChange"
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
          v-model="pageParams.tour_no"
          @keyup.enter.native="selectChange"
          :placeholder="$t('PleasePickUpRouteNo')"
          class="search-bar"
        >
        </el-input>
        <el-input
          clearable
          size="small"
          v-model="pageParams.keyword"
          @keyup.enter.native="selectChange"
          :placeholder="$t('SiteCode')"
          class="search-bar"
        >
        </el-input>
        <el-button
          size="small"
          @click="selectChange"
          :loading="this.$store.state.config.button_loading"
          type="primary"
          >{{ $t("Search") }}</el-button
        >
      </div>
      <el-tab-pane
        v-for="item in active_type"
        :key="item.label"
        :label="item.label"
        :name="item.name"
      >
        <el-table
          height="calc(100vh - 262px)"
          :data="hasPerm('batch.index') ? list : []"
          border
          v-loading="global_loading()"
          element-loading-text="loading..."
          :cell-style="{ padding: '5px 0px' }"
        >
          <template slot="empty" v-if="!hasPerm('batch.index')">
            <div style="font-size: 18px">
              {{ $t("AdministratorActivate") }}
            </div>
          </template>
          <el-table-column
            :label="$t('Number')"
            type="index"
            width="50"
            align="center"
          >
          </el-table-column>
          <el-table-column :label="$t('Operation')" align="center" width="120">
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
                    :disabled="hasPerm('batch.index') ? true : false"
                  >
                    <div
                      :style="
                        hasPerm('batch.index') ? '' : 'cursor: not-allowed'
                      "
                    >
                      <el-dropdown-item
                        :disabled="!hasPerm('batch.index')"
                        @click.native="toStationRecord(scope.row)"
                      >
                        <el-button
                          class="operation"
                          size="small"
                          type="text"
                          :disabled="!hasPerm('batch.index')"
                          >{{ $t("Details") }}</el-button
                        >
                      </el-dropdown-item>
                    </div>
                  </el-tooltip>
                  <!-- 更换路线 -->
                  <el-tooltip
                    :content="$t('AdministratorActivate')"
                    placement="top"
                    :disabled="hasPerm('batch.assign-tour') ? true : false"
                  >
                    <div
                      :style="
                        hasPerm('batch.assign-tour')
                          ? ''
                          : 'cursor: not-allowed'
                      "
                    >
                      <el-dropdown-item
                        :disabled="!hasPerm('batch.assign-tour')"
                        @click.native="replaceLine(scope.row)"
                        v-if="scope.row.status === 1 || scope.row.status === 2"
                      >
                        <el-button
                          class="operation"
                          size="small"
                          type="text"
                          :disabled="!hasPerm('batch.assign-tour')"
                          >{{ $t("Changeroute") }}</el-button
                        >
                      </el-dropdown-item>
                    </div>
                  </el-tooltip>
                  <!-- 取消取派 -->
                  <el-tooltip
                    :content="$t('AdministratorActivate')"
                    placement="top"
                    :disabled="hasPerm('batch.cancel') ? true : false"
                  >
                    <div
                      :style="
                        hasPerm('batch.cancel') ? '' : 'cursor: not-allowed'
                      "
                    >
                      <el-dropdown-item
                        v-if="
                          scope.row.status === 1 ||
                          scope.row.status === 2 ||
                          scope.row.status === 3 ||
                          scope.row.status === 4
                        "
                        @click.native="onSubmitExpress(scope.row)"
                        :disabled="!hasPerm('batch.cancel')"
                      >
                        <el-button
                          class="operation"
                          size="small"
                          type="text"
                          :disabled="!hasPerm('batch.cancel')"
                          >{{ $t("CancelDistribution") }}</el-button
                        >
                      </el-dropdown-item>
                    </div>
                  </el-tooltip>
                  <!-- 移除线路 -->
                  <el-tooltip
                    :content="$t('AdministratorActivate')"
                    placement="top"
                    :disabled="hasPerm('batch.remove') ? true : false"
                  >
                    <div
                      :style="
                        hasPerm('batch.remove') ? '' : 'cursor: not-allowed'
                      "
                    >
                      <el-dropdown-item
                        :disabled="!hasPerm('batch.remove')"
                        v-if="scope.row.status === 1 || scope.row.status === 2"
                        @click.native="delStation(scope.row)"
                      >
                        <el-button
                          class="operation"
                          size="small"
                          type="text"
                          :disabled="!hasPerm('batch.remove')"
                          >{{ $t("deleteStation") }}</el-button
                        >
                      </el-dropdown-item>
                    </div>
                  </el-tooltip>
                </el-dropdown-menu>
              </el-dropdown>
            </template>
          </el-table-column>
          <el-table-column
            prop="batch_no"
            :label="$t('SiteCode')"
            align="center"
          >
            <template slot-scope="scope">
              <el-link
                class="link"
                :underline="false"
                @click="
                  hasPerm('tracking-order.index')
                    ? $router.push({
                        name: 'WaybillManagement',
                        params: { batch_no: scope.row.batch_no },
                      })
                    : {}
                "
                >{{ scope.row.batch_no }}</el-link
              >
            </template>
          </el-table-column>
          <el-table-column
            prop="tour_no"
            :label="$t('Pickuplinecode')"
            align="center"
            width="160"
          >
          </el-table-column>
          <el-table-column
            prop="line_name"
            :label="$t('LineName')"
            align="center"
            width="160"
          >
          </el-table-column>
          <el-table-column
            prop="merchant_id_name"
            :label="$t('ownedBySites')"
            align="center"
            width="160"
          >
          </el-table-column>
          <el-table-column
            prop="execution_date"
            :label="$t('DistributeDate')"
            align="center"
            width="150"
          >
          </el-table-column>
          <el-table-column
            :label="$t('PickupStatus')"
            align="center"
            width="150"
          >
            <template slot-scope="scope">
              {{ scope.row.status_name }}
            </template>
          </el-table-column>
          <el-table-column
            prop="driver_name"
            :label="$t('PickupDriver')"
            align="center"
            width="150"
          >
          </el-table-column>
          <el-table-column
            prop="car_no"
            :label="$t('PickupTruck')"
            align="center"
            width="150"
          >
          </el-table-column>
          <el-table-column
            :label="$t('EstimatedActualPickupQty')"
            align="center"
            width="250"
          >
            <template slot-scope="scope">
              {{ scope.row.expect_pickup_quantity }}/{{
                scope.row.actual_pickup_quantity
              }}
            </template>
          </el-table-column>
          <el-table-column
            :label="$t('EstimatedActualDeliveryQty')"
            align="center"
            width="250"
          >
            <template slot-scope="scope">
              {{ scope.row.expect_pie_quantity }}/{{
                scope.row.actual_pie_quantity
              }}
            </template>
          </el-table-column>
          <el-table-column
            prop="created_at"
            :label="$t('CreationTime')"
            align="center"
            width="160"
          >
          </el-table-column>
        </el-table>
      </el-tab-pane>
      <base-pagination
        ref="searchRef"
        :pageParams="pageParams"
        @search="getList"
        class="pagination-size"
      ></base-pagination>
    </el-tabs>

    <!-- 更换路线 -->
    <el-dialog
      :title="$t('Changeroute')"
      width="60%"
      @close="lineClose"
      :visible.sync="replaceLineDlg"
    >
      <el-form
        ref="line_form"
        :model="line_form"
        :rules="rules"
        label-width="160px"
      >
        <el-form-item :label="$t('date')" prop="execution_date">
          <el-date-picker
            size="small"
            @change="getLineList"
            v-model="line_form.execution_date"
            value-format="yyyy-MM-dd"
            :placeholder="$t('Date')"
            type="date"
            :picker-options="pickerOptions"
          >
          </el-date-picker>
        </el-form-item>
        <el-form-item
          :label="$t('routeOptions')"
          v-if="line_form.execution_date"
        >
          <el-radio-group v-model="line_form.line_id">
            <el-radio
              v-for="(item, index) in options"
              :key="index"
              :label="item.id"
              @change="changeTourNo"
              >{{ item.name }}</el-radio
            >
          </el-radio-group>
        </el-form-item>
        <el-form-item label=" " v-if="line_form.line_id && allLine.length">
          <el-select
            v-model="line_form.tour_no"
            :placeholder="$t('pleaseSelectPickupRoute')"
          >
            <el-option
              v-for="(item, index) in allLine"
              :key="index"
              :label="item.tour_no"
              :value="item.tour_no"
            ></el-option>
          </el-select>
        </el-form-item>
        <el-form-item
          :label="$t('createLineType')"
          v-if="line_form.line_id && !allLine.length && merchant_id !== 0"
        >
          <el-radio-group v-model="line_form.is_alone">
            <el-radio label="1">{{ $t("independentLine") }}</el-radio>
            <el-radio label="2">{{ $t("mixedLine") }}</el-radio>
          </el-radio-group>
        </el-form-item>
      </el-form>
      <el-row>
        <el-col :span="2" :offset="17">
          <el-button @click="replaceLineDlg = false">{{
            $t("Cancel")
          }}</el-button>
        </el-col>
        <el-col :span="2" :offset="1">
          <el-button
            @click="onSubmitReplaceLine"
            :disabled="checkLineForm"
            type="primary"
            >{{ $t("Submit") }}</el-button
          >
        </el-col>
      </el-row>
    </el-dialog>
    <!-- 更换路线 -->
  </div>
</template>

<script>
import BasePagination from "@/components/BasePagination/BasePagination";
import baseApi from "@/api/axios/base_api";
import loading from "@/mixin/global_loading";

export default {
  name: "stationList",
  mixins: [loading],
  components: {
    BasePagination,
  },
  data() {
    return {
      list: [],
      search_date: ["", ""],
      replaceLineDlg: false,
      line_form: {
        id: "",
        execution_date: "",
        tour_no: "",
        line_id: null,
        is_alone: null,
      },
      allDriverList: [],
      merchant_id: null, // 是否多商家
      options: [],
      allLine: [], // 所有线路
      pageParams: {
        page: 1,
        per_page: 20,
        total: 0,
        tour_no: "",
        status: "" || this.$route.query.status,
        driver_id: "", // 司机ID
        begin_date: "",
        end_date: "",
        keyword: "" || this.$route.params.id,
      },
      tourDate: [],
      nowDate: Date.now() - 24 * 60 * 60 * 1000,
      rules: {
        execution_date: [
          { required: true, message: this.$t("Required"), trigger: "blur" },
        ],
      },
    };
  },
  activated() {
    if (this.$route.params.id) {
      this.pageParams.keyword = this.$route.params.id;
      this.getList();
    }
  },
  created() {
    this.getList();
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
    pickerOptions() {
      return {
        disabledDate(time) {
          return time.getTime() < Date.now() - 8.64e7;
        },
      };
    },
    checkLineForm() {
      if (this.line_form.execution_date && this.line_form.line_id) {
        return false;
      } else {
        return true;
      }
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
    delStation(row) {
      this.$api.stationRemove(row.id).then((res) => {
        if (!res) return;
        this.getList();
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

    // 更换线路功能
    // 更换线路功能--打开弹窗
    replaceLine(row) {
      this.replaceLineDlg = !this.replaceLineDlg;
      this.line_form.id = row.id;
      this.merchant_id = row.merchant_id;
    },

    // 更换线路功能--关闭弹窗
    lineClose() {
      this.$refs.line_form.resetFields();
      this.line_form.execution_date = null;
      this.line_form.line_id = null;
      this.line_form.tour_no = null;
      this.options = [];
    },

    // 更换线路功能--改变线路id
    changeTourNo(val) {
      this.line_form.tour_no = null;
      this.$api
        .getListJoinByLineId({
          line_id: val,
          execution_date: this.line_form.execution_date,
        })
        .then((res) => {
          this.allLine = res.data;
        });
    },

    // 更换线路功能--获取可选日期
    // getLineDate(data) {
    //   this.$api.getLineDate(data).then((res) => {
    //     this.tourDate = res.data;
    //   });
    // },

    // 更换线路功能--选择日期
    getLineList() {
      this.line_form.line_id = null;
      this.line_form.tour_no = null;
      this.allLine = [];
      this.$api
        .getListByDate({
          date: this.line_form.execution_date,
        })
        .then((res) => {
          if (!res) return false;
          this.options = res.data;
        });
    },

    // 更换线路功能--提交表单
    onSubmitReplaceLine() {
      this.$refs.line_form.validate((valid) => {
        if (!valid) return;
        this.$api.replaceLine(this.line_form.id, this.line_form).then((res) => {
          if (!res) return;
          this.getList();
          this.replaceLineDlg = !this.replaceLineDlg;
        });
      });
    },

    // 取消取派
    onSubmitExpress(row) {
      this.$confirm(this.$t("ConfirmToCancel") + "?", this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      })
        .then(() => {
          this.$api.cancelBatch(row.id).then((res) => {
            if (!res) return;
            this.getList();
          });
        })
        .catch(() => {});
    },

    selectChange() {
      if (this.pageParams.keyword || this.pageParams.driver_id) {
        this.pageParams.page = 1;
      }
      this.getList();
    },
    handlerTabClick() {
      this.$route.query.page = 1;
      this.pageParams.page = 1;
      this.$refs.searchRef.onChangeValue("status", this.pageParams.status);
    },
    toStationRecord(row) {
      this.$router.push({
        name: "stationDetail",
        query: {
          id: row.id,
        },
      });
    },
    getList() {
      this.$api.getStationList(this.pageParams).then((res) => {
        this.list = res.data.data;
        this.pageParams.total = res.data.meta.total;
        this.pageParams.page = res.data.meta.current_page;
      });
    },
  },
};
</script>
<style lang="less" scoped>
.content_box {
  padding: 0px 25px 5px;
  background-color: #fff;
  position: relative;
  .el-tabs__content {
    .search {
      margin: 0 0 10px 0;
    }
  }
  .el-tabs__nav-scroll {
    padding-left: 25px;
    box-sizing: border-box;
    height: 62px;
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
