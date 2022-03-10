<template>
  <div class="Information-box">
    <el-tabs v-model="activeName" @tab-click="handleClick">
      <!-- 企业信息配置 -->
      <el-tab-pane
        :label="$t('Information')"
        name="1"
        v-if="enterpriseInfoConfiguration"
      >
        <company-infoConfig></company-infoConfig>
      </el-tab-pane>
      <!-- 自定义界面 -->
      <el-tab-pane
        :label="$t('customInterface')"
        name="2"
        v-if="customInterface"
      >
        <custom-interface></custom-interface>
      </el-tab-pane>
      <!-- 修改密码 -->
      <el-tab-pane :label="$t('PasswordModify')" name="3" v-if="passwordModify">
        <password-modify></password-modify>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>
<script>
import CompanyInfoConfig from "@/page/settingManagement/components/CompanyInfoConfig";
import CustomInterface from "@/page/settingManagement/components/CustomInterface";
import PasswordModify from "@/page/settingManagement/components/PasswordModify";

export default {
  name: "Information",
  components: {
    CompanyInfoConfig,
    PasswordModify,
    CustomInterface,
  },
  data() {
    return {
      activeName: "1",
      enterpriseInfoConfiguration: false,
      passwordModify: false,
      customInterface: false,
    };
  },
  activated() {
    if (this.$route.params.type) {
      this.updataType();
    }
  },
  mounted() {
    this.updataType();
    this.getAuthTree();
  },
  methods: {
    handleClick(tab, event) {
      //console.log(tab, event);
    },
    updataType() {
      let type = this.$route.params.type;
      if (type == 1) {
        this.activeName = "1";
      } else if (type == 2) {
        this.activeName = "2";
      } else if (type == 3) {
        this.activeName = "3";
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
                    if (j.name == "公司信息配置") {
                      this.enterpriseInfoConfiguration = true;
                    } else if (j.name == "修改密码") {
                      this.passwordModify = true;
                    } else if (j.name == "自定义界面") {
                      this.customInterface = true;
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
.Information-box {
  background-color: #fff;
  padding: 20px;
}
</style>