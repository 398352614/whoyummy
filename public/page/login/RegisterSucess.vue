<template>
  <page-model :styleObj="customStyleObject">
    <div class="rg_box">
      <div class="rg_main">
        <div class="rg_banner">
          <div class="rg_img">
            <img src="@/assets/img/ht.png" class="img_box" alt="" />
            <div class="tmsht">
              <span class="hongTu">红兔</span
              ><span class="tms">TMS-专业城市配送TMS</span>
            </div>
          </div>
        </div>
        <div class="rg_right_box">
          <div class="rg_right">
            <div class="rg_title">
              <img src="@/assets/img/correct.png" alt="" />
              <p class="title_header">{{ $t("registerSuccess") }}!</p>
            </div>
          </div>
          <div class="rg_bt_box">
            <span class="rg_btn" @click="handlerToLogin"
              >{{ $t("toLogin") }}></span
            >
          </div>
          <div class="rg_footer">
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
  name: "registerSucess",
  components: {
    PageModel,
  },
  data() {
    return {
      customStyleObject: {
        background: "#fff",
      },
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
    // 登录
    handlerToLogin() {
      this.$router.push({
        name: "login",
      });
    },
  },
};
</script>

<style lang="less" scoped>
.rg_box {
  // margin: 80px 0 0 0;
  // min-height: 620px;
  width: 100%;
  height: 100vh;
  .rg_main {
    // margin: 0 auto;
    // width: 400px;
    // text-align: center;
    // box-sizing: border-box;
    display: flex;
    width: 100%;
    background: rgba(255, 255, 255, 1);
    // border: 1px solid rgba(255, 255, 255, 0.18);
    box-shadow: 0px 0px 18px 0px rgba(237, 237, 237, 0.96);
    .rg_banner {
      width: 50%;
      .rg_img {
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
    .rg_right_box {
      width: 50%;
      .rg_right {
        width: 58%;
        margin: 0 auto;
        .rg_title {
          margin-top: 50%;
          text-align: center;
          .title_header {
            font-size: 30px;
            font-family: Microsoft YaHei;
            font-weight: 400;
            color: rgba(40, 208, 148, 1);
            // line-height: 47px;
          }
        }
      }
      .rg_desc {
        font-size: 40px;
      }
      .rg_bt_box {
        text-align: center;
        margin-top: 20px;
        .rg_btn {
          // display: inline-block;
          // text-align: center;
          font-size: 14px;
          font-family: Microsoft YaHei;
          font-weight: 400;
          color: rgba(40, 208, 148, 1);
          cursor: pointer;
        }
      }
      .rg_footer {
        position: absolute;
        bottom: 20px;
        right: 22%;
      }
    }
  }
}
</style>
