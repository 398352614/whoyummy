<template>
  <page-model>
    <div class="register_box">
      <div class="register_main">
        <div class="register_banner">
          <div class="register_img">
            <img src="@/assets/img/ht.png" class="img_box" alt="" />
            <div class="tmsht">
              <span class="hongTu">红兔</span
              ><span class="tms">TMS-专业城市配送TMS</span>
            </div>
          </div>
        </div>
        <div class="register_form_box">
          <div class="register_form">
            <div class="register_title">
              <span class="title_header">{{ $t("Welcometoregister") }}</span>
            </div>
            <el-form
              :model="registerForm"
              ref="registerForm"
              :rules="registerRules"
              aria-autocomplete="off"
            >
              <!-- 公司名称 -->
              <el-form-item prop="name" :label="$t('CompanyName')">
                <el-input
                  v-model="registerForm.name"
                  :placeholder="$t('PleaseenteryourCompanyName')"
                >
                </el-input>
              </el-form-item>
              <!-- 邮箱 -->
              <el-form-item prop="email" :label="$t('Email')">
                <el-input
                  type="email"
                  v-model="registerForm.email"
                  :placeholder="$t('Pleaseenteryouremail')"
                >
                </el-input>
              </el-form-item>
              <!-- 验证码 -->
              <el-form-item prop="code" :label="$t('VerifyCode')">
                <el-input
                  v-model="registerForm.code"
                  :placeholder="$t('Pleaseenterverifycode')"
                >
                  <span
                    v-show="iconSwitchStatus === 1"
                    slot="append"
                    class="code_desc"
                    @click="onRegisterCode"
                  >
                    {{ $t("RequestVerifyCode") }}
                  </span>
                  <span v-show="iconSwitchStatus === 2" slot="append"
                    >{{ $t("countdown") }} {{ count }} {{ $t("seconds") }}</span
                  >
                  <el-button
                    v-show="iconSwitchStatus === 3"
                    slot="append"
                    size="small"
                    icon="el-icon-loading"
                  ></el-button>
                </el-input>
              </el-form-item>
              <el-form-item prop="password" :label="$t('Password')">
                <el-input
                  type="password"
                  v-model="registerForm.password"
                  :placeholder="$t('Pleaseenteryourpassword')"
                >
                </el-input>
              </el-form-item>
              <el-form-item
                prop="confirm_password"
                :label="$t('confirmYourPassword')"
              >
                <el-input
                  type="password"
                  v-model="registerForm.confirm_password"
                  :placeholder="$t('Pleaseenteryourpassword')"
                >
                </el-input>
              </el-form-item>
            </el-form>
            <div class="form_btn">
              <el-button
                class="register_btn"
                :loading="$store.state.token.loading"
                @click="registerAccount('registerForm')"
                type="primary"
              >
                {{ $t("Register") }}
              </el-button>
              <div class="register_btn_desc">
                {{ $t("Byclicking")
                }}<span>{{ $t("TMSServiceAgreement") }}</span>
              </div>
              <el-button
                class="register_to_login"
                @click="goBacktoLogin"
                type="text"
              >
                {{ $t("Loginwithexistaccount") }}
              </el-button>
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
  name: "login1",
  components: {
    PageModel,
  },
  data() {
    // 注册
    const validateRegister = (rule, value, callback) => {
      if (value === "") {
        callback(new Error(this.$t("Pleaseenterthepasswordagain")));
      } else if (value !== this.registerForm.password) {
        callback(new Error(`${this.$t("Incorrectpasswordenteredtwice")}!`));
      } else {
        callback();
      }
    };
    const checkPhone = (rule, value, callback) => {
      if (!value) {
        return callback(new Error(this.$t("Pleaseenteryourtel")));
      }
      const reg = /^1[3|4|5|7|8][0-9]\d{8}$/;
      if (reg.test(value)) {
        return callback();
      }
      return callback(new Error(this.$t("pleaseenteravalidphonenumber")));
    };
    return {
      registerRules: {
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
            trigger: ["blur"],
          },
        ],
        code: [
          {
            required: true,
            message: this.$t("Pleaseenterverifycode"),
            trigger: "blur",
          },
        ],
        phone: [{ validator: checkPhone, trigger: "blur" }],
        password: [
          {
            required: true,
            message: this.$t("Pleaseenteranewpassword"),
            trigger: "blur",
          },
          { min: 8, max: 20, message: this.$t("inlength"), trigger: "blur" },
        ],
        confirm_password: [
          { required: true, validator: validateRegister, trigger: "blur" },
          { min: 8, max: 20, message: this.$t("inlength"), trigger: "blur" },
        ],
      },
      registerForm: {
        name: "",
        email: "",
        code: "",
        password: "",
        confirm_password: "",
      },
      keep: false,
      show: true,
      iconSwitchStatus: 1,
      count: "",
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
    onRegisterCode() {
      if (!this.registerForm.email) {
        return this.$message.error(this.$t("Pleaseenteryouremail"));
      }
      this.iconSwitchStatus = 3;
      this.$api
        .getRegisterCode(
          {
            email: this.registerForm.email,
          },
          this.$t("sendsucessful")
        )
        .then((res) => {
          if (!res) {
            clearTimeout(this.timer);
            this.iconSwitchStatus = 1;
            this.timer = null;
            return false;
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
    goBacktoLogin() {
      this.$router.push({
        name: "login",
      });
    },
    registerAccount(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          this.$api.register(this.registerForm).then((res) => {
            if (!res) return;
            this.$router.push({
              name: "registerSucess",
            });
          });
        }
      });
    },
  },
};
</script>

<style lang="less" scoped>
.register_box {
  // display: flex;
  // box-sizing: border-box;
  // width: 998px;
  // height: 672px;
  // margin: 82px auto 0;
  // background: rgba(255, 255, 255, 1);
  // // border: 10px solid rgba(255, 255, 255, 0.18);
  // box-shadow: 0px 0px 18px 0px rgba(237, 237, 237, 0.96);\
  width: 100%;
  height: 100vh;
  .register_main {
    display: flex;
    width: 100%;
    background: rgba(255, 255, 255, 1);
    box-shadow: 0px 0px 18px 0px rgba(237, 237, 237, 0.96);
    .register_banner {
      // width: 538.6px;
      // height: 670.6px;
      // flex: 1;
      // box-sizing: border-box;
      width: 50%;
      .register_img {
        width: 100%;
        height: 100vh;
        background-color: #3f5f5b;
        text-align: center;
        .img_box {
          width: 60%;
          height: 60vh;
          margin-top: 20%;
          // margin-left: 200px;
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
  }
  .register_form_box {
    width: 50%;
    .register_form {
      // margin-left: 80px;
      // margin-right: 80px;
      // overflow: hidden;
      width: 40%;
      margin: 0 auto;
      .register_title {
        margin-top: 50%;
        text-align: left;
        .title_header {
          font-size: 24px;
          font-family: Microsoft YaHei;
          font-weight: 400;
          color: #376455;
        }
      }
      .code_desc {
        cursor: pointer;
      }
    }
  }

  .form_btn {
    text-align: center;
    .register_btn {
      margin-top: 20px;
      width: 100%;
      height: 50px;
      font-size: 18px;
      background: #28b482;
      font-weight: 400;
    }
    .register_btn_desc {
      line-height: 28px;
      font-size: 12px;
      font-family: Microsoft YaHei;
      font-weight: 400;
      color: #000000;
      white-space: nowrap;
    }
    .register_to_login {
      font-size: 14px;
      font-family: Microsoft YaHei;
      font-weight: 400;
      color: #28b482;
    }
  }
  .login_footer {
    position: absolute;
    bottom: 20px;
    right: 22%;
    // color: ;
  }
}
</style>
<style scoped>
.el-form-item {
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
