<template>
  <div class="order-list">
    <!-- 订单列表 -->
    <el-tabs type="border-card" @tab-click="changeOrderType">
      <el-tab-pane :label="$t('All')" name="0"></el-tab-pane>
      <el-tab-pane :label="$t('DeliveryOrder')" name="2"></el-tab-pane>
      <el-tab-pane :label="$t('PickupOrder')" name="1"></el-tab-pane>
      <el-tab-pane :label="$t('pickUpOeliveryOrder')" name="3"></el-tab-pane>
    </el-tabs>

    <div class="dispatch-order">
      <div class="top_btn_grop">
        <el-checkbox
          v-model="pageParams.exception_label"
          :true-label="2"
          :false-label="''"
          @change="handleChangePageParams"
          >{{ $t("SendAbnormal") }}</el-checkbox
        >
        <!-- 新增 -->
        <el-tooltip
          :content="$t('AdministratorActivate')"
          placement="top"
          :disabled="hasPerm('order.store') ? true : false"
        >
          <div>
            <el-button
              :disabled="!hasPerm('order.store')"
              size="small"
              type="primary"
              @click="addNewOrder"
              style="margin-right: 5px"
              >{{ $t("Add") }}</el-button
            >
          </div>
        </el-tooltip>

        <!-- 导入 -->
        <el-tooltip
          :content="$t('AdministratorActivate')"
          placement="top"
          :disabled="hasPerm('order.import-list') ? true : false"
        >
          <div>
            <el-button
              :disabled="!hasPerm('order.import-list')"
              :class="[
                hasPerm('order.import-list') ? 'common' : 'common-disabled',
                'top-btn',
              ]"
              size="small"
              @click="orderUpload"
              style="margin-right: -5px"
              >{{ $t("ToChannel") }}</el-button
            >
          </div>
        </el-tooltip>

        <!-- 导出 -->
        <el-tooltip
          :content="$t('AdministratorActivate')"
          placement="top"
          :disabled="hasPerm('order.export') ? true : false"
        >
          <!-- <el-popover
            placement="bottom"
            trigger="click"
            v-model="popoverShow"
            :disabled="!hasPerm('order.export')"
          > -->
          <!-- <div>
              <el-date-picker
                v-model="export_search_date"
                type="daterange"
                value-format="yyyy-MM-dd"
                :range-separator="$t('To')"
                :start-placeholder="$t('StartTime')"
                :end-placeholder="$t('EndTime')"
              >
              </el-date-picker>
              <div style="text-align: center; margin-top: 10px">
                <el-button
                  type="primary"
                  size="small"
                  @click="popoverShow = false"
                  class="cancel"
                  >{{ $t("Cancel") }}</el-button
                >
                <el-button
                  type="primary"
                  size="small"
                  @click="getOrderExcel()"
                  class="submit"
                  >{{ $t("Confirm") }}</el-button
                >
              </div>
            </div> -->
          <!-- </el-popover> -->
          <div>
            <el-button
              :class="[
                hasPerm('order.export') ? 'common' : 'common-disabled',
                'top-btn',
              ]"
              slot="reference"
              size="small"
              :disabled="!hasPerm('order.export')"
              @click="getOrderExcel"
              >{{ $t("Export") }}</el-button
            >
          </div>
        </el-tooltip>

        <!-- 批量操作 -->
        <el-dropdown
          size="small"
          trigger="hover"
          placement="bottom"
          class="mult-control-btn"
        >
          <el-button size="small" class="common top-btn">
            {{ $t("bulkOperations") }}
            <i class="el-icon-arrow-down el-icon--right"></i>
          </el-button>
          <el-dropdown-menu slot="dropdown">
            <!-- <el-dropdown-item v-if="pageParams.type != 2">
                        <el-button class="operation" size="small" type="text" @click.native="handlerLineAdd(addOrderData)">{{$t('线路加单')}}</el-button>
                    </el-dropdown-item>
                    <el-dropdown-item>
                        <el-button class="operation" size="small" type="text" @click.native="removeStation(orderData)">{{$t('站点移除')}}</el-button>
                    </el-dropdown-item> -->
            <!-- 批量打印 -->
            <el-tooltip
              :content="$t('AdministratorActivate')"
              placement="top"
              :disabled="hasPerm('order.print') ? true : false"
            >
              <div :style="hasPerm('order.print') ? '' : 'cursor: not-allowed'">
                <el-dropdown-item
                  @click.native="getOrderPrintAll(id_list)"
                  :disabled="!hasPerm('order.print')"
                >
                  <el-button
                    class="operation top-btn"
                    size="small"
                    type="text"
                    :disabled="!hasPerm('order.print')"
                    >{{ $t("batchPrint") }}</el-button
                  >
                </el-dropdown-item>
              </div>
            </el-tooltip>

            <!-- <el-dropdown-item>
                        <el-button class="operation" size="small" type="text" @click="getOrderExcel()">{{$t('订单导出')}}</el-button>
                    </el-dropdown-item> -->
            <!-- 批量删除 -->
            <el-tooltip
              :content="$t('AdministratorActivate')"
              placement="top"
              :disabled="hasPerm('order.destroy') ? true : false"
            >
              <div
                :style="hasPerm('order.destroy') ? '' : 'cursor: not-allowed'"
              >
                <el-dropdown-item
                  @click.native="batchDeletion(multipleSelection)"
                  :disabled="!hasPerm('order.destroy')"
                >
                  <el-button
                    class="operation"
                    :disabled="!hasPerm('order.destroy')"
                    size="small"
                    type="text"
                    >{{ $t("batchDelete") }}</el-button
                  >
                </el-dropdown-item>
              </div>
            </el-tooltip>
            <!-- 同步订单状态 -->
            <el-tooltip
              :content="$t('AdministratorActivate')"
              placement="top"
              :disabled="hasPerm('order.synchronize') ? true : false"
            >
              <div
                :style="hasPerm('order.destroy') ? '' : 'cursor: not-allowed'"
              >
                <el-dropdown-item
                  @click.native="synchronizeStatusList(id_list)"
                  :disabled="!hasPerm('order.synchronize')"
                >
                  <el-button
                    class="operation"
                    :disabled="!hasPerm('order.synchronize')"
                    size="small"
                    type="text"
                    >{{ $t("syncOrderStatus") }}</el-button
                  >
                </el-dropdown-item>
              </div>
            </el-tooltip>
          </el-dropdown-menu>
        </el-dropdown>
      </div>
      <el-tabs v-model="tab" @tab-click="changeTab">
        <div class="conditional">
          <span style="margin-top: 6px">{{ $t("DistributeDate") }} </span>
          <el-date-picker
            size="small"
            v-model="search_date"
            type="daterange"
            value-format="yyyy-MM-dd"
            :range-separator="$t('To')"
            :start-placeholder="$t('StartTime')"
            :end-placeholder="$t('EndTime')"
            :picker-options="pickerOptionsSearch"
            class="search-bar-date"
            style="margin-bottom: 8px"
          >
          </el-date-picker>
          <el-select
            clearable
            size="small"
            v-model="pageParams.merchant_id"
            @keyup.enter.native="handleChangePageParams"
            :placeholder="$t('PleaseSelectMerchant')"
            class="search-bar"
          >
            <el-option
              v-for="(item, index) in merchantsList"
              :key="index"
              :label="item.name"
              :value="item.id"
            ></el-option>
          </el-select>
          <!-- 查询 -->
          <el-input
            size="small"
            clearable
            :placeholder="$t('PleaseEnterOrderNo')"
            @keyup.enter.native="handleChangePageParams"
            v-model="pageParams.order_no"
            class="search-bar"
          >
          </el-input>
          <el-input
            size="small"
            clearable
            :placeholder="$t('PleaseenterExternalOrderNumber')"
            @keyup.enter.native="handleChangePageParams"
            v-model="pageParams.out_order_no"
            class="search-bar"
          >
          </el-input>
          <el-input
            size="small"
            clearable
            :placeholder="$t('pleaseEnterWaybillNo')"
            @keyup.enter.native="handleChangePageParams"
            v-model="pageParams.tracking_order_no"
            class="search-bar"
          >
          </el-input>
          <el-input
            size="small"
            clearable
            :placeholder="$t('customerNumber')"
            @keyup.enter.native="handleChangePageParams"
            v-model="pageParams.out_user_id"
            class="search-bar"
          >
          </el-input>
          <el-input
            size="small"
            clearable
            :placeholder="$t('PleaseEnterTheExternalAppointmentBatchNumber')"
            @keyup.enter.native="handleChangePageParams"
            v-model="pageParams.out_group_order_no"
            class="search-bar"
          >
          </el-input>
          <el-button
            size="small"
            @click="handleChangePageParams"
            :loading="this.$store.state.config.button_loading"
            type="primary"
            >{{ $t("Search") }}</el-button
          >

          <!-- 查询 -->

          <!-- <el-row :gutter="10"> -->
          <!-- <el-col :span="6">
              <el-input size="small" clearable :placeholder="$t('请输入站点编号')" @keyup.enter.native="handleChangePageParams" @clear="handleChangePageParams" v-model="pageParams.batch_no">
              </el-input>
            </el-col> -->
          <!-- <el-col :span="6">
              <el-input size="small" clearable :placeholder="$t('PleaseEnterZipCode')" @keyup.enter.native="handleChangePageParams" @clear="handleChangePageParams" v-model="pageParams.place_post_code">
              </el-input>
            </el-col> -->
          <!-- <el-col :span="6" class="search">
              <el-input size="small" clearable :placeholder="$t('请输入取件线路')" @keyup.enter.native="handleChangePageParams" @clear="handleChangePageParams" v-model="pageParams.tour_no">
                <el-button slot="append" size="small" @click="handleChangePageParams" :loading="this.$store.state.config.button_loading" type="primary" >{{$t('Search')}}</el-button>
              </el-input>
            </el-col> -->
          <!-- </el-row> -->
        </div>
        <el-tab-pane
          v-for="(item, index) in orderList"
          :label="`${item.name} ${item.total || 0}`"
          :name="item.name + ''"
          :key="index"
        >
          <!-- :data="hasPerm('order.index') ? tableData : []" 判断没有权限是列表为空 -->
          <el-table
            :height="
              heightSearch ? 'calc(100vh - 358px)' : 'calc(100vh - 326px)'
            "
            :data="hasPerm('order.index') ? tableData : []"
            :ref="`orderTable${item.id}`"
            v-loading="global_loading()"
            @selection-change="handleSelectionChange"
            @select="checkItemBox"
            @select-all="checkAllBox"
            @row-click="hangleRowClick"
            @expand-change="clearWaybill"
            element-loading-text="loading..."
            border
            style="width: 100%"
            :cell-style="{ padding: '5px 0px' }"
          >
            <!-- 改变table为空时的背景内容 -->
            <template slot="empty" v-if="!hasPerm('order.index')">
              <div style="font-size: 18px">
                {{ $t("AdministratorActivate") }}
              </div>
            </template>
            <el-table-column type="expand">
              <template slot-scope="props">
                <el-table
                  max-height="600px"
                  :data="props.row.waybillList"
                  border
                  style="width: 100%"
                  :cell-style="{ padding: '5px 0px' }"
                >
                  <el-table-column
                    prop="tracking_order_no"
                    align="center"
                    :label="$t('waybillNo')"
                  >
                    <template slot-scope="scope">
                      <el-link
                        class="link"
                        :underline="false"
                        @click="
                          hasPerm('tracking-order.index')
                            ? $router.push({
                                name: 'WaybillManagement',
                                params: {
                                  tracking_order_no:
                                    scope.row.tracking_order_no,
                                },
                              })
                            : {}
                        "
                        >{{ scope.row.tracking_order_no }}</el-link
                      >
                    </template>
                  </el-table-column>
                  <el-table-column
                    prop="type_name"
                    align="center"
                    :label="$t('WaybillType')"
                  >
                  </el-table-column>
                  <el-table-column
                    prop="status_name"
                    align="center"
                    :label="$t('waybillStatus')"
                  >
                  </el-table-column>
                  <el-table-column
                    prop="execution_date"
                    align="center"
                    :label="$t('DistributeDate')"
                  >
                  </el-table-column>
                  <el-table-column
                    prop="place_post_code"
                    align="center"
                    :label="$t('ZipCode')"
                  >
                  </el-table-column>
                  <el-table-column
                    prop="place_house_number"
                    align="center"
                    :label="$t('DoorNo')"
                  >
                  </el-table-column>
                </el-table>
              </template>
            </el-table-column>
            <el-table-column align="center" type="selection" width="80">
            </el-table-column>
            <el-table-column
              prop="date"
              :label="$t('Operation')"
              align="center"
              width="120"
            >
              <template slot-scope="scope">
                <el-dropdown size="mini" trigger="hover" placement="bottom">
                  <el-button size="mini" type="primary" plain class="operation">
                    {{ $t("Operation") }}
                    <i class="el-icon-arrow-down el-icon--right"></i>
                  </el-button>
                  <!-- 同步订单状态 -->
                  <el-dropdown-menu slot="dropdown">
                    <el-tooltip
                      :content="$t('AdministratorActivate')"
                      placement="top"
                      :disabled="hasPerm('order.synchronize') ? true : false"
                    >
                      <div
                        :style="
                          hasPerm('order.synchronize')
                            ? ''
                            : 'cursor: not-allowed'
                        "
                      >
                        <el-dropdown-item
                          :disabled="!hasPerm('order.synchronize')"
                          v-if="
                            (scope.row.status == 2 ||
                              scope.row.status == 3 ||
                              scope.row.status == 4) &&
                            scope.row.exception_label == 1
                          "
                          @click.native="synchronizeStatusList(scope.row.id)"
                        >
                          <el-button
                            class="operation"
                            :disabled="!hasPerm('order.synchronize')"
                            size="small"
                            type="text"
                            >{{ $t("syncOrderStatus") }}</el-button
                          >
                        </el-dropdown-item>
                      </div>
                    </el-tooltip>
                    <!-- 查看详情 -->
                    <el-tooltip
                      :content="$t('AdministratorActivate')"
                      placement="top"
                      :disabled="hasPerm('order.index') ? true : false"
                    >
                      <div
                        :style="
                          hasPerm('order.index') ? '' : 'cursor: not-allowed'
                        "
                      >
                        <el-dropdown-item
                          @click.native="viewDetails(scope.row)"
                          :disabled="!hasPerm('order.index')"
                        >
                          <el-button
                            class="operation"
                            size="small"
                            type="text"
                            :disabled="!hasPerm('order.index')"
                            >{{ $t("viewDetails") }}</el-button
                          >
                        </el-dropdown-item>
                      </div>
                    </el-tooltip>
                    <!-- 查看记录 -->
                    <!-- <el-dropdown-item
                      v-if="hasPerm('order.show')"
                      @click.native="showOrderDetails(scope.row.id)"
                    >
                      <el-button class="operation" size="small" type="text">{{
                        $t("Details")
                      }}</el-button>
                    </el-dropdown-item> -->

                    <!-- 查看轨迹 -->
                    <el-tooltip
                      :content="$t('AdministratorActivate')"
                      placement="top"
                      :disabled="hasPerm('order.trail') ? true : false"
                    >
                      <div
                        :style="
                          hasPerm('order.trail') ? '' : 'cursor: not-allowed'
                        "
                      >
                        <el-dropdown-item
                          @click.native="getOrderTrail(scope.row.order_no)"
                          :disabled="!hasPerm('order.trail')"
                          v-if="scope.row.status != 5"
                        >
                          <el-button
                            class="operation"
                            size="small"
                            type="text"
                            :disabled="!hasPerm('order.trail')"
                            >{{ $t("Track") }}</el-button
                          >
                        </el-dropdown-item>
                      </div>
                    </el-tooltip>
                    <!-- 第三方对接日志 -->
                    <el-tooltip
                      :content="$t('AdministratorActivate')"
                      placement="top"
                      :disabled="
                        hasPerm('order.third-party-log') ? true : false
                      "
                    >
                      <div
                        :style="
                          hasPerm('order.third-party-log')
                            ? ''
                            : 'cursor: not-allowed'
                        "
                      >
                        <el-dropdown-item
                          @click.native="getThirdPartyLog(scope.row.id)"
                          :disabled="!hasPerm('order.third-party-log')"
                          v-if="scope.row.status != 5"
                        >
                          <el-button
                            class="operation"
                            size="small"
                            type="text"
                            :disabled="!hasPerm('order.third-party-log')"
                            >{{ $t("ThirdPartyDockingLog") }}</el-button
                          >
                        </el-dropdown-item>
                      </div>
                    </el-tooltip>

                    <!-- 删除订单 -->
                    <el-tooltip
                      :content="$t('AdministratorActivate')"
                      placement="top"
                      :disabled="hasPerm('order.destroy') ? true : false"
                    >
                      <div
                        :style="
                          hasPerm('order.destroy') ? '' : 'cursor: not-allowed'
                        "
                      >
                        <el-dropdown-item
                          @click.native="delOrder(scope.row.id)"
                          :disabled="!hasPerm('order.destroy')"
                          v-if="scope.row.status == 1"
                        >
                          <el-button
                            class="operation"
                            size="small"
                            type="text"
                            :disabled="!hasPerm('order.destroy')"
                            >{{ $t("DeleteOrder") }}</el-button
                          >
                        </el-dropdown-item>
                      </div>
                    </el-tooltip>
                    <!-- 编辑订单 -->
                    <el-tooltip
                      :content="$t('AdministratorActivate')"
                      placement="top"
                      :disabled="hasPerm('order.update') ? true : false"
                    >
                      <div
                        :style="
                          hasPerm('order.update') ? '' : 'cursor: not-allowed'
                        "
                      >
                        <el-dropdown-item
                          @click.native="toEditOrder(scope.row.id)"
                          v-if="scope.row.status == 1"
                          :disabled="!hasPerm('order.update')"
                        >
                          <el-button
                            class="operation"
                            size="small"
                            type="text"
                            :disabled="!hasPerm('order.update')"
                            >{{ $t("EditOrder") }}</el-button
                          >
                        </el-dropdown-item>
                      </div>
                    </el-tooltip>

                    <!-- 继续派送 -->
                    <el-tooltip
                      :content="$t('AdministratorActivate')"
                      placement="top"
                      :disabled="hasPerm('order.again') ? true : false"
                    >
                      <div
                        :style="
                          hasPerm('order.again') ? '' : 'cursor: not-allowed'
                        "
                      >
                        <el-dropdown-item
                          @click.native="getAgainInfo(scope.row.id)"
                          v-if="scope.row.status == 2 || scope.row.status == 1"
                          :disabled="!hasPerm('order.again')"
                        >
                          <el-button
                            class="operation"
                            size="small"
                            type="text"
                            :disabled="!hasPerm('order.again')"
                            >{{ $t("continueDelivery") }}</el-button
                          >
                        </el-dropdown-item>
                      </div>
                    </el-tooltip>
                    <!-- 终止派送 -->
                    <el-tooltip
                      :content="$t('AdministratorActivate')"
                      placement="top"
                      :disabled="hasPerm('order.end') ? true : false"
                    >
                      <div
                        :style="
                          hasPerm('order.end') ? '' : 'cursor: not-allowed'
                        "
                      >
                        <el-dropdown-item
                          @click.native="putAgainEnd(scope.row.id)"
                          v-if="scope.row.status == 2 || scope.row.status == 1"
                          :disabled="!hasPerm('order.end')"
                        >
                          <el-button
                            class="operation"
                            size="small"
                            type="text"
                            :disabled="!hasPerm('order.end')"
                            >{{ $t("terminateDelivery") }}</el-button
                          >
                        </el-dropdown-item>
                      </div>
                    </el-tooltip>
                    <!-- <el-dropdown-item v-if="scope.row.status === 1" @click.native="openAssignToBatchDalog(scope.row.id)">
                              <el-button class="operation" size="small" type="text">{{$t('AssignToExistingSite')}}</el-button>
                          </el-dropdown-item>
                          <el-dropdown-item @click.native="removeFromBatch(scope.row.id)">
                              <el-button class="operation" size="small" type="text">{{$t('RemoveFromCurrentSite')}}</el-button>
                          </el-dropdown-item> -->
                    <!-- <el-dropdown-item @click.native="openRecoveryOrder(scope.row.id)">
                              <el-button class="operation" size="small" type="text">{{$t('Recover')}}</el-button>
                          </el-dropdown-item>
                          <el-dropdown-item @click.native="actualDestroyOrder(scope.row.id)">
                              <el-button class="operation" size="small" type="text">{{$t('DeletePermanently')}}</el-button>
                          </el-dropdown-item> -->
                    <!-- <el-dropdown-item v-if="(scope.row.status == 4 || scope.row.status == 5 || scope.row.status == 6) && scope.row.exception_label == 1" @click.native="synchronizeStatusList(scope.row.id)">
                              <el-button class="operation" size="small" type="text">{{$t('同步订单状态')}}</el-button>
                          </el-dropdown-item>-->

                    <!-- 订单无效化 -->
                    <el-tooltip
                      :content="$t('AdministratorActivate')"
                      placement="top"
                      :disabled="hasPerm('order.neutralize') ? true : false"
                    >
                      <div
                        :style="
                          hasPerm('order.neutralize')
                            ? ''
                            : 'cursor: not-allowed'
                        "
                      >
                        <el-dropdown-item
                          v-if="scope.row.status == 3"
                          :disabled="!hasPerm('order.neutralize')"
                          @click.native="orderNeutralize(scope.row.id)"
                        >
                          <el-button
                            class="operation"
                            size="small"
                            type="text"
                            :disabled="!hasPerm('order.neutralize')"
                            >{{ $t("OrderInvalidation") }}</el-button
                          >
                        </el-dropdown-item>
                      </div>
                    </el-tooltip>
                  </el-dropdown-menu>
                </el-dropdown>
              </template>
            </el-table-column>
            <el-table-column
              prop="order_no"
              align="center"
              width="160"
              :label="$t('OrderNumber')"
            >
              <template slot-scope="scope">
                <div>
                  {{ scope.row.order_no }}
                </div>
                <div
                  v-if="scope.row.exception_label !== 1"
                  class="el-icon-warning icon-warning"
                  style="color: #fea100"
                ></div>
              </template>
            </el-table-column>
            <el-table-column
              prop="merchant_id_name"
              align="center"
              :label="$t('Merchantinquiry')"
              width="170"
            >
            </el-table-column>
            <el-table-column
              prop="merchant_id_code"
              align="center"
              width="150"
              :label="$t('cargoOwnerCode')"
            >
            </el-table-column>
            <el-table-column
              prop="type_name"
              align="center"
              :label="$t('OrderType')"
              width="150"
            >
            </el-table-column>
            <el-table-column
              prop="status_name"
              align="center"
              width="140"
              :label="$t('OrderStatus')"
            >
              <template slot-scope="scope">
                <span v-if="scope.row.status === 7" style="color: #efb336">{{
                  scope.row.status_name
                }}</span>
                <span v-else>{{ scope.row.status_name }}</span>
              </template>
            </el-table-column>
            <el-table-column
              prop="tracking_order_status_name"
              align="center"
              width="180"
              :label="$t('waybillStatus')"
            >
            </el-table-column>
            <el-table-column
              prop="tracking_order_count"
              align="center"
              :label="$t('waybillQuantity')"
              width="140"
            >
            </el-table-column>
            <el-table-column
              prop="out_user_id"
              align="center"
              width="150"
              :label="$t('customerNumber')"
            >
            </el-table-column>
            <el-table-column
              prop="out_order_no"
              align="center"
              width="180"
              :label="$t('ExternalOrderNumber')"
            >
            </el-table-column>
            <el-table-column
              prop="source_name"
              align="center"
              :label="$t('OrderSource')"
              width="140"
            >
            </el-table-column>
            <!-- <el-table-column
              prop="out_status_name"
              align="center"
              :label="$t('可否出库')">
            </el-table-column>
            <el-table-column
              prop="out_user_id"
              align="center"
              :label="$t('ExternalCustomer')">
            </el-table-column>
            <el-table-column
              prop="merchant_id_name"
              align="center"
              :label="$t('Belonging')">
            </el-table-column>
            <el-table-column
              prop="place_post_code"
              align="center"
              :label="$t('ZipCode')">
            </el-table-column>
            <el-table-column
              prop="place_house_number"
              align="center"
              :label="$t('DoorNo')">
            </el-table-column>
            <el-table-column
              prop="execution_date"
              align="center"
              :label="$t('取派日期')">
            </el-table-column> -->
            <!-- <el-table-column
              prop="driver_name"
              align="center"
              :label="$t('DeliveryDriver')">
            </el-table-column>
            <el-table-column
              prop="line_name"
              align="center"
              :label="$t('线路名称')">
            </el-table-column>
            <el-table-column
              prop="batch_no"
              align="center"
              :label="$t('SiteNumber')">
              <template slot-scope="scope">
                <el-link :underline="false" @click="$router.push({name: 'stationList', params: {id: scope.row.batch_no}})">{{scope.row.batch_no}}</el-link>
              </template>
            </el-table-column>
            <el-table-column
              prop="tour_no"
              align="center"
              :label="$t('PickupLineNumber')">
              <template slot-scope="scope">
                <el-link :underline="false" @click="$router.push({name: 'lineTask', params: {id: scope.row.tour_no}})">{{scope.row.tour_no}}</el-link>
              </template>
            </el-table-column> -->
            <el-table-column
              prop="out_group_order_no"
              align="center"
              width="230"
              :label="$t('externalAppointmentBatchNo')"
            >
            </el-table-column>
            <el-table-column
              prop="replace_amount"
              align="center"
              width="200"
              :label="$t('CollectMoney') + '(' + currency_unit_symbol + ')'"
            >
            </el-table-column>
            <el-table-column
              prop="settlement_amount"
              align="center"
              width="220"
              :label="
                $t('EstimatedDeliveryFee') + '(' + currency_unit_symbol + ')'
              "
            >
            </el-table-column>
            <el-table-column
              prop="count_settlement_amount"
              align="center"
              width="200"
              :label="
                $t('ActualDeliveryFee') + '(' + currency_unit_symbol + ')'
              "
            >
            </el-table-column>
            <el-table-column
              prop="created_at"
              align="center"
              width="160"
              :label="$t('Created')"
            >
            </el-table-column>
          </el-table>
        </el-tab-pane>
        <base-pagination
          :pageParams="pageParams"
          @search="getOrder"
          class="pagination-size"
        ></base-pagination>
        <!-- </div> -->
      </el-tabs>

      <!-- 查看轨迹 -->
      <el-dialog
        :title="$t('LogisticStatus')"
        :visible.sync="show_trajectory"
        class="logistic"
        width="70%"
      >
        <div class="logistic_box">
          <div class="box-tit">
            <span>{{ $t("Sender") }}</span>
            <span>{{ $t("Receiver") }}</span>
          </div>
          <div>
            <span>{{ $t("Name") }}: {{ trailInfo.place_fullname }}</span>
            <span>{{ $t("Name") }}: {{ trailInfo.second_place_fullname }}</span>
          </div>
          <div>
            <span>{{ $t("Address") }}: {{ trailInfo.place_address }}</span>
            <span
              >{{ $t("Address") }}: {{ trailInfo.second_place_address }}</span
            >
          </div>
        </div>
        <el-divider></el-divider>
        <p class="box-tit">{{ $t("contentPickDeliveryResults") }}</p>
        <div class="logistic_table">
          <el-table
            max-height="600px"
            :data="trailInfo.package_list"
            border
            style="width: 100%"
          >
            <el-table-column
              type="index"
              header-align="center"
              align="center"
              width="80"
            >
              <template slot="header">
                <span>{{ $t("Number") }}</span>
              </template>
            </el-table-column>
            <el-table-column
              prop="name"
              align="center"
              :label="'*' + $t('PackageName')"
            >
            </el-table-column>
            <el-table-column
              prop="express_first_no"
              align="center"
              :label="'*' + $t('parcelNo')"
            >
            </el-table-column>
            <el-table-column
              prop="status_name"
              align="center"
              :label="'*' + $t('DispatchResult')"
            >
            </el-table-column>
          </el-table>
        </div>
        <el-divider></el-divider>
        <p class="box-tit">{{ $t("logisticsRecords") }}</p>
        <div>
          <el-timeline>
            <el-timeline-item
              v-for="(activity, index) in activities"
              :key="index"
              :icon="activity.icon"
              :type="activity.type"
              :color="activity.color"
              :size="activity.size"
              :timestamp="activity.timestamp"
            >
              {{ activity.content }}
            </el-timeline-item>
          </el-timeline>
        </div>
        <el-button type="text" size="small" @click="expandAll(!trailSign)"
          >{{ trailSign ? $t("Hide") : $t("Expand")
          }}<i
            style="margin-left: 5px; font-size: 15px"
            :class="[trailSign ? 'el-icon-arrow-up' : 'el-icon-arrow-down']"
          ></i>
        </el-button>
      </el-dialog>
      <!-- 查看轨迹 -->
      <!-- 第三方对接日志 -->
      <el-dialog
        :title="$t('ThirdPartyDockingLog')"
        :visible.sync="show_Operation"
        width="50%"
      >
        <el-table
          max-height="600px"
          element-loading-text="loading..."
          border
          :data="dockingLog"
        >
          <el-table-column
            :label="$t('Time')"
            prop="created_at"
            align="center"
          ></el-table-column>
          <el-table-column
            :label="$t('logContent')"
            prop="content"
            align="center"
          ></el-table-column>
        </el-table>
      </el-dialog>

      <!-- 查看记录 -->
      <el-dialog
        class="order-details"
        :title="$t('Details')"
        :visible.sync="show_order"
        width="60%"
      >
        <span class="details_tit">基础信息</span>
        <el-table max-height="600px" border :data="orderDetails.baseInfo">
          <el-table-column
            label="订单编号"
            prop="order_no"
            align="center"
          ></el-table-column>
          <el-table-column
            label="订单类型"
            prop="type_name"
            align="center"
          ></el-table-column>
          <el-table-column
            label="取件日期"
            prop="execution_date"
            align="center"
          ></el-table-column>
          <el-table-column
            label="派件日期"
            prop="second_execution_date"
            align="center"
          ></el-table-column>
          <el-table-column
            label="外部客户ID"
            prop="out_user_id"
            align="center"
          ></el-table-column>
          <el-table-column
            :label="$t('代收货款') + '(' + currency_unit_symbol + ')'"
            prop="replace_amount"
            align="center"
          ></el-table-column>
          <el-table-column
            label="结算方式"
            prop="settlement_type_name"
            align="center"
          ></el-table-column>
          <el-table-column
            label="送货上门"
            prop="delivery_name"
            align="center"
          ></el-table-column>
          <el-table-column
            label="掩码"
            prop="mask_code"
            align="center"
          ></el-table-column>
        </el-table>

        <span class="details_tit">取派地址信息</span>
        <el-table max-height="600px" border :data="orderDetails.addressInfo">
          <el-table-column
            label="联系人姓名"
            prop="fullname"
            align="center"
          ></el-table-column>
          <el-table-column
            label="地址类型"
            prop="type"
            align="center"
          ></el-table-column>
          <el-table-column
            label="联系电话"
            prop="phone"
            align="center"
          ></el-table-column>
          <el-table-column
            label="省/市"
            prop="city"
            align="center"
          ></el-table-column>
          <el-table-column
            label="街道"
            prop="street"
            align="center"
          ></el-table-column>
          <el-table-column
            label="门牌号"
            prop="house_number"
            align="center"
          ></el-table-column>
          <el-table-column
            label="邮编"
            prop="post_code"
            align="center"
          ></el-table-column>
          <el-table-column
            label="国家"
            prop="country_name"
            align="center"
          ></el-table-column>
          <el-table-column
            label="详细地址"
            prop="address"
            align="center"
          ></el-table-column>
        </el-table>

        <span class="details_tit">费用明细</span>
        <el-table max-height="600px" border :data="orderDetails.feeInfo">
          <el-table-column
            :label="$t('预计运费') + '(' + currency_unit_symbol + ')'"
            prop="settlement_amount"
            align="center"
          ></el-table-column>
          <el-table-column
            :label="$t('实际运费') + '(' + currency_unit_symbol + ')'"
            prop="count_settlement_amount"
            align="center"
          ></el-table-column>
          <el-table-column
            :label="$t('贴单费') + '(' + currency_unit_symbol + ')'"
            prop="sticker_amount"
            align="center"
          ></el-table-column>
          <el-table-column
            :label="$t('提货费') + '(' + currency_unit_symbol + ')'"
            prop="delivery_amount"
            align="center"
          ></el-table-column>
          <el-table-column label="运费明细" align="center">
            <template slot-scope="scope">
              <el-button @click="viewFees(scope.row.detalis)">查看</el-button>
            </template>
          </el-table-column>
        </el-table>

        <span class="details_tit">清单信息</span>
        <el-table
          max-height="600px"
          border
          :data="orderDetails.detailedList"
          :span-method="objectSpanMethod"
        >
          <el-table-column
            label="外部订单号"
            prop="out_order_no"
            align="center"
          ></el-table-column>
          <el-table-column
            label="性质"
            prop="nature"
            align="center"
          ></el-table-column>
          <el-table-column
            label="单号/代码"
            prop="code"
            align="center"
          ></el-table-column>
          <el-table-column
            label="数量"
            prop="qty"
            align="center"
          ></el-table-column>
          <el-table-column
            label="下单重量"
            prop="weight"
            align="center"
          ></el-table-column>
          <el-table-column
            label="实际重量"
            prop="actual_weight"
            align="center"
          ></el-table-column>
          <el-table-column
            label="特性"
            prop="features"
            align="center"
          ></el-table-column>
          <el-table-column
            label="最迟派送日期"
            prop="expiration_date"
            align="center"
          ></el-table-column>
        </el-table>

        <span class="details_tit">其他信息</span>
        <div>
          <span style="display: inline-block; min-width: 200px"
            >特别事项: {{ orderDetails.special_remark }}</span
          >
          <span style="display: inline-block; min-width: 200px"
            >其余备注: {{ orderDetails.remark }}</span
          >
        </div>
      </el-dialog>
      <!-- 查看记录 -->

      <!-- 费用查看 -->
      <el-dialog
        title="运费明细"
        :visible.sync="fee_show"
        @close="closeFeeDialog"
      >
        <div class="fee_info">
          <div>计算方式: {{ feeInfo.transport_price_type_name }}</div>
          <el-divider></el-divider>
          <div>
            订单费用:
            <div class="order_fee">
              <span
                >固定费用{{ `(${currency_unit_symbol})` }}:
                {{ feeInfo.starting_price }}</span
              >
              <!-- <span>特殊时段{{`(${currency_unit_symbol})`}}: {{ feeInfo.count_settlement_amount }}</span> -->
            </div>
          </div>
          <el-divider></el-divider>
          <div style="margin: 0 0 10px 0">包裹费用:</div>
          <el-table max-height="600px" border :data="feeInfo.list">
            <el-table-column
              label="包裹"
              prop="express_first_no"
              align="center"
            ></el-table-column>
            <el-table-column
              :label="$t('预计费用') + '(' + currency_unit_symbol + ')'"
              prop="count_settlement_amount"
              align="center"
            ></el-table-column>
            <el-table-column
              :label="$t('实际费用') + '(' + currency_unit_symbol + ')'"
              prop="settlement_amount"
              align="center"
            ></el-table-column>
          </el-table>
        </div>
      </el-dialog>
      <!-- 费用查看 -->

      <!-- 分配至已有站点 -->
      <el-dialog
        :title="$t('distribution')"
        :visible.sync="assignToBatch_show"
        @close="closeAssignToBatch"
        width="50%"
      >
        <el-form
          :model="assignToBatchInfo"
          :rules="rules"
          ref="assignToBatchForm"
          label-position="right"
          label-width="100px"
        >
          <el-form-item :label="$t('Date')" prop="execution_date">
            <el-date-picker
              size="small"
              v-model="assignToBatchInfo.execution_date"
              :picker-options="pickerOptions"
              @change="handlerDateChange(assignToBatchInfo.execution_date)"
              type="date"
              value-format="yyyy-MM-dd"
              :placeholder="$t('SelectDate')"
            >
            </el-date-picker>
          </el-form-item>
          <el-form-item
            :label="$t('AvailableSite')"
            prop="batch_no"
            v-if="batchPageListByOrderList.length"
          >
            <el-radio-group v-model="assignToBatchInfo.batch_no">
              <el-radio
                v-for="(item, index) in batchPageListByOrderList"
                :key="index"
                :value="item.tour_no"
                :label="item.batch_no"
              ></el-radio>
            </el-radio-group>
          </el-form-item>
          <el-form-item :label="$t('AvailableSite')" v-else>
            <span style="color: red">
              {{ $t("NoAvailableSite") }}
              {{ $t("CreateNewSiteAutomatically") }}
            </span>
          </el-form-item>
        </el-form>
        <div slot="footer">
          <el-button @click="assignToBatch_show = false">{{
            $t("Cancel")
          }}</el-button>
          <el-button type="primary" @click="assignToBatch">{{
            $t("Confirm")
          }}</el-button>
        </div>
      </el-dialog>
      <!-- 分配至已有站点 -->

      <!-- 恢复订单 -->
      <el-dialog
        :visible.sync="recoveryOrder_show"
        @close="closeRecoveryOrder"
        width="50%"
      >
        <el-form
          :model="recoveryOrderInfo"
          :rules="rules"
          ref="recoveryOrderForm"
          label-position="right"
          label-width="100px"
        >
          <el-form-item :label="$t('Date')" prop="execution_date">
            <el-date-picker
              size="small"
              v-model="recoveryOrderInfo.execution_date"
              :picker-options="pickerOptions"
              type="date"
              value-format="yyyy-MM-dd"
              :placeholder="$t('SelectDate')"
            >
            </el-date-picker>
          </el-form-item>
        </el-form>
        <div slot="footer">
          <el-button @click="recoveryOrder_show = false">{{
            $t("Cancel")
          }}</el-button>
          <el-button type="primary" @click="recoveryOrder">{{
            $t("Confirm")
          }}</el-button>
        </div>
      </el-dialog>
      <!-- 恢复订单 -->

      <!-- 线路加单 -->
      <el-dialog
        :title="$t('PleaseSelectTheRoute')"
        :visible.sync="visibleAddOrder"
      >
        <el-row :gutter="20" class="header-box">
          <el-col :span="12">
            <el-date-picker
              size="small"
              v-model="lineDataParams.execution_date"
              :picker-options="expireTimeOption"
              type="date"
              :clearable="false"
              value-format="yyyy-MM-dd"
              @change="handlerAvailableLines(lineDataParams)"
              :placeholder="$t('SelectDate')"
            >
            </el-date-picker>
          </el-col>
          <el-col :span="12">
            <el-input
              clearable
              @clear="handlerSearchLine()"
              v-model="lineDataParams.keyword"
              size="small"
              :placeholder="$t('PleaseEnterPickupDriver')"
            >
              <el-button
                slot="append"
                icon="el-icon-search"
                @click="handlerSearchLine()"
              ></el-button>
            </el-input>
          </el-col>
        </el-row>
        <el-table
          max-height="600px"
          element-loading-text="loading..."
          border
          :data="lineData"
          @row-click="handlerRowClick($event)"
        >
          <el-table-column :label="$t('select')" align="center">
            <template slot-scope="scope">
              <el-radio v-model="lineDataRadio" :label="scope.row.tour_no">
                <span class="el-radio__label"></span>
              </el-radio>
            </template>
          </el-table-column>
          <el-table-column
            :label="$t('lineNumber')"
            prop="tour_no"
            align="center"
          ></el-table-column>
          <el-table-column
            :label="$t('LineName')"
            prop="line_name"
            align="center"
          ></el-table-column>
          <el-table-column
            :label="$t('DisDate')"
            prop="execution_date"
            align="center"
          ></el-table-column>
          <el-table-column
            :label="$t('PickDriver')"
            prop="driver_name"
            align="center"
          ></el-table-column>
          <el-table-column
            :label="$t('DispatchStatusF')"
            prop="status_name"
            align="center"
          ></el-table-column>
        </el-table>
        <div class="pagination-box">
          <base-pagination
            :pageParams="lineDataParams"
            @search="handlerAvailableLines(lineDataParams)"
          >
          </base-pagination>
        </div>
        <div class="footer-btn-box">
          <div class="ctrol-btn">
            <el-button
              size="small"
              @click="handlerOrderCancel(visibleAddOrder)"
              >{{ $t("Cancel") }}</el-button
            >
            <el-button
              size="small"
              @click="onSubmit(lineDataRadio, orderData)"
              type="primary"
              >{{ $t("Submit") }}</el-button
            >
          </div>
        </div>
      </el-dialog>
      <!-- 线路加单 -->

      <!-- 继续派送 -->
      <el-dialog
        :visible.sync="again_show"
        :title="$t('Pleaseselectdistributiondate')"
        width="70%"
      >
        <p>{{ $t("WaybillType") }} :{{ againInfo.tracking_order_type_name }}</p>
        <el-row class="again">
          <el-col :span="12">{{ $t("Sender") }}</el-col>
          <el-col :span="12">{{ $t("Receiver") }}</el-col>
          <el-col :span="12"
            >{{ $t("Name") }}:{{ againInfo.place_fullname }}</el-col
          >
          <el-col :span="12"
            >{{ $t("Name") }}:{{ againInfo.second_place_fullname }}</el-col
          >
          <el-col :span="12"
            >{{ $t("Tel") }}:{{ againInfo.place_phone }}</el-col
          >
          <el-col :span="12"
            >{{ $t("Tel") }}:{{ againInfo.second_place_phone }}</el-col
          >
          <el-col :span="12"
            >{{ $t("Country") }}:{{ againInfo.place_country_name }}</el-col
          >
          <el-col :span="12"
            >{{ $t("Country") }}:{{
              againInfo.second_place_country_name
            }}</el-col
          >
          <el-col :span="12"
            >{{ $t("Address") }}:{{ againInfo.place_address }}</el-col
          >
          <el-col :span="12"
            >{{ $t("Address") }}:{{ againInfo.second_place_address }}</el-col
          >
          <el-col>
            <span class="again-date">{{
              $t("Pleaseselectdistributiondate")
            }}</span>
            <el-date-picker
              size="small"
              v-model="againInfo.execution_date"
              :picker-options="pickerOptions"
              type="date"
              value-format="yyyy-MM-dd"
              :placeholder="$t('SelectDate')"
            >
            </el-date-picker>
          </el-col>
        </el-row>
        <div slot="footer">
          <el-button @click="again_show = false">{{ $t("Cancel") }}</el-button>
          <el-button type="primary" @click="putAgain">{{
            $t("Confirm")
          }}</el-button>
        </div>
      </el-dialog>
      <!-- 继续派送 -->
    </div>
    <iframe frameborder="0" name="downExcel" style="display: none"></iframe>
  </div>
</template>

<script>
import BasePagination from "@/components/BasePagination/BasePagination";
import loading from "@/mixin/global_loading";
import websocket from "@/lib/websocket";

export default {
  name: "OrderList",
  components: {
    BasePagination,
  },
  mixins: [loading],
  data() {
    return {
      heightSearch: false,
      currency_unit_symbol: "", //单位
      lineDataRadio: "",
      lineData: [],
      visibleAddOrder: false,
      orderList: [],
      multipleSelection: [],
      tab: this.$t("All"),
      tableData: [],
      orderDetails: {},
      assignToBatchInfo: {
        id: null,
        execution_date: null,
        batch_no: null,
      },
      batchPageListByOrderInfo: {
        id: null,
        execution_date: null,
      },
      recoveryOrder_show: false,
      recoveryOrderInfo: {
        id: null,
        execution_date: null,
      },
      lineDataParams: {
        execution_date: "",
        notNeedInitQuery: true,
        order_id_list: "",
        keyword: "",
        total: 0,
        page: 1,
        per_page: 10,
      },
      pageParams: {
        order_no: this.$route.params.order_no || "",
        out_order_no: "",
        out_user_id: "",
        out_group_order_no: "",
        tracking_order_no: "",
        status: "",
        type: null,
        begin_date: "",
        end_date: "",
        merchant_id: "",
        line_name: "",
        exception_label: "",
        tour_no: this.$route.params.tour_no || null,
        batch_no: this.$route.params.batch_no || null,
        place_post_code: "",
        keyword: "",
        total: 0,
        page: 1,
        per_page: 20,
      },
      merchantsList: [], // 货主列表
      lineList: [], // 线路列表
      batchPageListByOrderList: [],
      orderNature: [
        {
          id: "1",
          name: "包裹",
        },
        {
          id: "2",
          name: "材料",
        },
        {
          id: "3",
          name: "文件",
        },
        {
          id: "4",
          name: "增值服务",
        },
        {
          id: "5",
          name: "其他",
        },
      ],
      settlementType: [
        {
          id: "1",
          name: "寄付",
        },
        {
          id: "2",
          name: "到付",
        },
      ],
      deliveryType: [
        {
          id: "1",
          name: "是",
        },
        {
          id: "2",
          name: "否",
        },
      ],
      show_trajectory: false,
      activities: [],
      trailInfo: {},
      trailSign: false,
      show_Operation: false,
      show_order: false,
      search_date: ["", ""],
      export_search_date: [],
      assignToBatch_show: false,
      tourDate: [], // 可分配星期
      // eslint-disable-next-line no-mixed-operators
      nowDate: Date.now() - 24 * 60 * 60 * 1000,
      expireTimeOption: {
        disabledDate(date) {
          /* eslint-disable */
          return date.getTime() < Date.now() - 24 * 60 * 60 * 1000;
        },
      },
      id_list: "", // 选择的表格项id

      // 模板id
      template_id: localStorage.getItem("address_template_id") || 1,

      //第三方对接日志
      dockingLog: [],

      popoverShow: false,
      waybillList: [],

      // 派送
      again_show: false,
      againInfo: {},

      //费用弹窗
      fee_show: false,
      feeInfo: {},

      rules: {
        execution_date: [
          { required: true, message: this.$t("SelectDate"), trigger: "blur" },
        ],
        batch_no: [
          { required: true, message: this.$t("SelectSite"), trigger: "blur" },
        ],
      },
    };
  },
  computed: {
    orderData() {
      return this.multipleSelection.map((item) => item.id).join(",");
    },
    addOrderData() {
      return this.multipleSelection.map((item) => {
        if ((item.status === 1 || item.status === 2) && item.type === 1) {
          return item.id;
        } else {
          return 0;
        }
      });
    },
    pickerOptions() {
      return {
        disabledDate: (date) => {
          if (date.getTime() >= this.nowDate) {
            let month =
              date.getMonth() === 9 ||
              date.getMonth() === 10 ||
              date.getMonth() === 11
                ? date.getMonth() + 1
                : "0" + (date.getMonth() + 1);
            let day =
              date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
            let item = `${date.getFullYear()}-${month}-${day}`;
            for (let i = 0; i < this.tourDate.length; i++) {
              if (item == this.tourDate[i]) {
                return false;
              }
            }
            return true;
          } else {
            return true;
          }
          const result = timeFormat(date);
          if (limit.includes(`${result}`)) {
            return false;
          }
          return true;
        },
      };
    },
    reset() {
      return this.$i18n.locale;
    },
    dictionary() {
      return this.$store.state.globalData.dictionary;
    },
    orderType() {
      return [
        {
          id: "1",
          name: this.$t("PickupOrder"),
        },
        {
          id: "2",
          name: this.$t("DeliveryOrder"),
        },
      ];
    },

    checkOrder() {
      return this.$store.state.globalData.checkedOrder;
    },
    // 单位

    unit() {
      return this.$store.state.globalData.unit;
    },
    pickerOptionsSearch() {
      return {
        shortcuts: [
          {
            text: this.$t("zuiJingYiZhou"),
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
              picker.$emit("pick", [start, end]);
            },
          },
          {
            text: this.$t("zuiJingYiYue"),
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
              picker.$emit("pick", [start, end]);
            },
          },
          {
            text: this.$t("LastThreeMonths"),
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
              picker.$emit("pick", [start, end]);
            },
          },
        ],
      };
    },
  },
  activated() {
    if (this.$route.params.order_no) {
      this.pageParams.order_no = this.$route.params.order_no;
    }
    this.getOrder();
  },
  created() {
    this.getDictionary();
    // this.getInitPieIndex();
    this.getOrder();
    this.getMerchants();
    this.getAdvanceSetting();
    // this.getLineList(); // 拉取线路
    // this.id_list = this.$store.state.globalData.checkedOrder; // 读取已选中的订单
  },
  mounted() {
    this.searchFormWidth();
    //  onresize  事件会在窗口或框架被调整大小时发生
    window.onresize = () => {
      if (!this.timer) {
        this.timer = true;
        let that = this;
        setTimeout(function () {
          that.searchFormWidth();
          that.timer = false;
        }, 400);
      }
    };
  },
  destroyed() {
    // 组件销毁后解绑事件
    window.onresize = null;
  },
  watch: {
    assignToBatch_show(oldVal) {
      this.assignToBatchInfo.batch_no = "";
      this.batchPageListByOrderList = [];
    },
    search_date: {
      // 日期筛选
      handler() {
        if (this.search_date) {
          this.pageParams.begin_date = this.search_date[0];
          this.pageParams.end_date = this.search_date[1];
        } else {
          this.pageParams.begin_date = "";
          this.pageParams.end_date = "";
        }
        this.pageParams.page = 1;
      },
    },
  },
  methods: {
    // 获取窗口的宽度,调整表格高度
    searchFormWidth() {
      let w = window.innerWidth;
      if (w <= 1845) {
        this.heightSearch = true;
      } else {
        this.heightSearch = false;
      }
    },
    getDictionary() {
      let status_list = [];
      this.$api.getDictionary().then((res) => {
        status_list = res.data.orderStatusList;
        for (let i = 0; i < status_list.length; i++) {
          const item = status_list[i];
          this.orderList.push({
            name: item.name,
            id: item.id,
            total: null,
          });
        }
        this.getInitPieIndex();
      });
    },
    // 获取单位值
    getAdvanceSetting() {
      this.$api.getAdvanceSetting().then((res) => {
        this.currency_unit_symbol = res.data.currency_unit_symbol; // 单价单位
      });
    },
    // 订单导入
    orderUpload() {
      this.$router.push({
        name: "orderUpload",
      });
    },
    getNowFormatDate() {
      // 获取当前日期
      var date = new Date();
      var seperator1 = "-";
      var year = date.getFullYear();
      var month = date.getMonth() + 1;
      var strDate = date.getDate();
      if (month >= 1 && month <= 9) {
        month = "0" + month;
      }
      if (strDate >= 0 && strDate <= 9) {
        strDate = "0" + strDate;
      }
      var currentdate = year + seperator1 + month + seperator1 + strDate;
      return currentdate;
    },
    // 线路加单功能--开始
    async handlerLineAdd(selectDate) {
      // 线路加单--打开弹窗
      if (selectDate.length === 0) {
        this.$message({
          type: "error",
          message: this.$t("pleaseSelectOrder"),
        });
        return;
      }
      if (!selectDate.every((item) => item > 0)) {
        this.$message({
          type: "error",
          message: this.$t("lineAdditionOrders"),
        });
        return;
      } // 必须都大于 0
      this.lineDataParams.execution_date = this.getNowFormatDate();
      const orderListIds = selectDate.join(",");
      this.visibleAddOrder = !this.visibleAddOrder;
      const res = await this.getAvailableLines(
        orderListIds,
        "",
        this.lineDataParams.execution_date
      );
      if (!res) return;
      this.lineData = res.data.data;
      this.lineDataParams.page = res.data.meta.current_page;
      this.lineDataParams.total = res.data.meta.total;
    },
    handlerOrderCancel(visibleValue) {
      //线路加单--关闭弹窗
      this.visibleAddOrder = !visibleValue;
      this.multipleSelection = [];
      this.lineDataRadio = "";
      this.lineDataParams.execution_date = "";
    },
    handlerAvailableLines(data) {
      //线路加单--拉取列表
      this.lineDataParams.order_id_list = this.addOrderData.join(",");

      this.$api.availableLines(data).then((res) => {
        if (!res) return;
        this.lineData = res.data.data;
        this.lineDataParams.page = res.data.meta.current_page;
        this.lineDataParams.total = res.data.meta.total;
      });
    },
    async handlerSearchLine() {
      // 线路加单--拉取刷选列表
      const data = this.addOrderData.join(",");
      const res = await this.getAvailableLines(
        data,
        this.lineDataParams.keyword,
        this.lineDataParams.execution_date
      );
      this.lineData = res.data.data;
      this.lineDataParams.page = res.data.meta.current_page;
      this.lineDataParams.total = res.data.meta.total;
    },
    handlerRowClick(e) {
      //线路加单--选择列表项
      this.lineDataRadio = e.tour_no;
    },
    async getAvailableLines(data, keyword = " ", execution_date) {
      // 线路加单--拉取列表
      return this.$api.availableLines({
        order_id_list: data,
        keyword,
        execution_date: execution_date,
        page: 1,
        per_page: 10,
      });
    },
    onSubmit(radioValue, selectDate) {
      // 线路加单--提交
      if (!selectDate) return;
      if (!radioValue) {
        this.$message({
          type: "error",
          message: this.$t("PleaseSelectARoute"),
        });
        return;
      }
      this.$api
        .multAddOrderToLines({
          id_list: selectDate,
          tour_no: radioValue,
        })
        .then((res) => {
          if (!res) return;
          this.visibleAddOrder = !this.visibleAddOrder;
          this.getOrder();
        });
    },
    // 线路加单功能--结束

    //站点移除功能--开始
    removeStation(selectDate) {
      // 站点移除
      if (!selectDate) {
        this.$message({
          type: "error",
          message: this.$t("pleaseSelectOrder"),
        });
        return;
      }
      this.$confirm(this.$t("whetherRemoveExistingSite") + "?").then(() => {
        this.$api
          .multDeleteStation({ params: { id_list: selectDate } })
          .then((res) => {
            if (!res) return;
            this.multipleSelection = [];
            this.updataOrderInfo();
          });
      });
    },
    // 站点移除功能--结束

    // 批量打印功能--开始
    checkItemBox(selection, row) {
      // 批量打印--选择列表项
      this.id_list = [];
      selection.forEach((el, index) => {
        this.id_list.push(el.id);
        // if (index === selection.length - 1) {
        //   this.id_list.push(el.id);
        // } else {
        //   this.id_list += el.id + ",";
        // }
      });
      // this.$store.commit('globalData/setCheckOrder', this.id_list); // 储存已选中的订单
    },
    checkAllBox(selection) {
      // 批量打印--全选
      this.id_list = [];
      selection.forEach((el, index) => {
        this.id_list.push(el.id);
        // if (index === selection.length - 1) {
        //   this.id_list += el.id;
        // } else {
        //   this.id_list += el.id + ",";
        // }
      });
      // this.$store.commit('globalData/setCheckOrder', this.id_list); // 储存已选中的订单
    },
    getOrderPrintAll(data) {
      // 批量打印--拉取面单数据
      if (!data) {
        this.$message({
          type: "error",
          message: this.$t("pleaseSelectOrder"),
        });
        return;
      }
      data.map((item) => {
        this.$api.printerOrder({ id_list: item }).then((res) => {
          if (!res) return;
          websocket(res.data);
        });
      });
    },
    // 批量打印功能--结束

    // 订单导出功能--开始
    getOrderExcel() {
      // 订单导出--下载excel
      this.$confirm(this.$t("AreSureWantExport"), this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      }).then(() => {
        this.$api.getOrderExcel(this.pageParams).then((res) => {
          if (!res) return;
          let iframe = window.frames["downExcel"];
          let href = res.data.path; //接口路径地址，返回数据类型为application/binary，后台控制显示信息，前端仅为下载功能
          iframe.location.href = href;
        });
      });
      // if (!this.export_search_date) {
      //   this.$message({
      //     type: "error",
      //     message: this.$t("PleaseChooseTime"),
      //   });
      //   return;
      // }
    },
    // 订单导出功能--结束

    // 批量删除
    batchDeletion(data) {
      if (!data.length) {
        this.$message({
          type: "error",
          message: this.$t("pleaseSelectOrder"),
        });
        return;
      }
      let result = data.every((el) => {
        return el.status === 1;
      });
      if (!result) {
        this.$message({
          type: "error",
          message: this.$t("SomeOrderStatusesDeleted"),
        });
        return;
      }

      let ids = data.map((el) => {
        return el.id;
      });
      this.$api.delMoreOrder(ids.join(",")).then((res) => {
        this.updataOrderInfo();
      });
    },

    // 分配至已有站点--开始
    handleSelectionChange(val) {
      // 勾选订单
      this.multipleSelection = val;
    },
    handlerDateChange(date) {
      // 分配至已有站点--日期change事件
      this.assignToBatchInfo.batch_no = "";
      if (!date) return;
      this.getBatchPageListByOrder(date);
    },
    openAssignToBatchDalog(id) {
      // 分配至已有站点--打开弹窗
      this.assignToBatch_show = true;
      this.batchPageListByOrderInfo.id = id;
      this.assignToBatchInfo.id = id;
      this.getTourDate(id);
    },
    closeAssignToBatch() {
      // 分配至已有站点--关闭弹窗
      this.$refs.assignToBatchForm.resetFields();
    },
    getTourDate(id) {
      // 分配至已有站点--获取可分配日期
      this.$api.getTourDate(id).then((res) => {
        this.tourDate = res.data;
      });
    },
    getBatchPageListByOrder(val) {
      // 分配至已有站点--获取可分配站点列表
      this.batchPageListByOrderInfo.execution_date = val;
      this.$api
        .getBatchPageListByOrder(this.batchPageListByOrderInfo)
        .then((res) => {
          if (res.data.length) {
            this.batchPageListByOrderList = res.data.data;
          } else {
            this.batchPageListByOrderList = [];
          }
        });
    },
    assignToBatch() {
      // 分配至已有站点--提交
      this.$refs.assignToBatchForm.validate((valid) => {
        if (!valid) return;
        this.$api.assignToBatch(this.assignToBatchInfo).then((res) => {
          if (res.code === 200) {
            this.$message({
              message: this.$t("AssignedSuccessfully"),
              type: "success",
            });
            this.updataOrderInfo();
            this.assignToBatch_show = false;
          }
        });
      });
    },
    // 分配至已有站点--开始

    //同步订单状态--开始
    synchronizeStatusList(ids) {
      if (!ids) {
        this.$message({
          type: "error",
          message: this.$t("pleaseSelectOrder"),
        });
        return;
      }
      this.$api.synchronizeStatusList({ id_list: ids }).then((res) => {});
    },
    //同步订单状态--结束

    //订单无效化--开始
    orderNeutralize(id) {
      this.$api.orderNeutralize(id).then((res) => {});
    },
    //订单无效化--结束

    // 订单初始化--开始
    getInitPieIndex() {
      // 订单类数据
      this.$api
        .getOrderCount({ type: this.pageParams.type || 0 })
        .then((res) => {
          this.orderList.forEach((val) => {
            val.total = res.data[val.id];
          });
        });
    },
    hangleRowClick(row, column, event) {
      // 点击列表行
    },
    clearWaybill(expandedRows, expanded) {
      // 子列表change状态
      if (expanded.length !== 0) {
        let index = this.tableData.findIndex((item) => {
          return item.id === expandedRows.id;
        });
        this.getTrackingOrderList(expandedRows.id, index);
      }
      // this.waybillList = [];
    },
    getOrder() {
      // 列表
      this.id_list = ""; // 订单打印缓存问题
      // if (this.pageParams.keyword) {
      //   this.pageParams.page = 1;
      // }
      this.pageParams.status === 0 ? (this.pageParams.status = null) : "";
      let obj = { exception_label: 2, ...this.pageParams };
      delete obj.status;
      this.$api
        .getOrder(this.pageParams.status === 10 ? obj : this.pageParams)
        .then((res) => {
          this.tableData = res.data.data.map((el) => {
            return { waybillList: [], ...el };
          });
          this.pageParams.total = res.data.meta.total;
        });
    },
    getTrackingOrderList(id, index) {
      // 运单查询
      this.$api.getTrackingOrderList(id).then((res) => {
        this.tableData[index].waybillList = res.data;
        // this.waybillList = res.data.data;
      });
    },
    handleChangePageParams() {
      this.pageParams.page = 1;
      this.getOrder();
    },
    updataOrderInfo() {
      // 更新订单信息
      this.getInitPieIndex();
      this.getOrder();
    },
    getMerchants() {
      // 货主列表
      this.$api.getMerchantsPublic().then((res) => {
        this.merchantsList = res.data.data;
      });
    },
    getLineList() {
      // 选择线路列表
      this.$api.getLineListOfOrderPublic({ per_page: 200 }).then((res) => {
        this.lineList = res.data;
      });
    },
    changeTab(tab) {
      // 切换tab
      this.orderList.forEach((val) => {
        if (val.name === tab.name) {
          this.pageParams.status = val.id;
          this.pageParams.page = 1;
          this.getOrder();
        }
      });
    },
    changeOrderType(val) {
      this.pageParams.page = 1;
      this.pageParams.type = +val.name == 0 ? null : val.name;
      this.getInitPieIndex();
      this.getOrder();
    },
    // 订单初始化--结束

    delOrder(id) {
      // 删除订单
      this.$confirm(this.$t("ConfirmToCancel") + "?", this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      })
        .then(() => {
          this.$api.delOrder(id).then((res) => {
            if (res.code === 200) {
              // this.$message({
              //   message: this.$t('success'),
              //   type: "success",
              // });
              this.updataOrderInfo();
            }
          });
        })
        .catch(() => {});
    },
    actualDestroyOrder(id) {
      // 彻底删除
      this.$confirm(this.$t("ConfirmToCancel"), this.$t("Notice"), {
        distinguishCancelAndClose: true,
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
      })
        .then(() => {
          this.$api.actualDestroyOrder(id).then((res) => {
            if (!res) return;
            this.updataOrderInfo();
          });
        })
        .catch(() => {});
    },
    // 恢复订单功能--开始
    openRecoveryOrder(id) {
      // 恢复订单功能--打开弹窗
      this.recoveryOrderInfo.id = id;
      this.recoveryOrder_show = true;
      this.getTourDate(id);
    },
    recoveryOrder() {
      // 恢复订单功能--恢复订单
      this.$refs.recoveryOrderForm.validate((valid) => {
        if (!valid) return;
        this.$api.recoveryOrder(this.recoveryOrderInfo).then((res) => {
          if (res.code === 200) {
            this.$message({
              message: this.$t("SuccessfulRecovery") + "!",
              type: "success",
            });
            this.updataOrderInfo();
            this.recoveryOrder_show = false;
          }
        });
      });
    },
    // 恢复订单功能--结束

    closeRecoveryOrder() {
      // 复位
      this.$refs.recoveryOrderForm.resetFields();
    },
    getOrderTrail(data) {
      // 查看轨迹
      this.activities = [];
      this.show_trajectory = true;
      this.$api.getOrderTrail(data).then((res) => {
        this.trailInfo = res.data;
        this.createTimeLine(this.trailInfo.order_trail_list);
        this.expandAll(false);
      });
    },
    createTimeLine(arr) {
      // 生成时间线
      this.activities = arr.map((el) => {
        return {
          content: el.content,
          timestamp: `${el.created_at}  ${el.operator_name}`,
          color: "#0bbd87",
        };
      });
      this.activities.reverse();
    },
    expandAll(status) {
      this.trailSign = status;
      if (status) {
        this.createTimeLine(this.trailInfo.order_trail_list);
      } else {
        let arr = this.trailInfo.order_trail_list.slice(
          this.trailInfo.order_trail_list.length - 3
        );
        this.createTimeLine(arr);
      }
    },

    // showOrderDetails(id) {
    //   // 查看记录
    //   this.show_order = true;
    //   this.$api.getOrderDetails(id).then((res) => {
    //     // this.orderDetails = res.data;
    //     // if (this.orderDetails.nature) {
    //     //   this.orderDetails.nature = this.orderNature[
    //     //     this.orderDetails.nature - 1
    //     //   ].name;
    //     // }
    //     // if (this.orderDetails.settlement_type) {
    //     //   this.orderDetails.settlement_type = this.settlementType[
    //     //     this.orderDetails.settlement_type - 1
    //     //   ].name;
    //     // }
    //     // if (this.orderDetails.delivery) {
    //     //   this.orderDetails.delivery = this.deliveryType[
    //     //     this.orderDetails.delivery - 1
    //     //   ].name;
    //     // }
    //     let data = {
    //       baseInfo: [],
    //       addressInfo: [],
    //       feeInfo: [],
    //       detailedList: [],
    //       special_remark: "",
    //       remark: "",
    //     };

    //     // 拼接基础信息
    //     data.baseInfo.push({
    //       order_no: res.data.order_no,
    //       type_name: res.data.type_name,
    //       execution_date: res.data.execution_date,
    //       second_execution_date: res.data.second_execution_date,
    //       out_user_id: res.data.out_user_id,
    //       replace_amount: res.data.replace_amount,
    //       settlement_type_name: res.data.settlement_type_name,
    //       delivery_name: res.data.delivery_name,
    //       mask_code: res.data.mask_code,
    //     });

    //     // 拼接地址信息
    //     data.addressInfo.push({
    //       fullname: res.data.place_fullname,
    //       type: "发货人",
    //       phone: res.data.place_phone,
    //       city: res.data.place_city,
    //       street: res.data.place_street,
    //       house_number: res.data.place_house_number,
    //       post_code: res.data.place_post_code,
    //       country_name: res.data.place_country_name,
    //       address: res.data.place_address,
    //     });
    //     if (res.data.second_place_fullname) {
    //       data.addressInfo.push({
    //         fullname: res.data.second_place_fullname,
    //         type: "收货人",
    //         phone: res.data.second_place_phone,
    //         city: res.data.second_place_city,
    //         street: res.data.second_place_street,
    //         house_number: res.data.second_place_house_number,
    //         post_code: res.data.second_place_post_code,
    //         country_name: res.data.second_place_country_name,
    //         address: res.data.second_place_address,
    //       });
    //     }

    //     //拼接费用明细
    //     data.feeInfo.push({
    //       count_settlement_amount: res.data.count_settlement_amount,
    //       settlement_amount: res.data.settlement_amount,
    //       sticker_amount: res.data.sticker_amount,
    //       delivery_amount: res.data.delivery_amount,
    //       detalis: {
    //         transport_price_type_name: res.data.transport_price_type_name,
    //         starting_price: res.data.starting_price,
    //         count_settlement_amount: res.data.count_settlement_amount,
    //         list: res.data.package_list.map((el) => {
    //           return {
    //             express_first_no: el.express_first_no,
    //             count_settlement_amount: el.count_settlement_amount,
    //             settlement_amount: el.settlement_amount,
    //           };
    //         }),
    //       },
    //     });

    //     // 拼接清单信息
    //     let packageList = res.data.package_list.map((el) => {
    //       return {
    //         out_order_no: res.data.out_order_no,
    //         nature: "包裹",
    //         code: el.express_first_no,
    //         qty: el.expect_quantity,
    //         weight: el.weight,
    //         actual_weight: el.actual_weight,
    //         features: el.feature_logo,
    //         expiration_date: el.expiration_date,
    //       };
    //     });
    //     let materialList = res.data.material_list.map((el) => {
    //       return {
    //         out_order_no: res.data.out_order_no,
    //         nature: "材料",
    //         code: el.code,
    //         qty: el.expect_quantity,
    //         weight: el.weight,
    //         actual_weight: el.actual_weight,
    //         features: "/",
    //       };
    //     });
    //     data.detailedList = data.detailedList.concat(packageList, materialList);

    //     // 其他信息
    //     data.special_remark = res.data.special_remark;
    //     data.remark = res.data.remark;

    //     this.orderDetails = data;
    //   });
    // },
    // 查看详情
    viewDetails(row) {
      this.$router.push({
        name: "viewDetails",
        query: { id: row.id, order_no: row.order_no },
      });
    },
    objectSpanMethod({ row, column, rowIndex, columnIndex }) {
      if (columnIndex == 0) {
        if (rowIndex == 0) {
          return {
            rowspan: this.orderDetails.detailedList.length,
            colspan: 1,
          };
        } else {
          return {
            rowspan: 0,
            colspan: 1,
          };
        }
      }
    },
    viewFees(val) {
      // 查看费用
      this.fee_show = true;
      this.feeInfo = val;
    },
    closeFeeDialog() {
      // 关闭费用弹窗
      this.feeInfo = {};
    },
    removeFromBatch(id) {
      // 从当前站点移除
      this.$api.removeFromBatch(id).then((res) => {
        if (res.code === 200) {
          this.$message({
            message: this.$t("RemovedSuccessfully") + "!",
            type: "success",
          });
          this.updataOrderInfo();
        }
      });
    },
    // 编辑订单
    toEditOrder(id) {
      /* eslint-disable */
      this.$router.push({
        name: "orderAdd",
        query: { id: id },
      });
    },

    // 第三方对接日志
    getThirdPartyLog(id) {
      this.show_Operation = true;
      this.$api.getThirdPartyLog(id).then((res) => {
        this.dockingLog = res.data;
      });
    },

    // 获取再次取派信息
    getAgainInfo(id) {
      this.againInfo = {};
      this.again_show = true;
      this.$api.getAgainInfo(id).then((res) => {
        if (res.code == 200) {
          this.againInfo = res.data;
          this.getDateList(res.data.tracking_order_id);
        }
        // 判断派送就互换值
        // if (res.data.type == 2) {
        //   this.againInfo = {
        //     ...res.data,
        //     second_place_fullname: res.data.place_fullname,
        //     second_place_phone: res.data.place_phone,
        //     second_place_country_name: res.data.place_country_name,
        //     second_place_address: res.data.place_address,
        //     place_fullname: res.data.second_place_fullname,
        //     place_phone: res.data.second_place_phone,
        //     place_country_name: res.data.second_place_country_name,
        //     place_address: res.data.second_place_address,
        //   };
        // } else {
        //   this.againInfo = res.data || {};
        // }
      });
    },

    // 取得日期
    getDateList(id) {
      this.$api.getDateList(id).then((res) => {
        this.tourDate = res.data;
      });
    },

    // 继续派送
    putAgain() {
      this.$api.putAgain(this.againInfo).then((res) => {
        if (res.code == 200) {
          this.again_show = false;
        }
      });
    },

    // 终止派送
    putAgainEnd(id) {
      this.$confirm(this.$t("whetherTerminateDelivery"), this.$t("Notice"), {
        distinguishCancelAndClose: true,
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
      })
        .then(() => {
          this.$api.putAgainEnd({ id: id }).then((res) => {});
        })
        .catch(() => {});
    },

    addNewOrder() {
      // 新增订单
      this.$router.push({
        path: "orderAdd",
      });
    },

    // 日期筛选
    searchAll() {
      this.pageParams.begin_date = "";
      this.pageParams.end_date = "";
      this.search_date = ["", ""];
    },

    searchToday() {
      this.search_date = ["", ""];
      const end = new Date();
      const start = new Date();
      this.pageParams.begin_date = start.format("yyyy-MM-dd");
      this.pageParams.end_date = start.format("yyyy-MM-dd");
      this.search_date = [start.format("yyyy-MM-dd"), end.format("yyyy-MM-dd")];
    },

    searchWeek() {
      const end = new Date();
      const start = new Date();
      const week = end.getDay() - 1;
      start.setTime(start.getTime() - 3600 * 1000 * 24 * week);
      this.pageParams.begin_date = start.format("yyyy-MM-dd");
      this.pageParams.end_date = end.format("yyyy-MM-dd");
      this.search_date = [start.format("yyyy-MM-dd"), end.format("yyyy-MM-dd")];
    },

    searchMonth() {
      const end = new Date();
      const start = new Date();
      const month = end.getDate() - 1;
      start.setTime(start.getTime() - 3600 * 1000 * 24 * month);
      this.pageParams.begin_date = start.format("yyyy-MM-dd");
      this.pageParams.end_date = end.format("yyyy-MM-dd");
      this.search_date = [start.format("yyyy-MM-dd"), end.format("yyyy-MM-dd")];
    },
  },
};
</script>

<style lang="less" scoped>
.order-list {
  .el-tabs--border-card {
    box-shadow: none;
    border-bottom-style: none;
    border: 1px solid rgba(228, 235, 241, 1);
  }
}
.dispatch-order {
  padding: 10px 25px 10px;
  background: #ffffff;
  border: 1px solid rgba(228, 235, 241, 1);
  border-top-style: none;
  position: relative;
  // top: -30px;
  margin-top: -30px;
  .conditional {
    padding: 0px 0 10px;
    .search {
      .el-input-group__append {
        .el-button .el-button--primary {
          background: #28d094;
          color: #ffffff;
        }
      }
    }
    .el-col {
      margin: 10px 0;
    }
  }
  .top_btn_grop {
    position: absolute;
    right: 0;
    z-index: 99;
    display: flex;
    justify-content: space-between;
    align-items: center;
    .el-checkbox {
      padding-right: 5px;
    }
  }
}
.logistic {
  .box-tit {
    font-weight: bold;
  }
  .logistic_box {
    div {
      display: flex;
      & > span {
        display: inline-block;
        width: 50%;
        margin: 5px 0;
      }
      &:not(:first-child) {
        padding-left: 20px;
      }
    }
  }
  .logistic_table {
    max-height: 250px;
    overflow: auto;
  }
}
</style>
<style lang="less">
.dispatch-order {
  .conditional {
    .search {
      .el-button {
        background: #28d094 !important;
        color: #ffffff !important;
        border-radius: 0;
      }
    }
    .el-input__inner {
      border-radius: 0;
    }
    .el-date-editor--daterange.el-input__inner {
      width: 100%;
      max-width: 350px;
    }
  }
  .order-details {
    .el-row {
      margin: 30px 0;
      .el-col {
        margin: 10px 0;
        span {
          font-weight: bold;
        }
      }
    }
    .dividing-line {
      border-top: 1px solid rgba(228, 235, 241, 1);
    }
  }
  .header-box {
    margin: 10px 0 10px 0;
  }
  .pagination-box {
    margin: 10px 0;
  }
  .footer-btn-box {
    margin-top: 10px;
    .ctrol-btn {
      margin: 0 0 0 auto;
      width: 126px;
    }
  }
}
.again {
  // .el-col {
  //   margin: 10px 0;
  // }
  .again-date {
    margin-right: 20px;
  }
}
.icon-warning {
  position: absolute;
  top: 12px;
  right: 10px;
}
</style>
<style lang="less">
.el-picker-panel {
  border-radius: 0;
}
.mult-control-btn {
  margin-right: 10px;
}
</style>
