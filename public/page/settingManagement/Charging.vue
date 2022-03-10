<template>
  <div class="charging-box">
    <el-tabs v-model="activeName" @tab-click="handleClick">
      <!-- 运价设置 -->
      <el-tab-pane
        :label="$t('FreightManagement')"
        name="1"
        v-if="freightRateSettings"
      >
        <freight-management></freight-management>
      </el-tab-pane>
      <!-- 增值服务 -->
      <el-tab-pane
        :label="$t('valueaddedServices')"
        name="2"
        v-if="valueaddedServices"
      >
        <valueAddedServices></valueAddedServices>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>
<script>
import FreightManagement from "@/page/companyManagement/FreightManagementList"; //运价设置
import ValueAddedServices from "@/page/companyManagement/ValueAddedServicesList"; //增值服务

export default {
  name: "Charging",
  components: {
    ValueAddedServices,
    FreightManagement,
  },
  data() {
    return {
      activeName: "1",
      freightRateSettings: false, //运价设置
      valueaddedServices: false, //增值服务
    };
  },
  activated() {
    if (this.$route.query.type) {
      this.upDataType();
    }
  },
  mounted() {
    this.upDataType();
    this.getAuthTree();
  },
  methods: {
    handleClick(tab, event) {
      //console.log(tab, event);
    },
    upDataType() {
      let type = this.$route.query.type;
      if (type == 1) {
        this.activeName = "1";
      } else if (type == 2) {
        this.activeName = "2";
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
                    if (j.name == "增值服务") {
                      this.valueaddedServices = true;
                    } else if (j.name == "运价") {
                      this.freightRateSettings = true;
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
.charging-box {
  background-color: #fff;
  padding: 5px 20px;
  // min-height: calc(100vh - 300px);
}
</style>