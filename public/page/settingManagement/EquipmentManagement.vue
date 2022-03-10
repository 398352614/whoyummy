<template>
  <!-- 设备管理 -->
  <div class="equipment-management">
    <div class="equipment-management-box" @click="toEquipmentManagement">
      <el-row>
        <el-col :span="4">
          <div
            class="equipment-management-log"
            style="background-color: #59b287"
          >
            <img
              src="../../assets/img/configuration-img/database.png"
              alt=""
              class="equipment-management-img"
            />
          </div>
        </el-col>
        <el-col :span="15">
          <div class="equipment-management-headline">
            {{ $t("deviceManagement") }}
          </div>
          <div class="equipment-management-title">
            {{ $t("deviceManagementState") }}
          </div>
        </el-col>
        <el-col :span="5">
          <img
            src="../../assets/img/configuration-img/arrowhead.png"
            alt=""
            class="equipment-management-arrowhead"
          />
        </el-col>
      </el-row>
    </div>
  </div>
</template>

<script>
export default {
  methods: {
    toEquipmentManagement() {
      this.$router.push({
        name: "DeviceManagement",
      });
    },
  },
};
</script>

<style lang="less" scoped>
.equipment-management {
  .equipment-management-box:hover {
    background-color: #e7e7e7;
    cursor: pointer;
  }
  .equipment-management-box {
    width: 85%;
    height: 200px;
    background-color: white;
    margin: 0 auto;
    margin-bottom: 20px;

    .equipment-management-log {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      text-align: center;
      margin: 0 auto;
      margin-top: 70px;

      .equipment-management-img {
        margin-top: 10px;
        width: 30px;
      }
    }
    .equipment-management-headline {
      font-size: 18px;
      font-weight: bold;
      color: #636d7c;
      margin-top: 60px;
    }
    .equipment-management-title {
      color: #515458;
      margin-top: 30px;
    }
    .equipment-management-arrowhead {
      width: 50px;
      height: 50px;
      text-align: center;
      margin: 0 auto;
      margin-top: 70px;
    }
  }
}
</style>
