<template>
  <!--添加订单 -->
  <div class="order-add-container">
    <el-form
      :model="orderForm"
      ref="orderForm"
      :rules="rules"
      label-width="130px"
      label-position="right"
    >
      <el-card class="box-card">
        <h4 class="box_title">{{ $t("GeneralInfo") }}</h4>
        <div class="general-info">
          <!-- 类型 -->
          <el-form-item :label="$t('OrderType')" prop="type">
            <el-select
              v-model="orderForm.type"
              :disabled="isPickUp"
              size="small"
              :placeholder="$t('OrderType')"
              @change="changeType"
            >
              <el-option
                v-for="item in orderTypeList"
                :key="item.id"
                :label="item.name"
                :value="item.id"
              >
              </el-option>
            </el-select>
          </el-form-item>

          <!-- 所属货主 -->
          <el-form-item :label="$t('Merchant')" prop="merchant_id">
            <el-select
              v-model="orderForm.merchant_id"
              :disabled="isPickUp"
              size="small"
              :placeholder="$t('Merchant')"
              @change="merchant"
            >
              <el-option
                v-for="item in merchantList"
                :key="item.id"
                :label="item.name"
                :value="item.id"
              >
              </el-option>
            </el-select>
          </el-form-item>
          <!-- 开单日期 -->
          <el-form-item :label="$t('billingDate')" prop="create_date">
            <el-date-picker
              value-format="yyyy-MM-dd"
              v-model="orderForm.create_date"
              :disabled="isPickUp"
              type="date"
              size="small"
              :placeholder="$t('PleaseSelectTheBillingDate')"
            >
            </el-date-picker>
          </el-form-item>
          <!-- 外部客户ID  客户订单-->
          <el-form-item :label="$t('customerNumber')">
            <el-input
              v-model="orderForm.out_user_id"
              :disabled="isPickUp"
              size="small"
            ></el-input>
          </el-form-item>
          <!-- 外部订单号 货号 -->
          <el-form-item :label="$t('ExternalOrderNumber')">
            <el-input
              v-model="orderForm.out_order_no"
              :disabled="isPickUp"
              size="small"
            ></el-input>
          </el-form-item>
        </div>
      </el-card>
      <el-card class="box-card">
        <el-row :gutter="20">
          <!-- 发件人 -->
          <el-col :span="12" v-if="orderForm.type != 2">
            <div v-if="orderForm.type == 2" style="width: 100%"></div>
            <h4 class="box_title">
              {{ $t("Sender") }}
              <span class="add-row address-table">
                <el-button
                  @click="setReceiver(orderForm.merchant_id)"
                  :disabled="isPickUp"
                  v-show="orderForm.merchant_id"
                  >{{ $t("AddressBook") }}</el-button
                >
              </span>
            </h4>
            <div class="shipper">
              <el-row>
                <el-col
                  :span="3"
                  style="
                    height: 350px;
                    text-align: center;
                    background-color: #f3f2f2;
                  "
                >
                  <div style="margin-top: 130px">
                    <img
                      src="../../assets/img/loading.png"
                      alt=""
                      width="40px"
                    />
                    <div style="margin-top: 20px">
                      {{ $t("LoadingPlace") }}
                    </div>
                  </div>
                </el-col>
                <el-col :span="20" style="margin-top: 10px; margin-left: -20px">
                  <!-- 姓名 -->
                  <el-form-item
                    :label="$t('Sender')"
                    prop="place_fullname"
                    class="item-style item-left item-bottom"
                  >
                    <el-input v-model="orderForm.place_fullname"></el-input>
                  </el-form-item>
                  <!-- 电话 -->
                  <el-form-item
                    :label="$t('Tel')"
                    prop="place_phone"
                    class="item-style item-font item-bottom"
                  >
                    <el-input v-model="orderForm.place_phone"></el-input>
                  </el-form-item>
                  <!-- 地址 -->
                  <!-- <el-form-item :label="$t('地址')" prop="place_phone">
                    <el-input v-model="orderForm.place_phone"></el-input>
                  </el-form-item> -->

                  <div v-if="template_id == 1">
                    <!-- 国家 -->
                    <el-form-item :label="$t('Country')" prop="place_country">
                      <el-select
                        v-model="orderForm.place_country"
                        :placeholder="$t('pleaseSelectCountry')"
                        size="small"
                        style="width: 100%"
                        @change="changeCountry(2)"
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
                      class="item-style item-left item-bottom"
                    >
                      <el-input
                        v-model="orderForm.place_post_code"
                        @blur="getLocation(2)"
                      >
                      </el-input>
                    </el-form-item>
                    <!-- 门牌号 -->
                    <el-form-item
                      :label="$t('DoorNo')"
                      prop="place_house_number"
                      class="item-style item-font item-bottom"
                    >
                      <el-input
                        v-model="orderForm.place_house_number"
                        @blur="getLocation(2)"
                      ></el-input>
                    </el-form-item>
                    <!-- 城市 -->
                    <el-form-item
                      :label="$t('City')"
                      prop="place_city"
                      class="item-style item-left"
                    >
                      <el-input
                        v-model="orderForm.place_city"
                        @blur="getLocation(2)"
                        :disabled="disabledBtnByCountry(2)"
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
                        v-model="orderForm.place_street"
                        @blur="getLocation(2)"
                        :disabled="disabledBtnByCountry(2)"
                      ></el-input>
                    </el-form-item>
                    <!-- 日期 -->
                    <el-form-item :label="$t('date')" prop="execution_date">
                      <el-date-picker
                        value-format="yyyy-MM-dd"
                        v-model="orderForm.execution_date"
                        :disabled="isPickUp"
                        type="date"
                        :picker-options="disabledDateOptionsPik"
                        :placeholder="$t('PleaseDate')"
                        @change="getDateChange"
                      >
                      </el-date-picker>
                    </el-form-item>
                  </div>
                  <div v-else>
                    <input-search-baidu
                      v-if="mapType === 'baidu'"
                      v-show="false"
                      @result="getResult(2, $event)"
                    ></input-search-baidu>
                    <input-search
                      v-else
                      v-show="false"
                      @result="getResult(2, $event)"
                      dom-id="placeId"
                    ></input-search>
                    <el-form-item :label="$t('Address')" prop="place_address">
                      <el-input
                        id="placeId"
                        v-model="orderForm.place_address"
                        :placeholder="$t('PleaseEnterAddress')"
                      >
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
                        v-model="orderForm.execution_date"
                        :disabled="isPickUp"
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

          <!-- 仓库 -->
          <el-col :span="12" v-if="orderForm.type != 3 && orderForm.type != 4">
            <div v-if="orderForm.type == 2" style="width: 100%"></div>
            <h4 class="box_title">
              {{ $t("Warehouse") }}
            </h4>
            <div class="shipper">
              <el-row>
                <el-col
                  :span="3"
                  style="
                    height: 350px;
                    text-align: center;
                    background-color: #f3f2f2;
                  "
                >
                  <div style="margin-top: 130px">
                    <img src="../../assets/img/warehouse.png" alt="" />
                    <div style="margin-top: 10px">{{ $t("Warehouse") }}</div>
                  </div></el-col
                >
                <el-col :span="20" style="margin-top: 20px">
                  <!-- 姓名 -->
                  <el-form-item :label="$t('Warehouse')" prop="name">
                    <el-input v-model="orderForm.name" disabled></el-input>
                  </el-form-item>
                  <!-- 电话 -->
                  <el-form-item :label="$t('Tel')" prop="phone">
                    <el-input v-model="orderForm.phone" disabled></el-input>
                  </el-form-item>
                  <!-- 地址 -->
                  <!-- <el-form-item :label="$t('地址')" prop="place_phone">
                    <el-input v-model="orderForm.place_phone"></el-input>
                  </el-form-item> -->

                  <div v-if="template_id == 1">
                    <!-- 国家 -->
                    <el-form-item :label="$t('Country')" prop="country">
                      <el-select
                        v-model="orderForm.country"
                        placeholder=""
                        style="width: 100%"
                        disabled
                      >
                        <el-option
                          v-for="item in countryList"
                          :key="item.short"
                          :label="item.name"
                          :value="item.short"
                        >
                        </el-option>
                      </el-select>
                    </el-form-item>
                    <!-- 邮编 -->
                    <el-form-item
                      :label="$t('ZipCode')"
                      prop="post_code"
                      class="item-style item-left item-bottom"
                    >
                      <el-input v-model="orderForm.post_code" disabled>
                      </el-input>
                    </el-form-item>
                    <!-- 门牌号 -->
                    <el-form-item
                      :label="$t('DoorNo')"
                      prop="house_number"
                      class="item-style item-font item-bottom"
                    >
                      <el-input
                        v-model="orderForm.house_number"
                        disabled
                      ></el-input>
                    </el-form-item>
                    <!-- 城市 -->
                    <el-form-item
                      :label="$t('City')"
                      prop="city"
                      class="item-style item-left"
                    >
                      <el-input v-model="orderForm.city" disabled> </el-input>
                    </el-form-item>
                    <!-- 街道 -->
                    <el-form-item
                      :label="$t('Street')"
                      prop="street"
                      class="item-style item-font"
                    >
                      <el-input v-model="orderForm.street" disabled></el-input>
                    </el-form-item>
                    <!-- 日期 -->
                    <!-- <el-form-item :label="$t('日期')" prop="second_execution_date">
                      <el-date-picker
                        value-format="yyyy-MM-dd"
                        :disabled="isPickUp"
                        v-model="orderForm.second_execution_date"
                        type="date"
                        :picker-options="pickerOptions"
                        :placeholder="$t('请选择派件日期')"
                      >
                      </el-date-picker>
                    </el-form-item> -->
                  </div>
                  <div v-else>
                    <input-search-baidu
                      v-if="mapType === 'baidu'"
                      v-show="false"
                    ></input-search-baidu>
                    <input-search
                      v-else
                      v-show="false"
                      dom-id="addressId"
                    ></input-search>
                    <el-form-item :label="$t('Address')" prop="address">
                      <el-input
                        id="addressId"
                        v-model="orderForm.address"
                        placeholder=""
                        disabled
                      >
                        <el-button slot="append" class="locale-btn" disabled>
                          <i class="el-icon-map-location" />{{
                            $t("positioning")
                          }}
                        </el-button>
                      </el-input>
                    </el-form-item>
                  </div>
                </el-col>
              </el-row>
            </div>
          </el-col>

          <!-- 收件人 -->
          <el-col
            :span="12"
            v-if="
              orderForm.type == 2 || orderForm.type == 3 || orderForm.type == 4
            "
          >
            <div v-if="orderForm.type == 2" style="width: 100%"></div>
            <h4 class="box_title">
              {{ $t("Receiver") }}
              <span class="add-row address-table">
                <el-button
                  @click="setSender(orderForm.merchant_id)"
                  v-show="orderForm.merchant_id"
                  >{{ $t("AddressBook") }}</el-button
                >
              </span>
            </h4>
            <div class="shipper">
              <el-row>
                <el-col
                  :span="3"
                  style="
                    height: 350px;
                    text-align: center;
                    background-color: #f3f2f2;
                  "
                >
                  <div style="margin-top: 130px">
                    <img src="../../assets/img/unload.png" alt="" />
                    <div style="margin-top: 10px">
                      {{ $t("unloadingPlace") }}
                    </div>
                  </div></el-col
                >
                <el-col :span="20" style="margin-top: 10px; margin-left: -20px">
                  <!-- 姓名 -->
                  <el-form-item
                    :label="$t('Receiver')"
                    prop="second_place_fullname"
                    class="item-style item-left item-bottom"
                  >
                    <el-input
                      v-model="orderForm.second_place_fullname"
                    ></el-input>
                  </el-form-item>
                  <!-- 电话 -->
                  <el-form-item
                    :label="$t('Tel')"
                    prop="second_place_phone"
                    class="item-style item-font item-bottom"
                  >
                    <el-input v-model="orderForm.second_place_phone"></el-input>
                  </el-form-item>
                  <!-- 地址 -->
                  <!-- <el-form-item :label="$t('地址')" prop="place_phone">
                    <el-input v-model="orderForm.place_phone"></el-input>
                  </el-form-item> -->

                  <div v-if="template_id == 1">
                    <!-- 国家 -->
                    <el-form-item
                      :label="$t('Country')"
                      prop="second_place_country"
                    >
                      <el-select
                        v-model="orderForm.second_place_country"
                        :placeholder="$t('pleaseSelectCountry')"
                        size="small"
                        style="width: 100%"
                        @change="changeCountry(1)"
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
                      class="item-style item-left item-bottom"
                    >
                      <el-input
                        v-model="orderForm.second_place_post_code"
                        @blur="getLocation(1)"
                      >
                      </el-input>
                    </el-form-item>
                    <!-- 门牌号 -->
                    <el-form-item
                      :label="$t('DoorNo')"
                      prop="second_place_house_number"
                      class="item-style item-font item-bottom"
                    >
                      <el-input
                        v-model="orderForm.second_place_house_number"
                        @blur="getLocation(1)"
                      ></el-input>
                    </el-form-item>
                    <!-- 城市 -->
                    <el-form-item
                      :label="$t('City')"
                      prop="second_place_city"
                      class="item-style item-left"
                    >
                      <el-input
                        v-model="orderForm.second_place_city"
                        @blur="getLocation(1)"
                        :disabled="disabledBtnByCountry(1)"
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
                        v-model="orderForm.second_place_street"
                        @blur="getLocation(1)"
                        :disabled="disabledBtnByCountry(1)"
                      ></el-input>
                    </el-form-item>
                    <!-- 日期 -->
                    <el-form-item
                      :label="$t('date')"
                      prop="second_execution_date"
                      v-if="this.orderForm.type == 2"
                    >
                      <el-date-picker
                        value-format="yyyy-MM-dd"
                        :disabled="isPickUp"
                        v-model="orderForm.second_execution_date"
                        type="date"
                        :picker-options="disabledDateOptionsPie"
                        :placeholder="$t('PleasePieDate')"
                        @change="getDateChange"
                      >
                      </el-date-picker>
                    </el-form-item>
                    <el-form-item
                      :label="$t('date')"
                      prop="second_execution_date"
                      v-if="this.orderForm.type != 2"
                    >
                      <el-date-picker
                        value-format="yyyy-MM-dd"
                        :disabled="isPickUp"
                        v-model="orderForm.second_execution_date"
                        type="date"
                        :picker-options="disabledDateOptionsPie"
                        :placeholder="$t('PleasePieDate')"
                        @change="getDateChange"
                      >
                      </el-date-picker>
                    </el-form-item>
                  </div>

                  <div v-else>
                    <input-search-baidu
                      v-if="mapType === 'baidu'"
                      v-show="false"
                      @result="getResult(1, $event)"
                    ></input-search-baidu>
                    <input-search
                      v-else
                      v-show="false"
                      @result="getResult(1, $event)"
                      dom-id="secondId"
                    ></input-search>
                    <el-form-item
                      :label="$t('Address')"
                      prop="second_place_address"
                    >
                      <el-input
                        id="secondId"
                        v-model="orderForm.second_place_address"
                        :placeholder="$t('PleaseEnterAddress')"
                      >
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
                      v-if="this.orderForm.type == 2"
                    >
                      <el-date-picker
                        value-format="yyyy-MM-dd"
                        :disabled="isPickUp"
                        v-model="orderForm.second_execution_date"
                        type="date"
                        :picker-options="disabledDateOptionsPie"
                        :placeholder="$t('PleasePieDate')"
                      >
                      </el-date-picker>
                    </el-form-item>
                    <el-form-item
                      :label="$t('date')"
                      prop="second_execution_date"
                      v-if="this.orderForm.type != 2"
                    >
                      <el-date-picker
                        value-format="yyyy-MM-dd"
                        :disabled="isPickUp"
                        v-model="orderForm.second_execution_date"
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
      <el-form
        :model="orderForm"
        ref="orderForm"
        :rules="rules"
        label-width="110px"
        label-position="right"
      >
        <el-card class="box-card">
          <h4 class="box_title">{{ $t("ProductInfo") }}</h4>
          <!-- 包裹-->
          <el-form-item style="margin-left: -90px" v-if="this.nature != 3">
            <div class="add-row">
              <span style="float: left">{{ $t("package") }}</span>
              <el-button @click="addRow(1)" :disabled="isPickUp">{{
                $t("Add")
              }}</el-button>
            </div>
            <el-table
              max-height="600px"
              :data="orderForm.package_list"
              style="width: 100%"
              border
            >
              <el-table-column type="index" align="center"></el-table-column>
              <!-- 包裹名称 -->
              <el-table-column
                :label="'*' + $t('WrappingMaterial')"
                align="center"
              >
                <template slot-scope="scope">
                  <el-input
                    v-model="scope.row.name"
                    :disabled="isPickUp"
                    size="mini"
                  ></el-input>
                </template>
              </el-table-column>
              <!-- 包裹单号 -->
              <el-table-column :label="'*' + $t('parcelNo')" align="center">
                <template slot-scope="scope">
                  <el-input
                    v-model="scope.row.express_first_no"
                    :disabled="isPickUp"
                    size="mini"
                  ></el-input>
                </template>
              </el-table-column>
              <!-- 数量 -->
              <!-- <el-table-column
              :label="'*' + $t('Quantity')"
              align="center"
              width="150px"
            >
              <template slot-scope="scope">
                <el-input-number
                  v-model="scope.row.expect_quantity"
                  :min="1"
                  :disabled="true"
                  controls-position="right"
                  size="mini"
                ></el-input-number>
              </template>
            </el-table-column> -->
              <!-- 重量 -->
              <el-table-column
                :label="$t('Weight') + '(' + weight_unit_symbol + ')'"
                align="center"
                width="150px"
              >
                <template slot-scope="scope">
                  <el-input-number
                    v-model="scope.row.weight"
                    :min="1"
                    :disabled="isPickUp"
                    size="mini"
                  ></el-input-number>
                </template>
              </el-table-column>
              <!-- 特性 -->
              <el-table-column :label="'*' + $t('nature')" align="center">
                <template slot-scope="scope">
                  <el-select
                    v-model="scope.row.feature_logo"
                    :disabled="isPickUp"
                    :placeholder="$t('pleaseSelect')"
                    size="mini"
                  >
                    <el-option
                      v-for="item in packageFeatureList"
                      :key="item.id"
                      :label="item.name"
                      :value="item.name"
                    >
                    </el-option>
                  </el-select>
                </template>
              </el-table-column>
              <!-- 快递单号2 -->
              <!-- <el-table-column :label="$t('备用包裹单号')" align="center">
              <template slot-scope="scope">
                <el-input
                  v-model="scope.row.express_second_no"
                  :disabled="isPickUp"
                ></el-input>
              </template>
            </el-table-column> -->

              <!-- 其他备注 -->
              <el-table-column :label="$t('Remark')" align="center">
                <template slot-scope="scope">
                  <el-input
                    v-model="scope.row.remark"
                    :disabled="isPickUp"
                    size="mini"
                  ></el-input>
                </template>
              </el-table-column>
              <!-- 最迟派送日期 -->
              <el-table-column
                :label="'*' + $t('LatestDeliveryDate')"
                align="center"
                width="200px"
              >
                <template slot-scope="scope">
                  <el-date-picker
                    value-format="yyyy-MM-dd"
                    v-model="scope.row.expiration_date"
                    :disabled="isPickUp"
                    type="date"
                    :picker-options="pickerOptions"
                    :placeholder="$t('LatestDeliveryDate')"
                    size="mini"
                    style="width: 180px"
                  >
                  </el-date-picker>
                </template>
              </el-table-column>
              <!-- 外部标示 -->
              <el-table-column align="center" width="180">
                <template slot="header">
                  {{ $t("Externallabeling") }}
                  <el-tooltip
                    class="item"
                    effect="dark"
                    :content="$t('Externallabelingforpushtothirdparties')"
                    placement="top"
                  >
                    <i class="el-icon-info"></i>
                  </el-tooltip>
                </template>
                <template slot-scope="scope">
                  <el-input
                    v-model="scope.row.out_order_no"
                    :disabled="isPickUp"
                    size="mini"
                  ></el-input>
                </template>
              </el-table-column>
              <!-- 操作 -->
              <el-table-column :label="$t('Operation')" align="center">
                <template slot-scope="scope">
                  <el-button
                    @click.native.prevent="
                      deleteRow(scope.$index, orderForm.package_list)
                    "
                    :disabled="isPickUp"
                    type="danger"
                    size="mini"
                    >{{ $t("Remove") }}</el-button
                  >
                </template>
              </el-table-column>
            </el-table>
          </el-form-item>

          <!-- 货物 -->
          <el-form-item style="margin-left: -90px" v-if="this.nature != 2">
            <div class="add-row">
              <span style="float: left">{{ $t("cargo") }}</span>

              <el-button @click="addRow(2)" :disabled="isPickUp">{{
                $t("Add")
              }}</el-button>
            </div>
            <!--  -->
            <el-table
              max-height="600px"
              :data="orderForm.material_list"
              style="width: 100%"
              border
              :summary-method="getSummaries"
              show-summary
            >
              <el-table-column type="index" prop="heji"></el-table-column>
              <!-- 货物名称 -->
              <el-table-column
                :label="'*' + $t('ProductName')"
                prop="name"
                align="center"
                width="120"
              >
                <template slot-scope="scope">
                  <el-input
                    v-model="scope.row.name"
                    :disabled="isPickUp"
                    size="mini"
                  ></el-input>
                </template>
              </el-table-column>
              <!-- 代码 -->
              <el-table-column
                :label="'*' + $t('Code')"
                prop="code"
                align="center"
              >
                <template slot-scope="scope">
                  <el-input
                    v-model="scope.row.code"
                    :disabled="isPickUp"
                    size="mini"
                  ></el-input>
                </template>
              </el-table-column>
              <!-- 数量 -->
              <el-table-column
                :label="$t('Quantity') + '(' + $t('Pieces') + ')'"
                align="center"
                width="150"
                prop="expect_quantity"
              >
                <template slot-scope="scope">
                  <el-input-number
                    v-model="scope.row.expect_quantity"
                    :min="1"
                    :precision="0"
                    :step="1"
                    controls-position="right"
                    size="mini"
                  ></el-input-number>
                </template>
              </el-table-column>
              <!-- 重量 -->
              <el-table-column
                :label="$t('Weight') + '(' + weight_unit_symbol + ')'"
                align="center"
                width="150"
                prop="weight"
              >
                <template slot-scope="scope">
                  <el-input-number
                    v-model="scope.row.weight"
                    :min="1"
                    :disabled="isPickUp"
                    size="mini"
                  ></el-input-number>
                </template>
              </el-table-column>
              <!-- 体积 -->
              <el-table-column
                :label="$t('Volume') + '(' + volume_unit_symbol + ')'"
                align="center"
                width="150"
                prop="size"
              >
                <template slot-scope="scope">
                  <el-input-number
                    v-model="scope.row.size"
                    :min="1"
                    :disabled="isPickUp"
                    size="mini"
                  ></el-input-number>
                </template>
              </el-table-column>
              <!-- 货物类型 -->
              <el-table-column
                :label="$t('MerchantType')"
                align="center"
                prop="type"
              >
                <template slot-scope="scope">
                  <el-select
                    v-model="scope.row.type"
                    :disabled="isPickUp"
                    :placeholder="$t('pleaseSelect')"
                    size="mini"
                  >
                    <el-option
                      v-for="item in materialTypeList"
                      :key="item.id"
                      :label="item.name"
                      :value="item.id"
                    >
                    </el-option>
                  </el-select>
                </template>
              </el-table-column>
              <!-- 包装 -->
              <el-table-column
                :label="$t('cargoPackage')"
                align="center"
                prop="pack_type"
              >
                <template slot-scope="scope">
                  <el-select
                    v-model="scope.row.pack_type"
                    :disabled="isPickUp"
                    :placeholder="$t('pleaseSelect')"
                    size="mini"
                  >
                    <el-option
                      v-for="item in materialPackTypeList"
                      :key="item.id"
                      :label="item.name"
                      :value="item.id"
                    >
                    </el-option>
                  </el-select>
                </template>
              </el-table-column>
              <!-- 单价 -->
              <el-table-column
                :label="$t('Price') + '(' + currency_unit_symbol + ')'"
                align="center"
                prop="unit_price"
                width="150"
              >
                <template slot-scope="scope">
                  <el-input
                    v-model="scope.row.unit_price"
                    :disabled="isPickUp"
                    size="mini"
                  ></el-input>
                </template>
              </el-table-column>
              <!-- 备注 -->
              <el-table-column
                :label="$t('Remark')"
                align="center"
                prop="remark"
              >
                <template slot-scope="scope">
                  <el-input
                    v-model="scope.row.remark"
                    :disabled="isPickUp"
                    size="mini"
                  ></el-input>
                </template>
              </el-table-column>
              <!-- 外部标示 -->
              <el-table-column align="center" prop="out_order_no" width="200">
                <template slot="header">
                  {{ $t("Externallabeling") }}
                  <el-tooltip
                    class="item"
                    effect="dark"
                    :content="$t('Externallabelingforpushtothirdparties')"
                    placement="top"
                  >
                    <i class="el-icon-info"></i>
                  </el-tooltip>
                </template>
                <template slot-scope="scope">
                  <el-input
                    v-model="scope.row.out_order_no"
                    :disabled="isPickUp"
                    size="mini"
                  ></el-input>
                </template>
              </el-table-column>
              <el-table-column
                :label="$t('Operation')"
                align="center"
                prop="yichu"
              >
                <template slot-scope="scope">
                  <!-- 移除 -->
                  <el-button
                    @click.native.prevent="
                      deleteRow(scope.$index, orderForm.material_list)
                    "
                    :disabled="isPickUp"
                    type="danger"
                    class="remove-btn"
                    size="mini"
                    >{{ $t("Remove") }}</el-button
                  >
                </template>
              </el-table-column>
            </el-table>
          </el-form-item>
          <!-- 控货方式 -->
          <div style="margin-top: 40px">
            <div style="float: left">
              <el-form-item :label="$t('controlMethod')" prop="is_ticket">
                <el-radio-group v-model="orderForm.control_mode">
                  <el-radio :label="1">{{ $t("no") }}</el-radio>
                  <el-radio :label="2">{{ $t("WaitGoods") }}</el-radio>
                </el-radio-group>
              </el-form-item>
            </div>

            <div style="float: right">
              <span>{{ $t("ReceiptRequirements") }}</span>
              <el-select
                v-model="orderForm.receipt_type"
                :placeholder="$t('OriginalOrderReturn')"
                size="mini"
              >
                <el-option
                  v-for="item in orderReceiptTypeList"
                  :key="item.id"
                  :label="item.name"
                  :value="item.id"
                >
                </el-option>
              </el-select>
              <el-input-number
                v-model="orderForm.receipt_count"
                controls-position="right"
                :min="0"
                :max="10000"
                style="width: 80px"
                size="mini"
                :precision="0"
                :step="1"
              ></el-input-number>
              {{ $t("Share") }}
            </div>
          </div>
        </el-card>
        <el-card class="box-card">
          <div class="box">
            <!-- 基础费用 -->
            <div>
              <h4 class="box_title">{{ $t("BaseFee") }}</h4>
              <el-form-item style="margin-left: -90px">
                <div>
                  <div class="base-fee">
                    {{ $t("freigheCost") }}
                    {{ "(" + currency_unit_symbol + ")" }}
                  </div>
                  <div class="fee-box">
                    <el-input
                      v-model="orderForm.settlement_amount"
                      :disabled="isPickUp"
                      size="mini"
                      @input="sum"
                      style="width: 90%"
                    ></el-input>
                  </div>
                </div>
                <!-- 预估价格 -->
                <el-tooltip
                  :content="$t('AdministratorActivate')"
                  placement="top"
                  :disabled="hasPerm('order.price-count') ? true : false"
                >
                  <div style="width: 100px">
                    <el-button
                      @click.native.prevent="show"
                      :disabled="isPickUp || !hasPerm('order.price-count')"
                      type="primary"
                      class="estimated-btn"
                      size="mini"
                      >{{ $t("EstimatedPrice") }}
                    </el-button>
                  </div>
                </el-tooltip>
              </el-form-item>
            </div>
            <!-- 增值服务 -->
            <div>
              <h4 class="box_title">{{ $t("valueaddedServices") }}</h4>
              <el-form-item style="margin-left: -90px">
                <el-table
                  max-height="600px"
                  :data="orderForm.bill_list"
                  style="width: 100%"
                  border
                >
                  <!-- 基础运费 -->
                  <el-table-column
                    :label="$t(item.name) + '(' + currency_unit_symbol + ')'"
                    align="center"
                    v-for="(item, index) in orderAmountType_list"
                    :key="index"
                    width="220"
                  >
                    <template slot-scope="scope">
                      <div>
                        <el-input
                          v-model="scope.row[item.id]"
                          :disabled="isPickUp"
                          size="mini"
                          @input="sum"
                        ></el-input>
                      </div>
                    </template>
                  </el-table-column>
                </el-table>
              </el-form-item>
            </div>
          </div>
        </el-card>
      </el-form>
      <!-- 其他信息 -->
      <el-form
        ref="orderForm"
        :rules="rules"
        label-width="160px"
        label-position="right"
      >
        <el-row :gutter="20">
          <el-col :span="18">
            <el-card class="box-card">
              <h4 class="box_title">{{ $t("Optional") }}</h4>
              <!-- 特别事项 -->
              <el-form-item>
                <div slot="label">
                  <span>{{ $t("SpecialReminder") }}</span>
                  <el-tooltip
                    effect="dark"
                    :content="$t('Itwill')"
                    placement="top"
                  >
                    <span class="el-icon-warning icon-question"></span>
                  </el-tooltip>
                </div>
                <el-input v-model="orderForm.special_remark"> </el-input>
              </el-form-item>
              <!-- 掩码 -->
              <el-form-item
                :label="$t('maskCode')"
                class="item-style item-left input-style"
              >
                <el-input
                  v-model="orderForm.mask_code"
                  :disabled="isPickUp"
                ></el-input>
              </el-form-item>
            </el-card>
          </el-col>

          <el-col :span="6">
            <el-card class="box-card">
              <h4 class="box_title">{{ $t("settlementInfo") }}</h4>
              <!-- 运费合计 -->
              <el-form-item
                :label="
                  $t('totalFreightCost') + '(' + currency_unit_symbol + '):'
                "
              >
                <el-tooltip
                  effect="dark"
                  :content="$t('totalFreightCostState')"
                  placement="top"
                >
                  <span class="el-icon-warning icon-question"></span>
                </el-tooltip>
                <span style="color: red">{{ expect_total_amount }}</span>
              </el-form-item>
              <!-- 付款方式 -->
              <el-form-item :label="$t('paymentMethod') + ':'">
                {{ this.orderForm.settlement_type_name }}
                <!-- <el-select
                  v-model="orderForm.settlement_type"
                  :placeholder="$t('OriginalOrderReturn')"
                  size="mini"
                  style="width: 100px"
                >
                  <el-option
                    v-for="(item, index) in merchantSettlementTypeList"
                    :key="index"
                    :label="item.name"
                    :value="item.id"
                  >
                  </el-option>
                </el-select> -->
              </el-form-item>
            </el-card>
          </el-col>
        </el-row>
      </el-form>
    </el-form>
    <div class="save-btn">
      <!-- 打印运单 -->
      <el-checkbox v-model="orderForm.checked">{{
        $t("PrintTheWaybill")
      }}</el-checkbox>
      <!-- <el-checkbox v-model="checked">打印货签</el-checkbox> -->
    </div>
    <div class="save-btn">
      <!-- 继续开单 -->
      <el-button
        @click="confirmContinue('orderForm')"
        class="cancel"
        :loading="this.$store.state.config.button_loading"
        >{{ $t("ContinueToOrder") }}</el-button
      >
      <!-- 提交 -->
      <el-button
        type="primary"
        @click="confirm('orderForm')"
        class="submit"
        :loading="this.$store.state.config.button_loading"
        >{{ $t("Submit") }}</el-button
      >
    </div>
    <!-- 发货人 收货人 -->
    <el-dialog :visible.sync="show_receiver_address" width="60%">
      <span slot="title">{{ addressDialogTit }}</span>
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
        <el-col :span="2">
          <!-- 查询 -->
          <el-button
            size="small"
            type="primary"
            @click="getOrder"
            :loading="this.$store.state.config.button_loading"
            >{{ $t("Search") }}</el-button
          ></el-col
        >
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
import InputSearch from "@/components/Map/InputSearchMap";
import BMap from "@/components/BaiduMap/getAddressByMap";
import InputSearchBaidu from "@/components/BaiduMap/InputSearchMap";
import websocket from "@/lib/websocket"; //打印

export default {
  name: "Test",
  components: {
    BasePagination,
    googleMap,
    InputSearch,
    BMap,
    InputSearchBaidu,
  },
  data() {
    return {
      printId: "", //打印id
      nature: "", //包裹内容物
      activeColor: "red", // 颜色
      currency_unit_symbol: "", // 单价单位
      weight_unit_symbol: "", // 重量单位
      volume_unit_symbol: "", // 体积单位
      orderReceiptTypeList: [], // 原单返回下拉框
      orderTransportModeList: [], // 运输方式下拉框
      orderOriginTypeList: [], // 始发地
      orderTypeList: [], // 订单类型
      orderTypeList2: [],
      materialTypeList: [], // 货物类型
      materialPackTypeList: [], // 包装
      merchantList: [], // 所属货主
      packageFeatureList: [], // 特性列表
      limitTimePik: [], //寄件人限制时间
      limitTimePei: [], //收件人限制时间
      itemArr: {}, // 包裹数据
      materialArr: {}, // 货物数据
      orderAmountArr: {}, // 费用数据
      is_sender: "",
      tableData: [], // 列表
      warehouseTabledata: [], // 仓库
      countryList: [], //国家下拉
      showPlaceCountry: false, //判断发国家显隐
      showSecondCountry: false, //判断收国家显隐
      show_receiver_address: false, // 发/收货人弹出框
      show_warehouse: false, // 仓库弹出框
      isPickUp: false, // 禁用
      show_estimate: false,
      estimateInfo: "", // 估算信息
      lon: "", // 经度
      lat: "", // 纬度
      expect_total_amount: "0.00", // 运费合计
      orderForm: {
        transport_mode: 2, // 运输方式
        origin_type: null, // 始发地
        merchant_id: "", // 货主
        type: 1, // 类型
        checked: false, // 打印运单
        receipt_type: 1, // 原单返回
        control_mode: 1, // 控货方式
        settlement_type: null, // 结算方式
        settlement_type_name: "",
        create_date: new Date(), // 开单时间
        receipt_count: 0,
        execution_date: "", //取件时间
        second_execution_date: "", //派件时间
        express_first_no: "", //包裹单号
        out_user_id: "", //外部订单号
        settlement_amount: null,
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
        // 仓库
        name: "",
        phone: "",
        country: "",
        post_code: "",
        house_number: "",
        city: "",
        street: "",
        address: "",

        special_remark: "", // 特别注意事项
        remark: "",
        country_id: "",
        package_list: [], // 包裹列表
        material_list: [], // 货物列表
        bill_list: [{}], // 费用列表
        mask_code: "",
      },
      BaseFeeList: [{}], //运费
      summation: 0, // 运费合计
      zhanshi: true,
      hidden: true,
      activeName: "",
      orderAmountType_list: [], // 自定义费用列表
      // 付款方式
      merchantSettlementTypeList: [],
      limitTimeRules: [],
      rules: {
        // 所属货主
        merchant_id: [
          { required: true, message: this.$t("Required"), trigger: "change" },
        ],
        // 开单时间
        create_date: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        // 类型
        type: [
          {
            required: true,
            message: this.$t("Pleaseenterordertype"),
            trigger: "change",
          },
        ],
        // 包裹单号
        express_first_no: [
          {
            required: true,
            message: this.$t("PleaseenterOrderNumber1"),
            trigger: "change",
          },
        ],
        // 派件日期
        second_execution_date: [
          {
            required: true,
            message: this.$t("Pleaseselectdistributiondate"),
            trigger: "change",
          },
        ],
        // 取件日期
        execution_date: [
          {
            required: true,
            message: this.$t("Pleaseselectdistributiondate"),
            trigger: "change",
          },
        ],

        // 结算方式
        settlement_type: [
          {
            required: true,
            message: this.$t("Pleaseselectsettlementmethod"),
            trigger: "change",
          },
        ],
        // 发件人
        place_fullname: [
          {
            required: true,
            message: this.$t("PleaseenterSenderName"),
            trigger: "blur",
          },
        ],
        place_phone: [
          {
            required: true,
            message: this.$t("PleaseenterSenderTel"),
            trigger: "blur",
          },
        ],
        place_country: [
          {
            required: true,
            message: " ",
            trigger: "blur",
          },
        ],
        place_post_code: [
          {
            required: true,
            message: this.$t("PleaseenterSenderPostcode"),
            trigger: "blur",
          },
        ],
        place_house_number: [
          {
            required: true,
            message: this.$t("PleaseenterSenderHousenumber"),
            trigger: "blur",
          },
        ],
        place_city: [
          {
            required: true,
            message: this.$t("PleaseenterSenderCity"),
            trigger: "blur",
          },
        ],
        place_street: [
          {
            required: true,
            message: this.$t("PleaseenterSenderStreet"),
            trigger: "blur",
          },
        ],
        place_address: [
          {
            required: true,
            message: this.$t("PleaseenterSenderAddress"),
            trigger: "blur",
          },
        ],
        // 收件人
        second_place_fullname: [
          {
            required: true,
            message: this.$t("PleaseenterRecipientName"),
            trigger: "change",
          },
        ],
        second_place_phone: [
          {
            required: true,
            message: this.$t("PleaseenterRecipientTel"),
            trigger: "blur",
          },
        ],
        second_place_country: [
          {
            required: true,
            message: " ",
            trigger: "change",
          },
        ],
        second_place_post_code: [
          {
            required: true,
            message: this.$t("PleaseenterRecipientPostcode"),
            trigger: "blur",
          },
        ],
        second_place_house_number: [
          {
            required: true,
            message: this.$t("PleaseenterRecipientHouseName"),
            trigger: "blur",
          },
        ],
        second_place_city: [
          {
            required: true,
            message: this.$t("PleaseenterRecipientCity"),
            trigger: "blur",
          },
        ],
        second_place_street: [
          {
            required: true,
            message: this.$t("PleaseenterRecipientStreet"),
            trigger: "blur",
          },
        ],
        second_place_address: [
          {
            required: true,
            message: this.$t("PleaseenterRecipientAddress"),
            trigger: "blur",
          },
        ],
        // 仓库
        name: [
          {
            required: true,
            message: this.$t("PleaseenterRecipientName"),
            trigger: "change",
          },
        ],
        post_code: [
          {
            required: true,
            message: this.$t("PleaseenterRecipientPostcode"),
            trigger: "change",
          },
        ],
        house_number: [
          {
            required: true,
            message: this.$t("PleaseenterRecipientHouseName"),
            trigger: "blur",
          },
        ],
        country: [
          {
            required: true,
            message: this.$t("pleaseSelectCountry"),
            trigger: "blur",
          },
        ],
        city: [
          {
            required: true,
            message: this.$t("PleaseenterRecipientCity"),
            trigger: "blur",
          },
        ],
        street: [
          {
            required: true,
            message: this.$t("PleaseenterRecipientStreet"),
            trigger: "blur",
          },
        ],
        address: [
          {
            required: true,
            message: this.$t("PleaseenterRecipientAddress"),
            trigger: "blur",
          },
        ],
      },
      // 查询
      pageParams: {
        place_post_code: "",
        merchant_id: "",
        status: "",
        group_id: "",
        is_lock: "",
        begin_date: "",
        end_date: "",
        from_source: "",
        keyword: "",
        total: 0,
        page: 1,
        per_page: 10,
      },
      // 开单日期
      // pickerOptionsCreate: {
      //   disabledDate(time) {
      //     return time.getTime() > Date.now();
      //   },
      // },
      // 日期
      pickerOptions: {
        disabledDate(time) {
          return time.getTime() < Date.now() - 8.64e7;
          // return time.getTime() > Date.now() - 8.64e7;
        },
      },

      // 地图
      select_map: false,
      template_id: localStorage.getItem("address_template_id") || 1,
      country:
        localStorage.getItem("country") &&
        JSON.parse(localStorage.getItem("country")).en,
      ReceiverMapDialog: false,
    };
  },
  created() {
    this.getFee();
    this.getAddedCountry(); //国家列表
    this.getIndentSet(); // 拉取订单设置的数据
    this.activeName = "1";
    this.getMerchant(); // 获取货主列表
    this.getDictionary(); //数据字典
    this.getAdvanceSetting(); // 获取单位值
    if (this.$route.query.id) {
      this.getOrderDetails(this.$route.query.id);
    }
  },
  computed: {
    mapType() {
      return this.$store.state.globalData.maptype;
    },
    // 拉取发收件人
    addressDialogTit() {
      if (this.orderForm.type == 2) {
        return this.$t("Receiver");
      } else if (this.orderForm.type == 3) {
        return this.is_sender == this.$t("Receiver")
          ? this.$t("Sender")
          : this.$t("Receiver");
      }
      return this.$t("Sender");
    },
    // 预算估价
    // unit() {
    //   return this.$store.state.globalData.unit;
    // },
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
    // // 限制不可选日期
    // disabledDateOptions() {
    //   return {
    //     disabledDate: (date) => {
    //       const limit = this.limitTimeRules;
    //       // 将时间戳转换成年月日
    //       function add0(m) {
    //         return m < 10 ? `0${m}` : m;
    //       }
    //       // 时间戳转化成时间格式
    //       function timeFormat(timestamp) {
    //         // timestamp是整数，否则要parseInt转换,不会出现少个0的情况
    //         const time = new Date(timestamp);
    //         const year = time.getFullYear();
    //         const month = time.getMonth() + 1;
    //         const day = time.getDate();
    //         return `${year}-${add0(month)}-${add0(day)}`;
    //       }
    //       const result = timeFormat(date);
    //       if (limit.includes(`${result}`)) {
    //         return false;
    //       }
    //       return true;
    //     },
    //   };
    // },
  },
  watch: {
    "orderForm.type": {
      handler(val) {
        if (val == 1) {
          this.orderForm.mask_code = null;
          this.orderForm.package_list.forEach((el) => {
            el.feature_logo = "";
          });
        }
      },
    },
  },
  methods: {
    // 判断国家 禁用
    disabledBtnByCountry(el) {
      if (el == 1) {
        return this.orderForm.second_place_country === "NL";
      } else {
        return this.orderForm.place_country === "NL";
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
      if (el == 2) {
        if (
          this.orderForm.place_country != "" ||
          this.orderForm.place_country != null
        ) {
          this.showPlaceCountry = false;
        } else {
          this.showPlaceCountry = true;
        }
        this.orderForm.place_post_code = "";
        this.orderForm.place_house_number = "";
        this.orderForm.place_city = "";
        this.orderForm.place_street = "";
        this.orderForm.place_lat = "";
        this.orderForm.place_lon = "";
      } else {
        if (
          this.orderForm.second_place_country != "" ||
          this.orderForm.second_place_country != null
        ) {
          this.showSecondCountry = false;
        } else {
          this.showSecondCountry = true;
        }
        this.orderForm.second_place_post_code = "";
        this.orderForm.second_place_house_number = "";
        this.orderForm.second_place_city = "";
        this.orderForm.second_place_street = "";
        this.orderForm.second_place_lat = "";
        this.orderForm.second_place_lon = "";
      }
    },
    // 当类型改变时
    changeType() {
      this.orderForm = {
        ...this.orderForm,
        name: "",
        phone: "",
        post_code: "",
        house_number: "",
        city: "",
        street: "",
      };
      if (this.orderForm.type == 1) {
        this.getOrderWarehouse();
      } else if (this.orderForm.type == 2) {
        this.getOrderWarehouse();
      } else {
        return;
      }
    },
    changorigintype(value) {
      const orderTypeList = JSON.parse(JSON.stringify(this.orderTypeList2));
      if (value == 1) {
        var arr = orderTypeList.slice(0, 3);
        this.$set(this.orderForm, "type", 1);
        this.orderTypeList = arr;
      } else {
        var arr = orderTypeList.slice(-1);
        this.$set(this.orderForm, "type", 4);
        this.orderTypeList = arr;
      }
    },
    // 获取单位值
    getAdvanceSetting() {
      this.$api.getAdvanceSetting().then((res) => {
        this.volume_unit_symbol = res.data.volume_unit_symbol; // 体积单位
        this.weight_unit_symbol = res.data.weight_unit_symbol; // 重量单位
        this.currency_unit_symbol = res.data.currency_unit_symbol; // 单价单位
      });
    },
    // 改变货主 获取结算方式
    merchant() {
      this.getMerchants();
    },
    // 获取结算方式
    getMerchants() {
      this.$api.getMerchants().then((res) => {
        const Merchants = res.data.data;
        Merchants.map((item) => {
          if (this.orderForm.merchant_id == item.id) {
            this.orderForm.settlement_type = item.settlement_type;
            this.orderForm.settlement_type_name = item.settlement_type_name;
          }
        });
      });
    },
    // 拉取订单设置的数据
    getIndentSet() {
      this.$api.getIndentSet().then((res) => {
        this.orderForm.type = res.data.type; //订单类型
        this.orderForm.receipt_count = res.data.receipt_count; //回单数
        this.orderForm.receipt_type = res.data.receipt_type; //原单返回
        // this.orderForm.settlement_type = res.data.settlement_type; // 结算方式
        this.orderForm.control_mode = res.data.control_mode; // 控货方式
        this.nature = res.data.nature; //包裹内容物
      });
    },
    // 货物合计
    getSummaries(param) {
      const { columns, data } = param;
      const sums = [];
      columns.forEach((column, index) => {
        if (index === 0) {
          sums[index] = "总计";
        } else if (index === 3 || index === 4 || index === 5 || index === 8) {
          const values = data.map((item) => Number(item[column.property]));
          if (!values.every((value) => isNaN(value))) {
            sums[index] = values.reduce((prev, curr) => {
              const value = Number(curr);
              if (!isNaN(value)) {
                return prev + curr;
              }
              return prev;
            }, 0);
          } else {
            sums[index] = "N/A";
          }
        } else {
          sums[index] = "--";
        }
      });
      return sums;
    },

    // 获取货主列表
    getMerchant() {
      this.$api.getMerchantsPublic({ status: 1, per_page: 1000 }).then((res) => {
        if (!res) return;
        this.merchantList = res.data.data;
      });
    },
    // 获取下拉框
    getDictionary() {
      this.$api.getDictionary().then((res) => {
        this.materialPackTypeList = res.data.materialPackTypeList; // 包装
        this.orderTypeList = res.data.orderTypeList; // 订单类型
        this.orderTypeList2 = res.data.orderTypeList;
        this.materialTypeList = res.data.materialTypeList; // 货物类型
        this.orderOriginTypeList = res.data.orderOriginTypeList; // 始发地
        this.orderTransportModeList = res.data.orderTransportModeList; // 运输方式
        this.packageFeatureList = res.data.packageFeatureList; // 特性
        this.merchantSettlementTypeList = res.data.orderSettlementTypeList; // 结算方式
        this.orderReceiptTypeList = res.data.orderReceiptTypeList; // 原单返回
      });
    },
    //  增值服务
    getFee() {
      let obj = {
        level: 2,
        status: 1,
        object_type: 1,
        pay_timing: 1,
      };
      this.$api.getFee(obj).then((res) => {
        this.orderAmountType_list = res.data.data; // 费用
      });
    },
    getResult(type, data) {
      // 填充收货人
      if (type == 2) {
        this.orderForm.place_city = data.city;
        this.orderForm.place_street = `${data.route}`;
        this.orderForm.place_house_number = data.street_number;
        this.orderForm.place_country = data.country;
        this.orderForm.place_post_code = data.postal_code;
        this.orderForm.place_lat = data.location.lat;
        this.orderForm.place_lon = data.location.lng;
        this.lat = data.location.lat;
        this.lon = data.location.lng;
        if (this.mapType === "baidu") {
          this.orderForm.place_address =
            oldAddress + data.route + data.street_number;
        } else {
          this.orderForm.place_address = data.address;
          this.getOrderDate((this.is_sender = this.$t("Receiver")));
        }
      } else {
        // 发件人
        this.orderForm.second_place_city = data.city;
        this.orderForm.second_place_street = `${data.route}`;
        this.orderForm.second_place_house_number = data.street_number;
        this.orderForm.second_place_country = data.country;
        this.orderForm.second_place_post_code = data.postal_code;
        this.orderForm.second_place_lat = data.location.lat;
        this.orderForm.second_place_lon = data.location.lng;
        this.lat = data.location.lat;
        this.lon = data.location.lng;
        if (this.mapType === "baidu") {
          this.orderForm.second_place_address =
            oldAddress + data.route + data.street_number;
        } else {
          this.orderForm.second_place_address = data.address;
          // document.getElementById("senderId").value = data.address;
          this.getOrderDate((this.is_sender = this.$t("sender")));
        }
      }
    },
    // 预算估价
    show() {
      this.getOrderPriceCount();
    },
    cancel() {
      this.$emit("cancel");
    },
    // 删除行
    deleteRow(index, rows) {
      rows.splice(index, 1);
    },
    // 添加行
    addRow(flag) {
      if (flag === 1) {
        this.orderForm.package_list.push({
          name: "", // 包裹名称
          express_first_no: "", // 包裹单号
          weight: "", // 重量
          expect_quantity: 1, // 数量
          remark: "", // 备注
          expiration_date: "", // 最迟派送日期
          out_order_no: "", // 外部标识
        });
      } else if (flag === 2) {
        this.orderForm.material_list.push({
          name: "", // 货物名称
          expect_quantity: 1, // 数量
          weight: "1", // 重量
          size: "1", // 体积
          type: "", // 类型
          pack_type: "", // 包装
          unit_price: "1", // 单价
          out_order_no: "", // 外部标识
          remark: "", // 备注
          code: "", // 代码
        });
      }
    },
    // 拉取时间
    getOrderDate() {
      let data = {};
      if (this.is_sender === this.$t("Receiver")) {
        data = {
          type: 1,
          place_country: this.orderForm.place_country,
          place_post_code: this.orderForm.place_post_code,
          place_house_number: this.orderForm.place_house_number,
          place_city: this.orderForm.place_city,
          place_street: this.orderForm.place_street,
          place_lat: this.orderForm.place_lat,
          place_lon: this.orderForm.place_lon,
          merchant_id: this.orderForm.merchant_id,
        };
      } else {
        data = {
          type: 2,
          place_country: this.orderForm.second_place_country,
          place_post_code: this.orderForm.second_place_post_code,
          place_house_number: this.orderForm.second_place_house_number,
          place_city: this.orderForm.second_place_city,
          place_street: this.orderForm.second_place_street,
          place_lat: this.orderForm.second_place_lat,
          place_lon: this.orderForm.second_place_lon,
          merchant_id: this.orderForm.merchant_id,
        };
      }
      this.$api.getOrderDate(data).then((res) => {
        var data = res.data;
        if (this.is_sender === this.$t("Receiver")) {
          this.limitTimePik = data;
          if (this.lat && this.lon && data) {
            let isHas = false;
            isHas = data.some((el) => {
              return el == this.orderForm.execution_date;
            });
            isHas ? "" : (this.orderForm.execution_date = data[0]);
          }
        } else {
          this.limitTimePei = data;
          if (this.lat && this.lon && data) {
            let isHas = false;
            isHas = data.some((el) => {
              return el == this.orderForm.second_execution_date;
            });
            isHas ? "" : (this.orderForm.second_execution_date = data[0]);
          }
        }
        if (
          (this.$route.query.id && this.orderForm.type == 3) ||
          this.orderForm.type == 4
        ) {
          this.limitTimePik = data;
          this.limitTimePei = data;
        }
        if (this.orderForm.type == 1 || this.orderForm.type == 2) {
          this.getOrderWarehouse();
        }
      });
    },

    // 拉取仓库
    getOrderWarehouse() {
      let data = {};
      if (this.orderForm.type == 1) {
        data = {
          type: this.orderForm.type,
          place_country: this.orderForm.place_country,
          place_post_code: this.orderForm.place_post_code,
          place_house_number: this.orderForm.place_house_number,
          place_city: this.orderForm.place_city,
          place_street: this.orderForm.place_street,
          execution_date: this.orderForm.execution_date,
          merchant_id: this.orderForm.merchant_id,
        };
      } else if (this.orderForm.type == 2) {
        data = {
          type: this.orderForm.type,
          place_country: this.orderForm.second_place_country,
          place_post_code: this.orderForm.second_place_post_code,
          place_house_number: this.orderForm.second_place_house_number,
          place_city: this.orderForm.second_place_city,
          place_street: this.orderForm.second_place_street,
          execution_date: this.orderForm.second_execution_date,
          merchant_id: this.orderForm.merchant_id,
        };
      }
      if (
        this.orderForm.execution_date ||
        this.orderForm.second_execution_date
      ) {
        this.$api.getOrderWarehouse(data).then((res) => {
          // 仓库信息
          this.orderForm.name = res.data.name;
          this.orderForm.phone = res.data.phone;
          this.orderForm.country = res.data.country;
          this.orderForm.post_code = res.data.post_code;
          this.orderForm.house_number = res.data.house_number;
          this.orderForm.city = res.data.city;
          this.orderForm.street = res.data.street;
          this.orderForm.address = res.data.address;
        });
      }
    },
    getDateChange() {
      if (this.orderForm.type == 1 || this.orderForm.type == 2) {
        this.getOrderWarehouse();
      }
    },
    getLocation(type) {
      // 拉取经纬度
      let post = null;
      let house = null;
      let city = null;
      let street = null;
      let country = null;
      if (type === 1) {
        // 发货人
        country = this.orderForm.second_place_country;
        post = this.orderForm.second_place_post_code;
        house = this.orderForm.second_place_house_number;
        city = this.orderForm.second_place_city;
        street = this.orderForm.second_place_street;
        // 判断国家显隐
        if (country == "" || country == null) {
          this.showSecondCountry = true;
        } else {
          this.showSecondCountry = false;
        }
      } else {
        // 收货人
        country = this.orderForm.place_country;
        post = this.orderForm.place_post_code;
        house = this.orderForm.place_house_number;
        city = this.orderForm.place_city;
        street = this.orderForm.place_street;
        if (country == "" || country == null) {
          this.showPlaceCountry = true;
        } else {
          this.showPlaceCountry = false;
        }
      }
      if (post && house && country === "NL") {
        let isNL = false;
        if (country === "NL") {
          this.lon = "";
          this.lat = "";
          isNL = true;
        }
        this.$api
          .getLocation({
            country: country,
            post_code: post,
            house_number: house,
          })
          .then((res) => {
            if (!res) {
              this.lon = "";
              this.lat = "";
              return;
            }
            this.lon = res.data.lon;
            this.lat = res.data.lat;
            if (isNL) {
              if (type === 1) {
                this.is_sender = this.$t("Sender");
                this.orderForm.second_place_city = res.data.city;
                this.orderForm.second_place_street = res.data.street;
                this.orderForm.second_place_lat = res.data.lat;
                this.orderForm.second_place_lon = res.data.lon;
                this.lon = res.data.lat;
                this.lat = res.data.lon;
                this.getOrderDate();
              } else {
                this.is_sender = this.$t("Receiver");
                this.orderForm.place_city = res.data.city;
                this.orderForm.place_street = res.data.street;
                this.orderForm.place_lat = res.data.lat;
                this.orderForm.place_lon = res.data.lon;
                this.lon = res.data.lat;
                this.lat = res.data.lon;
                this.getOrderDate();
              }
            }
          });
      } else if (post && house && country === "AU" && city && street) {
        let isNL = false;
        if (this.country === "AU") {
          this.lon = "";
          this.lat = "";
          isNL = true;
        }
        this.$api
          .getLocation({
            country: country,
            post_code: post,
            house_number: house,
            city: city,
            street: street,
          })
          .then((res) => {
            if (!res) {
              this.lon = "";
              this.lat = "";
              return;
            }
            this.lon = res.data.lon;
            this.lat = res.data.lat;
            if (isNL) {
              if (type === 1) {
                this.is_sender = this.$t("Sender");
                this.orderForm.second_place_lat = res.data.lat;
                this.orderForm.second_place_lon = res.data.lon;
                this.lon = res.data.lat;
                this.lat = res.data.lon;
                this.getOrderDate();
              } else {
                this.is_sender = this.$t("Receiver");
                this.orderForm.place_lat = res.data.lat;
                this.orderForm.place_lon = res.data.lon;
                this.lon = res.data.lat;
                this.lat = res.data.lon;
                this.getOrderDate();
              }
            }
          });
      } else if (post && house && city && street) {
        this.$api
          .getLocation({
            country: country,
            post_code: post,
            house_number: house,
            city: city,
            street: street,
          })
          .then((res) => {
            if (!res) {
              this.lon = "";
              this.lat = "";
              return;
            }
            this.lon = res.data.lon;
            this.lat = res.data.lat;
            if (type === 1) {
              this.is_sender = this.$t("Sender");
              this.orderForm.second_place_lat = res.data.lat;
              this.orderForm.second_place_lon = res.data.lon;
              this.lon = res.data.lat;
              this.lat = res.data.lon;
              this.getOrderDate();
            } else {
              this.is_sender = this.$t("Receiver");
              this.orderForm.place_lat = res.data.lat;
              this.orderForm.place_lon = res.data.lon;
              this.lon = res.data.lat;
              this.lat = res.data.lon;
              this.getOrderDate();
            }
          });
      }
    },
    // 合计
    sum() {
      let summation = 0;
      this.orderForm.bill_list.forEach((item) => {
        for (const key in item) {
          // if (key == 2 || key == 10 || key == 11) {
          //   continue;
          // } else {
          summation += Number(item[key]);
          // }
        }
      });
      const expect_total_amount =
        summation + Number(this.orderForm.settlement_amount);
      this.expect_total_amount = expect_total_amount.toFixed(2);
    },
    // 估算运价
    getOrderPriceCount() {
      let data = {};
      if (this.orderForm.type == 2) {
        data = {
          ...this.orderForm,
          lon: this.lon,
          lat: this.lat,
          package_list: JSON.stringify(this.orderForm.package_list),
          material_list: JSON.stringify(this.orderForm.material_list),
          bill_list: [],
          place_fullname: this.orderForm.second_place_fullname,
          place_phone: this.orderForm.second_place_phone,
          place_country: this.orderForm.second_place_country,
          place_post_code: this.orderForm.second_place_post_code,
          place_city: this.orderForm.second_place_city,
          place_street: this.orderForm.second_place_street,
          place_house_number: this.orderForm.second_place_house_number,
          execution_date: this.orderForm.second_execution_date,
        };
      } else {
        data = {
          ...this.orderForm,
          lon: this.lon,
          lat: this.lat,
          package_list: JSON.stringify(this.orderForm.package_list),
          material_list: JSON.stringify(this.orderForm.material_list),
          bill_list: [],
        };
      }
      this.$api.getOrderPriceCount(data).then((res) => {
        if (!res) return;
        // 给运费赋值
        if (res.data.settlement_amount == "" || null) {
          this.orderForm.settlement_amount = Number(0);
        } else {
          this.orderForm.settlement_amount = res.data.settlement_amount;
        }
        this.sum();
      });
    },
    // 继续开单
    confirmContinue(formName, estimate = true) {
      this.itemArr = null;
      this.materialArr = null;
      // this.orderAmountArr = null;
      const obj = JSON.parse(JSON.stringify(this.orderForm.bill_list[0]));
      const orderAmountType_list = JSON.parse(
        JSON.stringify(this.orderAmountType_list)
      );
      const arr = [];
      orderAmountType_list.map((item) => {
        const newobj = {};
        for (const key in obj) {
          if (item.id == key) {
            newobj.fee_id = item.id;
            newobj.expect_amount = Number(obj[key]);
            arr.push(newobj);
          }
        }
      });
      if (this.orderForm.package_list.length) {
        this.itemArr = JSON.stringify(this.orderForm.package_list);
      }
      if (this.orderForm.material_list.length) {
        this.materialArr = JSON.stringify(this.orderForm.material_list);
      }
      this.$refs[formName].validate((valid) => {
        if (valid) {
          if (this.$route.query.id) {
            this.putOrder();
            return "";
          }
          var data = {};
          // 给仓库赋值
          if (this.orderForm.type == 1) {
            data = {
              ...this.orderForm,
              lon: this.lon,
              lat: this.lat,
              package_list: this.itemArr,
              material_list: this.materialArr,
              expect_total_amount: this.expect_total_amount,
              bill_list: JSON.stringify(arr),
              second_place_fullname: this.orderForm.name,
              second_place_phone: this.orderForm.phone,
              second_place_post_code: this.orderForm.post_code,
              second_place_house_number: this.orderForm.house_number,
              second_place_city: this.orderForm.city,
              second_place_street: this.orderForm.street,
              second_place_address: this.orderForm.address,
            };
          } else if (this.orderForm.type == 2) {
            data = {
              ...this.orderForm,
              lon: this.lon,
              lat: this.lat,
              package_list: this.itemArr,
              material_list: this.materialArr,
              expect_total_amount: this.expect_total_amount,
              bill_list: JSON.stringify(arr),
              second_place_fullname: this.orderForm.name,
              second_place_phone: this.orderForm.phone,
              second_place_post_code: this.orderForm.post_code,
              second_place_house_number: this.orderForm.house_number,
              second_place_city: this.orderForm.city,
              second_place_street: this.orderForm.street,
              second_place_address: this.orderForm.address,
              place_fullname: this.orderForm.second_place_fullname,
              place_phone: this.orderForm.second_place_phone,
              place_post_code: this.orderForm.second_place_post_code,
              place_house_number: this.orderForm.second_place_house_number,
              place_city: this.orderForm.second_place_city,
              place_street: this.orderForm.second_place_street,
              place_address: this.orderForm.second_place_address,
              execution_date: this.orderForm.second_execution_date,
              place_lat: this.orderForm.second_place_lat,
              place_lon: this.orderForm.second_place_lon,
              second_execution_date: "",
            };
          } else {
            data = {
              ...this.orderForm,
              lon: this.lon,
              lat: this.lat,
              package_list: this.itemArr,
              material_list: this.materialArr,
              expect_total_amount: this.expect_total_amount,
              bill_list: JSON.stringify(arr),
            };
          }
          this.$api.addOrder(data).then((res) => {
            if (res.code == 200) {
              this.$store.commit("config/refreshRouter", Math.random());
            } else {
              this.$message.error(res.msg);
            }
            this.getOrder();
          });
        }
      });
    },
    // 提交订单
    confirm(formName, estimate = true) {
      if (this.orderForm.place_country == "") {
        this.showPlaceCountry = true;
      } else {
        this.showPlaceCountry = false;
      }

      if (this.orderForm.second_place_country == "") {
        this.showSecondCountry = true;
      } else {
        this.showSecondCountry = false;
      }
      this.itemArr = null;
      this.materialArr = null;
      const obj = JSON.parse(JSON.stringify(this.orderForm.bill_list[0]));
      const orderAmountType_list = JSON.parse(
        JSON.stringify(this.orderAmountType_list)
      );
      const arr = [];
      orderAmountType_list.map((item) => {
        const newobj = {};
        for (const key in obj) {
          if (item.id == key) {
            newobj.fee_id = item.id;
            newobj.expect_amount = Number(obj[key]);
            arr.push(newobj);
          }
        }
      });
      if (this.orderForm.package_list.length) {
        this.itemArr = JSON.stringify(this.orderForm.package_list);
      }
      if (this.orderForm.material_list.length) {
        this.materialArr = JSON.stringify(this.orderForm.material_list);
      }
      this.$refs[formName].validate((valid) => {
        if (valid) {
          if (this.$route.query.id) {
            this.putOrder();
            return "";
          }
          let data = {};
          // 给仓库赋值
          if (this.orderForm.type == 1) {
            data = {
              ...this.orderForm,
              lon: this.lon,
              lat: this.lat,
              package_list: this.itemArr,
              material_list: this.materialArr,
              expect_total_amount: this.expect_total_amount,
              bill_list: JSON.stringify(arr),
              second_place_fullname: this.orderForm.name,
              second_place_phone: this.orderForm.phone,
              second_place_country: this.orderForm.country,
              second_place_post_code: this.orderForm.post_code,
              second_place_house_number: this.orderForm.house_number,
              second_place_city: this.orderForm.city,
              second_place_street: this.orderForm.street,
              second_place_address: this.orderForm.address,
            };
          } else if (this.orderForm.type == 2) {
            data = {
              ...this.orderForm,
              lon: this.lon,
              lat: this.lat,
              package_list: this.itemArr,
              material_list: this.materialArr,
              expect_total_amount: this.expect_total_amount,
              bill_list: JSON.stringify(arr),
              second_place_fullname: this.orderForm.name,
              second_place_phone: this.orderForm.phone,
              second_place_country: this.orderForm.country,
              second_place_post_code: this.orderForm.post_code,
              second_place_house_number: this.orderForm.house_number,
              second_place_city: this.orderForm.city,
              second_place_street: this.orderForm.street,
              second_place_address: this.orderForm.address,
              place_fullname: this.orderForm.second_place_fullname,
              place_phone: this.orderForm.second_place_phone,
              place_country: this.orderForm.second_place_country,
              place_post_code: this.orderForm.second_place_post_code,
              place_house_number: this.orderForm.second_place_house_number,
              place_city: this.orderForm.second_place_city,
              place_street: this.orderForm.second_place_street,
              place_address: this.orderForm.second_place_address,
              execution_date: this.orderForm.second_execution_date,
              place_lat: this.orderForm.second_place_lat,
              place_lon: this.orderForm.second_place_lon,
              second_execution_date: "",
            };
          } else {
            data = {
              ...this.orderForm,
              lon: this.lon,
              lat: this.lat,
              package_list: this.itemArr,
              material_list: this.materialArr,
              expect_total_amount: this.expect_total_amount,
              bill_list: JSON.stringify(arr),
            };
          }
          this.$api.addOrder(data).then((res) => {
            this.printId = res.data.id;
            if (res.code == 200) {
              // 打印
              if (this.orderForm.checked == true) {
                this.$api
                  .printerOrder({ id_list: this.printId })
                  .then((res) => {
                    if (!res) return;
                    websocket(res.data);
                  });
              }
              this.$store.commit("config/refreshRouter", Math.random());
              this.$router.replace({
                name: "orderManagement",
                params: {
                  orderManagement: 1,
                },
              });
            } else {
              this.$message.error(res.msg);
            }
            this.getOrder();
          });
        }
      });
    },
    // 收货人
    setSender(merchantId) {
      this.is_sender = this.$t("Sender");
      this.show_receiver_address = true;
      this.pageParams.type = 2;
      this.getOrder(merchantId);
    },
    // 发货人
    setReceiver() {
      this.is_sender = this.$t("Receiver");
      this.show_receiver_address = true;
      this.pageParams.type = 1;
      this.getOrder();
    },
    getOrder() {
      // 列表
      this.pageParams.merchant_id = this.orderForm.merchant_id;
      // 收件人
      if (this.is_sender === this.$t("Receiver")) {
        this.$api.getReceiverAddress(this.pageParams).then((res) => {
          this.tableData = res.data.data;
          this.pageParams.total = res.data.meta.total;
        });
      } else {
        this.$api.getReceiverAddress(this.pageParams).then((res) => {
          this.tableData = res.data.data;
          this.pageParams.total = res.data.meta.total;
        });
      }
    },
    // 地址库选取联系人
    getTableItem(row) {
      // 发件人
      if (this.is_sender === this.$t("Sender")) {
        this.orderForm.second_place_fullname = row.place_fullname;
        this.orderForm.second_place_phone = row.place_phone;
        this.orderForm.second_place_country = row.short;
        this.orderForm.second_place_post_code = row.place_post_code;
        this.orderForm.second_place_house_number = row.place_house_number;
        this.orderForm.second_place_city = row.place_city;
        this.orderForm.second_place_street = row.place_street;
        this.orderForm.second_place_address = row.place_address;
        this.orderForm.second_place_lat = row.place_lat;
        this.orderForm.second_place_lon = row.place_lon;
        this.show_receiver_address = false;
        // if (this.orderForm.type == 1 || this.orderForm.type == 2) {
        this.lon = row.place_lat;
        this.lat = row.place_lon;
        this.getOrderDate();
        // }
      } else {
        this.orderForm.place_fullname = row.place_fullname;
        this.orderForm.place_phone = row.place_phone;
        this.orderForm.place_country = row.short;
        this.orderForm.place_post_code = row.place_post_code;
        this.orderForm.place_house_number = row.place_house_number;
        this.orderForm.place_city = row.place_city;
        this.orderForm.place_street = row.place_street;
        this.orderForm.place_address = row.place_address;
        this.lon = row.lon;
        this.lat = row.lat;
        this.orderForm.place_lat = row.place_lat;
        this.orderForm.place_lon = row.place_lon;
        this.show_receiver_address = false;
        this.lon = row.place_lat;
        this.lat = row.place_lon;
        // if (this.orderForm.type == 1 || this.orderForm.type == 2) {
        this.getOrderDate();
        // }
      }
    },
    // 订单详情
    getOrderDetails(id) {
      this.$api.getOrderDetails(id).then((res) => {
        const result = res.data;
        this.orderForm = result;
        console.log("result341344");
        console.log(result);
        if (
          result.settlement_type == null ||
          result.settlement_type == "" ||
          result.receipt_type == null
        ) {
          this.orderForm.receipt_type = 1;
          this.getMerchants(); //获取货主结算方式
        }
        this.expect_total_amount = result.expect_total_amount;
        this.lon = this.orderForm.lon;
        this.lat = this.orderForm.lat;
        if (
          res.data.status == 4 ||
          res.data.status == 3 ||
          +res.data.source == 3
        ) {
          this.isPickUp = true;
        }
        if (result.type == 1) {
          this.orderForm.name = result.second_place_fullname;
          this.orderForm.phone = result.second_place_phone;
          this.orderForm.country = result.second_place_country;
          this.orderForm.post_code = result.second_place_post_code;
          this.orderForm.house_number = result.second_place_house_number;
          this.orderForm.city = result.second_place_city;
          this.orderForm.street = result.second_place_street;
          this.orderForm.address = result.second_place_address;
        } else if (result.type == 2) {
          // 仓库
          this.orderForm.name = result.second_place_fullname;
          this.orderForm.phone = result.second_place_phone;
          this.orderForm.country = result.second_place_country;
          this.orderForm.post_code = result.second_place_post_code;
          this.orderForm.house_number = result.second_place_house_number;
          this.orderForm.city = result.second_place_city;
          this.orderForm.street = result.second_place_street;
          this.orderForm.address = result.second_place_address;
          // 收件人
          this.orderForm.second_place_fullname = result.place_fullname;
          this.orderForm.second_place_phone = result.place_phone;
          this.orderForm.second_place_country = result.place_country;
          this.orderForm.second_place_post_code = result.place_post_code;
          this.orderForm.second_place_house_number = result.place_house_number;
          this.orderForm.second_place_city = result.place_city;
          this.orderForm.second_place_street = result.place_street;
          this.orderForm.second_execution_date = result.execution_date;
        }
        // 第三方 数据
        if (result.source == 3 && this.orderForm.type == 2) {
          this.orderForm.second_place_fullname = result.place_fullname;
          this.orderForm.second_place_phone = result.place_phone;
          this.orderForm.second_place_country = result.place_country;
          this.orderForm.second_place_post_code = result.place_post_code;
          this.orderForm.second_place_house_number = result.place_house_number;
          this.orderForm.second_place_city = result.place_city;
          this.orderForm.second_place_street = result.place_street;
          this.orderForm.second_place_address = result.place_address;
        }
        if (this.orderForm.type == 1) {
          this.getOrderDate((this.is_sender = this.$t("Receiver")));
        } else if (this.orderForm.type == 2) {
          this.getOrderDate((this.is_sender = this.$t("Sender")));
        } else {
          this.getOrderDate((this.is_sender = this.$t("Receiver")));
        }
        if (result.create_date == null || result.create_date == "") {
          this.rules.create_date[0].required = false; //去掉必填项
        }
        // 运费
        let yobj = {
          object_type: 1,
          object_no: result.order_no,
        };
        this.$api.feeOrder(yobj).then((res) => {
          const bill_list = res.data.data;
          const orderAmountType_list = this.orderAmountType_list;
          // 判断orderAmountType_list =[{name:'基础运费'，id：1}] 和 接口 bill_list=[{type_name:'基础运费'，expect_amount：10}]
          // 为了得到 this.orderForm.bill_list = [{1:'10'}]
          const yunfeiobj = {};
          orderAmountType_list.forEach((item) => {
            bill_list.forEach((amountiten) => {
              if (item.name == amountiten.fee_name) {
                yunfeiobj[item.id] = amountiten.expect_amount;
              }
            });
          });
          // [yunfeiobj] = [{1:'10'}]
          const yunfeiarr = [yunfeiobj];
          // 把匹配好的指赋值给表格tabledata
          this.$set(this.orderForm, "bill_list", yunfeiarr);
        });
      });
    },
    // 编辑订单
    putOrder() {
      const obj = JSON.parse(JSON.stringify(this.orderForm.bill_list[0]));
      const orderAmountType_list = JSON.parse(
        JSON.stringify(this.orderAmountType_list)
      );
      const arr = [];
      orderAmountType_list.map((item) => {
        const newobj = {};
        for (const key in obj) {
          if (item.id == key) {
            newobj.fee_id = item.id;
            newobj.expect_amount = Number(obj[key]);
            arr.push(newobj);
          }
        }
      });
      let data = {};

      // 给仓库赋值
      if (this.orderForm.type == 1) {
        data = {
          ...this.orderForm,
          lon: this.lon,
          lat: this.lat,
          package_list: this.itemArr,
          material_list: this.materialArr,
          expect_total_amount: this.expect_total_amount,
          bill_list: JSON.stringify(arr),
          second_place_fullname: this.orderForm.name,
          second_place_phone: this.orderForm.phone,
          second_place_country: this.orderForm.country,
          second_place_post_code: this.orderForm.post_code,
          second_place_house_number: this.orderForm.house_number,
          second_place_city: this.orderForm.city,
          second_place_street: this.orderForm.street,
          second_place_address: this.orderForm.address,
        };
      } else if (this.orderForm.type == 2) {
        data = {
          ...this.orderForm,
          lon: this.lon,
          lat: this.lat,
          package_list: this.itemArr,
          material_list: this.materialArr,
          expect_total_amount: this.expect_total_amount,
          bill_list: JSON.stringify(arr),
          second_place_fullname: this.orderForm.name,
          second_place_phone: this.orderForm.phone,
          second_place_country: this.orderForm.country,
          second_place_post_code: this.orderForm.post_code,
          second_place_house_number: this.orderForm.house_number,
          second_place_city: this.orderForm.city,
          second_place_street: this.orderForm.street,
          second_place_address: this.orderForm.address,
          second_place_lat: this.orderForm.place_lat,
          second_place_lon: this.orderForm.place_lon,
          place_fullname: this.orderForm.second_place_fullname,
          place_phone: this.orderForm.second_place_phone,
          place_country: this.orderForm.second_place_country,
          place_post_code: this.orderForm.second_place_post_code,
          place_house_number: this.orderForm.second_place_house_number,
          place_city: this.orderForm.second_place_city,
          place_street: this.orderForm.second_place_street,
          place_address: this.orderForm.second_place_address,
          execution_date: this.orderForm.second_execution_date,
          place_lat: this.orderForm.second_place_lat,
          place_lon: this.orderForm.second_place_lon,
          second_execution_date: "",
        };
      } else {
        data = {
          ...this.orderForm,
          lon: this.lon,
          lat: this.lat,
          package_list: this.itemArr,
          material_list: this.materialArr,
          expect_total_amount: this.expect_total_amount,
          bill_list: JSON.stringify(arr),
        };
      }
      this.$api.putOrder(data).then((res) => {
        if (!res) return;
        // this.$router.go(-1);
        this.$router.push({
          name: "orderManagement",
          params: {
            orderManagement: 1,
          },
        });
      });
    },

    // 地图
    // 获取地图的定位信息
    handlerAddressSelected(data) {
      if (this.ReceiverMapDialog) {
        this.orderForm.place_country = data.country;
        this.orderForm.place_address = data.address;
        this.orderForm.place_post_code = data.postal_code;
        this.orderForm.place_house_number = data.street_number;
        this.orderForm.place_city = data.city;
        this.orderForm.place_street = `${data.route}`;
        this.orderForm.place_lat = data.location.lat;
        this.orderForm.place_lon = data.location.lng;
        this.lon = data.location.lng;
        this.lat = data.location.lat;
        this.getOrderDate((this.is_sender = this.$t("Receiver")));
      } else {
        this.orderForm.second_place_country = data.country;
        this.orderForm.second_place_address = data.address;
        this.orderForm.second_place_post_code = data.postal_code;
        this.orderForm.second_place_house_number = data.street_number;
        this.orderForm.second_place_city = data.city;
        this.orderForm.second_place_street = `${data.route}`;
        this.orderForm.second_place_lat = data.location.lat;
        this.orderForm.second_place_lon = data.location.lng;
        this.lon = data.location.lng;
        this.lat = data.location.lat;
        this.getOrderDate((this.is_sender = this.$t("Sender")));
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
    getColor(item) {
      if (item.id == 2 || item.id == 10 || item.id == 11) {
        return "feiyong2 ";
      }
      return "feiyong";
    },
  },
};
</script>

<style lang="less" scoped>
@import "../../less/public_variable.less";
.order-add-container {
  .box-card {
    margin-bottom: 20px;
    .general-info {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr 1fr;
    }
    .estimated-btn {
      background: #e6b550;
      border-color: #e6b550;
    }
    .box {
      display: grid;
      grid-template-columns: 250px 1fr;
    }
  }
  .box_title {
    color: #a1a1a1;
  }
  .express-style {
    width: 50%;
    display: inline-block;
  }
  .express-other {
    float: right;
  }
  .el-input-group__prepend {
    background-color: #fff;
    padding: 0 10px;
  }
  .el-icon-warning {
    color: #efb336;
  }
  .shipper {
    border: 1px solid #e4ebf1;
    height: 350px;
    /deep/ .el-input__inner {
      border: 0;
      border-bottom: 1px solid #d6d7d6;
    }
  }
  .receiving_party {
    text-align: center;
    margin-top: 60px;
    height: 350px;
    background-color: #f2f2f2;
    .receiving_party_box {
      padding-top: 130px;
      .receiving_party_box_title {
        margin-top: 20px;
        // font-size: 20px;
        // font-weight: ;
      }
    }
  }
  .save-btn {
    margin-top: 20px;
    text-align: right;
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
  .item-style {
    display: inline-block;
  }
  .item-left {
    width: 50%;
  }
  .item-bottom {
    margin-bottom: 30px;
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
  .tabLength {
    width: 150px !important;
  }
  // .input-style {
  //   .el-input {
  //     // width: 194px;
  //   }
  // }
  .pay_box {
    display: flex;
    flex-wrap: nowrap;
  }
  .locale-btn {
    color: #f6a50d !important;
    background-color: #fff !important;
    border-left: 1px solid #d6d7d6;
  }
  .estimate_price_text {
    border: 1px #000 solid;
    padding: 20px 0;
    text-align: center;
    background: #dcdfe6;
    border-radius: 4px;
    color: #000;
  }
  .feiyong {
    /deep/ .el-input__inner {
      color: red;
    }
  }
  .feiyong2 {
    /deep/ .el-input__inner {
      color: black;
    }
  }
  .base-fee {
    height: 65px;
    color: #646262;
    font-size: 15px;
    font-weight: 600;
    text-align: center;
    line-height: 65px;
    border: 1px solid #ebeef5;
  }
  .fee-box {
    border: 1px solid #ebeef5;
    height: 50px;
    border-top: 0;
    text-align: center;
    line-height: 50px;
  }
}
</style>
