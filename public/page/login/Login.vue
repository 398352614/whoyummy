<template>
  <page-model>
    <div class="login_box">
      <div class="login_main">
        <div class="login_banner">
          <div v-if="adminLoginBackground" style="height: 100%">
            <img
              :src="imgObj.admin_login_background"
              alt=""
              width="100%"
              style="height: 100%"
            />
          </div>
          <div class="login_img_box" v-if="defaultBackground">
            <div class="login_img">
              <img src="@/assets/img/ht.png" class="img_box" alt="" />
              <div class="tmsht">
                <span class="hongTu">红兔</span
                ><span class="tms">TMS-专业城市配送TMS</span>
              </div>
            </div>
          </div>
        </div>
        <div class="login_form_box">
          <div class="login_form">
            <div class="login_title">
              <div v-if="adminLoginTitle">
                <img
                  :src="imgObj.admin_login_title"
                  alt=""
                  width="100%"
                  height="67px"
                />
              </div>
              <div v-if="defaultTitle">
                <div class="title_1">{{ $t("RedRabbit") }}</div>
                <span class="title_2">{{ $t("ParcelPickupSystem") }}</span
                ><span class="title_3">Pacel TMS</span>
              </div>
            </div>
            <el-form
              :model="loginForm"
              :rules="formRules"
              ref="loginForm"
              style="margin-top: 40px"
            >
              <el-form-item prop="username" type="email">
                <el-input
                  v-model="loginForm.username"
                  prefix-icon="el-icon-user-solid"
                  :placeholder="$t('Pleaseenteryouremail')"
                  auto-complete="off"
                ></el-input>
              </el-form-item>
              <el-form-item prop="password">
                <el-input
                  v-model="loginForm.password"
                  prefix-icon="el-icon-lock"
                  :placeholder="$t('Pleaseenteryourpassword')"
                  type="password"
                  auto-complete="off"
                ></el-input>
              </el-form-item>
            </el-form>
            <div class="forget_psw">
              <el-checkbox class="psw_autoLogin" v-model="keep">{{
                $t("Password")
              }}</el-checkbox>
              <span class="psw_desc" @click="handlerForgetPassword"
                >{{ $t("ForgetPassword") }}?</span
              >
            </div>
            <div class="form_btn">
              <el-button
                class="login_btn"
                size="small"
                type="primary"
                @keyup.enter.native="handlerLogin"
                @click.native="handlerLogin"
                :loading="this.$store.state.config.button_loading"
              >
                {{ $t("Login") }}
              </el-button>
              <!-- <el-button
                class="register_btn"
                size="small"
                type="text"
                @click.native="handlerRegister"
              >
                {{ $t("Register") }}
              </el-button> -->
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
    return {
      formRules: {
        username: [
          { required: true, message: this.$t("Required"), trigger: "blur" },
        ],
        password: [
          { required: true, message: this.$t("Required"), trigger: "blur" },
        ],
      },
      show: false,
      loginForm: {
        username: "",
        password: "",
      },
      imgObj: {},
      keep: false,
      // 背景
      adminLoginBackground: false, //自定义
      defaultBackground: false, //默认
      // 标题
      adminLoginTitle: false, //自定义
      defaultTitle: false, //默认
    };
  },
  mounted() {
    window.addEventListener("keyup", (e) => {
      const that = this;
      if (e.key === "Enter" && this.$route.name === "login") {
        that.handlerLogin && that.handlerLogin();
      }
    });
  },
  created() {
    // 获取界面数据
    let host = window.location.host;
    // 判断域名不是dev-tms-admin.nle-tech.com请求
    if (host == "dev-tms-admin.nle-tech.com") {
      this.defaultBackground = true;
      this.defaultTitle = true;
    } else {
      this.$api.getFace({ url: host }).then((res) => {
        // let host = "https://admin.jh77express.com";
        // 判断界面展示
        if (res.code == 200) {
          this.imgObj = res.data;
          // 判断背景
          if (
            res.data.admin_login_background &&
            res.data.admin_login_background != ""
          ) {
            this.adminLoginBackground = true;
          } else {
            this.defaultBackground = true;
          }
          // 判断标题
          if (res.data.admin_login_title && res.data.admin_login_title != "") {
            this.adminLoginTitle = true;
          } else {
            this.defaultTitle = true;
          }
        } else {
          this.defaultBackground = true;
          this.defaultTitle = true;
        }
      });
    }
  },
  methods: {
    // 时区
    getTimezone() {
      let event = new Date();
      let time = event.toString();
      let arr = time.split(" ");
      let list = arr[5];
      let el = list.split("T");
      let timezone = el[1];
      this.$api.getTimezone({ timezone }).then((res) => {});
    },
    // 改变界面
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
    handlerEnter() {},
    handlerLogin() {
      if (this.keep) {
        localStorage.setItem("USERNAME", this.loginForm.username.trim());
        localStorage.setItem("PASSWORD", this.loginForm.password.trim());
      } else {
        localStorage.removeItem("USERNAME");
        localStorage.removeItem("PASSWORD");
      }
      this.$refs.loginForm.validate((vaild) => {
        if (vaild) {
          this.$api
            .login({
              username: this.loginForm.username,
              password: this.loginForm.password,
            })
            .then((res) => {
              this.getTimezone(); //时区
              if (res.code !== 200) return;
              if (res.data.is_permission == 2) {
                this.$message({
                  message: "当前账号无登录权限, 请联系管理员确定。",
                  type: "error",
                });
                return;
              }
              this.$store.dispatch("globalData/getDictionary");
              // this.$store.dispatch('globalData/getAuthTree');
              this.$store.commit(
                "routerData/switchPermissionMapFilterStatus",
                false
              ); // 重新加载路由配置
              const token = res.data.access_token;
              localStorage.setItem("user_name", res.data.username);
              localStorage.setItem("user_email", this.loginForm.username);

              // 储存登录信息
              localStorage.setItem("user_info", JSON.stringify(res.data));

              // 国家
              let countryObj = {
                cn: res.data.company_config.country_cn_name,
                en: res.data.company_config.country,
              };
              localStorage.setItem("country", JSON.stringify(countryObj));

              // // 模板标识
              // localStorage.setItem(
              //   "address_template_id",
              //   res.data.company_config.address_template_id
              // );

              // 线路分配规则
              localStorage.setItem(
                "line_rule",
                res.data.company_config.line_rule
              );

              // 线路分配显示
              localStorage.setItem(
                "show_type",
                res.data.company_config.show_type
              );

              this.$store.commit("token/addToken", {
                token,
                keep: this.keep,
              });
              this.$store.commit(
                "globalData/setUnit",
                res.data.company_config.currency_unit_symbol
              ); // 设置货币单位

              this.$store.commit(
                "globalData/setWeight",
                res.data.company_config.weight_unit
              ); // 设置重量单位

              this.$store.commit(
                "globalData/setMaptype",
                res.data.company_config.map
              );
              if (
                !(
                  res.data.company_config.hasOwnProperty(
                    "address_template_id"
                  ) && res.data.company_config.hasOwnProperty("line_rule")
                )
              ) {
                // 如果是新用户，就跳到高级配置页面
                this.$router.push({
                  name: "CompanyInfoConfig",
                });
              } else {
                this.$router.push({
                  name: "controlPanel",
                });
              }
            });
        }
      });
    },
    handlerRegister() {
      this.$router.push({
        name: "register",
      });
    },
    handlerForgetPassword() {
      this.$router.push({
        name: "forgetPassword",
      });
    },
  },
};
</script>

<style lang="less" scoped>
.login_box {
  // margin: 82px 0 0 0;
  // min-height: 620px;
  width: 100%;
  height: 100vh;
  // /deep/ .el-form-item__label {
  //   line-height: 25px;
  // }
  .login_main {
    // margin: 0 auto;
    display: flex;
    // box-sizing: border-box;
    width: 100%;
    // height: 672px;
    background: rgba(255, 255, 255, 1);
    // border: 1px solid rgba(255, 255, 255, 0.18);
    box-shadow: 0px 0px 18px 0px rgba(237, 237, 237, 0.96);
    .login_banner {
      width: 50%;
      height: 100%;
      height: 100vh;
      // flex: 1;
      // box-sizing: border-box;
      .login_img_box {
        width: 100%;
        height: 100vh;
        background-color: #3f5f5b;
        text-align: center;
        display: flex;
        align-items: center;

        .login_img {
          width: 100%;
          // height: 100vh;
          align-items: center;
          text-align: center;
          .img_box {
            width: 60%;
            // height: 60vh;
            // margin-top: 20%;
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
    .login_form_box {
      width: 50%;
      .login_form {
        width: 40%;
        margin: 0 auto;
        // flex: 1;
        // margin-left: 80px;
        // margin-right: 80px;
        .login_title {
          margin-top: 50%;
          // margin-bottom: 77px;
          text-align: left;
          .title_1 {
            font-size: 20px;
            font-family: Microsoft YaHei;
            font-weight: bold;
            color: #ff6262;
            line-height: 20px;
            margin-bottom: 0px;
          }
          .title_2 {
            font-size: 24px;
            font-family: Microsoft YaHei;
            font-weight: 400;
            color: #376455;
            line-height: 24px;
          }
          .title_3 {
            font-size: 14px;
            font-family: Microsoft YaHei;
            font-weight: bold;
            color: #28d094;
            line-height: 47px;
            vertical-align: super;
            margin-left: 11px;
          }
        }
        .el-input__inner {
          margin-top: 0;
        }
        /deep/.el-input__icon {
          color: #376455;
        }
        .el-form {
          line-height: 20px;
        }
      }
      .form_btn {
        text-align: center;
        margin-top: 19px;
        .login_btn {
          width: 100%;
          height: 50px;
          color: #fff;
          background: #28b482;
          font-size: 18px;
          font-weight: 400;
        }
        .register_btn {
          margin: 10px 0 0 0;
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
        right: 22%;
        // color: ;
      }
      .forget_psw {
        display: flex;
        justify-content: space-between;
        margin: -9px 0 9px;
        .psw_autoLogin {
          font-size: 14px;
          font-family: Microsoft YaHei;
          font-weight: 400;
          color: #c6d0d8;
          cursor: pointer;
        }
        .psw_desc {
          font-size: 14px;
          font-family: Microsoft YaHei;
          font-weight: 400;
          color: #28b482;
          cursor: pointer;
        }
      }
    }
  }
}
</style>
<style scoped>
.el-form-item {
  margin-bottom: 20px;
}
.el-form-item__label {
  font-size: 14px;
  font-family: Microsoft YaHei;
  font-weight: 400;
  color: rgba(40, 208, 148, 1);
  line-height: 47px;
} /* 更改组件的样式 */
</style>
