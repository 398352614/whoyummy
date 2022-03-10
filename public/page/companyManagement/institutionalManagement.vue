<template>
  <div class="just-like">
    <div class="left-page">
      <div class="left-page-title">
        {{ $t("Organization") }}
      </div>
      <div class="left-page-tree">
        <el-tree
          :data="data"
          default-expand-all
          :props="defaultProps"
          @node-click="getID"
          :highlight-current="true"
        >
          <span class="custom-tree-node" slot-scope="{ node, data }">
            <span>{{ node.label }}</span>
            <span class="operation-group" v-if="data.id === treeID">
              <el-button
                type="text"
                size="mini"
                @click.stop="editInstitutions(data)"
              >
                <span>
                  <i class="el-icon-edit"></i>
                </span>
              </el-button>
              <el-button
                type="text"
                size="mini"
                v-if="data.distance < 4"
                @click.stop="() => addToInstitutionsDialog(data)"
              >
                <span class="operation">
                  <i class="el-icon-plus"></i>
                </span>
              </el-button>
              <el-button
                type="text"
                size="mini"
                @click.stop="() => remove(node, data)"
              >
                <span class="operation">
                  <i class="el-icon-minus"></i>
                </span>
              </el-button>
            </span>
          </span>
        </el-tree>
      </div>
      <div class="add">
        <el-button type="primary" @click="openDialog" plain>{{
          $t("AddSubOrganization")
        }}</el-button>
      </div>
    </div>
    <div class="right-page">
      <div class="right-page-title">
        {{ $t("CurrentOrganization") }}: {{ institutionsDetails.name }}
      </div>
      <div class="page-info">
        <p>{{ $t("Department") }}</p>
        <div class="page-info-details">
          {{ $t("OrganizationTel") }}:
          <span>{{ institutionsDetails.phone }}</span>
          {{ $t("OrganzationContactPerson") }}:
          <span>{{ institutionsDetails.contacts }}</span>
          {{ $t("CountryCity") }}:
          <span>{{ institutionsDetails.country_name }}</span>
          {{ $t("DetailedAddress") }}:
          <span>{{ institutionsDetails.address }}</span>
        </div>
      </div>
      <div class="page-table">
        <p>{{ institutionsDetails.name || "" + $t("StaffList") }}</p>
        <el-table
          max-height="600px"
          :data="institutionsEmployees"
          border
          style="width: 100%"
        >
          <el-table-column align="center" type="index" width="50">
            <template slot="header">
              <span>{{ $t("Number") }}</span>
            </template>
          </el-table-column>
          <el-table-column
            align="center"
            prop="fullname"
            :label="$t('StaffName')"
          >
          </el-table-column>
          <el-table-column align="center" prop="phone" :label="$t('Phone')">
          </el-table-column>
          <el-table-column align="center" prop="group" :label="$t('UserGroup')">
          </el-table-column>
          <el-table-column
            align="center"
            prop="date"
            :label="$t('Operation')"
            width="95"
          >
            <template slot-scope="scope">
              <el-dropdown size="mini" trigger="click" placement="bottom">
                <el-button size="mini">
                  {{ $t("Operation")
                  }}<i class="el-icon-arrow-down el-icon--right"></i>
                </el-button>
                <el-dropdown-menu slot="dropdown">
                  <el-dropdown-item>
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      @click="handleMove(scope.row)"
                      >{{ $t("Move") }}</el-button
                    >
                  </el-dropdown-item>
                  <el-dropdown-item>
                    <el-button
                      class="operation"
                      @click="getOrderTrail(scope.row.order_no)"
                      size="small"
                      type="text"
                      >{{ $t("Delete") }}</el-button
                    >
                  </el-dropdown-item>
                </el-dropdown-menu>
              </el-dropdown>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </div>

    <el-dialog
      :title="$t('AddSubOrganization')"
      :visible.sync="show_dialog"
      width="80%"
      @close="closeDialog"
    >
      <el-form
        :model="institutionsInfo"
        :rules="rules"
        ref="institutionsForm"
        label-position="right"
        label-width="210px"
        class="add-table"
      >
        <el-form-item :label="$t('OrganizationName')" prop="name">
          <el-input
            v-model="institutionsInfo.name"
            :placeholder="$t('PleaseEnterOrganizationName')"
          ></el-input>
        </el-form-item>
        <el-form-item :label="$t('OrganzationContactPerson')" prop="contacts">
          <el-input
            v-model="institutionsInfo.contacts"
            :placeholder="$t('PleaseEnterOrganzationContactPerson')"
          ></el-input>
        </el-form-item>
        <el-form-item :label="$t('OrganizationTel')" prop="phone">
          <el-input
            v-model="institutionsInfo.phone"
            :placeholder="$t('PleaseEnterOrganizationTel')"
          ></el-input>
        </el-form-item>
        <el-form-item :label="$t('DetailedAddress')" prop="address">
          <el-input
            v-model="institutionsInfo.address"
            :placeholder="$t('PleaseEnterAddress')"
          ></el-input>
        </el-form-item>
        <el-form-item :label="$t('Organization')" prop="parent_id">
          <el-cascader
            v-model="institutionsInfo.parent_id"
            :options="data"
            :props="props"
            :show-all-levels="false"
            clearable
          ></el-cascader>
        </el-form-item>
      </el-form>
      <div slot="footer">
        <el-button @click="show_dialog = false">{{ $t("Cancel") }}</el-button>
        <el-button
          type="primary"
          @click="addInstitutions"
          :loading="this.$store.state.config.button_loading"
          >{{ $t("Confirm") }}</el-button
        >
      </div>
    </el-dialog>

    <el-dialog :visible.sync="visibleMove" width="80%" class="move_dialog">
      <el-cascader
        v-model="moveSelectId"
        :options="options"
        ref="organization"
        @change="organizationChange"
        :props="{
          checkStrictly: true,
          label: 'name',
          value: 'id',
          emitPath: false,
        }"
        clearable
      >
      </el-cascader>
      <el-button type="primary" @click="handlerMoveComfirm">确定</el-button>
    </el-dialog>
  </div>
</template>

<script>
export default {
  name: "InstitutionalManagement",
  data() {
    return {
      moveSelectId: "",
      moveEmployeeId: "",
      options: [],
      treeID: null,
      data: [],
      visibleMove: false,
      defaultProps: {
        children: "children",
        label: "name",
      },
      props: {
        checkStrictly: true,
        value: "id",
        label: "name",
        emitPath: false,
      },
      show_dialog: false,
      institutionsInfo: {
        name: "",
        phone: "",
        contacts: "",
        address: "",
        parent_id: null,
      },
      edit: false,
      institutionsDetails: {},
      institutionsEmployees: [],
      rules: {
        name: [
          {
            required: true,
            message: this.$t("PleaseEnterOrganizationName"),
            trigger: "blur",
          },
        ],
        contacts: [
          {
            required: true,
            message: this.$t("PleaseEnterOrganzationContactPerson"),
            trigger: "blur",
          },
        ],
        phone: [
          {
            required: true,
            message: this.$t("PleaseEnterOrganizationTel"),
            trigger: "blur",
          },
        ],
        address: [
          {
            required: true,
            message: this.$t("PleaseEnterAddress"),
            trigger: "blur",
          },
        ],
        parent_id: [
          {
            required: true,
            message: this.$t("PleaseSelectOrganization"),
            trigger: "change",
          },
        ],
      },
    };
  },
  created() {
    this.getInstitutions();
  },
  methods: {
    organizationChange() {
      this.$refs.organization.toggleDropDownVisible();
    },
    handlerMoveComfirm() {
      this.$api
        .moveInstitutions(this.moveEmployeeId, this.moveSelectId)
        .then((res) => {
          if (!res) return;
          this.visibleMove = !this.visibleMove;
          this.$store.commit("config/refreshRouter", Math.random());
        });
    },
    handleMove(row) {
      this.options = this.data;
      this.moveEmployeeId = row.id;
      this.visibleMove = !this.visibleMove;
    },
    getInstitutions() {
      // 树
      this.$api.getInstitutions().then((res) => {
        this.data = res.data;
      });
    },
    getInstitutionsDetails(id) {
      // 拉取组织详情
      // eslint-disable-next-line
      this.$api.getInstitutionsDetails({ id: id }).then((res) => {
        this.institutionsDetails = res.data;
      });
    },
    getInstitutionsEmployees(id) {
      // 拉取组织成员
      this.$api.getInstitutionsEmployees(id).then((res) => {
        this.institutionsEmployees = res.data.data;
      });
    },
    openDialog() {
      this.show_dialog = true;
    },
    closeDialog() {
      this.edit = false;
      this.$refs.institutionsForm.resetFields();
    },
    addToInstitutionsDialog(data) {
      this.institutionsInfo.parent_id = data.id;
      this.openDialog();
    },
    addInstitutions() {
      // 树
      this.$refs.institutionsForm.validate((valid) => {
        if (!valid) return;
        if (this.edit) {
          this.$api.putInstitutions(this.institutionsInfo).then((res) => {
            if (res.code === 200) {
              this.$message({
                message: "修改成功!",
                type: "success",
              });
              this.getInstitutions();
              this.show_dialog = false;
            }
          });
        } else {
          this.$api.addInstitutions(this.institutionsInfo).then((res) => {
            if (res.code === 200) {
              this.$message({
                message: "添加成功!",
                type: "success",
              });
              this.getInstitutions();
              this.show_dialog = false;
            }
          });
        }
      });
    },
    editInstitutions(data) {
      this.edit = true;
      this.show_dialog = true;
      this.$api.getInstitutionsDetails({ id: data.id }).then((res) => {
        this.institutionsInfo = res.data;
        this.institutionsInfo.parent_id = res.data.parent;
      });
    },
    getID(data) {
      this.treeID = data.id;
      this.getInstitutionsDetails(data.id);
      this.getInstitutionsEmployees(data.id);
    },
    remove(node, data) {
      this.$api.delInstitutions(data.id).then((res) => {
        if (res.code === 200) {
          this.$message({
            message: this.$t("SuccessfullDeleted"),
            type: "success",
          });
          this.getInstitutions();
        }
      });
    },
  },
};
</script>
<style lang="less" scoped>
.just-like {
  .left-page {
    display: inline-block;
    box-sizing: border-box;
    width: 30%;
    background: #ffffff;
    .left-page-title {
      padding: 20px 16px;
      box-sizing: border-box;
      border-bottom: 1px solid rgba(228, 235, 241, 1);
      font-weight: bold;
    }
    .left-page-tree {
      padding: 20px 16px;
      .custom-tree-node {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 14px;
        padding-right: 8px;
        .operation-group {
          .operation {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1px;
            border: 1px solid #28d195;
            border-radius: 50%;
          }
        }
      }
    }
    .add {
      text-align: center;
      padding: 56px 0;
    }
  }
  .right-page {
    display: inline-block;
    box-sizing: border-box;
    width: 68%;
    float: right;
    background: #ffffff;
    .right-page-title {
      padding: 20px 16px;
      box-sizing: border-box;
      border-bottom: 1px solid rgba(228, 235, 241, 1);
      font-weight: bold;
    }
    .page-info {
      padding: 15px 31px;
      p {
        font-weight: bold;
      }
      .page-info-details {
        span {
          margin-right: 50px;
        }
      }
    }
    .page-table {
      p {
        font-weight: bold;
      }
      padding: 15px 31px;
    }
  }
}
</style>
<style lang="less" scoped>
.just-like {
  .left-page {
    .add {
      .el-button {
        border-radius: 0;
      }
    }
  }
  .add-table {
    .el-select {
      width: 100%;
    }
    .el-cascader {
      width: 100%;
    }
  }
}
</style>
