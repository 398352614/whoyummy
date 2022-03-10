<template>
  <!-- 入库异常配置 -->
  <div class="storage-configuration">
    <div class="storage-configuration-box" @click="toWarehousingException">
      <el-row>
        <el-col :span="4">
          <div
            class="storage-configuration-log"
            style="background-color: #59b287"
          >
            <img
              src="../../assets/img/configuration-img/alert.png"
              alt=""
              class="storage-configuration-img"
            />
          </div>
        </el-col>
        <el-col :span="15">
          <div class="storage-configuration-headline">
            {{ $t("storageException") }}
          </div>
          <div class="storage-configuration-title">
            {{ $t("storageExceptionState") }}
          </div>
        </el-col>
        <el-col :span="5">
          <img
            src="../../assets/img/configuration-img/arrowhead.png"
            alt=""
            class="storage-configuration-arrowhead"
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
    toWarehousingException() {
      this.$router.push({
        name: "SpecialSceneConfiguration",
      });
    },
  },
};
</script>

<style lang="less" scoped>
.storage-configuration {
  .storage-configuration-box:hover {
    background-color: #e7e7e7;
    cursor: pointer;
  }
  .storage-configuration-box {
    width: 85%;
    height: 200px;
    background-color: white;
    margin: 0 auto;
    margin-bottom: 20px;

    .storage-configuration-log {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      text-align: center;
      margin: 0 auto;
      margin-top: 70px;

      .storage-configuration-img {
        margin-top: 10px;
        width: 30px;
      }
    }
    .storage-configuration-headline {
      font-size: 18px;
      font-weight: bold;
      color: #636d7c;
      margin-top: 60px;
    }
    .storage-configuration-title {
      color: #515458;
      margin-top: 30px;
    }
    .storage-configuration-arrowhead {
      width: 50px;
      height: 50px;
      text-align: center;
      margin: 0 auto;
      margin-top: 70px;
    }
  }
}
</style>
