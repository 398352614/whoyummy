<template>
  <div class="sen_main">
    <el-form
      class="sen_form"
      :model="senderInfo"
      :rules="rules"
      ref="senderForm"
      label-position="right"
      label-width="100px"
    >
      <el-form-item :label="$t('类型') + ':'" prop="type">
        <el-select v-model="senderInfo.type" :placeholder="$t('请选择类型')">
          <el-option
            v-for="item in addressTypeList"
            :key="item.id"
            :label="item.name"
            :value="item.id"
          ></el-option>
        </el-select>
      </el-form-item>
      <el-form-item :label="$t('Name') + ':'" prop="second_place_fullname">
        <el-input
          v-model="senderInfo.second_place_fullname"
          :placeholder="$t('pleaseEnterName')"
        ></el-input>
      </el-form-item>
      <el-form-item :label="$t('Tel') + ':'" prop="second_place_phone">
        <el-input
          v-model="senderInfo.second_place_phone"
          :placeholder="$t('PleaseEnterYel')"
        ></el-input>
      </el-form-item>
      <!-- <el-form-item :label="$t('Country') + ':'" prop="second_place_country">
        <el-select v-model="senderInfo.second_place_country" :placeholder="$t('pleaseSelectCountry')">
          <el-option
            v-for="(item, index) in address"
            :key="index"
            :label="item.name"
            :value="item.short"
          ></el-option>
        </el-select>
      </el-form-item> -->
      <div v-if="template_id == 1">
        <el-row>
          <el-col :span="12">
            <el-form-item
              :label="$t('ZipCode') + ':'"
              prop="second_place_post_code"
            >
              <el-input
                v-model="senderInfo.second_place_post_code"
                @change="getLocation()"
                :placeholder="$t('PleaseEnterZipCode')"
              ></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item
              :label="$t('DoorNo') + ':'"
              prop="second_place_house_number"
            >
              <el-input
                v-model="senderInfo.second_place_house_number"
                @change="getLocation()"
                :placeholder="$t('PleaseEnterDoorNo')"
              ></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item :label="$t('City') + ':'" prop="second_place_city">
          <el-input
            v-model="senderInfo.second_place_city"
            :disabled="disabledBtnByCountry"
            @change="getLocation()"
            :placeholder="$t('PleaseSelectCity')"
          ></el-input>
        </el-form-item>
        <el-form-item :label="$t('Street') + ':'" prop="second_place_street">
          <el-input
            v-model="senderInfo.second_place_street"
            :disabled="disabledBtnByCountry"
            @change="getLocation()"
            :placeholder="$t('PleaseEnterStreet')"
          ></el-input>
        </el-form-item>
        <!-- <el-form-item :label="$t('Address') + ':'" prop="second_place_address">
          <el-input v-model="senderInfo.second_place_address" :placeholder="$t('PleaseEnterAddress')"></el-input>
        </el-form-item> -->
      </div>
      <el-form-item
        v-else
        :label="$t('Address') + ':'"
        prop="second_place_address"
      >
        <input-search vi-show="false" @result="getResult"></input-search>
        <el-input
          id="pac_input_box"
          v-model="senderInfo.second_place_address"
          :placeholder="$t('PleaseEnterAddress')"
        >
          <el-button
            slot="append"
            @click="select_map = true"
            class="locale-btn"
          >
            <i class="el-icon-map-location" />定位
          </el-button>
        </el-input>
      </el-form-item>

      <!-- <el-form-item :label="$t('Address') + ':'" prop="second_place_address">
        <el-input v-model="senderInfo.second_place_address" :placeholder="$t('PleaseEnterAddress')"></el-input>
      </el-form-item> -->
      <el-form-item :label="$t('Merchant') + ':'" prop="merchant_id">
        <el-select
          v-model="senderInfo.merchant_id"
          filterable
          :placeholder="$t('PleaseSelectMerchant')"
        >
          <el-option
            v-for="(item, index) in merchantsList"
            :key="index"
            :label="item.name"
            :value="item.id"
          ></el-option>
        </el-select>
      </el-form-item>
      <div class="sen_btn">
        <el-button
          type="primary"
          @click="onSave"
          :loading="this.$store.state.config.button_loading"
          >{{ $t("saveAndContinue") }}</el-button
        >
        <el-button
          type="primary"
          @click="onSubmit"
          :loading="this.$store.state.config.button_loading"
          >{{ $t("Submit") }}</el-button
        >
      </div>
    </el-form>

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
import googleMap from "@/components/Map/GoogleMap";
import InputSearch from "@/components/Map/InputSearchMap";
import BMap from "@/components/BaiduMap/getAddressByMap";

export default {
  name: "AddReceiver",
  components: {
    googleMap,
    InputSearch,
    BMap,
  },
  data() {
    return {
      addressTypeList: [],
      senderInfo: {
        type: "",
        second_place_fullname: "",
        second_place_phone: "",
        second_place_country: "",
        second_place_post_code: "",
        second_place_house_number: "",
        second_place_city: "",
        second_place_street: "",
        second_place_address: "",
        second_place_lon: "",
        second_place_lat: "",
        merchant_id: null,
      },
      isEdit: false,
      address: [],
      merchantsList: [],

      // 地图
      select_map: false,
      template_id: localStorage.getItem("address_template_id") || 1,
      country:
        localStorage.getItem("country") &&
        JSON.parse(localStorage.getItem("country")).en,

      rules: {
        merchant_id: [
          { required: true, message: this.$t("Required"), trigger: "change" },
        ],
        second_place_fullname: [
          {
            required: true,
            message: this.$t("pleaseEnterName"),
            trigger: "blur",
          },
        ],
        second_place_phone: [
          {
            required: true,
            message: this.$t("PleaseEnterYel"),
            trigger: "blur",
          },
        ],
        second_place_country: [
          {
            required: true,
            message: this.$t("pleaseSelectCountry"),
            trigger: "change",
          },
        ],
        second_place_post_code: [
          {
            required: true,
            message: this.$t("PleaseEnterZipCode"),
            trigger: "blur",
          },
        ],
        second_place_house_number: [
          {
            required: true,
            message: this.$t("PleaseEnterDoorNo"),
            trigger: "blur",
          },
        ],
        second_place_city: [
          {
            required: true,
            message: this.$t("PleaseSelectCity"),
            trigger: "blur",
          },
        ],
        second_place_street: [
          {
            required: true,
            message: this.$t("PleaseEnterStreet"),
            trigger: "blur",
          },
        ],
        second_place_address: [
          {
            required: true,
            message: this.$t("PleaseEnterAddress"),
            trigger: "blur",
          },
        ],
      },
    };
  },
  created() {
    this.getCountryList();
    this.getMerchants();
    this.getDictionary();
    if (this.$route.query.id) {
      this.isEdit = true;
      this.getDetail(this.$route.query.id);
    }
  },
  computed: {
    disabledBtnByCountry() {
      return this.country === "NL";
    },
    mapType() {
      return this.$store.state.globalData.maptype;
    },
  },
  methods: {
    getDictionary() {
      this.$api.getDictionary().then((res) => {
        this.addressTypeList = res.data.addressTypeList;
      });
    },
    getResult(data) {
      console.log(data, "获取到的地址");
      // 填充收货人
      this.senderInfo.second_place_city = data.city;
      this.senderInfo.second_place_street = `${data.route}`;
      this.senderInfo.second_place_house_number = data.street_number;
      this.senderInfo.second_place_country = data.country;
      this.senderInfo.second_place_post_code = data.postal_code;
      this.senderInfo.second_place_address = data.address;
      this.senderInfo.second_place_lat = data.location.lat;
      this.senderInfo.second_place_lon = data.location.lng;
    },
    getDetail(id) {
      this.$api.getSenderAdressDetails(id).then((res) => {
        this.senderInfo = res.data;
      });
    },
    getLocation() {
      console.log(this.country, "this.country");
      if (
        this.senderInfo.second_place_post_code &&
        this.senderInfo.second_place_house_number &&
        (this.country === "NL" ||
          (this.senderInfo.second_place_city &&
            this.senderInfo.second_place_street))
      ) {
        let isNL = false;
        if (this.country === "NL") {
          this.senderInfo.second_place_city = "";
          this.senderInfo.second_place_street = "";
          isNL = true;
        }
        this.$api
          .getLocation({
            post_code: this.senderInfo.second_place_post_code,
            house_number: this.senderInfo.second_place_house_number,
          })
          .then((res) => {
            if (!res) return;
            this.senderInfo.second_place_lat = res.data.lat;
            this.senderInfo.second_place_lon = res.data.lon;
            if (isNL) {
              this.senderInfo.second_place_city = res.data.city;
              this.senderInfo.second_place_street = res.data.street;
            }
          });
      } else if (
        this.senderInfo.post_code &&
        this.senderInfo.house_number &&
        this.senderInfo.city &&
        this.senderInfo.street
      ) {
        this.$api
          .getLocation({
            post_code: this.senderInfo.post_code,
            house_number: this.senderInfo.house_number,
            city: this.senderInfo.city,
            street: this.senderInfo.street,
          })
          .then((res) => {
            if (!res) return;
            this.senderInfo.lat = res.data.lat;
            this.senderInfo.lon = res.data.lon;
          });
      }
    },
    onSave() {
      this.$refs.senderForm.validate((valid) => {
        if (!valid) return;
        if (this.isEdit) {
          this.$api.putSenderAdress(this.senderInfo).then((res) => {
            if (!res) return;
            this.$store.commit("config/refreshRouter", Math.random());
          });
        } else {
          this.$api.addSenderAdress(this.senderInfo).then((res) => {
            if (!res) return;
            this.$store.commit("config/refreshRouter", Math.random());
          });
        }
      });
    },
    onSubmit() {
      this.$refs.senderForm.validate((valid) => {
        if (!valid) return;
        if (this.isEdit) {
          this.$api.putSenderAdress(this.senderInfo).then((res1) => {
            if (!res1) return;
            this.$router.push({
              name: "customerManagement",
            });
          });
        } else {
          this.$api.addSenderAdress(this.senderInfo).then((res) => {
            if (!res) return;
            this.$router.push({
              name: "customerManagement",
            });
          });
        }
      });
    },
    getMerchants() {
      // 货主列表
      this.$api.getMerchantsPublic().then((res) => {
        this.merchantsList = res.data.data.filter((el) => {
          return el.status == 1;
        });
        if (this.merchantsList[0] && this.merchantsList[0].id) {
          this.senderInfo.merchant_id = this.merchantsList[0].id;
        }
      });
    },
    getCountryList() {
      // 国家列表
      this.$api.getCountry().then((res) => {
        this.address = res.data;
      });
    },

    // 地图
    handlerAddressSelected(data) {
      this.senderInfo.second_place_address = data.address;
      this.senderInfo.second_place_post_code = data.postal_code;
      this.senderInfo.second_place_house_number = data.street_number;
      this.senderInfo.second_place_city = data.city;
      this.senderInfo.second_place_street = `${data.street_number},${data.route}`;
      this.senderInfo.second_place_lon = data.location.lng;
      this.senderInfo.second_place_lat = data.location.lat;
      this.select_map = false;
    },
  },
};
</script>

<style lang="less" scoped>
.sen_main {
  background: #fff;
  padding: 20px;
  display: flex;
  justify-content: center;
  .sen_form {
    width: 80%;
    .sen_btn {
      width: 280px;
      margin-left: auto;
    }
  }
  .locale-btn {
    color: #f6a50d !important;
    background-color: #fff !important;
  }
}
</style>
