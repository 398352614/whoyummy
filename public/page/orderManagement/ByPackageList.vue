<template>
  <!-- 顺带包裹查询 -->
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
        v-model="pageParams.merchant_id"
        @keyup.enter.native="handleChangePageParams"
        :placeholder="$t('PleaseSelectMerchant')"
        class="search-bar"
      >
        <el-option
          v-for="(item, index) in merchantList"
          :key="index"
          :label="item.name"
          :value="item.id"
        ></el-option>
      </el-select>
      <el-input
        size="small"
        clearable
        :placeholder="$t('PleaseEnterParcelNo')"
        @keyup.enter.native="handleChangePageParams"
        v-model="pageParams.package_no"
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
    <div style="height: calc(100vh - 193px)">
      <el-table
        height="calc(100vh - 230px)"
        :data="hasPerm('additional-package.index') ? tableData : []"
        v-loading="global_loading()"
        element-loading-text="loading..."
        border
        style="width: 100%"
        :cell-style="{ padding: '7px 0px' }"
      >
        <template slot="empty" v-if="!hasPerm('additional-package.index')">
          <div style="font-size: 18px">
            {{ $t("AdministratorActivate") }}
          </div>
        </template>
        <el-table-column type="expand">
          <template slot-scope="props">
            <el-table
              max-height="600px"
              :data="props.row.additional_package_list"
              v-loading="global_loading()"
              element-loading-text="loading..."
              border
              style="width: 100%"
              :cell-style="{ padding: '7px 0px' }"
            >
              <el-table-column
                prop="package_no"
                align="center"
                :label="$t('incidentalParcelNumber')"
              >
              </el-table-column>
              <el-table-column
                prop="merchant_name"
                align="center"
                :label="$t('pushForwarder')"
              >
              </el-table-column>
              <el-table-column
                prop="sticker_no"
                align="center"
                :label="$t('waybillPrintingNo')"
              >
                <template slot-scope="scope">
                  {{ scope.row.sticker_no || "/" }}
                </template>
              </el-table-column>
              <el-table-column
                prop="sticker_amount"
                align="center"
                :label="
                  $t('printWaybillFee') + '(' + currency_unit_symbol + ')'
                "
              >
                <template slot-scope="scope">
                  {{ +scope.row.sticker_amount || "/" }}
                </template>
              </el-table-column>
              <el-table-column
                prop="delivery_amount"
                align="center"
                :label="$t('pickFee') + '(' + currency_unit_symbol + ')'"
              >
                <template slot-scope="scope">
                  {{ +scope.row.delivery_amount || "/" }}
                </template>
              </el-table-column>
            </el-table>
          </template>
        </el-table-column>
        <el-table-column type="index" align="center" width="50">
          <template slot="header">
            <span>{{ $t("Number") }}</span>
          </template>
        </el-table-column>
        <el-table-column
          prop="batch_no"
          align="center"
          :label="$t('incidentalSiteNumber')"
        >
        </el-table-column>
        <el-table-column
          prop="additional_package_count"
          align="center"
          :label="$t('incidentalQuantity')"
        >
        </el-table-column>
        <el-table-column
          prop="driver_name"
          align="center"
          :label="$t('Driver')"
        >
        </el-table-column>
        <el-table-column
          prop="line_name"
          align="center"
          :label="$t('LineName')"
        >
        </el-table-column>
        <el-table-column
          prop="tour_no"
          align="center"
          :label="$t('PickupLineNumber')"
        >
        </el-table-column>
        <el-table-column
          prop="updated_at"
          align="center"
          :label="$t('dateOfReceipt')"
        >
        </el-table-column>
      </el-table>
      <base-pagination
        :pageParams="pageParams"
        @search="getAdditionalPackage"
        class="pagination-size"
      ></base-pagination>
    </div>
  </div>
</template>

<script>
import BasePagination from "@/components/BasePagination/BasePagination";
import loading from "@/mixin/global_loading";

export default {
  name: "ByPackageList",
  components: {
    BasePagination,
  },
  mixins: [loading],
  data() {
    return {
      currency_unit_symbol: "", //单位
      tableData: [],
      pageParams: {
        package_no: "",
        merchant_id: "",
        begin_date: "",
        end_date: "",
        keyword: "",
        total: 0,
        page: 1,
        per_page: 20,
      },
      search_date: ["", ""],
      merchantList: [],
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
    unit() {
      return this.$store.state.globalData.unit;
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
  created() {
    this.getAdvanceSetting();
    this.getAdditionalPackage();
    this.getMerchantsList();
  },
  methods: {
    // 获取单位值
    getAdvanceSetting() {
      this.$api.getAdvanceSetting().then((res) => {
        this.currency_unit_symbol = res.data.currency_unit_symbol; //单价单位
      });
    },
    getAdditionalPackage() {
      // 列表
      this.$api.getAdditionalPackage(this.pageParams).then((res) => {
        this.tableData = res.data.data;
        this.pageParams.total = res.data.meta.total;
      });
    },
    handleChangePageParams() {
      this.pageParams.page = 1;
      this.getAdditionalPackage();
    },
    getMerchantsList() {
      let pageParam = {
        status: "",
        begin_date: "",
        end_date: "",
        keyword: "",
        total: 0,
        page: 1,
        per_page: 200,
      };
      this.$api.getMerchantsPublic(pageParam).then((res) => {
        this.merchantList = res.data.data;
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
