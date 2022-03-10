<template>
  <!-- 计费管理 -->
  <div class="accounting-management">
    <!-- 运价设置 -->
    <div
      class="accounting-management-box"
      @click="toChargingFare"
      v-if="freightRateSettings"
    >
      <el-row>
        <el-col :span="4">
          <div
            class="accounting-management-log"
            style="background-color: #59b287"
          >
            <img
              src="../../assets/img/configuration-img/calculate.png"
              alt=""
              class="accounting-management-img"
            />
          </div>
        </el-col>
        <el-col :span="15">
          <div class="accounting-management-headline">
            {{ $t("freightRateSettings") }}
          </div>
          <div class="accounting-management-title">
            {{ $t("freightRateSettingsStatus") }}
          </div>
        </el-col>
        <el-col :span="5">
          <img
            src="../../assets/img/configuration-img/arrowhead.png"
            alt=""
            class="accounting-management-arrowhead"
          />
        </el-col>
      </el-row>
    </div>
    <!-- 增值服务 -->
    <div
      class="accounting-management-box"
      @click="toChargingAppreciation"
      v-if="valueaddedServices"
    >
      <el-row>
        <el-col :span="4">
          <div
            class="accounting-management-log"
            style="background-color: #f2a67c"
          >
            <img
              src="../../assets/img/configuration-img/appstore.png"
              alt=""
              class="accounting-management-img"
            />
          </div>
        </el-col>
        <el-col :span="15">
          <div class="accounting-management-headline">
            {{ $t("valueaddedServices") }}
          </div>
          <div class="accounting-management-title">
            {{ $t("valueaddedservicesStatus") }}
          </div>
        </el-col>
        <el-col :span="5">
          <img
            src="../../assets/img/configuration-img/arrowhead.png"
            alt=""
            class="accounting-management-arrowhead"
          />
        </el-col>
      </el-row>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      freightRateSettings: false, //运价设置
      valueaddedServices: false, //增值服务
    };
  },
  created() {
    this.getAuthTree();
  },
  methods: {
    toChargingFare() {
      this.$router.push({
        name: "Charging",
        query: {
          type: 1,
        },
      });
    },
    toChargingAppreciation() {
      this.$router.push({
        name: "Charging",
        query: {
          type: 2,
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
.accounting-management {
  .accounting-management-box:hover {
    background-color: #e7e7e7;
    cursor: pointer;
  }
  .accounting-management-box {
    width: 85%;
    height: 200px;
    background-color: white;
    margin: 0 auto;
    margin-bottom: 20px;

    .accounting-management-log {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      text-align: center;
      margin: 0 auto;
      margin-top: 70px;

      .accounting-management-img {
        margin-top: 10px;
        width: 30px;
      }
    }
    .accounting-management-headline {
      font-size: 18px;
      font-weight: bold;
      color: #636d7c;
      margin-top: 60px;
    }
    .accounting-management-title {
      color: #515458;
      margin-top: 30px;
    }
    .accounting-management-arrowhead {
      width: 50px;
      height: 50px;
      text-align: center;
      margin: 0 auto;
      margin-top: 70px;
    }
  }
}
</style>
