<template>
  <div class="testing-process">
    <el-card class="box-card">
      <el-row :gutter="20">
        <el-col :span="3">
          <img src="../../../assets/img/test-img/logo.png" alt="" class="img" />
        </el-col>
        <el-col :span="12">
          <div class="headline">{{ $t("TestingProcess") }}</div>
          <div>
            {{ $t("TestingProcessState") }}
            <!-- {{ template_id == 1 ? "您当前选择的是按邮编划分区域" : "" }} -->
          </div>
        </el-col>
      </el-row>
    </el-card>
    <el-form
      :model="form"
      ref="form"
      :rules="formRules"
      label-width="120px"
      label-position="right"
    >
      <el-card class="card">
        <el-row :gutter="20">
          <!-- 发件人 -->
          <el-col :span="12">
            <h4 class="box-title">
              {{ $t("Sender") }}
              <span class="add-row address-table">
                <el-button @click="setSender">{{
                  $t("AddressBook")
                }}</el-button>
              </span>
            </h4>
            <div class="shipper">
              <el-row>
                <el-col :span="3" class="shipper-box">
                  <div class="shipper-img">
                    <img
                      src="../../../assets/img/test-img/shipments.png"
                      alt=""
                    />
                    <div class="shipper-title">{{ $t("Sender") }}</div>
                  </div>
                </el-col>
                <el-col :span="20" class="shipper-form">
                  <!-- 姓名 -->
                  <el-form-item :label="$t('Sender')" prop="place_fullname">
                    <el-input v-model="form.place_fullname"></el-input>
                  </el-form-item>
                  <!-- 电话 -->
                  <!-- <el-form-item :label="$t('Tel')" prop="place_phone">
                    <el-input v-model="form.place_phone"></el-input>
                  </el-form-item> -->
                  <!-- 地址 -->
                  <!-- <el-form-item :label="$t(this.$t('Address'))" prop="place_phone">
                    <el-input v-model="form.place_phone"></el-input>
                  </el-form-item> -->

                  <div v-if="template_id == 1">
                    <!-- 国家 -->
                    <el-form-item :label="$t('Country')" prop="place_country">
                      <el-select
                        v-model="form.place_country"
                        :placeholder="$t('pleaseSelectCountry')"
                        size="small"
                        style="width: 100%"
                        @change="changeCountry(1)"
                        :class="showPlaceCountry ? 'select-country' : ''"
                      >
                        <el-option
                          v-for="item in countryList"
                          :key="item.short"
                          :label="item.name"
                          :value="item.short"
                        >
                        </el-option>
                      </el-select>
                      <div v-if="showPlaceCountry" class="country">
                        {{ $t("pleaseSelectCountry") }}
                      </div>
                    </el-form-item>
                    <!-- 邮编 -->
                    <el-form-item
                      :label="$t('ZipCode')"
                      prop="place_post_code"
                      class="item-style item-left"
                    >
                      <el-input
                        v-model="form.place_post_code"
                        @blur="getLocation(1)"
                      >
                      </el-input>
                    </el-form-item>
                    <!-- 门牌号 -->
                    <el-form-item
                      :label="$t('DoorNo')"
                      prop="place_house_number"
                      class="item-style item-font"
                    >
                      <el-input
                        v-model="form.place_house_number"
                        @blur="getLocation(1)"
                      ></el-input>
                    </el-form-item>
                    <!-- 城市 -->
                    <el-form-item
                      :label="$t('City')"
                      prop="place_city"
                      class="item-style item-left"
                    >
                      <el-input
                        v-model="form.place_city"
                        @blur="getLocation(1)"
                        :disabled="disabledBtnByCountry(1)"
                      >
                      </el-input>
                    </el-form-item>
                    <!-- 街道 -->
                    <el-form-item
                      :label="$t('Street')"
                      prop="place_street"
                      class="item-style item-font"
                    >
                      <el-input
                        v-model="form.place_street"
                        @blur="getLocation(1)"
                        :disabled="disabledBtnByCountry(1)"
                      ></el-input>
                    </el-form-item>
                    <!-- 日期 -->
                    <el-form-item :label="$t('date')" prop="execution_date">
                      <el-date-picker
                        value-format="yyyy-MM-dd"
                        v-model="form.execution_date"
                        type="date"
                        :picker-options="disabledDateOptionsPik"
                        :placeholder="$t('PleaseDate')"
                      >
                      </el-date-picker>
                    </el-form-item>
                  </div>
                  <div v-else>
                    <el-form-item
                      :label="this.$t('Address')"
                      prop="place_address"
                    >
                      <el-input v-model="form.place_address">
                        <el-button
                          slot="append"
                          @click="selectReceiverMap"
                          class="locale-btn"
                        >
                          <i class="el-icon-map-location" />{{
                            $t("positioning")
                          }}
                        </el-button>
                      </el-input>
                    </el-form-item>
                    <!-- 日期 -->
                    <el-form-item :label="$t('date')" prop="execution_date">
                      <el-date-picker
                        value-format="yyyy-MM-dd"
                        v-model="form.execution_date"
                        type="date"
                        :picker-options="disabledDateOptionsPik"
                        :placeholder="$t('PleaseDate')"
                      >
                      </el-date-picker>
                    </el-form-item></div
                ></el-col>
              </el-row>
            </div>
          </el-col>

          <!-- 收件人 -->
          <el-col :span="12">
            <h4 class="box-title">
              {{ $t("Receiver") }}
              <span class="add-row address-table">
                <el-button @click="setReceiver">{{
                  $t("AddressBook")
                }}</el-button>
              </span>
            </h4>
            <div class="shipper">
              <el-row>
                <el-col :span="3" class="shipper-box">
                  <div class="shipper-img">
                    <img
                      src="../../../assets/img/test-img/pick-up-goods.png"
                      alt=""
                    />
                    <div class="shipper-title">{{ $t("Receiver") }}</div>
                  </div></el-col
                >
                <el-col :span="20" class="shipper-form">
                  <!-- 姓名 -->
                  <el-form-item
                    :label="$t('Receiver')"
                    prop="second_place_fullname"
                  >
                    <el-input v-model="form.second_place_fullname"></el-input>
                  </el-form-item>
                  <!-- 电话 -->
                  <!-- <el-form-item :label="$t('Tel')" prop="second_place_phone">
                    <el-input v-model="form.second_place_phone"></el-input>
                  </el-form-item> -->
                  <!-- 地址 -->
                  <!-- <el-form-item :label="$t(this.$t('Address'))" prop="place_phone">
                    <el-input v-model="form.place_phone"></el-input>
                  </el-form-item> -->

                  <div v-if="template_id == 1">
                    <!-- 国家 -->
                    <el-form-item
                      :label="$t('Country')"
                      prop="second_place_country"
                    >
                      <el-select
                        v-model="form.second_place_country"
                        :placeholder="$t('pleaseSelectCountry')"
                        size="small"
                        style="width: 100%"
                        @change="changeCountry(2)"
                        :class="showSecondCountry ? 'select-country' : ''"
                      >
                        <el-option
                          v-for="item in countryList"
                          :key="item.short"
                          :label="item.name"
                          :value="item.short"
                        >
                        </el-option>
                      </el-select>
                      <div v-if="showSecondCountry" class="country">
                        {{ $t("pleaseSelectCountry") }}
                      </div>
                    </el-form-item>
                    <!-- 邮编 -->
                    <el-form-item
                      :label="$t('ZipCode')"
                      prop="second_place_post_code"
                      class="item-style item-left"
                    >
                      <el-input
                        v-model="form.second_place_post_code"
                        @blur="getLocation(2)"
                      >
                      </el-input>
                    </el-form-item>
                    <!-- 门牌号 -->
                    <el-form-item
                      :label="$t('DoorNo')"
                      prop="second_place_house_number"
                      class="item-style item-font"
                    >
                      <el-input
                        v-model="form.second_place_house_number"
                        @blur="getLocation(2)"
                      ></el-input>
                    </el-form-item>
                    <!-- 城市 -->
                    <el-form-item
                      :label="$t('City')"
                      prop="second_place_city"
                      class="item-style item-left"
                    >
                      <el-input
                        v-model="form.second_place_city"
                        @blur="getLocation(2)"
                        :disabled="disabledBtnByCountry(2)"
                      >
                      </el-input>
                    </el-form-item>
                    <!-- 街道 -->
                    <el-form-item
                      :label="$t('Street')"
                      prop="second_place_street"
                      class="item-style item-font"
                    >
                      <el-input
                        v-model="form.second_place_street"
                        @blur="getLocation(2)"
                        :disabled="disabledBtnByCountry(2)"
                      ></el-input>
                    </el-form-item>
                    <!-- 日期 -->
                    <el-form-item
                      :label="$t('date')"
                      prop="second_execution_date"
                    >
                      <el-date-picker
                        value-format="yyyy-MM-dd"
                        v-model="form.second_execution_date"
                        type="date"
                        :picker-options="disabledDateOptionsPie"
                        :placeholder="$t('PleasePieDate')"
                      >
                      </el-date-picker>
                    </el-form-item>
                  </div>

                  <div v-else>
                    <el-form-item
                      :label="this.$t('Address')"
                      prop="second_place_address"
                    >
                      <el-input v-model="form.second_place_address">
                        <el-button
                          slot="append"
                          @click="selectSenderMap"
                          class="locale-btn"
                        >
                          <i class="el-icon-map-location" />{{
                            $t("positioning")
                          }}
                        </el-button>
                      </el-input>
                    </el-form-item>
                    <el-form-item
                      :label="$t('date')"
                      prop="second_execution_date"
                    >
                      <el-date-picker
                        value-format="yyyy-MM-dd"
                        v-model="form.second_execution_date"
                        type="date"
                        :picker-options="disabledDateOptionsPie"
                        :placeholder="$t('PleasePieDate')"
                      >
                      </el-date-picker>
                    </el-form-item>
                  </div>
                </el-col>
              </el-row>
            </div>
          </el-col>
        </el-row>
      </el-card>
    </el-form>
    <el-button class="card-but" @click="getTestingProcess">{{
      $t("Test")
    }}</el-button>
    <el-card class="box-bottom" v-if="show">
      <h4>{{ $t("TestResult") }}</h4>
      <div class="box">
        <!-- 流程图 -->
        <el-row :gutter="20">
          <el-col
            :span="2"
            v-for="(item, index) in folwData"
            :key="index"
            style="width: 190px; text-align: center"
          >
            <el-row :gutter="20" class="box-img">
              <!-- 发件人 收件人 -->
              <!-- is_center=3是 发件人 收件人 2网点 1分拨中心 -->
              <div v-if="item.is_center == 3">
                <el-col :span="12">
                  <img
                    src="../../../assets/img/test-img/head-portrait.png"
                    alt=""
                    width="50"
                  />
                </el-col>
                <!-- Status=1发件人 -->
                <el-col :span="12" v-if="item.status == 1">
                  <div class="box-pick">{{ $t("Pickup") }}</div>
                  <img
                    src="../../../assets/img/test-img/double-headed.png"
                    alt=""
                    width="50"
                  />
                  <div class="box-title">{{ $t("RuKu") }}</div>
                </el-col>
              </div>
              <!-- 网点 -->
              <div v-if="item.is_center == 2">
                <el-col :span="12">
                  <img
                    src="../../../assets/img/test-img/warehouse.png"
                    alt=""
                    width="50"
                  />
                </el-col>
                <el-col :span="12">
                  <div
                    class="box-pie"
                    v-if="index === folwData.length - 2 ? true : false"
                  >
                    {{ $t("Delivery") }}
                  </div>

                  <img
                    src="../../../assets/img/test-img/single-arrow.png"
                    alt=""
                    width="40"
                    class="box-arrow"
                  />
                </el-col>
              </div>
              <!-- 分拨中心 -->
              <div v-if="item.is_center == 1">
                <el-col :span="12">
                  <img
                    src="../../../assets/img/test-img/transfer-station.png"
                    alt=""
                    width="60"
                  />
                </el-col>
                <el-col :span="12">
                  <div
                    class="box-pie"
                    v-if="index === folwData.length - 2 ? true : false"
                  >
                    {{ $t("Delivery") }}
                  </div>
                  <img
                    src="../../../assets/img/test-img/single-arrow.png"
                    alt=""
                    width="40"
                    class="box-arrow"
                  />
                </el-col>
              </div>
            </el-row>
            <div class="status-name">{{ item.status_name }}</div>
            <div class="name">
              {{ item.name }}
            </div>
          </el-col>
        </el-row>
      </div>
    </el-card>

    <!-- 发货人 收货人 -->
    <el-dialog :visible.sync="show_receiver_address" width="60%">
      <span slot="title">{{
        this.type == 1 ? this.$t("Sender") : this.$t("Receiver")
      }}</span>
      <el-row :gutter="10">
        <el-col :span="6">
          <el-input
            size="small"
            style="margin-bottom: 10px"
            clearable
            :placeholder="$t('PleaseEnterZipCode')"
            @keyup.enter.native="getOrder"
            @clear="getOrder"
            v-model="pageParams.place_post_code"
          >
          </el-input>
        </el-col>
      </el-row>
      <el-table
        max-height="600px"
        :data="tableData"
        border
        @row-click="getTableItem"
        style="width: 100%"
      >
        <el-table-column align="center">
          <template slot-scope="scope">
            <el-radio v-model="scope.row.id">{{ "" }}</el-radio>
          </template>
        </el-table-column>
        <el-table-column type="index" align="center" width="50">
          <template slot="header">
            <span>{{ $t("inSequence") }}</span>
          </template>
        </el-table-column>
        <el-table-column
          prop="place_fullname"
          align="center"
          :label="$t('name')"
        >
        </el-table-column>
        <el-table-column prop="place_phone" align="center" :label="$t('Tel')">
        </el-table-column>
        <el-table-column
          prop="place_country_name"
          align="center"
          :label="$t('Country')"
        >
        </el-table-column>
        <el-table-column
          prop="place_street"
          align="center"
          :label="$t('Street')"
        >
        </el-table-column>
        <el-table-column
          prop="place_house_number"
          align="center"
          :label="$t('DoorNo')"
          width="150"
        >
        </el-table-column>
        <el-table-column
          prop="place_post_code"
          align="center"
          :label="$t('ZipCode')"
        >
        </el-table-column>
        <el-table-column prop="place_city" align="center" :label="$t('City')">
        </el-table-column>
        <el-table-column
          prop="place_address"
          align="center"
          :label="$t('Address')"
        >
        </el-table-column>
      </el-table>
      <base-pagination
        :pageParams="pageParams"
        @search="getOrder"
      ></base-pagination>
    </el-dialog>
    <!-- 地图 -->
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
import BasePagination from "@/components/BasePagination/BasePagination";
import googleMap from "@/components/Map/GoogleMap";
import BMap from "@/components/BaiduMap/getAddressByMap";
export default {
  components: {
    BasePagination,
    googleMap,
    BMap,
  },
  data() {
    return {
      show_receiver_address: false, //弹出框
      select_map: false, //地图
      show: false, //判断测试结果
      showPlaceCountry: false, //判断发国家显隐
      showSecondCountry: false, //判断收国家显隐
      type: 1, //判断发 收人
      countryList: [], //国家下拉
      tableData: [], //列表
      folwData: [], //流程列表
      limitTimePik: [], //寄件人限制时间
      limitTimePei: [], //收件人限制时间
      lon: "", // 经度
      lat: "", // 纬度

      form: {
        // 发货人
        place_fullname: "",
        place_phone: "",
        place_country: "",
        place_post_code: "",
        place_house_number: "",
        place_city: "",
        place_street: "",
        place_address: "",
        place_note_address: "",
        place_lat: "",
        place_lon: "",
        execution_date: "",
        // 收货人
        second_place_fullname: "",
        second_place_phone: "",
        second_place_country: "",
        second_place_post_code: "",
        second_place_house_number: "",
        second_place_city: "",
        second_place_street: "",
        second_place_address: "",
        second_place_note_address: "",
        second_place_lat: "",
        second_place_lon: "",
        second_execution_date: "",
      },
      pageParams: {
        place_post_code: "",
        type: "",
        keyword: "",
        total: 0,
        page: 1,
        per_page: 10,
      },
      //   获取模板
      template_id: localStorage.getItem("address_template_id") || 1,
      country:
        localStorage.getItem("country") &&
        JSON.parse(localStorage.getItem("country")).en,
      ReceiverMapDialog: false,
      // 日期
      // pickerOptions: {
      //   disabledDate(time) {
      //     return time.getTime() < Date.now();
      //   },
      // },
    };
  },
  computed: {
    mapType() {
      return this.$store.state.globalData.maptype;
    },
    // 寄件人限制不可选日期
    disabledDateOptionsPik() {
      return {
        disabledDate: (date) => {
          const limit = this.limitTimePik;
          // 将时间戳转换成年月日
          function add0(m) {
            return m < 10 ? `0${m}` : m;
          }
          // 时间戳转化成时间格式
          function timeFormat(timestamp) {
            // timestamp是整数，否则要parseInt转换,不会出现少个0的情况
            const time = new Date(timestamp);
            const year = time.getFullYear();
            const month = time.getMonth() + 1;
            const day = time.getDate();
            return `${year}-${add0(month)}-${add0(day)}`;
          }
          const result = timeFormat(date);
          if (limit.includes(`${result}`)) {
            return false;
          }
          return true;
        },
      };
    },
    // 收件人限制不可选日期
    disabledDateOptionsPie() {
      return {
        disabledDate: (date) => {
          const limit = this.limitTimePei;
          // 将时间戳转换成年月日
          function add0(m) {
            return m < 10 ? `0${m}` : m;
          }
          // 时间戳转化成时间格式
          function timeFormat(timestamp) {
            // timestamp是整数，否则要parseInt转换,不会出现少个0的情况
            const time = new Date(timestamp);
            const year = time.getFullYear();
            const month = time.getMonth() + 1;
            const day = time.getDate();
            return `${year}-${add0(month)}-${add0(day)}`;
          }
          const result = timeFormat(date);
          if (limit.includes(`${result}`)) {
            return false;
          }
          return true;
        },
      };
    },
    formRules() {
      return {
        place_fullname: [
          //姓名
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        place_post_code: [
          //邮编
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        place_house_number: [
          //门牌号
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        place_city: [
          //城市
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        place_street: [
          //街道
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        place_country: [
          //国家
          { required: true, trigger: "blur", message: " " },
        ],
        execution_date: [
          //日期
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        second_place_fullname: [
          //姓名
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        second_place_post_code: [
          //邮编
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        second_place_house_number: [
          //门牌号
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        second_place_city: [
          //城市
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        second_place_street: [
          //街道
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        second_place_country: [
          //国家
          { required: true, trigger: "blur", message: " " },
        ],
        second_execution_date: [
          //日期
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
      };
    },
  },
  created() {
    this.getAddedCountry();
  },
  methods: {
    // 判断国家 禁用
    disabledBtnByCountry(el) {
      if (el == 2) {
        return this.form.second_place_country === "NL";
      } else {
        return this.form.place_country === "NL";
      }
    },
    getAddedCountry() {
      // 国家列表
      this.$api.getAddedCountry().then((res) => {
        this.countryList = res.data;
      });
    },
    // 改变国家
    changeCountry(el) {
      if (el == 1) {
        if (this.form.place_country != "" || this.form.place_country != null) {
          this.showPlaceCountry = false;
        } else {
          this.showPlaceCountry = true;
        }
        this.form.place_post_code = "";
        this.form.place_house_number = "";
        this.form.place_city = "";
        this.form.place_street = "";
        this.form.place_lat = "";
        this.form.place_lon = "";
      } else {
        if (
          this.form.second_place_country != "" ||
          this.form.second_place_country != null
        ) {
          this.showSecondCountry = false;
        } else {
          this.showSecondCountry = true;
        }
        this.form.second_place_post_code = "";
        this.form.second_place_house_number = "";
        this.form.second_place_city = "";
        this.form.second_place_street = "";
        this.form.second_place_lat = "";
        this.form.second_place_lon = "";
      }
    },
    setSender() {
      this.type = 1;
      this.pageParams.type = 1;
      this.show_receiver_address = true;
      this.getOrder();
    },
    setReceiver() {
      this.type = 2;
      this.pageParams.type = 2;
      this.show_receiver_address = true;
      this.getOrder();
    },
    // 输入邮编 门牌号 拉取城市
    getLocation(type) {
      // 拉取经纬度
      let post = null;
      let house = null;
      let city = null;
      let street = null;
      let country = null;
      if (type === 1) {
        post = this.form.place_post_code;
        house = this.form.place_house_number;
        city = this.form.place_city;
        street = this.form.place_street;
        country = this.form.place_country;
        // 判断国家显隐
        if (country == "" || country == null) {
          this.showPlaceCountry = true;
        } else {
          this.showPlaceCountry = false;
        }
      } else {
        post = this.form.second_place_post_code;
        house = this.form.second_place_house_number;
        city = this.form.second_place_city;
        street = this.form.second_place_street;
        country = this.form.second_place_country;
        // 判断国家显隐
        if (country == "" || country == null) {
          this.showSecondCountry = true;
        } else {
          this.showSecondCountry = false;
        }
      }
      if (post && house && country === "NL") {
        this.$api
          .getLocation({
            post_code: post,
            house_number: house,
            country: country,
          })
          .then((res) => {
            if (type === 1) {
              this.type = 1;
              this.form.place_city = res.data.city;
              this.form.place_street = res.data.street;
              this.form.place_lat = res.data.lat;
              this.form.place_lon = res.data.lon;
              this.getOrderDate();
            } else {
              this.type = 2;
              this.form.second_place_city = res.data.city;
              this.form.second_place_street = res.data.street;
              this.form.second_place_lat = res.data.lat;
              this.form.second_place_lon = res.data.lon;
              this.getOrderDate();
            }
          });
      } else if (post && house && city && street && country) {
        this.$api
          .getLocation({
            post_code: post,
            house_number: house,
            city: city,
            street: street,
            country: country,
          })
          .then((res) => {
            if (type === 1) {
              this.type = 1;
              this.form.place_city = res.data.city;
              this.form.place_street = res.data.street;
              this.form.place_lat = res.data.lat;
              this.form.place_lon = res.data.lon;
              this.getOrderDate();
            } else {
              this.type = 2;
              this.form.second_place_city = res.data.city;
              this.form.second_place_street = res.data.street;
              this.form.second_place_lat = res.data.lat;
              this.form.second_place_lon = res.data.lon;
              this.getOrderDate();
            }
          });
      }
    },
    // 列表
    getOrder() {
      // 收件人
      this.$api.getReceiverAddress(this.pageParams).then((res) => {
        this.tableData = res.data.data;
        this.pageParams.total = res.data.meta.total;
      });
    },
    // 测试流程
    getTestingProcess() {
      if (this.form.place_country == "") {
        this.showPlaceCountry = true;
      } else {
        this.showPlaceCountry = false;
      }

      if (this.form.second_place_country == "") {
        this.showSecondCountry = true;
      } else {
        this.showSecondCountry = false;
      }
      this.$refs.form.validate((valid) => {
        if (valid) {
          this.$api.getTestingProcess(this.form).then((res) => {
            if (res.code == 200) {
              this.show = true;
              this.folwData = res.data;
            }
          });
        }
      });
    },
    // 地址库选取联系人
    getTableItem(row) {
      // 发件人
      if (this.type == 1) {
        this.form.place_fullname = row.place_fullname;
        this.form.place_phone = row.place_phone;
        this.form.place_country = row.short;
        this.form.place_post_code = row.place_post_code;
        this.form.place_house_number = row.place_house_number;
        this.form.place_city = row.place_city;
        this.form.place_street = row.place_street;
        this.form.place_address = row.place_address;
        this.form.place_lat = row.place_lat;
        this.form.place_lon = row.place_lon;
        this.show_receiver_address = false;
        this.getOrderDate();
      } else {
        this.form.second_place_fullname = row.place_fullname;
        this.form.second_place_phone = row.place_phone;
        this.form.second_place_country = row.short;
        this.form.second_place_post_code = row.place_post_code;
        this.form.second_place_house_number = row.place_house_number;
        this.form.second_place_city = row.place_city;
        this.form.second_place_street = row.place_street;
        this.form.second_place_address = row.place_address;
        this.form.second_place_lat = row.place_lat;
        this.form.second_place_lon = row.place_lon;
        this.show_receiver_address = false;
        this.getOrderDate();
      }
    },
    // 拉取时间
    getOrderDate() {
      let data = {};
      if (this.type == 1) {
        data = {
          type: 1,
          place_post_code: this.form.place_post_code,
          place_house_number: this.form.place_house_number,
          place_city: this.form.place_city,
          place_street: this.form.place_street,
          place_country: this.form.place_country,
          place_lat: this.form.place_lat,
          place_lon: this.form.place_lon,
        };
      } else {
        data = {
          type: 2,
          place_post_code: this.form.second_place_post_code,
          place_house_number: this.form.second_place_house_number,
          place_city: this.form.second_place_city,
          place_street: this.form.second_place_street,
          place_country: this.form.second_place_country,
          place_lat: this.form.second_place_lat,
          place_lon: this.form.second_place_lon,
        };
      }
      this.$api.getOrderDate(data).then((res) => {
        var data = res.data;
        if (this.type == 1) {
          this.form.execution_date = data[0];
          this.limitTimePik = data;
        } else {
          this.form.second_execution_date = data[0];
          this.limitTimePei = data;
        }
      });
    },

    // 获取地图的定位信息
    handlerAddressSelected(data) {
      if (this.ReceiverMapDialog) {
        this.form.place_address = data.address;
        this.form.place_post_code = data.postal_code;
        this.form.place_house_number = data.street_number;
        this.form.place_city = data.city;
        this.form.place_country = data.country;
        this.form.place_street = `${data.route}`;
        this.form.place_lat = data.location.lat;
        this.form.place_lon = data.location.lng;
        // this.lon = data.location.lng;
        // this.lat = data.location.lat;
      } else {
        this.form.second_place_address = data.address;
        this.form.second_place_post_code = data.postal_code;
        this.form.second_place_house_number = data.street_number;
        this.form.second_place_city = data.city;
        this.form.second_place_country = data.country;
        this.form.second_place_street = `${data.route}`;
        this.form.second_place_lat = data.location.lat;
        this.form.second_place_lon = data.location.lng;
      }
      this.select_map = false;
    },

    selectReceiverMap() {
      this.select_map = true;
      this.ReceiverMapDialog = true;
    },

    selectSenderMap() {
      this.select_map = true;
      this.ReceiverMapDialog = false;
    },
  },
};
</script>

<style lang="less" scoped>
@import "../../../less/public_variable.less";

.testing-process {
  .box-card {
    margin: 20px;
    height: 150px;
    .img {
      width: 60px;
      margin-left: 30px;
      margin-top: 20px;
    }
    .headline {
      font-size: 17px;
      font-weight: 500;
      margin: 20px 0;
    }
  }
  .card-but {
    width: 97%;
    margin: 20px;
    margin-top: 10px;
    color: white;
    background-color: #72bc99;
    height: 50px;
  }
  .box-bottom {
    margin: 20px;
    min-height: 300px;
    .box {
      margin: 10px;
      .box-img {
        // width: 140px;

        height: 55px;
        // text-align: center;
        .box-pick {
          position: absolute;
          color: #aaaaaa;
          font-size: 10px;
          left: 80%;
        }
        .box-title {
          position: absolute;
          width: 50px;
          top: 40px;
          left: 52%;
          color: #aaaaaa;
          font-size: 10px;
        }
        .box-pie {
          position: absolute;
          color: #aaaaaa;
          font-size: 10px;
          left: 52%;
          bottom: 60%;
        }
        .box-arrow {
          margin-top: 10px;
        }
      }

      .status-name {
        width: 100px;
        margin-top: 15px;
        margin-left: -10px;
        text-align: center;
      }
      .name {
        width: 100px;
        margin-left: -10px;
        margin-top: 10px;
        text-align: center;
      }
    }
  }
  .card {
    margin: 20px;
    .box-title {
      color: #a1a1a1;
    }

    .add-row {
      text-align: right;
      margin-bottom: 10px;
      .el-button {
        border: 1px #28d094 solid;
        border-color: #28d094;
        color: @ThemeColor;
        padding: 7px 9px !important;
        background: #fff !important;
        &:hover {
          background-color: @ThemeColor !important;
          color: #fff;
        }
      }
    }
    .address-table {
      float: right;
    }
    .shipper {
      border: 1px solid #e4ebf1;
      height: 320px;
      .shipper-box {
        height: 320px;
        text-align: center;
        background-color: #f3f2f2;
        .shipper-img {
          margin-top: 100px;
        }
        .shipper-title {
          margin-top: 10px;
        }
      }
      .shipper-form {
        margin-left: -20px;
        margin-top: 10px;
        .locale-btn {
          color: #f6a50d !important;
          background-color: #fff !important;
          border-left: 1px solid #d6d7d6;
        }
      }
      .item-style {
        display: inline-block;
      }
      .item-left {
        width: 50%;
      }
      .item-right {
        width: 50%;
        .el-input {
          width: 100%;
          min-width: 100px;
        }
      }
      .item-font {
        width: 49%;
      }
      /deep/ .el-input__inner {
        border: 0;
        border-bottom: 1px solid #d6d7d6;
      }
    }
  }
}
</style>
