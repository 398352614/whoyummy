<template>
  <!-- 领用记录 -->
  <div class="spare-list">
    <!-- 查询开始 -->
    <el-card class="box-card" shadow="never">
      <div class="conditional">
        <span class="search-title"> {{ $t("collectioTime") }}</span>
        <el-date-picker
          size="small"
          v-model="search_date"
          type="daterange"
          value-format="yyyy-MM-dd"
          :range-separator="$t('To')"
          :start-placeholder="$t('StartTime')"
          :end-placeholder="$t('EndTime')"
          class="search-bar-date"
          :picker-options="pickerOptionsSearch"
        >
        </el-date-picker>
        <!-- <span class="search-title">{{ $t("sparePartNo") }}:</span> -->
        <el-input
          size="small"
          clearable
          :placeholder="$t('enterSparNo')"
          @keyup.enter.native="handleChangePageParams"
          v-model="pageParams.sp_name"
          class="search-bar"
          style="margin-bottom: 10px"
        >
        </el-input>
        <!-- <span class="search-title">{{ $t("recipient") }}:</span> -->
        <el-input
          size="small"
          clearable
          :placeholder="$t('PleaseEnterTheRecipient')"
          @keyup.enter.native="handleChangePageParams"
          v-model="pageParams.receive_person"
          class="search-bar"
        >
        </el-input>
        <!-- <span class="search-title">{{ $t("usedVehicles") }}:</span> -->
        <el-select
          clearable
          :placeholder="this.$t('PleaseEnterVehicle')"
          size="small"
          @keyup.enter.native="handleChangePageParams"
          v-model="pageParams.car_no"
          class="search-bar"
        >
          <el-option
            v-for="(item, index) in car_List"
            :key="index"
            :label="item.car_no"
            :value="item.id"
          ></el-option>
        </el-select>
        <!-- <span class="search-title"> {{ $t("Status") }}:</span> -->
        <el-select
          clearable
          :placeholder="this.$t('PleaseSelectStatus')"
          size="small"
          @keyup.enter.native="handleChangePageParams"
          v-model="pageParams.receive_status"
          class="search-bar"
        >
          <el-option
            v-for="(item, index) in receive_status_list"
            :key="index"
            :label="item.name"
            :value="item.id"
          ></el-option>
        </el-select>

        <el-button
          size="small"
          @click="handleChangePageParams"
          :loading="this.$store.state.config.button_loading"
          type="primary"
          >{{ $t("Search") }}</el-button
        >
        <!-- <el-button
              size="small"
              type="info"
              @click="handleResetPageParams"
              :loading="this.$store.state.config.button_loading"
              >{{ $t("reset") }}</el-button
            > -->
      </div>
    </el-card>
    <!-- 表格 -->
    <el-card shadow="never" class="card-box">
      <el-table
        height="calc(100vh - 287px)"
        :data="hasPerm('spare-parts.record') ? tableData : []"
        v-loading="global_loading()"
        element-loading-text="loading..."
        border
        style="width: 100%"
        :cell-style="{ padding: '5px 0px' }"
      >
        <template slot="empty" v-if="!hasPerm('spare-parts.record')">
          <div style="font-size: 18px">
            {{ $t("AdministratorActivate") }}
          </div>
        </template>
        <!-- 操作 -->
        <!-- <el-table-column width="100" align="center" :label="$t('Operation')">
          <template slot-scope="scope">
            <el-dropdown>
              <el-button size="small" type="primary" plain class="operation">
                {{ $t("Operation") }}
                <i class="el-icon-arrow-down el-icon--right"></i>
              </el-button>
              <el-dropdown-menu slot="dropdown">
                <el-dropdown-item @click.native="cancellation(scope.row.id)">
                  <el-button class="operation" size="small" type="text">{{
                    $t("作废")
                  }}</el-button>
                </el-dropdown-item>
              </el-dropdown-menu>
            </el-dropdown>
          </template>
        </el-table-column> -->
        <el-table-column width="100" align="center" :label="$t('Operation')">
          <template slot-scope="scope">
            <el-tooltip
              :content="$t('AdministratorActivate')"
              placement="top"
              :disabled="hasPerm('spare-parts.invalid') ? true : false"
            >
              <div>
                <el-button
                  :disabled="!hasPerm('spare-parts.invalid')"
                  class="operation"
                  size="mini"
                  type="primary"
                  plain
                  @click.native="cancellation(scope.row.id)"
                  >{{ $t("void") }}</el-button
                >
              </div>
            </el-tooltip>
          </template>
        </el-table-column>
        <el-table-column
          prop="created_at"
          align="center"
          :label="$t('collectioTime')"
          width="160"
        >
        </el-table-column>
        <el-table-column
          prop="receive_status_name"
          align="center"
          :label="$t('Status')"
        >
        </el-table-column>
        <el-table-column
          prop="sp_no"
          align="center"
          :label="$t('sparePartNo')"
          width="140"
        >
        </el-table-column>
        <el-table-column
          prop="sp_name"
          align="center"
          :label="$t('sparePartName')"
          width="140"
        >
        </el-table-column>
        <el-table-column
          prop="sp_brand"
          align="center"
          :label="$t('spareBrand')"
          width="100"
        >
        </el-table-column>
        <el-table-column
          prop="sp_model"
          align="center"
          :label="$t('spModel')"
          width="100"
        >
        </el-table-column>
        <el-table-column prop="sp_unit_name" align="center" :label="$t('unit')">
        </el-table-column>
        <el-table-column
          prop="receive_price"
          align="center"
          :label="$t('unitPrice')"
          width="140"
        >
        </el-table-column>
        <el-table-column
          prop="receive_quantity"
          align="center"
          :label="$t('collectionQuantity')"
          width="160"
        >
        </el-table-column>
        <el-table-column
          prop="car_no"
          align="center"
          :label="$t('usedVehicles')"
          width="160"
        >
        </el-table-column>
        <el-table-column
          prop="price_total"
          align="center"
          :label="$t('totalPrice')"
          width="180"
        >
        </el-table-column>
        <el-table-column
          prop="receive_person"
          align="center"
          :label="$t('recipient')"
        >
        </el-table-column>
      </el-table>
      <base-pagination
        :pageParams="pageParams"
        @search="getRecipientsRecords"
        class="pagination-size"
      ></base-pagination>
      <iframe frameborder="0" name="downExcel" style="display: none"></iframe>
    </el-card>
  </div>
</template>

<script>
import BasePagination from "@/components/BasePagination/BasePagination";
import loading from "@/mixin/global_loading";

export default {
  name: "RecipientsRecords",
  components: {
    BasePagination,
  },
  mixins: [loading],
  data() {
    return {
      heightSearch: false,
      tableData: [],
      car_List: [],
      search_date: ["", ""],
      dialogVisible: false,
      labelPosition: "right",
      type: 1,
      pageParams: {
        sp_name: "", //备品名称
        receive_person: "", //领用人
        car_no: "", //使用车辆
        receive_status: "", //状态
        begin_date: "", //开始时间
        end_date: "", //结束时间
        total: 0,
        page: 1,
        per_page: 20,
      },
      // 状态
      receive_status_list: [
        { id: 1, name: this.$t("normal") },
        { id: 2, name: this.$t("void") },
      ],
    };
  },
  // 时间筛选
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
    this.getCarList();
    this.getRecipientsRecords();
  },
  methods: {
    // 车牌号
    getCarList() {
      this.$api.getCarsPublic().then((res) => {
        this.car_List = res.data.data;
      });
    },
    getRecipientsRecords() {
      // 列表
      this.$api.getRecipientsRecords(this.pageParams).then((res) => {
        this.tableData = res.data.data;
        this.pageParams.total = res.data.meta.total;
      });
    },
    handleChangePageParams() {
      // 查询
      this.pageParams.page = 1;
      this.getRecipientsRecords();
    },
    // 重置
    handleResetPageParams() {
      this.pageParams.sp_name = "";
      this.pageParams.receive_person = "";
      this.pageParams.car_no = "";
      this.pageParams.receive_status = "";
      this.search_date = ""; //维修时间
      this.getRecipientsRecords();
    },
    // 作废
    cancellation(id) {
      this.$confirm(this.$t("afterBeingVoided") + "?", this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      })
        .then(() => {
          this.$api.putRecipientsRecords(id).then((res) => {
            if (res.code === 200) {
              // this.$message({
              //   message: "已作废!",
              //   type: "success",
              // });
              this.getRecipientsRecords();
            }
          });
        })
        .catch(() => {});
    },
  },
};
</script>
<style lang="less" scoped>
.spare-list {
  .box-card {
    margin-bottom: 10px;
  }
  .conditional {
    .conditional_but {
      margin-top: 20px;
      float: right;
    }
  }
}
</style>
<style lang="less">
.spare-list {
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
  .el-dialog__footer {
    text-align: center;
  }
}
</style>
