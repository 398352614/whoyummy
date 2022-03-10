<template>
  <div class="dispatch-box">
    <el-tabs v-model="activeName" @tab-click="handleClick">
      <!-- 调度规则 -->
      <el-tab-pane
        :label="$t('schedulingRule')"
        name="1"
        v-if="schedulingRuleState"
      >
        <scheduling></scheduling>
      </el-tab-pane>
      <!-- 邮编划分 -->
      <el-tab-pane
        :label="$t('postcodeBreakdown')"
        name="2"
        v-if="postcodeBreakdown"
      >
        <linePlanningPostCodeList></linePlanningPostCodeList>
      </el-tab-pane>
      <!-- 区域划分 -->
      <!-- <el-tab-pane
        :label="$t('areaBreakdown')"
        name="3"
        v-if="areaBreakdownState"
      >
        <linePlanningArea></linePlanningArea>
      </el-tab-pane> -->
      <!-- 测试流程 -->
      <el-tab-pane
        :label="$t('testProcedure')"
        name="4"
        v-if="testProcedureState"
      >
        <testingProcess></testingProcess>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>
<script>
import Scheduling from "@/page/settingManagement/components/Scheduling"; //调度规则
import LinePlanningPostCodeList from "@/page/exitManagement/LinePlanningPostCodeList"; //邮编划分
import LinePlanningArea from "@/page/exitManagement/LinePlanningArea"; //区域划分
import TestingProcess from "@/page/settingManagement/components/TestingProcess"; //测试流程

export default {
  name: "Dispatch",
  components: {
    Scheduling,
    LinePlanningPostCodeList,
    LinePlanningArea,
    TestingProcess,
  },
  data() {
    return {
      activeName: "1",
      schedulingRuleState: false, //调度规则
      testProcedureState: false, //测试流程
      postcodeBreakdown: false, //邮编划分
      areaBreakdownState: false, //区域划分
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
                    if (j.name == "邮编划分") {
                      this.postcodeBreakdown = true;
                    } else if (j.name == "区域划分") {
                      this.areaBreakdownState = true;
                    } else if (j.name == "调度规则") {
                      this.schedulingRuleState = true;
                    } else if (j.name == "测试流程") {
                      this.testProcedureState = true;
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
.dispatch-box {
  background-color: #fff;
  padding: 20px 20px 5px;
  min-height: calc(100vh - 143px);
}
</style>