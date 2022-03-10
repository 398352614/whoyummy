<template>
  <!-- 假期配置 -->
  <div class="holiday-configuration">
    <div class="holiday-configuration-box" @click="toRestrictedItemManagement">
      <el-row>
        <el-col :span="4">
          <div
            class="holiday-configuration-log"
            style="background-color: #59b287"
          >
            <img
              src="../../assets/img/configuration-img/calendar.png"
              alt=""
              class="holiday-configuration-img"
            />
          </div>
        </el-col>
        <el-col :span="15">
          <div class="holiday-configuration-headline">{{ $t("holiday") }}</div>
          <div class="holiday-configuration-title">
            {{ $t("holidayState") }}
          </div>
        </el-col>
        <el-col :span="5">
          <img
            src="../../assets/img/configuration-img/arrowhead.png"
            alt=""
            class="holiday-configuration-arrowhead"
          />
        </el-col>
      </el-row>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {};
  },
  methods: {
    toRestrictedItemManagement() {
      this.$router.push({
        name: "RestrictedItemManagement",
      });
    },
  },
};
</script>

<style lang="less" scoped>
.holiday-configuration {
  .holiday-configuration-box:hover {
    background-color: #e7e7e7;
    cursor: pointer;
  }
  .holiday-configuration-box {
    width: 85%;
    height: 200px;
    background-color: white;
    margin: 0 auto;
    margin-bottom: 20px;

    .holiday-configuration-log {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      text-align: center;
      margin: 0 auto;
      margin-top: 70px;

      .holiday-configuration-img {
        margin-top: 10px;
        width: 30px;
      }
    }
    .holiday-configuration-headline {
      font-size: 18px;
      font-weight: bold;
      color: #636d7c;
      margin-top: 60px;
    }
    .holiday-configuration-title {
      color: #515458;
      margin-top: 30px;
    }
    .holiday-configuration-arrowhead {
      width: 50px;
      height: 50px;
      text-align: center;
      margin: 0 auto;
      margin-top: 70px;
    }
  }
}
</style>
