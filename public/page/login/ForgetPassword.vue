<template>
  <page-model>
    <div class="forgetPassword_box">
      <div class="forgetPassword_main">
        <div class="forgetPassword_banner">
          <div class="forgetPassword_img">
            <img src="@/assets/img/ht.png" class="img_box" alt="" />
            <div class="tmsht">
              <span class="hongTu">红兔</span
              ><span class="tms">TMS-专业城市配送TMS</span>
            </div>
          </div>
        </div>
        <div class="forgetPassword_form_box">
          <div class="forgetPassword_form">
            <div class="forgetPassword_title">
              <span class="title_header">忘记密码</span>
            </div>
            <el-form
              :model="findForm"
              :rules="formRules"
              ref="findForm"
              style="margin-top: 30px"
            >
              <el-form-item :label="$t('Email')" type="email" prop="email">
                <el-input
                  v-model="findForm.email"
                  :placeholder="$t('Pleaseenteryouremail')"
                  auto-complete="off"
                ></el-input>
              </el-form-item>
              <el-form-item :label="$t('VerifyCode')" prop="code">
                <el-input
                  v-model="findForm.code"
                  :placeholder="$t('Pleaseenterverifycode')"
                  auto-complete="off"
                >
                  <span
                    v-show="iconSwitchStatus === 1"
                    slot="append"
                    class="btn_desc"
                    @click="onRegisterCode"
                  >
                    {{ $t("RequestVerifyCode") }}
                  </span>
                  <span v-show="iconSwitchStatus === 2" slot="append"
                    >{{ count }}{{ $t("seconds") }}{{ $t("countdown") }}</span
                  >
                  <el-button
                    v-show="iconSwitchStatus === 3"
                    slot="append"
                    icon="el-icon-loading"
                  ></el-button>
                </el-input>
              </el-form-item>
              <el-form-item :label="$t('newPassword')" prop="new_password">
                <el-input
                  v-model="findForm.new_password"
                  :placeholder="$t('Pleaseenteranewpassword')"
                  type="password"
                  auto-complete="off"
                ></el-input>
              </el-form-item>
              <el-form-item
                :label="$t('Confirmnewpassword')"
                prop="confirm_new_password"
              >
                <el-input
                  v-model="findForm.confirm_new_password"
                  :placeholder="$t('Pleaseconfirmyournewpassword')"
                  type="password"
                  auto-complete="off"
                ></el-input>
              </el-form-item>
            </el-form>
            <div class="form_btn">
              <el-button
                class="login_btn"
                type="primary"
                @click="onSubmit"
                @keyup.enter.native="onSubmit"
                >{{ $t("Submit") }}</el-button
              >
              <el-button
                class="register_btn"
                type="text"
                @click.native="handlerToLogin"
                >{{ $t("ReturntoLogin") }}</el-button
              >
            </div>
          </div>
          <div class="login_footer">
            <span @click="handlerSwitchLangCn">中文</span>
            &nbsp;
            <span @click="handlerSwitchLangEn">英文</span>
            <!-- <span>荷兰</span> -->
          </div>
        </div>
      </div>
    </div>
  </page-model>
</template>

<script>
import PageModel from "./components/PageModel";

export default {
  name: "forgetPassword",
  components: {
    PageModel,
  },
  data() {
    // 重置
    const validatePass2 = (rule, value, callback) => {
      if (value === "") {
        callback(new Error(this.$t("Pleaseenterthepasswordagain")));
      } else if (value !== this.findForm.new_password) {
        callback(new Error(`${this.$t("Incorrectpasswordenteredtwice")}!`));
      } else {
        callback();
      }
    };
    return {
      formRules: {
        name: [
          {
            required: true,
            message: this.$t("Pleaseenteracompanyname"),
            trigger: "blur",
          },
        ],
        email: [
          {
            required: true,
            message: this.$t("Pleaseenteryouremail"),
            trigger: "blur",
          },
          {
            type: "email",
            message: this.$t("Pleaseinputthecorrectemailaddress"),
            trigger: ["blur", "change"],
          },
        ],
        code: [
          {
            required: true,
            message: this.$t("Pleaseenterverifycode"),
            trigger: "blur",
          },
        ],
        new_password: [
          {
            required: true,
            message: this.$t("Pleaseenteranewpassword"),
            trigger: "blur",
          },
          { min: 8, max: 20, message: this.$t("inlength"), trigger: "change" },
        ],
        confirm_new_password: [
          { required: true, validator: validatePass2, trigger: "blur" },
          { min: 8, max: 20, message: this.$t("inlength"), trigger: "change" },
        ],
      },
      findForm: {
        email: "",
        new_password: "",
        code: "",
        confirm_new_password: "",
      },
      iconSwitchStatus: 1,
      count: 0,
      timer: null,
    };
  },
  methods: {
    // 中文
    handlerSwitchLangCn() {
      this.$i18n.locale = "cn";
      localStorage.setItem("lang", "cn");
    },
    // 英文
    handlerSwitchLangEn() {
      this.$i18n.locale = "en";
      localStorage.setItem("lang", "en");
    },
    onSubmit() {
      this.$refs.findForm.validate((valid) => {
        if (valid) {
          this.$api.resetPassword(this.findForm, "重置成功!").then((res) => {
            if (!res) return;
            this.$router.push({
              name: "login",
            });
          });
        }
      });
    },
    handlerToLogin() {
      this.$router.push({
        name: "login",
      });
    },
    handlerForgetPassword() {
      this.$router.push({
        name: "forgetPassword",
      });
    },
    onRegisterCode() {
      if (!this.findForm.email) {
        return this.$message.error(this.$t("Pleaseenteryouremail"));
      }
      this.iconSwitchStatus = 3;
      this.$api
        .getResetCode(
          {
            email: this.findForm.email,
          },
          "发送成功, 请到邮箱查收!"
        )
        .then((res) => {
          if (!res) {
            this.iconSwitchStatus = 1;
            if (this.timer) {
              clearTimeout(this.timer);
            }
            return;
          }
          this.iconSwitchStatus = 2;
          const TIME_COUNT = 60;
          if (!this.timer) {
            this.count = TIME_COUNT;
            this.timer = setInterval(() => {
              if (this.count > 0 && this.count <= TIME_COUNT) {
                this.count -= 1;
              } else {
                this.iconSwitchStatus = 1;
                clearInterval(this.timer);
                this.timer = null;
              }
            }, 1000);
          }
        });
    },
  },
};
</script>

<style lang="less" scoped>
.forgetPassword_box {
  // margin: 82px 0 0 0;
  // min-height: 620px;
  width: 100%;
  height: 100vh;

  .forgetPassword_main {
    // margin: 0 auto;
    display: flex;
    // box-sizing: border-box;
    width: 100%;
    // height: 672px;
    background: rgba(255, 255, 255, 1);
    // border: 10px solid rgba(255, 255, 255, 0.18);
    // box-shadow: 0px 0px 18px 0px rgba(237, 237, 237, 0.96);
    .forgetPassword_banner {
      width: 50%;
      // height: 670.6px;
      // flex: 1;
      // box-sizing: border-box;
      .forgetPassword_img {
        width: 100%;
        height: 100vh;
        background-color: #3f5f5b;
        text-align: center;
        .img_box {
          width: 60%;
          height: 60vh;
          margin-top: 20%;
        }
        .tmsht {
          font-size: 30px;

          .hongTu {
            color: #ff6262;
          }
          .tms {
            color: white;
          }
        }
      }
    }
    .forgetPassword_form_box {
      width: 50%;
      .forgetPassword_form {
        width: 40%;
        margin: 0 auto;
        // flex: 1;
        // margin-left: 80px;
        // margin-right: 80px;
        .btn_desc {
          cursor: pointer;
        }
        .forgetPassword_title {
          margin-top: 50%;

          text-align: left;
          // margin: 93px 0 36px 0;
          .title_header {
            font-size: 24px;
            font-family: Microsoft YaHei;
            font-weight: 400;
            color: #376455;
          }
        }
      }
    }

    .form_btn {
      text-align: center;
      margin-top: 20px;
      .login_btn {
        width: 100%;
        height: 50px;
        color: #fff;
        background: #28b482;
        font-size: 18px;
        font-weight: 400;
      }
      .register_btn {
        margin-top: 10px;
        width: 100%;
        height: 36px;
        background: #fff;
        font-size: 14px;
        font-family: Microsoft YaHei;
        font-weight: 400;
        color: #28b482;
      }
    }
    .login_footer {
      position: absolute;
      bottom: 20px;
      right: 23%;
      // color: ;
    }
  }
}
</style>
<style scoped>
.el-form .el-form-item {
  margin-bottom: 10px;
}
.el-form-item__label {
  font-size: 14px;
  font-family: Microsoft YaHei;
  font-weight: 400;
  color: rgba(40, 208, 148, 1);
  line-height: 47px;
} /* 更改组件的样式 */
</style>
