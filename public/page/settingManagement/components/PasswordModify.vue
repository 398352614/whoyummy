<template>
  <!-- 修改密码 -->
  <div class="password-modify">
    <el-form
      :model="info"
      :rules="rules"
      ref="modifyPass"
      label-position="top"
      label-width="100px"
      class="password-modify-form right-angle"
    >
      <el-form-item :label="$t('CurrentPassword')" prop="origin_password">
        <el-input
          v-model="info.origin_password"
          :placeholder="$t('PleaseEnterCurrentPassword')"
          show-password
        ></el-input>
      </el-form-item>
      <el-form-item :label="$t('NewPassword')" prop="new_password">
        <el-input
          v-model="info.new_password"
          :placeholder="$t('PleaseEnterNewPassword')"
          show-password
        ></el-input>
      </el-form-item>
      <el-form-item
        :label="$t('ComfirmNewPassword')"
        prop="new_confirm_password"
      >
        <el-input
          v-model="info.new_confirm_password"
          :placeholder="$t('PleaseComfirmNewPassword')"
          show-password
        ></el-input>
      </el-form-item>
    </el-form>
    <el-tooltip
      :content="$t('AdministratorActivate')"
      placement="top"
      :disabled="hasPerm('employees.reset-password') ? true : false"
    >
      <div class="save">
        <el-button
          :disabled="!hasPerm('employees.reset-password')"
          type="primary"
          @click="putPassword"
          :loading="this.$store.state.config.button_loading"
          :class="[
            hasPerm('employees.reset-password') ? 'submit' : 'submit-disabled',
          ]"
          >{{ $t("Submit") }}</el-button
        >
      </div>
    </el-tooltip>
  </div>
</template>

<script>
export default {
  name: "PasswordModify",
  data() {
    return {
      info: {
        origin_password: "",
        new_password: "",
        new_confirm_password: "",
      },
      rules: {
        origin_password: [
          {
            required: true,
            message: this.$t("PleaseEnterCurrentPassword"),
            trigger: "blur",
          },
        ],
        new_password: [
          {
            required: true,
            message: this.$t("PleaseEnterNewPassword"),
            trigger: "blur",
          },
        ],
        new_confirm_password: [
          {
            required: true,
            message: this.$t("PleaseComfirmNewPassword"),
            trigger: "blur",
          },
        ],
      },
    };
  },
  methods: {
    putPassword() {
      this.$refs.modifyPass.validate((valid) => {
        if (!valid) return;
        this.$api.putPassword(this.info).then((res) => {
          if (res.code === 200) {
            // this.$message({
            //   message: res.msg,
            //   type: 'success',
            // });
            this.$refs.modifyPass.clearValidate();
          } else {
            // this.$message({
            //   message: res.msg,
            //   type: 'error',
            // });
          }
        });
      });
    },
  },
};
</script>
<style lang="less" scoped>
.password-modify {
  // background: #ffffff;
  // border: 1px solid rgba(228, 235, 241, 1);
  padding: 47px 0;
  min-height: calc(100vh - 308px);
  .password-modify-form {
    .el-form-item {
      width: 264px;
      margin: 0 auto 22px;
    }
  }
  .save {
    text-align: center;
    margin-top: 62px;
    .el-button {
      width: 100%;
      max-width: 264px;
      border-radius: 0;
      padding-left: 44px;
      padding-right: 44px;
    }
  }
}
</style>
<style lang="less">
.password-modify {
  .right-angle {
    .el-input__inner {
      border-radius: 0;
    }
    .el-button {
      border-radius: 0;
    }
  }
}
</style>
