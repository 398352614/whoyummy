<template>
  <div class="h5-configuration">
    <el-tabs v-model="activeName">
      <el-tab-pane
        :label="$t('PaymentConfiguration')"
        name="1"
        v-if="paymentConfiguration"
      >
        <payment-configuration></payment-configuration>
      </el-tab-pane>
      <el-tab-pane
        :label="$t('CarouselManagement')"
        name="2"
        v-if="carouselManagement"
      >
        <carousel-management></carousel-management>
      </el-tab-pane>
      <el-tab-pane
        :label="$t('ArticleManagement')"
        name="3"
        v-if="articleManagement"
      >
        <article-management></article-management>
      </el-tab-pane>
      <el-tab-pane
        :label="$t('ClauseManagement')"
        name="4"
        v-if="clauseManagement"
      >
        <clause-management></clause-management>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script>
import PaymentConfiguration from "../settingManagement/components/PaymentConfiguration.vue";
import ArticleManagement from "../settingManagement/components/ArticleManagement.vue";
import CarouselManagement from "../settingManagement/components/CarouselManagement.vue";
import ClauseManagement from "../settingManagement/components/ClauseManagement.vue";

export default {
  components: {
    PaymentConfiguration,
    ArticleManagement,
    CarouselManagement,
    ClauseManagement,
  },
  data() {
    return {
      activeName: "1",
      tabPosition: "left",
      paymentConfiguration: false, //支付配置
      carouselManagement: false, //轮播图
      articleManagement: false, //文章
      clauseManagement: false, //条款
    };
  },
  activated() {
    if (this.$route.query.type) {
      this.updateType();
    }
  },
  mounted() {
    this.updateType();
    this.getAuthTree();
  },
  methods: {
    updateType() {
      let type = this.$route.query.type;
      // 判断type的值，更改activeName的值
      if (type === 1) {
        this.activeName = "1";
      } else if (type === 2) {
        this.activeName = "2";
      } else if (type === 3) {
        this.activeName = "3";
      } else if (type === 4) {
        this.activeName = "4";
      }
    },
    // 权限
    getAuthTree() {
      this.$api.getAuthTree().then((res) => {
        let menu_list = res.data.menu_list;
        menu_list.map((item) => {
          item.children.map((el) => {
            if (el.children) {
              el.children.forEach((i) => {
                if (i.children) {
                  i.children.map((j) => {
                    if (j.name == "支付配置") {
                      this.paymentConfiguration = true;
                    } else if (j.name == "轮播图配置") {
                      this.carouselManagement = true;
                    } else if (j.name == "文章配置") {
                      this.articleManagement = true;
                    } else if (j.name == "条款配置") {
                      this.clauseManagement = true;
                    }
                  });
                }
              });
            }
          });
        });
      });
    },
  },
};
</script>

<style lang="less" scoped>
.h5-configuration {
  min-height: 80vh;
  padding: 20px 20px;
  background-color: #fff;
}
</style>
