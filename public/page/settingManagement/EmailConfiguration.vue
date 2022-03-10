<template>
  <!-- 邮件配置 -->
  <div class="email-configuration">
    <div class="email-configuration-box" @click="toPostcode">
      <el-row>
        <el-col :span="4">
          <div
            class="email-configuration-log"
            style="background-color: #59b287"
          >
            <img
              src="../../assets/img/configuration-img/mailbox.png"
              alt=""
              class="email-configuration-img"
            />
          </div>
        </el-col>
        <el-col :span="15">
          <div class="email-configuration-headline">
            {{ $t("EmailConfiguration") }}
          </div>
          <div class="email-configuration-title">
            {{ $t("EmailState") }}
          </div>
        </el-col>
        <el-col :span="5">
          <img
            src="../../assets/img/configuration-img/arrowhead.png"
            alt=""
            class="email-configuration-arrowhead"
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
    toPostcode() {
      this.$router.push({
        name: "Postcode",
      });
    },
  },
};
</script>

<style lang="less" scoped>
.email-configuration {
  .email-configuration-box:hover {
    background-color: #e7e7e7;
    cursor: pointer;
  }
  .email-configuration-box {
    width: 85%;
    height: 200px;
    background-color: white;
    margin: 0 auto;
    margin-bottom: 20px;

    .email-configuration-log {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      text-align: center;
      margin: 0 auto;
      margin-top: 70px;

      .email-configuration-img {
        margin-top: 10px;
        width: 30px;
      }
    }
    .email-configuration-headline {
      font-size: 18px;
      font-weight: bold;
      color: #636d7c;
      margin-top: 60px;
    }
    .email-configuration-title {
      color: #515458;
      margin-top: 30px;
    }
    .email-configuration-arrowhead {
      width: 50px;
      height: 50px;
      text-align: center;
      margin: 0 auto;
      margin-top: 70px;
    }
  }
}
</style>
