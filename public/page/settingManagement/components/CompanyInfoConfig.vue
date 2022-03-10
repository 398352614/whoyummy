<template>
  <!-- 企业信息配置 -->
  <div class="basic-config">
    <el-form
      :model="companyInfo"
      :rules="rules"
      ref="companyForm"
      label-position="right"
      label-width="160px"
      class="company-info"
    >
      <!-- 企业网址 -->
      <el-form-item :label="$t('enterpriseWebsite')" prop="web_site">
        <el-input
          v-model="companyInfo.web_site"
          :placeholder="$t('enterpriseWebsite')"
        ></el-input>
      </el-form-item>
      <!-- 系统名称 -->
      <el-form-item :label="$t('systemName')" prop="system_name">
        <el-input
          v-model="companyInfo.system_name"
          :placeholder="$t('systemName')"
        ></el-input>
      </el-form-item>
      <!-- 公司地址 -->
      <!-- <div v-if="template_id == 1">
        <el-row :gutter="20">
          <el-col :span="12">
            <el-form-item :label="$t('ZipCode')" prop="post_code">
              <el-input
                v-model="companyInfo.post_code"
                @change="getLocation()"
                :placeholder="$t('PleaseEnterZipCode')"
                style="width: 100%"
              ></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item :label="$t('DoorNo')" prop="house_number">
              <el-input
                v-model="companyInfo.house_number"
                @change="getLocation()"
                :placeholder="$t('PleaseEnterDoorNo')"
                style="width: 100%"
              ></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item :label="$t('City')" prop="city">
              <el-input
                v-model="companyInfo.city"
                :disabled="disabledBtnByCountry"
                @change="getLocation()"
                :placeholder="$t('PleaseEnterCity')"
                style="width: 100%"
              ></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item :label="$t('Street')" prop="street">
              <el-input
                v-model="companyInfo.street"
                :disabled="disabledBtnByCountry"
                @change="getLocation()"
                :placeholder="$t('PleaseEnterStreet')"
                style="width: 100%"
              ></el-input>
            </el-form-item>
          </el-col>
        </el-row>
      </div> -->
      <el-form-item :label="$t('CompanyAaddress') + ':'" prop="address">
        <el-input
          size="small"
          v-model="companyInfo.address"
          :placeholder="$t('PleaseEnterCompanyAddress')"
        >
          <el-button slot="append" @click="selectAddressMap" class="locale-btn">
            <i class="el-icon-map-location" />{{ $t("positioning") }}
          </el-button></el-input
        >
      </el-form-item>
      <!-- 公司名称-->
      <el-form-item :label="$t('CompanyName')" prop="name">
        <el-input
          v-model="companyInfo.name"
          :placeholder="$t('PleaseEnterCompanyName')"
        ></el-input>
      </el-form-item>
      <!-- 联系人 -->
      <el-form-item :label="$t('companyContacts')" prop="contacts">
        <el-input
          v-model="companyInfo.contacts"
          :placeholder="$t('PleaseEnterContactPerson')"
        ></el-input>
      </el-form-item>
      <!-- 电话 -->
      <el-form-item :label="$t('Tel')" prop="phone">
        <el-input
          v-model="companyInfo.phone"
          :placeholder="$t('PleaseEnterCompanyTel')"
        ></el-input>
      </el-form-item>
      <!-- 企业logo -->
      <!-- <el-form-item :label="$t('enterpriseLogo')" prop="logo_url">
        <el-upload
          :headers="Authorization"
          :on-success="handleLogoSuccess"
          :action="BaseApiImg"
          :on-remove="handlerClearLogo"
          :limit="1"
          name="image"
          :data="{
            dir: 'driver',
          }"
          class="avatar-uploader"
        >
          <img
            v-if="companyInfo.logo_url"
            :src="companyInfo.logo_url"
            class="avatar"
          />
          <i v-else class="avatar-uploader-icon">
            <img
              src="../../../assets/img/configuration-img/picture.png"
              :alt="$t('uploadImage')"
            />
          </i>
          <div slot="tip" class="el-upload-tip">
            {{ $t("uploadImageTitle") }}
          </div>
        </el-upload>
      </el-form-item> -->
      <!-- 登入界面logo -->
      <!-- <el-form-item :label="$t('loginInterface')" prop="login_logo_url">
        <el-upload
          class="avatar-uploader"
          :headers="Authorization"
          :action="BaseApiImg"
          :on-success="handleLoginSuccess"
          :on-remove="handlerClearLogin"
          name="image"
          :data="{
            dir: 'driver',
          }"
          :limit="1"
        >
          <img
            v-if="companyInfo.login_logo_url"
            :src="companyInfo.login_logo_url"
            class="avatar"
          />
          <i v-else class="avatar-uploader-icon">
            <img
              src="../../../assets/img/configuration-img/picture.png"
              :alt="$t('uploadImage')"
            />
          </i>
          <div slot="tip" class="el-upload-tip">
            {{ $t("uploadImageTitle") }}
          </div>
        </el-upload>
      </el-form-item> -->
    </el-form>
    <el-tooltip
      :content="$t('AdministratorActivate')"
      placement="top"
      :disabled="hasPerm('company-info.update') ? true : false"
      style="width: 440px"
    >
      <div class="save">
        <el-button
          :disabled="!hasPerm('company-info.update')"
          type="primary"
          @click="putCompanyDetails"
          :class="[
            hasPerm('company-info.update') ? 'submit' : 'submit-disabled',
          ]"
          :loading="this.$store.state.config.button_loading"
          >{{ $t("Submit") }}</el-button
        >
      </div>
    </el-tooltip>
    <!-- 地图弹出框 -->
    <el-dialog
      :title="$t('findAddress')"
      :close-on-click-modal="false"
      :visible.sync="select_map"
      width="90%"
    >
      <b-map
        class="select-map dialog-wrapper"
        v-if="mapType === 'baidu'"
        @addressSelected="handlerAddressSelected"
      ></b-map>
      <google-map
        class="select-map dialog-wrapper"
        v-else
        @addressSelected="handlerAddressSelected"
      ></google-map>
    </el-dialog>
  </div>
</template>

<script>
import baseApi from "@/api/axios/base_api";
import googleMap from "@/components/Map/GoogleMap";
import InputSearch from "@/components/Map/InputSearchMap";
import BMap from "@/components/BaiduMap/getAddressByMap";
import InputSearchBaidu from "@/components/BaiduMap/InputSearchMap";

export default {
  name: "CompanyInfoConfig",
  components: {
    googleMap,
    InputSearch,
    BMap,
    InputSearchBaidu,
  },
  data() {
    return {
      template_id: localStorage.getItem("address_template_id") || 1,
      country:
        localStorage.getItem("country") &&
        JSON.parse(localStorage.getItem("country")).en,
      companyInfo: {
        // 公司信息
        web_site: "", //企业网址
        system_name: "", //系统名称
        name: "", //公司名称
        contacts: "", //联系人
        phone: "", //电话
        address: "", //公司地址
        logo_url: "", //企业logo
        login_logo_url: "", //登入界面logo
        post_code: "", //邮编
        house_number: "", //门牌号
        city: "", //城市
        street: "", //街道
        location: {
          lat: null,
          lng: null,
        },
      },
      select_map: false, //地图

      // 表单验证
      rules: {
        name: [
          {
            required: true,
            message: this.$t("PleaseEnterCompanyName"),
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
    };
  },
  computed: {
    disabledBtnByCountry() {
      return this.country === "NL";
    },
    Authorization() {
      return { Authorization: this.$store.state.token.token };
    },
    BaseApiImg() {
      return `${baseApi}admin/upload/image`;
    },
    BaseApiFile() {
      return `${baseApi}admin/upload/file`;
    },
    mapType() {
      return this.$store.state.config.maptype;
    },
  },
  created() {
    this.getCompanyDetails();
  },
  methods: {
    getLocation() {
      // console.log(this.country, "this.country");
      if (
        this.companyInfo.post_code &&
        this.companyInfo.house_number &&
        (this.country === "NL" ||
          (this.companyInfo.city && this.companyInfo.street))
      ) {
        let isNL = false;
        if (this.country === "NL") {
          this.companyInfo.city = "";
          this.companyInfo.street = "";
          isNL = true;
        }
        this.$api
          .getLocation({
            post_code: this.companyInfo.post_code,
            house_number: this.companyInfo.house_number,
          })
          .then((res) => {
            if (!res) return;
            this.companyInfo.lat = res.data.lat;
            this.companyInfo.lon = res.data.lon;
            if (isNL) {
              this.companyInfo.city = res.data.city;
              this.companyInfo.street = res.data.street;

              console.log("this.companyInfo.street ");
              console.log(this.companyInfo.street);
            }
          });
      } else if (
        this.companyInfo.post_code &&
        this.companyInfo.house_number &&
        this.companyInfo.city &&
        this.companyInfo.street
      ) {
        this.$api
          .getLocation({
            post_code: this.companyInfo.post_code,
            house_number: this.companyInfo.house_number,
            city: this.companyInfo.city,
            street: this.companyInfo.street,
          })
          .then((res) => {
            if (!res) return;
            this.companyInfo.lat = res.data.lat;
            this.companyInfo.lon = res.data.lon;
          });
      }
    },
    // 公司详情
    getCompanyDetails() {
      this.$api.getCompanyDetails().then((res) => {
        if (!res.data) {
          this.companyInfo = {};
        } else {
          this.companyInfo = res.data.data;
        }
      });
    },

    // 修改公司信息
    putCompanyDetails() {
      this.$refs.companyForm.validate((valid) => {
        if (!valid) return;
        this.$api.putCompanyDetails(this.companyInfo);
      });
    },
    selectAddressMap() {
      this.select_map = true;
    },
    handlerAddressSelected(data) {
      this.companyInfo.address = data.address;
      this.companyInfo.location = data.location;
      this.select_map = false;
    },
    // 获取图片地址
    handleLogoSuccess(response) {
      if (response.code === 200) {
        this.companyInfo.logo_url = response.data.path;
      }
    },
    handleLoginSuccess(response) {
      if (response.code === 200) {
        this.companyInfo.login_logo_url = response.data.path;
      }
    },
    // 清除图片
    handlerClearLogo() {
      this.companyInfo.logo_url = "";
    },
    handlerClearLogin() {
      this.companyInfo.login_logo_url = "";
    },
  },
};
</script>
<style lang="less" scoped>
.basic-config {
  // background: #ffffff;
  // border: 1px solid rgba(228, 235, 241, 1);
  // padding: 20px 25px 10px;
  .company-info {
    margin-top: 47px;
    .locale-btn {
      color: #f6a50d;
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
  /deep/ .avatar-uploader .el-upload {
    border: 1px solid #d9d9d9;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    width: 300px;
  }
  /deep/ .avatar-uploader .el-upload:hover {
    border-color: #409eff;
  }
  /deep/ .el-upload-list {
    width: 300px;
  }
  .el-upload-tip {
    width: 210px;
    color: #c2c0c0;
  }
  .avatar {
    width: 300px;
  }
}
</style>
<style lang="less" >
.basic-config {
  .company-info {
    .el-input {
      max-width: 628px;
    }
    .el-select {
      width: 100%;
      max-width: 628px;
    }
    .el-input__inner {
      border-radius: 0;
    }
  }
}
</style>
