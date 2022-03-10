<template>
  <!-- 基础配置 -->
  <div class="basics-configuration">
    <div class="basics-box">
      <!-- 订单设置 -->
      <div class="basics-configuration-box" @click="toBasicsUnit" v-if="set">
        <div class="basics-configuration-inbox">
          <div
            class="basics-configuration-log"
            style="background-color: #59b287"
          >
            <img
              src="../../assets/img/configuration-img/order-set.png"
              alt=""
              class="basics-configuration-img"
            />
          </div>
          <div class="basics-configuration-headline">{{ $t("Set") }}</div>
          <div class="basics-configuration-title">
            {{ $t("SetState") }}
          </div>
          <el-button size="medium" class="basics-configuration-button">
            {{ $t("Show") }}</el-button
          >
        </div>
      </div>
      <!-- 面单设置 -->
      <div class="basics-configuration-box" @click="toBasicsExpressSheet" v-if="prin">
        <div class="basics-configuration-inbox">
          <div
            class="basics-configuration-log"
            style="background-color: #f2a67c"
          >
            <img
              src="../../assets/img/configuration-img/file.png"
              alt=""
              class="basics-configuration-img"
            />
          </div>
          <div class="basics-configuration-headline">
            {{ $t("ExpressSheet") }}
          </div>
          <div class="basics-configuration-title">
            {{ $t("ExpressSheetState") }}
          </div>
          <el-button size="medium" class="basics-configuration-button">
            {{ $t("Show") }}</el-button
          >
        </div>
      </div>
      <!-- 单号规则设置 -->
      <div class="basics-configuration-box" @click="toBasicsOdd" v-if="rule">
        <div class="basics-configuration-inbox">
          <div
            class="basics-configuration-log"
            style="background-color: #ef8d75"
          >
            <img
              src="../../assets/img/configuration-img/tag.png"
              alt=""
              class="basics-configuration-img"
            />
          </div>
          <div class="basics-configuration-headline">
            {{ $t("SingleRule") }}
          </div>
          <div class="basics-configuration-title">
            {{ $t("OrderNumberState") }}
          </div>
          <el-button size="medium" class="basics-configuration-button">
            {{ $t("Show") }}</el-button
          >
        </div>
      </div>
      <!-- 计量单位设置 -->
      <div class="basics-configuration-box" @click="toBasicsMeasuringUnit" v-if="measure">
        <div class="basics-configuration-inbox">
          <div
            class="basics-configuration-log"
            style="background-color: #ef8ea1"
          >
            <img
              src="../../assets/img/configuration-img/set-square.png"
              alt=""
              class="basics-configuration-img"
            />
          </div>
          <div class="basics-configuration-headline">{{ $t("Measure") }}</div>
          <div class="basics-configuration-title">
            {{ $t("MeasureState") }}
          </div>
          <el-button size="medium" class="basics-configuration-button">
            {{ $t("Show") }}</el-button
          >
        </div>
      </div>
      <!-- 地图引擎 -->
      <div class="basics-configuration-box" @click="toBasicsMap" v-if="map">
        <div class="basics-configuration-inbox">
          <div
            class="basics-configuration-log"
            style="background-color: #869def"
          >
            <img
              src="../../assets/img/configuration-img/map.png"
              alt=""
              class="basics-configuration-img"
            />
          </div>
          <div class="basics-configuration-headline">
            {{ $t("MapEngine") }}
          </div>
          <div class="basics-configuration-title">
            {{ $t("MapEngineState") }}
          </div>
          <el-button size="medium" class="basics-configuration-button">
            {{ $t("Show") }}</el-button
          >
        </div>
      </div>

      <!-- 网点-->
      <div class="basics-configuration-box" @click="toBasicsWarehouse" v-if="netWork">
        <div class="basics-configuration-inbox">
          <div
            class="basics-configuration-log"
            style="background-color: #f7ce81"
          >
            <img
              src="../../assets/img/configuration-img/entrepot.png"
              alt=""
              class="basics-configuration-img"
            />
          </div>
          <div class="basics-configuration-headline">
            {{ $t("Network") }}
          </div>
          <div class="basics-configuration-title">
            {{ $t("NetWorkState") }}
          </div>
          <el-button size="medium" class="basics-configuration-button">
            {{ $t("Show") }}</el-button
          >
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "BasicConfiguration",
  data() {
    return {
      set: false, //订单设置
      prin: false, //面单
      rule: false, //单号规则
      measure: false, //计量单位
      map: false, //地图引擎
      netWork: false, //网点
    };
  },
  created() {
    this.getAuthTree();
  },
  methods: {
    toBasicsUnit() {
      this.$router.push({
        name: "Basics",
        query: {
          type: 1,
        },
      });
    },
    toBasicsExpressSheet() {
      this.$router.push({
        name: "Basics",
        query: {
          type: 2,
        },
      });
    },
    toBasicsOdd() {
      this.$router.push({
        name: "Basics",
        query: {
          type: 3,
        },
      });
    },
    toBasicsMeasuringUnit() {
      this.$router.push({
        name: "Basics",
        query: {
          type: 4,
        },
      });
    },

    toBasicsMap() {
      this.$router.push({
        name: "Basics",
        query: {
          type: 5,
        },
      });
    },

    toBasicsWarehouse() {
      this.$router.push({
        name: "Basics",
        query: {
          type: 6,
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
.basics-configuration {
  .basics-box {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    grid-gap: 20px;
  }
  .basics-configuration-box:hover {
    background-color: #e7e7e7;
    cursor: pointer;
  }
  .basics-configuration-box {
    width: 100%;
    height: 240px;
    background: white;
    // margin-bottom: 20px;
    text-align: center;

    .basics-configuration-inbox {
      width: 90%;
      padding-top: 20px;
      margin: 0 auto;
      .basics-configuration-log {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin: 0 auto;
        text-align: center;
        .basics-configuration-img {
          margin-top: 8px;
          width: 23px;
        }
      }
      .basics-configuration-headline {
        font-size: 18px;
        font-weight: bold;
        color: #636d7c;
        margin-top: 20px;
      }
      .basics-configuration-title {
        height: 35px;
        margin-top: 15px;
        color: #999c9f;
      }
      // .basics-configuration-ckbutton {
      //   margin-top: 20px;
      //   border-radius: 5px !important;
      //   border: #59b287;
      //   background-color: #59b287;
      //   color: white;
      // }
      .basics-configuration-button {
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