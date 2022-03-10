<template>
  <!-- 调度管理 -->
  <div class="scheduling-configuration">
    <div class="scheduling-box">
      <!-- 调度规则 -->
      <div
        class="scheduling-configuration-box"
        @click="toDispatchDispatch"
        v-if="schedulingRuleState"
      >
        <div class="scheduling-configuration-inbox">
          <div
            class="scheduling-configuration-log"
            style="background-color: #59b287"
          >
            <img
              src="../../assets/img/configuration-img/control.png"
              alt=""
              class="scheduling-configuration-img"
            />
          </div>
          <div class="scheduling-configuration-headline">
            {{ $t("schedulingRule") }}
          </div>
          <div class="scheduling-configuration-title">
            {{ $t("schedulingRuleState") }}
          </div>
          <el-button size="medium" class="scheduling-configuration-button">
            {{ $t("Show") }}</el-button
          >
        </div>
      </div>
      <!-- 邮编划分 -->
      <div
        class="scheduling-configuration-box"
        @click="toDispatchMail"
        v-if="postcodeBreakdown"
      >
        <div class="scheduling-configuration-inbox">
          <div
            class="scheduling-configuration-log"
            style="background-color: #f2a67c"
          >
            <img
              src="../../assets/img/configuration-img/mailbox.png"
              alt=""
              class="scheduling-configuration-img"
            />
          </div>
          <div class="scheduling-configuration-headline">
            {{ $t("postcodeBreakdown") }}
          </div>
          <div class="scheduling-configuration-title">
            {{ $t("postcodeBreakdownState") }}
          </div>
          <el-button size="medium" class="scheduling-configuration-button">
            {{ $t("Show") }}</el-button
          >
        </div>
      </div>
      <!-- 区域划分 -->
      <!-- <div
        class="scheduling-configuration-box"
        @click="toDispatchArea"
        v-if="areaBreakdownState"
      >
        <div class="scheduling-configuration-inbox">
          <div
            class="scheduling-configuration-log"
            style="background-color: #ef8d75"
          >
            <img
              src="../../assets/img/configuration-img/area.png"
              alt=""
              class="scheduling-configuration-img"
            />
          </div>
          <div class="scheduling-configuration-headline">
            {{ $t("areaBreakdown") }}
          </div>
          <div class="scheduling-configuration-title">
            {{ $t("areaBreakdownState") }}
          </div>
          <el-button size="medium" class="scheduling-configuration-button">
            {{ $t("Show") }}</el-button
          >
        </div>
      </div> -->
      <!-- 测试流程 -->
      <div
        class="scheduling-configuration-box"
        @click="toTestingProcess"
        v-if="testProcedureState"
      >
        <div class="scheduling-configuration-inbox">
          <div
            class="scheduling-configuration-log"
            style="background-color: #ef8ea1"
          >
            <img
              src="../../assets/img/configuration-img/flow-chart.png"
              alt=""
              class="scheduling-configuration-img"
            />
          </div>
          <div class="scheduling-configuration-headline">
            {{ $t("testProcedure") }}
          </div>
          <div class="scheduling-configuration-title">
            {{ $t("testProcedureState") }}
          </div>
          <el-button size="medium" class="scheduling-configuration-button">
            {{ $t("Show") }}</el-button
          >
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      schedulingRuleState: false, //调度规则
      testProcedureState: false, //测试流程
      postcodeBreakdown: false, //邮编划分
      areaBreakdownState: false, //区域划分
    };
  },
  created() {
    this.getAuthTree();
  },
  methods: {
    // 调度规则
    toDispatchDispatch() {
      this.$router.push({
        name: "Dispatch",
        query: {
          type: 1,
        },
      });
    },

    toDispatchMail() {
      this.$router.push({
        name: "Dispatch",
        query: {
          type: 2,
        },
      });
    },
    toDispatchArea() {
      this.$router.push({
        name: "Dispatch",
        query: {
          type: 3,
        },
      });
    },
    // 测试流程
    toTestingProcess() {
      this.$router.push({
        name: "Dispatch",
        query: {
          type: 4,
        },
      });
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
.scheduling-configuration {
  .scheduling-box {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    grid-gap: 20px;
  }
  .scheduling-configuration-box:hover {
    background-color: #e7e7e7;
    cursor: pointer;
  }
  .scheduling-configuration-box {
    width: 100%;
    height: 240px;
    background: white;
    // margin-bottom: 20px;
    text-align: center;
    .scheduling-configuration-inbox {
      width: 90%;
      padding-top: 20px;
      margin: 0 auto;
      .scheduling-configuration-log {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin: 0 auto;
        text-align: center;
        .scheduling-configuration-img {
          margin-top: 8px;
          width: 23px;
        }
      }
      .scheduling-configuration-headline {
        font-size: 18px;
        font-weight: bold;
        color: #636d7c;
        margin-top: 10px;
      }
      .scheduling-configuration-title {
        margin-top: 15px;
        height: 35px;
        color: #999c9f;
      }
      .scheduling-configuration-ddbutton {
        margin-top: 50px;
        border-radius: 5px !important;
        border: #59b287;
        background-color: #59b287;
        color: white;
      }
      .scheduling-configuration-button {
        margin-top: 35px;
        border-radius: 5px !important;
        border: #59b287;
        background-color: #59b287;
        color: white;
      }
    }
  }
}
</style>
