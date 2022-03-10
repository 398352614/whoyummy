<template>
  <!-- 入库异常处理 -->
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
      <el-input
        @keyup.enter.native="handleChangePageParams"
        v-model="pageParams.keyword"
        clearable
        :placeholder="$t('pleaseEnterparcelOrder')"
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
      :data="hasPerm('stock-exception.index') ? tableData : []"
      v-loading="global_loading()"
      element-loading-text="loading..."
      border
      style="width: 100%"
      :cell-style="{ padding: '5px 0px' }"
    >
      <template slot="empty" v-if="!hasPerm('stock-exception.index')">
        <div style="font-size: 18px">
          {{ $t("AdministratorActivate") }}
        </div>
      </template>
      <el-table-column type="index" align="center" width="50">
        <template slot="header">
          <span>{{ $t("Number") }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" :label="$t('Operation')">
        <template slot-scope="scope">
          <el-dropdown size="mini" trigger="hover" placement="bottom">
            <el-button size="mini" type="primary" plain class="operation">
              {{ $t("Operation") }}
              <i class="el-icon-arrow-down el-icon--right"></i>
            </el-button>
            <el-dropdown-menu slot="dropdown" v-if="scope.row.status == 1">
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('stock-exception.deal') ? true : false"
              >
                <div
                  :style="
                    hasPerm('stock-exception.deal') ? '' : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    @click.native="openDialog(scope.row.id)"
                    v-if="scope.row.status == 1"
                    :disabled="!hasPerm('stock-exception.deal')"
                  >
                    <el-button
                      size="small"
                      type="text"
                      :disabled="!hasPerm('stock-exception.deal')"
                      >{{ $t("review") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
            </el-dropdown-menu>
          </el-dropdown>
        </template>
      </el-table-column>
      <el-table-column
        prop="status_name"
        align="center"
        :label="$t('reviewStatus')"
      >
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
        prop="created_at"
        align="center"
        :label="$t('submissionTime')"
      >
      </el-table-column>
      <el-table-column
        prop="remark"
        align="center"
        :label="$t('exceptionReason')"
      >
      </el-table-column>
      <el-table-column
        prop="deal_remark"
        align="center"
        width="130"
        :label="$t('handledResults')"
      >
      </el-table-column>
    </el-table>
    <base-pagination
      :pageParams="pageParams"
      @search="getStock"
      class="pagination-size"
    ></base-pagination>

    <!-- 添加规则 -->
    <el-dialog title="" :visible.sync="show_Dialog" @close="clearForm">
      <div style="text-align: center">
        <p style="font-size: 20px">
          {{ $t("whetherReviewAbnormalPackagesNormal") }}
        </p>
        <el-radio-group v-model="radio">
          <el-radio :label="2">{{ $t("reviewedSuccessfully") }}</el-radio>
          <el-radio :label="3">{{ $t("reviewFailed") }}</el-radio>
        </el-radio-group>
      </div>
      <span slot="footer">
        <div style="text-align: center">
          <el-button @click="show_Dialog = false" class="cancel">{{
            $t("Cancel")
          }}</el-button>
          <el-button type="primary" @click="trial()" class="submit">{{
            $t("Confirm")
          }}</el-button>
        </div>
      </span>
    </el-dialog>
    <!-- 添加规则 结束-->
  </div>
</template>

<script>
import BasePagination from "@/components/BasePagination/BasePagination";
import loading from "@/mixin/global_loading";

export default {
  name: "WarehousingException",
  components: {
    BasePagination,
  },
  mixins: [loading],
  data() {
    return {
      tableData: [],
      pageParams: {
        begin_date: "",
        end_date: "",
        keyword: "",
        total: 0,
        page: 1,
        per_page: 20,
      },
      search_date: ["", ""],
      show_Dialog: false,
      radio: 2,
      trial_id: null,
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
  },
  methods: {
    getStock() {
      // 列表
      this.$api.getStockExceptionList(this.pageParams).then((res) => {
        this.tableData = res.data.data;
        this.pageParams.total = res.data.meta.total;
      });
    },

    handleChangePageParams() {
      this.pageParams.page = 1;
      this.getStock();
    },

    openDialog(id) {
      this.show_Dialog = true;
      this.trial_id = id;
    },

    trial() {
      // 审核
      const data = {
        id: this.trial_id,
        status: this.radio,
      };
      this.$api.putStockException(data).then((res) => {
        if (res.code == 200) {
          this.handleChangePageParams();
          this.show_Dialog = false;
        }
      });
    },

    clearForm() {
      this.trial_id = null;
      this.radio = 1;
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
