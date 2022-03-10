<template>
  <!-- 权限配置 -->
  <div class="forum-list">
    <div class="bg">
      <div class="side-bar">
        <ul>
          <li class="li-title" style="cursor: default">
            <span>{{ $t("permissionGroupList") }}</span>
            <span style="margin-right: 180px">
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('role.store') ? true : false"
              >
                <div>
                  <el-button
                    :disabled="!hasPerm('role.store')"
                    size="mini"
                    type="text"
                    @click="addRole"
                    ><i
                      class="el-icon-circle-plus-outline"
                      :style="
                        hasPerm('role.store')
                          ? 'color: red;font-size: 18px'
                          : 'color: rgba(201, 201, 201, 0.8) ;font-size: 18px '
                      "
                    ></i
                  ></el-button>
                </div>
              </el-tooltip>
            </span>
          </li>
          <li
            v-for="(item, index) in roleList"
            :key="index"
            :class="{ 'li-light': high_light == item.id }"
            @click="getPermissions(item)"
          >
            <span>{{ item.name }}</span>
            <span class="operation">
              <!-- 添加员工 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('role.assign-employee-list') ? true : false"
              >
                <div>
                  <el-button
                    :disabled="!hasPerm('role.assign-employee-list')"
                    size="mini"
                    type="text"
                    :style="high_light == item.id ? 'color: #fff;' : ''"
                    @click.stop="openEmployeeDialog(item.id, 0)"
                    ><i
                      class="el-icon-circle-plus-outline"
                      :style="
                        hasPerm('role.assign-employee-list')
                          ? 'color: red;font-size: 18px'
                          : 'color: rgba(201, 201, 201, 0.8) ;font-size: 18px '
                      "
                    ></i
                  ></el-button>
                </div>
              </el-tooltip>
              <!-- 查看员工 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('role.employee-list') ? true : false"
              >
                <div>
                  <el-button
                    :disabled="!hasPerm('role.employee-list')"
                    size="mini"
                    type="text"
                    :style="high_light == item.id ? 'color: #fff' : ''"
                    @click.stop="openEmployeeDialog(item.id, 1)"
                    ><i
                      class="el-icon-view"
                      :style="
                        hasPerm('role.employee-list')
                          ? 'color: black;font-size: 18px'
                          : 'color: rgba(201, 201, 201, 0.8) ;font-size: 18px '
                      "
                    ></i
                  ></el-button>
                </div>
              </el-tooltip>
              <!-- 编辑员工 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('role.update') ? true : false"
              >
                <div>
                  <el-button
                    :disabled="!hasPerm('role.update')"
                    type="text"
                    @click.stop="openModify(item.id, item.name)"
                    ><i
                      class="el-icon-edit"
                      :style="
                        hasPerm('role.update')
                          ? 'color: black;font-size: 18px'
                          : 'color: rgba(201, 201, 201, 0.8) ;font-size: 18px '
                      "
                    ></i
                  ></el-button>
                </div>
              </el-tooltip>
              <!-- 删除员工 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('role.destroy') ? true : false"
              >
                <div>
                  <el-button
                    :disabled="!hasPerm('role.destroy')"
                    v-if="item.is_admin != 1"
                    type="text"
                    @click.stop="delRole(item.id)"
                    ><i
                      class="el-icon-delete"
                      :style="
                        hasPerm('role.destroy')
                          ? 'color: red;font-size: 18px'
                          : 'color: rgba(201, 201, 201, 0.8) ;font-size: 18px '
                      "
                    ></i
                  ></el-button>
                </div>
              </el-tooltip>
            </span>
          </li>
        </ul>
        <!-- <div class="add-role" v-if="hasPerm('role.store')">
          <el-input
            v-model.trim="name"
            @keyup.enter.native="addRole"
            :placeholder="$t('请输入权限组名称')"
          ></el-input>
          <el-button type="primary" :disabled="!name" @click="addRole">{{
            $t("添加")
          }}</el-button>
        </div> -->
      </div>
      <div class="content-bar">
        <div class="content-title">{{ $t("webPermissionList") }}</div>
        <!-- <el-col v-for="(item, index) in permissionsList" :key="index"> -->
        <el-tree
          ref="tree"
          :data="permissionsList"
          node-key="id"
          :props="props"
          show-checkbox
          @check-change="getId"
          :default-checked-keys="checkList"
        >
        </el-tree>
        <!-- </el-col> -->
        <el-row>
          <el-col :offset="1" :span="8" style="margin-top: 20px">
            <el-tooltip
              :content="$t('AdministratorActivate')"
              placement="top"
              :disabled="hasPerm('role.permission') ? true : false"
              style="width: 70px"
            >
              <div>
                <el-button
                  :loading="this.$store.state.config.button_loading"
                  v-if="permissionsList.length !== 0"
                  :disabled="!hasPerm('role.permission') || isAdmin"
                  type="primary"
                  @click="setPermissions()"
                  :class="[
                    hasPerm('role.permission') ? 'submit' : 'submit-disabled',
                  ]"
                  >{{ $t("Save") }}</el-button
                >
              </div>
            </el-tooltip>
          </el-col>
        </el-row>
      </div>
    </div>
    <!-- 添加角色 -->
    <el-dialog :visible.sync="AddRole" width="50%">
      <el-form
        :model="form"
        :rules="rules"
        ref="form"
        label-position="right"
        label-width="170px"
      >
        <el-row>
          <el-col>
            <el-form-item prop="name" :label="$t('permissionGroupName')">
              <el-input
                :placeholder="$t('PleaseEnterPermissionGroupName')"
                v-model="form.name"
              ></el-input>
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <div style="text-align: right">
        <el-button @click="AddRole = false" class="cancel">{{
          $t("Cancel")
        }}</el-button>
        <el-button type="primary" @click="addClick" class="submit">{{
          $t("Confirm")
        }}</el-button>
      </div>
    </el-dialog>

    <!-- 权限组弹窗 -->
    <el-dialog :visible.sync="modify_Role" width="50%">
      <el-form
        :model="role"
        :rules="rules"
        ref="modify_Role"
        label-position="right"
        label-width="170px"
      >
        <el-row>
          <el-col>
            <el-form-item prop="name" :label="$t('permissionGroupName')">
              <el-input
                :placeholder="$t('PleaseEnterPermissionGroupName')"
                v-model="role.name"
              ></el-input>
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <div style="text-align: right">
        <el-button @click="modify_Role = false" class="cancel">{{
          $t("Cancel")
        }}</el-button>
        <el-button
          type="primary"
          :loading="$store.state.switchBtn"
          @click="modifyRole"
          class="submit"
          >{{ $t("Confirm") }}</el-button
        >
      </div>
    </el-dialog>

    <!-- 员工弹窗 -->
    <el-dialog
      :visible.sync="employee_dialog"
      width="60%"
      :title="employee_tit"
      @close="closeDialog"
      class="employee_dialog"
    >
      <div style="text-align: right; margin-bottom: 10px">
        <el-input
          size="small"
          clearable
          :placeholder="$t('PleaseEnterStaffName')"
          @keyup.enter.native="handleResetParamsSearch"
          @clear="handleResetParamsSearch"
          v-model="pageParams.fullname"
        >
        </el-input>
        <el-button
          size="small"
          @click="handleResetParamsSearch"
          :loading="this.$store.state.config.button_loading"
          type="primary"
          >{{ $t("Search") }}</el-button
        >
      </div>
      <el-table
        max-height="600px"
        v-loading="global_loading()"
        element-loading-text="loading..."
        :data="employeeList"
        @selection-change="handleSelectionChange"
        border
        style="width: 100%"
      >
        <el-table-column type="selection" align="center" width="50">
        </el-table-column>
        <el-table-column
          :label="$t('Number')"
          type="index"
          width="60"
          align="center"
        >
        </el-table-column>
        <el-table-column prop="fullname" align="center" :label="$t('Name')">
        </el-table-column>
        <el-table-column prop="email" align="center" :label="$t('Account')">
        </el-table-column>
      </el-table>
      <base-pagination
        :pageParams="pageParams"
        @search="handleResetParamsSearch(true)"
      ></base-pagination>
      <div
        v-if="isNewaddOrSee == 0"
        style="text-align: right; margin-top: 10px"
      >
        <el-button @click="employee_dialog = false" class="cancel">{{
          $t("Cancel")
        }}</el-button>
        <el-button
          type="primary"
          :loading="$store.state.switchBtn"
          @click="employeeSubmit"
          class="submit"
          >{{ $t("Confirm") }}</el-button
        >
      </div>
      <div v-else style="text-align: right; margin-top: 10px">
        <el-button
          type="danger"
          :loading="$store.state.switchBtn"
          @click="employeeSubmit"
          >{{ $t("Remove") }}</el-button
        >
      </div>
    </el-dialog>
  </div>
</template>
<script>
import loading from "@/mixin/global_loading";
import BasePagination from "@/components/BasePagination/BasePagination";
export default {
  name: "RoleManagement",
  mixins: [loading],
  data() {
    return {
      roleList: [],
      name: "",
      clickItem: null,
      role: {
        id: null,
        name: "",
      },
      form: {
        name: "",
      },
      modify_Role: false,
      AddRole: false,
      permissionsList: [],
      checkList: [],
      isAdmin: false, // 管理员标识
      props: {
        label: "name",
        children: "children",
        disabled: (data, node) => {
          return this.isAdmin;
        },
      },

      employee_dialog: false,
      role_id: null,
      employeeList: [],
      employee_id_list: [],
      isNewaddOrSee: 0, // 新增员工或删除
      employee_tit: "",
      pageParams: {
        fullname: "",
        keyword: "",
        page: 1,
        per_page: 10,
        total: 0,
      },
      high_light: null,
      rules: {
        name: [
          {
            required: true,
            message: this.$t("PleaseEnterPermissionGroupName"),
            trigger: "blur",
          },
        ],
      },
    };
  },
  created() {
    this.getRoleList();
  },
  computed: {
    reset() {
      return this.$store.state.lang;
    },
  },
  watch: {
    reset: {
      handler() {
        this.getRoleList();
        // this.getPermissions(this.high_light);
      },
    },
  },
  methods: {
    // 拉取角色列表
    getRoleList() {
      this.$api.getRole().then((res) => {
        this.roleList = res.data.data;
      });
    },

    recursion(arr = []) {
      let newArray = [];
      if (arr.length && arr.length !== 0) {
        arr.forEach((el) => {
          if (el.is_auth == 1) {
            if (el.children && el.children.length !== 0) {
              let demo = this.recursion(el.children);
              newArray = newArray.concat(demo);
              // console.log('值: ', demo, newArray)
            } else {
              newArray.push(el.id);
            }
          }
        });
      }
      return newArray;
    },
    // 拉取权限列表
    getPermissions(item) {
      const id = item.id;
      this.clickItem = item;
      if (!id) {
        return false;
      } else if (item.is_admin == 1) {
        // 禁止修改管理员权限
        this.isAdmin = true;
      } else {
        this.isAdmin = false;
      }
      this.high_light = id;
      this.$api.getPermissionTree(id).then((res) => {
        this.checkList = this.recursion(res.data);
        this.permissionsList = res.data;
      });
    },
    // 添加角色
    addRole() {
      this.AddRole = true;
    },
    addClick() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          this.$api.addRole(this.form).then((res) => {
            if (res.code == 200) {
              this.name = "";
              this.getRoleList();
              this.AddRole = false;
            }
          });
        }
      });
    },
    // 删除角色
    delRole(id) {
      this.$confirm(this.$t("ConfirmToCancel") + "?", this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      }).then(() => {
        this.$api.delRole(id).then((res) => {
          if (res.code == 200) {
            this.getRoleList();
          }
        });
      });
    },
    openModify(id, name) {
      this.role.id = id;
      this.role.name = name;
      this.modify_Role = true;
    },
    // 修改角色
    modifyRole() {
      this.$refs.modify_Role.validate((valid) => {
        if (!valid) return;
        this.$api.putRole(this.role).then((res) => {
          if (res.code == 200) {
            this.getRoleList();
            this.modify_Role = false;
          }
        });
      });
    },
    checkChild(arr = []) {
      if (arr.length && arr.length !== 0) {
        arr.forEach((el) => {
          if (el.is_auth == 1) {
            newArray.push(el.id);
            if (el.children && el.children.length !== 0) {
              let demo = this.recursion(el.children);
              newArray = newArray.concat(demo);
              // console.log('值: ', demo, newArray)
            }
          }
        });
      } else {
      }
      return newArray;
    },
    getId(child, checked) {
      // console.log(child, checked)
      // if (checked) {
      //   child.is_auth = 1;
      //   this.checkList.indexOf(child.id) == -1 ? this.checkList.push(child.id) : '';
      // } else {
      //   child.is_auth = 2;
      //   let ishas = this.checkList.indexOf(child.id);
      //   ishas == -1 ? '' : this.checkList.splice(ishas, 1);
      // }
    },

    // 员工
    getEmployeeList() {
      // 新增
      this.$api.getEmployeeList(this.pageParams).then((res) => {
        if (res.code == 200) {
          this.employeeList = res.data.data;
          this.pageParams.total = res.data.meta.total;
        }
      });
    },
    getRoleEmployeeList() {
      // 查看
      this.$api
        .getRoleEmployeeList(this.role_id, this.pageParams)
        .then((res) => {
          if (res.code == 200) {
            this.employeeList = res.data.data;
            this.pageParams.total = res.data.meta.total;
          }
        });
    },
    handleResetParamsSearch(noReset = false) {
      !noReset ? (this.pageParams.page = 1) : "";
      this.isNewaddOrSee == 0
        ? this.getEmployeeList()
        : this.getRoleEmployeeList();
    },
    openEmployeeDialog(id, status) {
      // 打开弹窗
      this.employee_tit = status
        ? this.$t("viewEmployees")
        : this.$t("AddStaff");
      this.isNewaddOrSee = status;
      this.role_id = id;
      this.handleResetParamsSearch();
      this.employee_dialog = true;
    },

    handleSelectionChange(val) {
      // 选中项
      this.employee_id_list = val;
    },

    employeeSubmit() {
      this.isNewaddOrSee == 0
        ? this.putAssignEmployee()
        : this.delRemoveEmployee();
    },

    putAssignEmployee() {
      // 新增员工
      if (!this.employee_id_list.length) {
        this.$message({
          type: "error",
          message: this.$t("PleaseTickEmployeesYou"),
        });
        return;
      }
      this.$api
        .putAssignEmployee({
          id: this.role_id,
          employee_id_list: this.employee_id_list
            .map((el) => {
              return el.id;
            })
            .join(","),
        })
        .then((res) => {
          if (res.code == 200) {
            this.employee_dialog = false;
          }
        });
    },

    delRemoveEmployee() {
      // 移除员工
      if (!this.employee_id_list.length) {
        this.$message({
          type: "error",
          message: this.$t("PleaseTickEmployeesRemoved"),
        });
        return;
      }
      this.$api
        .delRemoveEmployee({
          id: this.role_id,
          employee_id_list: this.employee_id_list
            .map((el) => {
              return el.id;
            })
            .join(","),
        })
        .then((res) => {
          if (res.code == 200) {
            this.employee_dialog = false;
          }
        });
    },

    closeDialog() {
      this.employeeList = [];
      this.employee_id_list = [];
      this.role_id = null;
      this.pageParams = {
        fullname: "",
        keyword: "",
        page: 1,
        per_page: 10,
        total: 0,
      };
    },

    // 提交
    setPermissions() {
      // let ids = this.checkList.join(',');
      console.log(this.$refs.tree.getHalfCheckedKeys());
      let checkedKeys = this.$refs.tree.getCheckedKeys(); //所有被选中的节点
      let halfCheckedKeys = this.$refs.tree.getHalfCheckedKeys(); //所有半选中的节点
      let ids = checkedKeys.concat(halfCheckedKeys).join(",");

      if (!ids) {
        this.$message({
          type: "error",
          message: this.$t("PleaseCheckPermissions"),
        });
        return;
      }
      this.$api
        .putAssignPermission({
          id: this.high_light,
          permission_id_list: ids,
        })
        .then((res) => {
          if (res.code == 200) {
            this.getPermissions(this.clickItem);
          }
        });
    },
  },
};
</script>
<style lang="less" scoped="scoped">
.forum-list {
  overflow: auto;
  .bg {
    overflow: hidden;
    display: flex;
  }
  .side-bar {
    width: 350px;
    // min-height: 150px;
    margin-right: 20px;
    padding-bottom: 100px;
    float: left;
    border-radius: 3px;
    background: white;
    border: 1px rgb(234, 234, 234) solid;
    position: relative;
    overflow: hidden;
    ul {
      list-style: none;
      position: relative;
      padding: 0;
      li {
        line-height: 40px;
        padding-left: 10px;
        border-bottom: 1px rgb(234, 234, 234) solid;
        display: flex;
        align-items: center;
        // flex-direction: column;
        justify-content: space-between;
        cursor: pointer;
        .operation {
          display: flex;
          margin-right: 20px;
          gap: 10px;
          align-items: center;
        }
      }
      .li-title {
        color: #28d094;
      }
      .li-light {
        background: #28d094;
        color: #ffffff;
      }
      .li-disable {
        background: #f6f6f6;
        cursor: default;
      }
    }
    .add-role {
      width: 100%;
      position: absolute;
      bottom: 10px;
      box-sizing: border-box;
      padding: 0 5px;
      display: flex;
      .el-input {
        margin-right: 10px;
      }
    }
  }
  .content-bar {
    width: 70%;
    // height: 100%;
    float: left;
    padding: 0 10px 10px;
    border-radius: 3px;
    border: 1px rgb(234, 234, 234) solid;
    background: white;
    box-sizing: border-box;
    .content-title {
      line-height: 40px;
      color: #28d094;
    }
  }

  .employee_dialog {
    .el-input {
      width: 328px;
    }
  }
}
</style>
<style lang="less">
.forum-list {
  .el-tree-node__content {
    display: flex;
  }
}
</style>