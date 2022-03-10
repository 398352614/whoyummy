<template>
  <!-- 地图引擎 -->
  <div class="engine-config">
    <el-form
      :model="engine"
      :rules="engineRules"
      ref="engineForm"
      label-position="right"
      label-width="110px"
    >
      <!-- 国家 -->
      <el-form-item :label="$t('Country') + ':'" class="engine-form">
        <el-tag
          :key="tag.id"
          v-for="tag in address"
          closable
          :disable-transitions="false"
          @close="delCountry(tag)"
          class="country-tag"
        >
          {{ tag.name }}
        </el-tag>
        <!-- 判断账号是test-HK@nle-tech.com就不限制添加国家数量 否则最多三个 -->
        <el-button
          class="add-country"
          v-if="
            user_name === 'test-HK@nle-tech.com'
              ? address.length < 10000000
              : address.length < 3
          "
          size="small"
          @click="openCountryDialog"
          ><i class="el-icon-plus"></i>{{ $t("AddCountry") }}</el-button
        >
      </el-form-item>
      <!-- 地图类型 -->
      <div class="show-template-box">
        <!-- 模板一 -->
        <div class="template-title">{{ $t("mapType") }} :</div>
        <div
          :class="{
            'address-template': true,
            'address-template-check': engine.back_type === 1,
          }"
        >
          <div class="engine-img">
            <img
              src="../../../assets/img/configuration-img/map-google.png"
              alt=""
            />
            <div class="img-title">{{ $t("google") }}</div>
          </div>

          <el-form-item :label="$t('developer')" prop="google_key">
            <el-input v-model="engine.google_key"></el-input>
          </el-form-item>
          <el-form-item :label="$t('developerKEY')" prop="google_secret">
            <el-input v-model="engine.google_secret"></el-input>
          </el-form-item>
          <div class="address-template-radio">
            <el-radio v-model="engine.back_type" :label="1"
              >{{ $t("selectThisGoogle") }}
              <el-tooltip
                effect="dark"
                :content="$t('chooseThisBaiduState')"
                placement="top"
              >
                <span class="el-icon-warning icon-question"></span> </el-tooltip
            ></el-radio>
          </div>
        </div>

        <!-- 模板二 -->
        <div
          :class="{
            'address-template': true,
            'address-template-check': engine.back_type === 2,
          }"
        >
          <div class="engine-img">
            <img
              src="../../../assets/img/configuration-img/map-baidu.png"
              alt=""
            />

            <div class="img-title">{{ $t("baidu") }}</div>
          </div>

          <el-form-item :label="$t('developer')" prop="baidu_key">
            <el-input v-model="engine.baidu_key"></el-input>
          </el-form-item>
          <el-form-item :label="$t('developerKEY')" prop="baidu_secret">
            <el-input v-model="engine.baidu_secret"></el-input>
          </el-form-item>
          <div class="address-template-radio">
            <el-radio v-model="engine.back_type" :label="2"
              >{{ $t("chooseThisBaidu")
              }}<el-tooltip
                effect="dark"
                :content="$t('chooseThisBaiduState')"
                placement="top"
                class="dark"
              >
                <span class="el-icon-warning icon-question"></span> </el-tooltip
            ></el-radio>
          </div>
        </div>
      </div>
    </el-form>
    <!-- 提交 -->
    <el-tooltip
      :content="$t('AdministratorActivate')"
      placement="top"
      :disabled="hasPerm('map.update') ? true : false"
      style="width: 440px"
    >
      <div class="save save-engine">
        <el-button
          :disabled="!hasPerm('map.update')"
          type="primary"
          @click="putEngine"
          :class="[hasPerm('map.update') ? 'submit' : 'submit-disabled']"
          :loading="this.$store.state.config.button_loading"
          >{{ $t("Submit") }}</el-button
        >
      </div>
    </el-tooltip>

    <!-- 添加国家弹窗 -->
    <el-dialog
      :title="$t('AddCountry')"
      :visible.sync="show_country"
      class="dialog-country"
      @close="close_dialog"
    >
      <el-form
        :model="addCountryInfo"
        :rules="rules"
        ref="countryList"
        label-position="right"
        label-width="100px"
      >
        <el-form-item :label="$t('Country')" prop="short">
          <el-select
            v-model="addCountryInfo.short"
            clearable
            filterable
            :placeholder="$t('PleaseSelectACountry')"
            :filter-method="dataFilter"
          >
            <el-option
              v-for="(item, index) in allCountry"
              :key="index"
              :label="item.name"
              :value="item.short"
            ></el-option>
          </el-select>
        </el-form-item>
      </el-form>
      <span slot="footer">
        <el-button @click="show_country = false" class="cancel">{{
          $t("Cancel")
        }}</el-button>
        <el-button type="primary" @click="addCountry" class="submit">{{
          $t("Confirm")
        }}</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
export default {
  name: "Engine",
  data() {
    return {
      tab: "1",
      user_name: localStorage.getItem("user_name") || "",
      engine: {
        // 地图引擎信息
        back_type: null,
        google_key: "",
        google_secret: "",
        baidu_key: "",
        baidu_secret: "",
      },
      // allAddress: [],
      address: [],
      allCountry: [],
      show_country: false,
      addCountryInfo: {
        short: "",
      },

      // 选择模板
      check_template: false,
      template: {
        // back_type: null,
        // google_key: "",
        // google_secret: "",
        // baidu_key: "",
        // baidu_secret: "",
      },
      options: [],
      rules: {
        name: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        contacts: [
          {
            required: true,
            message: this.$t("PleaseEnterContactPerson"),
            trigger: "blur",
          },
        ],
        phone: [
          {
            required: true,
            message: this.$t("PleaseEnterCompanyTel"),
            trigger: "blur",
          },
        ],
        country: [
          {
            required: true,
            message: this.$t("PleaseSelectCountry"),
            trigger: "blur",
          },
        ],
        address: [
          {
            required: true,
            message: this.$t("PleaseEnterCompanyAddress"),
            trigger: "blur",
          },
        ],
        short: [
          {
            required: true,
            message: this.$t("PleaseSelectACountry"),
            trigger: "blur",
          },
        ],
      },
      engineRules: {
        line_rule: [
          {
            required: true,
            message: this.$t("PleaseSelectRouteAssignRules"),
            trigger: "change",
          },
        ],
        show_type: [
          { required: true, message: this.$t("Mandatory"), trigger: "change" },
        ],
        weight_unit: [
          {
            required: true,
            message: this.$t("PleaseSelectAWeightUnit"),
            trigger: "blur",
          },
        ],
        currency_unit: [
          {
            required: true,
            message: this.$t("PleaseSelectACurrencyUnit"),
            trigger: "blur",
          },
        ],
        volume_unit: [
          {
            required: true,
            message: this.$t("PleaseSelectAVolumeUnit"),
            trigger: "blur",
          },
        ],
        map: [
          {
            required: true,
            message: this.$t("PleaseSelectAMap"),
            trigger: "blur",
          },
        ],
      },
    };
  },
  created() {
    this.getAddedCountry();
    this.getEngine();
  },
  computed: {
    // line_rule() {
    //   return [
    //     {
    //       value: 1,
    //       label: this.$t("distributedByPostCode"),
    //     },
    //     {
    //       value: 2,
    //       label: this.$t("distributedByFeild"),
    //     },
    //   ];
    // },
    // mapList() {
    //   return [
    //     {
    //       value: "google",
    //       label: this.$t("google"),
    //     },
    //     {
    //       value: "baidu",
    //       label: this.$t("baidu"),
    //     },
    //   ];
    // },
  },
  methods: {
    // 下拉搜索 :filter-method="dataFilter"
    dataFilter(val) {
      this.addCountryInfo.short = val;
      if (val) {
        //val存在
        this.allCountry = this.options.filter((item) => {
          if (
            !!~item.pinyin.indexOf(val) ||
            !!~item.name.indexOf(val) ||
            !!~item.short.indexOf(val) ||
            !!~item.pinyin.toUpperCase().indexOf(val.toUpperCase())
          ) {
            return true;
          }
        });
      } else {
        //val为空时，还原数组
        this.allCountry = this.options;
      }
    },
    // 拉取高级配置
    getEngine() {
      this.$api.getEngine().then((res) => {
        if (!res.data) {
          this.engine.back_type = null;
          return;
        }
        this.engine = res.data;
      });
    },

    // 国家列表
    getCountries() {
      this.$api.getCountries().then((res) => {
        this.allCountry = res.data.country_list;
        this.options = res.data.country_list;
      });
    },
    getAddedCountry() {
      // 国家列表
      this.$api.getAddedCountry().then((res) => {
        this.address = res.data;
        // 国家
        let countryObj = { cn: res.data[0].name, en: res.data[0].short };
        localStorage.setItem("country", JSON.stringify(countryObj));
      });
    },
    // 修改地图引擎
    putEngine() {
      this.$refs.engineForm.validate((valid) => {
        if (!valid) return;
        this.$api.putEngine(this.engine).then((res) => {
          if (!res) return;
          // 设置地图
          this.$store.commit("globalData/setMaptype", this.engine.back_type);
          this.getEngine();
        });
      });
    },

    // 添加国家函数
    openCountryDialog() {
      this.show_country = true;
      this.getCountries();
    },
    close_dialog() {
      this.addCountryInfo = {
        short: "",
      };
    },
    addCountry() {
      this.$refs.countryList.validate((valid) => {
        if (!valid) return;
        this.$api
          .addCountry({
            short: this.addCountryInfo.short,
          })
          .then(() => {
            this.getAddedCountry();
            this.show_country = false;
          });
      });
    },
    delCountry(tag) {
      this.$api.delCountry(tag.id).then((res) => {
        if (!res.code) return;
        this.getAddedCountry();
      });
    },
  },
};
</script>
<style lang="less" scoped>
.engine-config {
  padding: 20px 25px 10px;
  .country-tag {
    margin-left: 10px;
  }
  .engine-form {
    margin-bottom: 50px;
    margin-left: 60px;
  }

  .engine-img {
    position: relative;
    margin-left: 30%;
    margin-bottom: 20px;
    .img-title {
      position: absolute;
      top: 35%;
      left: 20%;
      color: #606266;
    }
  }
  .save {
    .el-button {
      margin-left: 160px;
      border-radius: 0;
      padding-left: 44px;
      padding-right: 44px;
    }
  }
  .el-icon-warning {
    color: #efb336;
    margin-left: 10px;
  }
}
</style>
<style lang="less">
.engine-config {
  .add-country {
    color: #28d094;
    border: 1px solid #28d094;
    margin-left: 5px;
  }
  .dialog-country {
    overflow: hidden;
    .el-button {
      border-radius: 0;
    }
    .el-input__inner {
      border-radius: 0;
    }
  }
  .el-dialog {
    border-radius: 0;
  }
  .show-template-box {
    display: flex;
    margin-bottom: 50px;
    .template-title {
      position: absolute;
      left: 115px;
      color: #606266;
    }
    .address-template {
      width: 400px;
      border: 1px solid #ebeef5;
      box-shadow: 0 0 3px 1px #f2f6fc;
      box-sizing: border-box;
      padding: 20px;
      margin-left: 160px;
    }
    .address-template-radio {
      display: flex;
      justify-content: center;
    }
    .address-template-check {
      border-color: #c4c4c4;
    }
  }
}
</style>
