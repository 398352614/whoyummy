<template>
  <div class="basics-box">
    <el-tabs v-model="activeName" @tab-click="handleClick">
      <!-- 订单设置 -->
      <el-tab-pane :label="$t('Set')" name="1" v-if="set">
        <indent-set></indent-set>
      </el-tab-pane>
      <!-- 面单设置 -->
      <el-tab-pane :label="$t('ExpressSheet')" name="2" v-if="prin">
        <print-management></print-management>
      </el-tab-pane>
      <!-- 单号规则设置 -->
      <el-tab-pane :label="$t('SingleRule')" name="3" v-if="rule">
        <rule></rule>
      </el-tab-pane>
      <!-- 计量单位设置 -->
      <el-tab-pane :label="$t('Measure')" name="4" v-if="measure">
        <measure></measure>
      </el-tab-pane>
      <!-- 地图引擎 -->
      <el-tab-pane :label="$t('MapEngine')" name="5" v-if="map">
        <engine></engine>
      </el-tab-pane>
      <!-- 仓库地址设置 -->
      <!-- <el-tab-pane :label="$t('WarehouseAddress')" name="6">
        <warehouse-address></warehouse-address>
      </el-tab-pane> -->
      <!-- 网点 -->
      <el-tab-pane :label="$t('Network')" name="6" v-if="netWork">
        <network></network>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>
<script>
import IndentSet from "@/page/settingManagement/components/IndentSet"; //订单设置
import PrintManagement from "@/page/settingManagement/components/PrintManagement"; //打印
import rule from "@/page/settingManagement/components/rule"; //单号规则
import Measure from "@/page/settingManagement/components/Measure"; //计量单位设置
import Engine from "@/page/settingManagement/components/Engine"; //地图引擎
// import WarehouseAddress from "@/page/settingManagement/components/WarehouseAddress"; //仓库地址设置
import Network from "@/page/settingManagement/components/Network"; //网点

//仓库地址设置

export default {
  name: "Basics",

  components: {
    IndentSet,
    PrintManagement,
    Measure,
    Engine,
    rule,
    Network,
  },
  data() {
    return {
      activeName: "1",
      set: false, //订单设置
      prin: false, //面单
      rule: false, //单号规则
      measure: false, //计量单位
      map: false, //地图引擎
      netWork: false, //网点
    };
  },
  activated() {
    if (this.$route.query.type) {
      this.updateType();
    }
  },
  mounted() {
    this.updateType();
  },
  created() {
    this.getAuthTree();
  },
  methods: {
    handleClick(tab, event) {
      //console.log(tab, event);
    },
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
      } else if (type === 5) {
        this.activeName = "5";
      } else if (type === 6) {
        this.activeName = "6";
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
                    if (j.name == "网点地址设置") {
                      this.netWork = true;
                    } else if (j.name == "单号规则设置") {
                      this.rule = true;
                    } else if (j.name == "面单配置") {
                      this.prin = true;
                    } else if (j.name == "订单设置") {
                      this.set = true;
                    } else if (j.name == "地图引擎") {
                      this.map = true;
                    } else if (j.name == "计量单位") {
                      this.measure = true;
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
.basics-box {
  background-color: #fff;
  padding: 20px;
  min-height: calc(100vh - 158px);
}
</style>