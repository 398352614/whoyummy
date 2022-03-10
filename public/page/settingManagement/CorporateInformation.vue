<template>
  <!-- 企业信息 -->
  <div class="corporate-information">
    <div
      class="corporate-information-box"
      @click="toInformation"
      v-if="enterpriseInfoConfiguration"
    >
      <el-row>
        <el-col :span="4">
          <div
            class="corporate-information-log"
            style="background-color: #59b287"
          >
            <img
              src="../../assets/img/configuration-img/folder.png"
              alt=""
              class="corporate-information-img"
            />
          </div>
        </el-col>
        <el-col :span="15">
          <div class="corporate-information-headline">
            {{ $t("enterpriseInfoConfiguration") }}
          </div>
          <div class="corporate-information-title">
            {{ $t("enterpriseInfoState") }}
          </div>
        </el-col>
        <el-col :span="5">
          <img
            src="../../assets/img/configuration-img/arrowhead.png"
            alt=""
            class="corporate-information-arrowhead"
          />
        </el-col>
      </el-row>
    </div>
    <!-- 自定义界面 -->
    <div
      class="corporate-information-box"
      @click="toCustomInterface"
      v-if="customInterface"
    >
      <el-row>
        <el-col :span="4">
          <div
            class="corporate-information-log"
            style="background-color: rgb(239, 142, 161)"
          >
            <img
              src="../../assets/img/configuration-img/appstore.png"
              alt=""
              class="corporate-information-img"
            />
          </div>
        </el-col>
        <el-col :span="15">
          <div class="corporate-information-headline">
            {{ $t("customInterface") }}
          </div>
          <div class="corporate-information-title">
            {{ $t("customInterfaceState") }}
          </div>
        </el-col>
        <el-col :span="5">
          <img
            src="../../assets/img/configuration-img/arrowhead.png"
            alt=""
            class="corporate-information-arrowhead"
          />
        </el-col>
      </el-row>
    </div>
    <!-- 修改密码 -->
    <div
      class="corporate-information-box"
      @click="toInformationPassword"
      v-if="PasswordModify"
    >
      <el-row>
        <el-col :span="4">
          <div
            class="corporate-information-log"
            style="background-color: #f2a67c"
          >
            <img
              src="../../assets/img/configuration-img/coded-lock.png"
              alt=""
              class="corporate-information-img"
            />
          </div>
        </el-col>
        <el-col :span="15">
          <div class="corporate-information-headline">
            {{ $t("PasswordModify") }}
          </div>
          <div class="corporate-information-title">
            {{ $t("passwordModifyState") }}
          </div>
        </el-col>
        <el-col :span="5">
          <img
            src="../../assets/img/configuration-img/arrowhead.png"
            alt=""
            class="corporate-information-arrowhead"
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
      enterpriseInfoConfiguration: false, //企业信息
      PasswordModify: false, //修改密码
      customInterface: false, //自定义界面
    };
  },
  created() {
    this.getAuthTree();
  },
  methods: {
    toInformation() {
      this.$router.push({
        name: "Information",
        params: {
          type: 1,
        },
      });
    },
    toCustomInterface() {
      this.$router.push({
        name: "Information",
        params: {
          type: 2,
        },
      });
    },
    toInformationPassword() {
      this.$router.push({
        name: "Information",
        params: {
          type: 3,
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
                    if (j.name == "公司信息配置") {
                      this.enterpriseInfoConfiguration = true;
                    } else if (j.name == "修改密码") {
                      this.PasswordModify = true;
                    } else if (j.name == "自定义界面") {
                      this.customInterface = true;
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
.corporate-information {
  .corporate-information-box:hover {
    background-color: #e7e7e7;
    cursor: pointer;
  }
  .corporate-information-box {
    width: 85%;
    height: 200px;
    background-color: white;
    margin: 0 auto;
    margin-bottom: 20px;

    .corporate-information-log {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      text-align: center;
      margin: 0 auto;
      margin-top: 70px;

      .corporate-information-img {
        margin-top: 10px;
        width: 30px;
      }
    }
    .corporate-information-headline {
      font-size: 18px;
      font-weight: bold;
      color: #636d7c;
      margin-top: 60px;
    }
    .corporate-information-title {
      color: #515458;
      margin-top: 30px;
    }
    .corporate-information-arrowhead {
      width: 50px;
      height: 50px;
      text-align: center;
      margin: 0 auto;
      margin-top: 70px;
    }
  }
}
</style>
