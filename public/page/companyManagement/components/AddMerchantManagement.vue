<template>
  <div class="addMerchantManagement-box">
    <!-- 添加货主 -->
    <div class="form-main">
      <el-form
        :rules="formRules"
        ref="formData"
        :model="formData"
        label-width="200px"
      >
        <div class="form_box">
          <div>
            <div class="label-info">{{ $t("GeneralInfo") }}</div>
            <!-- <el-form-item :label="$t('MerchantType')">
              <el-radio-group v-model="formData.type">
                <el-radio :label="1">{{$t('personal')}}</el-radio>
                <el-radio :label="2">{{$t('货主')}}</el-radio>
              </el-radio-group>
            </el-form-item> -->
            <el-row :gutter="20">
              <el-col :span="12">
                <el-form-item :label="$t('enterpriseName') + ':'" prop="name">
                  <el-input
                    size="small"
                    v-model="formData.name"
                    style="width: 300px"
                  ></el-input>
                </el-form-item>
                <el-form-item
                  :label="$t('ContactPerson') + ':'"
                  prop="contacter"
                >
                  <el-input
                    size="small"
                    v-model="formData.contacter"
                    style="width: 300px"
                  ></el-input>
                </el-form-item>
                <!-- 联系地址 -->
                <!-- <el-row :gutter="20" v-if="template_id == 1">
                  <el-col :span="12">
                    <el-form-item :label="$t('ZipCode')" prop="post_code">
                      <el-input
                        v-model="formData.post_code"
                        @change="getLocation()"
                        :placeholder="$t('PleaseEnterZipCode')"
                        size="small"
                        style="width: 100%"
                      ></el-input>
                    </el-form-item>
                  </el-col>
                  <el-col :span="12">
                    <el-form-item :label="$t('DoorNo')" prop="house_number">
                      <el-input
                        v-model="formData.house_number"
                        @change="getLocation()"
                        :placeholder="$t('PleaseEnterDoorNo')"
                        style="width: 100%"
                        size="small"
                      ></el-input>
                    </el-form-item>
                  </el-col>
                  <el-col :span="12">
                    <el-form-item :label="$t('City')" prop="city">
                      <el-input
                        v-model="formData.city"
                        :disabled="disabledBtnByCountry"
                        @change="getLocation()"
                        :placeholder="$t('PleaseEnterCity')"
                        style="width: 100%"
                        size="small"
                      ></el-input>
                    </el-form-item>
                  </el-col>
                  <el-col :span="12">
                    <el-form-item :label="$t('Street')" prop="street">
                      <el-input
                        v-model="formData.street"
                        :disabled="disabledBtnByCountry"
                        @change="getLocation()"
                        :placeholder="$t('PleaseEnterStreet')"
                        style="width: 100%"
                        size="small"
                      ></el-input>
                    </el-form-item>
                  </el-col>
                </el-row> -->
                <el-form-item :label="$t('address') + ':'" prop="address">
                  <input-search
                    @result="getResult(1, $event)"
                    dom-id="addressId"
                  ></input-search>
                  <el-input
                    id="addressId"
                    size="small"
                    v-model="formData.address"
                    :placeholder="$t('PleaseEnterAddress')"
                    style="width: 300px"
                  >
                    <el-button
                      slot="append"
                      @click="selectAddressMap"
                      class="locale-btn"
                    >
                      <i class="el-icon-map-location" />{{ $t("positioning") }}
                    </el-button>
                  </el-input>
                </el-form-item></el-col
              >
              <el-col :span="12">
                <el-form-item :label="$t('Tel') + ':'" prop="phone">
                  <el-input
                    size="small"
                    v-model="formData.phone"
                    style="width: 300px"
                  ></el-input>
                </el-form-item>
                <el-form-item :label="$t('Email') + ':'" prop="email">
                  <el-input
                    size="small"
                    v-model="formData.email"
                    style="width: 300px"
                  ></el-input>
                </el-form-item>
                <el-form-item
                  :label="$t('enterpriseProfile') + ':'"
                  prop="introduction"
                >
                  <el-input
                    size="small"
                    v-model="formData.introduction"
                    style="width: 300px"
                    type="textarea"
                    :rows="5"
                  ></el-input> </el-form-item
              ></el-col>
            </el-row>
            <div class="label-info">{{ $t("invoiceInfo") + ":" }}</div>
            <el-row :gutter="20">
              <el-col :span="12">
                <el-form-item
                  :label="$t('invoiceTitle') + ':'"
                  prop="invoice_title"
                >
                  <el-input
                    size="small"
                    v-model="formData.invoice_title"
                    style="width: 300px"
                  ></el-input>
                </el-form-item>
                <el-form-item :label="$t('bankName') + ':'" prop="bank">
                  <el-input
                    size="small"
                    v-model="formData.bank"
                    style="width: 300px"
                  ></el-input>
                </el-form-item>
                <!-- 寄票地址 -->
                <!-- <el-row :gutter="20" v-if="template_id == 1">
                  <el-col :span="12">
                    <el-form-item :label="$t('ZipCode')" prop="post_code">
                      <el-input
                        v-model="formData.post_code"
                        @change="getLocation()"
                        :placeholder="$t('PleaseEnterZipCode')"
                        size="small"
                        style="width: 100%"
                      ></el-input>
                    </el-form-item>
                  </el-col>
                  <el-col :span="12">
                    <el-form-item :label="$t('DoorNo')" prop="house_number">
                      <el-input
                        v-model="formData.house_number"
                        @change="getLocation()"
                        :placeholder="$t('PleaseEnterDoorNo')"
                        style="width: 100%"
                        size="small"
                      ></el-input>
                    </el-form-item>
                  </el-col>
                  <el-col :span="12">
                    <el-form-item :label="$t('City')" prop="city">
                      <el-input
                        v-model="formData.city"
                        :disabled="disabledBtnByCountry"
                        @change="getLocation()"
                        :placeholder="$t('PleaseEnterCity')"
                        style="width: 100%"
                        size="small"
                      ></el-input>
                    </el-form-item>
                  </el-col>
                  <el-col :span="12">
                    <el-form-item :label="$t('Street')" prop="street">
                      <el-input
                        v-model="formData.street"
                        :disabled="disabledBtnByCountry"
                        @change="getLocation()"
                        :placeholder="$t('PleaseEnterStreet')"
                        style="width: 100%"
                        size="small"
                      ></el-input>
                    </el-form-item>
                  </el-col>
                </el-row> -->
                <el-form-item
                  :label="$t('sendingBillAddress') + ':'"
                  prop="invoice_address"
                >
                  <input-search
                    @result="getResult(2, $event)"
                    dom-id="invoiceId"
                  ></input-search>
                  <el-input
                    id="invoiceId"
                    size="small"
                    v-model="formData.invoice_address"
                    :placeholder="$t('PleaseEnterAddress')"
                    style="width: 300px"
                  >
                    <el-button
                      slot="append"
                      @click="selectInvoiceMap"
                      class="locale-btn"
                    >
                      <i class="el-icon-map-location" />{{ $t("positioning") }}
                    </el-button></el-input
                  >
                </el-form-item></el-col
              >
              <el-col :span="12">
                <el-form-item
                  :label="$t('taxpayerIdentificationCode') + ':'"
                  prop="taxpayer_code"
                >
                  <el-input
                    size="small"
                    v-model="formData.taxpayer_code"
                    style="width: 300px"
                  ></el-input>
                </el-form-item>

                <el-form-item
                  :label="$t('bankAccount') + ':'"
                  prop="bank_account"
                >
                  <el-input
                    size="small"
                    v-model="formData.bank_account"
                    style="width: 300px"
                  ></el-input>
                </el-form-item>
                <el-form-item
                  :label="$t('receivingBillAddress') + ':'"
                  prop="invoice_email"
                >
                  <el-input
                    size="small"
                    v-model="formData.invoice_email"
                    style="width: 300px"
                  ></el-input> </el-form-item
              ></el-col>
            </el-row>
            <!-- <el-form-item :label="$t('Merchant')" prop="name">
              <el-input size="small" v-model="formData.name"></el-input>
            </el-form-item>
            <el-form-item :label="$t('ContactName')" prop="contacter">
              <el-input size="small" v-model="formData.contacter"></el-input>
            </el-form-item>
            <el-form-item :label="$t('Tel')" prop="phone">
              <el-input size="small" v-model="formData.phone"></el-input>
            </el-form-item>
            <el-form-item :label="$t('address')" prop="address">
              <el-input size="small" v-model="formData.address"></el-input>
            </el-form-item> -->

            <div class="label-info">{{ $t("expansionInfo") }}</div>
            <el-row :gutter="20">
              <el-col :span="12">
                <!-- 结算方式 -->
                <el-form-item
                  :label="$t('SettlementMethod') + ':'"
                  prop="settlement_type"
                >
                  <el-select
                    size="small"
                    v-model="formData.settlement_type"
                    style="width: 300px"
                  >
                    <el-option
                      v-for="item in payType"
                      :key="item.id"
                      :label="item.name"
                      :value="item.id"
                    >
                    </el-option>
                  </el-select>
                </el-form-item>
                <!-- 自动生成对账单 -->
                <div class="automatic-bill">
                  <el-form-item :label="$t('AutomaticBillGeneration')">
                    <el-checkbox
                      v-model="checked"
                      @change="handleCheckedCitiesChange"
                    ></el-checkbox>
                  </el-form-item>
                  <div v-if="formData.auto_settlement == 1">
                    <div class="bill-box" v-if="formData.settlement_type == 1">
                      <div>
                        {{ $t("WhenfOrderCompleted") }}
                      </div>
                    </div>
                    <el-form-item
                      :label="$t('Daily')"
                      style="margin-left: -50px"
                      v-if="formData.settlement_type == 2"
                    >
                      {{ formData.settlement_time }}
                    </el-form-item>
                    <el-form-item
                      :label="$t('weekly')"
                      style="margin-left: -50px"
                      v-if="formData.settlement_type == 3"
                    >
                      <el-select
                        size="small"
                        v-model="formData.settlement_week"
                        :clearable="true"
                      >
                        <el-option
                          v-for="item in weekList"
                          :key="item.id"
                          :label="item.name"
                          :value="item.id"
                        >
                        </el-option>
                      </el-select>
                    </el-form-item>
                    <el-form-item
                      :label="$t('perMonth')"
                      prop="merchant_group_id"
                      style="margin-left: -50px"
                      v-if="formData.settlement_type == 4"
                    >
                      <el-select
                        size="small"
                        v-model="formData.settlement_date"
                        :clearable="true"
                      >
                        <el-option
                          v-for="item in mouthDate"
                          :key="item.id"
                          :label="item.name"
                          :value="item.id"
                        >
                        </el-option>
                      </el-select>
                    </el-form-item>
                  </div>
                </div>
                <!-- 货主组  -->
                <el-form-item
                  :label="$t('MerchantGroup') + ':'"
                  prop="merchant_group_id"
                >
                  <el-select
                    size="small"
                    v-model="formData.merchant_group_id"
                    :clearable="true"
                    style="width: 300px"
                  >
                    <el-option
                      v-for="item in MerchantGroups"
                      :key="item.id"
                      :label="item.name"
                      :value="item.id"
                    >
                    </el-option>
                  </el-select>
                </el-form-item>

                <!-- 仓库一体 -->
                <!-- <el-form-item :label="$t('integration')">
                  <el-switch
                    v-model="formData.below_warehouse"
                    :active-value="1"
                    :inactive-value="2"
                  >
                  </el-switch>
                  <div class="form-title">
                    {{ $t("integrationState") }}
                  </div>
                </el-form-item> -->
                <!-- 签约网点 -->
                <!-- <el-form-item
                  :label="$t('contractedOutstation') + ':'"
                  prop="warehouse_id"
                  v-if="this.formData.below_warehouse == 1"
                >
                  <el-select
                    size="small"
                    v-model="formData.warehouse_id"
                    :clearable="true"
                    style="width: 300px"
                  >
                    <el-option
                      v-for="item in warehouseList"
                      :key="item.id"
                      :label="item.name"
                      :value="item.id"
                    >
                    </el-option>
                  </el-select>
                </el-form-item> -->
                <!-- 状态 -->
                <el-form-item :label="$t('Status') + ':'">
                  <el-radio-group v-model="formData.status">
                    <el-radio :label="1">{{ $t("enable") }}</el-radio>
                    <el-radio :label="2">{{ $t("disable") }}</el-radio>
                  </el-radio-group>
                </el-form-item></el-col
              >
              <el-col :span="12">
                <div class="avatar-box">
                  <el-form-item label=" " ref="avatar">
                    <div class="avatar_desc">
                      {{ $t("enterpriseLogo") + ":" }}
                    </div>
                    <el-upload
                      class="avatar-uploader"
                      :headers="Authorization"
                      :on-success="handleAvatarSuccess"
                      :action="BaseApiFile"
                      :on-remove="handlerClearAvatar"
                      :file-list="fileList"
                      :limit="1"
                      name="image"
                      :data="{
                        dir: 'merchant',
                      }"
                    >
                      <img
                        v-if="this.formData.avatar"
                        :src="this.formData.avatar"
                        class="avatar"
                      />
                      <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                    </el-upload>
                  </el-form-item></div
              ></el-col>
            </el-row>
          </div>
        </div>
      </el-form>
      <div class="footer-button">
        <el-button size="small" @click="handlerCancel" class="cancel">{{
          $t("Cancel")
        }}</el-button>
        <el-button
          size="small"
          type="primary"
          @click="onSubmit"
          class="submit"
          :loading="this.$store.state.config.button_loading"
          >{{ $t("Submit") }}</el-button
        >
      </div>
    </div>
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
// import InputSearchMap from "../../../components/BaiduMap/InputSearchMap.vue";
export default {
  name: "addMerchantManagement",
  components: {
    googleMap,
    InputSearch,
    BMap,
    InputSearchBaidu,
    // InputSearchMap,
  },
  data() {
    return {
      template_id: localStorage.getItem("address_template_id") || 1,
      country:
        localStorage.getItem("country") &&
        JSON.parse(localStorage.getItem("country")).en,
      select_map: false,
      dialogVisible: false,
      settlementTypes: [],
      MerchantGroups: [],
      warehouseList: [],
      fileList: [],
      checked: false,
      formData: {
        type: 2,
        name: "", //名称
        email: "", //邮箱
        settlement_type: 1,
        merchant_group_id: "", //货主组
        contacter: "", //联系人
        phone: "", //电话
        address: "", //联系地址
        avatar: "", //企业Logo
        status: 1, //状态
        invoice_title: "", //发票抬头
        bank: "", //开户行
        invoice_address: "", //寄票地址
        taxpayer_code: "", //纳税人识别码
        bank_account: "", //开户账号
        invoice_email: "", //收票邮箱
        introduction: "", //企业简介
        warehouse_id: "", //签约网点
        below_warehouse: 2,
        advance_days: 0,
        appointment_days: "",
        delay_time: 0,
        fee_code_list: "",
        additional_status: 1,
        pie_count: null,
        pickup_count: null,
        auto_settlement: 2,
        settlement_time: "00:00",
        settlement_week: "",
        settlement_date: "",
      },
      fee_code_list: [],
      payType: [],
      pageParams: {
        page: 1,
        per_page: 10,
        total: 0,
        status: this.$route.query.status,
        driver_id: "", // 司机ID
        begin_date: "",
        end_date: "",
        keyword: this.$route.query.id,
      },
      feeList: [],
      feePageParams: {
        merchant_id: this.$route.query.merchant_id || "",
      },
      mouthDate: [
        { id: 1, name: 1 },
        { id: 2, name: 2 },
        { id: 3, name: 3 },
        { id: 4, name: 4 },
        { id: 5, name: 5 },
        { id: 6, name: 6 },
        { id: 7, name: 7 },
        { id: 8, name: 8 },
        { id: 9, name: 9 },
        { id: 10, name: 10 },
        { id: 11, name: 11 },
        { id: 12, name: 12 },
        { id: 13, name: 13 },
        { id: 14, name: 14 },
        { id: 15, name: 15 },
        { id: 16, name: 16 },
        { id: 17, name: 17 },
        { id: 18, name: 18 },
        { id: 19, name: 19 },
        { id: 20, name: 20 },
        { id: 21, name: 21 },
        { id: 22, name: 22 },
        { id: 23, name: 23 },
        { id: 24, name: 24 },
        { id: 25, name: 25 },
        { id: 26, name: 26 },
        { id: 27, name: 27 },
        { id: 28, name: 28 },
      ],
    };
  },
  created() {
    this.getDictionary();
    this.getWarehouse();
    this.getPayTypes();
    // this.getFee();
    this.getMerchantGroups();
    if (this.isEdit) {
      this.getList(this.$route.query.id);
    }
  },
  computed: {
    disabledBtnByCountry() {
      return this.country === "NL";
    },
    Authorization() {
      return { Authorization: this.$store.state.token.token };
    },
    BaseApiFile() {
      return `${baseApi}admin/upload/image/`;
    },
    isEdit() {
      return !!this.$route.query.id;
    },
    mapType() {
      return this.$store.state.config.maptype;
    },
    formRules() {
      return {
        name: [
          //名称
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        email: [
          //邮箱
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        contacter: [
          //联系人
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        phone: [
          //电话
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        address: [
          //联系地址
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        introduction: [
          //企业简介
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        merchant_group_id: [
          //货主组
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        warehouse_id: [
          //网点
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        appointment_days: [
          {
            validator: (rule, value, callback) => {
              if (value < 0) {
                callback(new Error("必须大于0"));
              } else {
                callback();
              }
            },
            trigger: "change",
          },
        ],
        delay_time: [
          {
            validator: (rule, value, callback) => {
              let isnum2 = /^\d+$/.test(value);
              if (!isnum2 && value) {
                callback(new Error("必须是纯数字"));
              } else if (value < 0) {
                callback(new Error("必须大于0"));
              } else if (value > 60) {
                callback(new Error("必须小于60"));
              } else {
                callback();
              }
            },
            trigger: "change",
          },
        ],
      };
    },
  },
  methods: {
    getDictionary() {
      this.$api.getDictionary().then((res) => {
        this.weekList = res.data.weekList;
      });
    },
    handleCheckedCitiesChange(val) {
      if (val == true) {
        this.formData.auto_settlement = 1;
      } else {
        this.formData.auto_settlement = 2;
      }
    },
    getWarehouse() {
      // 网点
      let acceptance_type = 3; //获取仓配一体
      this.$api
        .getWarehouse({ acceptance_type: acceptance_type })
        .then((res) => {
          this.warehouseList = res.data.data;
        });
    },
    // 地址
    getResult(type, data) {
      if (type == 1) {
        this.formData.address = data.address; //联系地址
        // document.getElementById("addressId").value = data.address;
      } else {
        this.formData.invoice_address = data.address; //联系地址
        // document.getElementById("invoiceId").value = data.address;
      }
    },
    getLocation() {
      console.log(this.country, "this.country");
      if (
        this.formData.place_post_code &&
        this.formData.place_house_number &&
        (this.country === "NL" ||
          (this.formData.place_city && this.formData.place_street))
      ) {
        let isNL = false;
        if (this.country === "NL") {
          this.formData.place_city = "";
          this.formData.place_street = "";
          isNL = true;
        }
        this.$api
          .getLocation({
            post_code: this.formData.place_post_code,
            house_number: this.formData.place_house_number,
          })
          .then((res) => {
            if (!res) return;
            this.formData.place_lat = res.data.lat;
            this.formData.place_lon = res.data.lon;
            if (isNL) {
              this.formData.place_city = res.data.city;
              this.formData.place_street = res.data.street;
            }
          });
      } else if (
        this.formData.post_code &&
        this.formData.house_number &&
        this.formData.city &&
        this.formData.street
      ) {
        this.$api
          .getLocation({
            post_code: this.formData.post_code,
            house_number: this.formData.house_number,
            city: this.formData.city,
            street: this.formData.street,
          })
          .then((res) => {
            if (!res) return;
            this.formData.lat = res.data.lat;
            this.formData.lon = res.data.lon;
          });
      }
    },
    getPayTypes() {
      this.$api.getPayTypes().then((res) => {
        if (!res) return;
        this.payType = res.data.settlement_type_list;
      });
    },
    handlerClearAvatar() {
      this.fileList = [];
      this.formData.avatar = "";
    },
    handleAvatarSuccess(response) {
      if (response.code === 200) {
        this.formData.avatar = response.data.path;
        this.$refs.avatar.clearValidate();
      }
    },
    getMerchantGroups() {
      this.$api.getMerchantsGroupPublic().then((res) => {
        if (!res) return;
        this.MerchantGroups = res.data.data;
        // 新建默认选中默认货主组
        if (!this.isEdit) {
          res.data.data.forEach((el) => {
            if (el.is_default === 1) {
              this.formData.merchant_group_id = el.id;
            }
          });
        }
      });
    },
    getList(id) {
      this.$api.getMerchantsDetial(id).then((res) => {
        if (!res) return;
        this.formData = res.data;
        this.formData.settlement_time = "00:00";
        // 自动生成对账单 显示
        if (res.data.auto_settlement == 1) {
          this.checked = true;
        } else {
          this.checked = false;
        }
        this.fee_code_list = res.data.fee_list.map((el) => {
          return el.code;
        });
      });
    },
    handlerCancel() {
      this.transformRouter();
    },
    transformRouter() {
      this.$router.replace({
        name: "MerchantManagement",
        params: {
          MerchantManagement: 1,
        },
      });
    },
    onSubmit() {
      this.$refs.formData.validate((validateRes) => {
        if (!validateRes) return;
        this.formData.fee_code_list = this.fee_code_list.join(",");
        // 判断未开启仓配把warehouse_id置空
        if (this.formData.below_warehouse == 2) {
          this.formData.warehouse_id = "";
        }
        if (this.isEdit) {
          delete this.formData.fee_list;
          this.$api
            .editMerchants(this.$route.query.id, this.formData)
            .then((res) => {
              if (!res) return;
              this.transformRouter();
            });
          return;
        }
        this.$api.addMerchants(this.formData).then((res) => {
          if (!res) return;
          this.transformRouter();
        });
      });
    },

    getFee() {
      // 增值服务列表
      this.$api.getFeeList(this.feePageParams).then((res) => {
        this.feeList = res.data;
        console.log(this.feeList);
      });
    },
    // 联系地址
    selectAddressMap() {
      this.select_map = true;
      this.ReceiverMapDialog = false;
    },
    // 寄票地址
    selectInvoiceMap() {
      this.select_map = true;
      this.ReceiverMapDialog = true;
    },
    // 获取地图的定位信息
    handlerAddressSelected(data) {
      if (this.ReceiverMapDialog) {
        this.formData.invoice_address = data.address;
      } else {
        this.formData.address = data.address;
      }
      this.select_map = false;
    },
  },
};
</script>

<style lang="less" scoped>
// @import "../../../less/public_variable.less";
.addMerchantManagement-box {
  background-color: #fff;
  padding: 20px;
  .form-main {
    margin: 20px;
    .form_box {
      .label-info {
        margin-left: 80px;
        margin-bottom: 20px;
        font-weight: bold;
      }
      .avatar-box {
        margin-top: -50px;
        .avatar-desc {
          margin-left: 50px;
        }
      }
      .form-title {
        font-size: 10px;
      }
    }
  }
  .footer-button {
    float: right;
  }
  .automatic-bill {
    display: flex;
    justify-content: space-between;
    width: 500px;
    .bill-box {
      margin-top: 10px;
    }
  }
}
</style>

