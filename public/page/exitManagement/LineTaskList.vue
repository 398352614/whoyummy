<template>
  <!-- 线路任务 -->
  <div class="content-box">
    <div class="search-right">
      <el-button size="small" @click="sortByDate()" class="common">
        {{ $t("PickupDateSort") }}
        <i class="el-icon-d-caret"></i>
      </el-button>
      <!-- 导出站点 -->
      <el-tooltip
        :content="$t('AdministratorActivate')"
        placement="top"
        :disabled="hasPerm('tour.batch-excel') ? true : false"
      >
        <div>
          <el-popover placement="top" width="300" v-model="station_visible">
            <div>
              <p>{{ $t("ExportMonth") }}</p>
              <div class="select-date">
                <el-select
                  size="small"
                  v-model="batchExcelData.year"
                  :placeholder="$t('year')"
                >
                  <el-option
                    v-for="item in yearData"
                    :key="item"
                    :label="item"
                    :value="item"
                  >
                  </el-option>
                </el-select>
                <el-select
                  size="small"
                  v-model="batchExcelData.month"
                  :placeholder="$t('month')"
                >
                  <el-option
                    v-for="item in monthData"
                    :key="item"
                    :label="item"
                    :value="item"
                  >
                  </el-option>
                </el-select>
              </div>
            </div>
            <div style="text-align: right; margin: 0">
              <el-button
                size="mini"
                type="primary"
                @click="station_visible = false"
                class="cancel"
                >{{ $t("Cancel") }}
              </el-button>
              <el-button
                type="primary"
                size="mini"
                :disabled="!(batchExcelData.year && batchExcelData.month)"
                @click="exportExcelData"
                class="submit"
                >{{ $t("Confirm") }}</el-button
              >
            </div>
            <el-button
              slot="reference"
              size="small"
              :class="[
                hasPerm('tour.batch-excel') ? 'common' : 'common-disabled',
                'top-btn',
              ]"
              :disabled="!hasPerm('tour.batch-excel')"
              >{{ $t("SiteDataExport") }}</el-button
            >
          </el-popover>
        </div>
      </el-tooltip>
      <!-- 智能调度 -->
      <el-tooltip
        :content="$t('AdministratorActivate')"
        placement="top"
        :disabled="hasPerm('tour.intelligent-scheduling') ? true : false"
      >
        <div>
          <el-button
            :disabled="!hasPerm('tour.intelligent-scheduling')"
            size="small"
            type="danger"
            @click="$router.push({ name: 'intelligentDispatch' })"
            >{{ $t("IntelligentLineScheduling") }}</el-button
          >
        </div>
      </el-tooltip>
    </div>
    <el-tabs v-model="pageParams.status" @tab-click="handlerTabClick">
      <div class="search-panel">
        <div class="search-input">
          <el-date-picker
            size="small"
            @change="handlerSearchDate"
            v-model="search_date"
            type="daterange"
            value-format="yyyy-MM-dd"
            :range-separator="$t('To')"
            :start-placeholder="$t('StartTime')"
            :end-placeholder="$t('EndTime')"
            :picker-options="pickerOptionsSearch"
            class="search-bar-date"
          >
          </el-date-picker>
          <el-select
            size="small"
            clearable
            v-model="pageParams.driver_id"
            :placeholder="$t('PleaseSelectDriver')"
            class="search-bar"
          >
            <el-option
              v-for="(item, index) in allDriverList"
              :key="index"
              :label="item.fullname"
              :value="item.id"
            ></el-option>
          </el-select>
          <el-select
            clearable
            :placeholder="this.$t('pleaseNumber')"
            size="small"
            @keyup.enter.native="hanndlerChange"
            v-model="pageParams.car_no"
            class="search-bar"
          >
            <el-option
              v-for="(item, index) in carList"
              :key="index"
              :label="item.car_no"
              :value="item.car_no"
            ></el-option>
          </el-select>

          <el-input
            v-model="pageParams.line_name"
            @keyup.enter.native="hanndlerChange"
            clearable
            :placeholder="$t('LineName')"
            size="small"
            class="search-bar"
          ></el-input>
          <el-input
            @keyup.enter.native="hanndlerChange"
            v-model="pageParams.tour_no"
            clearable
            :placeholder="$t('Pickuplinecode')"
            size="small"
            class="search-bar"
          ></el-input>
          <el-button
            size="small"
            @click="hanndlerChange"
            :loading="this.$store.state.config.button_loading"
            type="primary"
            >{{ $t("Search") }}</el-button
          >
        </div>
      </div>
      <el-tab-pane
        v-for="item in active_type"
        :key="item.label"
        :label="item.label"
        :name="item.name"
      >
        <el-table
          height="calc(100vh - 272px)"
          :data="hasPerm('tour.index') ? list : []"
          v-loading="global_loading()"
          element-loading-text="loading..."
          border
          :cell-style="{ padding: '5px 0px' }"
        >
          <template slot="empty" v-if="!hasPerm('tour.index')">
            <div style="font-size: 18px">
              {{ $t("AdministratorActivate") }}
            </div>
          </template>
          <el-table-column
            :label="$t('Number')"
            type="index"
            width="50"
            align="center"
          >
          </el-table-column>
          <el-table-column :label="$t('Operation')" align="center" width="120">
            <template slot-scope="scope">
              <el-dropdown>
                <el-button size="mini" type="primary" plain class="operation">
                  {{ $t("Operation") }}
                  <i class="el-icon-arrow-down el-icon--right"></i>
                </el-button>
                <el-dropdown-menu slot="dropdown">
                  <!-- 分配司机 -->
                  <el-tooltip
                    :content="$t('AdministratorActivate')"
                    placement="top"
                    :disabled="
                      hasPerm('assign-driver|tour.cancel-driver') ? true : false
                    "
                  >
                    <div
                      :style="
                        hasPerm('assign-driver|tour.cancel-driver')
                          ? ''
                          : 'cursor: not-allowed'
                      "
                    >
                      <el-dropdown-item
                        v-if="scope.row.status === 1"
                        :disabled="!hasPerm('assign-driver|tour.cancel-driver')"
                        @click.native="shareDriver(scope.row)"
                      >
                        <el-button
                          class="operation"
                          size="small"
                          type="text"
                          :disabled="
                            !hasPerm('assign-driver|tour.cancel-driver')
                          "
                          >{{ $t("AssignDriver") }}</el-button
                        >
                      </el-dropdown-item>
                    </div>
                  </el-tooltip>
                  <!-- 线路追踪 -->
                  <el-tooltip
                    :content="$t('AdministratorActivate')"
                    placement="top"
                    :disabled="hasPerm('tour.track') ? true : false"
                  >
                    <div
                      :style="
                        hasPerm('tour.track') ? '' : 'cursor: not-allowed'
                      "
                    >
                      <el-dropdown-item
                        v-if="scope.row.status === 4 || scope.row.status === 5"
                        :disabled="!hasPerm('tour.track')"
                        @click.native="handlerLineTrack(scope.row)"
                      >
                        <el-button
                          class="operation"
                          size="small"
                          type="text"
                          :disabled="!hasPerm('tour.track')"
                          >{{ $t("lineTasks") }}</el-button
                        >
                      </el-dropdown-item>
                    </div>
                  </el-tooltip>
                  <!-- 取消分配司机 -->
                  <el-tooltip
                    :content="$t('AdministratorActivate')"
                    placement="top"
                    :disabled="
                      hasPerm('assign-driver|tour.cancel-driver') ? true : false
                    "
                  >
                    <div
                      :style="
                        hasPerm('assign-driver|tour.cancel-driver')
                          ? ''
                          : 'cursor: not-allowed'
                      "
                    >
                      <el-dropdown-item
                        :disabled="!hasPerm('assign-driver|tour.cancel-driver')"
                        v-if="scope.row.status === 2"
                        @click.native="cancelDriver(scope.row)"
                      >
                        <el-button
                          :disabled="
                            !hasPerm('assign-driver|tour.cancel-driver')
                          "
                          class="operation"
                          size="small"
                          type="text"
                          >{{ $t("CancelAssignDriver") }}</el-button
                        >
                      </el-dropdown-item>
                    </div>
                  </el-tooltip>
                  <!-- 分配车辆 -->
                  <el-tooltip
                    :content="$t('AdministratorActivate')"
                    placement="top"
                    :disabled="
                      hasPerm('tour.assign-car|tour.cancel-car') ? true : false
                    "
                  >
                    <div
                      :style="
                        hasPerm('tour.assign-car|tour.cancel-car')
                          ? ''
                          : 'cursor: not-allowed'
                      "
                    >
                      <el-dropdown-item
                        v-if="!scope.row.car_id"
                        @click.native="shareCar(scope.row)"
                        :disabled="!hasPerm('tour.assign-car|tour.cancel-car')"
                      >
                        <el-button
                          :disabled="
                            !hasPerm('tour.assign-car|tour.cancel-car')
                          "
                          class="operation"
                          size="small"
                          type="text"
                          >{{ $t("AssignTruck") }}</el-button
                        >
                      </el-dropdown-item>
                    </div>
                  </el-tooltip>
                  <!-- 取消分配车辆 -->
                  <el-tooltip
                    :content="$t('AdministratorActivate')"
                    placement="top"
                    :disabled="
                      hasPerm('tour.assign-car|tour.cancel-car') ? true : false
                    "
                  >
                    <div
                      :style="
                        hasPerm('tour.assign-car|tour.cancel-car')
                          ? ''
                          : 'cursor: not-allowed'
                      "
                    >
                      <el-dropdown-item
                        v-if="
                          scope.row.car_id &&
                          (scope.row.status === 1 || scope.row.status === 2)
                        "
                        @click.native="cancelCar(scope.row)"
                        :disabled="!hasPerm('tour.assign-car|tour.cancel-car')"
                      >
                        <el-button
                          :disabled="
                            !hasPerm('tour.assign-car|tour.cancel-car')
                          "
                          class="operation"
                          size="small"
                          type="text"
                          >{{ $t("CancelAssignTruck") }}</el-button
                        >
                      </el-dropdown-item>
                    </div>
                  </el-tooltip>
                  <!-- 智能调度 -->
                  <el-tooltip
                    :content="$t('AdministratorActivate')"
                    placement="top"
                    :disabled="
                      hasPerm('tour.intelligent-scheduling') ? true : false
                    "
                  >
                    <div
                      :style="
                        hasPerm('tour.intelligent-scheduling')
                          ? ''
                          : 'cursor: not-allowed'
                      "
                    >
                      <el-dropdown-item
                        v-if="scope.row.status !== 5"
                        :disabled="!hasPerm('tour.intelligent-scheduling')"
                        @click.native="intelligentScheduling(scope.row)"
                      >
                        <el-button
                          :disabled="!hasPerm('tour.intelligent-scheduling')"
                          class="operation"
                          size="small"
                          type="text"
                          >{{ $t("IntelligentDispatch") }}</el-button
                        ></el-dropdown-item
                      >
                    </div>
                  </el-tooltip>
                  <!-- 更换线路 -->
                  <el-tooltip
                    :content="$t('AdministratorActivate')"
                    placement="top"
                    :disabled="hasPerm('tour.assign') ? true : false"
                  >
                    <div
                      :style="
                        hasPerm('tour.assign') ? '' : 'cursor: not-allowed'
                      "
                    >
                      <el-dropdown-item
                        v-if="scope.row.status === 1 || scope.row.status === 2"
                        :disabled="!hasPerm('tour.assign')"
                        @click.native="replaceLine(scope.row)"
                      >
                        <el-button
                          :disabled="!hasPerm('tour.assign')"
                          class="operation"
                          size="small"
                          type="text"
                          >{{ $t("Changeroute") }}</el-button
                        ></el-dropdown-item
                      >
                    </div>
                  </el-tooltip>
                  <!-- 查看详情  任务报告查看-->
                  <el-tooltip
                    :content="$t('AdministratorActivate')"
                    placement="top"
                    :disabled="hasPerm('tour.index') ? true : false"
                  >
                    <div
                      :style="
                        hasPerm('tour.index') ? '' : 'cursor: not-allowed'
                      "
                    >
                      <el-dropdown-item
                        @click.native="showDetial(scope.row)"
                        :disabled="!hasPerm('tour.index')"
                      >
                        <el-button
                          :disabled="!hasPerm('tour.index')"
                          class="operation"
                          size="small"
                          type="text"
                          >{{ $t("viewDetails") }}</el-button
                        ></el-dropdown-item
                      >
                    </div>
                  </el-tooltip>
                  <!-- 取消待出库 -->
                  <el-tooltip
                    :content="$t('AdministratorActivate')"
                    placement="top"
                    :disabled="hasPerm('tour.unlock') ? true : false"
                  >
                    <div
                      :style="
                        hasPerm('tour.unlock') ? '' : 'cursor: not-allowed'
                      "
                    >
                      <el-dropdown-item
                        v-if="scope.row.status === 3"
                        @click.native="cancelOutbound(scope.row)"
                        :disabled="!hasPerm('tour.unlock')"
                      >
                        <el-button
                          :disabled="!hasPerm('tour.unlock')"
                          class="operation"
                          size="small"
                          type="text"
                          >{{ $t("cancelOutWarehouse") }}</el-button
                        ></el-dropdown-item
                      >
                    </div>
                  </el-tooltip>
                  <!-- <el-dropdown-item @click.native="outputStationList(scope.row)">{{$t('exportExpressList')}}</el-dropdown-item>
                    <el-dropdown-item @click.native="outputCityList(scope.row)">{{$t('exportExpressCity')}}</el-dropdown-item>
                    <el-dropdown-item @click.native="outputCityMap(scope.row)">{{$t('exportMapPrint')}}</el-dropdown-item>
                    <el-dropdown-item @click.native="handlerMap(scope.row)">{{$t('地图demo')}}</el-dropdown-item> -->
                </el-dropdown-menu>
              </el-dropdown>
            </template>
          </el-table-column>
          <el-table-column
            prop="tour_no"
            :label="$t('Pickuplinecode')"
            align="center"
            width="160"
          >
            <template slot-scope="scope">
              <el-link
                class="link"
                :underline="false"
                @click="
                  hasPerm('tracking-order.index')
                    ? $router.push({
                        name: 'WaybillManagement',
                        params: { tour_no: scope.row.tour_no },
                      })
                    : {}
                "
                >{{ scope.row.tour_no }}</el-link
              >
            </template>
          </el-table-column>
          <el-table-column
            prop="expect_batch_count"
            :label="$t('siteQuantity')"
            align="center"
            width="160"
          >
          </el-table-column>
          <el-table-column
            prop="line_name"
            :label="$t('LineName')"
            align="center"
            width="160"
          >
          </el-table-column>
          <el-table-column
            prop="merchant_id_name"
            :label="$t('routeRange')"
            align="center"
            width="160"
          >
          </el-table-column>
          <el-table-column
            prop="execution_date"
            :label="$t('DistributeDate')"
            align="center"
            width="160"
          >
          </el-table-column>
          <el-table-column
            :label="$t('PickupStatus')"
            align="center"
            width="160"
          >
            <template slot-scope="scope">
              {{ scope.row.status_name }}
            </template>
          </el-table-column>
          <el-table-column
            prop="driver_name"
            :label="$t('PickupDriver')"
            align="center"
            width="160"
          >
          </el-table-column>
          <el-table-column
            prop="car_no"
            :label="$t('PickupTruck')"
            align="center"
            width="160"
          >
          </el-table-column>
          <el-table-column
            :label="$t('EstimatedActualPickupQty')"
            align="center"
            width="250"
          >
            <template slot-scope="scope">
              {{ scope.row.expect_pickup_quantity }}/{{
                scope.row.actual_pickup_quantity
              }}
            </template>
          </el-table-column>
          <el-table-column
            :label="$t('EstimatedActualDeliveryQty')"
            align="center"
            width="250"
          >
            <template slot-scope="scope">
              {{ scope.row.expect_pie_quantity }}/{{
                scope.row.actual_pie_quantity
              }}
            </template>
          </el-table-column>
          <el-table-column
            prop="created_at"
            :label="$t('Created')"
            align="center"
            width="180"
          >
          </el-table-column>
        </el-table>
      </el-tab-pane>
      <base-pagination
        ref="searchRef"
        :pageParams="pageParams"
        @search="getList"
        class="pagination-size"
      ></base-pagination>
    </el-tabs>

    <el-dialog :title="$t('AssignDriver')" :visible.sync="visible_driver">
      <!-- 查询 -->
      <el-row :gutter="10">
        <el-col :span="6">
          <el-input
            size="small"
            style="margin-bottom: 10px"
            clearable
            :placeholder="$t('pleaseEnterName')"
            @keyup.enter.native="getDrivers"
            v-model="driverPageParams.fullname"
          >
          </el-input>
        </el-col>
        <el-col :span="2">
          <!-- 查询 -->
          <el-button
            size="small"
            type="primary"
            @click="getDrivers"
            :loading="this.$store.state.config.button_loading"
            >{{ $t("Search") }}</el-button
          ></el-col
        >
      </el-row>
      <el-table
        max-height="600px"
        @current-change="handleDriverSelectionChange"
        @row-click="handlerDriverRowClick"
        border
        :data="drivers_list"
      >
        <el-table-column align="center" width="55" label="">
          <template slot-scope="scope">
            <el-radio v-model="tempSelected" :label="scope.row.id">{{
              ""
            }}</el-radio>
          </template>
        </el-table-column>
        <el-table-column
          align="center"
          :label="$t('Code')"
          type="index"
          width="60"
        ></el-table-column>
        <el-table-column :label="$t('ContactName')" align="center">
          <template slot-scope="scope">
            {{ scope.row.fullname }}
          </template>
        </el-table-column>
        <el-table-column
          align="center"
          :label="$t('Tel')"
          prop="phone"
        ></el-table-column>
        <el-table-column align="center" :label="$t('DriverType')">
          <template slot-scope="scope">
            {{
              scope.row.crop_type === 1 ? $t("employ") : $t("dedicatedRoute")
            }}
          </template>
        </el-table-column>
        <el-table-column align="center" :label="$t('Status')">
          <template slot-scope="scope">
            {{ scope.row.is_locked === 1 ? $t("normal") : $t("Locked") }}
          </template>
        </el-table-column>
      </el-table>
      <base-pagination
        :pageParams="driverPageParams"
        @search="getDrivers"
      ></base-pagination>
      <el-row class="list_button">
        <el-col :span="2" :offset="17">
          <el-button @click="visible_driver = false" class="cancel">{{
            $t("Cancel")
          }}</el-button>
        </el-col>
        <el-col :span="2" :offset="1">
          <el-button @click="onSubmitDriver" type="primary" class="submit">{{
            $t("Submit")
          }}</el-button>
        </el-col>
      </el-row>
    </el-dialog>

    <el-dialog
      :title="$t('AssignTruck')"
      @current-change="handleCarSelectionChange"
      :visible.sync="visible_car"
    >
      <!-- 查询 -->
      <el-row :gutter="10">
        <el-col :span="6">
          <el-input
            size="small"
            style="margin-bottom: 10px"
            clearable
            :placeholder="$t('pleaseEnterTheLicensePlateNumber')"
            @keyup.enter.native="getCars"
            @clear="getCars"
            v-model="carPageParams.car_no"
          >
          </el-input>
        </el-col>
        <el-col :span="2">
          <!-- 查询 -->
          <el-button
            size="small"
            type="primary"
            @click="getCars"
            :loading="this.$store.state.config.button_loading"
            >{{ $t("Search") }}</el-button
          ></el-col
        >
      </el-row>
      <el-table
        max-height="600px"
        border
        :data="car_list"
        @row-click="handlerRowClick"
      >
        <el-table-column align="center" width="55" label="">
          <template slot-scope="scope">
            <el-radio v-model="car_data.car_id" :label="scope.row.id">{{
              ""
            }}</el-radio>
          </template>
        </el-table-column>
        <el-table-column
          align="center"
          :label="$t('Number')"
          type="index"
          width="60"
        >
        </el-table-column>
        <el-table-column
          align="center"
          :label="$t('NumberPlate')"
          prop="car_no"
        >
        </el-table-column>
      </el-table>
      <base-pagination
        :pageParams="carPageParams"
        @search="getCars"
      ></base-pagination>
      <el-row class="list_button">
        <el-col :span="2" :offset="17">
          <el-button @click="visible_car = false" class="cancel">{{
            $t("Cancel")
          }}</el-button>
        </el-col>
        <el-col :span="2" :offset="1">
          <el-button @click="onSubmitCar" type="primary" class="submit">{{
            $t("Submit")
          }}</el-button>
        </el-col>
      </el-row>
    </el-dialog>

    <!-- 更换路线 -->
    <el-dialog
      :title="$t('Changeroute')"
      width="60%"
      @close="lineClose"
      :visible.sync="replaceLineDlg"
    >
      <el-form
        ref="line_form"
        :model="line_form"
        :rules="rules"
        label-width="160px"
      >
        <el-form-item :label="$t('date')" prop="execution_date">
          <el-date-picker
            size="small"
            @change="getLineList"
            v-model="line_form.execution_date"
            value-format="yyyy-MM-dd"
            :placeholder="$t('Date')"
            type="date"
            :picker-options="pickerOptions"
          >
          </el-date-picker>
        </el-form-item>
        <el-form-item
          :label="$t('routeOptions')"
          v-if="line_form.execution_date"
        >
          <el-radio-group v-model="line_form.line_id">
            <el-radio
              v-for="(item, index) in options"
              :key="index"
              :label="item.id"
              @change="changeTourNo"
              >{{ item.name }}</el-radio
            >
          </el-radio-group>
        </el-form-item>
        <el-form-item label=" " v-if="line_form.line_id && allLine.length">
          <el-select
            v-model="line_form.tour_no"
            :placeholder="$t('pleaseSelectPickupRoute')"
          >
            <el-option
              v-for="(item, index) in allLine"
              :key="index"
              :label="item.tour_no"
              :value="item.tour_no"
            ></el-option>
          </el-select>
        </el-form-item>
        <!-- <el-form-item
            label="创建线路类型"
            v-if="line_form.line_id && !allLine.length && merchant_id !== 0">
            <el-radio-group v-model="line_form.is_alone">
              <el-radio label="1">独立线路</el-radio>
              <el-radio label="2">混合线路</el-radio>
            </el-radio-group>
          </el-form-item> -->
      </el-form>
      <el-row>
        <el-col :span="2" :offset="17">
          <el-button @click="replaceLineDlg = false" class="cancel">{{
            $t("Cancel")
          }}</el-button>
        </el-col>
        <el-col :span="2" :offset="1">
          <el-button
            @click="onSubmitReplaceLine"
            :disabled="checkLineForm"
            type="primary"
            class="submit"
            >{{ $t("Submit") }}</el-button
          >
        </el-col>
      </el-row>
    </el-dialog>
    <!-- 更换路线 -->

    <iframe frameborder="0" name="downExcel" style="display: none"></iframe>
  </div>
</template>

<script>
import BasePagination from "@/components/BasePagination/BasePagination";
import loading from "@/mixin/global_loading";

export default {
  name: "lineTask",
  components: {
    BasePagination,
  },
  mixins: [loading],
  data() {
    return {
      list: [],
      search_date: ["", ""],
      pageParams: {
        page: 1,
        per_page: 20,
        total: 0,
        status: "",
        sort_by_time: 2,
        driver_id: "", // 司机ID
        begin_date: "",
        end_date: "",
        line_name: "",
        tour_no: "",
      },
      allDriverList: [],
      loading: false,
      options: [],
      visible_driver: false,
      visible_car: false,
      drivers_list: [],
      carList: [], //查询获取车辆下拉
      car_list: [],
      driverPageParams: {
        fullname: "", //联系人姓名
        page: 1,
        per_page: 10,
        total: 0,
        driver_id: "", // 司机ID
        begin_date: "",
        end_date: "",
        tour_no: "",
      },
      carPageParams: {
        car_no: "", //车牌号
        page: 1,
        per_page: 10,
        total: 0,
        driver_id: "", // 司机ID
        begin_date: "",
        end_date: "",
        tour_no: "",
      },
      driver_data: {
        id: "", // 取件线路 id
        driver_id: "", // 司机 id
      },
      car_data: {
        id: "", // 取件线路 id
        car_id: "", // 车辆 id
      },
      tempSelected: "", // 临时变量

      // 更换线路
      replaceLineDlg: false,
      merchant_id: null, // 是否多商家
      line_form: {
        id: "",
        execution_date: "",
        tour_no: "",
        line_id: null,
      },
      options: [],
      allLine: [], // 所有线路
      tourDate: [],
      nowDate: Date.now() - 24 * 60 * 60 * 1000,

      batchExcelData: {
        year: null,
        month: null,
      },
      station_visible: false,
      rules: {
        execution_date: [
          { required: true, message: this.$t("Required"), trigger: "blur" },
        ],
      },
    };
  },
  beforeRouteEnter: (to, from, next) => {
    next((vm) => {
      console.log(from, "asd");
      if (vm.$store.state.globalData.lineTeskData) {
        // vm.pageParams = vm.$store.state.globalData.lineTeskData
      } else {
        vm.$route.query.status = "";
      }
    });
  },
  beforeRouteLeave(to, from, next) {
    if (
      to.name === "intelligentDispatch" ||
      to.name === "lineTrack" ||
      to.name === "TaskReportDetails"
    ) {
      this.$store.commit("globalData/setLineTeskData", this.pageParams);
    } else {
      this.$store.commit("globalData/setLineTeskData", {});
    }
    console.log(
      this.$store.state.globalData.lineTeskData,
      "this.$store.state.globalData.lineTeskData",
      to
    );
    next(); //跳转到另一个路由
  },
  activated() {
    if (this.$route.params.id) {
      this.pageParams.tour_no = this.$route.params.id;
      this.getList();
    }
    this.getList();
  },
  created() {
    // this.getList();
    console.log(
      this.$store.state.globalData.lineTeskData,
      "sss",
      this.$router,
      this.$route
    );
    if (this.$store.state.globalData.lineTeskData["page"] !== undefined) {
      console.log("as");
      this.pageParams = this.$store.state.globalData.lineTeskData;
      this.search_date = [this.pageParams.begin_date, this.pageParams.end_date];
    }
    if (this.$route.params.id) {
      this.pageParams.tour_no = this.$route.params.id;
      this.pageParams.status = "";
    }
  },
  mounted() {
    this.getList();
    this.getAllDrivers();
    this.getCarList();
  },
  computed: {
    active_type() {
      return [
        {
          name: "",
          label: this.$t("All"),
        },
        {
          name: "1",
          label: this.$t("Unassigned"),
        },
        {
          name: "2",
          label: this.$t("Assigned"),
        },
        {
          name: "3",
          label: this.$t("Waitingfordelivery"),
        },
        {
          name: "4",
          label: this.$t("Distributing"),
        },
        {
          name: "5",
          label: this.$t("Delivered"),
        },
      ];
    },
    pickerOptions() {
      return {
        disabledDate(time) {
          return time.getTime() < Date.now() - 8.64e7;
        },
      };
    },
    checkLineForm() {
      if (
        this.line_form.execution_date &&
        this.line_form.tour_no &&
        this.line_form.line_id
      ) {
        return false;
      } else {
        return true;
      }
    },
    mapType() {
      return this.$store.state.globalData.maptype;
    },
    yearData() {
      let date = new Date();
      let now = date.getFullYear();
      let yearList = [];
      for (let i = 0; i < 50; i++) {
        yearList.push(+now - i);
      }
      return yearList;
    },
    monthData() {
      let date = new Date();
      let now = date.getMonth();
      let nowYear = date.getFullYear();
      let monthList = [];
      for (let i = 0; i <= 11; i++) {
        if (this.batchExcelData.year && +this.batchExcelData.year == nowYear) {
          if (i <= +now) {
            monthList.push(i + 1);
          }
        } else {
          monthList.push(i + 1);
        }
      }
      return monthList;
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
  watch: {
    "batchExcelData.year": {
      handler(val, old) {
        let date = new Date();
        let now = date.getMonth();
        if (+this.batchExcelData.month > +now + 1) {
          this.batchExcelData.month = null;
        }
      },
      deep: true,
    },
  },
  methods: {
    // getDictionary() {
    //   this.$api.getDictionary().then((res) => {
    //     this.addressTypeList = res.data.addressTypeList;
    //   });
    // },
    // 获取车辆下拉
    getCarList() {
      this.$api.getCarsPublic().then((res) => {
        this.carList = res.data.data;
      });
    },
    handlerMap(row) {
      this.$router.push({
        name: "Mapdemo",
        query: {
          id: row.id,
          tour_no: row.tour_no,
        },
      });
    },
    getAllDrivers() {
      this.$api
        .getDriversPublic({
          page: 1,
          per_page: 200,
        })
        .then((res) => {
          this.allDriverList = res.data.data;
        });
    },
    handlerLineTrack(row) {
      if (this.mapType === "baidu") {
        this.$router.push({
          name: "lineTrackBaidu",
          query: {
            id: row.id,
            tour_no: row.tour_no,
          },
        });
      } else {
        this.$router.push({
          name: "lineTrack",
          query: {
            id: row.id,
            tour_no: row.tour_no,
          },
        });
      }
    },
    outputCityMap(row) {
      this.$api.cityMap(row.id).then((res) => {
        if (!res) return;
        this.downloadFile(res.data.path, res.data.name);
      });
    },
    outputStationList(row) {
      this.$api.stationListText(row.id).then((res) => {
        if (!res) return;
        this.downloadFile(res.data.path, res.data.name);
      });
    },
    downloadTxt(content, txtName) {
      const result = JSON.stringify(content);
      const datastr = `data:text/txt;charset=utf-8,${result}`;
      const a = document.createElement("a");
      a.setAttribute("href", datastr);
      a.setAttribute("download", `${txtName}.txt`);
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
    },
    downloadFile(url, txtName) {
      const a = document.createElement("a");
      a.setAttribute("href", url);
      a.setAttribute("download", `${txtName}`);
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
    },
    outputCityList(row) {
      this.$api.cityListTxt(row.id).then((res) => {
        if (!res) return;
        this.downloadTxt(res.data.txt, res.data.name);
      });
    },
    handlerRowClick(row) {
      this.car_data.car_id = row.id;
    },
    handlerDriverRowClick(row) {
      this.tempSelected = row.id;
    },
    handleCarSelectionChange(selected) {
      if (!selected) return;
      this.car_data.car_id = selected.id;
    },
    handleDriverSelectionChange(selected) {
      if (!selected) return;
      this.driver_data.driver_id = selected.id;
    },
    onSubmitDriver() {
      this.$api
        .distributeDriver(this.driver_data.id, {
          driver_id: this.driver_data.driver_id,
        })
        .then((res) => {
          if (!res) return;
          this.visible_driver = !this.visible_driver;

          this.tempSelected = "";
          this.driver_data.driver_id = "";
          this.driver_data.id = "";
          this.getList();
          this.getDrivers();
        });
    },
    onSubmitCar() {
      this.$api
        .distributeCar(this.car_data.id, {
          car_id: this.car_data.car_id,
        })
        .then((res) => {
          if (!res) return;
          this.visible_car = !this.visible_car;

          this.tempSelected = "";
          this.car_data.car_id = "";
          this.car_data.id = "";
          this.getList();
          this.getDrivers();
        });
    },
    getCars() {
      this.$api.getCarsPublic(this.carPageParams).then((res) => {
        // console.log(" res.data.data");
        // console.log(res.data.data);
        this.car_list = res.data.data;
        this.carPageParams.total = res.data.meta.total;
      });
    },
    getDrivers() {
      this.$api.getDriversPublic(this.driverPageParams).then((res) => {
        this.drivers_list = res.data.data;
        this.driverPageParams.total = res.data.meta.total;
      });
    },
    shareDriver(row) {
      this.driver_data.id = "";
      this.driver_data.id = row.id;
      this.driverPageParams.tour_no = row.tour_no;

      this.visible_driver = !this.visible_driver;
      this.getDrivers();
    },
    cancelDriver(row) {
      this.$api.cancelDriverDistribute(row.id).then((res) => {
        if (!res) return;
        this.getList();
        this.getDrivers();
      });
    },
    cancelCar(row) {
      this.$api.cancelCarDistribute(row.id).then((res) => {
        if (!res) return;
        this.getList();
        this.getDrivers();
      });
    },
    shareCar(row) {
      this.car_data.id = "";
      this.car_data.id = row.id;
      this.carPageParams.tour_no = row.tour_no;

      this.visible_car = !this.visible_car;
      this.getCars();
    },
    intelligentScheduling(row) {
      if (this.mapType === "baidu") {
        this.$router.push({
          name: "IntelligentDispatchBaidu",
          query: {
            id: row.id,
            status: row.status,
            tour_no: row.tour_no,
          },
        });
      } else {
        this.$router.push({
          name: "intelligentDispatch",
          query: {
            id: row.id,
            status: row.status,
            tour_no: row.tour_no,
            is_dispatch: row.is_dispatch,
          },
        });
      }
    },
    showDetial(row) {
      this.$router.push({
        name: "TaskReportDetails",
        query: {
          id: row.id,
        },
      });
    },
    cancelOutbound(row) {
      this.$api.cancelOutbound(row.id).then(() => {
        this.getList();
      });
    },
    hanndlerChange() {
      this.pageParams.page = 1;
      this.getList();
    },
    remoteMethod(query) {
      if (query !== "") {
        this.getLines(query);
      } else {
        this.options = [];
      }
    },
    getLines(name) {
      this.line_form.line_id = null;
      this.line_form.tour_no = null;
      this.line_form.allLine = [];
      this.$api.getLineList({ name }).then((res) => {
        this.loading = false;
        this.options = res.data.data.map((item) => item.name);
      });
    },
    handlerSearchDate() {
      if (this.search_date) {
        this.pageParams.begin_date = this.search_date[0];
        this.pageParams.end_date = this.search_date[1];
      } else {
        this.pageParams.begin_date = "";
        this.pageParams.end_date = "";
      }

      this.pageParams.page = 1;
    },
    selectChange() {
      this.$route.query.page = 1;
      this.pageParams.page = 1;
      this.getList();
    },
    handlerTabClick() {
      this.$route.query.page = 1;
      this.pageParams.page = 1;
      this.$refs.searchRef.onChangeValue("status", this.pageParams.status);
    },
    toStationRecord(row) {
      this.$router.push({
        path: "stationDetail",
        query: {
          id: row.id,
        },
      });
    },
    getList() {
      this.$api.getLines(this.pageParams).then((res) => {
        this.list = res.data.data;
        this.pageParams.total = res.data.meta.total;
      });
    },

    sortByDate(val) {
      // 根据日期排序列表
      this.pageParams.sort_by_time == 1
        ? (this.pageParams.sort_by_time = 2)
        : (this.pageParams.sort_by_time = 1);
      this.getList();
    },

    // 更换线路功能
    // 更换线路功能--打开弹窗
    replaceLine(row) {
      this.replaceLineDlg = !this.replaceLineDlg;
      this.line_form.id = row.id;
      this.merchant_id = row.merchant_id;
    },

    // 更换线路功能--关闭弹窗
    lineClose() {
      this.$refs.line_form.resetFields();
      this.line_form.execution_date = null;
      this.line_form.tour_no = null;
      this.line_form.line_id = null;
      this.options = [];
      this.allLine = [];
    },

    // 更换线路功能--改变线路id
    changeTourNo(val) {
      this.line_form.tour_no = null;
      this.allLine = [];
      this.$api
        .getListJoinByLineId({
          line_id: val,
          execution_date: this.line_form.execution_date,
        })
        .then((res) => {
          this.allLine = res.data;
          this.line_form.tour_no = null;
        });
    },

    // 更换线路功能--获取可选日期
    // getLineDate(data) {
    //   this.$api.getLineDate(data).then((res) => {
    //     this.tourDate = res.data;
    //   });
    // },

    // 更换线路功能--选择日期
    getLineList() {
      this.$api
        .getListByDate({
          date: this.line_form.execution_date,
        })
        .then((res) => {
          if (!res) return false;
          this.options = res.data;
        });
    },

    // 更换线路功能--提交表单
    onSubmitReplaceLine() {
      this.$refs.line_form.validate((valid) => {
        if (!valid) return;
        this.$api
          .getTourAssign(this.line_form.id, this.line_form)
          .then((res) => {
            if (!res) return;
            this.getList();
            this.replaceLineDlg = !this.replaceLineDlg;
          });
      });
    },

    //导出站点统计
    exportExcelData() {
      this.$api.getBatchExcel(this.batchExcelData).then((res) => {
        if (!res) return;
        let iframe = window.frames["downExcel"];
        let href = res.data.path; //接口路径地址，返回数据类型为application/binary，后台控制显示信息，前端仅为下载功能
        iframe.location.href = href;
        this.station_visible = !this.station_visible;
      });
    },
  },
};
</script>
<style lang="less" scoped>
.content-box {
  padding: 10px 25px 5px;
  background-color: #fff;
  position: relative;
  .search-right {
    position: absolute;
    right: 0;
    margin-right: 25px;
    z-index: 99;
    display: flex;
    gap: 10px;
  }
  .search-panel {
    margin: 0 0 10px 0;
    .search-input {
      float: left;
      margin-bottom: 10px;
    }
  }
  .el-tabs__nav-scroll {
    padding-left: 25px;
    box-sizing: border-box;
    height: 62px;
  }
  .search {
    .el-input-group__append {
      background: #28d094;
      color: #ffffff;
      border: 1px solid #28d094;
    }
  }
  .list_button {
    margin: 10px 0 0 0;
  }

  .el-table__header .el-table-column--selection .el-checkbox {
    visibility: hidden;
  }
}

.select-date {
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
}
</style>
<style lang="less">
.content-box {
  .el-input__inner {
    border-radius: 0 !important;
  }
  .el-dropdown-menu__item {
    color: #28d094 !important;
  }
}
</style>
