<template>
  <!-- 员工列表 -->
  <div class="dispatch-order">
    <div class="conditional">
      <div>
        <!-- 账号 -->
        <el-input
          size="small"
          clearable
          @keyup.enter.native="handlerSearch"
          v-model="pageParams.email"
          :placeholder="$t('EnterAccount')"
          class="search-bar"
        ></el-input>
        <!-- 姓名 -->
        <el-input
          size="small"
          clearable
          @keyup.enter.native="handlerSearch"
          v-model="pageParams.fullname"
          :placeholder="$t('EnterName')"
          class="search-bar"
        ></el-input>

        <!-- 所属网点 -->
        <el-select
          size="small"
          clearable
          v-model="pageParams.warehouse_id"
          @keyup.enter.native="handlerSearch"
          :placeholder="$t('AffiliatedBranches')"
          class="search-bar"
        >
          <el-option
            v-for="(item, index) in warehouseList"
            :key="index"
            :label="item.name"
            :value="item.id"
          ></el-option>
        </el-select>

        <!-- 所属权限组 -->
        <el-select
          size="small"
          clearable
          v-model="pageParams.role_id"
          @keyup.enter.native="handlerSearch"
          :placeholder="$t('owningPermissionGroup')"
          class="search-bar"
        >
          <el-option
            v-for="(item, index) in roleList"
            :key="index"
            :label="item.name"
            :value="item.id"
          ></el-option>
        </el-select>

        <!-- 查询 -->
        <el-button
          size="small"
          type="primary"
          @click="handlerSearch"
          :loading="this.$store.state.config.button_loading"
          >{{ $t("Search") }}</el-button
        >
      </div>
      <div>
        <!-- 添加员工 -->
        <el-tooltip
          :content="$t('AdministratorActivate')"
          placement="top"
          :disabled="hasPerm('employees.store') ? true : false"
        >
          <div>
            <el-button
              :disabled="!hasPerm('employees.store')"
              size="small"
              type="primary"
              @click="addDialog"
              >{{ $t("Add") }}</el-button
            >
          </div>
        </el-tooltip>
      </div>
    </div>
    <el-table
      :data="hasPerm('employees.index') ? tableData : []"
      v-loading="global_loading()"
      element-loading-text="loading..."
      border
      style="width: 100%"
      height="calc(100vh - 239px)"
      ref="multipleSelection"
      @selection-change="handleSelectionChange"
      :cell-style="{ padding: '5px 0px' }"
    >
      <template slot="empty" v-if="!hasPerm('employees.index')">
        <div style="font-size: 18px">
          {{ $t("AdministratorActivate") }}
        </div>
      </template>
      <el-table-column align="center" type="selection" width="50">
      </el-table-column>
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
              <!-- 编辑员工 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('employees.update') ? true : false"
              >
                <div
                  :style="
                    hasPerm('employees.update') ? '' : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('employees.update')"
                    @click.native="getEmployeesDetails(scope.row.id)"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('employees.update')"
                      >{{ $t("Edit") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 删除员工-->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('employees.destroy') ? true : false"
              >
                <div
                  :style="
                    hasPerm('employees.destroy') ? '' : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    @click.native="delEmployees(scope.row.id)"
                    :disabled="!hasPerm('employees.destroy')"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('employees.destroy')"
                      >{{ $t("Delete") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 修改密码 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('employees.reset-password') ? true : false"
              >
                <div
                  :style="
                    hasPerm('employees.reset-password')
                      ? ''
                      : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    @click.native="EditPassword(scope.row.id)"
                    :disabled="!hasPerm('employees.reset-password')"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('employees.reset-password')"
                      >{{ $t("EditPassword") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
            </el-dropdown-menu>
          </el-dropdown>
        </template>
      </el-table-column>
      <!-- 启用状态 -->
      <el-table-column
        header-align="center"
        align="center"
        prop="forbid_login_name"
        :label="$t('enabledState')"
      >
      </el-table-column>
      <el-table-column
        header-align="center"
        align="center"
        prop="email"
        :label="$t('Account')"
      >
      </el-table-column>
      <!-- 所属网点 -->
      <el-table-column
        header-align="center"
        align="center"
        prop="warehouse_name"
        :label="$t('AffiliatedBranches')"
      >
      </el-table-column>
      <el-table-column
        header-align="center"
        align="center"
        prop="fullname"
        :label="$t('Name')"
      >
      </el-table-column>
      <el-table-column
        header-align="center"
        align="center"
        prop="remark"
        :label="$t('StaffRemark')"
      >
      </el-table-column>
      <el-table-column
        header-align="center"
        align="center"
        prop="role_id_name"
        :label="$t('Role')"
      >
      </el-table-column>
    </el-table>
    <!-- 状态 -->
    <div class="footer-btn">
      <div style="display: flex; gap: 5px">
        <el-tooltip
          :content="$t('AdministratorActivate')"
          placement="top"
          :disabled="hasPerm('employees.set-login') ? true : false"
        >
          <div>
            <el-button
              size="small"
              @click="handlerEnableSetting"
              :disabled="
                !multipleSelection.length && !hasPerm('employees.set-login')
              "
              :loading="this.$store.state.config.button_loading"
              :class="[
                hasPerm('employees.set-login') && !multipleSelection.length
                  ? 'footer_left_btn'
                  : 'common-disabled',
              ]"
              >{{ $t("enable") }}</el-button
            >
          </div>
        </el-tooltip>
        <el-tooltip
          :content="$t('AdministratorActivate')"
          placement="top"
          :disabled="hasPerm('employees.set-login') ? true : false"
        >
          <div>
            <el-button
              size="small"
              @click="handlerDisenableSetting"
              :disabled="
                !multipleSelection.length && !hasPerm('employees.set-login')
              "
              :loading="this.$store.state.config.button_loading"
              :class="[
                hasPerm('employees.set-login') && !multipleSelection.length
                  ? 'footer_left_btn'
                  : 'common-disabled',
              ]"
              >{{ $t("disable") }}</el-button
            >
          </div>
        </el-tooltip>
      </div>

      <base-pagination
        :pageParams="pageParams"
        @search="getEmployees"
      ></base-pagination>
    </div>
    <!-- 修改密码 -->
    <el-dialog
      :title="$t('EditPassword')"
      :visible.sync="visibleModeifyPassword"
    >
      <el-form
        ref="password_form"
        :model="password_form"
        :rules="password_rules"
        label-width="170px"
      >
        <el-form-item :label="$t('NewPassword')" prop="password">
          <el-input v-model="password_form.password" type="password"></el-input>
        </el-form-item>
        <el-form-item
          :label="$t('ConfirmPassword')"
          prop="confirm_password"
          class="password"
        >
          <el-input
            v-model="password_form.confirm_password"
            type="password"
          ></el-input>
        </el-form-item>
        <el-form-item class="password_button">
          <el-row>
            <el-col :span="2" :offset="16">
              <el-button
                @click="visibleModeifyPassword = false"
                class="cancel"
                >{{ $t("Cancel") }}</el-button
              >
            </el-col>
            <el-col :span="2" :offset="2">
              <el-button
                @click="handlerPasswordSubmit"
                type="primary"
                class="submit"
                >{{ $t("Submit") }}</el-button
              >
            </el-col>
          </el-row>
        </el-form-item>
      </el-form>
    </el-dialog>
    <!-- <el-dialog
      class="right-angle"
      :title="edit ? $t('EditStaff') : $t('AddStaff')"
      :visible.sync="show_dialog"
      @open="open_dialog"
      width="60%"
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
        <el-form-item :label="$t('StaffAccount')" prop="email">
          <el-input
            v-model="employees.email"
            :placeholder="$t('PleaseEnterTheStaffAccount')"
            auto-complete="off"
          ></el-input>
        </el-form-item>
        <el-form-item :label="$t('Password')" prop="password" v-if="!edit">
          <el-input
            v-model="employees.password"
            type="password"
            :placeholder="$t('PleaseEnterPassword')"
            auto-complete="off"
          ></el-input>
        </el-form-item>
        <el-form-item :label="$t('StaffName')" prop="fullname">
          <el-input
            v-model="employees.fullname"
            :placeholder="$t('PleaseEnterTheStaffName')"
          ></el-input>
        </el-form-item>
        <el-form-item :label="$t('Phone')" prop="phone">
          <el-input
            v-model="employees.phone"
            :placeholder="$t('PleaseEnterPhoneNumber')"
          ></el-input>
        </el-form-item>
        <el-form-item :label="$t('StaffRemark')" prop="remark">
          <el-input
            v-model="employees.remark"
            :placeholder="$t('PleaseEnterTheStaffRemark')"
          ></el-input>
        </el-form-item> -->
    <!-- <el-form-item :label="$t('StaffGroup')" prop="group_id">
          <el-select
            v-model="employees.group_id"
            filterable
            :placeholder="$t('pleaseSelectStaffGroup')"
          >
            <el-option
              v-for="(item, index) in groupArr"
              :key="index"
              :label="item.name"
              :value="item.id"
            ></el-option>
          </el-select>
        </el-form-item> -->
    <!-- <el-form-item :label="$t('权限组')" prop="role_id">
          <el-select
            v-model="employees.role_id"
            filterable
            :placeholder="$t('请选择权限组')"
          >
            <el-option
              v-for="(item, index) in roleList"
              :key="index"
              :label="item.name"
              :value="item.id"
            ></el-option>
          </el-select>
        </el-form-item> -->
    <!-- <el-form-item :label="$t('Organization')" prop="institution_id">
          <el-cascader
            :placeholder="$t('PleaseSelectOrganization')"
            v-model="employees.institution_id"
            :options="institutionsList"
            :show-all-levels="false"
            :props="{
              checkStrictly: true,
              label: 'name',
              value: 'id',
              emitPath: false,
            }"
            ref="organization"
            @change="organizationChange"
            clearable>
          </el-cascader>
          </el-form-item> -->
    <!-- </el-form> -->
    <!-- <div slot="footer">
        <el-button @click="show_dialog = false">{{ $t("Cancel") }}</el-button>
        <el-button type="primary" @click="addEmployees">{{
          $t("Confirm")
        }}</el-button>
      </div> -->
    <!-- </el-dialog> -->
  </div>
</template>

<script>
import BasePagination from "@/components/BasePagination/BasePagination";
import loading from "@/mixin/global_loading";

export default {
  name: "employeeManagement",
  components: {
    BasePagination,
  },
  mixins: [loading],
  data() {
    return {
      multipleSelection: [], //选中
      tableData: [],
      visibleModeifyPassword: false, //密码弹出框
      pageParams: {
        email: "", //账号
        fullname: "", //姓名
        role_id: "", //所属权限组
        warehouse_id: "", //网点
        page: 1,
        per_page: 20,
        total: 0,
      },
      show_dialog: false,
      edit: false,
      groupArr: [
        {
          name: 1,
          id: 1,
        },
      ],
      warehouseList: [], //网点
      roleList: [],
      employees: {
        email: "",
        password: null,
        username: "",
        fullname: "",
        group_id: 1,
        institution_id: null,
        phone: null,
        role_id: null,
        remark: "",
      },
      // 修改密码
      password_form: {
        id: "",
        password: "",
        confirm_password: "",
      },
      institutionsList: [],
      // 表单验证
      rules: {
        email: [
          {
            required: true,
            message: this.$t("PleaseEnterTheStaffAccount"),
            trigger: "blur",
          },
        ],
        password: [
          {
            required: true,
            message: this.$t("PleaseEnterPassword"),
            trigger: "blur",
          },
        ],
        phone: [
          {
            required: true,
            message: this.$t("PleaseEnterPhoneNumber"),
            trigger: "blur",
          },
        ],
        fullname: [
          {
            required: true,
            message: this.$t("PleaseEnterTheStaffName"),
            trigger: "blur",
          },
        ],
        group_id: [
          {
            required: true,
            message: this.$t("pleaseSelectStaffGroup"),
            trigger: "blur",
          },
        ],
        role_id: [
          {
            required: true,
            message: this.$t("PleaseSelectPermissionGroup"),
            trigger: "blur",
          },
        ],
        remark: [
          {
            required: true,
            message: this.$t("PleaseEnterTheStaffRemark"),
            trigger: "blur",
          },
        ],
        institution_id: [
          {
            required: true,
            message: this.$t("PleaseSelectOrganization"),
            trigger: "blur",
          },
        ],
      },
      // 密码表验证
      password_rules: {
        password: [
          {
            required: true,
            message: this.$t("PleaseEnterPassword"),
            trigger: "blur",
          },
        ],
        confirm_password: [
          {
            required: true,
            message: this.$t("PleaseEnterPassword"),
            trigger: "blur",
          },
        ],
      },
    };
  },
  activated() {
    if (this.$route.params.employeeManagement) {
      this.getEmployees();
    }
  },
  created() {
    this.getWarehouse();
    this.handlerSearch();
    this.getRoleList();
  },
  methods: {
    getWarehouse() {
      // 网点列表
      this.$api.getWarehouse().then((res) => {
        this.warehouseList = res.data.data;
      });
    },
    // 列表选中
    handleSelectionChange(val) {
      this.multipleSelection = val;
    },
    // 启用
    handlerEnableSetting() {
      if (!this.multipleSelection.length) return;
      let multipleSelection = this.multipleSelection.map((item) => item.id);
      this.$api
        .multEmployees({
          status: 2,
          id_list: this.multipleSelection.map((item) => item.id).join(","),
        })
        .then((res) => {
          if (!res) return;
          this.getEmployees();
        });
    },
    // 禁用
    handlerDisenableSetting() {
      if (!this.multipleSelection.length) return;
      this.$api
        .multEmployees({
          status: 1,
          id_list: this.multipleSelection.map((item) => item.id).join(","),
        })
        .then((res) => {
          if (!res) return;
          this.getEmployees();
        });
    },

    organizationChange() {
      this.$refs.organization.toggleDropDownVisible();
    },
    handlerSearch() {
      if (
        this.pageParams.email ||
        this.pageParams.fullname ||
        this.pageParams.role_id
      ) {
        this.pageParams.page = 1;
      }
      this.getEmployees();
    },
    getEmployees() {
      // 列表
      this.$api.getEmployees(this.pageParams).then((res) => {
        this.tableData = res.data.data;
        this.pageParams.total = res.data.meta.total;
      });
    },
    // 拉取角色列表
    getRoleList() {
      this.$api.getRolePublic().then((res) => {
        this.roleList = res.data.data;
      });
    },

    // 添加员工
    addDialog() {
      // this.show_dialog = true;
      // this.getInstitutions();
      this.$router.push({
        name: "AddStaff",
      });
    },
    // 编辑
    getEmployeesDetails(id) {
      // this.show_dialog = true;
      // this.edit = true;
      this.$router.push({
        name: "AddStaff",
        query: {
          id: id,
        },
      });
      // this.getInstitutions();
      // eslint-disable-next-line
      // this.$api.getEmployeesDetails({ id: id }).then((res) => {
      //   if (!res) return;
      //   this.employees = { password: null, ...res.data };
      //   this.employees.group_id = 1;
      //   // this.employees.institution_id = res.data.institution.id;
      // });
    },

    // 修改密码
    EditPassword(id) {
      this.password_form = {
        id: id,
        password: "",
        confirm_password: "",
      };
      // this.password_form.id = id;
      this.visibleModeifyPassword = true;
    },
    // 密码提交
    handlerPasswordSubmit() {
      this.$refs.password_form.validate((valid) => {
        if (valid) {
          this.$api.EditPassword(this.password_form).then((res) => {
            if (!res) return;
            if (res.code == 200) {
              this.$message({
                message: this.$t("passwordhasbeenupdated"),
                type: "success",
              });
              this.visibleModeifyPassword = false;
              this.getEmployees();
            }
          });
        }
      });
    },
    // 删除
    delEmployees(id) {
      this.$confirm(this.$t("ConfirmToCancel") + "?", this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      })
        .then(() => {
          this.$api.delEmployees(id).then((res) => {
            if (!res) return;
            this.getEmployees();
          });
        })
        .catch(() => {});
    },
    // 保存
    // addEmployees() {
    //   this.$refs.employeeForm.validate((valid) => {
    //     if (!valid) return;
    //     this.employees.username = this.employees.email;
    //     if (this.edit) {
    //       this.$api.putEmployees(this.employees).then((res) => {
    //         if (!res) return;
    //         this.show_dialog = false;
    //         this.getEmployees();
    //       });
    //     } else {
    //       this.$api.addEmployees(this.employees).then((res) => {
    //         if (!res) return;
    //         this.show_dialog = false;
    //         this.getEmployees();
    //       });
    //     }
    //   });
    // },

    pen_dialog() {},
    close_dialog() {
      this.$refs.employeeForm.resetFields();
      this.edit = false;
    },
    getInstitutions() {
      this.$api.getInstitutions().then((res) => {
        if (!res) return;
        this.institutionsList = res.data;
      });
    },
  },
};
</script>

<style lang="less" scoped>
.dispatch-order {
  padding: 20px 25px 10px;
  background: #ffffff;
  border: 1px solid rgba(228, 235, 241, 1);
  .conditional {
    padding-bottom: 15px;
    display: flex;
    justify-content: space-between;
    // .header-btn {
    //   // display: inline-flex;
    //   // justify-content: space-between;
    //   &:not(:last-child) > * {
    //     // margin-right: 10px;
    //   }

    //   // &:first-child {
    //   //   .el-input:first-child {
    //   //     margin-right: 10px;
    //   //   }
    //   // } // 给第一个 btn 加个 margin
    // }
    // .el-select {
    //   width: 180px;
    // }
    // .el-input {
    //   width: 180px;
    // }
  }
  .footer-btn {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
  }
}
</style>

