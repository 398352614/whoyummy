<template>
  <!-- 信用额度 -->
  <div class="cash-recharge-list">
    <div class="conditional">
      <span style="margin-top: 6px">{{ $t("RegistrationTime") }} </span>
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
      <!-- 货主编号 -->
      <el-input
        @keyup.enter.native="handleChangePageParams"
        v-model="pageParams.code"
        clearable
        :placeholder="$t('pleaseEnterTheOwnerNumber')"
        size="small"
        class="search-bar"
      >
      </el-input>
      <!-- 货主组 -->
      <el-select
        size="small"
        @keyup.enter.native="handleChangePageParams"
        v-model="pageParams.merchant_group_id"
        :placeholder="$t('MerchantGroup')"
        :clearable="true"
        class="search-bar"
      >
        <el-option
          v-for="item in merchantsList"
          :key="item.id"
          :label="item.name"
          :value="item.id"
        >
        </el-option>
      </el-select>
      <el-button
        size="small"
        @click="handleChangePageParams"
        type="primary"
        :loading="this.$store.state.config.button_loading"
        >{{ $t("Search") }}</el-button
      >
    </div>
    <el-table
      :data="hasPerm('ledger.index') ? tableData : []"
      border
      ref="table"
      height="calc(100vh - 235px)"
      :summary-method="getSummaries"
      show-summary
      style="width: 100%"
      v-loading="global_loading()"
      element-loading-text="loading..."
      :cell-style="{ padding: '5px 0px' }"
    >
      <template slot="empty" v-if="!hasPerm('ledger.index')">
        <div style="font-size: 18px">
          {{ $t("AdministratorActivate") }}
        </div>
      </template>
      <el-table-column type="index" align="center" width="55">
        <template slot="header">
          <span>{{ $t("Number") }}</span>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        prop="date"
        :label="$t('Operation')"
        width="120"
      >
        <template slot-scope="scope">
          <el-dropdown size="small" trigger="hover" placement="bottom">
            <el-button size="mini" type="primary" plain class="operation">
              {{ $t("Operation") }}
              <i class="el-icon-arrow-down el-icon--right"></i>
            </el-button>
            <el-dropdown-menu slot="dropdown">
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('ledger.update') ? true : false"
              >
                <div
                  :style="hasPerm('ledger.update') ? '' : 'cursor: not-allowed'"
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('ledger.update')"
                    @click.native="getSetQuota(scope.row)"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('ledger.update')"
                      >{{ $t("SetQuota") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('ledger.log') ? true : false"
              >
                <div
                  :style="hasPerm('ledger.log') ? '' : 'cursor: not-allowed'"
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('ledger.log')"
                    @click.native="getQuotaRecord(scope.row.id)"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('ledger.log')"
                      >{{ $t("QuotaRecord") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
            </el-dropdown-menu>
          </el-dropdown>
        </template>
      </el-table-column>
      <el-table-column align="center" prop="name" :label="$t('Merchant')">
      </el-table-column>
      <el-table-column align="center" prop="code" :label="$t('cargoOwnerNo')">
      </el-table-column>
      <el-table-column
        align="center"
        prop="credit"
        :label="$t('Credits') + '(' + currency_unit_symbol + ')'"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="balance"
        :label="$t('AccountBalance') + '(' + currency_unit_symbol + ')'"
      >
      </el-table-column>
      <el-table-column align="center" prop="phone" :label="$t('PhoneNumber')">
      </el-table-column>
      <el-table-column align="center" prop="email" :label="$t('Email')">
      </el-table-column>
      <!-- 是否允许超额 -->
      <el-table-column
        align="center"
        prop="status_name"
        :label="$t('WhetherAllowExcess')"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="merchant_group_name"
        :label="$t('UserGroup')"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="created_at"
        :label="$t('RegistrationTime')"
      >
      </el-table-column>
    </el-table>

    <div>
      <base-pagination
        :pageParams="pageParams"
        @search="getCredits"
        class="pagination-size"
      ></base-pagination>
    </div>

    <!-- 设置额度 -->
    <div>
      <el-dialog :title="$t('SetQuota')" :visible.sync="dialogFormVisible">
        <el-form :model="form" :rules="rules" ref="form" label-width="120px">
          <el-form-item :label="$t('cargoOwnerNo')">
            {{ form.code }}
          </el-form-item>
          <el-form-item :label="$t('SetQuota')" prop="credit">
            <el-input
              v-model.number="form.credit"
              autocomplete="off"
              style="width: 80%"
              :placeholder="$t('PleaseEnterSetAmount')"
            ></el-input>
          </el-form-item>
          <el-form-item :label="$t('WhetherAllowExcess')" prop="status">
            <el-switch
              v-model="form.status"
              active-color="#13ce66"
              :active-value="1"
              :inactive-value="2"
            >
            </el-switch>
          </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="cancel" class="cancel">
            {{ $t("Cancel") }}</el-button
          >
          <el-button type="primary" @click="confirm" class="submit">
            {{ $t("Confirm") }}</el-button
          >
        </div>
      </el-dialog>
    </div>
    <!-- 额度记录 -->
    <div>
      <el-dialog
        :title="$t('QuotaRecord')"
        :visible.sync="quotaRecordDateVisible"
        width="60%"
      >
        <el-table :data="quotaRecordDate" border height="500px">
          <el-table-column type="index" align="center" width="50">
            <template slot="header">
              <span>{{ $t("Number") }}</span>
            </template>
          </el-table-column>
          <el-table-column
            prop="user_code"
            align="center"
            :label="$t('cargoOwnerNo')"
          ></el-table-column>
          <el-table-column
            prop="credit"
            align="center"
            :label="$t('SetQuota')"
          ></el-table-column>
          <el-table-column
            prop="created_at"
            align="center"
            :label="$t('Created')"
          ></el-table-column>
          <el-table-column
            prop="operator_name"
            align="center"
            :label="$t('operator')"
          ></el-table-column>
        </el-table>
      </el-dialog>
    </div>
  </div>
</template>

<script>
import BasePagination from "@/components/BasePagination/BasePagination";
import loading from "@/mixin/global_loading";

export default {
  name: "CashRechargeList",
  components: {
    BasePagination,
  },
  mixins: [loading],
  data() {
    // 数值范围
    var valiNumberPass = (rule, value, callback) => {
      //包含小数的数字
      let reg = /^[+-]?(0|([1-9]\d*))(\.\d+)?$/g;
      if (value === "") {
        callback(new Error(this.$t("Required")));
      } else if (!reg.test(value)) {
        callback(new Error(this.$t("TheAmountMustBeGreater")));
      } else if (value > 1000000) {
        callback(new Error(this.$t("TheAmountMustBeGreater")));
      } else {
        if (value < 0) {
          callback(new Error(this.$t("TheAmountMustBeGreater")));
        } else {
          callback();
        }
      }
    };
    return {
      currency_unit_symbol: "", //单位
      dialogFormVisible: false, //设置额度
      quotaRecordDateVisible: false, //额度记录
      tableData: [], //表格
      merchantsList: [], //货主组
      quotaRecordDate: [
        {
          name: 11,
        },
      ],
      pageParams: {
        user_type: 4,
        code: "",
        end_date: "",
        begin_date: "",
        merchant_group_id: "",
        total: 0,
        page: 1,
        per_page: 20,
      },
      form: {
        id: "",
        credit: "",
        status: "2",
      },
      rules: {
        credit: [
          { required: true, validator: valiNumberPass, trigger: "blur" },
        ],
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
  // 表格合计不展示问题
  updated() {
    this.$nextTick(() => {
      this.$refs["table"].doLayout();
    });
  },
  created() {
    this.getCredits();
    this.getMerchantsGroup();
    this.getAdvanceSetting(); //单位
  },
  methods: {
    // 合计
    getSummaries(param) {
      const { columns, data } = param;
      const sums = [];
      columns.forEach((column, index) => {
        if (index === 0) {
          sums[index] = this.$t("Total");
        } else if (index === 4 || index === 5) {
          const values = data.map((item) => Number(item[column.property]));
          if (!values.every((value) => isNaN(value))) {
            sums[index] = values.reduce((prev, curr) => {
              const value = Number(curr).toFixed(2);
              if (!isNaN(value)) {
                return prev + curr;
              } else {
                return prev;
              }
            }, 0);
          } else {
            // sums[index] = 'N/A'
          }
        } else {
          sums[index] = "";
        }
      });
      sums[4] = sums[4] ? sums[4].toFixed(2) : "0.00";
      sums[5] = sums[5] ? sums[5].toFixed(2) : "0.00";

      return sums;
    },
    // 获取单位值
    getAdvanceSetting() {
      this.$api.getAdvanceSetting().then((res) => {
        this.currency_unit_symbol = res.data.currency_unit_symbol; // 单价单位
      });
    },
    // 货主组
    getMerchantsGroup() {
      // 货主列表
      this.$api.getMerchantsGroup().then((res) => {
        this.merchantsList = res.data.data;
      });
    },
    // 列表
    getCredits() {
      this.$api.getCredits(this.pageParams).then((res) => {
        this.tableData = res.data.data;
        this.pageParams.total = res.data.meta.total;
      });
    },
    // 查询
    handleChangePageParams() {
      this.pageParams.page = 1;
      this.getCredits();
    },
    getSetQuota(rows) {
      this.dialogFormVisible = true;
      this.form = {
        ...this.form,
        id: rows.id,
        code: rows.code,
        credit: rows.credit,
        status: rows.status,
      };
      // this.$refs.form.resetFields();
    },
    // 确定
    confirm() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          this.$api.getSetQuota(this.form).then((res) => {
            if (res.code == 200) {
              this.dialogFormVisible = false;
              this.getCredits();
            }
          });
        }
      });
    },
    // 取消
    cancel() {
      this.dialogFormVisible = false;
      this.$refs.form.resetFields();
    },
    getQuotaRecord(id) {
      this.quotaRecordDateVisible = true;
      this.$api.getQuotaRecord(id).then((res) => {
        this.quotaRecordDate = res.data;
      });
    },
    getReview(id) {
      // 审核
      this.$router.push({ name: "FinancialExamine", query: { id: id } });
    },
  },
};
</script>
<style lang="less" scoped>
.cash-recharge-list {
  padding: 20px 25px 10px;
  background: #ffffff;
  border: 1px solid rgba(228, 235, 241, 1);
  .conditional {
    padding: 0px 0 10px;
  }
}
</style>
<style lang="less">
.cash-recharge-list {
  .conditional {
    .el-input__inner {
      border-radius: 0;
    }
  }
}
</style>
