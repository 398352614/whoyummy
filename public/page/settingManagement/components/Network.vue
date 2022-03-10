<template>
  <!-- 网点 -->
  <div class="network">
    <el-row :gutter="30">
      <el-col :span="6">
        <el-card class="box-card">
          <!-- 新增 -->
          <!-- <div class="card-but">
            <el-button type="primary" size="small" @click="addNetwork"
              >{{ $t("Add") }}
            </el-button> -->
          <!-- 删除 -->
          <!-- <el-button type="danger" size="small" @click="delWarehouse">{{
              $t("Delete")
            }}</el-button>
          </div> -->
          <!-- 树节点 -->
          <el-tree
            :data="networkTree"
            :props="defaultProps"
            @node-click="handleNodeClick"
            default-expand-all
            :expand-on-click-node="false"
            :highlight-current="true"
          >
            <span class="custom-tree-node" slot-scope="{ node, data }">
              <span :class="getTitle(node)">{{ node.label }}</span>
              <span class="operation">
                <!-- 添加网点 -->
                <el-tooltip
                  :content="$t('AdministratorActivate')"
                  placement="top"
                  :disabled="hasPerm('warehouse.store') ? true : false"
                >
                  <div>
                    <el-button
                      v-if="node.level == 2 || node.level == 1"
                      :disabled="!hasPerm('warehouse.store')"
                      type="text"
                      size="mini"
                      @click.stop="() => addNetwork(data)"
                    >
                      <i
                        class="el-icon-circle-plus-outline"
                        :style="
                          hasPerm('warehouse.store')
                            ? 'font-size: 20px; color: #64abde;'
                            : 'color: rgba(201, 201, 201, 0.8) ;font-size: 20px; '
                        "
                      ></i>
                    </el-button>
                  </div>
                </el-tooltip>
                <!-- 删除网点 -->
                <el-tooltip
                  :content="$t('AdministratorActivate')"
                  placement="top"
                  :disabled="hasPerm('warehouse.destroy') ? true : false"
                >
                  <div>
                    <el-button
                      :disabled="!hasPerm('warehouse.destroy')"
                      v-if="node.level != 1"
                      type="text"
                      size="mini"
                      @click.stop="() => delWarehouse(node, data)"
                    >
                      <i
                        class="el-icon-delete"
                        :style="
                          hasPerm('warehouse.destroy')
                            ? 'color: red;font-size: 20px;'
                            : 'color: rgba(201, 201, 201, 0.8) ;font-size: 20px '
                        "
                      ></i>
                    </el-button>
                  </div>
                </el-tooltip>
              </span>
            </span>
          </el-tree>
        </el-card>
      </el-col>
      <!-- 展示内容 -->
      <el-col :span="18">
        <el-form
          :rules="formRules"
          ref="form"
          :model="form"
          label-width="150px"
          v-if="show"
        >
          <el-card>
            <h3>{{ $t("GeneralInfo") }}</h3>
            <!-- 网点编号 -->
            <el-form-item :label="$t('NodeEditor')" prop="code">
              <el-input
                size="small"
                v-model="form.code"
                style="width: 300px"
              ></el-input>
            </el-form-item>
            <!-- 网点名称 -->
            <el-form-item :label="$t('NetworkName')" prop="name">
              <el-input
                size="small"
                v-model="form.name"
                style="width: 300px"
              ></el-input>
            </el-form-item>
            <!-- 所属网点 -->
            <el-form-item
              :label="$t('AffiliatedBranches')"
              prop="parent"
              v-if="parentId != 0 || type == 1"
            >
              <el-input
                size="small"
                v-model="form.parent_name"
                style="width: 300px"
                disabled
              ></el-input>
            </el-form-item>

            <!-- 详细地址 -->
            <div v-if="template_id == 1">
              <!-- 国家 -->
              <el-form-item :label="$t('Country')" prop="country">
                <el-select
                  v-model="form.country"
                  :placeholder="$t('pleaseSelectCountry')"
                  size="small"
                  style="width: 300px"
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
              <el-row :gutter="20">
                <el-col :span="12">
                  <el-form-item :label="$t('ZipCode')" prop="post_code">
                    <el-input
                      size="small"
                      v-model="form.post_code"
                      @change="getLocation"
                      @blur="getLocation"
                      :placeholder="$t('PleaseEnterZipCode')"
                      style="width: 300px"
                    ></el-input>
                  </el-form-item>
                </el-col>
                <el-col :span="12">
                  <el-form-item :label="$t('DoorNo')" prop="house_number">
                    <el-input
                      size="small"
                      v-model="form.house_number"
                      @change="getLocation"
                      @blur="getLocation"
                      :placeholder="$t('PleaseEnterDoorNo')"
                      style="width: 300px"
                    ></el-input>
                  </el-form-item>
                </el-col>
                <el-col :span="12">
                  <el-form-item :label="$t('City')" prop="city">
                    <el-input
                      size="small"
                      v-model="form.city"
                      :disabled="disabledBtnByCountry"
                      @change="getLocation"
                      @blur="getLocation"
                      :placeholder="$t('PleaseEnterCity')"
                      style="width: 300px"
                    ></el-input>
                  </el-form-item>
                </el-col>
                <el-col :span="12">
                  <el-form-item :label="$t('Street')" prop="street">
                    <el-input
                      size="small"
                      v-model="form.street"
                      :disabled="disabledBtnByCountry"
                      @change="getLocation"
                      @blur="getLocation"
                      :placeholder="$t('PleaseEnterStreet')"
                      style="width: 300px"
                    ></el-input>
                  </el-form-item>
                </el-col>
              </el-row>
            </div>
            <el-form-item v-else :label="$t('address')" prop="address">
              <el-input
                size="small"
                v-model="form.address"
                style="width: 400px"
              >
                <el-button
                  slot="append"
                  @click="selectAddressMap"
                  class="locale-btn"
                >
                  <i class="el-icon-map-location" />{{ $t("positioning") }}
                </el-button>
              </el-input>
            </el-form-item>
            <!-- 网点功能 -->
            <!-- <el-form-item :label="$t('NetworkFunction')" prop="acceptance_type">
              <el-checkbox-group v-model="form.acceptance_type">
                <el-checkbox
                  v-for="item in warehouseAcceptanceTypeList"
                  :label="item.id"
                  :key="item.id"
                >
                  {{ item.name }}
                </el-checkbox>
              </el-checkbox-group>
            </el-form-item> -->
            <!-- 网点类型 -->
            <el-form-item :label="$t('NodeTypes')" prop="type">
              <el-radio-group v-model="form.type">
                <el-radio :label="1">{{ $t("League") }} </el-radio>
                <el-radio :label="2">{{ $t("ProprietaryTrading") }} </el-radio>
              </el-radio-group>
            </el-form-item>
            <!-- 是否分拨中心 -->
            <el-form-item :label="$t('DistributionCenter')">
              <el-switch
                v-model="form.is_center"
                :active-value="1"
                :inactive-value="2"
                disabled
              >
              </el-switch>
              <div class="form-title">
                {{ $t("DistributionCenterState") }}
              </div>
            </el-form-item>
            <!-- 是否支持一键扫描 -->
            <el-form-item :label="$t('SupportOneClickScanning')">
              <el-switch
                v-model="form.can_select_all"
                :active-value="1"
                :inactive-value="2"
              >
              </el-switch>
              <!-- 判断一键扫描开启展示 -->
              <el-button
                type="text"
                style="margin-left: 20px"
                v-if="form.can_select_all == 1"
                @click="configureShipper"
                >{{ $t("configureShipper") }}</el-button
              >
              <div class="form-title">
                {{ $t("SupportOneClickScanningState") }}({{
                  $t("ThisItemGlobalConfiguration")
                }})
              </div>
            </el-form-item>
            <!-- 联系信息 -->
            <h3>{{ $t("ContactInformation") }}</h3>
            <el-row :gutter="20">
              <el-col :span="12">
                <!-- 公司名称 -->
                <el-form-item :label="$t('CompanyName')" prop="company_name">
                  <el-input
                    size="small"
                    v-model="form.company_name"
                    style="width: 300px"
                  ></el-input>
                </el-form-item>
                <!-- 联系人 -->
                <el-form-item :label="$t('ContactPerson')" prop="fullname">
                  <el-input
                    size="small"
                    v-model="form.fullname"
                    style="width: 300px"
                  ></el-input>
                </el-form-item>
                <!-- 电话 -->
                <el-form-item :label="$t('Tel')" prop="phone">
                  <el-input
                    size="small"
                    v-model="form.phone"
                    style="width: 300px"
                  ></el-input>
                </el-form-item>
                <!-- 邮箱 -->
                <el-form-item :label="$t('Email')" prop="email">
                  <el-input
                    size="small"
                    v-model="form.email"
                    style="width: 300px"
                  ></el-input>
                </el-form-item>
              </el-col>
              <!-- 头像 -->
              <el-col :span="12">
                <div class="avatar-box">
                  <el-form-item label=" " ref="avatar">
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
                        v-if="this.form.avatar"
                        :src="this.form.avatar"
                        class="avatar"
                      />
                      <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                    </el-upload>
                  </el-form-item></div
              ></el-col>
            </el-row>
            <!-- 承包路线 -->
            <h3>{{ $t("ContractRoute") }}</h3>
            <el-form-item style="margin-left: -90px">
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('warehouse.line-store') ? true : false"
              >
                <div class="add-row">
                  <el-button
                    :disabled="!hasPerm('warehouse.line-store')"
                    @click="addRow"
                    type="primary"
                    size="small"
                    v-if="parentId != 0 || type == 1"
                    >{{ $t("Addition") }}</el-button
                  >
                </div>
              </el-tooltip>
              <el-table
                max-height="700px"
                :data="hasPerm('warehouse.line-index') ? lineData : []"
                style="width: 100%"
                border
              >
                <template slot="empty" v-if="!hasPerm('warehouse.line-index')">
                  <div style="font-size: 18px">
                    {{ $t("AdministratorActivate") }}
                  </div>
                </template>
                <el-table-column type="index" align="center"></el-table-column>
                <!-- 线路名称 -->
                <el-table-column
                  :label="$t('LineName')"
                  align="center"
                  prop="name"
                >
                </el-table-column>
                <!-- 工作时间 -->
                <el-table-column
                  :label="$t('WorkingHours')"
                  align="center"
                  prop="work_day_list"
                >
                </el-table-column>
                <!-- 操作 -->
                <el-table-column :label="$t('Operation')" align="center">
                  <template slot-scope="scope">
                    <el-tooltip
                      :content="$t('AdministratorActivate')"
                      placement="top"
                      :disabled="hasPerm('warehouse.line-index') ? true : false"
                    >
                      <span>
                        <el-button
                          :disabled="!hasPerm('warehouse.line-index')"
                          @click.native.prevent="getLineItem(scope.row)"
                          type="success"
                          plain
                          size="small"
                          >{{ $t("details") }}</el-button
                        >
                      </span>
                    </el-tooltip>
                    <el-tooltip
                      :content="$t('AdministratorActivate')"
                      placement="top"
                      :disabled="
                        hasPerm('warehouse.line-destroy') ? true : false
                      "
                    >
                      <span>
                        <el-button
                          :disabled="!hasPerm('warehouse.line-destroy')"
                          v-if="parentId != 0 || type == 1"
                          @click.native.prevent="deleteRow(scope.row)"
                          type="danger"
                          plain
                          size="small"
                          >{{ $t("Remove") }}
                        </el-button>
                      </span>
                    </el-tooltip>
                  </template>
                </el-table-column>
              </el-table>
              <base-pagination
                :pageParams="pageParams"
                @search="getNetwork"
              ></base-pagination>
            </el-form-item>
            <!-- 保存 -->
            <el-tooltip
              :content="$t('AdministratorActivate')"
              placement="top"
              :disabled="hasPerm('warehouse.update') ? true : false"
              style="width: 250px"
            >
              <div class="footer-but">
                <el-button
                  :disabled="!hasPerm('warehouse.update')"
                  type="primary"
                  @click="putNetwork"
                  :class="[
                    hasPerm('warehouse.update') ? 'submit' : 'submit-disabled',
                  ]"
                  :loading="this.$store.state.config.button_loading"
                  >{{ $t("Save") }}</el-button
                >
              </div>
            </el-tooltip>
          </el-card>
        </el-form>
      </el-col>
    </el-row>
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
    <!-- 线路弹出框 -->
    <el-dialog :visible.sync="show_line" width="60%">
      <el-row :gutter="10">
        <el-col :span="18">
          <div style="font-size: 18px">{{ $t("PleaseChooseRoute") }}</div>
        </el-col>
        <el-col :span="4" style="text-align: right">
          <el-input
            size="small"
            style="margin-bottom: 10px"
            clearable
            :placeholder="$t('LineName')"
            @keyup.enter.native="getNetworkAll"
            @clear="getNetworkAll"
            v-model="params.name"
          >
          </el-input>
        </el-col>
        <el-col :span="2" style="text-align: right">
          <el-button
            size="small"
            @click="getNetworkAll"
            type="primary"
            :loading="this.$store.state.config.button_loading"
            >{{ $t("Search") }}</el-button
          >
        </el-col>
      </el-row>
      <el-table
        max-height="600px"
        :data="lineList"
        row-key="id"
        border
        style="width: 100%"
        ref="multipleTable"
        @selection-change="handleSelectionChange"
      >
        <el-table-column
          :reserve-selection="true"
          type="selection"
          width="55"
          :selectable="selectable"
        >
        </el-table-column>
        <el-table-column type="index" align="center" width="50">
          <template slot="header">
            <span>{{ $t("inSequence") }}</span>
          </template>
        </el-table-column>
        <el-table-column prop="name" align="center" :label="$t('LineName')">
        </el-table-column>
        <el-table-column
          prop="work_day_list"
          align="center"
          :label="$t('date')"
        >
        </el-table-column>
      </el-table>
      <base-pagination
        :pageParams="params"
        @search="getNetworkAll"
      ></base-pagination>
      <div slot="footer" class="dialog-footer">
        <el-button @click="show_line = false" class="cancel"
          >{{ $t("Cancel") }}
        </el-button>
        <el-button type="primary" @click="confirm" class="confirm-but"
          >{{ $t("Confirm") }}
        </el-button>
      </div>
    </el-dialog>
    <!-- 线路详情 -->
    <el-dialog
      :title="$t('DetailsOnLine')"
      :visible.sync="showDetails"
      width="60%"
    >
      <!-- 线路名称 -->
      <el-row :gutter="20" class="showDetails-box">
        <el-col :span="4" class="box-title"> {{ $t("LineName") + ":" }}</el-col>
        <el-col :span="20" class="wenzi">{{ details.name }}</el-col>
      </el-row>
      <!-- 服务范围 -->
      <el-row :gutter="20" class="showDetails-box">
        <el-col :span="4" class="box-title">{{
          $t("ScopeOfServices") + ":"
        }}</el-col>
        <el-col :span="20">
          <el-table
            max-height="600px"
            :data="postCodeList"
            border
            style="width: 100%; margin-bottom: 20px"
          >
            <el-table-column type="index" align="center" width="50" label="#">
            </el-table-column>
            <el-table-column
              prop="country_name"
              align="center"
              width="100"
              :label="$t('Country')"
            >
            </el-table-column>
            <el-table-column
              prop="post_code_start"
              align="center"
              :label="$t('StartPostCode')"
            >
            </el-table-column>
            <el-table-column
              prop="post_code_end"
              align="center"
              :label="$t('EndPostCode')"
            >
            </el-table-column>
          </el-table>

          <el-table
            max-height="600px"
            :data="preciseList"
            border
            style="width: 100%"
            :header-cell-style="{ 'text-align': 'center' }"
          >
            <el-table-column type="index" align="center" width="50" label="#">
            </el-table-column>
            <el-table-column
              prop="country_name"
              align="center"
              width="100"
              :label="$t('Country')"
            >
            </el-table-column>
            <el-table-column
              prop="post_code_start"
              align="left"
              :label="$t('PreciseZipCode')"
            >
              <template slot-scope="scope">
                <el-tag :key="tag" v-for="tag in scope.row.dynamicTags">
                  {{ tag }}
                </el-tag>
              </template>
            </el-table-column>
            <!-- <el-table-column
              prop="post_code_end"
              align="center"
              :label="$t('EndPostCode')"
            >
            </el-table-column> -->
          </el-table>
        </el-col>
      </el-row>
      <!-- 日最大承接量 -->
      <el-row :gutter="20" class="showDetails-box">
        <el-col :span="4" class="box-title">{{
          $t("MaxCapacity") + ":"
        }}</el-col>
        <el-col :span="20">
          <!-- 最大取件订单量 -->
          <div class="max-pic">
            <div>
              <span>{{ $t("MaxPickOrder") + ":" }}</span
              ><span class="pic-title">{{ details.pickup_max_count }} </span>
            </div>
            <el-table
              max-height="600px"
              :data="qujianList"
              border
              style="width: 100%"
            >
              <el-table-column type="index" align="center" width="50" label="#">
              </el-table-column>
              <el-table-column
                prop="merchant_group_name"
                align="center"
                :label="$t('MerchantGroup')"
              >
              </el-table-column>
              <el-table-column
                prop="pickup_min_count"
                align="center"
                :label="$t('minimumQuantity')"
              >
              </el-table-column>
            </el-table>
          </div>
          <!-- 最大派件订单量 -->
          <div>
            <div>
              <span>{{ $t("MaxDeliveryOrder") + ":" }}</span
              ><span class="pic-title"> {{ details.pie_max_count }} </span>
            </div>
            <el-table
              max-height="600px"
              :data="paijianList"
              border
              style="width: 100%"
            >
              <el-table-column type="index" align="center" width="50" label="#">
              </el-table-column>
              <el-table-column
                prop="merchant_group_name"
                align="center"
                :label="$t('MerchantGroup')"
              >
              </el-table-column>
              <el-table-column
                prop="pie_min_count"
                align="center"
                :label="$t('MinimumPieQuantity')"
              >
              </el-table-column>
            </el-table>
          </div>
        </el-col>
      </el-row>
      <!-- 工作日 -->
      <el-row :gutter="20" class="showDetails-box">
        <el-col :span="4" class="box-title"> {{ $t("weekdays") + ":" }}</el-col>
        <el-col :span="20">{{ work_day_list }}</el-col>
      </el-row>
    </el-dialog>
    <!-- 一键扫描 货主配置 -->
    <el-dialog
      :title="$t('SelectMerchant')"
      :visible.sync="dialogConfigureShipper"
      width="65%"
    >
      <!-- 所有内容 -->
      <el-input
        @keyup.enter.native="getMerchants"
        v-model="merchantPageParams.keyword"
        clearable
        :placeholder="$t('enterContent')"
        size="small"
        class="search-bar"
      >
      </el-input>
      <el-button
        size="small"
        @click="getMerchants"
        type="primary"
        :loading="this.$store.state.config.button_loading"
        >{{ $t("Search") }}</el-button
      >
      <el-table
        :data="merchantData"
        max-height="400px"
        border
        style="margin-top: 10px"
      >
        <!-- <el-table-column width="50" align="center" style="text-align: center">
          <template slot-scope="scope">
            <el-radio v-model="merchant.id" :label="scope.row.id"
              >{{ " " }}
            </el-radio>
          </template>
        </el-table-column> -->
        <el-table-column type="index" align="center" width="50">
          <template slot="header">
            <span>{{ $t("Number") }}</span>
          </template>
        </el-table-column>
        <el-table-column
          property="code"
          align="center"
          :label="$t('cargoOwnerNo')"
        ></el-table-column>
        <el-table-column
          property="name"
          align="center"
          :label="$t('Merchant')"
        ></el-table-column>
        <el-table-column
          property="email"
          align="center"
          :label="$t('Email')"
        ></el-table-column>
        <el-table-column
          property="phone"
          align="center"
          :label="$t('Tel')"
        ></el-table-column>
        <el-table-column
          align="center"
          :label="$t('OneClickScan')"
          prop="can_select_all"
          width="115"
        >
          <template slot-scope="scope">
            <el-switch
              v-model="scope.row.can_select_all"
              @change="putStatusByList(scope.row)"
              :active-value="1"
              :inactive-value="2"
            >
            </el-switch>
          </template>
        </el-table-column>
      </el-table>
    </el-dialog>
  </div>
</template>

<script>
import baseApi from "@/api/axios/base_api";
import googleMap from "@/components/Map/GoogleMap";
import BMap from "@/components/BaiduMap/getAddressByMap";

export default {
  components: {
    baseApi,
    googleMap,
    BMap,
  },
  data() {
    return {
      showCountry: false,
      titleColor: "red",
      template_id: localStorage.getItem("address_template_id") || 1,
      country:
        localStorage.getItem("country") &&
        JSON.parse(localStorage.getItem("country")).en,
      show_line: false, //线路弹出框
      showDetails: false, //详情弹出框
      select_map: false, //地图
      show: false,
      dialogConfigureShipper: false, //一键扫描弹出
      work_day_list: "", //工作日
      parentId: null, //网点
      type: 1, //判断修改 添加
      parent_name: "", //所属网点名字
      level: "", //几级菜单
      addId: "", //添加线路新增id
      putId: "", //添加线路修改id
      postCodeList: [], //邮编列表
      preciseList: [], //精准邮编
      countryList: [], //国家下拉
      networkTree: [], //树节点
      lineList: [], //弹出框线路列表
      qujianList: [], //取件列表
      paijianList: [], //派件列表
      fileList: [], //头像
      lineData: [], //线路列表
      merchantData: [], //货主
      warehouseAcceptanceTypeList: [], //网点功能
      details: {}, //详情
      defaultProps: {
        label: "name",
        children: "children",
      },
      form: {
        id: "",
        code: "", //网点编号
        name: "", //网点名称
        parent: "", //所属网点
        parent_name: "", //所属网点名字
        type: null, //网点类型
        is_center: "1", //是否分拨中心
        can_select_all: 1,
        company_name: "", //公司名称
        fullname: "", //联系人
        phone: "", //电话
        email: "", //邮箱
        avatar: "", //头像
        country: "", //国家
        post_code: "", //邮编
        house_number: "", //门牌号
        city: "", //城市
        street: "", //街道
        address: "", //详细地址
        lon: "", //经度
        lat: "", //纬度
        acceptance_type: [], //网点功能
      },
      addLine: {
        //网点线路添加
        id: "",
        line_ids: [],
      },
      deleteLine: {
        //网点线路删除
        id: "",
        line_ids: "",
      },
      ids: "",
      // 弹出框分页
      params: {
        name: "",
        total: 0,
        page: 1,
        per_page: 10,
      },
      // 表格分页
      pageParams: {
        id: "",
        total: 0,
        page: 1,
        per_page: 10,
      },
      merchantPageParams: {
        keyword: "",
        status: 1,
        per_page: 0,
      },
    };
  },
  computed: {
    disabledBtnByCountry() {
      return this.form.country === "NL";
    },
    Authorization() {
      return { Authorization: this.$store.state.token.token };
    },
    BaseApiFile() {
      return `${baseApi}admin/upload/image/`;
    },
    //   地图
    mapType() {
      return this.$store.state.config.maptype;
    },
    formRules() {
      return {
        code: [
          //网点编号
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        name: [
          //网点名称
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        parent: [
          //所属网点
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        address: [
          //详细地址
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        // company_name: [
        //   //公司名称
        //   { required: true, trigger: "blur", message: this.$t("Required") },
        // ],
        // fullname: [
        //   //联系人
        //   { required: true, trigger: "blur", message: this.$t("Required") },
        // ],
        // phone: [
        //   //电话
        //   { required: true, trigger: "blur", message: this.$t("Required") },
        // ],
        country: [
          //国家
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        avatar: [
          //头像
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        post_code: [
          {
            required: true,
            message: this.$t("PleaseEnterZipCode"),
            trigger: "blur",
          },
        ],
        house_number: [
          {
            required: true,
            message: this.$t("PleaseEnterDoorNo"),
            trigger: "blur",
          },
        ],
        city: [
          {
            required: true,
            message: this.$t("PleaseEnterCity"),
            trigger: "blur",
          },
        ],
        street: [
          {
            required: true,
            message: this.$t("PleaseEnterStreet"),
            trigger: "blur",
          },
        ],
        acceptance_type: [
          //网点功能
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        type: [
          //网点类型
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
      };
    },
  },
  created() {
    this.getNetworkTree();
    this.getDictionary();
    this.getAddedCountry();
  },
  methods: {
    // 配置货主
    configureShipper() {
      this.dialogConfigureShipper = true;
      this.getMerchants();
    },
    //货主列表-查询
    getMerchants() {
      this.$api.getMerchants(this.merchantPageParams).then((res) => {
        this.merchantData = res.data.data;
      });
    },
    // 一键扫描
    putStatusByList(rows) {
      let id = rows.id;
      this.$api.editMerchants(id, rows).then((res) => {
        if (res.code == 200) {
          this.getMerchants();
        }
      });
    },
    getAddedCountry() {
      // 国家列表
      this.$api.getAddedCountry().then((res) => {
        this.countryList = res.data;
      });
    },
    // 改变国家
    changeCountry() {
      if (this.form.country != "" || this.form.country != null) {
        this.showCountry = false;
      } else {
        this.showCountry = true;
      }
      this.form.post_code = "";
      this.form.house_number = "";
      this.form.city = "";
      this.form.street = "";
      this.form.lon = "";
      this.form.lat = "";
    },
    getLocation() {
      // 判断国家显隐
      if (this.form.country == "" || this.form.country == null) {
        this.showCountry = true;
      } else {
        this.showCountry = false;
      }
      //判断国家填充城市
      if (
        this.form.post_code &&
        this.form.house_number &&
        this.form.country === "NL"
      ) {
        let isNL = false;
        if (this.form.country === "NL") {
          this.form.city = "";
          this.form.street = "";
          isNL = true;
        }
        this.$api
          .getLocation({
            country: this.form.country,
            post_code: this.form.post_code,
            house_number: this.form.house_number,
          })
          .then((res) => {
            if (!res) return;
            this.form.lat = res.data.lat;
            this.form.lon = res.data.lon;
            if (isNL) {
              this.form.city = res.data.city;
              this.form.street = res.data.street;
            }
          });
      } else if (
        this.form.post_code &&
        this.form.house_number &&
        this.form.city &&
        this.form.street
      ) {
        this.$api
          .getLocation({
            country: this.form.country,
            post_code: this.form.post_code,
            house_number: this.form.house_number,
            city: this.form.city,
            street: this.form.street,
          })
          .then((res) => {
            if (!res) return;
            this.form.lat = res.data.lat;
            this.form.lon = res.data.lon;
          });
      }
    },
    // 数据字典
    getDictionary() {
      this.$api.getDictionary().then((res) => {
        this.warehouseAcceptanceTypeList = res.data.warehouseAcceptanceTypeList;
      });
    },

    // 树节点
    getNetworkTree() {
      this.$api.getNetworkTree().then((res) => {
        this.networkTree = res.data;
      });
    },
    // 详情
    getWarehouseDetails() {
      this.$api.getWarehouseDetails(this.pageParams.id).then((res) => {
        let list = res.data;
        this.form = list;
        this.parent_name = res.data.name; //获取所属网点名称
        if (list.acceptance_type == "" || null) {
          // 如果等于null就返回一个空的数组
          return (list.acceptance_type = []);
        } else {
          // 否则就取出每一项
          const temp = [...list.acceptance_type.split(",")];
          var arr = [];
          temp.forEach((item) => {
            arr.push(Number(item));
          });
          this.form.acceptance_type = arr;
        }
      });
    },
    // 展示内容
    handleNodeClick(el, node) {
      this.show = true;
      this.type = 2;
      this.level = node.level; //获取到第几级菜单
      this.parentId = el.parent; //判断网点id
      this.pageParams.id = el.id; //获取详情id
      this.addId = el.id; //添加线路新增id
      this.putId = el.parent; //添加线路修改id
      this.deleteLine.id = el.id;
      this.addLine.id = el.id;
      this.getWarehouseDetails();
      this.params.page = 1; //每次切换分页都是1
      this.pageParams.page = 1; //每次切换分页都是1
      this.getNetwork();
      if (this.$refs.form) {
        this.$refs.form.resetFields();
      }
    },

    // 获取承包路线 展示列表
    getNetwork() {
      if (this.type == 2) {
        this.$api.getNetwork({ ...this.pageParams }).then((res) => {
          this.lineData = res.data.data;
          this.pageParams.total = res.data.meta.total;
        });
      }
    },

    // 弹出列表
    getNetworkAll() {
      // 新增时线路添加
      if (this.type == 1) {
        this.$api
          .getNetworkAll({ ...this.params, id: this.addId })
          .then((res) => {
            this.lineList = res.data.data;
            this.params.total = res.data.meta.total;
          });
      } else {
        // 修改时线路添加
        this.$api
          .getNetworkAll({ ...this.params, id: this.putId })
          .then((res) => {
            this.lineList = res.data.data;
            this.params.total = res.data.meta.total;
          });
      }
      if (this.$refs.multipleTable) {
        this.$refs.multipleTable.clearSelection(); //清除表格的选中项
      }
    },
    // 表格选中项
    handleSelectionChange(val) {
      this.selected = val;
    },
    // 判断选中项 禁用
    selectable(row, index) {
      if (
        this.lineData.some((el) => {
          return el.id === row.id;
        })
      ) {
        return false;
      } else {
        return true;
      }
    },
    //   添加行
    addRow() {
      this.show_line = true;
      this.getNetworkAll();
    },
    // 确定
    confirm() {
      this.show_line = false;
      var selected = this.selected;
      var list = this.selected;
      // 给原有的线路列表 添加新的线路
      if (this.type == 1) {
        selected.map((el) => {
          if (this.type == 1) {
            this.lineData.push(el);
          }
        });
      }
      if (this.type == 2) {
        this.addLine.line_ids = [];
        list.map((item) => {
          this.addLine.line_ids.push(item.id);
        });
        this.$api
          .addNetworkLine({
            ...this.addLine,
            line_ids: this.addLine.line_ids.join(","),
          })
          .then((res) => {
            if (res.code == 200) {
              this.getNetwork();
            }
          });
      }
    },
    // 删除行
    deleteRow(row) {
      if (this.type == 1) {
        let lineData = this.lineData;
        for (var i = 0; i < lineData.length; i++) {
          if (lineData[i].id === row.id) {
            lineData.splice(i, 1);
          }
        }
        // lineData.map((el) => {
        //   ;
        //   if (el.id === row.id) {
        //     this.lineData.splice(el, 1);
        //   }
        // });
      } else {
        this.ids = row.id;
        this.deleteLine = {
          ...this.deleteLine,
          line_ids: this.ids,
        };
        this.$api
          .delNetworkLine(this.deleteLine.id, this.deleteLine.line_ids)
          .then((res) => {
            if (res.code == 200) {
              this.getNetwork();
            }
          });
      }
    },
    // 详情
    getLineItem(row) {
      this.postCodeList = [];
      this.preciseList = [];
      this.showDetails = true;
      this.work_day_list = row.work_day_list; //工作日
      let detailsId = row.id; //id
      // 获取详情信息
      this.$api.getLineItem(detailsId).then((res) => {
        this.details = res.data; //详情信息
        var pinKupArr = [];
        var pieArr = [];
        res.data.merchant_group_count_list.map((el) => {
          if (el.pickup_min_count != 0) {
            var pinKupObj = {
              id: el.merchant_group_id,
              merchant_group_name: el.merchant_group_name,
              pickup_min_count: el.pickup_min_count,
            };
            pinKupArr.push(pinKupObj);
          }
          if (el.pie_min_count != 0) {
            var pieObj = {
              id: el.merchant_group_id,
              merchant_group_name: el.merchant_group_name,
              pie_min_count: el.pie_min_count,
            };
            pieArr.push(pieObj);
          }
        });
        this.qujianList = pinKupArr; //取件列表
        this.paijianList = pieArr; //派件列表
        let lsit = res.data.line_range;
        lsit.map((el) => {
          if (el.type == 1) {
            this.postCodeList.push(el);
          } else {
            this.arrayTransfer(res.data.line_range);
          }
        });
      });
    },
    arrayTransfer(data) {
      const listArr = [];
      data.forEach((el) => {
        if (el.type == 2) {
          for (let i = 0; i < listArr.length; i++) {
            if (listArr[i].country === el.country) {
              listArr[i].dynamicTags.push(el.post_code_start);
              return;
            }
          }
          listArr.push({
            country: el.country,
            country_name: el.country_name,
            post_code_end: el.post_code_end,
            type: el.type,
            dynamicTags: [el.post_code_start],
          });
        }
        this.preciseList = listArr;
      });
    },
    // 网点删除
    delWarehouse(node, data) {
      console.log("node, data");
      console.log(node, data);
      // if (this.parentId == 0) {
      //   this.$message.error("总部网点不可删除");
      // } else {
      this.$confirm(this.$t("AreYouDelete") + "?", this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      }).then(() => {
        this.$api.delWarehouse(data.id).then((res) => {
          if (res.code == 200) {
            this.getNetworkTree();
            this.show = false;
          }
        });
      });
      // }
    },
    // 添加
    addNetwork(data) {
      this.addId = data.id;
      this.show = true;
      this.type = 1;
      // // 判断有没有选中网点
      // if (this.parentId == null) {
      //   this.$message.error("请先选择网点");
      // } else if (this.level == 3) {
      //   //判断等于三就不可以新增
      //   this.$message.error("目前只支持三级菜单");
      // } else {
      this.form = {
        id: "",
        code: "", //网点编号
        name: "", //网点名称
        type: "", //网点类型
        is_center: 2, //是否分拨中心
        can_select_all: 2,
        company_name: "", //公司名称
        fullname: "", //联系人
        phone: "", //电话
        email: "", //邮箱
        avatar: "", //头像
        country: "", //国家
        post_code: "", //邮编
        house_number: "", //门牌号
        city: "", //城市
        street: "", //街道
        address: "", //详细地址
        lon: "", //经度
        lat: "", //纬度
        acceptance_type: [], //网点功能
      };
      // 判断是否为分拨中心
      if (data.parent == 0) {
        this.form.is_center = 1;
      }
      this.lineData = [];
      this.form.parent = data.id; //所属网点
      this.form.parent_name = data.name; //网点名字
      this.pageParams = {
        id: "",
        total: 0,
        page: 1,
        per_page: 10,
      };
      // }
    },
    //   保存
    putNetwork() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          // 新增
          if (this.type == 1) {
            let lineData = this.lineData;
            let line_ids = []; //线路ID列表
            // 取出线路id
            lineData.map((item) => {
              line_ids.push(item.id);
            });
            this.$api
              .addWarehouse({
                ...this.form,
                acceptance_type: this.form.acceptance_type.join(","),
                line_ids: line_ids.join(","),
              })
              .then((res) => {
                if (res.code == 200) {
                  this.getNetworkTree();
                  this.show = false;
                }
              });
          } else {
            // 修改
            if (this.form.type == 0) {
              // 如果网点类型等于就返回null/""
              return (this.form.type = "" || null);
            }
            this.$api
              .putWarehouse({
                ...this.form,
                acceptance_type: this.form.acceptance_type.join(","),
              })
              .then((res) => {
                if (res.code == 200) {
                  this.getNetworkTree();
                }
              });
          }
        }
      });
    },
    // putNetwork() {
    //   this.$refs.form.validate((valid) => {
    //     if (valid) {
    //       let lineData = this.lineData;
    //       let line_ids = []; //线路ID列表
    //       // 取出线路id
    //       lineData.map((item) => {
    //         line_ids.push(item.id);
    //       });
    //       // 新增
    //       if (this.type == 1) {
    //         this.$api
    //           .addWarehouse({
    //             ...this.form,
    //             acceptance_type: this.form.acceptance_type.join(","),
    //             line_ids: line_ids.join(","),
    //           })
    //           .then((res) => {
    //             console.log("this.form");
    //             console.log(this.form.type);
    //             if (res.code == 200) {
    //               this.getNetworkTree();
    //               this.show = false;
    //             }
    //           });
    //       } else {
    //         if (this.parentId == 0) {
    //           this.$api
    //             .putWarehouse({
    //               ...this.form,
    //               acceptance_type: this.form.acceptance_type.join(","),
    //               line_ids: this.form.line_ids,
    //               // ...this.pageParams,
    //             })
    //             .then((res) => {
    //               // console.log("this.form.line_ids");
    //               // console.log(this.form.line_ids);
    //               if (res.code == 200) {
    //                 this.getNetworkTree();
    //               }
    //             });
    //         } else {
    //           if (this.form.type == 0) {
    //             return (this.form.type = "" || null);
    //           }
    //           // 修改
    //           this.$api
    //             .putWarehouse({
    //               ...this.form,
    //               acceptance_type: this.form.acceptance_type.join(","),
    //               line_ids: line_ids.join(","),
    //               // ...this.pageParams,
    //             })
    //             .then((res) => {
    //               // console.log("this.form.line_ids");
    //               // console.log(this.form.line_ids);
    //               if (res.code == 200) {
    //                 this.getNetworkTree();
    //               }
    //             });
    //         }
    //       }
    //     }
    //   });
    // },
    // 联系地址
    selectAddressMap() {
      this.select_map = true;
    },
    // 获取地图的定位信息
    handlerAddressSelected(data) {
      // console.log("data");
      // console.log(data);
      this.form.address = data.address;
      this.form.country = data.country;
      this.form.post_code = data.postal_code;
      this.form.city = data.city;
      this.form.house_number = data.street_number;
      this.form.street = `${data.route}`;
      this.form.lon = data.location.lng;
      this.form.lat = data.location.lat;
      this.select_map = false;
    },
    // 图片
    handlerClearAvatar() {
      this.fileList = [];
      this.form.avatar = "";
    },
    handleAvatarSuccess(response) {
      if (response.code === 200) {
        this.form.avatar = response.data.path;
        this.$refs.avatar.clearValidate();
      }
    },
    getTitle(node) {
      // console.log("data");
      // console.log(data);
      if (node.level == 1) {
        return "title-color";
      } else if (node.level == 2) {
        return "title-color-1";
      } else {
        return "title-color-2";
      }
    },
  },
};
</script>

<style lang="less" scoped>
.network {
  .box-card {
    height: 1000px;
    .card-but {
      margin-bottom: 20px;
    }
    .custom-tree-node {
      flex: 2;
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: 14px;
      padding-right: 3px;
      .operation {
        display: flex;
        gap: 10px;
        align-items: center;
      }
    }
  }
  .add-row {
    float: right;
    margin-bottom: 10px;
    margin-top: -40px;
  }
  .footer-but {
    .el-button {
      border: #4899d0;
      background-color: #4899d0;
      padding-left: 50px;
      padding-right: 50px;
      margin-left: 60px;
    }
  }
  /deep/ .el-upload-list__item-name {
    width: 100px;
  }
  .confirm-but {
    border: #4899d0;
    background-color: #4899d0;
  }
  .showDetails-box {
    margin-bottom: 25px;
    .max-pic {
      margin-bottom: 20px;
      .pic-title {
        margin-left: 10px;
      }
    }
    .box-title {
      text-align: right;
    }
  }
  .title-color {
    // margin-bottom: 10px;
    font-size: 18px;
    font-weight: bold;
  }
  .title-color-1 {
    font-size: 15px;
  }
  .title-color-2 {
    color: rgb(160, 158, 158);
  }
  /deep/.el-tree-node__content {
    height: 40px !important;
    // border-bottom: solid 1px red;
  }
}
// .select-country {
//   /deep/.el-input__inner {
//     border-color: #f56c6c;
//   }
// }
.country {
  margin-top: -10px;
  margin-bottom: -20px;
  font-size: 5px;
  color: #f56c6c;
}
.el-tag {
  margin-right: 5px;
}
.btn {
  float: center;
}
</style>
