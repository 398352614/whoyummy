<template>
  <!-- 顺带包裹 -->
  <div class="passing-parcel">
    <div class="passing-parcel-box" @click="toPassingTheParcel">
      <el-row>
        <el-col :span="4">
          <div class="passing-parcel-log" style="background-color: #59b287">
            <img
              src="../../assets/img/configuration-img/full.png"
              alt=""
              class="passing-parcel-img"
            />
          </div>
        </el-col>
        <el-col :span="15">
          <div class="passing-parcel-headline">{{ $t("incidentalRule") }}</div>
          <div class="passing-parcel-title">
            {{ $t("incidentalRuleState") }}
          </div>
        </el-col>
        <el-col :span="5">
          <img
            src="../../assets/img/configuration-img/arrowhead.png"
            alt=""
            class="passing-parcel-arrowhead"
          />
        </el-col>
      </el-row>
    </div>
  </div>
</template>

<script>
export default {
  methods: {
    toPassingTheParcel() {
      this.$router.push({
        name: "Incidentally",
      });
    },
  },
};
</script>

<style lang="less" scoped>
.passing-parcel {
  .passing-parcel-box:hover {
    background-color: #e7e7e7;
    cursor: pointer;
  }
  .passing-parcel-box {
    width: 85%;
    height: 200px;
    background-color: white;
    margin: 0 auto;
    margin-bottom: 20px;

    .passing-parcel-log {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      text-align: center;
      margin: 0 auto;
      margin-top: 70px;

      .passing-parcel-img {
        margin-top: 10px;
        width: 30px;
      }
    }
    .passing-parcel-headline {
      font-size: 18px;
      font-weight: bold;
      color: #636d7c;
      margin-top: 60px;
    }
    .passing-parcel-title {
      color: #515458;
      margin-top: 30px;
    }
    .passing-parcel-arrowhead {
      width: 50px;
      height: 50px;
      text-align: center;
      margin: 0 auto;
      margin-top: 70px;
    }
  }
}
</style>
