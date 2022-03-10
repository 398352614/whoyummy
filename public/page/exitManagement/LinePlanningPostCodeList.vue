<template>
  <!-- 邮编划分 -->
  <div class="line-planning-post-code">
    <div class="query-panel">
      <div>
        <!-- 线路名称 -->
        <el-input
          clearable
          @keyup.enter.native="handleChangePageParams"
          v-model="pageParams.name"
          :placeholder="$t('pleaseEnterLineName')"
          size="small"
          class="search-bar"
        ></el-input>
        <!-- 邮编 -->
        <el-input
          clearable
          v-model="pageParams.post_code"
          @keyup.enter.native="handleChangePageParams"
          @input="
            pageParams.post_code = pageParams.post_code.replace(/[^\d]/g, '')
          "
          :placeholder="$t('PleaseEnterZipCode')"
          size="small"
          class="search-bar"
        ></el-input>
        <el-button
          @click="handleChangePageParams"
          :loading="this.$store.state.config.button_loading"
          size="small"
          type="primary"
          >{{ $t("Search") }}</el-button
        >
      </div>
      <el-tooltip
        :content="$t('AdministratorActivate')"
        placement="top"
        :disabled="hasPerm('line.post-code-store') ? true : false"
      >
        <div>
          <!-- 增加线路规 -->
          <el-button
            :disabled="!hasPerm('line.post-code-store')"
            @click="handlerAddClick"
            size="small"
            type="primary"
            >{{ $t("Add") }}</el-button
          >
        </div>
      </el-tooltip>
    </div>
    <!-- 表格 -->
    <el-table
      height="calc(100vh - 281px)"
      class="table_box"
      @selection-change="selectionChange"
      v-loading="global_loading()"
      element-loading-text="loading..."
      border
      :data="hasPerm('line.post-code-index') ? lineData : []"
      :cell-style="{ padding: '5px 0px' }"
      fit
    >
      <template slot="empty" v-if="!hasPerm('line.post-code-index')">
        <div style="font-size: 18px">
          {{ $t("AdministratorActivate") }}
        </div>
      </template>
      <el-table-column align="center" type="selection" width="55">
      </el-table-column>
      <el-table-column
        align="center"
        :label="$t('Number')"
        width="55"
        type="index"
      >
      </el-table-column>
      <el-table-column align="center" :label="$t('Operation')" width="120">
        <template slot-scope="scope">
          <el-dropdown>
            <el-button size="mini" type="primary" plain class="operation">
              {{ $t("Operation") }}
              <i class="el-icon-arrow-down el-icon--right"></i>
            </el-button>
            <el-dropdown-menu slot="dropdown">
              <!-- 编辑 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('line.post-code-update') ? true : false"
              >
                <div
                  :style="
                    hasPerm('line.post-code-update')
                      ? ''
                      : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    @click.native="modeifyItem(scope.row)"
                    :disabled="!hasPerm('line.post-code-update')"
                    :style="
                      hasPerm('line.post-code-update')
                        ? ''
                        : 'color:rgba(201, 201, 201, 0.8)'
                    "
                  >
                    {{ $t("Edit") }}
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 高级配置 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="
                  hasPerm('line.post-code-merchant-config') ? true : false
                "
              >
                <div
                  :style="
                    hasPerm('line.post-code-merchant-config')
                      ? ''
                      : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('line.post-code-merchant-config')"
                    @click.native="openConfigDialog(scope.row)"
                    :style="
                      hasPerm('line.post-code-merchant-config')
                        ? ''
                        : 'color:rgba(201, 201, 201, 0.8)'
                    "
                  >
                    {{ $t("advancedSettings") }}
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 删除 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('line.post-code-destroy') ? true : false"
              >
                <div
                  :style="
                    hasPerm('line.post-code-destroy')
                      ? ''
                      : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('line.post-code-destroy')"
                    @click.native="delItem(scope.row)"
                    :style="
                      hasPerm('line.post-code-destroy')
                        ? ''
                        : 'color:rgba(201, 201, 201, 0.8)'
                    "
                  >
                    {{ $t("Delete") }}
                  </el-dropdown-item>
                </div>
              </el-tooltip>
            </el-dropdown-menu>
          </el-dropdown>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        :label="$t('enabledState')"
        prop="country_name"
        width="115"
      >
        <template slot-scope="scope">
          <el-switch
            v-model="scope.row.status"
            @change="putStatusByList($event, scope.row.id, scope)"
            :disabled="!hasPerm('line.status')"
            :active-value="1"
            :inactive-value="2"
          >
          </el-switch>
        </template>
      </el-table-column>
      <!-- <el-table-column
        align="center"
        :label="$t('Country')"
        prop="country_name"
        width="150"
      >
      </el-table-column> -->
      <el-table-column
        align="center"
        :label="$t('contractingOutlets')"
        prop="warehouse_name"
        width="200"
      >
      </el-table-column>
      <el-table-column
        align="center"
        :label="$t('LineName')"
        prop="name"
        width="180"
      >
      </el-table-column>
      <el-table-column
        align="center"
        :label="$t('pickupRange')"
        prop="line_range"
        width="300"
      >
      </el-table-column>
      <el-table-column
        align="center"
        :label="$t('TimeRange')"
        prop="work_day_list"
        width="340"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="pickup_max_count"
        :label="$t('MaxPickOrder')"
        width="200"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="pie_max_count"
        :label="$t('MaxDeliveryOrder')"
        width="200"
      >
      </el-table-column>
      <!-- <el-table-column
        align="center"
        prop="creator_name"
        :label="$t('Creator')"
      >
      </el-table-column> -->
      <el-table-column
        align="center"
        prop="created_at"
        :label="$t('Created')"
        width="160"
      >
      </el-table-column>
    </el-table>
    <!-- 页码 -->
    <div class="footer-but">
      <div>
        <span>
          {{ $t("enableDisable") }}
        </span>
        <el-switch
          v-model="changStatus"
          @change="putMoreStatus"
          :disabled="checkItemList.length == 0 && !hasPerm('line.status')"
          :active-value="1"
          :inactive-value="2"
        >
        </el-switch>
      </div>

      <base-pagination :pageParams="pageParams" @search="getLineList">
      </base-pagination>
    </div>

    <!-- 高级配置表单 -->
    <el-dialog
      width="90%"
      :visible.sync="advanced_config_show"
      @close="advancedConfigClose"
    >
      <div class="line-name">{{ $t("lineName") }}: {{ merchantLine.name }}</div>
      <div
        class="line-config"
        v-for="(
          item_one, indexs
        ) in merchantLine.merchant_group_line_range_list"
        :key="indexs"
      >
        <div class="line-postcode">
          {{ $t("Zipcoderange") }}: {{ item_one.post_code_range }}
        </div>
        <div :class="[merchantShow[indexs] ? 'line-info-hidden' : 'line-info']">
          {{ $t("PleaseSelectMerchant") }}:
          <el-select
            class="slect-merchants"
            v-model="noUse"
            @change="addMerchants($event, item_one)"
            :placeholder="$t('pleaseSelectMerchantState')"
          >
            <el-option
              v-for="(item, index) in merchantList"
              :key="index"
              :label="item.name"
              :value="item"
              v-if="filterMerchants(item, item_one.merchant_group_list)"
            ></el-option>
          </el-select>
          <el-button @click="demo(indexs)" type="primary">{{
            $t("enlargeMinimize")
          }}</el-button>
          <div
            class="mer-container"
            v-for="(item_two, index) in item_one.merchant_group_list"
            :key="index"
          >
            <div class="mer-table">
              <span class="mer-name">{{
                item_two.merchant_group_id_name
              }}</span>
              <el-checkbox-group v-model="item_two.workday_list">
                <el-checkbox
                  v-for="week in weekList"
                  :key="week.id"
                  :label="week.id"
                  :disabled="disabledCheck(item_one.workday_list, week.id)"
                  >{{ week.name }}</el-checkbox
                >
              </el-checkbox-group>
              <el-checkbox
                v-model="item_two.is_alone"
                :true-label="1"
                :false-label="2"
                >{{ $t("independent") }}</el-checkbox
              >
            </div>
            <div class="mer-table-btn">
              <el-button
                type="danger"
                @click="removeMerchants(item_one.merchant_group_list, item_two)"
                >{{ $t("Remove") }}</el-button
              >
            </div>
          </div>
        </div>
      </div>
      <div class="dialog-footer">
        <el-button @click="advanced_config_show = false" class="cancel">{{
          $t("Cancel")
        }}</el-button>
        <el-button
          type="primary"
          @click="submitConfig"
          :loading="this.$store.state.config.button_loading"
          class="submit"
          >{{ $t("Confirm") }}</el-button
        >
      </div>
    </el-dialog>
  </div>
</template>

<script>
import BasePagination from "@/components/BasePagination/BasePagination";
import loading from "@/mixin/global_loading";

export default {
  name: "LinePlanningPostCode",
  mixins: [loading],
  components: {
    BasePagination,
  },
  data() {
    return {
      activeName: localStorage.getItem("line_rule") == 2 ? "area" : "postcode",
      lineData: [],
      pageParams: {
        group_id: "",
        is_lock: "",
        begin_date: "",
        end_date: "",
        from_source: "",
        keyword: "",
        total: 0,
        page: 1,
        per_page: 20,
        name: "",
        country: "",
        post_code: "",
      },
      countries: [],

      // 线路分配规则
      line_rule: localStorage.getItem("line_rule") || 1,
      show_type: localStorage.getItem("show_type"),

      // 区域
      addArea_show: false,
      addAreaInfo: {
        warehouse_id: null,
      },
      addressList: [],
      warehouseList: [],
      checkItemList: [],
      changStatus: 2,

      country: JSON.parse(localStorage.getItem("country")),

      // 高级配置
      advanced_config_show: false,
      line_id: null,
      checked: false,
      noUse: null,
      addMerchantsList: [],
      merchantLine: {},
      merchantList: [],
      merchantShow: [],
      weekList: [
        {
          name: this.$t("onMonday"),
          id: 1,
          disabled: false,
        },
        {
          name: this.$t("onTuesday"),
          id: 2,
          disabled: false,
        },
        {
          name: this.$t("onWednesday"),
          id: 3,
          disabled: false,
        },
        {
          name: this.$t("Thursday"),
          id: 4,
          disabled: false,
        },
        {
          name: this.$t("Friday"),
          id: 5,
          disabled: false,
        },
        {
          name: this.$t("onSaturday"),
          id: 6,
          disabled: false,
        },
        {
          name: this.$t("Sunday"),
          id: 0,
          disabled: false,
        },
      ],

      rules: {
        country: [
          {
            required: true,
            message: this.$t("pleaseSelectCountry"),
            trigger: "change",
          },
        ],
        warehouse_id: [
          {
            required: true,
            message: this.$t("pleaseSelectWarehouse"),
            trigger: "change",
          },
        ],
      },
    };
  },
  computed: {
    mapType() {
      return this.$store.state.globalData.maptype;
    },
  },
  activated() {
    this.getLineList();
  },
  created() {
    this.getLineList();
    console.log(this.show_type, "show_type", this.line_rule);
  },
  methods: {
    filterMerchants(mer, list) {
      let result = list.filter((el) => {
        return el.merchant_group_id === mer.id;
      });
      return result.length === 0 ? true : false;
    },
    // 编辑
    modeifyItem(row) {
      this.$router.push({
        name: "AddLine",
        query: {
          id: row.id,
        },
      });
    },
    // eslint-disable-next-line no-unused-vars
    // 删除
    delItem(row) {
      this.$confirm(this.$t("ConfirmToCancel"), this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      }).then(() => {
        this.$api.delLine(row.id).then((res) => {
          if (!res) return;
          this.getLineList();
        });
      });
    },
    handleChangePageParams() {
      this.pageParams.page = 1;
      this.getLineList();
    },
    getLineList() {
      this.$api
        .getLineList({
          page: this.$route.query.page,
          per_page: this.$route.query.per_page,
          ...this.pageParams,
        })
        .then((res) => {
          if (!res) return;
          this.lineData = res.data.data;
          this.pageParams.total = res.data.meta.total;
        });
    },
    // 增加线路规划
    handlerAddClick() {
      this.$router.push({
        name: "AddLine",
      });
    },

    selectionChange(sele) {
      // 列表选项
      this.checkItemList = sele;
      if (sele.length) {
        this.changStatus = sele[0].status;
      }
    },

    // 修改多个线路状态
    putMoreStatus(status) {
      let data = this.checkItemList;
      if (!data.length) {
        this.$message({
          type: "error",
          message: this.$t("PleaseCheckTheLineFirst"),
        });
        return;
      }
      let ids = data.map((el) => {
        return el.id;
      });
      this.$api
        .putStatusByList({
          id_list: ids.join(","),
          status: status,
        })
        .then((res) => {
          if (!res) return;
          data.forEach((el) => {
            el.status = status;
          });
        });
    },

    // 修改启用状态
    putStatusByList(status, data, scope) {
      this.$api
        .putStatusByList({
          id_list: data,
          status: status,
        })
        .then((res) => {
          if (!res.data) {
            status === 1 ? (scope.row.status = 2) : (scope.row.status = 1);
          }
        });
    },

    // 高级配置
    openConfigDialog(val) {
      this.advanced_config_show = true;
      this.line_id = val.id;
      this.getMerchants();
      this.$api.getMerchantLineRange(val.id).then((res) => {
        if (!res.data) return;
        this.merchantLine = res.data;
        this.merchantShow = [];
        this.merchantLine.merchant_group_line_range_list.forEach(
          (el, index) => {
            this.merchantShow[index] = false;
          }
        );
      });
    },

    advancedConfigClose() {
      this.merchantLine = [];
      this.line_id = null;
    },

    getMerchants() {
      this.$api
        .getMerchantsGroup({
          page: 1,
          per_page: 200,
        })
        .then((res) => {
          console.log("res.data.data");
          console.log(res.data.data);

          this.merchantList = res.data.data;
          // this.merchantList = res.data.data.filter(el => {
          //   return el.status === 1
          // })
        });
    },

    addMerchants(val, list) {
      console.log(val);
      let item = list.merchant_group_list.find((el) => {
        return el.merchant_group_id === val.id;
      });
      if (item === undefined) {
        list.merchant_group_list.push({
          merchant_group_id: val.id,
          merchant_group_id_name: val.name,
          workday_list: list.workday_list,
          is_alone: 2,
        });
      }
      this.noUse = null;
    },

    removeMerchants(list, val) {
      list.splice(list.indexOf(val), 1);
    },

    disabledCheck(list, id) {
      let arr = [];
      arr = list.find((el) => {
        return el === id;
      });
      return arr === undefined;
    },

    submitConfig() {
      let data = {
        id: this.line_id,
        merchant_group_line_range_list: null,
      };
      let merchant_group_line_range_list = [];
      this.merchantLine.merchant_group_line_range_list.forEach((one) => {
        one.merchant_group_list.forEach((two) => {
          merchant_group_line_range_list.push({
            post_code_range: one.post_code_range,
            merchant_group_id: two.merchant_group_id,
            is_alone: two.is_alone,
            workday_list: two.workday_list.join(","),
          });
        });
      });
      if (
        merchant_group_line_range_list.find((el) => {
          return !el.workday_list;
        })
      ) {
        this.$message({
          type: "error",
          message: this.$t("PleaseRemoveTheConsignor"),
        });
        return;
      }
      data.merchant_group_line_range_list = JSON.stringify(
        merchant_group_line_range_list
      );
      this.$api.putMerchantLineRange(data).then((res) => {
        if (res.code === 200) {
          this.advanced_config_show = false;
          this.handleChangePageParams();
        }
      });
    },

    demo(val) {
      let newValue = !this.merchantShow[val];
      this.$set(this.merchantShow, val, newValue);
    },
  },
};
</script>

<style lang="less" scoped>
.line-planning-post-code {
  padding: 0px 25px 0px;
  background-color: #fff;
  position: relative;
  .footer-but {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 10px;
  }
  .el-tabs__nav-scroll {
    padding-left: 25px;
    box-sizing: border-box;
    height: 52px;
  }
  .table_box {
    .el-table__header {
      border-bottom: 3px solid #e4ebf1;
    }
  }
  .query-panel {
    display: flex;
    justify-content: space-between;
    margin: 10px 0;
  }
  .dialog-footer {
    text-align: right;
    margin: 30px 20px 0 0;
  }
}
.el-dropdown-menu__item {
  color: #28d094;
}
.line-name {
  margin: 10px 0;
}
.line-config {
  margin: 10px 0;
  .line-postcode {
    margin: 10px 0;
  }
  .slect-merchants {
    width: 300px;
    margin: 10px 0;
  }
  .line-info {
    height: 110.53px;
    overflow: hidden;
  }
  .line-info-hidden {
    height: auto;
  }
  .mer-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    .mer-table {
      width: 90%;
      display: flex;
      justify-content: space-between;
      border: 1px #c4c4c4 solid;
      // padding: 10px 5px;
      .mer-name {
        display: inline-block;
        min-width: 100px;
      }
      & > *:last-child {
        border-left: 1px #c4c4c4 solid;
      }
      .el-checkbox {
        padding: 15px 5px;
      }
      & > span {
        padding: 15px 5px;
      }
    }
    &:not(:last-child) {
      .mer-table {
        border-bottom-style: none;
      }
    }
    .mer-table-btn {
      width: 7%;
    }
  }
}
</style>
<style lang="less">
.line-planning-post-code {
  .el-tabs__content {
    overflow: unset !important;
  }
  .el-input__inner {
    border-radius: 0 !important;
  }
  .el-table .cell {
    white-space: pre-line !important;
  }
}
</style>

