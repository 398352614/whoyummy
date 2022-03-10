<template>
  <!-- 查看详情 -->
  <div class="viewDetails_box">
    <el-tabs type="border-card" class="border-card">
      <!-- 订单详情 -->
      <el-tab-pane :label="$t('orderDetails')">
        <div class="top">
          <div>
            <span style="font-size: 17px; font-weight: 700"
              >{{ $t("OrderNumber") }}:</span
            >
            <span style="font-size: 17px; font-weight: 700">{{
              order_no
            }}</span>
          </div>
          <div class="top-but">
            <!-- 作废 -->
            <el-tooltip
              :content="$t('AdministratorActivate')"
              placement="top"
              :disabled="hasPerm('order.destroy') ? true : false"
            >
              <div>
                <el-button
                  :disabled="!hasPerm('order.destroy')"
                  type="danger"
                  plain
                  size="mini"
                  @click="delOrder"
                  v-if="this.form.status == 1"
                  >{{ $t("void") }}</el-button
                >
              </div>
            </el-tooltip>
            <!-- 编辑 -->
            <el-tooltip
              :content="$t('AdministratorActivate')"
              placement="top"
              :disabled="hasPerm('order.update') ? true : false"
              class="top-edit"
            >
              <div>
                <el-button
                  :disabled="!hasPerm('order.update')"
                  type="success"
                  plain
                  size="mini"
                  @click.native="toEditOrder"
                  v-if="this.form.status == 1"
                  >{{ $t("Edit") }}
                </el-button>
              </div>
            </el-tooltip>
            <!-- 打印 -->
            <el-tooltip
              :content="$t('AdministratorActivate')"
              placement="top"
              :disabled="hasPerm('order.print') ? true : false"
            >
              <div>
                <el-button
                  :disabled="!hasPerm('order.print')"
                  type="info"
                  plain
                  size="mini"
                  @click="printerOrder"
                  >{{ $t("print") }}</el-button
                >
              </div>
            </el-tooltip>
          </div>
        </div>
        <div class="top_box">
          <div>
            <el-row :gutter="20">
              <el-col :span="6" style="text-align: right; margin-top: 10px">
              </el-col>
              <el-col :span="4" style="text-align: center"
                ><h4>
                  {{ this.type == 2 ? address : place_address }}
                </h4>
              </el-col>
              <el-col :span="2" style="text-align: center">
                <div>
                  <img src="../../assets/img/trucks.png" alt="" />
                </div>
                <div>
                  <img src="../../assets/img/arrows.png" alt="" width="60px" />
                </div>
              </el-col>
              <el-col :span="4" style="text-align: center"
                ><h4>
                  {{ this.type != 3 || 4 ? second_place_address : address }}
                </h4>
              </el-col>
              <!-- 订单状态图片  -->
              <el-col :span="8" style="text-align: left">
                <img
                  src="../../assets/img/order-img/pending.png"
                  alt=""
                  width="100px"
                  v-if="pending"
                />
                <img
                  src="../../assets/img/order-img/take-pie.png"
                  alt=""
                  width="100px"
                  v-if="takePie"
                />
                <img
                  src="../../assets/img/order-img/completed.png"
                  alt=""
                  width="100px"
                  v-if="completed"
                />
                <img
                  src="../../assets/img/order-img/take-defeat.png"
                  alt=""
                  width="100px"
                  v-if="takeDefeat"
                />
                <img
                  src="../../assets/img/order-img/recycle-bin.png"
                  alt=""
                  width="100px"
                  v-if="recycleBin"
                />
              </el-col>
            </el-row>
          </div>
          <div
            style="
              height: 100px;
              margin-top: 20px;
              line-height: 40px;
              margin-left: 10px;
            "
          >
            <el-row :gutter="20" style="margin-left: 20px">
              <el-col :span="8">
                <div>
                  <span>{{ $t("MerchantType") }}:</span>
                  <span>{{ type_name }} </span>
                </div>
                <div>
                  <span>{{ $t("itemNo") }}:</span>
                  <span>{{ out_order_no }} </span>
                </div>
                <!-- <div>
                  <span>运输方式：</span>
                  <span>{{ transport_mode_name }} </span>
                </div> -->
              </el-col>
              <el-col :span="8">
                <div>
                  <span>{{ $t("Merchant") }}:</span>
                  <span>{{ merchant_id_name }} </span>
                </div>
                <!-- <div>
                  <span>始发地：</span> <span>{{ origin_type_name }} </span>
                </div> -->
              </el-col>
              <el-col :span="8">
                <div>
                  <span>{{ $t("customerNumber") }} :</span>
                  <span>{{ out_user_id }} </span>
                </div>
              </el-col>
            </el-row>
          </div>
        </div>
        <!-- 收发货人信息 -->
        <div>
          <h3>{{ $t("senderInformation") }}</h3>
          <div>
            <el-row :gutter="20">
              <!-- 发件人 -->
              <el-col :span="12" v-if="this.type != 2">
                <div class="shipper">
                  <el-row>
                    <el-col
                      :span="3"
                      style="
                        height: 300px;
                        text-align: center;
                        background-color: #f3f2f2;
                      "
                    >
                      <div style="margin-top: 100px">
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
                    <el-col
                      :span="20"
                      style="
                        margin-top: 10px;
                        line-height: 40px;
                        margin-left: -20px;
                      "
                    >
                      <div class="underline-box">
                        <div style="display: flex">
                          <div class="underline-title">{{ $t("Sender") }}:</div>
                          <div class="underline-bottom">
                            {{ place_fullname }}
                          </div>
                        </div>
                        <div style="display: flex">
                          <div class="underline-title">{{ $t("Tel") }}:</div>
                          <div class="underline-bottom">
                            {{ place_phone }}
                          </div>
                        </div>
                      </div>
                      <div style="display: flex">
                        <div class="country-title">{{ $t("Country") }}:</div>
                        <div class="country-bottom">
                          {{ place_country }}
                        </div>
                      </div>

                      <div class="underline-box">
                        <div style="display: flex">
                          <div class="underline-title">
                            {{ $t("ZipCode") }}:
                          </div>
                          <div class="underline-bottom">
                            {{ place_post_code }}
                          </div>
                        </div>
                        <div style="display: flex">
                          <div class="underline-title">{{ $t("DoorNo") }}:</div>
                          <div class="underline-bottom">
                            {{ place_house_number }}
                          </div>
                        </div>
                      </div>
                      <div class="underline-box">
                        <div style="display: flex">
                          <div class="underline-title">{{ $t("City") }}:</div>
                          <div class="underline-bottom">
                            {{ place_city }}
                          </div>
                        </div>
                        <div style="display: flex">
                          <div class="underline-title">{{ $t("Street") }}:</div>
                          <div class="underline-bottom">
                            {{ place_street }}
                          </div>
                        </div>
                      </div>
                      <div>
                        <div style="display: flex">
                          <div class="country-title">
                            {{ $t("TimeRange") }}:
                          </div>
                          <div class="country-bottom">
                            {{ execution_date }}
                          </div>
                        </div>
                      </div>
                    </el-col>
                  </el-row>
                </div>
              </el-col>
              <!-- 仓库 -->
              <el-col :span="12" v-if="this.type != 3 && this.type != 4">
                <div class="shipper">
                  <el-row>
                    <el-col
                      :span="3"
                      style="
                        height: 300px;
                        text-align: center;
                        background-color: #f3f2f2;
                      "
                    >
                      <div style="margin-top: 100px">
                        <img
                          src="../../assets/img/warehouse.png"
                          alt=""
                          width="40px"
                        />
                        <div style="margin-top: 20px">
                          {{ $t("Warehouse") }}
                        </div>
                      </div>
                    </el-col>
                    <el-col
                      :span="20"
                      style="
                        margin-top: 10px;
                        line-height: 40px;
                        margin-left: -20px;
                      "
                    >
                      <div style="display: flex">
                        <div class="country-title">
                          {{ $t("WarehouseName") }}:
                        </div>
                        <div class="country-bottom">{{ name }}</div>
                      </div>

                      <div style="display: flex">
                        <div class="country-title">{{ $t("Tel") }}:</div>
                        <div class="country-bottom">{{ phone }}</div>
                      </div>
                      <div style="display: flex">
                        <div class="country-title">{{ $t("Country") }}:</div>
                        <div class="country-bottom">
                          {{ country }}
                        </div>
                      </div>
                      <div class="underline-box">
                        <div style="display: flex">
                          <div class="underline-title">
                            {{ $t("ZipCode") }}:
                          </div>
                          <div class="underline-bottom">{{ post_code }}</div>
                        </div>
                        <div style="display: flex; margin-left: 30px">
                          <div class="underline-title">{{ $t("DoorNo") }}:</div>
                          <div class="underline-bottom">{{ house_number }}</div>
                        </div>
                      </div>
                      <div class="underline-box">
                        <div style="display: flex">
                          <div class="underline-title">{{ $t("City") }}:</div>
                          <div class="underline-bottom">{{ city }}</div>
                        </div>
                        <div style="display: flex; margin-left: 30px">
                          <div class="underline-title">{{ $t("Street") }}:</div>
                          <div class="underline-bottom">{{ street }}</div>
                        </div>
                      </div>
                      <!-- <div>
                        <div style="display: flex">
                          <div class="underline-title">
                            取件时间:
                          </div>
                          <div class="underline-bottom">{{1111}}</div>
                        </div>
                      </div> -->
                    </el-col>
                  </el-row>
                </div>
              </el-col>
              <!-- 收件人 -->
              <el-col
                :span="12"
                v-if="this.type == 2 || this.type == 3 || this.type == 4"
              >
                <div class="shipper">
                  <el-row>
                    <el-col
                      :span="3"
                      style="
                        height: 300px;
                        text-align: center;
                        background-color: #f3f2f2;
                      "
                    >
                      <div style="margin-top: 100px">
                        <img
                          src="../../assets/img/unload.png"
                          alt=""
                          width="40px"
                        />
                        <div style="margin-top: 20px">
                          {{ $t("unloadingPlace") }}
                        </div>
                      </div>
                    </el-col>
                    <el-col
                      :span="20"
                      style="
                        margin-top: 10px;
                        line-height: 40px;
                        margin-left: -20px;
                      "
                    >
                      <div class="underline-box">
                        <div style="display: flex">
                          <div class="underline-title">
                            {{ $t("Receiver") }}:
                          </div>
                          <div class="underline-bottom">
                            {{ second_place_fullname }}
                          </div>
                        </div>

                        <div style="display: flex">
                          <div class="underline-title">{{ $t("Tel") }}:</div>
                          <div class="underline-bottom">
                            {{ second_place_phone }}
                          </div>
                        </div>
                      </div>
                      <div style="display: flex">
                        <div class="country-title">{{ $t("Country") }}:</div>
                        <div class="country-bottom">
                          {{ second_place_country }}
                        </div>
                      </div>
                      <div class="underline-box">
                        <div style="display: flex">
                          <div class="underline-title">
                            {{ $t("ZipCode") }}:
                          </div>
                          <div class="underline-bottom">
                            {{ second_place_post_code }}
                          </div>
                        </div>
                        <div style="display: flex">
                          <div class="underline-title">{{ $t("DoorNo") }}:</div>
                          <div class="underline-bottom">
                            {{ second_place_house_number }}
                          </div>
                        </div>
                      </div>
                      <div class="underline-box">
                        <div style="display: flex">
                          <div class="underline-title">{{ $t("City") }}:</div>
                          <div class="underline-bottom">
                            {{ second_place_city }}
                          </div>
                        </div>
                        <div style="display: flex">
                          <div class="underline-title">{{ $t("Street") }}:</div>
                          <div class="underline-bottom">
                            {{ second_place_street }}
                          </div>
                        </div>
                      </div>
                      <div>
                        <div style="display: flex">
                          <div class="country-title">
                            {{ $t("DispatchTime") }}:
                          </div>
                          <div class="country-bottom" v-if="this.type != 2">
                            {{ second_execution_date }}
                          </div>
                          <div class="country-bottom" v-if="this.type == 2">
                            {{ execution_date }}
                          </div>
                        </div>
                      </div>
                    </el-col>
                  </el-row>
                </div>
              </el-col>
            </el-row>
          </div>
        </div>
        <!-- 货物信息 -->
        <div>
          <h3>{{ $t("ProductInfo") }}</h3>
          <h4>{{ $t("package") }}</h4>
          <el-table
            max-height="600px"
            :data="package_list"
            border
            style="width: 100%"
          >
            <el-table-column prop="name" :label="$t('PackageName')">
            </el-table-column>
            <el-table-column prop="express_first_no" :label="$t('parcelNo')">
            </el-table-column>
            <!-- <el-table-column prop="expect_quantity" :label="$t('数量')">
            </el-table-column> -->
            <el-table-column
              prop="weight"
              :label="$t('Weight') + '(' + weight_unit_symbol + ')'"
            >
            </el-table-column>
            <el-table-column prop="feature_logo" :label="$t('nature')">
            </el-table-column>
            <el-table-column
              prop="expiration_date"
              :label="$t('LatestDeliveryDate')"
            >
            </el-table-column>
            <el-table-column prop="remark" :label="$t('Remark')">
            </el-table-column>
          </el-table>
          <h4>{{ $t("cargo") }}</h4>
          <el-table
            max-height="600px"
            :data="material_list"
            border
            style="width: 100%"
          >
            <el-table-column prop="name" :label="$t('ProductName')">
            </el-table-column>
            <el-table-column prop="expect_quantity" :label="$t('Quantity')">
            </el-table-column>
            <el-table-column
              prop="weight"
              :label="$t('Weight') + '(' + weight_unit_symbol + ')'"
            >
            </el-table-column>
            <el-table-column
              prop="size"
              :label="$t('Volume') + '(' + volume_unit_symbol + ')'"
            >
            </el-table-column>
            <el-table-column prop="type_name" :label="$t('MerchantType')">
            </el-table-column>
            <el-table-column prop="pack_type_name" :label="$t('cargoPackage')">
            </el-table-column>
            <el-table-column
              prop="unit_price"
              :label="$t('Price') + '(' + currency_unit_symbol + ')'"
            >
            </el-table-column>
          </el-table>
        </div>

        <!-- 费用信息 -->
        <div>
          <div class="fee-box">
            <div>
              <h4 class="box_title">{{ $t("BaseFee") }}</h4>
              <div>
                <div class="base-fee">
                  {{ $t("freigheCost") }}
                  {{ "(" + currency_unit_symbol + ")" }}
                </div>
                <div class="fee">
                  {{ settlement_amount }}
                </div>
              </div>
            </div>
            <div>
              <h4 class="box_title">{{ $t("valueaddedServices") }}</h4>
              <el-table
                max-height="600px"
                :data="[{}]"
                border
                style="width: 100%"
              >
                <el-table-column
                  prop="expect_amount"
                  :label="item.fee_name + '(' + currency_unit_symbol + ')'"
                  v-for="(item, index) in amount_list"
                  :key="index"
                >
                  <template>
                    {{ item.expect_amount }}
                  </template>
                </el-table-column>
              </el-table>
            </div>
          </div>
        </div>
        <el-row :gutter="20">
          <el-col :span="18">
            <h3 class="box_title">{{ $t("Optional") }}</h3>
            <div style="margin-top: 20px">
              <span>{{ $t("SpecialReminder") }}</span>
              <el-tooltip effect="dark" :content="$t('Itwill')" placement="top">
                <span class="el-icon-warning icon-question"></span> </el-tooltip
              >:
              <span>{{ special_remark }} </span>
            </div>
            <div style="margin-top: 20px">
              <span>{{ $t("maskCode") }}:</span>
              <span>{{ mask_code }} </span>
            </div>
          </el-col>
          <el-col :span="6">
            <h3 class="box_title">{{ $t("settlementInfo") }}</h3>
            <div style="margin-top: 20px">
              <span>{{ $t("totalFreightCost") }}:</span>
              <span style="color: red">{{ expect_total_amount }} </span>
            </div>
            <div style="margin-top: 20px">
              <span>{{ $t("paymentMethod") }}:</span>
              <span>{{ settlement_type_name }} </span>
            </div>
          </el-col>
        </el-row>
      </el-tab-pane>
      <!-- 运单信息 -->
      <el-tab-pane :label="$t('waybillInfo')">
        <!-- 收发货人信息 -->
        <div>
          <h3>{{ $t("senderInformation") }}</h3>
          <div>
            <el-row :gutter="20">
              <!-- 发件人 -->
              <el-col :span="12" v-if="this.type != 2">
                <div class="shipper">
                  <el-row>
                    <el-col
                      :span="3"
                      style="
                        height: 300px;
                        text-align: center;
                        background-color: #f3f2f2;
                      "
                    >
                      <div style="margin-top: 100px">
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
                    <el-col
                      :span="20"
                      style="
                        margin-top: 10px;
                        line-height: 40px;
                        margin-left: -20px;
                      "
                    >
                      <div class="underline-box">
                        <div style="display: flex">
                          <div class="underline-title">{{ $t("Sender") }}:</div>
                          <div class="underline-bottom">
                            {{ place_fullname }}
                          </div>
                        </div>

                        <div style="display: flex">
                          <div class="underline-title">{{ $t("Tel") }}:</div>
                          <div class="underline-bottom">
                            {{ place_phone }}
                          </div>
                        </div>
                      </div>
                      <div style="display: flex">
                        <div class="country-title">{{ $t("Country") }}:</div>
                        <div class="country-bottom">
                          {{ place_country }}
                        </div>
                      </div>
                      <div class="underline-box">
                        <div style="display: flex">
                          <div class="underline-title">
                            {{ $t("ZipCode") }}:
                          </div>
                          <div class="underline-bottom">
                            {{ place_post_code }}
                          </div>
                        </div>
                        <div style="display: flex">
                          <div class="underline-title">{{ $t("DoorNo") }}:</div>
                          <div class="underline-bottom">
                            {{ place_house_number }}
                          </div>
                        </div>
                      </div>
                      <div class="underline-box">
                        <div style="display: flex">
                          <div class="underline-title">{{ $t("City") }}:</div>
                          <div class="underline-bottom">
                            {{ place_city }}
                          </div>
                        </div>
                        <div style="display: flex">
                          <div class="underline-title">{{ $t("Street") }}:</div>
                          <div class="underline-bottom">
                            {{ place_street }}
                          </div>
                        </div>
                      </div>
                      <div>
                        <div style="display: flex">
                          <div class="country-title">
                            {{ $t("TimeRange") }}:
                          </div>
                          <div class="country-bottom">
                            {{ execution_date }}
                          </div>
                        </div>
                      </div>
                    </el-col>
                  </el-row>
                </div>
              </el-col>
              <!-- 仓库 -->
              <el-col :span="12" v-if="this.type != 3 && this.type != 4">
                <div class="shipper">
                  <el-row>
                    <el-col
                      :span="3"
                      style="
                        height: 300px;
                        text-align: center;
                        background-color: #f3f2f2;
                      "
                    >
                      <div style="margin-top: 100px">
                        <img
                          src="../../assets/img/warehouse.png"
                          alt=""
                          width="40px"
                        />
                        <div style="margin-top: 20px">
                          {{ $t("Warehouse") }}
                        </div>
                      </div>
                    </el-col>
                    <el-col
                      :span="20"
                      style="
                        margin-top: 10px;
                        line-height: 40px;
                        margin-left: -20px;
                      "
                    >
                      <div style="display: flex">
                        <div class="country-title">
                          {{ $t("WarehouseName") }}:
                        </div>
                        <div class="country-bottom">{{ name }}</div>
                      </div>

                      <div style="display: flex">
                        <div class="country-title">{{ $t("Tel") }}:</div>
                        <div class="country-bottom">{{ phone }}</div>
                      </div>
                      <div style="display: flex">
                        <div class="country-title">{{ $t("Country") }}:</div>
                        <div class="country-bottom">
                          {{ country }}
                        </div>
                      </div>
                      <div class="underline-box">
                        <div style="display: flex">
                          <div class="underline-title">
                            {{ $t("ZipCode") }}:
                          </div>
                          <div class="underline-bottom">{{ post_code }}</div>
                        </div>
                        <div style="display: flex; margin-left: 30px">
                          <div class="underline-title">{{ $t("DoorNo") }}:</div>
                          <div class="underline-bottom">{{ house_number }}</div>
                        </div>
                      </div>
                      <div class="underline-box">
                        <div style="display: flex">
                          <div class="underline-title">{{ $t("City") }}:</div>
                          <div class="underline-bottom">{{ city }}</div>
                        </div>
                        <div style="display: flex; margin-left: 30px">
                          <div class="underline-title">{{ $t("Street") }}:</div>
                          <div class="underline-bottom">{{ street }}</div>
                        </div>
                      </div>
                      <!-- <div>
                        <div style="display: flex">
                          <div class="underline-title">
                            取件时间:
                          </div>
                          <div class="underline-bottom">{{1111}}</div>
                        </div>
                      </div> -->
                    </el-col>
                  </el-row>
                </div>
              </el-col>
              <!-- 收件人 -->
              <el-col
                :span="12"
                v-if="this.type == 2 || this.type == 3 || this.type == 4"
              >
                <div class="shipper">
                  <el-row>
                    <el-col
                      :span="3"
                      style="
                        height: 300px;
                        text-align: center;
                        background-color: #f3f2f2;
                      "
                    >
                      <div style="margin-top: 100px">
                        <img
                          src="../../assets/img/unload.png"
                          alt=""
                          width="40px"
                        />
                        <div style="margin-top: 20px">
                          {{ $t("unloadingPlace") }}
                        </div>
                      </div>
                    </el-col>
                    <el-col
                      :span="20"
                      style="
                        margin-top: 10px;
                        line-height: 40px;
                        margin-left: -20px;
                      "
                    >
                      <div class="underline-box">
                        <div style="display: flex">
                          <div class="underline-title">
                            {{ $t("Receiver") }}:
                          </div>
                          <div class="underline-bottom">
                            {{ second_place_fullname }}
                          </div>
                        </div>

                        <div style="display: flex">
                          <div class="underline-title">{{ $t("Tel") }}:</div>
                          <div class="underline-bottom">
                            {{ second_place_phone }}
                          </div>
                        </div>
                      </div>
                      <div style="display: flex">
                        <div class="country-title">{{ $t("Country") }}:</div>
                        <div class="country-bottom">
                          {{ second_place_country }}
                        </div>
                      </div>
                      <div class="underline-box">
                        <div style="display: flex">
                          <div class="underline-title">
                            {{ $t("ZipCode") }}:
                          </div>
                          <div class="underline-bottom">
                            {{ second_place_post_code }}
                          </div>
                        </div>
                        <div style="display: flex; margin-left: 30px">
                          <div class="underline-title">{{ $t("DoorNo") }}:</div>
                          <div class="underline-bottom">
                            {{ second_place_house_number }}
                          </div>
                        </div>
                      </div>
                      <div class="underline-box">
                        <div style="display: flex">
                          <div class="underline-title">{{ $t("City") }}:</div>
                          <div class="underline-bottom">
                            {{ second_place_city }}
                          </div>
                        </div>
                        <div style="display: flex; margin-left: 30px">
                          <div class="underline-title">{{ $t("Street") }}:</div>
                          <div class="underline-bottom">
                            {{ second_place_street }}
                          </div>
                        </div>
                      </div>
                      <div>
                        <div style="display: flex">
                          <div class="country-title">
                            {{ $t("DispatchTime") }}:
                          </div>
                          <div class="country-bottom" v-if="this.type != 2">
                            {{ second_execution_date }}
                          </div>
                          <div class="country-bottom" v-if="this.type == 2">
                            {{ execution_date }}
                          </div>
                        </div>
                      </div>
                    </el-col>
                  </el-row>
                </div>
              </el-col>
            </el-row>
          </div>
        </div>
        <!-- 运单 -->
        <div v-for="(item, index) in viewDetailsList" :key="index">
          <div
            style="
              height: 50px;
              background-color: #f3f2f2;
              margin-top: 20px;
              line-height: 50px;
            "
          >
            <div style="margin-left: 30px">
              <el-row :gutter="20">
                <el-col :span="12">
                  <span> {{ item.type_name }}:</span>
                  <span>{{ item.tracking_order_no }}</span></el-col
                >
                <el-col :span="12">{{ item.status_name }}</el-col>
              </el-row>
            </div>
          </div>
          <div>
            <el-row :gutter="20" style="margin-top: 30px">
              <el-col :span="10" style="margin-left: 30px">
                <div style="margin-bottom: 20px">
                  <span style="color: #7f7f7f">{{ $t("lineTask") }}:</span>
                  <span>{{ item.tour_no }} </span>
                </div>
                <div style="margin-bottom: 20px">
                  <span style="color: #7f7f7f">{{ $t("siteTask") }}:</span>
                  <span>{{ item.batch_no }} </span>
                </div>
                <div style="margin-bottom: 20px">
                  <span style="color: #7f7f7f">{{ $t("carManagement") }}:</span>
                  <span> {{ item.car_no }} </span>
                </div>
                <div style="margin-bottom: 20px">
                  <span style="color: #7f7f7f">{{ $t("Driver") }}:</span>
                  <span>{{ item.driver_name }} </span>
                </div>
                <div style="margin-bottom: 20px">
                  <span style="color: #7f7f7f">{{ $t("Tel") }}:</span>
                  <span>{{ item.driver_phone }} </span>
                </div>
              </el-col>
              <el-col :span="12">
                <div>
                  <el-timeline>
                    <el-timeline-item
                      v-for="(activity, ceindex) in viewDetailsList[index]
                        .time_list"
                      :key="ceindex"
                      :icon="activity.icon"
                      :type="activity.type"
                      :color="activity.color"
                      :size="activity.size"
                      :timestamp="activity.timestamp"
                    >
                      <el-row :gutter="20">
                        <el-col :span="12" style="">{{ activity.type }}</el-col>
                        <el-col :span="12">{{ activity.time }}</el-col>
                      </el-row>
                    </el-timeline-item>
                  </el-timeline>
                </div></el-col
              >
            </el-row>
          </div>
          <!-- 货物信息 -->
          <div>
            <h3>{{ $t("ProductInfo") }}</h3>
            <div>
              <h4>{{ $t("package") }}</h4>
              <el-table
                max-height="600px"
                :data="item.package_list"
                border
                style="width: 100%"
              >
                <el-table-column
                  prop="name"
                  :label="$t('PackageName')"
                  align="center"
                >
                </el-table-column>
                <el-table-column
                  prop="express_first_no"
                  :label="$t('parcelNo')"
                  align="center"
                >
                </el-table-column>
                <el-table-column
                  prop="out_order_no"
                  :label="$t('ExternalIdentification')"
                  align="center"
                >
                </el-table-column>
                <el-table-column
                  prop="address"
                  :label="$t('waybillPrintingNo')"
                  align="center"
                >
                </el-table-column
                ><el-table-column
                  prop="status_name"
                  :label="$t('Status')"
                  align="center"
                >
                </el-table-column>
              </el-table>
            </div>
            <div>
              <h4>{{ $t("cargo") }}</h4>
              <el-table
                max-height="600px"
                :data="item.material_list"
                border
                style="width: 100%"
              >
                <el-table-column
                  prop="name"
                  :label="$t('ProductName')"
                  align="center"
                >
                </el-table-column>
                <el-table-column
                  prop="expect_quantity"
                  :label="$t('EstimatedQuantity')"
                  align="center"
                >
                </el-table-column>
                <el-table-column
                  prop="expect_quantity"
                  :label="$t('theActualAmount')"
                  align="center"
                >
                </el-table-column>
                <el-table-column
                  prop="out_order_no"
                  :label="$t('ExternalIdentification')"
                  align="center"
                >
                </el-table-column>
                <!-- <el-table-column
                  prop="status_name"
                  label="状态"
                  align="center"
                >
                </el-table-column> -->
              </el-table>
            </div>
          </div>
        </div>
      </el-tab-pane>
      <!-- 物流轨迹 -->
      <el-tab-pane
        :label="$t('logisticsTrajectory')"
        v-if="hasPerm('order.tracking-order-trail')"
      >
        <!-- <trajectory-map></trajectory-map> -->
        <div v-for="(item, index) in waybillTrail_list" :key="index">
          <div v-if="item.tracking_order_trail != ''">
            <div
              class="google-map"
              v-if="
                item.tracking_order_trail != [] &&
                item.tracking_order_trail[0].type_name == '在途'
              "
            >
              <gmap-map
                ref="gmap"
                id="gmap"
                :noClear="false"
                :center="center"
                :zoom="center.zoom"
                class="map"
              >
                <gmap-marker
                  :key="index"
                  v-for="(m, index) in markers"
                  :icon="m.icon"
                  :position="m.position"
                >
                </gmap-marker>
                <gmap-polyline
                  :path.sync="polylinePath"
                  v-bind:options="{ strokeColor: '#60b1e9' }"
                >
                </gmap-polyline>
              </gmap-map>
              <div class="box">
                <div class="box-title">
                  {{ $t("dispatchEstimatedArrival") }}
                  {{ item.expect_arrive_time }}
                </div>
              </div>
            </div>
            <div
              style="
                height: 50px;
                background-color: #f3f2f2;
                margin-top: 20px;
                line-height: 50px;
                padding-left: 30px;
              "
            >
              <el-row :gutter="20">
                <el-col :span="7">
                  <span>{{ item.type_name }} :</span>
                  <span>{{ item.tracking_order_no }} </span></el-col
                >
                <el-col :span="10">
                  {{ creationTime }}
                </el-col>
                <el-col :span="7">
                  {{ item.tracking_order_trail[0].type_name }}
                </el-col>
              </el-row>
            </div>
          </div>

          <div style="margin-top: 20px">
            <!-- <div>
              {{ activity.content }}
            </div> -->
            <el-timeline>
              <el-timeline-item
                v-for="(activity, ecindex) in waybillTrail_list[index]
                  .tracking_order_trail"
                :key="ecindex"
                :icon="activity.icon"
                :type="activity.type"
                :color="getColor(ecindex)"
                :size="activity.size"
              >
                <!-- :timestamp="activity.timestamp" -->
                <el-row :gutter="20">
                  <el-col :span="4">{{ activity.type_name }}</el-col>
                  <el-col :span="7" style="text-align: left">{{
                    activity.timestamp
                  }}</el-col>
                  <el-col :span="6">{{ activity.content }}</el-col>
                  <el-col :span="6" style="text-align: right">
                    <!-- 物流轨迹删除 -->
                    <el-tooltip
                      :content="$t('AdministratorActivate')"
                      placement="top"
                      :disabled="
                        hasPerm('order.tracking-order-trail-destroy')
                          ? true
                          : false
                      "
                    >
                      <div>
                        <el-button
                          :disabled="
                            !hasPerm('order.tracking-order-trail-destroy')
                          "
                          type="text"
                          style="color: red"
                          size="small"
                          @click="delLogistics(activity.id)"
                          >{{ $t("Delete") }}</el-button
                        >
                      </div>
                    </el-tooltip>
                  </el-col>
                </el-row>
              </el-timeline-item>
            </el-timeline>
          </div>
        </div>
      </el-tab-pane>
      <!-- 财务记录 -->
      <el-tab-pane :label="$t('financeRecord')" v-if="hasPerm('order.amount')">
        <div>
          <!-- <div style="float: right; margin-bottom: 20px">
            费用新增
            <el-tooltip
              :content="$t('AdministratorActivate')"
              placement="top"
              :disabled="hasPerm('order.amount-store') ? true : false"
            >
              <div>
                <el-button
                  :disabled="!hasPerm('order.amount-store')"
                  size="small"
                  @click="handlerAddClick"
                  :loading="this.$store.state.config.button_loading"
                  type="primary"
                  style="margin-bottom: 15px"
                  >{{ $t("Add") }}</el-button
                >
              </div>
            </el-tooltip>
          </div> -->
          <div>
            <el-table
              max-height="600px"
              :data="tableData"
              border
              style="width: 100%"
              :summary-method="getSummaries"
              show-summary
            >
              <el-table-column
                type="index"
                align="center"
                width="50px"
              ></el-table-column>
              <el-table-column
                prop="fee_name"
                :label="$t('statements')"
                align="center"
              >
              </el-table-column>
              <el-table-column
                prop="expect_amount"
                :label="$t('fees')"
                align="center"
              >
              </el-table-column>
              <el-table-column
                prop="remark"
                :label="$t('Remark')"
                align="center"
              >
              </el-table-column>
              <el-table-column
                prop="created_at"
                :label="$t('Created')"
                align="center"
              >
              </el-table-column>
              <!-- 操作 -->
              <!-- <el-table-column
                :label="$t('Operation')"
                width="130"
                align="center"
              >
                <template slot-scope="scope" class="cell">
                  费用编辑
                  <el-tooltip
                    :content="$t('AdministratorActivate')"
                    placement="top"
                    :disabled="hasPerm('order.amount-update') ? true : false"
                  >
                    <div>
                      <el-button
                        :disabled="!hasPerm('order.amount-update')"
                        @click="putView(scope.row)"
                        type="primary"
                        size="small"
                        icon="el-icon-edit"
                      ></el-button>
                    </div>
                  </el-tooltip>
                  费用删除
                  <el-tooltip
                    style="margin-left: 10px"
                    :content="$t('AdministratorActivate')"
                    placement="top"
                    :disabled="hasPerm('order.amount-destroy') ? true : false"
                  >
                    <div>
                      <el-button
                        :disabled="!hasPerm('order.amount-destroy')"
                        type="danger"
                        size="small"
                        @click="delView(scope.row.id)"
                        icon="el-icon-delete"
                      ></el-button>
                    </div>
                  </el-tooltip>
                </template>
              </el-table-column> -->
            </el-table>
          </div>
        </div>
      </el-tab-pane>
      <!-- 客服记录 -->
      <el-tab-pane
        :label="$t('customerServiceRecords')"
        v-if="hasPerm('order.customer')"
      >
        <el-form :model="serviceForm" ref="form">
          <div style="background-color: #f9f9f9; height: 180px; margin: 40px">
            <el-row :gutter="20">
              <el-col :span="2" style="text-align: right">
                <div style="margin: 30px 0px">
                  <img src="../../assets/img/headPortrait.png" alt="" />
                  <div style="margin-top: 20px; padding-left: 20px">
                    {{ fullname }}
                  </div>
                </div>
              </el-col>
              <el-col :span="10">
                <div style="margin: 20px 0px">
                  <el-form-item label="">
                    <el-input
                      type="textarea"
                      :rows="4"
                      :placeholder="$t('enterContent')"
                      v-model="serviceForm.content"
                    >
                    </el-input>
                  </el-form-item>
                </div>
                <!-- 发送 -->
                <el-tooltip
                  :content="$t('AdministratorActivate')"
                  placement="top"
                  :disabled="hasPerm('order.create-customer') ? true : false"
                >
                  <div>
                    <el-button
                      :disabled="!hasPerm('order.create-customer')"
                      size="small"
                      @click="AddClick"
                      :loading="this.$store.state.config.button_loading"
                      type="primary"
                      style="margin-bottom: 15px"
                      >{{ $t("Fasong") }}</el-button
                    >
                  </div>
                </el-tooltip>
              </el-col>
              <el-col :span="3" style="margin-top: 50px">
                <el-form-item>
                  <el-upload
                    :headers="Authorization"
                    :before-upload="beforeAvatarUpload"
                    :on-success="handleServiceImgSuccess"
                    :action="BaseApiFile"
                    :on-remove="handlerClearLisence"
                    :file-list="fileList"
                    name="file"
                    :data="{
                      dir: 'driver',
                    }"
                  >
                    <el-button
                      type="primary"
                      plain
                      icon="el-icon-picture"
                      size="small"
                    >
                      <i clas="el-icon-picture"></i>
                      {{ $t("uploadImage") }}</el-button
                    >
                  </el-upload>
                </el-form-item>
              </el-col>
              <el-col :span="3" style="margin-top: 50px; text-align: left">
                <el-form-item>
                  <el-upload
                    :headers="Authorization"
                    :before-upload="beforeAvatarUpload"
                    :on-success="handleServiceSuccess"
                    :action="BaseApiFile"
                    :on-remove="handlerClearLisence"
                    :file-list="fileList"
                    name="file"
                    :data="{
                      dir: 'driver',
                    }"
                  >
                    <el-button
                      type="primary"
                      plain
                      size="small"
                      icon="el-icon-document-checked"
                      >{{ $t("uploadFiles") }}
                    </el-button>
                  </el-upload>
                </el-form-item>
              </el-col>
              <el-col :span="6" style="margin-top: 40px">
                <div>1、{{ $t("uploadImageTitle") }}</div>
                <div>2、{{ $t("fileUploadFormat") }}</div>
              </el-col>
            </el-row>
          </div>
        </el-form>

        <div style="margin: 40px">
          <h3 style="margin-bottom: 40px">
            {{ $t("customerServiceRecords") }}
          </h3>
          <div
            style="
              border-bottom: 1px dashed #a0a0a0;
              height: 230px;
              margin-bottom: 30px;
              margin-left: 40px;
            "
            v-for="(item, index) in Customer_list"
            :key="index"
          >
            <el-row :gutter="20">
              <el-col :span="16">
                <img
                  src="../../assets/img/headPortrait.png"
                  alt=""
                  style="float: left"
                />
                <div style="float: left; margin: 15px">
                  {{ item.fullname }}
                </div>
                <div style="padding-left: 15px">
                  <div style="clear: both; color: #999; padding-top: 10px">
                    {{ item.content }}
                  </div>
                  <div v-for="imgItem in item.picture_urls" :key="imgItem">
                    <img
                      :src="imgItem"
                      alt=""
                      style="
                        float: left;
                        width: 100px;
                        height: 100px;
                        margin: 30px 10px;
                      "
                    />
                  </div>
                  <div v-for="fileItem in item.file_urls" :key="fileItem">
                    <img
                      :src="fileItem"
                      alt=""
                      style="
                        float: left;
                        width: 100px;
                        height: 100px;
                        margin: 30px 10px;
                      "
                    />
                  </div>
                </div>
              </el-col>
              <el-col :span="5">
                <div style="color: #999">
                  {{ item.created_at }}
                </div>
              </el-col>
              <!-- 删除客服记录 -->
              <el-col :span="3">
                <el-tooltip
                  :content="$t('AdministratorActivate')"
                  placement="top"
                  :disabled="hasPerm('order.delete-customer') ? true : false"
                >
                  <div>
                    <el-button
                      :disabled="!hasPerm('order.delete-customer')"
                      type="danger"
                      size="small"
                      @click="delCustomer(item.id)"
                      >{{ $t("Delete") }}</el-button
                    >
                  </div>
                </el-tooltip>
              </el-col>
            </el-row>
          </div>
        </div>
      </el-tab-pane>
      <!-- 回单 -->
      <el-tab-pane :label="$t('receipthui')" v-if="hasPerm('order.receipt')">
        <div style="text-align: center; margin-top: 40px">
          <el-tooltip
            :content="$t('AdministratorActivate')"
            placement="top"
            style="margin-right: 10px"
            :disabled="hasPerm('order.create-receipt') ? true : false"
          >
            <el-upload
              :disabled="!hasPerm('order.create-receipt')"
              :headers="Authorization"
              :before-upload="beforeAvatarUpload"
              :on-success="handleReceiptSuccess"
              :action="BaseApiFile"
              :on-remove="handlerReceipt"
              :file-list="fileList"
              :limit="1"
              name="file"
              clearFiles
              :data="{
                dir: 'driver',
              }"
              class="upload-demo"
              drag
              multiple
            >
              <!-- 设置权限样式 -->
              <div
                :style="
                  hasPerm('order.create-receipt') ? '' : 'cursor: not-allowed'
                "
              >
                <i class="el-icon-upload"></i>
                <div class="el-upload__text">
                  {{ $t("dragHereor") }}<em> {{ $t("clickToUpload") }} </em>
                </div>
              </div>
              <div class="el-upload__tip" slot="tip">
                {{ $t("OnlyJpgPng") }}
              </div>
            </el-upload>
          </el-tooltip>
        </div>
        <!-- 表格 -->
        <div style="margin: 50px">
          <el-table
            max-height="600px"
            :data="receiptList"
            border
            style="width: 100%"
          >
            <el-table-column type="index" align="center"></el-table-column>
            <el-table-column
              prop="file_name"
              :label="$t('fileName')"
              width="300 "
            >
            </el-table-column>
            <el-table-column prop="file_type" :label="$t('fileType')">
            </el-table-column>
            <el-table-column prop="file_size" :label="$t('fileSize')">
            </el-table-column>
            <el-table-column prop="operator" :label="$t('uploader')">
            </el-table-column
            ><el-table-column prop="created_at" :label="$t('uploadTime')">
            </el-table-column>
            <!-- 操作 -->
            <el-table-column
              :label="$t('Operation')"
              width="300"
              align="center"
            >
              <template slot-scope="scope">
                <!-- 重命名 -->
                <el-tooltip
                  :content="$t('AdministratorActivate')"
                  placement="top"
                  :disabled="hasPerm('order.update-receipt') ? true : false"
                >
                  <div>
                    <el-button
                      :disabled="!hasPerm('order.update-receipt')"
                      @click="putReceipt(scope.row)"
                      type="primary"
                      size="small"
                      >{{ $t("Rename") }}</el-button
                    >
                  </div>
                </el-tooltip>
                <!-- 下载 -->
                <el-button
                  @click="toExport(scope.row)"
                  size="small"
                  style="margin: 0 10px"
                  >{{ $t("download") }}
                </el-button>

                <!-- 回单删除 -->
                <el-tooltip
                  :content="$t('AdministratorActivate')"
                  placement="top"
                  style="margin-right: 10px"
                  :disabled="hasPerm('order.delete-receipt') ? true : false"
                >
                  <div>
                    <el-button
                      :disabled="!hasPerm('order.delete-receipt')"
                      type="danger"
                      size="small"
                      @click="delReceipt(scope.row.id)"
                      >{{ $t("Delete") }}</el-button
                    >
                  </div>
                </el-tooltip>
              </template>
            </el-table-column>
          </el-table>
        </div>
      </el-tab-pane>
    </el-tabs>
    <!-- 费用弹出框 -->
    <el-dialog
      :title="type == 1 ? $t('Add') : $t('Edit')"
      :visible.sync="dialogVisible"
      width="700px"
    >
      <el-form label-width="100px" :model="form" ref="form">
        <div style="display: inline-flex">
          <el-form-item label="" prop="sp_unit">
            <el-select
              v-model="form.type"
              :placeholder="$t('statements')"
              style="width: 200px"
              size="small"
              :disabled="this.type == 2"
            >
              <el-option
                v-for="(item, index) in orderAmountTypeList"
                :key="index"
                :label="item.name"
                :value="item.id"
              ></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="" prop="expect_amount">
            <el-input
              :placeholder="$t('PleaseEnterTheAmount')"
              v-model="form.expect_amount"
              style="width: 200px"
              size="small"
            ></el-input>
          </el-form-item>
        </div>
        <el-form-item label="" prop="remark">
          <el-input
            :placeholder="$t('PleaseEnterNote')"
            v-model="form.remark"
            style="width: 500px"
            size="small"
            type="textarea"
            :rows="8"
          ></el-input>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer" style="margin: 0 auto">
        <el-button @click="dialogVisible = false">{{ $t("Cancel") }}</el-button>
        <el-button type="primary" @click="sure">{{ $t("Confirm") }}</el-button>
      </span>
    </el-dialog>
    <!-- 回单弹出框 -->
    <el-dialog :title="$t('Rename')" :visible.sync="visible" width="700px">
      <el-form label-width="100px" :model="receiptForm">
        <el-form-item label="" prop="file_name">
          <el-input
            v-model="receiptForm.file_name"
            style="width: 500px"
            size="small"
          ></el-input>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer" style="margin: 0 auto">
        <el-button @click="visible = false">{{ $t("Cancel") }}</el-button>
        <el-button type="primary" @click="sureReceipt">{{
          $t("Confirm")
        }}</el-button>
      </span>
    </el-dialog>
    <iframe frameborder="0" name="downExcel" style="display: none"></iframe>
  </div>
</template>

<script>
import { gmapApi } from "vue2-google-maps";
import baseApi from "@/api/axios/base_api";
import loading from "@/mixin/global_loading";
import websocket from "@/lib/websocket";
export default {
  // name: "ViewDetails",
  mixins: [loading],

  data() {
    return {
      reverse: true,
      pending: false, //待受理
      takePie: false, //取派中
      completed: false, //已完成
      takeDefeat: false, //取派失败
      recycleBin: false, //回收站
      customerId: null, //客服删除id
      waybillTrailId: null, //运单轨迹id
      currency_unit_symbol: "", //单价单位
      weight_unit_symbol: "", //重量单位
      volume_unit_symbol: "", //体积单位
      settlement_amount: "", //运费
      id: "", //作废id
      EditOrderId: "", //修改id
      printId: "", //打印id
      form: {
        type: "", //费用明细
        order_no: "", //订单号
        expect_amount: "", //费用金额
        remark: "", //费用备注
        in_total: "", //计入
        status: "", //状态
      },
      position: {
        lat: 0,
        lng: 0,
      },
      carIcon: require("../../assets/img/blue_car.png"),

      fileList: [],
      driver_location: null, // 司机位置
      place_location: null, // 收件人位置
      markers: [], // marker标记集合
      polylinePath: [
        // { lat: 55.9361256, lng: -4.7767353 },
        // { lat: 55.9366784, lng: -4.7739458 },
      ],
      center: {
        lat: 0,
        lng: 0,
        zoom: 8,
      },
      // 客服表单
      serviceForm: {
        order_no: "",
        content: "", //客服记录
        file_urls: [],
        picture_urls: [],
      },
      downloadhref: "",
      content: "",
      place_address: "", //发货人地址
      address: "", //仓库地址
      second_place_address: "", //收货人地址
      order_no: "", //订单号
      transport_mode_name: "", //运输方式
      out_user_id: "", //客户订单
      origin_type_name: "", //始发地
      out_order_no: "", //货号
      merchant_id_name: "", //货主
      type_name: "", //类型
      type: "", //类型
      // 收货人
      second_place_fullname: "", //发件人
      second_place_phone: "", //电话
      second_place_post_code: "", //邮编
      second_place_house_number: "", //门牌号
      second_place_city: "", //城市
      second_place_street: "", //街道
      second_place_country: "", //国家
      // 仓库
      name: "", //仓库名称
      phone: "", //电话
      post_code: "", //邮编
      house_number: "", //门牌号
      city: "", //城市
      street: "", //街道
      country: "", //国家
      // 发货人
      place_fullname: "", //发件人
      place_phone: "", //电话
      place_post_code: "", //邮编
      place_house_number: "", //门牌号
      place_city: "", //城市
      place_street: "", //街道
      place_country: "", //国家
      execution_date: "", //取派件时间
      // 货物信息
      settlement_type_name: "", //付款方式
      special_remark: "", //特别事项
      mask_code: "", //掩码
      package_list: [], //包裹
      material_list: [], //货物
      amount_list: [], //费用
      tableData: [],
      tracking_order_no: "", // 运单号
      tour_no: "", //线路任务
      batch_no: "", //站点任务
      car_no: "", //车辆
      driver_name: "", ///司机
      driver_phone: "", //电话
      status_name: "", //运单状态
      expect_total_amount: "", //运费合计
      package_list_data: "",
      material_list_data: "",
      created_at: "", //时间
      activities: [], //时间线
      viewDetailsList: [], //运单数组
      waybillTrail_list: [], // 物流轨迹
      waybillTrail: {},
      fullname: "", //客服姓名
      trailInfo: {},
      viewDetails: {},
      dialogVisible: false, //弹出框
      type: 1,
      orderAmountTypeList: [],
      receiptList: [], //回单列表
      order_no2: null, //费用订单号
      idTrail: null, //轨迹id
      creationTime: "", //轨迹时间
      typeName: "", //轨迹时间
      Customer_list: [],
      visible: false, //
      receiptForm: {
        id: "",
        file_name: "",
      },
      receipt: {
        file_name: "",
        file_size: "",
        file_url: "",
        file_type: "",
      },
    };
  },
  computed: {
    Authorization() {
      return { Authorization: this.$store.state.token.token };
    },
    BaseApiImg() {
      return `${baseApi}admin/upload/image`;
    },
    BaseApiFile() {
      return `${baseApi}admin/upload/file`;
    },
    google: gmapApi,
  },
  created() {
    this.getAdvanceSetting(); //单位

    this.getDictionary(); //数据字典
    this.del(this.$route.query.id); //删除
    this.EditOrder(this.$route.query.id); //修改
    this.print(this.$route.query.id); //打印
    this.getOrderCustomer(this.$route.query.order_no); //客服记录
    this.getOrderAmount(this.$route.query.order_no); //费用
    this.getOrderReceipt(this.$route.query.order_no); //回单
    if (this.$route.query.id) {
      this.getParticulars(this.$route.query.id);
      this.getTrackingOrderList(this.$route.query.id);
      this.getOrderWaybillTrail(this.$route.query.id);
    }
  },
  methods: {
    checkMap() {
      //   if(){
      //   }
    },
    zoomChanged() {},
    // 物流轨迹第一条改变颜色
    getColor(ecindex) {
      let color = "";
      if (ecindex == 0) {
        color = "#0bbd87";
      }
      return color;
    },
    // 打印id
    print(id) {
      this.printId = id;
    },
    //打印
    printerOrder() {
      this.$api.printerOrder({ id_list: this.printId }).then((res) => {
        if (!res) return;
        websocket(res.data);
      });
    },
    // 作废
    del(id) {
      this.id = id;
    },
    // 作废
    delOrder() {
      // 删除订单
      this.$confirm(this.$t("ConfirmToCancel") + "?", this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      })
        .then(() => {
          this.$api.delOrder(this.id).then((res) => {
            if (res.code === 200) {
              this.$message({
                message: this.$t("SuccessfullDeleted"),
                type: "success",
              });
              this.$router.push({
                name: "orderManagement",
              });
            }
          });
        })
        .catch(() => {});
    },
    // 获取单位值
    getAdvanceSetting() {
      this.$api.getAdvanceSetting().then((res) => {
        this.volume_unit_symbol = res.data.volume_unit_symbol; //体积单位
        this.weight_unit_symbol = res.data.weight_unit_symbol; //重量单位
        this.currency_unit_symbol = res.data.currency_unit_symbol; //单价单位
      });
    },
    // 数据字典
    getDictionary() {
      this.$api.getDictionary().then((res) => {
        this.orderAmountTypeList = res.data.orderAmountTypeList; //明细下拉框
      });
    },
    EditOrder(id) {
      this.EditOrderId = id;
    },
    // 修改
    toEditOrder() {
      /* eslint-disable */
      this.$router.push({
        name: "orderAdd",
        query: { id: this.EditOrderId },
      });
    },

    // 订单详情
    getParticulars(id) {
      // 判断订单状态图片
      this.$api.getOrderDetails(id).then((res) => {
        switch (res.data.status) {
          case 1:
            this.pending = true; //待受理
            break;
          case 2:
            this.takePie = true; //取派中
            break;
          case 3:
            this.completed = true; //已完成
            break;
          case 4:
            this.takeDefeat = true; //取派失败
            break;
          case 5:
            this.recycleBin = true; //回收站
        }
        this.form.status = res.data.status; //判断作废或修改的显示隐藏
        this.order_no = res.data.order_no;
        this.place_address = res.data.place_address;
        // this.address = res.data.address;
        this.second_place_address = res.data.second_place_address;
        if (res.data.source == 3) {
          this.transport_mode_name = "零担";
        } else {
          this.transport_mode_name = res.data.transport_mode_name;
        }
        this.out_user_id = res.data.out_user_id;
        this.origin_type_name = res.data.origin_type_name;
        this.out_order_no = res.data.out_order_no;
        this.merchant_id_name = res.data.merchant_id_name;
        this.type_name = res.data.type_name;
        this.type = res.data.type;
        // 发件人
        this.place_fullname = res.data.place_fullname;
        this.place_phone = res.data.place_phone;
        this.place_country = res.data.place_country_name; //国家
        this.place_post_code = res.data.place_post_code;
        this.place_house_number = res.data.place_house_number;
        this.place_city = res.data.place_city;
        this.place_street = res.data.place_street;
        this.execution_date = res.data.execution_date;
        // 收件人
        this.second_place_fullname = res.data.second_place_fullname;
        this.second_place_phone = res.data.second_place_phone;
        this.second_place_country = res.data.second_place_country_name; //国家
        this.second_place_post_code = res.data.second_place_post_code;
        this.second_place_house_number = res.data.second_place_house_number;
        this.second_place_city = res.data.second_place_city;
        this.second_place_street = res.data.second_place_street;
        this.second_execution_date = res.data.second_execution_date;
        // 仓库
        // 仓库信息
        if (this.type == 1) {
          this.name = res.data.second_place_fullname;
          this.phone = res.data.second_place_phone;
          this.country = res.data.second_place_country_name;
          this.post_code = res.data.second_place_post_code;
          this.house_number = res.data.second_place_house_number;
          this.city = res.data.second_place_city;
          this.street = res.data.second_place_street;
          this.address = res.data.second_place_address;
        } else if (this.type == 2) {
          // 仓库赋值
          this.name = res.data.second_place_fullname;
          this.phone = res.data.second_place_phone;
          this.country = res.data.second_place_country_name;
          this.post_code = res.data.second_place_post_code;
          this.house_number = res.data.second_place_house_number;
          this.city = res.data.second_place_city;
          this.street = res.data.second_place_street;
          this.address = res.data.second_place_address;
          // 收货人赋值
          this.second_place_fullname = res.data.place_fullname;
          this.second_place_phone = res.data.place_phone;
          this.second_place_country = res.data.place_country_name;
          this.second_place_post_code = res.data.place_post_code;
          this.second_place_house_number = res.data.place_house_number;
          this.second_place_city = res.data.place_city;
          this.second_place_street = res.data.place_street;
          this.second_place_address = res.data.place_address;
        }
        // this.name = res.data.name;
        // this.phone = res.data.phone;
        // this.post_code = res.data.post_code;
        // this.house_number = res.data.house_number;
        // this.city = res.data.city;
        // this.street = res.data.street;
        this.settlement_type_name = res.data.settlement_type_name; //付款方式
        this.special_remark = res.data.special_remark; //特别事项
        this.mask_code = res.data.mask_code; //掩码
        this.package_list = res.data.package_list; //包裹
        this.settlement_amount = res.data.settlement_amount; //运费
        this.material_list = res.data.material_list; //货物
        this.expect_total_amount = res.data.expect_total_amount; //运费合计
        this.amount_list = res.data.bill_list; //增值费用
      });
    },
    // 运单轨迹
    getTrackingOrderList(id) {
      this.$api.getTrackingOrderList(id).then((res) => {
        var viewDetailsList = res.data;
        // viewDetailsList.map((item) => {
        //   var arr = [];
        //   item.time_list.map((item) => {
        //     this.waybillTrailId = item.id;
        //     this.creationTime = item.created_at;
        //     this.typeName = item.type_name;
        //     //物流轨迹改颜色
        //     var obj = {
        //       type_name: item.type,
        //       content: item.content,
        //       timestamp: item.created_at,
        //       color: "#0bbd87",
        //     };
        //     arr.push(obj);
        //   });
        //   // 处理完重新赋值
        //   item.time_list = arr;
        // });
        this.viewDetailsList = viewDetailsList;
      });
    },
    // 物流轨迹
    getOrderWaybillTrail(id) {
      this.idTrail = id;
      this.$api.getOrderWaybillTrail(id).then((res) => {
        var waybillTrail_list = res.data;
        waybillTrail_list.map((item) => {
          var arr = [];
          item.tracking_order_trail.map((item) => {
            this.waybillTrailId = item.id;
            this.creationTime = item.created_at;
            this.typeName = item.type_name;
            //物流轨迹改颜色
            var obj = {
              type_name: item.type_name,
              content: item.content,
              timestamp: item.created_at,
              // color: "#0bbd87",
              id: item.id,
            };
            arr.push(obj);
          });
          // 处理完重新赋值
          item.tracking_order_trail = arr;
          // 地图经纬度 标记
          this.center = {
            lat: +item.place_lat,
            lng: +item.place_lon,
            zoom: 8,
          };
          this.place_location = {
            lat: +item.place_lat,
            lng: +item.place_lon,
          };
          this.driver_location = {
            lat: +item.driver_lat,
            lng: +item.driver_lon,
          };

          let options = [];
          options.push(this.place_location);
          this.markers = this.initTempDate(options);
          this.markers.push(this.addMarker(this.driver_location, this.carIcon));
          this.polylinePath.push(this.place_location, this.driver_location);
        });
        this.waybillTrail_list = waybillTrail_list;
      });
    },
    // 人的标记点
    initTempDate(options) {
      const tempArray = [];
      options.forEach((item, index) => {
        // 处理地图上的所有 marker
        const pointer = {
          position: {
            lat: +item.lat,
            lng: +item.lng,
          },
          icon: null,
        };
        tempArray.push(pointer);
      });
      return tempArray;
    },
    // 车标记点
    addMarker(data, iconUrl) {
      return {
        position: {
          lat: +data.lat,
          lng: +data.lng,
        },
        icon: iconUrl,
      };
    },
    // 删除轨迹
    delLogistics(id) {
      console.log("id");
      console.log(id);
      // console.log("this.waybillTrailId");
      // console.log(this.waybillTrailId);
      var waybillTrail_list = this.waybillTrail_list;
      waybillTrail_list.map((item) => {
        console.log("item");
        console.log(item);
      });
      this.$confirm(this.$t("ConfirmToCancel") + "?", this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      })
        .then(() => {
          this.$api.delOrderWaybillTrail(id).then((res) => {
            if (res.code === 200) {
              this.$message({
                message: this.$t("SuccessfullDeleted"),
                type: "success",
              });
            }
            this.getOrderWaybillTrail(this.idTrail);
          });
        })
        .catch(() => {});
    },
    // 生成时间线
    // createTimeLine(arr) {
    //   this.waybillTrail.activities = arr.map((el) => {
    //     return {
    //       content: el.content,
    //       timestamp: el.created_at,
    //       color: "#0bbd87",
    //     };
    //   });
    //   this.waybillTrail.activities.reverse();
    // },

    // 费用查询
    getOrderAmount(order_no) {
      let obj = {
        object_type: 1,
        object_no: order_no,
      };
      this.$api.feeOrder(obj).then((res) => {
        this.tableData = res.data.data;
      });
    },
    // 费用添加
    handlerAddClick() {
      this.type = 1;
      this.dialogVisible = true;
      this.form = {
        order_no: "", //订单编号
        type: "", //备品编号
        expect_amount: "", //备品名称
        remark: "", //品牌
        in_total: "", //计入
        status: "", //状态
        order_no: this.order_no, //订单号
      };
    },
    // 修改费用
    putView(row) {
      this.type = 2;
      this.dialogVisible = true;
      this.form = {
        id: row.id,
        type: row.type,
        expect_amount: row.expect_amount,
        remark: row.remark,
        in_total: row.in_total,
        status: row.status,
        order_no: row.order_no,
      };
    },
    // 删除费用
    delView(id) {
      this.$confirm(this.$t("ConfirmToCancel") + "?", this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      })
        .then(() => {
          this.$api.delOrderAmount(id).then((res) => {
            if (res.code === 200) {
              this.$message({
                message: this.$t("SuccessfullDeleted"),
                type: "success",
              });
            }
            this.getOrderAmount(this.order_no2);
            this.getParticulars(this.id);
          });
        })
        .catch(() => {});
    },
    // 确定
    sure() {
      // 新增
      if (this.type == 1) {
        this.$api.addOrderAmount({ ...this.form }).then((res) => {
          if (res.code === 200) {
            this.$message({
              message: "新增成功!",
              type: "success",
            });
          }
          this.getOrderAmount(this.order_no2);
          this.getParticulars(this.id);
        });
        this.dialogVisible = false;
      } else if (this.type == 2) {
        // 修改
        this.$api.putOrderAmount({ ...this.form }).then((res) => {
          if (res.code === 200) {
            this.$message({
              message: "修改成功!",
              type: "success",
            });
          }
          this.getOrderAmount(this.order_no2);
          this.getParticulars(this.id);
        });
        this.dialogVisible = false;
      }
    },
    // 合计
    getSummaries(param) {
      const { columns, data } = param;
      const sums = [];
      columns.forEach((column, index) => {
        if (index === 1) {
          sums[index] = this.$t("Total");
        } else if (index === 2) {
          const values = data.map((item) => Number(item[column.property]));
          if (!values.every((value) => isNaN(value))) {
            sums[index] = values.reduce((prev, curr) => {
              const value = Number(curr);
              if (!isNaN(value)) {
                return prev + curr;
              } else {
                return prev;
              }
            }, 0);
          } else {
            // sums[index] = 'N/A'
          }
        } else {
          sums[index] = "";
        }
      });
      sums[2] = sums[2] ? sums[2].toFixed(2) : "";
      return sums;
    },
    // 客服记录
    getOrderCustomer(order_no) {
      this.$api.getOrderCustomer(order_no).then((res) => {
        var Customer_list = res.data.data;
        this.Customer_list = Customer_list;
        Customer_list.map((item) => {
          this.fullname = item.fullname;
        });
      });
    },
    // 客服上传图片 成功
    handleServiceImgSuccess(response) {
      if (response.code === 200) {
        var picture_urls = this.serviceForm.picture_urls;
        var path = response.data.path;
        picture_urls.push(path);
      }
    },
    // 客服上传文件 成功
    handleServiceSuccess(response) {
      if (response.code === 200) {
        var file_urls = this.serviceForm.file_urls;
        var path = response.data.path;
        file_urls.push(path);
      }
    },
    // 添加客服记录

    AddClick() {
      this.$api
        .addOrderCustomer({
          ...this.serviceForm,
          order_no: this.order_no,
        })
        .then((res) => {
          // if (res.code === 200) {
          //   this.$message({
          //     message: this.$t("success"),
          //     type: "success",
          //   });
          // }
          this.fileList = [];
          this.getOrderCustomer(this.order_no);
          this.serviceForm.content = ""; //清空文字
          this.serviceForm.picture_urls = []; //清空图片
          this.serviceForm.file_urls = []; //清空文件
        });
    },
    // 删除客服记录
    delCustomer(id) {
      // var Customer_list = this.Customer_list;
      // Customer_list.map((item) => {
      //   this.customerId = item.id;
      // });
      this.$confirm(this.$t("ConfirmToCancel") + "?", this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      })
        .then(() => {
          this.$api.delOrderCustomer(id).then((res) => {
            if (res.code === 200) {
              this.$message({
                message: this.$t("SuccessfullDeleted"),
                type: "success",
              });
            }
            this.getOrderCustomer(this.order_no);
          });
        })
        .catch(() => {});
    },
    // 回单
    getOrderReceipt(order_no) {
      this.$api.getOrderReceipt(order_no).then((res) => {
        this.receiptList = res.data.data;
      });
    },
    // 上传成功 钩子函数
    handleReceiptSuccess(response) {
      if (response.code === 200) {
        // 获取文件信息
        this.receipt.file_url = response.data.path;
        this.receipt.file_name = response.data.name;
        this.receipt.file_type = response.data.type;
        this.receipt.file_size = response.data.size;
      }
      // 回单新增
      this.$api
        .addOrderReceipt({
          ...this.receipt,
          order_no: this.order_no,
        })
        .then((res) => {
          if (res.code === 200) {
            // this.$message({
            //   message: "新增成功!",
            //   type: "success",
            // });
            this.getOrderReceipt(this.order_no);
          }
        });
    },
    // 回单重命名
    putReceipt(row) {
      this.visible = true;
      this.receiptForm = {
        id: row.id,
        file_name: row.file_name,
      };
    },
    // 回单确定
    sureReceipt() {
      this.$api.putOrderReceipt({ ...this.receiptForm }).then((res) => {
        if (res.code === 200) {
          this.getOrderReceipt(this.order_no);
          this.visible = false;
        }
      });
    },
    // 回单删除
    delReceipt(id) {
      this.$confirm(this.$t("ConfirmToCancel") + "?", this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      })
        .then(() => {
          this.$api.delOrderReceipt(id).then((res) => {
            if (res.code === 200) {
              this.$message({
                message: this.$t("SuccessfullDeleted"),
                type: "success",
              });
            }
            this.getOrderReceipt(this.order_no);
          });
        })
        .catch(() => {});
    },
    // 回单下载
    toExport(row) {
      var url2 = row.file_url;

      this.downloadhref = url2;
      this.downloadIamge(row.file_url, row.file_name);
    },
    downloadIamge(imgsrc, name) {
      //下载图片地址和图片名
      var image = new Image();
      // 解决跨域 Canvas 污染问题
      image.setAttribute("crossOrigin", "anonymous");
      image.onload = function () {
        var canvas = document.createElement("canvas");
        canvas.width = image.width;
        canvas.height = image.height;
        var context = canvas.getContext("2d");
        context.drawImage(image, 0, 0, image.width, image.height);
        var url = canvas.toDataURL("image/png"); //得到图片的base64编码数据

        var a = document.createElement("a"); // 生成一个a元素
        var event = new MouseEvent("click"); // 创建一个单击事件
        a.download = name || "photo"; // 设置图片名称
        a.href = url; // 将生成的URL设置为a.href属性
        a.dispatchEvent(event); // 触发a的单击事件
      };
      image.src = imgsrc;
    },
    // 上传前钩子函数
    beforeAvatarUpload(file) {
      console.log("beforeAvatarUpload", file);
    },

    handlerClearLisence() {
      this.form.lisence_material = "";
      this.form.lisence_material_name = "";
    },
    handlerReceipt() {
      this.form.lisence_material = "";
      this.form.lisence_material_name = "";
    },
  },
};
</script>

<style lang="less" scoped>
.viewDetails_box {
  .border-card {
    .top {
      margin-top: 20px;
      display: flex;
      justify-content: space-between;
      .top-but {
        display: flex;
        margin-bottom: 20px;
        .top-edit {
          padding: 0 10px;
        }
      }
    }
    .top_box {
      clear: both; //清除浮动
      border: 1px solid #d7d7d7;
      height: 200px;
    }
    .shipper {
      border: solid 1px #e4ebf1;

      height: 300px;
      /deep/ .el-input__inner {
        border: 0;
        border-bottom: 1px solid #d6d7d6;
      }
    }
    .google-map {
      .map {
        width: 85%;
        height: 500px;
        margin: 0 auto;
      }
      .box {
        margin: 0 auto;
        width: 85%;
        height: 50px;
        background: #77cdf5;
        .box-title {
          font-size: 15px;
          text-align: center;
          line-height: 50px;
        }
      }
    }
  }
  .underline-box {
    display: grid;
    grid-template-columns: 1fr 1fr;
  }
  .underline {
    border-bottom: 1px solid #d6d7d6;
    width: 52%;
    border-bottom: 1px solid #d6d7d6;
    margin-top: 10px;
    margin-left: 20px;
  }
  .underline-title {
    width: 50%;
    text-align: right;
    font-size: 15px;
    margin-top: 10px;
  }
  .underline-bottom {
    border-bottom: 1px solid #d6d7d6;
    width: 200px;
    overflow: auto;
    margin-top: 10px;
    margin-left: 20px;
    white-space: nowrap !important;
  }
  ::-webkit-scrollbar {
    width: 0px !important;
    /*滚动条宽度*/
    height: 0px !important;
    /*滚动条高度*/
    // background-color: white;
  }
  .country-title {
    width: 22%;
    text-align: right;
    font-size: 15px;
    margin-top: 10px;
  }
  .country-bottom {
    border-bottom: 1px solid #d6d7d6;
    width: 78%;
    margin-top: 10px;
    margin-left: 20px;
  }
  .base-fee {
    color: #646262;
    font-size: 15px;
    font-weight: 600;
    width: 220px;
    padding-left: 10px;
    height: 47px;
    line-height: 47px;
    border: 1px solid #ebeef5;
  }
  .fee-box {
    display: grid;
    grid-template-columns: 250px 1fr;
    .fee {
      width: 220px;
      padding-left: 10px;
      border: 1px solid #ebeef5;
      border-top: 0;
      height: 47px;
      line-height: 47px;
    }
  }
}
/deep/.cell {
  display: inline-flex !important;
}
</style>
