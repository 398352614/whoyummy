<template>
  <!-- 订单导入 -->
  <div class="upload">
    <div class="domestic-batch-uploading" style="min-height: auto">
      <div class="model-download">
        <div class="batch-icon">1</div>
        <div class="model-text">
          <div class="model-title">{{ $t("DownloadTemplate") }}</div>
          <div class="model-content">
            {{ $t("DownloadTemplateSate") }}
          </div>
        </div>
      </div>
      <div class="domestic-batch-uploading-model">
        <div class="model-content">{{ $t("GenericTemplate") }}</div>
        <div class="model-div" @click="getOrderTemplate">
          <div class="model-div-icon">
            <img
              src="../../assets/img/configuration-img/excel.png"
              style="width: 30px"
            />
          </div>
          <div class="model-div-content">
            {{ $t("standardTemplate") }}
          </div>
        </div>
        <div class="update-time">
          {{ $t("templateUpdatedTime") }}:
          <span>{{ " 2021-05-29" }}</span>
        </div>
      </div>
    </div>
    <div class="domestic-batch-uploading">
      <div class="model-uploading">
        <div class="model-download">
          <div class="batch-icon">2</div>
          <div class="uploading-buttton">
            <el-upload
              class="upload-demo"
              :action="url"
              :show-file-list="false"
              :on-success="uploadExcelSuccess"
              :on-error="uploadError"
              :on-progress="uploadProgress"
              :headers="Authorization"
            >
              <el-button icon="el-icon-upload2" style="min-width: 150px">{{
                $t("uploadedTemplate")
              }}</el-button>
            </el-upload>
          </div>
          <div class="uploading-plan">
            <div class="uploading-img">
              <img
                src="../../assets/img/configuration-img/excel.png"
                style="width: 30px"
              />
            </div>
            <p>{{ excel_name }}</p>
            <div class="progress" v-show="progress">
              <el-progress :percentage="progress"></el-progress>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-if="is_started">
      <!-- 更换版 -->
      <el-form>
        <el-table
          max-height="600px"
          :data="tableData"
          v-loading="global_loading()"
          element-loading-text="loading..."
          border
          style="width: 93%; margin: 20px auto"
          :header-cell-style="{ background: '#25ad7a', color: '#fff' }"
        >
          <el-table-column type="index" width="50" align="center">
            <template slot="header">
              <span>{{ $t("Number") }}</span>
            </template>
            <template slot-scope="scope">
              <span>{{ scope.$index + 1 }}</span>

              <span v-if="checkModle[scope.$index].log != ''">
                <el-popover
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].log"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </span>
            </template>
          </el-table-column>
          <el-table-column
            prop="date"
            :label="$t('Operation')"
            width="50"
            align="center"
          >
            <template slot-scope="scope">
              <el-tooltip effect="dark" placement="top">
                <div slot="content">{{ $t("Delete") }}</div>
                <i
                  class="el-icon-circle-close"
                  @click="delItem(scope.$index)"
                ></i>
              </el-tooltip>
            </template>
          </el-table-column>

          <el-table-column
            v-for="item in tableHead"
            :key="item.value"
            :prop="item.value"
            :label="item.name"
            align="center"
            width="150"
          >
            <template slot-scope="scope">
              <!-- :error="checkModle[scope.$index].create_date" -->
              <!-- 类型 -->

              <el-form-item v-if="item.value === 'type'">
                <el-select v-model="scope.row.type">
                  <el-option
                    v-for="item in orderTypeList"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                  ></el-option>
                </el-select>

                <el-popover
                  v-if="checkModle[scope.$index].type"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].type"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>
              <!-- 货主 -->

              <el-form-item v-else-if="item.value === 'merchant'">
                <el-select v-model="scope.row.merchant_id">
                  <el-option
                    v-for="item in merchantList"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                  ></el-option>
                </el-select>

                <el-popover
                  v-if="checkModle[scope.$index].merchant_id"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].merchant_id"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>
              <!-- 发国家 -->
              <el-form-item v-else-if="item.value === 'place_country_name'">
                <el-select
                  v-model="scope.row.place_country"
                  @change="changeCountry(1, scope.row)"
                >
                  <el-option
                    v-for="item in countryList"
                    :key="item.short"
                    :label="item.name"
                    :value="item.short"
                  ></el-option>
                </el-select>

                <el-popover
                  v-if="checkModle[scope.$index].place_country"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].place_country"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>
              <!-- 收国家 -->
              <el-form-item
                v-else-if="item.value === 'second_place_country_name'"
              >
                <el-select
                  v-model="scope.row.second_place_country"
                  @change="changeCountry(2, scope.row)"
                >
                  <el-option
                    v-for="item in countryList"
                    :key="item.short"
                    :label="item.name"
                    :value="item.short"
                  ></el-option>
                </el-select>

                <el-popover
                  v-if="checkModle[scope.$index].second_place_country"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].second_place_country"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>
              <!-- 包裹 -->
              <el-form-item v-else-if="item.value === 'package_feature_1'">
                <el-select v-model="scope.row.package_feature_1">
                  <el-option
                    v-for="item in packageFeatureList"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                  ></el-option>
                </el-select>

                <el-popover
                  v-if="checkModle[scope.$index].package_feature_1"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].package_feature_1"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>

              <el-form-item v-else-if="item.value === 'package_feature_2'">
                <el-select v-model="scope.row.package_feature_2">
                  <el-option
                    v-for="item in packageFeatureList"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                  ></el-option>
                </el-select>

                <el-popover
                  v-if="checkModle[scope.$index].package_feature_2"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].package_feature_2"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>

              <el-form-item v-else-if="item.value === 'package_feature_3'">
                <el-select v-model="scope.row.package_feature_3">
                  <el-option
                    v-for="item in packageFeatureList"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                  ></el-option>
                </el-select>

                <el-popover
                  v-if="checkModle[scope.$index].package_feature_3"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].package_feature_3"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>

              <el-form-item v-else-if="item.value === 'material_pack_type_4'">
                <el-select v-model="scope.row.material_pack_type_4">
                  <el-option
                    v-for="item in packageFeatureList"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                  ></el-option>
                </el-select>

                <el-popover
                  v-if="checkModle[scope.$index].material_pack_type_4"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].material_pack_type_4"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>

              <el-form-item v-else-if="item.value === 'material_pack_type_5'">
                <el-select v-model="scope.row.material_pack_type_5">
                  <el-option
                    v-for="item in packageFeatureList"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                  ></el-option>
                </el-select>

                <el-popover
                  v-if="checkModle[scope.$index].material_pack_type_5"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].material_pack_type_5"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>
              <!-- 包装 -->
              <el-form-item v-else-if="item.value === 'material_pack_type_1'">
                <el-select v-model="scope.row.material_pack_type_1">
                  <el-option
                    v-for="item in materialPackTypeList"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                  ></el-option>
                </el-select>

                <el-popover
                  v-if="checkModle[scope.$index].material_pack_type_1"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].material_pack_type_1"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>

              <el-form-item v-else-if="item.value === 'material_pack_type_2'">
                <el-select v-model="scope.row.material_pack_type_2">
                  <el-option
                    v-for="item in materialPackTypeList"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                  ></el-option>
                </el-select>

                <el-popover
                  v-if="checkModle[scope.$index].material_pack_type_2"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].material_pack_type_2"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>

              <el-form-item v-else-if="item.value === 'material_pack_type_3'">
                <el-select v-model="scope.row.material_pack_type_3">
                  <el-option
                    v-for="item in materialPackTypeList"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                  ></el-option>
                </el-select>

                <el-popover
                  v-if="checkModle[scope.$index].material_pack_type_3"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].material_pack_type_3"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>

              <el-form-item v-else-if="item.value === 'package_feature_4'">
                <el-select v-model="scope.row.package_feature_1">
                  <el-option
                    v-for="item in materialPackTypeList"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                  ></el-option>
                </el-select>

                <el-popover
                  v-if="checkModle[scope.$index].package_feature_4"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].package_feature_4"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>

              <el-form-item v-else-if="item.value === 'package_feature_5'">
                <el-select v-model="scope.row.package_feature_5">
                  <el-option
                    v-for="item in materialPackTypeList"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                  ></el-option>
                </el-select>

                <el-popover
                  v-if="checkModle[scope.$index].package_feature_5"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].package_feature_5"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>
              <!-- 类型 -->
              <el-form-item v-else-if="item.value === 'material_type_1'">
                <el-select v-model="scope.row.material_type_1">
                  <el-option
                    v-for="item in materialTypeList"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                  ></el-option>
                </el-select>

                <el-popover
                  v-if="checkModle[scope.$index].material_type_1"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].material_type_1"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>

              <el-form-item v-else-if="item.value === 'material_type_2'">
                <el-select v-model="scope.row.material_type_2">
                  <el-option
                    v-for="item in materialTypeList"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                  ></el-option>
                </el-select>

                <el-popover
                  v-if="checkModle[scope.$index].material_type_2"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].material_type_2"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>

              <el-form-item v-else-if="item.value === 'material_type_3'">
                <el-select v-model="scope.row.material_type_3">
                  <el-option
                    v-for="item in materialTypeList"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                  ></el-option>
                </el-select>

                <el-popover
                  v-if="checkModle[scope.$index].material_type_3"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].material_type_3"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>

              <el-form-item v-else-if="item.value === 'material_type_4'">
                <el-select v-model="scope.row.material_type_4">
                  <el-option
                    v-for="item in materialTypeList"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                  ></el-option>
                </el-select>

                <el-popover
                  v-if="checkModle[scope.$index].material_type_4"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].material_type_4"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>

              <el-form-item v-else-if="item.value === 'material_type_5'">
                <el-select v-model="scope.row.material_type_5">
                  <el-option
                    v-for="item in materialTypeList"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                  ></el-option>
                </el-select>

                <el-popover
                  v-if="checkModle[scope.$index].material_type_5"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].material_type_5"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>
              <!-- 原单返回 -->
              <el-form-item v-else-if="item.value === 'receipt_type'">
                <el-select v-model="scope.row.receipt_type">
                  <el-option
                    v-for="item in orderReceiptTypeList"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                  ></el-option>
                </el-select>

                <el-popover
                  v-if="checkModle[scope.$index].receipt_type"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].receipt_type"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>
              <!-- 控货方式 -->
              <el-form-item v-else-if="item.value === 'control_mode'">
                <el-select v-model="scope.row.control_mode">
                  <el-option
                    v-for="item in orderControlModeList"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                  ></el-option>
                </el-select>

                <el-popover
                  v-if="checkModle[scope.$index].control_mode"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].control_mode"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>
              <!-- 普通输入框  -->
              <el-form-item v-else>
                <el-input
                  v-model="scope.row[item.value]"
                  @input="getChange"
                  :disabled="getdisabled(scope.row, item.value)"
                ></el-input>

                <el-popover
                  v-if="checkModle[scope.$index][item.value]"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index][item.value]"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>
            </template>
          </el-table-column>
        </el-table>
      </el-form>
    </div>
    <div class="uploading-save">
      <div class="model-download">
        <div class="batch-icon">3</div>
        <el-button
          style="width: auto"
          @click="ImportOrderCheck"
          class="but"
          type="primary"
          :disabled="!is_started"
          >{{ $t("anExamination") }}</el-button
        >
      </div>
    </div>
    <div>
      <div class="uploading-save">
        <div class="model-download">
          <div class="batch-icon">4</div>
          <el-button
            style="width: auto"
            @click="Submit"
            class="but"
            :disabled="is_no_problem"
            >{{ $t("Submit") }}</el-button
          >
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import baseApi from "@/api/axios/base_api";
import loading from "@/mixin/global_loading";
import EditableDiv from "@/page/companyManagement/components/EditableDiv.vue";

export default {
  name: "orderUpload",
  mixins: [loading],
  components: {
    EditableDiv,
  },
  data() {
    return {
      tableHead: [],
      headName: "",
      tableData: [],
      checkModle: [],
      errorList: [],
      orderTypeList: [], //订单MerchantType
      orderSettlementTypeList: [], //结算方式
      merchantList: [], //货主
      packageFeatureList: [], //nature
      materialTypeList: [], //MerchantType
      materialPackTypeList: [], //cargoPackage
      orderControlModeList: [], //控货方式
      orderReceiptTypeList: [], //回单要求
      countryList: [], //国家下拉
      error: null,
      status: null,
      merchant_id: "",
      currency_unit_symbol: "", // unitPrice单位
      weight_unit_symbol: "", // 重量单位
      volume_unit_symbol: "", // Volume单位
      url: baseApi + "admin/order-import", //上传地址
      excel_name: this.$t("orderForm"),
      status: "待检查",
      progress: 0, // 上传execel的进度
      countryPlace: false,
      countrySecond: false,
      is_started: false,
      loading: null,
      is_no_problem: true,
      country:
        localStorage.getItem("country") &&
        JSON.parse(localStorage.getItem("country")).en,
    };
  },
  computed: {
    Authorization() {
      return {
        Authorization: this.$store.state.token.token,
        language: localStorage.getItem("lang") || "cn",
      };
    },
  },
  created() {
    this.getMerchantsList();
    this.getDictionary();
    this.getAdvanceSetting();
    this.getAddedCountry();
  },
  methods: {
    // 禁用
    getdisabled(row, name) {
      if (row.place_country == "NL") {
        if (name == "place_city" || name == "place_street") {
          return true;
        } else {
          if (row.second_place_country == "NL") {
            if (name == "second_place_city" || name == "second_place_street") {
              return true;
            }
          } else {
            return false;
          }
        }
      } else {
        if (row.second_place_country == "NL") {
          if (name == "second_place_city" || name == "second_place_street") {
            return true;
          }
        } else {
          return false;
        }
      }
    },
    // 改变国家
    changeCountry(el, item) {
      console.log("el,item");
      console.log(el, item);
      if (el == 1) {
        item.place_country_name = item.place_country;
        item.place_post_code = "";
        item.place_house_number = "";
        item.place_city = "";
        item.place_street = "";
        item.place_lat = "";
        item.place_lon = "";
      } else {
        item.second_place_post_code = "";
        item.second_place_house_number = "";
        item.second_place_city = "";
        item.second_place_street = "";
        item.second_place_lat = "";
        item.second_place_lon = "";
      }
    },
    // // 发件人城市，街道禁用
    // disabledBtnByCountry(item, index) {
    //   if (item.place_country === "NL") {
    //     this.tableData[index].countryPlace = true;
    //   } else {
    //     this.tableData[index].countryPlace = false;
    //   }
    // },
    // // 收件人城市，街道禁用
    // disabledBtnBySecondCountry(item, index) {
    //   if (this.tableData[index].second_place_country === "NL") {
    //     this.tableData[index].countrySecond = true;
    //   } else {
    //     this.tableData[index].countrySecond = false;
    //   }
    // },
    ImportOrder() {
      this.$api.ImportOrder().then();
    },
    // 获取单位值
    getAdvanceSetting() {
      this.$api.getAdvanceSetting().then((res) => {
        this.volume_unit_symbol = res.data.volume_unit_symbol; // Volume单位
        this.weight_unit_symbol = res.data.weight_unit_symbol; // 重量单位
        this.currency_unit_symbol = res.data.currency_unit_symbol; // unitPrice单位
      });
    },
    getDictionary() {
      this.$api.getDictionary().then((res) => {
        // 取派MerchantType
        let orderTypeList = res.data.orderTypeList;
        orderTypeList.map((item) => {
          let obj = {
            id: item.id.toString(),
            name: item.name,
          };
          this.orderTypeList.push(obj);
        });
        // nature
        let packageFeatureList = res.data.packageFeatureList;
        packageFeatureList.map((item) => {
          let obj = {
            id: item.id.toString(),
            name: item.name,
          };
          this.packageFeatureList.push(obj);
        });
        // 货物MerchantType
        let materialTypeList = res.data.materialTypeList;
        materialTypeList.map((item) => {
          let obj = {
            id: item.id.toString(),
            name: item.name,
          };
          this.materialTypeList.push(obj);
        });
        // 货物cargoPackage
        let materialPackTypeList = res.data.materialPackTypeList;
        materialPackTypeList.map((item) => {
          let obj = {
            id: item.id.toString(),
            name: item.name,
          };
          this.materialPackTypeList.push(obj);
        });
        // 结算方式
        let orderSettlementTypeList = res.data.orderSettlementTypeList;
        orderSettlementTypeList.map((item) => {
          let obj = {
            id: item.id.toString(),
            name: item.name,
          };
          this.orderSettlementTypeList.push(obj);
        });
        // 控货方式
        let orderControlModeList = res.data.orderControlModeList;
        orderControlModeList.map((item) => {
          let obj = {
            id: item.id.toString(),
            name: item.name,
          };
          this.orderControlModeList.push(obj);
        });
        // 回单要求
        let orderReceiptTypeList = res.data.orderReceiptTypeList;
        orderReceiptTypeList.map((item) => {
          let obj = {
            id: item.id.toString(),
            name: item.name,
          };
          this.orderReceiptTypeList.push(obj);
        });
      });
    },
    // 国家
    getAddedCountry() {
      // 国家列表
      this.$api.getAddedCountry().then((res) => {
        this.countryList = res.data;
      });
    },
    getChange(item) {
      var status = "待检查";
      this.status = status;
    },
    // 货主信息
    getMerchantsList() {
      this.$api.getMerchantsPublic().then((res) => {
        let merchantList = res.data.data;
        merchantList.map((item) => {
          let merchantObj = {
            id: item.id.toString(),
            name: item.name,
          };
          this.merchantList.push(merchantObj);
        });
      });
    },
    // 获得模板 下载模板
    getOrderTemplate() {
      this.$api.getOrderTemplate().then((res) => {
        const link = document.createElement("a");
        let fname = "templateTips.docx";
        link.href = res.data.path;
        link.setAttribute("download", fname);
        document.body.appendChild(link);
        link.click();
      });
    },
    uploadExcelSuccess(res, file, fileList) {
      if (res.code == 200) {
        this.is_started = true;
        this.tableData = [];
        // this.tableData = this.tableData.concat(res.data).splice(0, 1);
        let headArr = [];
        let obj = {};
        let obj2 = {};
        headArr.push(res.data[0]);
        headArr.map((item) => {
          let head = [];
          let headArr = [];
          for (let el in item) {
            if (Number(el)) {
              obj2 = {
                value: el,
                name: item[el],
              };
              headArr.push(obj2);
            } else {
              obj = {
                value: el,
                name: item[el],
              };
              head.push(obj);
            }
          }
          head.push.apply(head, headArr); //合并两个数组
          this.tableHead = head;
        });
        let arr = res.data;
        this.tableData = arr.splice(1);
        // this.tableHead=this.
        // let tableData = res.data;
        // tableData.map((item, index) => {
        //   // this.disabledBtnByCountry(item, index);
        //   // this.disabledBtnBySecondCountry(item, index);
        //   // this.tableData[index].type = Number(item.type);
        // });
        this.checkModle = [];
        for (let i = 0; i < this.tableData.length; i++) {
          const obj = {};
          for (let item in this.tableData[i]) {
            obj[item] = null;
          }
          this.checkModle.push({ ...obj, log: "" });
        }

        // this.loading.close();
        this.is_started = true;
        this.$notify({
          title: this.$t("uploadedSuccessfully"),
          // message: res.msg,
          type: "success",
        });
        this.progress = 0;
        return;
      } else {
        this.uploadError(res);
      }
    },
    // 上传Excel进度条
    uploadProgress(event, file) {
      this.excel_name = file.name;
      this.progress = +event.percent.toFixed(2);

      console.log("event, file");
      console.log(event, file);
    },
    // 上传失败
    uploadError(res) {
      this.excel_name = this.$t("PleaseReUpload");
      this.resetDate();
      this.$notify({
        title: this.$t("uploadFailed"),
        message: res.msg,
        type: "error",
      });
    },
    // 重新上传,重置数据
    resetDate() {
      this.tableData = [];
      this.progress = 0;
    },
    // 删除表单项
    delItem(index) {
      console.log("index");
      console.log(index);
      let checkModle = this.checkModle;
      checkModle.map((item, l) => {
        if (l == index) {
          this.checkModle.splice(l, 1);
          this.tableData.splice(index, 1);
        }
      });
    },
    // 检查
    ImportOrderCheck() {
      console.log("this.tableData34124144444");
      console.log(this.tableData);
      let list = JSON.stringify(this.tableData);
      this.$api.ImportOrderCheck({ list: list }).then((res) => {
        console.log(res);
        let is_log = false;
        this.errorList = [];
        let tableData = [];
        res.data.map((item, index) => {
          let obj = item.error;
          this.errorList.push(obj);
          console.log("this.errorList");
          console.log(this.errorList);
          if (item.status === 2) {
            is_log = true;
            this.status = this.$t("failure");
          } else {
            this.status = this.$t("success");
          }
          if (this.country == "NL") {
            tableData.push(item.data);
            this.tableData = tableData;
          } else {
            tableData.push(item.data);
            this.tableData = tableData;
          }
        });
        this.errorList.forEach((ele, index) => {
          for (const item in this.checkModle[index]) {
            if (ele[item] === undefined) {
              this.checkModle[index][item] = "";
            } else {
              this.checkModle[index][item] = ele[item];
            }
          }
        });
        this.is_no_problem = is_log;
        if (is_log) {
          this.$message({
            type: "error",
            message: this.$t("failure"),
          });
        } else {
          this.$message({
            type: "success",
            message: this.$t("success"),
          });
        }
      });
    },
    // 提交
    Submit() {
      let list = JSON.stringify(this.tableData);
      this.$api
        .submitOrderTemplate({
          list: list,
        })
        .then((res) => {
          if (res.code === 200) {
            // this.$message({
            //   type: "success",
            //   message: res.msg,
            // });
            this.tableData = [];
            this.$router.push({
              name: "OrderList",
              params: {
                orderManagement: 1,
              },
            });
            this.excel_name = this.$t("orderForm");
            this.is_started = false;
          } else {
            // this.$message({
            //   type: "error",
            //   message: res.msg,
            // });
          }
        });
    },
  },
};
</script>

<style lang="less" scoped>
.upload {
  width: 95%;
  margin: 20px auto;
  // text-align: center;
}
.batch-icon {
  width: 30px;
  height: 30px;
  line-height: 30px;
  text-align: center;
  border-radius: 50%;
  border: 1px solid rgb(209, 206, 206);
  background: #fff;
  font-weight: 400;
  color: #010101;
  margin-right: 1rem;
}
.domestic-batch-uploading {
  .model-download {
    display: flex;
    flex-direction: row;
    align-items: center;
    .model-text {
      display: flex;
      flex-direction: row;
      align-items: center;
      .model-title {
        font-size: 1.2rem;
        color: #010101;
        font-weight: 600;
      }
      .model-content {
        line-height: 26px;
        margin-left: 15px;
        color: gray;
      }
    }
  }
  .domestic-batch-uploading-model {
    margin: 20px 50px;
    width: 450px;
    height: 200px;
    border: 1px solid rgb(209, 206, 206);
    background-color: #fff;
    .model-content {
      margin-left: 30px;
      margin-top: 20px;
      font-weight: 500;
    }
    .update-time {
      margin-left: 30px;
      font-size: 13px;
      span {
        color: red;
      }
    }
    .model-div {
      width: 100px;
      position: relative;
      display: flex;
      margin: 30px;
      border: 1px solid #390;
      padding: 10px 20px;
      cursor: pointer;
      .model-div-icon {
        width: 35px;
        text-align: right;
      }
      .model-div-content {
        text-indent: 5px;
        line-height: 30px;
        font-size: 10px;
      }
      &::before {
        content: "";
        position: absolute;
        width: 0;
        height: 0;
        border-width: 10px;
        right: -3px;
        bottom: -4px;
        border-style: solid;
        border-color: #fff #fff #fff #fff;
        transform: rotate(-40deg);
      }
      &:after {
        content: "";
        position: absolute;
        width: 0;
        height: 0;
        border-width: 10px;
        right: 3px;
        bottom: 3px;
        border-style: solid;
        border-color: transparent transparent #390 transparent;
        transform: rotate(-40deg);
      }
    }
    // .model-div:hover {
    //   border-color: #390;

    //   &:after {
    //     border-color: transparent transparent #390 transparent;
    //   }
    // }
  }
  .model-uploading {
    display: flex;
    flex-direction: row;
    align-items: center;
    .uploading-plan {
      margin: 0 40px;
      height: 30px;
      display: flex;
      padding: 5px;
      // background: #fff;

      .uploading-img {
        margin-top: 1px;
      }
      p {
        font-size: 12px;
        color: grey;
        margin-top: 10px;
        margin-left: 5px;
        margin-right: 20px;
      }
      .progress {
        margin-top: 6px;
        margin-right: 5px;
        width: 200px;
      }
    }
  }
}
.uploading-save {
  margin-top: 20px;
  .model-download {
    display: flex;
    flex-direction: row;
    align-items: center;
  }
}
.check-icon {
  color: #d81d06;
  font-size: 15px;
  position: absolute;
  top: 25px;
}
.el-input {
  top: 10px !important;
  width: 90% !important;
}
.el-select {
  top: 10px !important;
  width: 90% !important;
}
.but {
  padding-left: 60px;
  padding-right: 60px;
  background: #83aabf !important;
  border-color: #83aabf !important;
  color: #fff !important;
}
</style>

