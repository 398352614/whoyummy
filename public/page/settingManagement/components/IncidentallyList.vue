<template>
  <!-- 顺带规则 -->
  <div class="more-configuration">
    <el-tooltip
      :content="$t('AdministratorActivate')"
      placement="top"
      :disabled="hasPerm('package-no-rule.store') ? true : false"
    >
      <div style="margin-bottom: 10px; float: right">
        <el-button
          :disabled="!hasPerm('package-no-rule.store')"
          @click="openDialog()"
          type="primary"
          size="mini"
          >{{ $t("Add") }}</el-button
        >
      </div>
    </el-tooltip>
    <el-table
      height="calc(100vh - 190px)"
      v-loading="global_loading()"
      element-loading-text="loading..."
      :data="hasPerm('package-no-rule.index') ? packageTable : []"
      border
      style="width: 100%"
      :cell-style="{ padding: '5px 0px' }"
    >
      <template slot="empty" v-if="!hasPerm('package-no-rule.index')">
        <div style="font-size: 18px">
          {{ $t("AdministratorActivate") }}
        </div>
      </template>
      <el-table-column align="center" :label="$t('Operation')">
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
                :disabled="hasPerm('package-no-rule.update') ? true : false"
              >
                <div
                  :style="
                    hasPerm('package-no-rule.update')
                      ? ''
                      : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('package-no-rule.update')"
                    @click.native="getPackageNoRuleDetails(scope.row.id)"
                  >
                    <el-button
                      size="small"
                      type="text"
                      :disabled="!hasPerm('package-no-rule.update')"
                      >{{ $t("Edit") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 删除 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('package-no-rule.destroy') ? true : false"
              >
                <div
                  :style="
                    hasPerm('package-no-rule.destroy')
                      ? ''
                      : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('package-no-rule.destroy')"
                    @click.native="delPackageNoRule(scope.row.id)"
                  >
                    <el-button
                      size="small"
                      type="text"
                      :disabled="!hasPerm('package-no-rule.destroy')"
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
        prop="name"
        align="center"
        :label="$t('incidentalRuleName')"
      >
      </el-table-column>
      <el-table-column
        prop="prefix"
        align="center"
        :label="$t('beginningNumber')"
      >
      </el-table-column>
      <el-table-column
        prop="length"
        align="center"
        :label="$t('totalumberOfDigits')"
      >
      </el-table-column>
      <el-table-column
        prop="status"
        align="center"
        :label="$t('whetherToEnable')"
      >
        <template slot-scope="scope">
          <el-switch
            v-model="scope.row.status"
            :active-value="1"
            :inactive-value="2"
            @change="changeSwitch(scope.row)"
          >
          </el-switch>
        </template>
      </el-table-column>
    </el-table>

    <!-- 添加规则 -->
    <el-dialog :title="title" :visible.sync="show_Dialog" @close="clearForm">
      <el-form
        :model="packageInfo"
        :rules="rules"
        ref="packageInfo"
        label-position="right"
        label-width="215px"
      >
        <el-form-item :label="$t('ruleName')" prop="name">
          <el-input
            v-model.trim="packageInfo.name"
            :placeholder="$t('PleaseRuleName')"
            style="width: 90%"
          ></el-input>
        </el-form-item>
        <el-form-item :label="$t('beginningNumber')" prop="prefix">
          <el-input
            v-model.trim="packageInfo.prefix"
            :placeholder="$t('PleaseBeginningNumber')"
            style="width: 90%"
          ></el-input>
        </el-form-item>
        <el-form-item :label="$t('totalumberOfDigits')" prop="length">
          <el-input
            v-model.trim="packageInfo.length"
            type="number"
            :placeholder="$t('PleaseTotalumberOfDigits')"
            style="width: 90%"
          ></el-input>
        </el-form-item>
        <el-form-item :label="$t('whetherToEnable')">
          <el-switch
            v-model="packageInfo.status"
            :active-value="1"
            :inactive-value="2"
          >
          </el-switch>
        </el-form-item>
      </el-form>
      <span slot="footer">
        <el-button @click="show_Dialog = false" type="primary" class="cancel">{{
          $t("Cancel")
        }}</el-button>
        <el-button type="primary" @click="postPackageNoRule()" class="submit">{{
          $t("Confirm")
        }}</el-button>
      </span>
    </el-dialog>
    <!-- 添加规则 结束-->
  </div>
</template>

<script>
import loading from "@/mixin/global_loading";
export default {
  name: "MoreConfiguration",
  mixins: [loading],
  data() {
    return {
      packageTable: [],
      packageInfo: {
        name: "",
        prefix: "",
        length: null,
        status: 1,
      },
      show_Dialog: false,
      edit: false,

      rules: {
        name: [
          { required: true, message: this.$t("Required"), trigger: "blur" },
        ],
        prefix: [
          { required: true, message: this.$t("Required"), trigger: "blur" },
        ],
        length: [
          {
            validator: (rule, value, callback) => {
              console.log(value, Number.isInteger(value));
              if (!Number.isInteger(+value)) {
                callback(new Error(this.$t("PleaseEnterAnInteger")));
              } else if (value <= 0) {
                callback(new Error(this.$t("PleaseEnterIntegerGreater")));
              } else if (value > 50) {
                callback(new Error(this.$t("PleaseEnterIntegerLess")));
              } else {
                callback();
              }
            },
            trigger: "blur",
          },
        ],
      },
    };
  },
  created() {
    this.getPackageNoRuleList();
  },
  computed: {
    title() {
      if (this.edit) {
        return this.$t("ModifyTheRules");
      } else {
        return this.$t("AddRule");
      }
    },
  },
  methods: {
    getPackageNoRuleList() {
      // 列表
      this.$api.getPackageNoRuleList({}).then((res) => {
        if (res.code == 200) {
          this.packageTable = res.data.data;
        }
      });
    },

    openDialog() {
      // 打开弹窗
      this.edit = false;
      this.show_Dialog = true;
    },

    clearForm() {
      // 清除表单
      this.packageInfo = {
        name: "",
        prefix: "",
        length: null,
        status: 1,
      };
    },

    getPackageNoRuleDetails(id) {
      // 详情
      this.edit = true;
      this.show_Dialog = true;
      this.$api.getPackageNoRuleDetails(id).then((res) => {
        this.packageInfo = res.data;
      });
    },

    postPackageNoRule() {
      // 提交
      this.$refs.packageInfo.validate((valid) => {
        if (!valid) return;
        if (this.edit) {
          this.$api.putPackageNoRule(this.packageInfo).then((res) => {
            this.show_Dialog = false;
            this.clearForm();
            this.getPackageNoRuleList();
          });
        } else {
          this.$api.addPackageNoRule(this.packageInfo).then((res) => {
            this.show_Dialog = false;
            this.getPackageNoRuleList();
          });
        }
      });
    },

    delPackageNoRule(id) {
      // 删除
      this.$confirm(this.$t("ConfirmToCancel") + "?", this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      })
        .then(() => {
          this.$api.delPackageNoRule(id).then((res) => {
            if (res.code === 200) {
              this.getPackageNoRuleList();
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

    changeSwitch(val) {
      // 是否启用
      this.$api.putPackageNoRule(val).then((res) => {
        this.getPackageNoRuleList();
      });
    },
  },
};
</script>
<style lang="less" scoped>
.more-configuration {
  background: #ffffff;
  border: 1px solid rgba(228, 235, 241, 1);
  padding: 20px 25px 10px;
}
</style>
