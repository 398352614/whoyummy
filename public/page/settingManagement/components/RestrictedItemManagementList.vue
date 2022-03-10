<template>
  <!-- 假期配置 -->
  <div class="restricted-item-management">
    <el-tooltip
      :content="$t('AdministratorActivate')"
      placement="top"
      :disabled="hasPerm('holiday.store') ? true : false"
    >
      <div class="conditional">
        <el-button
          :disabled="!hasPerm('holiday.store')"
          size="small"
          type="primary"
          @click="addDialog"
          >{{ $t("Add") }}</el-button
        >
      </div>
    </el-tooltip>
    <el-table
      height="calc(100vh - 234px)"
      :data="hasPerm('holiday.index') ? tableData : []"
      v-loading="global_loading()"
      element-loading-text="loading..."
      border
      style="width: 100%"
      :cell-style="{ padding: '5px 0px' }"
    >
      <template slot="empty" v-if="!hasPerm('holiday.index')">
        <div style="font-size: 18px">
          {{ $t("AdministratorActivate") }}
        </div>
      </template>
      <el-table-column
        prop="date"
        header-align="center"
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
              <!-- 添加货主 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('holiday.merchant-store') ? true : false"
              >
                <div
                  :style="
                    hasPerm('holiday.merchant-store')
                      ? ''
                      : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('holiday.merchant-store')"
                    @click.native="openMerchantDialog(scope.row.id)"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('holiday.merchant-store')"
                      >{{ $t("AddMerchantManagement") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 编辑 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('holiday.update') ? true : false"
              >
                <div
                  :style="
                    hasPerm('holiday.update') ? '' : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    @click.native="getFeeDetails(scope.row.id)"
                    :disabled="!hasPerm('holiday.update')"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('holiday.update')"
                      >{{ $t("Edit") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 删除 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('holiday.destroy') ? true : false"
              >
                <div
                  :style="
                    hasPerm('holiday.destroy') ? '' : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('holiday.destroy')"
                    v-if="scope.row.level !== 1"
                    @click.native="delEmployees(scope.row.id)"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('holiday.destroy')"
                      >{{ $t("Delete") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
            </el-dropdown-menu>
          </el-dropdown>
        </template>
      </el-table-column>
      <el-table-column
        header-align="center"
        align="center"
        prop="name"
        :label="$t('MerchantName')"
      >
      </el-table-column>
      <el-table-column
        header-align="center"
        align="center"
        prop="date_list"
        :label="$t('holidayTime')"
      >
      </el-table-column>
      <el-table-column
        header-align="center"
        align="center"
        prop="amount"
        :label="$t('currentEnabledState')"
      >
        <template slot-scope="scope">
          <el-switch
            v-model="scope.row.status"
            :disabled="!hasPerm('holiday.status')"
            :active-text="$t('start')"
            :active-value="1"
            :inactive-text="$t('closed')"
            :inactive-value="2"
            @change="putHolidayStatus($event, scope.row.id)"
          >
          </el-switch>
        </template>
      </el-table-column>
      <el-table-column
        header-align="center"
        align="center"
        prop="created_at"
        :label="$t('Created')"
      >
      </el-table-column>
      <el-table-column
        header-align="center"
        align="center"
        prop="status_name"
        :label="$t('configureShipper')"
      >
        <template slot-scope="scope">
          <el-tag
            v-for="(tag, index) in scope.row.merchant_list"
            :key="index"
            :closable="hasPerm('holiday.merchant-destroy')"
            @close="delMerchant(scope.row.id, tag.id)"
            type="info"
          >
            {{ tag.name }}
          </el-tag>
        </template>
      </el-table-column>
    </el-table>
    <base-pagination
      class="pagination-size"
      :pageParams="pageParams"
      @search="getHoliday"
    ></base-pagination>

    <el-dialog :visible.sync="show_dialog" @close="close_dialog">
      <el-form
        :model="holiday"
        ref="employeeForm"
        label-position="right"
        label-width="130px"
      >
        <el-form-item :label="$t('MerchantName')">
          <el-input v-model="holiday.name"></el-input>
        </el-form-item>
        <el-form-item :label="$t('holidayTime')">
          <el-calendar v-model="unusedDate">
            <!-- 这里使用的是 2.5 slot 语法，对于新项目请使用 2.6 slot 语法-->
            <template slot="dateCell" slot-scope="{ date, data }">
              <p
                :class="data.isSelected ? 'is-selected' : ''"
                @click="demo(data)"
              >
                {{ data.day.split("-").slice(1).join("-") }}
              </p>
            </template>
          </el-calendar>
        </el-form-item>
        <el-form-item label="">
          <el-tag
            v-for="(tag, index) in checkDateList"
            :key="index"
            closable
            @close="delTag(checkDateList, tag)"
            type="info"
          >
            {{ tag }}
          </el-tag>
        </el-form-item>
      </el-form>
      <div slot="footer">
        <el-button @click="show_dialog = false" class="cancel">{{
          $t("Cancel")
        }}</el-button>
        <el-button type="primary" @click="addFee" class="submit">{{
          $t("Confirm")
        }}</el-button>
      </div>
    </el-dialog>

    <el-dialog :visible.sync="show_merchant" @close="close_dialog">
      <el-table
        max-height="600px"
        :data="merchantList"
        v-loading="global_loading()"
        element-loading-text="loading..."
        @selection-change="handleMerchantItem"
        border
        style="width: 100%"
      >
        <el-table-column type="selection" width="55"> </el-table-column>
        <el-table-column
          header-align="center"
          align="center"
          prop="name"
          :label="$t('shipperName')"
        >
        </el-table-column>
        <el-table-column
          header-align="center"
          align="center"
          prop="type_name"
          :label="$t('shipperType')"
        >
        </el-table-column>
        <el-table-column
          header-align="center"
          align="center"
          prop="email"
          :label="$t('Email')"
        >
        </el-table-column>
        <el-table-column
          header-align="center"
          align="center"
          prop="status"
          :label="$t('enabledState')"
        >
          <template slot-scope="scope">
            {{ scope.row.status === 1 ? $t("start") : $t("closed") }}
          </template>
        </el-table-column>
      </el-table>
      <base-pagination
        :pageParams="pageParamsMerchant"
        @search="getMerchantOfHoliday"
      ></base-pagination>
      <div slot="footer">
        <el-button @click="show_merchant = false" class="cancel">{{
          $t("Cancel")
        }}</el-button>
        <el-button
          type="primary"
          :disabled="!checkMerchant.length"
          @click="submitMerchant(id, checkMerchant)"
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
  name: "EmployeeManagement",
  components: {
    BasePagination,
  },
  mixins: [loading],
  data() {
    return {
      tableData: [],
      pageParams: {
        page: 1,
        per_page: 20,
        total: 0,
      },
      show_dialog: false,
      edit: false,
      holiday: {
        name: "",
        date_list: "",
      },
      unusedDate: null,
      checkDateList: [],

      show_merchant: false,
      merchantList: [],
      checkMerchant: [],
      id: null,
      pageParamsMerchant: {
        page: 1,
        per_page: 10,
        total: 0,
      },
      rules: {
        name: [
          { required: true, message: this.$t("Mandatory"), trigger: "blur" },
        ],
        code: [
          { required: true, message: this.$t("Mandatory"), trigger: "blur" },
        ],
        amount: [
          { required: true, message: this.$t("Mandatory"), trigger: "blur" },
        ],
        status: [
          { required: true, message: this.$t("Mandatory"), trigger: "change" },
        ],
      },
    };
  },
  computed: {
    unit() {
      return this.$store.state.globalData.unit;
    },
  },
  created() {
    this.getHoliday();
  },
  methods: {
    getHoliday() {
      // 列表
      this.$api.getHoliday(this.pageParams).then((res) => {
        this.tableData = res.data.data;
        this.pageParams.total = res.data.meta.total;
      });
    },
    addFee() {
      this.$refs.employeeForm.validate((valid) => {
        if (!valid) return;
        if (!this.holiday.name || !this.checkDateList.length) {
          this.$message({
            type: "error",
            message: `${
              this.holiday.name
                ? this.$t("pleaseSelectDate")
                : this.$t("PleaseName")
            }`,
          });
          return;
        }
        this.holiday.date_list = this.checkDateList.join(",");
        if (this.edit) {
          this.$api.putHoliday(this.holiday).then((res) => {
            if (!res) return;
            this.show_dialog = false;
            this.getHoliday();
            this.checkDateList = [];
          });
        } else {
          this.$api.addHoliday(this.holiday).then((res) => {
            if (!res) return;
            this.show_dialog = false;
            this.getHoliday();
            this.checkDateList = [];
          });
        }
      });
    },

    addDialog() {
      this.show_dialog = true;
    },

    clearFormData() {
      this.holiday.name = "";
      this.holiday.date_list = "";
      this.checkDateList = [];
    },
    close_dialog() {
      this.edit = false;
      this.clearFormData();
    },
    getFeeDetails(id) {
      this.show_dialog = true;
      this.edit = true;
      // eslint-disable-next-line
      this.$api.getHolidayDetails(id).then((res) => {
        if (!res) return;
        this.holiday = res.data;
        this.checkDateList = res.data.date_list.split(",");
      });
    },
    delEmployees(id) {
      this.$confirm(this.$t("ConfirmToCancel") + "?", this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      })
        .then(() => {
          this.$api.delHoliday(id).then((res) => {
            if (!res) return;
            this.getHoliday();
          });
        })
        .catch(() => {});
    },

    // 货主内容
    openMerchantDialog(id) {
      this.show_merchant = true;
      this.id = id;
      this.getMerchantOfHoliday();
    },
    // 货主列表
    getMerchantOfHoliday() {
      this.$api.getMerchantOfHoliday(this.pageParamsMerchant).then((res) => {
        this.merchantList = res.data.data;
        this.pageParamsMerchant.total = res.data.meta.total;
      });
    },

    // 删除货主
    delMerchant(id, merchant_id) {
      this.$api
        .delMerchantOfHoliday({ id: id, merchant_id: merchant_id })
        .then((res) => {
          this.getHoliday();
        });
    },
    // 选择货主列表
    handleMerchantItem(sele) {
      this.checkMerchant = sele;
    },
    // 提交
    submitMerchant(id, list) {
      let merchant_id_list = list.map((el) => {
        return el.id;
      });
      this.$api
        .addMerchantOfHoliday({
          id: id,
          merchant_id_list: merchant_id_list.join(","),
        })
        .then((res) => {
          this.show_merchant = false;
          this.getHoliday();
          this.id = null;
          this.checkMerchant = [];
        });
    },
    // 修改状态
    putHolidayStatus(status, id) {
      this.$api.putHolidayStatus({ id: id, status: status }).then((res) => {
        this.getHoliday();
      });
    },

    demo(data) {
      console.log(data, "dd");
      if (this.checkDateList.indexOf(data.day) == -1) {
        this.checkDateList.push(data.day);
      }
    },

    delTag(list = [], item) {
      list.splice(list.indexOf(item), 1);
    },
  },
};
</script>

<style lang="less" scoped>
.restricted-item-management {
  padding: 20px 25px 10px;
  background: #ffffff;
  border: 1px solid rgba(228, 235, 241, 1);
  .conditional {
    padding: 0px 0 10px;
    float: right;
  }
  .text-tips {
    display: flex;
    .text-right {
      padding: 0 5px;
    }
  }
  .el-tag {
    margin: 5px;
  }
}
</style>
