<template>
  <!-- 邮编模板 -->
  <div class="more-configuration">
    <el-tooltip
      :content="$t('AdministratorActivate')"
      placement="top"
      :disabled="hasPerm('email-template.store') ? true : false"
    >
      <div style="margin-bottom: 10px; float: right">
        <el-button
          :disabled="!hasPerm('email-template.store')"
          @click="addPostcode"
          type="primary"
          >{{ $t("Add") }}</el-button
        >
      </div>
    </el-tooltip>
    <el-table
      height="calc(100vh - 200px)"
      v-loading="global_loading()"
      element-loading-text="loading..."
      :data="hasPerm('email-template.index') ? table : []"
      border
      style="width: 100%"
      :cell-style="{ padding: '5px 0px' }"
    >
      <template slot="empty" v-if="!hasPerm('email-template.index')">
        <div style="font-size: 18px">
          {{ $t("AdministratorActivate") }}
        </div>
      </template>
      <el-table-column type="index" align="center" width="50">
        <template slot="header">
          <span>{{ $t("Number") }}</span>
        </template>
      </el-table-column>
      <el-table-column align="center" :label="$t('Operation')">
        <template slot-scope="scope">
          <el-dropdown size="mini" trigger="hover" placement="bottom">
            <el-button size="mini" type="primary" class="operation" plain>
              {{ $t("Operation") }}
              <i class="el-icon-arrow-down el-icon--right"></i>
            </el-button>
            <el-dropdown-menu slot="dropdown">
              <!-- 编辑 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('email-template.update') ? true : false"
              >
                <div
                  :style="
                    hasPerm('email-template.update')
                      ? ''
                      : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('email-template.update')"
                    @click.native="putPostcode(scope.row.id)"
                  >
                    <el-button
                      size="small"
                      type="text"
                      :disabled="!hasPerm('email-template.update')"
                      >{{ $t("Edit") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 发送范围 -->
              <el-dropdown-item @click.native="delPostcodeRange(scope.row.id)">
                <el-button size="small" type="text">{{
                  $t("sendingRange")
                }}</el-button>
              </el-dropdown-item>
              <!-- 删除 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('email-template.destroy') ? true : false"
              >
                <div
                  :style="
                    hasPerm('email-template.destroy')
                      ? ''
                      : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('email-template.destroy')"
                    @click.native="delPostcode(scope.row.id)"
                  >
                    <el-button
                      size="small"
                      type="text"
                      :disabled="!hasPerm('email-template.destroy')"
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
        prop="type_name"
        align="center"
        :label="$t('MerchantType')"
      >
      </el-table-column>
      <el-table-column prop="title" align="center" :label="$t('title')">
      </el-table-column>
      <el-table-column prop="status" align="center" :label="$t('Status')">
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

    <!-- 新增模板 -->
    <el-dialog :title="title" :visible.sync="show_Dialog" @close="clearForm">
      <el-form
        :model="form"
        :rules="rules"
        ref="form"
        label-position="right"
        label-width="120px"
      >
        <el-form-item :label="$t('templateType')" prop="type">
          <el-select v-model="form.type" :placeholder="$t('PleaseSelect')">
            <el-option
              v-for="(item, index) in emailTemplateTypeList"
              :key="index"
              :label="item.name"
              :value="item.id"
            >
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item :label="$t('mailTitle')" prop="title">
          <el-input
            v-model.trim="form.title"
            :placeholder="$t('mailTitleState')"
            style="width: 90%"
          ></el-input>
        </el-form-item>
        <el-form-item :label="$t('mailContent')" prop="content">
          <el-input
            v-model.trim="form.content"
            type="textarea"
            :rows="8"
            :placeholder="$t('mailContentState')"
            style="width: 90%"
          ></el-input>
        </el-form-item>
      </el-form>
      <span slot="footer">
        <el-button @click="show_Dialog = false" class="cancel">{{
          $t("Cancel")
        }}</el-button>
        <el-button type="primary" @click="postPostcode()" class="submit">{{
          $t("Confirm")
        }}</el-button>
      </span>
    </el-dialog>
    <!-- 新增模板 结束-->
  </div>
</template>

<script>
import loading from "@/mixin/global_loading";
export default {
  name: "Postcode",
  mixins: [loading],
  data() {
    return {
      table: [],
      form: {
        type: "", //模板类型
        title: "", //邮件标题
        content: null, //邮件内容
        status: 1,
      },
      show_Dialog: false,
      edit: false,
      emailTemplateTypeList: [], //模板类型
      rules: {
        // 表单验证
        type: [
          { required: true, message: this.$t("Required"), trigger: "blur" },
        ],
        title: [
          { required: true, message: this.$t("Required"), trigger: "blur" },
        ],
        content: [
          { required: true, message: this.$t("Required"), trigger: "blur" },
        ],
      },
    };
  },
  created() {
    this.getPostcodeList(); //列表
    this.getDictionary(); //数据字典
  },
  computed: {
    title() {
      if (this.edit) {
        return this.$t("ModifyTheTemplate");
      } else {
        return this.$t("AddTemplate");
      }
    },
  },
  methods: {
    // 数据字典
    getDictionary() {
      this.$api.getDictionary().then((res) => {
        this.emailTemplateTypeList = res.data.emailTemplateTypeList;
      });
    },
    getPostcodeList() {
      // 列表
      this.$api.getPostcodeList({}).then((res) => {
        if (res.code == 200) {
          this.table = res.data.data;
        }
      });
    },

    addPostcode() {
      // 打开弹窗
      this.edit = false;
      this.show_Dialog = true;
    },

    clearForm() {
      // 清除表单
      this.form = {
        name: "",
        type: "",
        content: null,
        status: 1,
      };
    },

    putPostcode(id) {
      // 详情
      this.edit = true;
      this.show_Dialog = true;
      this.$api.getPostcodeDetails(id).then((res) => {
        this.form = res.data;
      });
    },

    postPostcode() {
      // 提交
      this.$refs.form.validate((valid) => {
        if (!valid) return;
        if (this.edit) {
          this.$api.putPostcode(this.form).then((res) => {
            this.show_Dialog = false;
            this.clearForm();
            this.getPostcodeList();
          });
        } else {
          this.$api.addPostcode(this.form).then((res) => {
            this.show_Dialog = false;
            this.getPostcodeList();
          });
        }
      });
    },
    delPostcodeRange() {
      // 发送范围
    },
    delPostcode(id) {
      // 删除
      this.$confirm(this.$t("ConfirmToCancel") + "?", this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      })
        .then(() => {
          this.$api.delPostcode(id).then((res) => {
            if (res.code === 200) {
              this.getPostcodeList();
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
      this.$api.putPostcode(val).then((res) => {
        this.getPostcodeList();
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
  /deep/ .el-dialog__footer {
    text-align: center;
  }
}
</style>
