<template>
  <!-- 货主列表 -->
  <div class="content_box">
    <!-- <el-col :span="5">
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
    <div class="conditional">
      <div>
        <el-select
          size="small"
          @keyup.enter.native="selectChange"
          v-model="pageParams.merchant_group_id"
          :placeholder="$t('MerchantGroup')"
          :clearable="true"
          class="search-bar"
        >
          <el-option
            v-for="item in MerchantGroups"
            :key="item.id"
            :label="item.name"
            :value="item.id"
          >
          </el-option>
        </el-select>
        <el-input
          clearable
          size="small"
          v-model="pageParams.code"
          @keyup.enter.native="selectChange"
          :placeholder="$t('pleaseEnterTheOwnerNumber')"
          class="search-bar"
        >
        </el-input>
        <el-input
          clearable
          size="small"
          v-model="pageParams.name"
          @keyup.enter.native="selectChange"
          :placeholder="$t('PleaseEnterNameShipper')"
          class="search-bar"
        >
        </el-input>
        <el-input
          clearable
          size="small"
          v-model="pageParams.email"
          @keyup.enter.native="selectChange"
          :placeholder="$t('pleaseInputEmail')"
          class="search-bar"
        >
        </el-input>
        <el-button
          size="small"
          @click="selectChange"
          :loading="this.$store.state.config.button_loading"
          type="primary"
          >{{ $t("Search") }}</el-button
        >
      </div>
      <div class="conditional">
        <!-- 新增 -->
        <el-tooltip
          :content="$t('AdministratorActivate')"
          placement="top"
          :disabled="hasPerm('merchant.store') ? true : false"
        >
          <div>
            <el-button
              :disabled="!hasPerm('merchant.store')"
              type="primary"
              size="small"
              @click="handlerAddMerchant"
              class="but"
              >{{ $t("Add") }}</el-button
            >
          </div>
        </el-tooltip>
        <!-- 导出 -->
        <el-tooltip
          :content="$t('AdministratorActivate')"
          placement="top"
          :disabled="hasPerm('merchant.excel') ? true : false"
        >
          <div>
            <el-button
              :disabled="!hasPerm('merchant.excel')"
              size="small"
              @click="handlerOutput"
              :loading="this.$store.state.config.button_loading"
              :class="[
                hasPerm('merchant.excel') ? 'common' : 'common-disabled',
                'but',
              ]"
              >{{ $t("Export") }}</el-button
            >
          </div>
        </el-tooltip>
      </div>
    </div>

    <el-table
      height="calc(100vh - 232px)"
      :data="hasPerm('merchant.index') ? list : []"
      v-loading="global_loading()"
      element-loading-text="loading…"
      border
      @selection-change="handleSelectionChange"
      :cell-style="{ padding: '5px 0px' }"
    >
      <template slot="empty" v-if="!hasPerm('merchant.index')">
        <div style="font-size: 18px">
          {{ $t("AdministratorActivate") }}
        </div>
      </template>
      <el-table-column
        width="55"
        type="selection"
        align="center"
        header-align="center"
      >
      </el-table-column>
      <el-table-column
        :label="$t('Operation')"
        width="120"
        align="center"
        header-align="center"
      >
        <template slot-scope="scope">
          <el-dropdown>
            <el-button size="mini" type="primary" plain class="operation">
              {{ $t("Operation") }}
              <i class="el-icon-arrow-down el-icon--right"></i>
            </el-button>
            <!-- 编辑 -->
            <el-dropdown-menu slot="dropdown">
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('merchant.update') ? true : false"
              >
                <div
                  :style="
                    hasPerm('merchant.update') ? '' : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    @click.native="editMerchantInfo(scope.row)"
                    :disabled="!hasPerm('merchant.update')"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('merchant.update')"
                      >{{ $t("Edit") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- <el-dropdown-item @click.native="handlerAPIAuth(scope.row)">{{$t('APIAuthorization')}}</el-dropdown-item> -->
              <!-- <el-dropdown-item @click.native="cashRechargeDocking(scope.row)">现金充值对接</el-dropdown-item> -->
              <!-- <el-dropdown-item @click.native="changePassWord(scope.row)">{{$t('EditPassword')}}</el-dropdown-item> -->
              <!-- 状态 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('merchant.status') ? true : false"
              >
                <div
                  :style="
                    hasPerm('merchant.status') ? '' : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('merchant.status')"
                    @click.native="handlerStatus(scope.row)"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('merchant.status')"
                      >{{
                        scope.row.status === 1 ? $t("disable") : $t("enable")
                      }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
            </el-dropdown-menu>
          </el-dropdown>
        </template>
      </el-table-column>
      <el-table-column
        prop="type_name"
        :label="$t('MerchantType')"
        align="center"
        header-align="center"
        width="120"
      >
      </el-table-column>
      <el-table-column
        prop="code"
        :label="$t('cargoOwnerNo')"
        align="center"
        header-align="center"
        width="150"
      >
      </el-table-column>
      <el-table-column
        prop="name"
        :label="$t('MerchantName')"
        align="center"
        header-align="center"
      >
      </el-table-column>
      <el-table-column
        :label="$t('ContactName')"
        prop="contacter"
        align="center"
        header-align="center"
        width="150"
      >
      </el-table-column>
      <el-table-column
        prop="phone"
        :label="$t('Tel')"
        align="center"
        header-align="center"
        width="140"
      >
      </el-table-column>
      <el-table-column
        prop="merchant_group_name"
        :label="$t('MerchantGroup')"
        align="center"
        header-align="center"
        width="180"
      >
      </el-table-column>
      <el-table-column
        prop="email"
        :label="$t('Email')"
        align="center"
        header-align="center"
        width="190"
      >
      </el-table-column>
      <el-table-column
        prop="status"
        :label="$t('Status')"
        align="center"
        header-align="center"
      >
        <template slot-scope="scope">
          <i v-if="scope.row.status === 2" class="el-icon-lock custom_lock"></i>
          {{ scope.row.status === 1 ? $t("enable") : $t("disable") }}
        </template>
      </el-table-column>
      <el-table-column
        :label="$t('SettlementMethod')"
        align="center"
        header-align="center"
        width="160"
      >
        <template slot-scope="scope">
          {{ scope.row.settlement_type_name }}
        </template>
      </el-table-column>
      <el-table-column
        prop="created_at"
        :label="$t('CreationTime')"
        align="center"
        header-align="center"
        width="160"
      >
      </el-table-column>
    </el-table>
    <div class="footer_btn">
      <div class="footer-btn-box">
        <el-button class="font_reset" size="mini" type="text"
          >{{ $t("bulkOperations") }}:</el-button
        >
        <el-tooltip
          :content="$t('AdministratorActivate')"
          placement="top"
          :disabled="hasPerm('merchant.status') ? true : false"
        >
          <div>
            <!-- 禁用 -->
            <el-button
              :class="[
                hasPerm('merchant.status')
                  ? 'footer_left_btn'
                  : 'common-disabled',
              ]"
              size="small"
              type="primary"
              @click="handlerDisenableSetting"
              :disabled="
                !multipleSelection.length && !hasPerm('merchant.status')
              "
              :loading="this.$store.state.config.button_loading"
              >{{ $t("disable") }}</el-button
            >
          </div>
        </el-tooltip>
        <!-- 启用 -->
        <el-tooltip
          :content="$t('AdministratorActivate')"
          placement="top"
          :disabled="hasPerm('merchant.status') ? true : false"
        >
          <div>
            <el-button
              :class="[
                hasPerm('merchant.status')
                  ? 'footer_left_btn'
                  : 'common-disabled',
              ]"
              size="small"
              type="primary"
              @click="handlerEnableSetting"
              :disabled="
                !multipleSelection.length && !hasPerm('merchant.status')
              "
              :loading="this.$store.state.config.button_loading"
              >{{ $t("enable") }}</el-button
            >
          </div>
        </el-tooltip>
      </div>
      <base-pagination
        class="footer_right_btn"
        ref="searchRef"
        :pageParams="pageParams"
        @search="getList"
      ></base-pagination>
    </div>

    <el-dialog :title="$t('APIAuthorization')" :visible.sync="apiDialogVisible">
      <div>
        <el-form ref="form" :model="app" label-width="160px">
          <div>
            <el-form-item label="ApiKey">
              {{ app.key }}
            </el-form-item>
            <el-form-item label="Api Secret">
              {{ app.secret }}
            </el-form-item>
            <el-form-item :label="$t('push')">
              <el-switch
                size="mini"
                v-model="app.status"
                :active-value="1"
                :inactive-value="2"
              >
              </el-switch>
            </el-form-item>
            <el-form-item :label="$t('启用现金充值')">
              <el-switch
                size="mini"
                v-model="app.recharge_status"
                :active-value="1"
                :inactive-value="2"
              >
              </el-switch>
            </el-form-item>
            <el-form-item :label="$t('pushURL')">
              <el-input size="small" v-model="app.url"></el-input>
            </el-form-item>
            <el-form-item :label="$t('IPwhitelist')">
              <el-input
                size="small"
                type="textarea"
                v-model="app.white_ip_list"
                :placeholder="`${$t('eg')}: 1.1.1.1 2.2.2.2`"
              >
              </el-input>
            </el-form-item>
          </div>
        </el-form>
      </div>
      <div slot="footer">
        <el-button size="small" @click="apiDialogVisible = false">{{
          $t("Cancel")
        }}</el-button>
        <el-button
          size="small"
          type="primary"
          @click="handlerAppKeyChange"
          :loading="this.$store.state.config.button_loading"
          >{{ $t("Submit") }}</el-button
        >
      </div>
    </el-dialog>

    <el-dialog
      :title="$t('EditPassword')"
      :visible.sync="passwordDialogVisible"
    >
      <div>
        <el-form
          :rules="pswRules"
          ref="pswForm"
          :model="form"
          label-width="160px"
        >
          <div>
            <el-form-item label="ID">
              <el-input size="small" v-model="form.id" disabled></el-input>
            </el-form-item>
            <el-form-item
              :label="$t('NewPassword')"
              prop="password"
              :error="passwordErrorInfo"
            >
              <el-input
                size="small"
                type="password"
                v-model="form.password"
                show-password
              ></el-input>
            </el-form-item>
            <el-form-item
              :label="$t('ComfirmNewPassword')"
              prop="confirm_password"
              :error="passwordErrorInfo"
            >
              <el-input
                size="small"
                type="password"
                v-model="form.confirm_password"
                show-password
              ></el-input>
            </el-form-item>
          </div>
        </el-form>
      </div>
      <div slot="footer">
        <el-button size="small" @click="passwordDialogVisible = false">{{
          $t("Cancel")
        }}</el-button>
        <el-button
          size="small"
          type="primary"
          @click="handlerChangePassWord"
          :loading="this.$store.state.config.button_loading"
          >{{ $t("Submit") }}</el-button
        >
      </div>
    </el-dialog>
  </div>
</template>

<script>
import BasePagination from "@/components/BasePagination/BasePagination";
import loading from "@/mixin/global_loading";

export default {
  name: "MerchantManagement",
  components: {
    BasePagination,
  },
  mixins: [loading],
  data() {
    return {
      multipleSelection: [],
      app: {
        key: "",
        secret: "",
        status: 1,
        url: "",
        white_ip_list: "",
        recharge_status: 2,
      },
      formData: {
        type: 1,
        name: "",
        email: "",
        settlement_type: "",
        merchant_group_id: "",
        contacter: "",
        phone: "",
        address: "",
        avatar: "",
        status: "",
      },
      form: {
        id: "",
        password: "",
        confirm_password: "",
      },
      passwordErrorInfo: "",
      form_cache: null,
      dialogVisible: false,
      apiDialogVisible: false,
      passwordDialogVisible: false,
      list: [],
      search_date: ["", ""],
      pageParams: {
        email: "",
        name: "",
        code: "",
        keyword: "",
        page: 1,
        per_page: 20,
        total: 0,
        status: "" || this.$route.query.status,
        merchant_group_id: "",
        name: "",
      },
      drivers: [],
      MerchantGroups: [],
    };
  },
  activated() {
    if (this.$route.params.MerchantManagement) {
      this.getList();
    }
  },
  created() {
    this.form_cache = JSON.parse(JSON.stringify(this.form));
    if (!Number.isNaN(this.$route.params.id)) {
      this.pageParams.merchant_group_id = this.$route.params.id;
    }
    this.getList();
    this.getMerchantGroups();
    this.getDrivers();
  },
  computed: {
    pswRules() {
      return {
        password: [
          {
            required: true,
            trigger: "blur",
            message: this.$t("Required"),
          },
        ],
        confirm_password: [
          {
            required: true,
            trigger: "blur",
            message: this.$t("Required"),
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
        this.getList();
      },
    },
    passwordDialogVisible(oldVal) {
      if (!oldVal) {
        this.form = JSON.parse(JSON.stringify(this.form_cache));
      }
    },
  },
  methods: {
    handlerCopy() {
      document.execCommand("copy");
    },
    getMerchantGroups() {
      this.$api
        .getMerchantsGroup({
          per_page: 200,
        })
        .then((res) => {
          if (!res) return;
          this.MerchantGroups = res.data.data;
        });
    },
    handlerDisenableSetting() {
      if (!this.multipleSelection.length) return;
      this.$api
        .multMerchantsSetting({
          status: 2,
          ids: JSON.stringify(this.multipleSelection.map((item) => item.id)),
        })
        .then((res) => {
          if (!res) return;
          this.getList();
        });
    },
    handlerEnableSetting() {
      if (!this.multipleSelection.length) return;
      this.$api
        .multMerchantsSetting({
          status: 1,
          ids: JSON.stringify(this.multipleSelection.map((item) => item.id)),
        })
        .then((res) => {
          if (!res) return;
          this.getList();
        });
    },
    // 选中
    handleSelectionChange(val) {
      this.multipleSelection = val;
    },
    async handlerSetting(data) {
      const res = await this.$api.multMerchantsSetting(data);
      return res;
    },
    handlerChangePassWord() {
      if (this.form.password !== this.form.confirm_password) {
        // this.$message({
        //   type: 'error',
        //   message: '密码不一致!'
        // });
        this.passwordErrorInfo = "两次密码输入不一致!";
        return;
      } else {
        this.passwordErrorInfo = null;
      }

      this.$refs.pswForm.validate((valid) => {
        if (!valid) return;
        this.$api.editMerchantsPsw(this.form.id, this.form).then((res) => {
          if (!res) return;
          this.passwordDialogVisible = !this.passwordDialogVisible;
          this.getList();
        });
      });
    },
    handlerAppKeyChange() {
      this.$api
        .editAppKeyDetial(this.app.id, {
          url: this.app.url,
          white_ip_list: this.app.white_ip_list,
          status: this.app.status,
          recharge_status: this.app.recharge_status,
        })
        .then((res) => {
          if (!res) return;
          this.apiDialogVisible = !this.apiDialogVisible;
          this.getList();
        });
    },
    handlerStatus(row) {
      this.$api
        .disableMerchants(row.id, {
          status: row.status === 1 ? 2 : 1,
        })
        .then((res) => {
          if (!res) return;
          this.getList();
        });
    },
    changePassWord(row) {
      this.passwordDialogVisible = !this.passwordDialogVisible;
      this.form.id = row.id;
    },
    handlerAPIAuth(row) {
      this.apiDialogVisible = !this.apiDialogVisible;
      this.$api.getAppKeyDetial(row.id).then((res) => {
        this.app = res.data;
      });
    },
    editMerchantInfo(row) {
      this.$router.push({
        name: "editMerchant",
        query: {
          id: row.id,
          merchant_id: row.merchant_group_id,
        },
      });
    },
    handleRemove(file, fileList) {
      console.log(file, fileList);
    },
    handlePictureCardPreview(file) {
      this.dialogImageUrl = file.url;
      this.dialogVisible = true;
    },
    handlerOutput() {
      this.$confirm(this.$t("AreSureWantExport"), this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      }).then(() => {
        this.$api.outputMerchantExcel(this.pageParams).then((res) => {
          if (!res) return;
          const a = document.createElement("a");
          a.href = res.data.path;
          a.click();
        });
      });
    },
    handlerAddMerchant() {
      this.$router.push({
        name: "addMerchantManagement",
      });
    },
    selectChange() {
      if (this.pageParams.name || this.pageParams.merchant_group_id) {
        this.pageParams.page = 1;
      }
      this.getList();
    },
    getDrivers() {
      this.$api.getDriversPublic().then((res) => {
        this.drivers = res.data.data;
      });
    },
    handlerTabClick() {
      this.$route.query.page = 1;
      this.pageParams.page = 1;
      this.$refs.searchRef.onChangeValue("status", this.pageParams.status);
    },
    toStationRecord(row) {
      this.$router.push({
        name: "stationDetail",
        query: {
          id: row.id,
        },
      });
    },
    // 列表
    getList() {
      this.$api.getMerchants(this.pageParams).then((res) => {
        this.list = res.data.data;
        this.pageParams.total = res.data.meta.total;
        this.pageParams.page = res.data.meta.current_page;
      });
    },

    cashRechargeDocking(row) {
      // 现金充值对接
      this.$router.push({ name: "CashRechargeDocking" });
    },
  },
};
</script>
<style lang="less" scoped>
.content_box {
  padding: 20px 25px 10px;
  background-color: #fff;
  position: relative;
  .conditional {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    .but {
      margin-left: 5px;
      // float: right;
    }
  }
  .footer_btn {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
    .footer-btn-box {
      display: flex;
      gap: 5px;
    }
  }
  .font_reset {
    color: #606266;
    font-size: 13px;
    font-weight: 400;
  }
  .custom_lock {
    color: red;
  }
}
</style>
