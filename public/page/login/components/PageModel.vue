<template>
  <div class="page_model" :style="styleObj">
    <!-- <header class="header">
      <div class="logo">TMS</div>
      <div class="lang_switch">
        <el-dropdown>
          <div class="lang_text">
            Language
          </div>
          <el-dropdown-menu slot="dropdown">
            <el-dropdown-item
              v-for="item in lang"
              :key="item.id"
              @click.native="handlerSwitchLang(item.id)">
              {{item.lang}}
            </el-dropdown-item>
          </el-dropdown-menu>
        </el-dropdown>
      </div>
    </header> -->
    <div class="content">
      <slot></slot>
    </div>
    <!-- <div class="lang">
      <span :class="{'check': isLang === 'cn'}" @click="handlerSwitchLang(1)">中文</span>
      <span :class="{'check': isLang === 'en'}" @click="handlerSwitchLang(2)">English</span>
    </div> -->
    <!-- <base-footer></base-footer> -->
  </div>
</template>

<script>
// import BaseFooter from "@/components/BaseFooter/BaseFooter";

export default {
  components: {
    // BaseFooter,
  },
  props: {
    styleObj: {
      type: Object,
      default() {
        return {
          // isLang: null
        };
      },
    },
  },
  data() {
    return {
      lang: [
        {
          id: 1,
          lang: "CN",
        },
        {
          id: 2,
          lang: "EN",
        },
      ],
    };
  },
  created() {
    this.isLang = localStorage.getItem("lang");
  },
  methods: {
    handlerSwitchLang(id) {
      if (id === 1) {
        this.$i18n.locale = "cn";
        this.isLang = 'cn'
        localStorage.setItem("lang", "cn");
      }
      if (id === 2) {
        this.$i18n.locale = "en";
        this.isLang = 'en'
        localStorage.setItem("lang", "en");
      }
    },
  },
};
</script>

<style lang="less" scoped>
@import "../../../less/public_variable.less";

.page_model {
  overflow: hidden;
  background-color: #e9e9e9;
  // background-image: url('../../../assets/img/login_bg.png');
  .header {
    background-color: @ThemeColor;
    width: 100%;
    height: 80px;
    display: flex;
    justify-content: center;
    align-items: center;
    .lang_switch {
      position: absolute;
      top: 30px;
      right: 10px;
      .lang_text {
        font-size: 18px;
        cursor: pointer;
      }
    }
    .logo {
      font-size: 30px;
      color: #fff;
      font-weight: bold;
    }
  }
  // .content {
  //   // border-top: 1px solid #fff;
  //   // margin-bottom: 81px;
  // }
  .lang {
    text-align: center;
    padding: 10px 0 10px 105px;
    margin-bottom: 81px;
    color: #c4c4c4;
    .check {
      color: #000;
    }
    span {
      margin: 10px 0;
      padding: 0 10px;
      cursor: pointer;
      font-weight: bold;
      &:first-child {
        border-right: 2px solid #000;
      }
    }
  }
}
</style>
