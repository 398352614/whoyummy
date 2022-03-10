<template>
  <!-- 增值服务 -->
  <div class="dispatch-order">
    <div class="conditional">
      <div>
        <el-input
          size="small"
          clearable
          @keyup.enter.native="getFee"
          v-model="pageParams.name"
          :placeholder="$t('serviceName')"
          class="search-bar"
        ></el-input>
        <el-select
          clearable
          size="small"
          v-model="pageParams.payer_type"
          @keyup.enter.native="getFee"
          :placeholder="$t('PleasePayer')"
          class="search-bar"
        >
          <el-option
            v-for="(item, index) in feePayerTypeList"
            :key="index"
            :label="item.name"
            :value="item.id"
          ></el-option>
        </el-select>
        <el-button
          size="small"
          @click="getFee"
          :loading="this.$store.state.config.button_loading"
          type="primary"
          >{{ $t("Search") }}</el-button
        >
      </div>
      <!-- 新增按钮 -->
      <el-tooltip
        :content="$t('AdministratorActivate')"
        placement="top"
        :disabled="hasPerm('fee.store') ? true : false"
      >
        <div>
          <el-button
            :disabled="!hasPerm('fee.store')"
            size="small"
            type="primary"
            @click="addDialog"
            >{{ $t("Add") }}</el-button
          >
        </div>
      </el-tooltip>
    </div>
    <el-table
      height="calc(100vh - 276px)"
      :data="hasPerm('fee.index') ? tableData : []"
      v-loading="global_loading()"
      element-loading-text="loading..."
      border
      style="width: 100%"
      :cell-style="{ padding: '5px 0px' }"
    >
      <template slot="empty" v-if="!hasPerm('fee.index')">
        <div style="font-size: 18px">
          {{ $t("AdministratorActivate") }}
        </div>
      </template>
      <el-table-column
        type="index"
        header-align="center"
        align="center"
        width="50"
      >
        <template slot="header">
          <span>{{ $t("Number") }}</span>
        </template>
      </el-table-column>
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
              <!-- 编辑 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('fee.update') ? true : false"
              >
                <div
                  :style="hasPerm('fee.update') ? '' : 'cursor: not-allowed'"
                >
                  <el-dropdown-item
                    @click.native="getFeeDetails(scope.row.id)"
                    :disabled="!hasPerm('fee.update')"
                  >
                    <el-button
                      :disabled="!hasPerm('fee.update')"
                      class="operation"
                      size="small"
                      type="text"
                      >{{ $t("Edit") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 删除 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('fee.destroy') ? true : false"
              >
                <div
                  :style="hasPerm('fee.destroy') ? '' : 'cursor: not-allowed'"
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('fee.destroy')"
                    @click.native="delEmployees(scope.row.id)"
                    v-if="scope.row.level !== 1"
                  >
                    <el-button
                      :disabled="!hasPerm('fee.destroy')"
                      class="operation"
                      size="small"
                      type="text"
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
        :label="$t('serviceName')"
      >
      </el-table-column>
      <el-table-column
        header-align="center"
        align="center"
        prop="payer_type_name"
        :label="$t('payer')"
      >
      </el-table-column>
      <!-- <el-table-column
        header-align="center"
        align="center"
        prop="code"
        :label="$t('serviceCode')"
      >
      </el-table-column> -->
      <el-table-column
        header-align="center"
        align="center"
        prop="amount"
        :label="$t('fees') + '(' + currency_unit_symbol + ')'"
      >
      </el-table-column>
      <el-table-column
        header-align="center"
        align="center"
        prop="level_name"
        :label="$t('level')"
      >
        <!-- <template slot-scope="scope">
          {{ levelList[scope.row.level - 1] }}
        </template> -->
      </el-table-column>
      <el-table-column
        header-align="center"
        align="center"
        prop="status_name"
        :label="$t('Status')"
      >
      </el-table-column>
      <el-table-column
        header-align="center"
        align="center"
        prop="created_at"
        :label="$t('Created')"
      >
      </el-table-column>
    </el-table>
    <base-pagination
      class="pagination-size"
      :pageParams="pageParams"
      @search="getFee"
    ></base-pagination>

    <el-dialog
      class="right-angle"
      :title="edit ? $t('editingService') : $t('addService')"
      :visible.sync="show_dialog"
      @close="close_dialog"
    >
      <el-form
        :model="employees"
        :rules="rules"
        ref="employeeForm"
        label-position="right"
        label-width="130px"
        class="add-table"
      >
        <el-form-item :label="$t('serviceName')" prop="name">
          <el-input v-model="employees.name"></el-input>
        </el-form-item>
        <!-- <el-form-item :label="$t('serviceCode')" prop="code">
          <el-input v-model="employees.code" :disabled="edit"></el-input>
          <div>{{ $t("cannotGeneration") }}</div>
        </el-form-item> -->
        <el-form-item :label="$t('fees')" prop="amount">
          <div class="text-tips">
            <el-input v-model="employees.amount" style="width: 92%"></el-input>
            <div class="text-right">({{ currency_unit_symbol }})</div>
          </div>
        </el-form-item>
        <el-form-item :label="$t('payer')" prop="payer_type">
          <el-select v-model="employees.payer_type" placeholder=" ">
            <el-option
              v-for="item in feePayerTypeList"
              :key="item.id"
              :label="item.name"
              :value="item.id"
            >
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item :label="$t('Status')" prop="status">
          <el-radio-group v-model="employees.status">
            <el-radio :label="1">{{ $t("enable") }}</el-radio>
            <el-radio v-if="!(edit && employees.level === 1)" :label="2">{{
              $t("disable")
            }}</el-radio>
          </el-radio-group>
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
      currency_unit_symbol: "", //价格单位
      tableData: [],
      feePayerTypeList: [], //付款方
      pageParams: {
        name: "",
        keyword: "",
        page: 1,
        per_page: 20,
        total: 0,
      },
      show_dialog: false,
      edit: false,
      employees: {
        name: "",
        code: null,
        amount: "",
        payer_type: "",
        status: 1,
      },
      levelList: [this.$t("系统级"), this.$t("自定义")],
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
        payer_type: [
          { required: true, message: this.$t("Mandatory"), trigger: "change" },
        ],
      },
    };
  },
  created() {
    this.getAdvanceSetting();
    this.getDictionary();
    this.getFee();
  },
  methods: {
    getDictionary() {
      this.$api.getDictionary().then((res) => {
        this.feePayerTypeList = res.data.feePayerTypeList;
      });
    },
    // 获取单位值
    getAdvanceSetting() {
      this.$api.getAdvanceSetting().then((res) => {
        this.currency_unit_symbol = res.data.currency_unit_symbol; //单价单位
      });
    },
    getFee() {
      // 列表
      this.$api.getFee(this.pageParams).then((res) => {
        this.tableData = res.data.data;
        this.pageParams.total = res.data.meta.total;
      });
    },
    addFee() {
      this.$refs.employeeForm.validate((valid) => {
        if (!valid) return;
        if (this.edit) {
          this.$api.putFee(this.employees).then((res) => {
            if (!res) return;
            this.show_dialog = false;
            this.getFee();
          });
        } else {
          this.$api.addFee(this.employees).then((res) => {
            if (!res) return;
            this.show_dialog = false;
            this.getFee();
          });
        }
      });
    },
    addDialog() {
      this.show_dialog = true;
      this.employees = {
        name: "",
        code: null,
        amount: "",
        payer_type: "",
        status: 1,
      };
      this.$refs.employees.resetFields();
    },
    close_dialog() {
      this.$refs.employeeForm.resetFields();
      this.edit = false;
    },
    getFeeDetails(id) {
      this.employees = {
        name: "",
        code: null,
        amount: "",
        payer_type: "",
        status: 1,
      };
      this.show_dialog = true;
      this.edit = true;
      // eslint-disable-next-line
      this.$api.getFeeDetails(id).then((res) => {
        if (!res) return;
        this.employees = res.data;
      });
    },
    delEmployees(id) {
      this.$confirm(this.$t("ConfirmToCancel") + "?", this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      })
        .then(() => {
          this.$api.delFee(id).then((res) => {
            if (!res) return;
            this.getFee();
          });
        })
        .catch(() => {});
    },
  },
};
</script>

<style lang="less" scoped>
.dispatch-order {
  padding: 0px 25px 10px;
  .conditional {
    padding: 0px 0 10px;
    display: flex;
    justify-content: space-between;
  }
  .text-tips {
    display: flex;
    .text-right {
      padding: 0 10px;
    }
  }
}
</style>
