<template>
  <div
    :class="[
      'side_nav',
      sideNavIsCollapse
        ? 'side_collapse'
        : sideNavStatus
        ? 'nav_left'
        : 'init_nav_left',
    ]"
  >
    <!-- 侧边栏导航 -->
    <side-nav :sideMenu="sideMenu"></side-nav>

    <div class="title">
      <!-- 面包屑 -->
      <!-- <breadcrumb></breadcrumb> -->
      <!-- 返回键 -->
      <!-- <div class="go_back">
        <go-back></go-back>
      </div> -->
      <tagsview class="tab-menu" />
    </div>

    <!-- 右侧主要内容展示区 -->
    <div class="content">
      <keep-alive :include="cachedViews">
        <router-view :key="key"></router-view>
      </keep-alive>
    </div>

    <!-- 底部说明 -->
    <!-- <div class="footer">
      <base-footer></base-footer>
    </div> -->
  </div>
</template>
<script>
import BaseFooter from "@/components/BaseFooter/BaseFooter";
import GoBack from "@/components/goBack";
import Breadcrumb from "@/components/breadcrumb";
import SideNav from "@/components/sideNav";
import TransitionCustom from "@/components/transition";
import tagsview from "./components/tagsview";

export default {
  name: "Side",
  components: {
    SideNav,
    GoBack,
    Breadcrumb,
    BaseFooter,
    TransitionCustom,
    tagsview,
  },
  computed: {
    cachedViews() {
      return this.$store.state.tagsView.cachedViews;
    },
    sideNavIsCollapse() {
      return this.$store.state.globalData.sideNavIsCollapse;
    },
    sideMenu() {
      return this.$store.state.routerData.routerMap[0].children.filter(
        (item) => item.name === this.$route.matched[1].name
      )[0];
    },

    key() {
      return this.$route.path + this.$store.state.config.refreshRouterFlag;
    },

    sideNavStatus() {
      return +this.$store.state.config.sideNavStatus;
    },
  },
};
</script>

<style lang="less">
@import "../../less/public_variable.less";

.side_collapse {
  padding-left: 60px;
  transition: padding-left 0.3s ease-in-out;
}

.nav_left {
  padding-left: 150px;
  transition: padding-left 0.3s ease-in-out;
}

.init_nav_left {
  padding-left: 150px;
  transition: padding-left 0.3s ease-in-out;
}

.side_nav {
  .title {
    height: 40px;
    box-sizing: border-box;
    // position: sticky;
    // min-width: 100%;
    // z-index: 999;
    // top: 54px;
    // // display: flex;
    // // justify-content: space-between;
    // // align-items: center;
    // padding-left: 20px;
    // height: 40px;
    // border-top: 1px solid #e8e9eb;
    border-bottom: 1px solid #e8e9eb;
    background-color: #fff;
    // .go_back {
    //   position: absolute;
    //   top: 0;
    //   right: 246px;
    //   height: 40px;
    //   line-height: 40px;
    //   display: block;
    //   color: #606266;
    //   cursor: pointer;
    //   z-index: 99999;
    //   background-color: #fff;
    // }
    .tab-menu {
      width: 100%;
    }
  }
  .content {
    height: calc(100vh - 98px);
    box-sizing: border-box;
    padding: 10px;
    width: 100%;
    overflow: auto;
  }

  // .footer {
  //   box-sizing: border-box;
  //   overflow: hidden;
  // }
}
</style>
