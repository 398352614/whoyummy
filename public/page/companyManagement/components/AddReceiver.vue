<template>
  <div class="rec-main">
    <el-form
      class="rec_form"
      :model="receiverInfo"
      :rules="rules"
      ref="receiverForm"
      label-position="right"
      label-width="160px"
    >
      <el-form-item :label="$t('MerchantType') + ':'" prop="type">
        <el-radio-group v-model="receiverInfo.type">
          <el-radio :label="1">{{ $t("Sender") }}</el-radio>
          <el-radio :label="2">{{ $t("Receiver") }}</el-radio>
        </el-radio-group>
        <!-- <el-select v-model="receiverInfo.type" :placeholder="$t('请选择类型')">
          <el-option
            v-for="item in addressTypeList"
            :key="item.id"
            :label="item.name"
            :value="item.id"
          ></el-option>
        </el-select> -->
      </el-form-item>
      <el-form-item :label="$t('Name') + ':'" prop="place_fullname">
        <el-input
          v-model="receiverInfo.place_fullname"
          :placeholder="$t('pleaseEnterName')"
        ></el-input>
      </el-form-item>
      <el-form-item :label="$t('Tel') + ':'" prop="place_phone">
        <el-input
          v-model="receiverInfo.place_phone"
          :placeholder="$t('PleaseEnterYel')"
        ></el-input>
      </el-form-item>

      <div v-if="template_id == 1">
        <!-- 国家 -->
        <el-form-item :label="$t('Country')" prop="place_country">
          <el-select
            v-model="receiverInfo.place_country"
            :placeholder="$t('pleaseSelectCountry')"
            style="width: 100%"
            @change="changeCountry"
            :class="showCountry ? 'select-country' : ''"
          >
            <el-option
              v-for="item in countryList"
              :key="item.short"
              :label="item.name"
              :value="item.short"
            >
            </el-option>
          </el-select>
          <div v-if="showCountry" class="country">
            {{ $t("pleaseSelectCountry") }}
          </div>
        </el-form-item>
        <el-row>
          <el-col :span="12">
            <el-form-item :label="$t('ZipCode') + ':'" prop="place_post_code">
              <el-input
                v-model="receiverInfo.place_post_code"
                @blur="getLocation"
                :placeholder="$t('PleaseEnterZipCode')"
              ></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item :label="$t('DoorNo') + ':'" prop="place_house_number">
              <el-input
                v-model="receiverInfo.place_house_number"
                @blur="getLocation"
                :placeholder="$t('PleaseEnterDoorNo')"
              ></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item :label="$t('City') + ':'" prop="place_city">
          <el-input
            v-model="receiverInfo.place_city"
            :disabled="disabledBtnByCountry"
            @blur="getLocation"
            :placeholder="$t('PleaseSelectCity')"
          ></el-input>
        </el-form-item>
        <el-form-item :label="$t('Street') + ':'" prop="place_street">
          <el-input
            v-model="receiverInfo.place_street"
            :disabled="disabledBtnByCountry"
            @blur="getLocation"
            :placeholder="$t('PleaseEnterStreet')"
          ></el-input>
        </el-form-item>
        <!-- <el-form-item :label="$t('Address') + ':'" prop="place_address">
          <el-input v-model="receiverInfo.place_address" :placeholder="$t('PleaseEnterAddress')"></el-input>
        </el-form-item> -->
      </div>
      <el-form-item v-else :label="$t('Address') + ':'" prop="place_address">
        <input-search @result="getResult" dom-id="pac_input_box"></input-search>
        <el-input
          id="pac_input_box"
          v-model="receiverInfo.place_address"
          :placeholder="$t('PleaseEnterAddress')"
        >
          <el-button
            slot="append"
            @click="select_map = true"
            class="locale-btn"
          >
            <i class="el-icon-map-location" />{{ $t("positioning") }}
          </el-button>
        </el-input>
      </el-form-item>

      <!-- <el-form-item :label="$t('Address') + ':'" prop="place_address">
        <el-input v-model="receiverInfo.place_address" :placeholder="$t('PleaseEnterAddress')"></el-input>
      </el-form-item> -->
      <el-form-item :label="$t('Merchant') + ':'" prop="merchant_id">
        <el-select
          v-model="receiverInfo.merchant_id"
          :placeholder="$t('PleaseSelectMerchant')"
        >
          <el-option
            v-for="item in merchantsList"
            :key="item.id"
            :label="item.name"
            :value="item.id"
          ></el-option>
        </el-select>
      </el-form-item>
      <div class="rec-btn">
        <el-button
          @click="onSave"
          :loading="this.$store.state.config.button_loading"
          class="cancel"
          >{{ $t("saveAndContinue") }}</el-button
        >
        <el-button
          type="primary"
          @click="onSubmit"
          class="submit"
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
      receiverInfo: {
        place_fullname: "",
        place_phone: "",
        place_country: "",
        place_post_code: "",
        place_house_number: "",
        place_city: "",
        place_street: "",
        place_address: "",
        place_lon: "",
        place_lat: "",
        type: 1,
        merchant_id: null,
      },
      showCountry: false,
      isEdit: false,
      countryList: [], //国家下拉
      merchantsList: [],
      addressTypeList: [],

      // 地图
      select_map: false,
      template_id: localStorage.getItem("address_template_id") || 1,
      country:
        localStorage.getItem("country") &&
        JSON.parse(localStorage.getItem("country")).en,
      rules: {
        type: [
          { required: true, message: this.$t("Required"), trigger: "change" },
        ],
        merchant_id: [
          { required: true, message: this.$t("Required"), trigger: "change" },
        ],
        place_fullname: [
          {
            required: true,
            message: this.$t("pleaseEnterName"),
            trigger: "blur",
          },
        ],
        place_phone: [
          {
            required: true,
            message: this.$t("PleaseEnterYel"),
            trigger: "blur",
          },
        ],
        place_country: [
          {
            required: true,
            message: this.$t("pleaseSelectCountry"),
            trigger: "change",
          },
        ],
        place_post_code: [
          {
            required: true,
            message: this.$t("PleaseEnterZipCode"),
            trigger: "blur",
          },
        ],
        place_house_number: [
          {
            required: true,
            message: this.$t("PleaseEnterDoorNo"),
            trigger: "blur",
          },
        ],
        place_city: [
          {
            required: true,
            message: this.$t("PleaseSelectCity"),
            trigger: "blur",
          },
        ],
        place_street: [
          {
            required: true,
            message: this.$t("PleaseEnterStreet"),
            trigger: "blur",
          },
        ],
        place_address: [
          {
            required: true,
            message: this.$t("PleaseEnterAddress"),
            trigger: "blur",
          },
        ],
      },
    };
  },
  computed: {
    disabledBtnByCountry() {
      return this.receiverInfo.place_country === "NL";
    },
    mapType() {
      return this.$store.state.globalData.maptype;
    },
  },
  created() {
    this.getDictionary();
    this.getCountryList();
    this.getMerchants();
    if (this.$route.query.id) {
      this.isEdit = true;
      this.getDetail(this.$route.query.id);
    }
  },
  methods: {
    getDictionary() {
      this.$api.getDictionary().then((res) => {
        this.addressTypeList = res.data.addressTypeList;
      });
    },
    getResult(data) {
      // 填充收货人
      this.receiverInfo.place_city = data.city;
      this.receiverInfo.place_street = `${data.route}`;
      this.receiverInfo.place_house_number = data.street_number;
      this.receiverInfo.place_country = data.country;
      this.receiverInfo.place_post_code = data.postal_code;
      this.receiverInfo.place_address = data.address;
      this.receiverInfo.place_lat = data.location.lat;
      this.receiverInfo.place_lon = data.location.lng;
    },
    getDetail(id) {
      this.$api.getReceiverAddressDetails(id).then((res) => {
        this.receiverInfo = res.data;
        if (!res.data.merchant_id) {
          this.receiverInfo.merchant_id = 1;
        }
      });
    },
    onSave() {
      this.$refs.receiverForm.validate((valid) => {
        if (!valid) return;
        if (this.isEdit) {
          this.$api.putReceiverAddress(this.receiverInfo).then((res) => {
            if (!res) return;
            this.$store.commit("config/refreshRouter", Math.random());
          });
        } else {
          this.$api.addReceiverAddress(this.receiverInfo).then((res) => {
            if (!res) return;
            this.$store.commit("config/refreshRouter", Math.random());
          });
        }
      });
    },
    onSubmit() {
      this.$refs.receiverForm.validate((valid) => {
        if (!valid) return;
        if (this.isEdit) {
          this.$api.putReceiverAddress(this.receiverInfo).then((res1) => {
            if (!res1) return;
            this.$router.push({
              name: "CustomerManagement",
              params: {
                CustomerManagement: 1,
              },
            });
          });
        } else {
          this.$api.addReceiverAddress(this.receiverInfo).then((res) => {
            if (!res) return;
            this.$router.push({
              name: "CustomerManagement",
              params: {
                CustomerManagement: 1,
              },
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
          this.receiverInfo.merchant_id = this.merchantsList[0].id;
        }
      });
    },
    getCountryList() {
      // 国家列表
      this.$api.getCountry().then((res) => {
        this.countryList = res.data;
      });
    },
    // 改变国家
    changeCountry() {
      if (
        this.receiverInfo.place_country != "" ||
        this.receiverInfo.place_country != null
      ) {
        this.showCountry = false;
      } else {
        this.showCountry = true;
      }
      this.receiverInfo.place_post_code = "";
      this.receiverInfo.place_house_number = "";
      this.receiverInfo.place_city = "";
      this.receiverInfo.place_street = "";
      this.receiverInfo.place_lon = "";
      this.receiverInfo.place_lat = "";
    },
    getLocation() {
      // 判断国家显隐
      if (
        this.receiverInfo.place_country == "" ||
        this.receiverInfo.place_country == null
      ) {
        this.showCountry = true;
      } else {
        this.showCountry = false;
      }
      //判断国家填充城市
      if (
        this.receiverInfo.place_post_code &&
        this.receiverInfo.place_house_number &&
        this.receiverInfo.place_country === "NL"
      ) {
        let isNL = false;
        if (this.receiverInfo.place_country === "NL") {
          this.receiverInfo.place_city = "";
          this.receiverInfo.place_street = "";
          isNL = true;
        }
        this.$api
          .getLocation({
            country: this.receiverInfo.place_country,
            post_code: this.receiverInfo.place_post_code,
            house_number: this.receiverInfo.place_house_number,
          })
          .then((res) => {
            if (!res) return;
            this.receiverInfo.place_lat = res.data.lat;
            this.receiverInfo.place_lon = res.data.lon;
            if (isNL) {
              this.receiverInfo.place_city = res.data.city;
              this.receiverInfo.place_street = res.data.street;
            }
          });
      } else if (
        this.receiverInfo.place_post_code &&
        this.receiverInfo.place_house_number &&
        this.receiverInfo.place_city &&
        this.receiverInfo.place_street
      ) {
        this.$api
          .getLocation({
            country: this.receiverInfo.place_country,
            post_code: this.receiverInfo.place_post_code,
            house_number: this.receiverInfo.place_house_number,
            city: this.receiverInfo.place_city,
            street: this.receiverInfo.place_street,
          })
          .then((res) => {
            if (!res) return;
            this.receiverInfo.place_lat = res.data.lat;
            this.receiverInfo.place_lon = res.data.lon;
          });
      }
    },
    // 地图
    handlerAddressSelected(data) {
      this.receiverInfo.place_country = data.country;
      this.receiverInfo.place_address = data.address;
      this.receiverInfo.place_post_code = data.postal_code;
      this.receiverInfo.place_house_number = data.street_number;
      this.receiverInfo.place_city = data.city;
      this.receiverInfo.place_street = data.route;
      this.receiverInfo.place_lon = data.location.lng;
      this.receiverInfo.place_lat = data.location.lat;
      this.select_map = false;
    },
  },
};
</script>

<style lang="less" scoped>
.rec-main {
  height: calc(100vh - 158px);
  background: #fff;
  padding: 20px;
  display: flex;
  justify-content: center;
  .rec_form {
    width: 80%;
    .rec-btn {
      float: right;
      margin-left: auto;
    }
  }
  .locale-btn {
    color: #f6a50d !important;
    background-color: #fff !important;
  }
}
</style>
