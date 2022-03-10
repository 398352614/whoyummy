<template>
  <div class="content-box">
    <div class="search">
      <div>
        <el-input
          clearable
          @keyup.enter.native="getReloadList"
          size="small"
          v-model="pageParams.code"
          :placeholder="$t('pleaseEnterTheOwnerNumber')"
          class="search-bar"
        >
        </el-input>
        <el-input
          clearable
          @keyup.enter.native="getReloadList"
          size="small"
          v-model="pageParams.name"
          :placeholder="$t('PleaseEnterNameShipper')"
          class="search-bar"
        >
        </el-input>
        <el-button
          size="small"
          @click="getReloadList"
          :loading="this.$store.state.config.button_loading"
          type="primary"
          >{{ $t("Search") }}</el-button
        >
      </div>
      <el-tooltip
        :content="$t('AdministratorActivate')"
        placement="top"
        :disabled="hasPerm('merchant-api.store') ? true : false"
      >
        <div>
          <el-button
            :disabled="!hasPerm('merchant-api.store')"
            type="primary"
            size="small"
            @click="addNewMember"
            >{{ $t("Add") }}</el-button
          >
        </div>
      </el-tooltip>
    </div>

    <el-table
      height="calc(100vh - 232px)"
      :data="hasPerm('merchant-api.index') ? list : []"
      v-loading="global_loading()"
      element-loading-text="loading…"
      border
      :cell-style="{ padding: '5px 0px' }"
    >
      <template slot="empty" v-if="!hasPerm('merchant-api.index')">
        <div style="font-size: 18px">
          {{ $t("AdministratorActivate") }}
        </div>
      </template>
      <el-table-column
        type="selection"
        width="55"
        align="center"
        header-align="center"
      >
      </el-table-column>
      <el-table-column
        :label="$t('Operation')"
        align="center"
        header-align="center"
        width="120px"
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
                :disabled="hasPerm('merchant-api.update') ? true : false"
              >
                <div
                  :style="
                    hasPerm('merchant-api.update') ? '' : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('merchant-api.update')"
                    @click.native="getMerchantApiDetails(scope.row.id)"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('merchant-api.update')"
                      >{{ $t("Edit") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 状态 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('merchant-api.status') ? true : false"
              >
                <div
                  :style="
                    hasPerm('merchant-api.status') ? '' : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('merchant-api.status')"
                    @click.native="editStatus(scope.row)"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('merchant-api.status')"
                      >{{
                        scope.row.status == 1 ? $t("disable") : $t("enable")
                      }}</el-button
                    ></el-dropdown-item
                  >
                </div>
              </el-tooltip>
              <!-- 删除 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('merchant-api.destroy') ? true : false"
              >
                <div
                  :style="
                    hasPerm('merchant-api.destroy') ? '' : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('merchant-api.destroy')"
                    @click.native="handlerDel(scope.row)"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('merchant-api.destroy')"
                      >{{ $t("Delete") }}</el-button
                    ></el-dropdown-item
                  >
                </div>
              </el-tooltip>
            </el-dropdown-menu>
          </el-dropdown>
        </template>
      </el-table-column>
      <!-- <el-table-column
        prop="name"
        :label="$t('启用/禁用')"
        align="center"
        header-align="center"
      >
      </el-table-column> -->
      <el-table-column
        prop="merchant_id_code"
        :label="$t('cargoOwnerNo')"
        align="center"
        header-align="center"
      >
      </el-table-column>
      <el-table-column
        prop="merchant_id_name"
        :label="$t('Merchant')"
        align="center"
        header-align="center"
      >
      </el-table-column>
      <el-table-column
        prop="key"
        :label="$t('Api Key')"
        align="center"
        header-align="center"
      >
      </el-table-column>
      <el-table-column
        prop="secret"
        :label="$t('Api Secret')"
        align="center"
        header-align="center"
        width="350"
      >
      </el-table-column>
      <el-table-column
        prop="status_name"
        :label="$t('DisableEnable')"
        align="center"
        header-align="center"
      >
        <template slot-scope="scope">
          {{ scope.row.status == 1 ? $t("enable") : $t("disable") }}
        </template>
      </el-table-column>
    </el-table>
    <base-pagination
      class="pagination-size"
      ref="searchRef"
      :pageParams="pageParams"
      @search="getList"
    ></base-pagination>

    <el-dialog :title="$t('Add')" :visible.sync="addUserDialog">
      <div>
        <div style="text-align: right">
          <el-input
            clearable
            @clear="reloadParamsSearch"
            size="small"
            v-model="userParams.keyword"
            :placeholder="$t('cargoOwnerNoMerchantEmail')"
            class="new_add_user_input"
          >
            <el-button
              @click="reloadParamsSearch"
              slot="append"
              icon="el-icon-search"
            ></el-button>
          </el-input>
        </div>
        <el-table
          max-height="600px"
          :data="userList"
          border
          @select="handleSelect"
          @select-all="handleSelect"
        >
          <el-table-column type="selection" align="center" width="50">
          </el-table-column>
          <el-table-column
            prop="type_name"
            :label="$t('MerchantType')"
            align="center"
            header-align="center"
            width="150"
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
            width="150"
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
            width="150"
          >
          </el-table-column>
          <el-table-column
            prop="email"
            :label="$t('Email')"
            align="center"
            header-align="center"
            width="180"
          >
          </el-table-column>
          <el-table-column
            prop="created_at"
            :label="$t('Created')"
            align="center"
            header-align="center"
            width="160"
          >
          </el-table-column>
        </el-table>
        <base-pagination
          class="footer_right_btn"
          ref="searchRef"
          :pageParams="userParams"
          @search="getMerchants"
        ></base-pagination>
        <div style="text-align: right; margin-top: 10px">
          <el-button @click="addUserDialog = false" class="cancel">{{
            $t("Cancel")
          }}</el-button>
          <el-button
            type="primary"
            :disabled="!sele_ids.length"
            @click="addMerchantApi()"
            class="submit"
            >{{ $t("Confirm") }}</el-button
          >
        </div>
      </div>
    </el-dialog>

    <el-dialog :title="$t('Edit')" :visible.sync="editDialog">
      <el-form :rules="formRules" ref="form" :model="form" label-width="200px">
        <el-form-item :label="$t('ApiKey')">
          {{ form.key }}
        </el-form-item>
        <el-form-item :label="$t('Api Secret')">
          {{ form.secret }}
        </el-form-item>
        <el-form-item :label="$t('push')" prop="status">
          <el-switch
            v-model="form.status"
            :active-value="1"
            :inactive-value="2"
          >
          </el-switch>
        </el-form-item>
        <el-form-item :label="$t('cashRecharge')" prop="recharge_status">
          <el-switch
            v-model="form.recharge_status"
            :active-value="1"
            :inactive-value="2"
          >
          </el-switch>
        </el-form-item>
        <el-form-item :label="$t('pushURL')" prop="url">
          <el-input v-model="form.url"></el-input>
        </el-form-item>
        <el-form-item :label="this.$t('IPwhitelist')">
          <el-input
            v-model="form.white_ip_list"
            type="textarea"
            placeholder="示例: 1.1.1.1 2.2.2.2"
            :rows="2"
          ></el-input>
        </el-form-item>
        <!-- <el-form-item label="" prop="name">
          <p>一个IP地址为一行，多个请按回车，不填为代表所有</p>
          <p>例如:</p>
          <p>1.1.1.1</p>
          <p>2.2.2.2</p>
        </el-form-item> -->
      </el-form>
      <span slot="footer">
        <el-button @click="editDialog = false" class="cancel">{{
          $t("Cancel")
        }}</el-button>
        <el-button type="primary" @click="onSubmit()" class="submit">{{
          $t("Confirm")
        }}</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import BasePagination from "@/components/BasePagination/BasePagination";
import loading from "@/mixin/global_loading";

export default {
  name: "APIDockingManagement",
  components: {
    BasePagination,
  },
  mixins: [loading],
  data() {
    return {
      list: [],
      pageParams: {
        name: "",
        code: "",
        keyword: "",
        page: 1,
        per_page: 20,
        total: 0,
      },

      addUserDialog: false,
      userList: [],
      userParams: {
        keyword: "",
        page: 1,
        per_page: 10,
        total: 0,
      },
      sele_ids: [],

      editDialog: false,
      formRules: {
        status: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        recharge_status: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        url: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        white_ip_list: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
      },
      form: {
        status: "",
        recharge_status: "",
        url: "",
        white_ip_list: "",
      },
      edit_id: null,
    };
  },
  created() {
    this.getList();
  },
  methods: {
    getReloadList() {
      // 重置筛选条件
      this.pageParams.page = 1;
      this.getList();
    },

    getList() {
      // 列表
      this.$api.getMerchantApiList(this.pageParams).then((res) => {
        if (!res) return;
        this.list = res.data.data;
        this.pageParams.total = res.data.meta.total;
        this.pageParams.page = res.data.meta.current_page;
      });
    },

    // 新增--开始
    addNewMember() {
      // 打开新增弹窗
      this.addUserDialog = true;
      this.getMerchants();
    },

    reloadParamsSearch() {
      // 重置筛选条件
      this.userParams.page = 1;
      this.getMerchants();
    },

    getMerchants() {
      // 列表
      this.$api.getMerchantsPublic(this.userParams).then((res) => {
        if (!res) return;
        this.userList = res.data.data;
        this.userParams.total = res.data.meta.total;
        this.userParams.page = res.data.meta.current_page;
      });
    },

    handleSelect(sele, row = {}) {
      // 选中列表项
      this.sele_ids = sele.map((el) => {
        return el.id;
      });
    },

    addMerchantApi() {
      // 新增
      const ids = this.sele_ids.join(",");
      this.$api.addMerchantApi({ merchant_id: ids }).then((res) => {
        if (res.code == 200) {
          this.addUserDialog = false;
          this.sele_ids = [];
          this.getReloadList();
        }
      });
    },
    // 新增--结束

    handlerEdit(row) {
      // 编辑
      this.$router.replace({
        name: "EditAPIDocking",
        query: {
          id: row.id,
        },
      });
    },

    handlerDel(row) {
      // 删除
      this.$confirm(
        this.$t("WhetherDeleteTheConfiguration") + "?",
        this.$t("Notice"),
        {
          confirmButtonText: this.$t("Confirm"),
          cancelButtonText: this.$t("Cancel"),
          type: "warning",
        }
      )
        .then(() => {
          this.$api.delMerchantApi(row.id).then((res) => {
            if (res.code === 200) {
              this.getReloadList();
            } else {
              this.$message({
                message: res.msg,
                type: "error",
              });
            }
          });
        })
        .catch(() => {});
    },

    getMerchantApiDetails(id) {
      // 获取详情
      this.editDialog = true;
      this.edit_id = id;
      this.$api.getMerchantApiDetails(id).then((res) => {
        if (!res) return;
        this.form = res.data;
      });
    },

    editStatus(row) {
      // 修改启用状态
      const data = {
        id: row.id,
        status: row.status == 1 ? 2 : 1,
      };
      this.$api.putMerchantStatus(data).then((res) => {
        if (res.code == 200) {
          this.getList();
        }
      });
    },

    onSubmit() {
      // 提交
      this.$refs.form.validate((valid) => {
        if (!valid) return;
        this.$api.putMerchantApi(this.edit_id, this.form).then((res) => {
          if (res.code !== 200) return;
          this.editDialog = false;
          this.form = {};
          this.getList();
        });
        return;
      });
    },
  },
};
</script>
<style lang="less" scoped>
.content-box {
  padding: 20px 25px 10px;
  background-color: #fff;
  position: relative;
  .search {
    margin: 0 0 10px 0;
    display: flex;
    justify-content: space-between;
  }
  .footer_left_btn {
    width: 142px;
    height: 32px;
  }
  .new_add_user_input {
    max-width: 328px;
    margin-bottom: 10px;
  }
}
</style>
