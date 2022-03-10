<template>
  <!-- 支付配置 -->
  <div class="payment-configuration">
    <h3>{{ $t("OrderPaymentWaitingTime") }}</h3>
    <p>{{ $t("ConfigureOrderAutomatically") }}</p>
    <el-form :model="form">
      <el-form-item :label="$t('WaitingTime')">
        <el-input v-model="form.waiting_time" style="width: 200px"></el-input> s
        <el-tooltip
          class="item"
          effect="dark"
          :content="$t('NoMoreThanTwoHours')"
          placement="top-start"
        >
          <span class="el-icon-warning icon-question"></span>
        </el-tooltip>
      </el-form-item>
      <h3>{{ $t("OnlinePayments") }}</h3>
      <el-form-item>
        <el-table :data="tableData" border style="width: 100%">
          <el-table-column type="index" align="center" width="50">
            <template slot="header">
              <span>{{ $t("Number") }}</span>
            </template>
          </el-table-column>
          <el-table-column
            prop="date"
            :label="$t('Operation')"
            align="center"
            width="120"
          >
            <template slot-scope="scope">
              <!-- 操作 -->
              <el-dropdown size="mini" trigger="hover" placement="bottom">
                <el-button size="mini" type="primary" plain class="operation">
                  {{ $t("Operation") }}
                  <i class="el-icon-arrow-down el-icon--right"></i>
                </el-button>
                <el-dropdown-menu slot="dropdown">
                  <!-- 配置 -->
                  <el-tooltip
                    :content="$t('AdministratorActivate')"
                    placement="top"
                    :disabled="
                      hasPerm('employees.reset-password') ? true : false
                    "
                  >
                    <div
                      :style="
                        hasPerm('employees.reset-password')
                          ? ''
                          : 'cursor: not-allowed'
                      "
                    >
                      <el-dropdown-item
                        @click.native="configuration(scope.row.id)"
                        :disabled="!hasPerm('employees.reset-password')"
                      >
                        <el-button
                          class="operation"
                          size="small"
                          type="text"
                          :disabled="!hasPerm('employees.reset-password')"
                          >{{ $t("configuration") }}</el-button
                        >
                      </el-dropdown-item>
                    </div>
                  </el-tooltip>
                </el-dropdown-menu>
              </el-dropdown>
            </template>
          </el-table-column>
          <el-table-column
            prop="type"
            align="center"
            :label="$t('MerchantType')"
          >
          </el-table-column>
          <el-table-column
            prop="paypal_status"
            align="center"
            :label="$t('whetherToEnable')"
          >
            <template slot-scope="scope">
              <el-switch
                v-model="scope.row.paypal_status"
                :active-value="1"
                :inactive-value="2"
                @change="changeSwitch(scope.row)"
              >
              </el-switch>
            </template>
          </el-table-column>
        </el-table>
      </el-form-item>
      <el-form-item>
        <el-tooltip
          :content="$t('AdministratorActivate')"
          placement="top"
          :disabled="hasPerm('pay-config.update') ? true : false"
          style="width: 70px"
        >
          <el-button
            type="primary"
            @click="onSubmit"
            :disabled="!hasPerm('pay-config.update')"
            :class="[
              hasPerm('pay-config.update') ? 'submit' : 'submit-disabled',
            ]"
            >{{ $t("Submit") }}
          </el-button>
        </el-tooltip>
      </el-form-item>
      <!-- 点击paypald的配置 -->
      <el-dialog :title="$t('configuration')" :visible.sync="dialogPaypald">
        <el-form-item :label="$t('SandboxMode')" prop="type">
          <el-select
            v-model="form.paypal_sandbox_mode"
            filterable
            v-if="!$route.query.id"
            style="width: 100%"
          >
            <el-option
              v-for="item in sandboxModeList"
              :key="item.id"
              :label="item.name"
              :value="item.id"
            ></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="Client ID">
          <el-input v-model="form.paypal_client_id"></el-input>
        </el-form-item>
        <el-form-item label="Secret">
          <el-input v-model="form.paypal_client_secret"></el-input>
        </el-form-item>
        <!-- <el-form-item :label="$t('Account')">
          <el-input v-model="form.name"></el-input>
        </el-form-item> -->
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogPaypald = false">{{
            $t("Cancel")
          }}</el-button>
          <el-button type="primary" @click="dialogPaypald = false">{{
            $t("Confirm")
          }}</el-button>
        </div>
      </el-dialog>
    </el-form>
  </div>
</template>

<script>
export default {
  data() {
    return {
      dialogPaypald: false,
      dialogWechat: false,
      form: {},
      formDialog: {},
      list: [],
      sandboxModeList: [
        { id: 1, name: this.$t("Yes") },
        { id: 2, name: this.$t("No") },
      ],
      tableData: [
        {
          type: "paypald",
          paypal_status: 2,
        },
      ],
    };
  },
  created() {
    this.getPayConfig();
  },
  methods: {
    getPayConfig() {
      this.$api.getPayConfig().then((res) => {
        if ((res.code = 200)) {
          this.form = res.data;
          this.tableData.map((item) => {
            item.paypal_status = res.data.paypal_status;
          });
        }
      });
    },
    onSubmit() {
      this.$api.putPayConfig(this.form).then((res) => {
        if (res.code == 200) {
          this.getPayConfig();
        }
      });
    },
    changeSwitch(row) {
      this.form = {
        ...this.form,
        paypal_status: row.paypal_status,
      };
    },
    configuration() {
      this.dialogPaypald = true;
    },
  },
};
</script>

<style lang="less" scoped>
.payment-configuration {
  padding: 0px 20px;
  p {
    color: #b6b6b6;
  }
  .el-icon-warning {
    color: #efb336;
    margin-left: 10px;
  }
}
</style>
