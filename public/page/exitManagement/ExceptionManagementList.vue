<template>
  <!-- 异常管理 -->
  <div class="dispatch-order">
    <el-tabs v-model="tab" @tab-click="changeTab">
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
        <el-input
          size="small"
          clearable
          :placeholder="$t('pleaseEnterSiteNumber')"
          @keyup.enter.native="getOrder"
          v-model="pageParams.keyword"
          class="search-bar"
        >
        </el-input>
        <el-button
          size="small"
          @click="getOrder"
          type="primary"
          :loading="this.$store.state.config.button_loading"
          >{{ $t("Search") }}</el-button
        >
      </div>
      <el-tab-pane
        v-for="(item, index) in orderList"
        :label="item.name + ' ' + (item.num || '')"
        :name="item.name + ''"
        :key="index"
      >
        <el-table
          height="calc(100vh - 264px)"
          :data="hasPerm('batch-exception.index') ? tableData : []"
          v-loading="global_loading()"
          element-loading-text="loading..."
          border
          style="width: 100%"
          :cell-style="{ padding: '5px 0px' }"
        >
          <template slot="empty" v-if="!hasPerm('batch-exception.index')">
            <div style="font-size: 18px">
              {{ $t("AdministratorActivate") }}
            </div>
          </template>
          <el-table-column type="selection" align="center" width="50">
          </el-table-column>
          <el-table-column type="index" align="center" width="50">
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
                  <!-- 查看异常 -->
                  <el-tooltip
                    :content="$t('AdministratorActivate')"
                    placement="top"
                    :disabled="hasPerm('batch-exception.index') ? true : false"
                  >
                    <div
                      :style="
                        hasPerm('batch-exception.index')
                          ? ''
                          : 'cursor: not-allowed'
                      "
                    >
                      <el-dropdown-item
                        @click.native="viewExceptions(scope.row.id)"
                        :disabled="!hasPerm('batch-exception.index')"
                      >
                        <el-button
                          class="operation"
                          size="small"
                          type="text"
                          :disabled="!hasPerm('batch-exception.index')"
                          >{{ $t("ViewException") }}</el-button
                        >
                      </el-dropdown-item>
                    </div>
                  </el-tooltip>
                  <!-- 异常处理 -->
                  <el-tooltip
                    :content="$t('AdministratorActivate')"
                    placement="top"
                    :disabled="hasPerm('batch-exception.deal') ? true : false"
                  >
                    <div
                      :style="
                        hasPerm('batch-exception.deal')
                          ? ''
                          : 'cursor: not-allowed'
                      "
                    >
                      <el-dropdown-item
                        @click.native="openHandlingDialog(scope.row.id)"
                        :disabled="!hasPerm('batch-exception.deal')"
                      >
                        <el-button
                          :disabled="!hasPerm('batch-exception.deal')"
                          class="operation"
                          v-if="scope.row.status !== 2"
                          size="small"
                          type="text"
                          >{{ $t("ExceptionHandling") }}</el-button
                        >
                      </el-dropdown-item>
                    </div>
                  </el-tooltip>
                  <!-- <el-dropdown-item>
                    <el-button
                      class="operation"
                      @click="getOrderTrail(scope.row.order_no)"
                      size="small"
                      type="text"
                      >{{ $t("OpeationLog") }}</el-button
                    >
                  </el-dropdown-item> -->
                </el-dropdown-menu>
              </el-dropdown>
            </template>
          </el-table-column>
          <el-table-column
            prop="batch_no"
            align="center"
            :label="$t('SiteNumber')"
            width="160"
          >
          </el-table-column>
          <!-- <el-table-column
            prop="receiver"
            align="center"
            :label="$t('Receiver')"
            width="120"
          >
          </el-table-column> -->
          <el-table-column
            prop="status_name"
            align="center"
            :label="$t('Status')"
            width="160"
          >
          </el-table-column>
          <el-table-column
            prop="source"
            align="center"
            :label="$t('Source')"
            width="160"
          >
          </el-table-column>
          <el-table-column
            prop="created_at"
            align="center"
            width="160"
            :label="$t('CreationTime')"
          >
          </el-table-column>
          <el-table-column
            prop="driver_name"
            align="center"
            :label="$t('Creator')"
            width="160"
          >
          </el-table-column>
          <el-table-column
            prop="batch_exception_no"
            align="center"
            :label="$t('BNumber')"
            width="160"
          >
          </el-table-column>
          <el-table-column
            prop="stage_name"
            align="center"
            :label="$t('AbnormalStatus')"
            width="160"
          >
          </el-table-column>
          <el-table-column
            prop="type_name"
            align="center"
            :label="$t('AbnormalType')"
            width="160"
          >
          </el-table-column>
          <el-table-column
            prop="remark"
            align="center"
            :label="$t('AbnormalContent')"
            width="160"
          >
          </el-table-column>
          <el-table-column
            prop="deal_time"
            align="center"
            :label="$t('HandleTime')"
            width="160"
          >
          </el-table-column>
          <el-table-column
            prop="deal_name"
            align="center"
            :label="$t('Handler')"
            width="160"
          >
          </el-table-column>
        </el-table>
        <base-pagination
          :pageParams="pageParams"
          @search="getOrder"
          class="pagination-size"
        ></base-pagination>
      </el-tab-pane>
    </el-tabs>

    <el-dialog
      :title="$t('OpeationLog')"
      :visible.sync="show_trajectory"
      width="50%"
    >
      <el-timeline>
        <el-timeline-item
          v-for="(activity, index) in activities"
          :key="index"
          :icon="activity.icon"
          :type="activity.type"
          :color="activity.color"
          :size="activity.size"
          :timestamp="activity.timestamp"
        >
          {{ activity.content }}
        </el-timeline-item>
      </el-timeline>
    </el-dialog>

    <el-dialog
      :title="$t('ViewException')"
      :visible.sync="view_exceptions"
      @close="closeDialog"
    >
      <el-form :model="{}" label-position="right" label-width="150px">
        <el-form-item
          :label="$t('AbnormalStatus') + ':'"
          prop="origin_password"
        >
          {{ exceptionsDetails.stage_name }}
        </el-form-item>
        <el-form-item :label="$t('AbnormalType') + ':'" prop="new_password">
          {{ exceptionsDetails.type_name }}
        </el-form-item>
        <el-form-item :label="$t('AbnormalWay') + ':'" prop="new_password">
          {{ exceptionsDetails.deal_remark }}
        </el-form-item>
        <el-form-item
          :label="$t('AbnormalContent') + ':'"
          prop="new_confirm_password"
        >
          {{ exceptionsDetails.remark }}
        </el-form-item>
        <el-form-item :label="$t('Photo') + ':'" prop="new_confirm_password">
          <div style="width: 300px; height: 300px">
            <img :src="exceptionsDetails.picture" width="100%" height="100%" />
          </div>
        </el-form-item>
      </el-form>
    </el-dialog>

    <el-dialog
      :title="$t('ExceptionHandling')"
      :visible.sync="handling_exceptions"
      @close="closeDialog"
    >
      <el-form :model="{}" label-position="right" label-width="150px">
        <el-form-item
          :label="$t('AbnormalStatus') + ':'"
          prop="origin_password"
        >
          {{ exceptionsDetails.stage_name }}
        </el-form-item>
        <el-form-item :label="$t('AbnormalType') + ':'" prop="new_password">
          {{ exceptionsDetails.type_name }}
        </el-form-item>
        <el-form-item :label="$t('AbnormalWay') + ':'" prop="new_password">
          {{ exceptionsDetails.deal_remark }}
        </el-form-item>
        <el-form-item
          :label="$t('Description') + ':'"
          prop="new_confirm_password"
        >
          {{ exceptionsDetails.remark }}
        </el-form-item>
        <el-form-item :label="$t('Photo')" prop="new_confirm_password">
          <div style="width: 300px; height: 300px">
            <img :src="exceptionsDetails.picture" width="100%" height="100%" />
          </div>
        </el-form-item>
        <el-form-item
          :label="$t('ExceptionHandling') + ':'"
          prop="new_confirm_password"
        >
          <el-input
            type="textarea"
            :rows="4"
            v-model="exceptionsMsg"
            :placeholder="$t('PleaseEnterAtLeast5Characters')"
          ></el-input>
        </el-form-item>
      </el-form>
      <span slot="footer">
        <el-button type="primary" @click="handlingExceptions" class="submit">{{
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
  name: "exceptionManagement",
  components: {
    BasePagination,
  },
  mixins: [loading],
  data() {
    return {
      orderList: [
        { name: this.$t("All"), id: null, code: "", num: 0 },
        { name: this.$t("Unprocessed"), id: 1, code: "no_take", num: 0 },
        { name: this.$t("Processed"), id: 2, code: "assign", num: 0 },
      ],
      tab: this.$t("All"),
      tableData: [
        {
          address: "a",
        },
      ],
      exceptionsDetails: {},
      exceptionsId: null,
      exceptionsMsg: "",
      search_date: ["", ""],
      pageParams: {
        status: "",
        group_id: "",
        is_lock: "",
        begin_date: "",
        end_date: "",
        from_source: "",
        keyword: "",
        total: 0,
        page: 1,
        per_page: 20,
      },
      show_trajectory: false,
      activities: [],
      view_exceptions: false,
      handling_exceptions: false,
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
  created() {
    this.getOrder();
  },

  methods: {
    getOrder() {
      // 列表
      if (this.pageParams.keyword) {
        this.pageParams.page = 1;
      }
      this.$api
        .getBatchException(
          this.pageParams.status === 10
            ? { exception_label: 2 }
            : this.pageParams
        )
        .then((res) => {
          this.tableData = res.data.data;
          this.pageParams.total = res.data.meta.total;
        });
    },
    changeTab(tab) {
      // 切换tab
      this.orderList.forEach((val) => {
        if (val.name === tab.name) {
          this.pageParams.status = val.id;
          this.pageParams.page = 1;
          this.getOrder();
        }
      });
    },
    getOrderTrail(order) {
      // 列表
      this.activities = [];
      this.show_trajectory = true;
      this.$api.getOrderTrail({ order_no: order }).then((res) => {
        res.data.data.forEach((ele) => {
          this.activities.push({
            content: ele.content,
            timestamp: ele.created_at,
            color: "#0bbd87",
          });
        });
      });
    },
    getExceptionsInfo(id) {
      // 获得异常信息
      this.$api.getBatchExceptionDetails(id).then((res) => {
        this.exceptionsDetails = res.data;
      });
    },
    viewExceptions(id) {
      // 查看异常
      this.view_exceptions = true;
      this.getExceptionsInfo(id);
    },
    openHandlingDialog(id) {
      // 打开处理异常弹窗
      this.handling_exceptions = true;
      this.exceptionsId = id;
      this.getExceptionsInfo(id);
    },
    handlingExceptions() {
      // 处理异常
      if (!this.exceptionsMsg) return;
      this.$api
        .putBatchException({
          id: this.exceptionsId,
          deal_remark: this.exceptionsMsg,
        })
        .then((res) => {
          if (res.code === 200) {
            this.getOrder();
            this.handling_exceptions = false;
          }
        });
    },
    closeDialog() {
      this.exceptionsDetails = {};
      this.exceptionsId = null;
      this.exceptionsMsg = "";
    },
  },
};
</script>

<style lang="less" scoped>
.dispatch-order {
  padding: 0px 25px 5px;
  background: #ffffff;
  border: 1px solid rgba(228, 235, 241, 1);
  .conditional {
    padding: 0px 0 10px;
    .search {
      .el-input-group__append {
        .el-button .el-button--primary {
          background: #28d094;
          color: #ffffff;
        }
      }
    }
  }
  img {
    width: 100%;
  }
}
</style>
<style lang="less">
.dispatch-order {
  .conditional {
    .search {
      .el-button {
        background: #28d094 !important;
        color: #ffffff !important;
        border-radius: 0;
      }
    }
    .el-input__inner {
      border-radius: 0;
    }
  }
  .el-date-editor {
    width: 100%;
  }
}
</style>
<style lang="less">
// 危!危!危!
.el-picker-panel {
  border-radius: 0;
}
</style>
