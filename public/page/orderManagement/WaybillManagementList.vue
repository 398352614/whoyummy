<template>
  <!-- 运单列表 -->
  <div class="order-list">
    <!-- <div class="order-type">
      <el-button
        @click="changeOrderType(null)"
        :loading="this.$store.state.config.button_loading"
        :type="!pageParams.type ? 'danger' : null"
        >{{ $t("All") }}</el-button
      >
      <el-button
        @click="changeOrderType(2)"
        :loading="this.$store.state.config.button_loading"
        :type="pageParams.type == 2 ? 'danger' : null"
        >{{ $t("派件运单") }}</el-button
      >
      <el-button
        @click="changeOrderType(1)"
        :loading="this.$store.state.config.button_loading"
        :type="pageParams.type == 1 ? 'danger' : null"
        >{{ $t("取件运单") }}</el-button
      >
    </div> -->

    <el-tabs type="border-card" @tab-click="changeOrderType">
      <el-tab-pane :label="$t('All')" name="0"></el-tab-pane>
      <el-tab-pane :label="$t('deliveryWaybill')" name="2"></el-tab-pane>
      <el-tab-pane :label="$t('pickWaybill')" name="1"></el-tab-pane>
    </el-tabs>

    <div class="dispatch-order">
      <div class="top_btn_grop">
        <!-- 导出 -->
        <el-tooltip
          :content="$t('AdministratorActivate')"
          placement="top"
          :disabled="hasPerm('tracking-order.order-excel') ? true : false"
        >
          <div>
            <!-- <el-popover
              placement="bottom"
              trigger="click"
              v-model="popoverShow"
            >
              <div>
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
              </div>
            </el-popover> -->
            <!-- 判断是否有权限，改变样式 -->
            <el-button
              slot="reference"
              type="primary"
              size="small"
              :class="[
                hasPerm('tracking-order.order-excel')
                  ? 'common'
                  : 'common-disabled',
                'top-btn',
              ]"
              :disabled="!hasPerm('tracking-order.order-excel')"
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
          <el-button size="small" type="primary" class="common top-btn">
            {{ $t("bulkOperations") }}
            <i class="el-icon-arrow-down el-icon--right"></i>
          </el-button>
          <el-dropdown-menu slot="dropdown">
            <!-- 线路加单 -->
            <el-tooltip
              :content="$t('AdministratorActivate')"
              placement="top"
              :disabled="hasPerm('tracking-order.assign-tour') ? true : false"
            >
              <div
                :style="
                  hasPerm('tracking-order.assign-tour')
                    ? ''
                    : 'cursor: not-allowed'
                "
              >
                <el-dropdown-item
                  v-if="pageParams.type != 2"
                  @click.native="handlerLineAdd(addOrderData)"
                  :disabled="!hasPerm('tracking-order.assign-tour')"
                >
                  <el-button
                    class="operation"
                    size="small"
                    type="text"
                    :disabled="!hasPerm('tracking-order.assign-tour')"
                    >{{ $t("addOrderToRoute") }}</el-button
                  >
                </el-dropdown-item>
              </div>
            </el-tooltip>
            <!-- 站点移除 -->
            <el-tooltip
              :content="$t('AdministratorActivate')"
              placement="top"
              :disabled="hasPerm('tracking-order.remove-batch') ? true : false"
            >
              <div
                :style="
                  hasPerm('tracking-order.remove-batch')
                    ? ''
                    : 'cursor: not-allowed'
                "
              >
                <el-dropdown-item
                  :disabled="!hasPerm('tracking-order.remove-batch')"
                  @click.native="removeStation(orderData)"
                >
                  <el-button
                    class="operation"
                    :disabled="!hasPerm('tracking-order.remove-batch')"
                    size="small"
                    type="text"
                    >{{ $t("removedSite") }}</el-button
                  >
                </el-dropdown-item>
              </div>
            </el-tooltip>
            <!-- 允许出库 -->
            <el-tooltip
              :content="$t('AdministratorActivate')"
              placement="top"
              :disabled="hasPerm('tracking-order.out-status') ? true : false"
            >
              <div
                :style="
                  hasPerm('tracking-order.out-status')
                    ? ''
                    : 'cursor: not-allowed'
                "
              >
                <el-dropdown-item
                  :disabled="!hasPerm('tracking-order.out-status')"
                  @click.native="putTrackingOrderOutStatus(orderData, 1)"
                >
                  <el-button
                    class="operation"
                    :disabled="!hasPerm('tracking-order.out-status')"
                    size="small"
                    type="text"
                    >{{ $t("allowOutOfLibrary") }}
                  </el-button>
                </el-dropdown-item>
              </div>
            </el-tooltip>
            <!-- 禁止出库 -->
            <el-tooltip
              :content="$t('AdministratorActivate')"
              placement="top"
              :disabled="hasPerm('tracking-order.out-status') ? true : false"
            >
              <div
                :style="
                  hasPerm('tracking-order.out-status')
                    ? ''
                    : 'cursor: not-allowed'
                "
              >
                <el-dropdown-item
                  :disabled="!hasPerm('tracking-order.out-status')"
                  @click.native="putTrackingOrderOutStatus(orderData, 2)"
                >
                  <el-button
                    :disabled="!hasPerm('tracking-order.out-status')"
                    class="operation"
                    size="small"
                    type="text"
                    >{{ $t("prohibitDelivery") }}
                  </el-button>
                </el-dropdown-item>
              </div>
            </el-tooltip>
            <!-- <el-dropdown-item>
                        <el-button class="operation" size="small" type="text" @click="getOrderPrintAll(id_list)">{{$t('批量打印')}}</el-button>
                    </el-dropdown-item>
                    <el-dropdown-item>
                        <el-button class="operation" size="small" type="text" @click="getOrderExcel()">{{$t('订单导出')}}</el-button>
                    </el-dropdown-item>
                    <el-dropdown-item>
                        <el-button class="operation" size="small" type="text" @click="batchDeletion(multipleSelection)">{{$t('批量删除')}}</el-button>
                    </el-dropdown-item>
                    <el-dropdown-item>
                        <el-button class="operation" size="small" type="text" @click="synchronizeStatusList(id_list)">{{$t('同步订单状态')}}</el-button>
                    </el-dropdown-item> -->
          </el-dropdown-menu>
        </el-dropdown>
      </div>
      <el-tabs v-model="tab" @tab-click="changeTab">
        <div class="conditional">
          <!-- <el-col :xs="20" :sm="10" :lg="9">
              {{ $t("DistributeDate") }}:
              <el-button size="small" @click="searchAll">{{
                $t("All")
              }}</el-button>
              <el-button size="small" @click="searchToday">{{
                $t("today")
              }}</el-button>
              <el-button size="small" @click="searchWeek">{{
                $t("ThisWeek")
              }}</el-button>
              <el-button size="small" @click="searchMonth">{{
                $t("ThisMonth")
              }}</el-button>
            </el-col> -->
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
            style="margin-bottom: 10px"
          >
          </el-date-picker>
          <el-select
            size="small"
            clearable
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
          <el-select
            size="small"
            clearable
            v-model="pageParams.line_id"
            @keyup.enter.native="handleChangePageParams"
            :placeholder="$t('PleaseSelectTheRoute')"
            class="search-bar"
          >
            <el-option
              v-for="(item, index) in lineList"
              :key="index"
              :label="item.name"
              :value="item.id"
            ></el-option>
          </el-select>
          <!-- <el-col :span="10" class="search">
              <el-input size="small" clearable :placeholder="$t('请输入运单号/外部订单号/订单号/取件线路编号/邮编/站点编号/外部客户ID')" @keyup.enter.native="handleChangePageParams" @clear="handleChangePageParams" v-model="pageParams.keyword">
              </el-input>
            </el-col> -->

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
            :placeholder="$t('PleaseEnterZipCode')"
            @keyup.enter.native="handleChangePageParams"
            v-model="pageParams.post_code"
            class="search-bar"
          >
          </el-input>
          <el-input
            size="small"
            clearable
            :placeholder="$t('pleaseEnterSiteNumber')"
            @keyup.enter.native="handleChangePageParams"
            v-model="pageParams.batch_no"
            class="search-bar"
          >
          </el-input>
          <el-input
            size="small"
            clearable
            :placeholder="$t('PickupLineNumber')"
            @keyup.enter.native="handleChangePageParams"
            v-model="pageParams.tour_no"
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
          <!-- <el-row :gutter="10">
            <el-col :span="6">
              <el-input size="small" clearable :placeholder="$t('请输入站点编号')" @keyup.enter.native="handleChangePageParams" @clear="handleChangePageParams" v-model="pageParams.batch_no">
              </el-input>
            </el-col>
            <el-col :span="6">
              <el-input size="small" clearable :placeholder="$t('PleaseEnterZipCode')" @keyup.enter.native="handleChangePageParams" @clear="handleChangePageParams" v-model="pageParams.place_post_code">
              </el-input>
            </el-col>
            <el-col :span="6" class="search">
              <el-input size="small" clearable :placeholder="$t('请输入取件线路')" @keyup.enter.native="handleChangePageParams" @clear="handleChangePageParams" v-model="pageParams.tour_no">
                <el-button slot="append" size="small" @click="handleChangePageParams" :loading="this.$store.state.config.button_loading" type="primary" >{{$t('Search')}}</el-button>
              </el-input>
            </el-col>

          </el-row> -->
        </div>
        <el-tab-pane
          v-for="item in orderList"
          :label="`${item.name} ${item.total || 0}`"
          :name="item.id + ''"
          :key="item.id"
        >
          <el-table
            height="calc(100vh - 359px)"
            :data="hasPerm('tracking-order.index') ? tableData : []"
            :ref="`orderTable${item.id}`"
            v-loading="global_loading()"
            @selection-change="handleSelectionChange"
            @select="checkItemBox"
            @select-all="checkAllBox"
            element-loading-text="loading..."
            border
            style="width: 100%"
            :cell-style="{ padding: '5px 0px' }"
          >
            <template slot="empty" v-if="!hasPerm('tracking-order.index')">
              <div style="font-size: 18px">
                {{ $t("AdministratorActivate") }}
              </div>
            </template>
            <el-table-column align="center" type="selection" width="80">
            </el-table-column>
            <el-table-column
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
                  <el-dropdown-menu slot="dropdown">
                    <!-- 查看记录 -->
                    <el-tooltip
                      :content="$t('AdministratorActivate')"
                      placement="top"
                      :disabled="hasPerm('tracking-order.index') ? true : false"
                    >
                      <div
                        :style="
                          hasPerm('tracking-order.index')
                            ? ''
                            : 'cursor: not-allowed'
                        "
                      >
                        <el-dropdown-item
                          @click.native="showOrderDetails(scope.row.id)"
                          :disabled="!hasPerm('tracking-order.index')"
                        >
                          <el-button
                            :disabled="!hasPerm('tracking-order.index')"
                            class="operation"
                            size="small"
                            type="text"
                            >{{ $t("Details") }}</el-button
                          >
                        </el-dropdown-item>
                      </div>
                    </el-tooltip>
                    <!-- 查看轨迹 -->
                    <el-tooltip
                      :content="$t('AdministratorActivate')"
                      placement="top"
                      :disabled="hasPerm('tracking-order.trail') ? true : false"
                    >
                      <div
                        :style="
                          hasPerm('tracking-order.trail')
                            ? ''
                            : 'cursor: not-allowed'
                        "
                      >
                        <el-dropdown-item
                          :disabled="!hasPerm('tracking-order.trail')"
                          @click.native="
                            getOrderTrail(scope.row.tracking_order_no)
                          "
                        >
                          <el-button
                            :disabled="!hasPerm('tracking-order.trail')"
                            class="operation"
                            size="small"
                            type="text"
                            >{{ $t("Track") }}</el-button
                          >
                        </el-dropdown-item>
                      </div>
                    </el-tooltip>
                    <!-- <el-dropdown-item v-if="hasPerm('tracking-order.third-party-log')" @click.native="getThirdPartyLog(scope.row.id)">
                              <el-button class="operation" size="small" type="text">{{$t('操作日志')}}</el-button>
                          </el-dropdown-item> -->
                    <!-- 分配至已有站点  -->
                    <el-tooltip
                      :content="$t('AdministratorActivate')"
                      placement="top"
                      :disabled="
                        hasPerm('tracking-order.assign-batch') ? true : false
                      "
                    >
                      <div
                        :style="
                          hasPerm('tracking-order.assign-batch')
                            ? ''
                            : 'cursor: not-allowed'
                        "
                      >
                        <el-dropdown-item
                          @click.native="openAssignToBatchDalog(scope.row.id)"
                          v-if="scope.row.status === 1"
                          :disabled="!hasPerm('tracking-order.assign-batch')"
                        >
                          <el-button
                            :disabled="!hasPerm('tracking-order.assign-batch')"
                            class="operation"
                            size="small"
                            type="text"
                            >{{ $t("AssignToExistingSite") }}</el-button
                          >
                        </el-dropdown-item>
                      </div>
                    </el-tooltip>
                    <!-- 从当前站点移除 -->
                    <el-tooltip
                      :content="$t('AdministratorActivate')"
                      placement="top"
                      :disabled="
                        hasPerm('tracking-order.remove-batch') ? true : false
                      "
                    >
                      <div
                        :style="
                          hasPerm('tracking-order.remove-batch')
                            ? ''
                            : 'cursor: not-allowed'
                        "
                      >
                        <el-dropdown-item
                          @click.native="removeFromBatch(scope.row.id)"
                          v-if="
                            scope.row.status === 1 || scope.row.status === 2
                          "
                          :disabled="!hasPerm('tracking-order.remove-batch')"
                        >
                          <el-button
                            :disabled="!hasPerm('tracking-order.remove-batch')"
                            class="operation"
                            size="small"
                            type="text"
                            >{{ $t("RemoveFromCurrentSite") }}</el-button
                          >
                        </el-dropdown-item>
                      </div>
                    </el-tooltip>
                    <!-- <el-dropdown-item @click.native="actualDestroyOrder(scope.row.id)" v-if="scope.row.status === 7">
                              <el-button class="operation" size="small" type="text">{{$t('DeletePermanently')}}</el-button>
                          </el-dropdown-item> -->
                    <!-- <el-dropdown-item @click.native="orderNeutralize(scope.row.id)" v-if="scope.row.status === 5">
                              <el-button class="operation" size="small" type="text">{{$t('订单无效化')}}</el-button>
                          </el-dropdown-item> -->
                  </el-dropdown-menu>
                </el-dropdown>
                <!-- <el-dropdown size="mini" trigger="hover" placement="bottom">
                      <el-button size="mini" type="primary">
                          {{$t('Operation')}}
                      </el-button>
                      <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item @click.native="showOrderDetails(scope.row.id)">
                              <el-button class="operation" size="small" type="text">{{$t('Details')}}</el-button>
                          </el-dropdown-item>
                          <el-dropdown-item @click.native="openRecoveryOrder(scope.row.id)">
                              <el-button class="operation" size="small" type="text">{{$t('Recover')}}</el-button>
                          </el-dropdown-item>
                          <el-dropdown-item @click.native="delOrder(scope.row.id)">
                              <el-button class="operation" size="small" type="text">{{$t('DeleteOrder')}}</el-button>
                          </el-dropdown-item>
                          <el-dropdown-item @click.native="toEditOrder(scope.row.id)">
                              <el-button class="operation" size="small" type="text">{{$t('EditOrder')}}</el-button>
                          </el-dropdown-item>
                          <el-dropdown-item v-if="(scope.row.status == 4 || scope.row.status == 5 || scope.row.status == 6) && scope.row.exception_label == 1" @click.native="synchronizeStatusList(scope.row.id)">
                              <el-button class="operation" size="small" type="text">{{$t('同步订单状态')}}</el-button>
                          </el-dropdown-item>
                      </el-dropdown-menu>
                  </el-dropdown> -->
              </template>
            </el-table-column>
            <el-table-column
              prop="tracking_order_no"
              align="center"
              width="150"
              :label="$t('waybillNo')"
            >
            </el-table-column>
            <el-table-column
              prop="merchant_id_name"
              align="center"
              :label="$t('Belonging')"
              width="150"
            >
            </el-table-column>
            <el-table-column
              prop="execution_date"
              align="center"
              width="150"
              :label="$t('DistributeDate')"
            >
            </el-table-column>
            <el-table-column
              prop="status_name"
              align="center"
              :label="$t('Status')"
              width="150"
            >
              <template slot-scope="scope">
                <span v-if="scope.row.status == 7" style="color: #efb336">{{
                  scope.row.status_name
                }}</span>
                <span v-else>{{ scope.row.status_name }}</span>
              </template>
            </el-table-column>
            <el-table-column
              prop="out_status_name"
              align="center"
              :label="$t('warehouseOut')"
              width="160"
            >
            </el-table-column>
            <el-table-column
              prop="out_user_id"
              align="center"
              width="160"
              :label="$t('customerNumber')"
            >
            </el-table-column>
            <el-table-column
              prop="order_no"
              align="center"
              width="130"
              :label="$t('OrderNumber')"
            >
              <template slot-scope="scope">
                <div>
                  {{ scope.row.order_no }}
                </div>
                <span
                  v-if="scope.row.exception_label !== 1"
                  class="el-icon-warning"
                  style="color: #fea100"
                ></span>
              </template>
            </el-table-column>
            <el-table-column
              prop="out_order_no"
              align="center"
              width="150"
              :label="$t('ExternalOrderNumber')"
            >
            </el-table-column>
            <el-table-column
              prop="type_name"
              align="center"
              :label="$t('WaybillType')"
              width="150"
            >
            </el-table-column>
            <el-table-column
              prop="place_post_code"
              align="center"
              :label="$t('ZipCode')"
              width="150"
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
              prop="driver_name"
              align="center"
              :label="$t('DeliveryDriver')"
              width="160"
            >
            </el-table-column>
            <el-table-column
              prop="batch_no"
              align="center"
              :label="$t('SiteNumber')"
              width="160"
            >
              <template slot-scope="scope">
                <el-link
                  class="link"
                  :underline="false"
                  @click="
                    $router.push({
                      name: 'stationList',
                      params: { id: scope.row.batch_no },
                    })
                  "
                  >{{ scope.row.batch_no }}</el-link
                >
              </template>
            </el-table-column>
            <el-table-column
              prop="tour_no"
              align="center"
              width="160"
              :label="$t('PickupLineNumber')"
            >
              <template slot-scope="scope">
                <el-link
                  class="link"
                  :underline="false"
                  @click="
                    $router.push({
                      name: 'lineTask',
                      params: { id: scope.row.tour_no },
                    })
                  "
                  >{{ scope.row.tour_no }}</el-link
                >
              </template>
            </el-table-column>
            <el-table-column
              prop="line_name"
              align="center"
              width="110"
              :label="$t('lineName')"
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
          <base-pagination
            :pageParams="pageParams"
            @search="getOrder"
            class="pagination-size"
          ></base-pagination>
        </el-tab-pane>
      </el-tabs>

      <!-- 查看轨迹 -->
      <el-dialog
        :title="$t('LogisticStatus')"
        :visible.sync="show_trajectory"
        width="50%"
      >
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
      </el-dialog>
      <!-- 查看轨迹 -->

      <el-dialog
        :title="$t('操作日志')"
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
            label="时间"
            prop="created_at"
            align="center"
          ></el-table-column>
          <el-table-column
            label="操作内容"
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
        <el-row>
          <el-col :span="12">
            {{ $t("OrderNumber") }}: {{ orderDetails.order_no }}
          </el-col>
          <el-col :span="12">
            {{ $t("waybillNo") }}: {{ orderDetails.tracking_order_no }}
          </el-col>
          <el-col :span="12">
            {{ $t("PickupDeliveryDate") }}:
            {{ orderDetails.execution_date }}
          </el-col>
          <el-col :span="12">
            {{ $t("ExternalOrderNumber") }}:
            {{ orderDetails.out_order_no }}
          </el-col>
          <el-col :span="12">
            {{ $t("WaybillType") }}: {{ orderDetails.type_name }}
          </el-col>
          <el-col :span="12">
            {{ $t("ExternalCustomer") }}:
            {{ orderDetails.out_user_id }}
          </el-col>
          <!-- <el-col :span="12">
              {{$t('OrderType2')}}: {{orderDetails.nature}}
            </el-col> -->
          <el-col :span="12">
            {{ $t("PaymentMethod") }}:
            {{ orderDetails.settlement_type }}
          </el-col>
          <el-col :span="12">
            {{ $t("CollectMoney") }}:
            {{ orderDetails.replace_amount }}
          </el-col>
          <el-col :span="12">
            {{ $t("FreightAmount") }}:
            {{ orderDetails.settlement_amount }}
          </el-col>
          <el-col :span="12">
            {{ $t("DoorToDoor") }}:
            {{ orderDetails.delivery }}
          </el-col>
          <el-col :span="12" v-if="orderDetails.type == 2">
            {{ $t("maskCode") }}: {{ orderDetails.mask_code }}
          </el-col>
        </el-row>
        <el-row class="dividing-line">
          <el-col :span="12">
            <span>{{
              orderDetails.type == 2
                ? $t("Sender")
                : $t("Receiver")
            }}</span>
          </el-col>
          <el-col :span="12">
            <span>{{
              orderDetails.type == 2
                ? $t("Receiver")
                : $t("Sender")
            }}</span>
          </el-col>
          <el-col :span="12">
            {{ $t("name") }}:
            {{ orderDetails.sender_fullname }}
          </el-col>
          <el-col :span="12">
            {{ $t("name") }}:
            {{ orderDetails.place_fullname }}
          </el-col>
          <el-col :span="12">
            {{ $t("Tel") }}: {{ orderDetails.sender_phone }}
          </el-col>
          <el-col :span="12">
            {{ $t("Tel") }}: {{ orderDetails.place_phone }}
          </el-col>
          <el-col :span="12">
            {{ $t("Country") }}:
            {{ orderDetails.sender_country_name }}
          </el-col>
          <el-col :span="12">
            {{ $t("Country") }}:
            {{ orderDetails.place_country_name }}
          </el-col>

          <div v-if="template_id == 1">
            <el-col :span="12">
              {{ $t("ZipCode") }}/{{
                $t("DoorNo")
              }}:
              {{
                orderDetails.sender_post_code
                  ? orderDetails.sender_post_code +
                    "/" +
                    orderDetails.sender_house_number
                  : ""
              }}
            </el-col>
            <el-col :span="12">
              {{ $t("ZipCode") }}/{{
                $t("DoorNo")
              }}:
              {{
                orderDetails.place_post_code +
                "/" +
                orderDetails.place_house_number
              }}
            </el-col>
            <el-col :span="12">
              {{ $t("City") }}:
              {{ orderDetails.sender_city }}
            </el-col>
            <el-col :span="12">
              {{ $t("City") }}: {{ orderDetails.place_city }}
            </el-col>
            <el-col :span="12">
              {{ $t("Street") }}:
              {{ orderDetails.sender_street }}
            </el-col>
            <el-col :span="12">
              {{ $t("Street") }}:
              {{ orderDetails.place_street }}
            </el-col>
          </div>
          <div v-else>
            <el-col :span="12">
              {{ $t("Address") }}: {{ orderDetails.sender_address }}
            </el-col>
            <el-col :span="12">
              {{ $t("Address") }}: {{ orderDetails.place_address }}
            </el-col>
          </div>
        </el-row>
        <el-row class="dividing-line">
          <el-col :span="12">
            <span>{{ $t("ListInformation") }}</span>
          </el-col>
          <el-col>
            <el-tabs>
              <el-tab-pane :label="$t('Package')">
                <el-table
                  max-height="600px"
                  :data="orderDetails.package_list"
                  border
                  style="width: 100%; margin: 0 auto"
                >
                  <el-table-column type="index" align="center" width="50">
                    <template slot="header">
                      <span>{{ $t("Number") }}</span>
                    </template>
                  </el-table-column>
                  <el-table-column
                    prop="name"
                    align="center"
                    :label="$t('PackageName')"
                    width="140"
                  >
                  </el-table-column>
                  <el-table-column
                    prop="express_first_no"
                    align="center"
                    :label="$t('CourierNumber1')"
                    width="180"
                  >
                  </el-table-column>
                  <el-table-column
                    prop="express_second_no"
                    align="center"
                    :label="$t('CourierNumber2')"
                    width="180"
                  >
                  </el-table-column>
                  <el-table-column
                    prop="out_order_no"
                    align="center"
                    :label="$t('ExternalIdentification')"
                    width="160"
                  >
                  </el-table-column>
                  <el-table-column
                    v-if="orderDetails.type == 2"
                    prop="feature_logo"
                    align="center"
                    :label="$t('nature')"
                  >
                  </el-table-column>
                  <el-table-column
                    prop="weight"
                    align="center"
                    :label="$t('Weight')"
                  >
                  </el-table-column>
                  <el-table-column
                    prop="expect_quantity"
                    align="center"
                    :label="$t('Qty')"
                  >
                  </el-table-column>
                  <el-table-column
                    prop="remark"
                    align="center"
                    :label="$t('Remark')"
                  >
                  </el-table-column>
                  <el-table-column
                    prop="expiration_date"
                    align="center"
                    :label="$t('ExceededTheLatest')"
                    width="250"
                  >
                  </el-table-column>
                </el-table>
              </el-tab-pane>
              <el-tab-pane :label="$t('MaterialScience')">
                <el-table
                  max-height="600px"
                  :data="orderDetails.material_list"
                  border
                  style="width: 100%; margin: 0 auto"
                >
                  <el-table-column type="index" align="center" width="50">
                    <template slot="header">
                      <span>{{ $t("Number") }}</span>
                    </template>
                  </el-table-column>
                  <el-table-column
                    prop="name"
                    align="center"
                    :label="$t('MaterialName')"
                  >
                  </el-table-column>
                  <el-table-column
                    prop="code"
                    align="center"
                    :label="$t('MaterialCode')"
                  >
                  </el-table-column>
                  <el-table-column
                    prop="out_order_no"
                    align="center"
                    :label="$t('ExternalIdentification')"
                    width="250"
                  >
                  </el-table-column>
                  <el-table-column
                    prop="expect_quantity"
                    align="center"
                    :label="$t('Qty')"
                  >
                  </el-table-column>
                  <el-table-column
                    prop="remark"
                    align="center"
                    :label="$t('Remark')"
                  >
                  </el-table-column>
                </el-table>
              </el-tab-pane>
            </el-tabs>
          </el-col>
        </el-row>
        <el-row class="dividing-line">
          <el-col>
            <span>{{ $t("Optional") }}</span>
          </el-col>
          <el-col :span="12">
            {{ $t("SpecialReminder") }}:
            {{ orderDetails.special_remark }}
          </el-col>
          <el-col :span="12">
            {{ $t("Remark") }}: {{ orderDetails.remark }}
          </el-col>
        </el-row>
      </el-dialog>
      <!-- 查看记录 -->

      <!-- 分配至已有站点 -->
      <el-dialog
        :title="$t('AssignToExistingSite')"
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
          <el-button @click="assignToBatch_show = false" class="cancel">{{
            $t("Cancel")
          }}</el-button>
          <el-button type="primary" @click="assignToBatch" class="submit">{{
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
          <el-button @click="recoveryOrder_show = false" class="cancel">{{
            $t("Cancel")
          }}</el-button>
          <el-button type="primary" @click="recoveryOrder" class="submit">{{
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
            :label="$t('DistributeDate')"
            prop="execution_date"
            align="center"
          ></el-table-column>
          <el-table-column
            :label="$t('PickDriver')"
            prop="driver_name"
            align="center"
          ></el-table-column>
          <el-table-column
            :label="$t('DispatchStatus')"
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
            <!-- 取消 -->
            <el-button
              size="small"
              @click="handlerOrderCancel(visibleAddOrder)"
              class="cancel"
              >{{ $t("Cancel") }}</el-button
            >
            <!-- 确定 -->
            <el-button
              size="small"
              @click="onSubmit(lineDataRadio, orderData)"
              type="primary"
              class="submit"
              >{{ $t("Confirm") }}</el-button
            >
          </div>
        </div>
      </el-dialog>
      <!-- 线路加单 -->
    </div>
    <iframe frameborder="0" name="downExcel" style="display: none"></iframe>
  </div>
</template>

<script>
import BasePagination from "@/components/BasePagination/BasePagination";
import loading from "@/mixin/global_loading";
import LoginVue from "../login/Login.vue";

export default {
  name: "WaybillManagement",
  components: {
    BasePagination,
  },
  mixins: [loading],
  data() {
    return {
      lineDataRadio: "",
      lineData: [],
      visibleAddOrder: false,
      orderList: [],
      multipleSelection: [],
      tab: "0",
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
        tracking_order_id_list: "",
        keyword: "",
        total: 0,
        page: 1,
        per_page: 10,
      },
      pageParams: {
        tracking_order_no: this.$route.params.tracking_order_no || "",
        order_no: "",
        out_order_no: "",
        out_user_id: "",
        post_code: "",
        status: "",
        type: null,
        group_id: "",
        is_lock: "",
        begin_date: "",
        end_date: "",
        merchant_id: "",
        line_id: "",
        tour_no: this.$route.params.tour_no || null,
        batch_no: this.$route.params.batch_no || null,
        place_post_code: "",
        keyword: null,
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
      show_Operation: false,
      show_order: false,
      search_date: ["", ""],
      export_search_date: ["", ""],
      assignToBatch_show: false,
      tourDate: [], // 可分配星期
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
    if (
      this.$route.params.tracking_order_no ||
      this.$route.params.batch_no ||
      this.$route.params.tour_no
    ) {
      this.pageParams.tracking_order_no = this.$route.params.tracking_order_no;
      this.pageParams.batch_no = this.$route.params.batch_no;
      this.pageParams.tour_no = this.$route.params.tour_no;

      this.getOrder();
    }
  },
  created() {
    // const status_list =
    //   this.$store.state.globalData.dictionary.trackingOrderStatusList || [];
    // for (let i = 0; i < status_list.length; i++) {
    //   const item = status_list[i];
    //   this.orderList.push({
    //     name: item.name,
    //     id: item.id,
    //     total: null,
    //   });
    // }
    this.getDictionary();
    // this.getInitPieIndex();
    this.getOrder();
    this.getMerchants();
    this.getLineList(); // 拉取线路
    // this.id_list = this.$store.state.globalData.checkedOrder; // 读取已选中的订单
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
    // reset: {
    //   handler() {
    //     this.orderList = [
    //       { name: this.$t("All"), id: 0, code: "all_count", num: 0 },
    //       { name: this.$t("Unassigned"), id: 1, code: "no_take", num: 0 },
    //       { name: this.$t("Assigned"), id: 2, code: "assign", num: 0 },
    //       {
    //         name: this.$t("WaitingForDelivery"),
    //         id: 3,
    //         code: "wait_out",
    //         num: 0,
    //       },
    //       { name: this.$t("Distributing"), id: 4, code: "taking", num: 0 },
    //       { name: this.$t("Delivered"), id: 5, code: "singed", num: 0 },
    //       {
    //         name: this.$t("DistributionFailed"),
    //         id: 6,
    //         code: "cancel_count",
    //         num: 0,
    //       },
    //       {
    //         name: this.$t("AbnormalOrders"),
    //         id: 10,
    //         code: "exception_count",
    //         num: 0,
    //       },
    //       { name: this.$t("RecycleBin"), id: 7, code: "", num: 0 },
    //     ];
    //     for (let i = 0; i < this.orderList.length; i++) {
    //       if (+this.orderList[i].id == +this.pageParams.status) {
    //         this.tab = this.orderList[i].name;
    //       }
    //     }
    //     this.getInitPieIndex();
    //   },
    // },
    // dictionary: {
    //   handler(val) {
    //     const status_list = val || [];
    //     for (let i = 0; i < status_list.length; i++) {
    //       const item = status_list[i];
    //       this.orderList.push({
    //         name: item.name,
    //         id: item.id,
    //         total: null,
    //       });
    //     }
    //   },
    // },
  },
  methods: {
    getDictionary() {
      let status_list = [];
      this.$api.getDictionary().then((res) => {
        status_list = res.data.trackingOrderStatusList;
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
      // this.multipleSelection = [];
      this.lineDataRadio = "";
      this.lineDataParams.execution_date = "";
    },
    handlerAvailableLines(data) {
      //线路加单--拉取列表
      this.lineDataParams.tracking_order_id_list = this.addOrderData.join(",");

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
        tracking_order_id_list: data,
        keyword,
        execution_date: execution_date,
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
          tracking_order_id_list: selectDate,
          tour_no: radioValue,
        })
        .then((res) => {
          if (res.code == 200) {
            this.visibleAddOrder = !this.visibleAddOrder;
            this.getOrder();
          }
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
          .delTrackingOrderRemoveBatch({ params: { id_list: selectDate } })
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
      this.id_list = "";
      selection.forEach((el, index) => {
        if (index === selection.length - 1) {
          this.id_list += el.id;
        } else {
          this.id_list += el.id + ",";
        }
      });
      // this.$store.commit('globalData/setCheckOrder', this.id_list); // 储存已选中的订单
    },
    checkAllBox(selection) {
      // 批量打印--全选
      this.id_list = "";
      selection.forEach((el, index) => {
        if (index === selection.length - 1) {
          this.id_list += el.id;
        } else {
          this.id_list += el.id + ",";
        }
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
      this.$api.getOrderPrintAll({ id_list: data }).then((res) => {
        if (!res) return;
        window.open(res.data);
      });
    },
    // 批量打印功能--结束

    // 订单导出功能--开始
    getOrderExcel() {
      // 运单导出--下载excel
      this.$confirm(this.$t("AreSureWantExport"), this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      }).then(() => {
        this.$api.getTrackingOrderOrderExcel(this.pageParams).then((res) => {
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
      console.log(ids, ids.join(","));
      this.$api.delMoreOrder(ids.join(",")).then((res) => {
        this.updataOrderInfo();
      });
    },

    // 分配至已有站点--开始
    handleSelectionChange(val) {
      // 勾选订单
      console.log(val);
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
      this.$api.getDateList(id).then((res) => {
        this.tourDate = res.data;
      });
    },
    getBatchPageListByOrder(val) {
      // 分配至已有站点--获取可分配站点列表
      this.batchPageListByOrderInfo.execution_date = val;
      this.$api
        .getTrackingOrderBatchList(this.batchPageListByOrderInfo)
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
        this.$api
          .putTrackingOrderAssignBatch(this.assignToBatchInfo)
          .then((res) => {
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
      this.$api.synchronizeStatusList({ id_list: ids }).then((res) => {
        console.log(res);
      });
    },
    //同步订单状态--结束

    //订单无效化--开始
    orderNeutralize(id) {
      this.$api.orderNeutralize(id).then((res) => {
        console.log(res);
      });
    },
    //订单无效化--结束

    // 订单初始化--开始
    getInitPieIndex() {
      // 订单类数据
      this.$api
        .getTrackingOrderCount({ type: this.pageParams.type || 0 })
        .then((res) => {
          this.orderList.forEach((val) => {
            val.total = res.data[val.id];
          });
        });
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
        .getTrackingOrder(this.pageParams.status === 10 ? obj : this.pageParams)
        .then((res) => {
          this.tableData = res.data.data;
          this.pageParams.total = res.data.meta.total;
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
        if (val.id == tab.name) {
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
              this.$message({
                message: this.$t("SuccessfullDeleted") + "!",
                type: "success",
              });
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
    getOrderTrail(order) {
      // 查看轨迹
      this.activities = [];
      this.show_trajectory = true;
      this.$api.getTrackingOrderTrail(order).then((res) => {
        res.data.tracking_order_trail_list.forEach((ele) => {
          this.activities.push({
            content: ele.content,
            timestamp: `${ele.created_at}  ${ele.operator_name}`,
            color: "#0bbd87",
          });
        });
        this.activities.reverse();
      });
    },
    showOrderDetails(id) {
      // 查看记录
      this.show_order = true;
      this.$api.getTrackingOrderDetails(id).then((res) => {
        this.orderDetails = res.data;
        if (this.orderDetails.nature) {
          this.orderDetails.nature =
            this.orderNature[this.orderDetails.nature - 1].name;
        }
        if (this.orderDetails.settlement_type) {
          this.orderDetails.settlement_type =
            this.settlementType[this.orderDetails.settlement_type - 1].name;
        }
        if (this.orderDetails.delivery) {
          this.orderDetails.delivery =
            this.deliveryType[this.orderDetails.delivery - 1].name;
        }
      });
    },
    removeFromBatch(id) {
      // 从当前站点移除
      this.$api.trackingOrderRemoveBatch(id).then((res) => {
        if (res.code === 200) {
          this.$message({
            message: this.$t("RemovedSuccessfully") + "!",
            type: "success",
          });
          this.updataOrderInfo();
        }
      });
    },
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

    // 修改运单出库状态
    putTrackingOrderOutStatus(selectDate, status) {
      // 站点移除
      if (!selectDate) {
        this.$message({
          type: "error",
          message: this.$t("pleaseSelectOrder"),
        });
        return;
      }

      this.$api
        .putTrackingOrderOutStatus({ id_list: selectDate, out_status: status })
        .then((res) => {
          if (!res) return;
          this.multipleSelection = [];
          this.updataOrderInfo();
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
  }
}
.order-type {
  margin-bottom: 20px;
  padding: 20px 25px 20px;
  background: #ffffff;
  border: 1px solid rgba(228, 235, 241, 1);
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
      width: 170px;
    }
  }
}
</style>
<style lang="less">
.el-picker-panel {
  border-radius: 0;
}
.mult-control-btn {
  margin-right: 20px;
}
</style>
