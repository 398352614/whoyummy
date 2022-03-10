<template>
  <!-- 材料列表 -->
  <div class="package-list">
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
            :placeholder="$t('PleaseenterExternalOrderNumber')"
            @keyup.enter.native="handleChangePageParams"
            @clear="handleChangePageParams"
            v-model="pageParams.out_order_no"
          >
          </el-input>
        </el-col> -->
      <el-input
        size="small"
        clearable
        :placeholder="$t('PleaseEnterTheMaterialCode')"
        @keyup.enter.native="handleChangePageParams"
        v-model="pageParams.code"
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
        :data="hasPerm('material.index') ? tableData : []"
        v-loading="global_loading()"
        element-loading-text="loading..."
        border
        style="width: 100%"
        :cell-style="{ padding: '7px 0px' }"
      >
        <template slot="empty" v-if="!hasPerm('material.index')">
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
          prop="order_no"
          align="center"
          :label="$t('OrderNumber')"
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
        <el-table-column prop="code" align="center" :label="$t('MaterialCode')">
        </el-table-column>
        <el-table-column prop="name" align="center" :label="$t('MaterialName')">
        </el-table-column>
        <el-table-column
          prop="out_order_no"
          align="center"
          width="130"
          :label="$t('ExternalIdentification')"
        >
        </el-table-column>
        <el-table-column
          prop="execution_date"
          align="center"
          width="120"
          :label="$t('DistributeDate')"
        >
        </el-table-column>
        <el-table-column
          prop="created_at"
          align="center"
          :label="$t('Created')"
        >
        </el-table-column>
      </el-table>
      <base-pagination
        :pageParams="pageParams"
        @search="getMaterialList"
        class="pagination-size"
      ></base-pagination>
    </div>

    <iframe frameborder="0" name="downExcel" style="display: none"></iframe>
  </div>
</template>

<script>
import BasePagination from "@/components/BasePagination/BasePagination";
import loading from "@/mixin/global_loading";

export default {
  name: "MaterialList",
  components: {
    BasePagination,
  },
  mixins: [loading],
  data() {
    return {
      tableData: [],
      pageParams: {
        code: "",
        order_no: "",
        out_order_no: "",
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
  created() {
    this.getMaterialList();
  },
  methods: {
    getMaterialList() {
      // 列表
      this.$api.getMaterialList(this.pageParams).then((res) => {
        this.tableData = res.data.data;
        this.pageParams.total = res.data.meta.total;
      });
    },
    handleChangePageParams() {
      this.pageParams.page = 1;
      this.getMaterialList();
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
  .el-date-editor {
    width: 100%;
  }
  .el-select {
    width: 100%;
  }
}
</style>
