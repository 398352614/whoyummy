<template>
  <!-- 包裹列表 -->
  <div class="package-list">
    <div class="conditional">
      <!-- <el-col :span="6">
          <el-date-picker
            size="small"
            v-model="search_date"
            type="daterange"
            value-format="yyyy-MM-dd"
            :range-separator="$t('To')"
            :start-placeholder="$t('StartTime')"
            :end-placeholder="$t('EndTime')"
          >
          </el-date-picker>
        </el-col> -->
      <el-input
        size="small"
        clearable
        :placeholder="$t('PleaseEnterOrderNo')"
        @keyup.enter.native="handleChangePageParams"
        v-model="pageParams.order_no"
        class="search-bar"
      >
      </el-input>
      <!-- <el-col :span="4">
          <el-input
            size="small"
            clearable
            :placeholder="$t('请输入外部订单号')"
            @keyup.enter.native="handleChangePageParams"
            @clear="handleChangePageParams"
            v-model="pageParams.out_order_no"
          >
          </el-input>
        </el-col> -->

      <el-input
        size="small"
        clearable
        :placeholder="$t('PleaseEnterParcelNo')"
        @keyup.enter.native="handleChangePageParams"
        v-model="pageParams.express_first_no"
        class="search-bar"
      >
      </el-input>
      <el-select
        v-model="pageParams.stage"
        :placeholder="this.$t('pleaseSelect')"
        @keyup.enter.native="handleChangePageParams"
        clearable
        size="small"
        class="search-bar"
      >
        <el-option
          v-for="(item, index) in packageStageList"
          :key="index"
          :label="item.name"
          :value="item.id"
        >
        </el-option>
      </el-select>
      <el-button
        size="small"
        @click="handleChangePageParams"
        :loading="this.$store.state.config.button_loading"
        type="primary"
        >{{ $t("Search") }}</el-button
      >
    </div>
    <div style="height: calc(100vh - 192px)">
      <el-table
        height="calc(100vh - 230px)"
        :data="hasPerm('package.index') ? tableData : []"
        v-loading="global_loading()"
        element-loading-text="loading..."
        border
        style="width: 100%"
        :cell-style="{ padding: '7px 0px' }"
      >
        <template slot="empty" v-if="!hasPerm('package.index')">
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
          width="150"
        >
          <template slot-scope="scope">
            <el-link
              :underline="false"
              @click="
                hasPerm('order.index')
                  ? $router.push({
                      name: 'ParcelPracking',
                      params: { express_first_no: scope.row.express_first_no },
                    })
                  : ''
              "
              class="link"
              >{{ scope.row.express_first_no }}</el-link
            >
          </template>
        </el-table-column>
        <el-table-column
          prop="order_no"
          align="center"
          :label="$t('OrderNumber')"
          width="150"
        >
          <template slot-scope="scope">
            <el-link
              :underline="false"
              @click="
                hasPerm('order.index')
                  ? $router.push({
                      name: 'OrderList',
                      params: { order_no: scope.row.order_no },
                    })
                  : ''
              "
              class="link"
              >{{ scope.row.order_no }}</el-link
            >
          </template>
        </el-table-column>
        <el-table-column
          prop="warehouse_name"
          align="center"
          :label="$t('currentOutlets')"
          width="150"
        >
        </el-table-column>
        <el-table-column
          prop="next_warehouse_name"
          align="center"
          :label="$t('nextStop')"
          width="150"
        >
        </el-table-column>
        <el-table-column
          prop="weight"
          align="center"
          :label="$t('Weight') + '(' + weight_unit_symbol + ')'"
          width="150"
        >
        </el-table-column>
        <el-table-column
          prop="second_execution_date"
          align="center"
          :label="$t('EstimatedDeliveryDate')"
          width="200"
        >
        </el-table-column>
        <el-table-column
          prop="expiration_date"
          align="center"
          width="200"
          :label="$t('LatestDeliveryDate')"
        >
        </el-table-column>
        <el-table-column
          prop="created_at"
          align="center"
          :label="$t('Created')"
          width="160"
        >
        </el-table-column>
        <el-table-column
          prop="stock_in_time"
          align="center"
          :label="$t('collectionTime')"
          width="160"
        >
        </el-table-column>
        <el-table-column
          prop="bag_no"
          align="center"
          :label="$t('bagNumber')"
          width="150"
        >
        </el-table-column>
        <el-table-column
          prop="shift_no"
          align="center"
          :label="$t('trainNumber')"
          width="150"
        >
        </el-table-column>
        <el-table-column
          prop="stage_status_name"
          align="center"
          width="130"
          :label="$t('Status')"
        >
        </el-table-column>
      </el-table>
      <base-pagination
        class="pagination-size"
        :pageParams="pageParams"
        @search="getPackageList"
      ></base-pagination>
    </div>

    <iframe frameborder="0" name="downExcel" style="display: none"></iframe>
  </div>
</template>

<script>
import BasePagination from "@/components/BasePagination/BasePagination";
import loading from "@/mixin/global_loading";

export default {
  name: "PackageList",
  components: {
    BasePagination,
  },
  mixins: [loading],
  data() {
    return {
      weight_unit_symbol: "",
      tableData: [],
      packageStageList: [],
      pageParams: {
        order_no: "",
        out_order_no: "",
        express_first_no: "",
        stage: "",
        begin_date: "",
        end_date: "",
        keyword: "",
        total: 0,
        page: 1,
        per_page: 20,
      },
      search_date: ["", ""],
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
        this.getPackageList();
      },
    },
  },
  created() {
    this.getPackageList();
    this.getDictionary();
    this.getAdvanceSetting(); //单位
  },
  methods: {
    getAdvanceSetting() {
      this.$api.getAdvanceSetting().then((res) => {
        // this.volume_unit_symbol = res.data.volume_unit_symbol; // 体积单位
        this.weight_unit_symbol = res.data.weight_unit_symbol; // 重量单位
        // this.currency_unit_symbol = res.data.currency_unit_symbol; // 单价单位
      });
    },
    getDictionary() {
      this.$api.getDictionary().then((res) => {
        this.packageStageList = res.data.packageStageList;
      });
    },
    getPackageList() {
      // 列表
      this.$api.getPackageList(this.pageParams).then((res) => {
        this.tableData = res.data.data;
        this.pageParams.total = res.data.meta.total;
      });
    },
    handleChangePageParams() {
      this.pageParams.page = 1;
      this.getPackageList();
    },
  },
};
</script>
<style lang="less" scoped>
.package-list {
  padding: 20px 25px 10px;
  background: #ffffff;
  border: 1px solid rgba(228, 235, 241, 1);
  .conditional {
    padding: 0px 0 10px;
  }
}
</style>
<style lang="less">
.package-list {
  .conditional {
    .el-input__inner {
      border-radius: 0;
    }
  }
  // .el-date-editor {
  //   width: 100%;
  // }
  // .el-select {
  //   width: 100%;
  // }
}
</style>
