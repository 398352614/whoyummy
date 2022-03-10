<template>
  <div class="intelligent-dispatch">
    <div class="googleMap">
      <div class="control_panel">
        <!-- 查看路径 -->
        <span v-if="no_show_road" @click="handlerWapPoints">
          {{ $t("ViewRoute") }}
          <i class="iconfont">&#xe720;</i>
        </span>
        <!-- 取消查看路径 -->
        <span v-else @click="handlerSubmit">{{ $t("CancelViewPath") }}</span>
        <!-- 手动优化 -->
        <span @click="handlerOptimization" v-if="!isFinshedLine">
          {{ $t("manuaOptimization") }}
          <i class="iconfont">&#xe605;</i>
        </span>
        <!-- 一键优化 -->
        <span @click="handlerOnceOptimization">
          {{ $t("oneOptimization") }}
          <i class="iconfont">&#xe61d;</i>
        </span>
        <!-- 重新分配 -->
        <span
          v-if="line_data.status == 1 || line_data.status == 2"
          @click="handlerRedistribution"
        >
          {{ $t("Redistribute") }}
        </span>
        <!-- 反序 -->
        <span @click="antitone"> {{ $t("invertedSequence") }} </span>
        <!-- 导出计划 -->
        <span @click="getTourExcel"> {{ $t("exportPlan") }} </span>
      </div>

      <div
        :class="[
          'left_panel_btn',
          isHiddenPanel ? 'hidden_left_panel_btn' : '',
        ]"
      >
        <el-button
          type="text"
          size="small"
          v-if="!isHiddenPanel"
          :class="{ checkStasus: pageParams.is_dispatch == 2 }"
          @click="getLinesParams(2)"
          >{{ $t("toBeScheduled") }}</el-button
        >
        <el-button
          type="text"
          size="small"
          v-if="!isHiddenPanel"
          :class="{ checkStasus: pageParams.is_dispatch == 1 }"
          @click="getLinesParams(1)"
          >{{ $t("scheduled") }}</el-button
        >

        <el-button
          class="show_btn"
          type="text"
          size="small"
          @click="hiddenLeftPanel"
          ><i class="iconfont">&#xe63e;</i></el-button
        >
      </div>
      <div :class="['left_panel', isHiddenPanel ? 'hidden_left_panel' : '']">
        <div class="left_panel_input">
          <el-date-picker
            v-model="search_date"
            type="daterange"
            value-format="yyyy-MM-dd"
            :range-separator="$t('To')"
            :start-placeholder="$t('StartTime')"
            :end-placeholder="$t('EndTime')"
            style="margin-bottom: 5px; width: 100%"
          >
          </el-date-picker>
          <el-input
            size="small"
            clearable
            :placeholder="$t('PleaseEnterLineNumber')"
            @keyup.enter.native="handleClickFilterLine"
            v-model="pageParams.key_word"
          >
          </el-input>
        </div>
        <el-table
          max-height="600px"
          border
          :data="lineList"
          @row-click="handleRowClick"
          :row-class-name="tableRowClassName"
        >
          <el-table-column
            :label="$t('lineNumber')"
            prop="tour_no"
            align="center"
            width="120"
          ></el-table-column>
          <el-table-column
            :label="$t('lineName')"
            prop="line_name"
            align="center"
            width="120"
          ></el-table-column>
          <el-table-column
            :label="$t('DistributeDate')"
            prop="execution_date"
            align="center"
            width="120"
          ></el-table-column>
          <el-table-column
            :label="$t('siteQuantity')"
            prop="expect_batch_count"
            align="center"
            width="120"
          ></el-table-column>
        </el-table>
        <base-pagination
          :pageParams="pageParams"
          @search="getLines"
          :samll="true"
        ></base-pagination>
      </div>
      <!-- <div class="show_left_panel"
              v-show="isHiddenPanel"
                  @click="hiddenLeftPanel">
                  展开
              </div> -->

      <gmap-map
        ref="gmap"
        id="gmap"
        @idle="checkMap"
        :noClear="false"
        :center="center"
        :zoom="center.zoom"
        @zoom_changed="zoomChanged"
        style="width: 100%; height: calc(100vh - 118px)"
      >
        <gmap-marker
          :key="index"
          v-for="(m, index) in markers"
          :icon="m.icon || null"
          :position="m.position"
          :draggable="m.draggable || true"
          @dragend="getMarkerLatLng($event)"
          :label="{
            text: `${m.description}`,
          }"
          @click="toggleInfoWindow(m, index)"
        >
        </gmap-marker>

        <gmap-info-window
          :options="infoOptions"
          :position="infoWindowPos"
          :opened="infoWinOpen"
          @closeclick="infoWinOpen = false"
        >
          <div v-html="infoContent"></div>
        </gmap-info-window>
      </gmap-map>

      <div
        :class="[
          'wrapper',
          isWrapperClose === false ? 'wrapper' : 'wrapper_close',
        ]"
      >
        <div
          :class="[
            'wrapper_header',
            isWrapperHeaderClose ? 'wrapper_header_close' : 'wrapper_header',
          ]"
        >
          <div class="wrapper_header_des">
            <span v-if="!isWrapperClose"
              >{{ tour_no }}&nbsp;&nbsp;{{ $t("Line") }}</span
            >
            <span
              v-if="!isWrapperClose"
              class="wrapper_header_text"
              @click="handlerCloseHeaderDesc"
            >
              {{ $t("Show") }}
            </span>
          </div>
          <div v-show="isWrapperHeaderClose">
            <hr />
            <div class="station_desc" v-show="showDesc">
              <div class="station_text">
                <div class="desc_title">{{ $t("lineInfo") }}</div>
                <div class="desc_value">
                  <div>
                    {{ $t("lineName") }}:&nbsp;&nbsp;{{ line_data.line_name }}
                  </div>
                  <div>
                    {{ $t("DistributeDate") }}:&nbsp;&nbsp;{{
                      line_data.execution_date
                    }}
                  </div>
                </div>
              </div>
              <div class="station_text">
                <div class="desc_title">{{ $t("orderTotal") }}</div>
                <div class="desc_value">
                  <div>{{ line_data.batch_count }}{{ $t("ticket") }}</div>
                  <div>
                    {{ line_data.expect_pickup_quantity }}{{ $t("take") }}/{{
                      line_data.expect_pie_quantity
                    }}{{ $t("send") }}
                  </div>
                  <div>
                    {{ line_data.expect_pickup_package_quantity
                    }}{{ $t("PickupPackage") }}/{{
                      line_data.expect_pie_package_quantity
                    }}{{ $t("DispatchParcel") }}
                  </div>
                </div>
              </div>
              <div class="station_text">
                <div class="desc_title">{{ $t("DriverInfo") }}</div>
                <div class="desc_value">
                  <div>{{ line_data.driver_name }}</div>
                  <div>{{ line_data.car_no }}</div>
                </div>
              </div>
              <div class="station_text"></div>
              <div class="station_text">
                <div class="desc_title">{{ $t("EstimatedConsuming") }}</div>
                <div class="desc_value">
                  <div class="desc_value">
                    {{ $t("TotalEstimatedTime") }}:&nbsp;&nbsp;{{
                      line_data.expect_time_human
                    }}
                  </div>
                  <div class="desc_value">
                    {{ $t("TotalDistance") }}:&nbsp;&nbsp;{{
                      line_data.expect_distance | addKM
                    }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="wrapper_content">
          <div class="station-info" v-if="!switchText">
            {{ $t("Stopinfo") }}
          </div>
          <div class="list_selected">
            <span class="text" v-if="!isWrapperClose" @click="handlerInitOrder">
              {{ $t("selected") }}{{ checkItem }}{{ $t("item") }}
            </span>
            <span class="text" v-if="!isWrapperClose" @click="handlerInitOrder">
              {{ $t("resetSequence") }}
            </span>
          </div>
          <div
            id="stationsList"
            v-if="!isWrapperClose"
            class="stationsList-group"
          >
            <draggable
              ghost-class="ghost"
              @end="dddemo"
              v-if="upload"
              v-model="options"
            >
              <transition-group>
                <div
                  class="card"
                  @click="handlerClickItem(item)"
                  v-for="(item, index) in options"
                  :key="item.id"
                >
                  <div class="card_hd">
                    <div class="hd_clear">
                      <!-- <i class="el-icon-circle-close card_item" @click.stop="handlerRemoveClick(item.id)"></i> -->
                      <!-- <i v-if="item.status === 1 || item.status === 2" class="el-icon-edit card_item" @click.stop="openChangeLineDialog(item.id)"></i> -->
                    </div>
                  </div>
                  <div class="serial-number">
                    <div class="card_content_check">
                      <el-checkbox v-model="item.ischeck"></el-checkbox>
                    </div>
                    <el-input
                      v-if="item['show'] && item.show"
                      size="small"
                      style="min-width: 110px"
                      @keyup.enter.native="editSort(item)"
                      v-model="item.sort_id"
                    >
                      <el-button
                        slot="append"
                        size="small"
                        @click="editSort(item)"
                        type="primary"
                        >{{ $t("Confirm") }}</el-button
                      >
                    </el-input>
                    <span
                      class="round-mark"
                      v-else
                      @dblclick.stop="openSort(item)"
                      >{{
                        item["sort_id"] && item.sort_id !== 1000
                          ? item.sort_id
                          : index + 1
                      }}</span
                    >
                    <span class="card_text">{{ item.out_user_id }}</span>
                    <span>{{
                      ` ${item.place_city},${item.place_post_code}`
                    }}</span>
                  </div>
                  <div class="card_content">
                    <div>
                      <div v-if="showSiteInfo">
                        <span>{{ $t("EstimatedDistance") }}:</span>
                        <span>{{ item.expect_distance | addKM }}</span>
                      </div>
                      <div v-if="showSiteInfo">
                        <span>{{ $t("EstimatedArrivalTime") }}:</span>
                        <span>{{ item.expect_arrive_time }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </transition-group>
            </draggable>
          </div>
          <div class="control-site-information" v-if="!switchText">
            <span class="all-check" @click="selectAll">{{ $t("AddAll") }}</span>
            <span class="reverse-check" @click="selectReverse">{{
              $t("unselect")
            }}</span>
            <span
              class="show-panel"
              v-if="!showSiteInfo"
              @click="showSiteInfo = true"
              >{{ $t("enlargeAllSiteInfo") }}</span
            >
            <span class="show-panel" v-else @click="showSiteInfo = false">{{
              $t("minimizeAllSiteInfo")
            }}</span>
          </div>
        </div>
        <div class="wrapper_switch" @click="handleSwitchClick">
          <i
            :class="[
              switchText ? 'el-icon-d-arrow-left' : ' el-icon-d-arrow-right',
            ]"
          ></i>
          <!-- {{ switchText ? $t("Expand") : $t("Hide") }} -->
        </div>
      </div>
    </div>

    <el-dialog
      :title="$t('Stopinfo')"
      align="center"
      :visible.sync="dialogVisible"
      width="60%"
    >
      <el-table
        max-height="600px"
        @selection-change="handleSelectionChange"
        ref="multipleTable"
        height="600"
        :data="options"
      >
        <el-table-column type="selection" width="50"></el-table-column>
        <el-table-column
          :label="$t('Name')"
          prop="place_fullname"
        ></el-table-column>
        <el-table-column :label="$t('Address')" prop="location.info">
          <template slot-scope="scope">
            {{ scope.row.place_city }},&nbsp;
            {{ scope.row.place_street }},&nbsp;
            {{ scope.row.place_post_code }},&nbsp;
            {{ scope.row.place_house_number }}
          </template>
        </el-table-column>
      </el-table>
      <el-row style="margin-top: 20px">
        <el-col :span="3">
          <span>{{ $t("selected") }}:</span>
          <span>{{ selected.length || 0 }}</span>
          <span>{{ $t("item") }}</span>
        </el-col>
        <el-col :span="2" :offset="16">
          <el-button @click="dialogVisible = false">{{
            $t("Cancel")
          }}</el-button>
        </el-col>
        <el-col :span="2" :offset="1">
          <el-button :disabled="isOver" @click="handlerSubmit">{{
            $t("Confirm")
          }}</el-button>
        </el-col>
      </el-row>
    </el-dialog>

    <el-dialog
      :title="$t('Changeroute')"
      width="60%"
      :visible.sync="replaceDlg"
    >
      <el-form ref="line_form" :model="line_form" label-width="160px">
        <el-form-item label=" ">
          <el-radio-group v-model="line_form.line_id">
            <el-radio
              v-for="(item, index) in allLine"
              :key="index"
              :label="item.id"
              @change="changeTourNo"
              >{{ item.name }}</el-radio
            >
          </el-radio-group>
        </el-form-item>
        <el-form-item :label="$t('date')">
          <el-date-picker
            size="small"
            @change="getLineList"
            v-model="line_form.execution_date"
            value-format="yyyy-MM-dd"
            type="date"
            :picker-options="pickerOptions"
          >
          </el-date-picker>
        </el-form-item>
        <el-form-item
          :label="$t('routeOptions')"
          v-if="line_form.execution_date"
        >
          <el-select v-model="line_form.tour_no" v-if="optionalLine.length">
            <el-option
              v-for="item in optionalLine"
              :key="item.id"
              :label="item.line_name"
              :value="item.tour_no"
            >
            </el-option>
          </el-select>
          <div v-else>{{ $t("autoDistr") }}</div>
        </el-form-item>
      </el-form>
      <el-row>
        <el-col :span="2" :offset="17">
          <el-button @click="replaceDlg = false">{{ $t("Cancel") }}</el-button>
        </el-col>
        <el-col :span="2" :offset="1">
          <el-button @click="onSubmitReplaceLine" type="primary">{{
            $t("Submit")
          }}</el-button>
        </el-col>
      </el-row>
    </el-dialog>
    <el-dialog
      class="redistribution-dialog"
      :title="$t('SelectLineRedistribution')"
      @close="closeRedistributionDialog"
      :visible.sync="redistribution_show"
    >
      <div class="redistribution-row">
        <!-- <div>
            <el-table
 max-height="600px"              @selection-change="handleRDBSelectionChange"
              border
              max-height="380"
              :data="options"
            >
              <el-table-column
              type="selection"
              width="50"
              ></el-table-column>
              <el-table-column label="姓名" prop="place_fullname"></el-table-column>
              <el-table-column label="城市" prop="place_city"></el-table-column>
              <el-table-column label="详细地址" prop="place_address"></el-table-column>
              <el-table-column label="邮编" prop="place_post_code"></el-table-column>
            </el-table>
          </div> -->
        <div class="right-col">
          <div>
            <el-date-picker
              size="small"
              v-model="rbd_execution_date"
              @change="getLineListByDate"
              value-format="yyyy-MM-dd"
              type="date"
              :picker-options="pickerOptionsNoLimit"
            >
            </el-date-picker>
          </div>
          <div v-if="rbd_execution_date">
            <el-radio-group v-model="rbd_line_id" @change="getListJoinByLineId">
              <el-radio
                v-for="(item, index) in allLine"
                :key="index"
                :label="item.id"
                >{{ item.name }}</el-radio
              >
            </el-radio-group>
          </div>
          <div v-if="rbd_execution_date">
            <el-select
              v-model="rbd_tour_no"
              :placeholder="$t('pleaseSelectPickupRoute')"
            >
              <el-option
                v-for="(item, index) in lineCodeList"
                :key="index"
                :label="item.tour_no"
                :value="item.tour_no"
              ></el-option>
            </el-select>
          </div>
          <!-- 将选中站点生成另外的新线路 -->
          <!-- <div>
            <el-radio v-model="remove_site" :label="true">{{
              $t("GenerateSelectedStation")
            }}</el-radio>
          </div> -->
          <!-- 站点重线路中移除，等待重新分配 -->
          <div>
            <el-radio v-model="remove_site" :label="true">{{
              $t("RemoveSssignment")
            }}</el-radio>
          </div>
          <div>
            <el-button @click="redistribution_show = false">{{
              $t("Cancel")
            }}</el-button>
            <el-button @click="rbdSubmit" type="primary">{{
              $t("Submit")
            }}</el-button>
          </div>
        </div>
      </div>
    </el-dialog>
    <iframe frameborder="0" name="downExcel" style="display: none"></iframe>
  </div>
</template>

<script>
import draggable from "vuedraggable";
import { gmapApi } from "vue2-google-maps";
import BasePagination from "@/components/BasePagination/BasePagination";

export default {
  name: "IntelligentDispatch",
  components: {
    draggable,
    BasePagination,
  },
  data() {
    return {
      pageInfo: {
        // 页面信息
        line_id: this.$route.query.id || null,
      },
      loadOnlyOnce: false, // 只加载一次
      // 更换线路功能
      line_form: {
        id: "",
        execution_date: "",
        tour_no: "",
        line_id: null,
      },
      line_data: {}, // 线路总数据
      replaceDlg: false, // 弹窗
      allLine: [], // 全部线路
      tourDate: [], // 可选日期
      nowDate: Date.now() - 24 * 60 * 60 * 1000,
      optionalLine: [],

      no_show_road: true, // 查看路径show
      showDesc: false,

      location_info: "", // 地址信息
      next_code: "", // 下一地点的 code

      tour_no: "", // 线路编号

      currentDriverLocation: {
        latitude: "",
        longitude: "",
      },
      disabled: false,
      line: "", // 线路 code
      dialogVisible: false,

      center: {
        // 地图中心初始化
        lat: 52.37306,
        lng: 4.893,
        zoom: 5,
      },
      map: null,
      ckIcon: require("../../../assets/img/ck.png"),

      // google信息弹窗
      infoContent: "", // 信息弹窗内容
      infoWindowPos: {
        // 信息弹窗位置
        lat: 0,
        lng: 0,
      },
      infoWinOpen: false, // 信息弹窗开启变量
      infoOptions: {
        // 信息弹窗配置

        currentMidx: null,
        pixelOffset: {
          width: 0,
          height: -35,
        },
      },
      markers: [], // marker标记集合

      driver_location: null, // 司机位置
      warehouse_location: null, // 仓库位置

      driver_instance: null, // 司机实例
      options: [], // 缓存地址列表

      currentPosition: {
        lat: 1.38,
        lng: 103.8,
        zoom: 15,
      }, // 当前地图位置

      checkStatus: true,
      switchText: false,
      headerClose: false,

      selected: [], // 选择的数量

      redistribution_show: false, // 重新分配，弹窗
      rbd_line_id: null,
      rbd_site_ids: null,
      rbd_site_id: null,
      rbd_execution_date: null,
      rbd_tour_no: null,
      remove_site: false,
      oldSortId: null,
      lineCodeList: [],

      checked: false,
      showSiteInfo: false,

      //  全局加载动图
      overallSituationLoading: null,

      upload: true,

      pageParams: {
        begin_date: "",
        end_date: "",
        is_dispatch: 2,
        key_word: "",
        total: 0,
        page: 1,
        per_page: 20,
      },
      lineList: [],
      isHiddenPanel: false,
      search_date: ["", ""],
      country: JSON.parse(localStorage.getItem("country")),
    };
  },
  computed: {
    google: gmapApi,

    isWrapperClose() {
      return this.switchText !== false;
    },

    isWrapperHeaderClose() {
      return this.headerClose !== false;
    },

    isOver() {
      return this.selected.length === 0;
    },
    isFinshedLine() {
      return this.$route.query.status === 5;
    },
    pickerOptions() {
      // 更换线路--日期筛选
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
    pickerOptionsNoLimit() {
      // 更换线路--日期筛选
      return {
        disabledDate(time) {
          return time.getTime() < Date.now() - 8.64e7;
        },
      };
    },
    checkItem() {
      let list = this.options.filter((el) => {
        return el.ischeck;
      });
      return list.length;
    },
  },
  watch: {
    isWrapperHeaderClose(value) {
      if (value) {
        setTimeout(() => {
          this.showDesc = true;
        }, 310);
        return;
      }
      this.showDesc = false;
    },
    remove_site: {
      handler(val) {
        if (val) {
          this.rbd_line_id = null;
        }
      },
    },
    rbd_line_id: {
      handler(val) {
        if (val) {
          this.remove_site = null;
        }
      },
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
        this.handleClickFilterLine();
      },
    },
  },
  filters: {
    addKM(value) {
      // eslint-disable-next-line
      if (!!value) {
        // eslint-disable-next-line
        value = value / 1000;
        return value.toFixed(2) + "km";
        // eslint-disable-next-line
      } else {
        return 0 + "km";
      }
      // eslint-disable-next-line no-unreachable
      return "";
    },
  },
  activated() {
    if (this.$route.query.tour_no) {
      this.pageParams.key_word = this.$route.query.tour_no;
      this.pageParams.is_dispatch = this.$route.query.is_dispatch;

      this.pageInfo.line_id = this.$route.query.id;
      this.getDate(this.$route.query.id);
      this.getLines();
    }
  },
  created() {
    this.center.zoom = this.$store.state.config.zoom || 9;
    this.getLines();
  },
  mounted() {
    this.locationToCouncry(this.country.en, this.country.en);
  },
  methods: {
    locationToCouncry(country, adress) {
      // 定位到对应国家
      if (!country) return;
      this.$refs.gmap.$mapPromise.then((map) => {
        let geocoder = new google.maps.Geocoder();
        var that = this;
        geocoder.geocode(
          {
            address: adress,
            componentRestrictions: {
              country: country,
            },
          },
          function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
              if (results[0]) {
                console.log(
                  results[0].geometry.location,
                  results[0].formatted_address,
                  results
                );
                map.setCenter(results[0].geometry.location);
                results[0].address_components[0].types.forEach((el) => {
                  if (el === "country") {
                    // map.setZoom(8);
                  } else if (el === "administrative_area_level_1") {
                    map.setZoom(9);
                  } else if (el === "locality") {
                    map.setZoom(10);
                  } else if (el === "sublocality") {
                    map.setZoom(11);
                  }
                });

                // let marker = new google.maps.Marker({
                //   position: results[0].geometry.location,
                //   map: map,
                // });
              }
            } else {
              // alert("Geocoder failed due to: " + status);
            }
          }
        );
      });
    },
    checkMap() {
      // google地图加载函数
      if (this.loadOnlyOnce) return;
      this.loadOnlyOnce = true;
      if (!!this.pageInfo.line_id) {
        this.getDate(this.pageInfo.line_id);
      }
    },
    // 更换线路功能
    openChangeLineDialog(id) {
      // 打开修改弹窗
      this.replaceDlg = !this.replaceDlg;
      this.line_form.id = id;
      this.getAllLine();
    },
    // 更换线路功能--获取所有线路
    getAllLine() {
      this.$api.getAllLine().then((res) => {
        if (!res) return false;
        this.allLine = res.data.data;
      });
    },
    // 更换线路功能--改变线路id
    changeTourNo(val) {
      this.getLineDate({ id: this.line_form.id, line_id: val });
    },
    // 更换线路功能--获取可选日期
    getLineDate(data) {
      this.$api.getLineDate(data).then((res) => {
        this.tourDate = res.data;
      });
    },
    // 更换线路功能--拉取线路
    getLineList() {
      if (!this.line_form.execution_date) return;
      this.$api
        .getDistributableLine(this.line_form.id, this.line_form)
        .then((res) => {
          if (!res) return false;
          this.optionalLine = res.data;
        });
    },
    // 更换线路功能--修改线路
    onSubmitReplaceLine() {
      this.$refs.line_form.validate((valid) => {
        if (!valid) return;
        this.$api.replaceLine(this.line_form.id, this.line_form).then((res) => {
          if (!res) return;
          this.LineList(this.pageInfo.line_id);
          this.replaceDlg = !this.replaceDlg;
        });
      });
    },

    // 站点移除功能
    // handlerRemoveClick(id) {
    //   this.$confirm('确定移除站点?', '提示', {
    //     confirmButtonText: '确定',
    //     cancelButtonText: '取消',
    //     type: 'warning',
    //   }).then(() => {
    //     this.$api.stationRemove(id).then((res) => {
    //       if (!res) return;
    //       this.$message({
    //         type: 'success',
    //         message: '删除成功!',
    //       });
    //       this.getDate(this.$route.query.id);
    //     });
    //   });
    // },
    // 站点移除功能

    // 绘制路线
    renderWaypoints(
      origin = "", // 其实位置
      destination = "", // 目的地位置
      travelMode = "DRIVING", // 驾驶模式
      waypoints = [], // 途径点
      optimizeWaypoints = false // 优化沿途点
    ) {
      this.$refs.gmap.$mapPromise.then((map) => {
        // eslint-disable-next-line
        const directionsService = new google.maps.DirectionsService();
        // eslint-disable-next-line
        const directionsRenderer = new google.maps.DirectionsRenderer({
          suppressMarkers: true, // 去掉起点、终点的坐标图标
          polylineOptions: {
            strokeColor: "#6d93dc",
            strokeWeight: 5,
          },
        });

        const request = {
          origin,
          destination,
          travelMode: travelMode || "DRIVING",
          waypoints,
          optimizeWaypoints,
        };

        console.log("begin request google", request);

        directionsService.route(request, (result, status) => {
          console.log("get reponse", result, status);
          if (status === "OK") {
            directionsRenderer.setDirections(result);
            directionsRenderer.setMap(map);
            this.no_show_road = false; // 查询成功
          } else {
            // eslint-disable-next-line no-alert
            // alert('存在错误地址, 查询失败');
            this.$store.commit("config/refreshRouter", Math.random()); // 刷新当前路由
            directionsRenderer.setDirections(null);
          }
        });
      });
    },

    getLatLng(lat, lng) {
      // eslint-disable-next-line
      return new google.maps.LatLng(lat, lng);
    },

    handlerSubmit() {
      // 取消查看路径
      if (!this.selected.length) {
        this.$store.commit("config/refreshRouter", Math.random());
        return;
      }
      const startPoint = null;
      const endPoint = null;

      //仓库位置
      const warehouseWaypoint = {
        location: this.getLatLng(
          +this.line_data.warehouse_lat,
          +this.line_data.warehouse_lon
        ),
      };
      //途径的点
      var currentWaypoints = [];
      // const currentWaypoints = [{ // 司机位置
      //   location: this.getLatLng(
      //     +this.line_data.warehouse_lat,
      //     +this.line_data.warehouse_lon,
      //   ),
      //   stopover: false,
      // }];

      this.selected.forEach((item) => {
        currentWaypoints.push({
          location: this.getLatLng(+item.place_lat, +item.place_lon),
        });
      });

      const maxNum = 25;
      var lastWaypoint = {};
      for (var i = 0; i < currentWaypoints.length; i += maxNum) {
        var newWaypoints = currentWaypoints.slice(i, i + maxNum);

        var beginWaypoint = newWaypoints[0];
        var endWaypoint = newWaypoints[newWaypoints.length - 1];
        //如果是第一次运行， 途径站点，不包括最后一个站点
        var finalWaypoints = newWaypoints.slice(0, newWaypoints.length - 1);

        if (i == 0) {
          beginWaypoint = warehouseWaypoint;
        } else {
          beginWaypoint = lastWaypoint;
        }
        if (i + maxNum > currentWaypoints.length) {
          endWaypoint = warehouseWaypoint;
          finalWaypoints = newWaypoints;

          console.log("set endWaypoint,  i+maxNum", endWaypoint, i + maxNum);
        }

        console.log("beginWaypoint", beginWaypoint);
        console.log("waypoints", finalWaypoints);
        console.log("endWaypoint", endWaypoint);
        this.renderWaypoints(
          beginWaypoint,
          endWaypoint,
          "DRIVING",
          finalWaypoints,
          false
        ); // render
        lastWaypoint = endWaypoint;
      }

      this.selected = [];
      this.dialogVisible = false;
    },

    handleSelectionChange(val) {
      // 选择站点
      this.selected = val;

      // if (val.length && val.length > 250) {
      //   // eslint-disable-next-lin
      //   this.$message({
      //     type: 'error',
      //     message: '选择数量不超过 25个',
      //   });
      // }
    },

    handlerWapPoints() {
      //  查看路径开启弹窗
      this.dialogVisible = true;
    },

    handlerOnceOptimization() {
      // 一键优化
      if (!this.tour_no) {
        this.$message({
          type: "error",
          message: this.$t("PleaseSelectTheRoute") + "!",
        });
        return;
      }
      this.overallSituationLoading = this.$loading({
        lock: true,
        text: this.$t("Optimizing"),
        spinner: "el-icon-loading",
        background: "rgba(0, 0, 0, 0.7)",
      });
      this.$api
        .OnceOptimization({
          tour_no: this.tour_no,
        })
        .then((res) => {
          // this.$store.commit('config/refreshRouter', Math.random()); // 刷新当前路由
          this.overallSituationLoading.close();
          this.getDate(this.pageInfo.line_id);
          this.getLines();
        })
        .catch((e) => {
          this.overallSituationLoading.close();
        });
    },

    handlerOptimization() {
      // 手动优化
      if (!this.tour_no) {
        this.$message({
          type: "error",
          message: this.$t("PleaseSelectTheRoute") + "!",
        });
        return;
      }
      const locationIds = [];
      this.options.forEach((item) => {
        locationIds.push(item.id);
      });
      this.overallSituationLoading = this.$loading({
        lock: true,
        text: this.$t("Optimizing"),
        spinner: "el-icon-loading",
        background: "rgba(0, 0, 0, 0.7)",
      });
      this.$api
        .handlerOptimization({
          tour_no: this.tour_no,
          batch_ids: locationIds,
        })
        .then(() => {
          // this.$store.commit('config/refreshRouter', Math.random()); // 刷新当前路由
          this.overallSituationLoading.close();
          this.getDate(this.pageInfo.line_id);
          this.getLines();
        })
        .catch((e) => {
          this.overallSituationLoading.close();
        });
    },

    handlerClickItem(item) {
      // 点击右侧信息块
      this.currentPosition = {
        lat: +item.place_lat,
        lng: +item.place_lon,
        zoom: 12,
      };
      const info = `
          ${item.place_fullname}<br>
          ${item.place_street},
          ${item.place_house_number},
          ${item.place_city},
          ${item.place_post_code},
          ${item.place_country},
      `;
      const that = this;
      this.$refs.gmap.$mapPromise.then((map) => {
        map.panTo(this.currentPosition); // 设置地图中心
        map.setZoom(this.currentPosition.zoom); // 设置层级
        this.infoWindowPos = {
          lat: this.currentPosition.lat,
          lng: this.currentPosition.lng,
        }; // 设置 信息窗位置
        this.infoWinOpen = true; // 开启 信息窗
        this.infoContent = this.getInfoWindowContent(info); // 设置 信息窗内容
        setTimeout(() => {
          document.getElementById("card").onclick = function (event) {
            console.log(event, "ev", item);
            that.$router.push({
              name: "stationDetail",
              query: {
                id: item.id,
              },
            });
          };
        }, 100);
      });
    },

    handlerCloseHeaderDesc() {
      this.headerClose = !this.headerClose;
    },

    handleSwitchClick() {
      this.switchText = !this.switchText;
    },

    getMarkerLatLng(e) {
      this.location_info = `${e.latLng.lat()}, ${e.latLng.lng()}`;
      this.currentDriverLocation = {
        latitude: `${e.latLng.lat()}`,
        longitude: `${e.latLng.lng()}`,
      };
    },

    initTempDate(options) {
      const tempArray = [];
      options.forEach((item, index) => {
        // 处理地图上的所有 marker
        const pointer = {
          position: {
            lat: +item.place_lat,
            lng: +item.place_lon,
          },
          icon: null,
          description: `${
            item["sort_id"] && item.sort_id !== 1000 ? item.sort_id : index + 1
          }`,
          data: item,
        };
        tempArray.push(pointer);
      });
      console.log("tempArray");
      console.log(tempArray);
      return tempArray;
    },

    lonAndLatToGeographicalLocation(position, markerData, idx) {
      // 仓库经纬度转地理位置
      let geocoder = new google.maps.Geocoder();
      // var that = this;
      const that = this;

      geocoder.geocode(
        {
          location: position,
        },
        function (results, status) {
          if (status == google.maps.GeocoderStatus.OK) {
            const data = {};
            for (const item of results[0].address_components) {
              const key = item.types[0];
              const value = item.short_name;
              if (
                key === "street_number" ||
                key === "country" ||
                key === "postal_code" ||
                key === "postal_code_prefix" ||
                key === "locality" ||
                key === "route"
              ) {
                data[key] = value;

                if (key === "country") {
                  data.country_name = item.short_name;
                }
                if (key === "postal_code_prefix") {
                  data.postal_code = value;
                }
                if (key === "locality") {
                  data.city = value;
                }
                if (key === "route") {
                  data.route = value;
                }
              }
            }
            data.address =
              data["city"] +
              "," +
              data["route"] +
              "," +
              data["postal_code"] +
              "," +
              data["street_number"];
            that.infoContent = that.getInfoWindowContent(data.address);
          } else {
            // alert("Geocoder failed due to: " + status);
          }
          if (+that.currentMidx === +idx) {
            that.infoWinOpen = !that.infoWinOpen;
          } else {
            that.infoWinOpen = true;
            that.currentMidx = idx;
          } // 开关弹窗
          setTimeout(() => {
            document.getElementById("card").onclick = function (event) {
              console.log(event, "ev", markerData);
              that.$router.push({
                name: "stationDetail",
                query: {
                  id: markerData.data.id,
                },
              });
            };
          }, 100);
        }
      );
    },

    toggleInfoWindow(marker, idx) {
      // 点击地图坐标点
      let addressInfo = "";
      this.infoWindowPos = marker.position;
      console.log(marker, "markerrrrr", idx);
      if (marker.data) {
        addressInfo = `
          ${marker.data.place_fullname}<br>
          ${marker.data.place_street},
          ${marker.data.place_house_number},
          ${marker.data.place_city},
          ${marker.data.place_post_code},
          ${marker.data.place_country},
        `;
        this.infoContent = this.getInfoWindowContent(addressInfo);
        if (+this.currentMidx === +idx) {
          this.infoWinOpen = !this.infoWinOpen;
        } else {
          this.infoWinOpen = true;
          this.currentMidx = idx;
        } // 开关弹窗
        const that = this;
        setTimeout(() => {
          document.getElementById("card").onclick = function (event) {
            console.log(event, "ev", marker);
            that.$router.push({
              name: "stationDetail",
              query: {
                id: marker.data.id,
              },
            });
          };
        }, 100);
      } else {
        this.lonAndLatToGeographicalLocation(
          this.getLatLng(marker.position.lat, marker.position.lng),
          marker.data
        );
      }
    },

    getInfoWindowContent(description) {
      console.log("description");
      console.log(description);
      return `<div id="card">
          ${description}
        </div>`;
    },

    handlerInitOrder() {
      this.getDate(this.pageInfo.line_id);
    },

    addMarker(data, iconUrl, isDraggable, changeLat) {
      return {
        position: {
          lat: +data.latitude - changeLat,
          lng: +data.longitude,
        },
        description: " ",
        draggable: isDraggable || false,
        icon: iconUrl,
      };
    },

    LineList(id) {
      this.$api.getLineDetail(id).then((res) => {
        if (!res) {
          this.options = [];
          this.markers = [];
          this.infoWinOpen = false;
          this.pageParams.key_word = "";
          this.getLines();
        } else {
          this.options = res.data.batchs; // 站点列表
          this.options.forEach((el) => {
            el.ischeck = false;
          });
          this.infoWinOpen = false;
          this.getDate(this.pageInfo.line_id);
          this.getLines();
        }
      });
    },
    async getDate(id) {
      // 数据初始化
      if (!id) {
        this.$message({
          type: "error",
          message: this.$t("PleaseSelectTheRoute") + "!",
        });
        return;
      }
      const res = await this.$api.getLineDetail(id);
      this.pageParams.is_dispatch =
        res.data.is_dispatch || this.pageParams.is_dispatch;
      this.line_data = res.data;
      res.data.batchs.forEach((el, index) => {
        this.$set(res.data.batchs, index, {
          ...res.data.batchs[index],
          ischeck: false,
        });
      });
      this.options = res.data.batchs; // 站点列表
      console.log("this.options");
      console.log(this.options);
      this.tour_no = res.data.tour_no; // 取件线路编号

      // this.driver_location = res.data.driver_location; // 司机位置
      this.warehouse_location = {
        latitude: +res.data.warehouse_lat,
        longitude: +res.data.warehouse_lon,
      }; // 仓库位置

      this.markers = this.initTempDate(this.options); // 生成 marker
      this.markers.push(
        this.addMarker(this.warehouse_location, this.ckIcon, false, 0)
      );
    },

    // 重新分配--开始
    handlerRedistribution() {
      let list = this.options.filter((el) => {
        return el.ischeck;
      });
      let data = list.map((el) => {
        return el.id;
      });
      if (data.length === 0) {
        this.$message({
          type: "error",
          message: this.$t("PleaseSelectSite") + "!",
        });
        return;
      }
      this.rbd_site_id = data[0];
      this.rbd_site_ids = data.join(",");
      this.redistribution_show = true;
    },

    // 重新分配--弹窗关闭
    closeRedistributionDialog() {
      this.rbd_execution_date = null;
      this.rbd_line_id = null;
      this.rbd_tour_no = null;
      this.remove_site = false;
      this.lineCodeList = [];
      this.allLine = [];
    },

    // 重新分配--选择日期
    getLineListByDate() {
      this.rbd_line_id = null;
      this.rbd_tour_no = null;
      this.lineCodeList = [];
      this.allLine = [];
      this.$api
        .getListByDate({
          date: this.rbd_execution_date,
        })
        .then((res) => {
          if (!res) return false;
          this.allLine = res.data;
        });
    },

    handleRDBSelectionChange(select) {
      let arr = select.map((item) => {
        return item.id;
      });
      this.rbd_site_id = arr[0];
      this.rbd_site_ids = arr.join(",");
      console.log(this.rbd_site_ids);
      if (select.length <= 1) {
        this.getListJoinByLineId();
      }
    },

    getListJoinByLineId() {
      if (!(this.rbd_site_id && this.rbd_line_id)) {
        // this.getLineDate({id: this.rbd_site_id, line_id: this.rbd_line_id})
        return;
      }
      this.rbd_tour_no = null;
      this.lineCodeList = [];
      this.$api
        .getListJoinByLineId({
          line_id: this.rbd_line_id,
          execution_date: this.rbd_execution_date,
        })
        .then((res) => {
          this.lineCodeList = res.data;
          this.rbd_tour_no = null;
        });
    },

    rbdSubmit() {
      if (this.remove_site) {
        this.removeListFromTour();
      } else {
        this.putAssignListTour();
      }
    },

    putAssignListTour() {
      if (!this.rbd_site_ids) {
        this.$message({
          type: "error",
          message: this.$t("PleaseSelectSite") + "!",
        });
        return;
      }
      if (!this.rbd_line_id) {
        this.$message({
          type: "error",
          message: this.$t("PleaseSelectTheRoute") + "!",
        });
        return;
      }
      if (!this.rbd_execution_date) {
        this.$message({
          type: "error",
          message: this.$t("Pleaseselectdistributiondate") + "!",
        });
        return;
      }
      if (!this.rbd_tour_no) {
        this.$message({
          type: "error",
          message: this.$t("PleaseRouteNumber") + "!",
        });
        return;
      }
      let data = {
        id_list: this.rbd_site_ids,
        execution_date: this.rbd_execution_date,
        line_id: this.rbd_line_id,
        tour_no: this.rbd_tour_no,
      };
      this.$api.putAssignListTour(data).then((res) => {
        this.redistribution_show = false;
        this.LineList(this.pageInfo.line_id);
        // this.LineList();
        // this.$store.commit("config/refreshRouter", Math.random()); // 刷新当前路由
      });
    },

    removeListFromTour() {
      if (!this.rbd_site_ids) {
        this.$message({
          type: "error",
          message: this.$t("PleaseSelectSite") + "!",
        });
        return;
      }
      let data = {
        id_list: this.rbd_site_ids,
      };
      this.$api.removeListFromTour(this.rbd_site_ids).then((res) => {
        this.redistribution_show = false;
        this.LineList(this.pageInfo.line_id);
        // this.LineList();
        // this.$store.commit("config/refreshRouter", Math.random()); // 刷新当前路由
      });
    },
    // 重新分配--结束

    antitone() {
      // 反序
      let list = this.options.filter((el) => {
        return el.ischeck;
      });
      if (list.length === 0) {
        this.options.reverse();
        this.handlerOptimization();
      } else {
        // 部分反序
        list.reverse();

        let j = 0;
        for (let i = 0; i < this.options.length; i++) {
          if (this.options[i].ischeck) {
            this.$set(this.options, i, list[j]);
            j++;
          }
        }
        this.handlerOptimization();
      }
    },

    zoomChanged(e) {
      this.$store.commit("config/setZoom", e); // 存储当前缩放层次
    },

    openSort(val) {
      console.log("asd");
      if (val.sort_id && val.sort_id !== 1000) {
        val.show = true;
        this.oldSortId = val.sort_id;

        this.upload = false; // 刷新组件
        this.$nextTick(() => {
          this.upload = true;
        });
      }
    },

    editSort(val) {
      // 修改面板sort
      // 预设原数组是按从小到大排序的
      this.options.forEach((el) => {
        if (this.oldSortId - val.sort_id < 0) {
          // 旧值小的情况
          if (el.sort_id > this.oldSortId) {
            if (
              val.place_fullname !== el.place_fullname &&
              el.sort_id <= val.sort_id
            ) {
              console.log(el.place_fullname, el.sort_id);
              el.sort_id--;
            }
          }
        } else if (this.oldSortId - val.sort_id > 0) {
          // 旧值大的情况
          if (el.sort_id < this.oldSortId) {
            if (
              val.place_fullname !== el.place_fullname &&
              el.sort_id >= val.sort_id
            ) {
              console.log(el.place_fullname, el.sort_id);
              el.sort_id++;
            }
          }
        }
      });
      this.options.sort((a, b) => {
        return a.sort_id - b.sort_id;
      });
      this.handlerOptimization(); // 手动优化
      val.show = false;
    },

    getTourExcel() {
      // 导出计划
      if (!this.pageInfo.line_id) {
        this.$message({
          type: "error",
          message: this.$t("PleaseSelectTheRoute"),
        });
        return;
      }
      this.$api.getPlanExcel(this.pageInfo.line_id).then((res) => {
        let iframe = window.frames["downExcel"];
        let href = res.data.path; //接口路径地址，返回数据类型为application/binary，后台控制显示信息，前端仅为下载功能
        iframe.location.href = href;
      });
    },

    selectAll() {
      // 全选
      let isAll = this.options.every((el) => {
        return el.ischeck;
      });
      if (isAll) {
        this.options.forEach((el, index) => {
          el.ischeck = !el.ischeck;
        });
      } else {
        this.options.forEach((el, index) => {
          el.ischeck = true;
        });
      }
      console.log("asd", this.options);
    },

    selectReverse() {
      // 反选
      this.options.forEach((el, index) => {
        el.ischeck = !el.ischeck;
      });
    },

    dddemo(e) {
      console.log(e, "end");
      let checkArr = this.options.filter((el, index) => {
        // 过滤掉未被选择的元素
        return el.ischeck;
      });
      if (checkArr.length < 2) return; // 不是整体拖拽就return

      let aftUnCheck = []; // 之前且未选中
      let aftCheck = [];
      let befUnCheck = []; // 之后且未选中
      let befCheck = [];
      let sltel = []; // 当前拖拽的元素

      let upOrDown = e.newIndex - e.oldIndex; // 负数为上移， 正数为下移

      console.log(
        "aftuncheck:",
        aftUnCheck,
        "aftCheck:",
        aftCheck,
        "befUnCheck:",
        befUnCheck,
        "befCheck:",
        befCheck,
        "sltel:",
        sltel
      );

      this.options.forEach((el, index) => {
        if (upOrDown > 0) {
          // 下移的情况
          if (el.ischeck) {
            if (index == e.newIndex) {
              sltel.push(el);
            } else if (index < e.oldIndex) {
              aftCheck.push(el);
            } else {
              befCheck.push(el);
            }
          } else {
            if (index < e.newIndex) {
              aftUnCheck.push(el);
            } else {
              befUnCheck.push(el);
            }
          }
        } else if (upOrDown < 0) {
          // 上移的情况
          if (el.ischeck) {
            if (index == e.newIndex) {
              sltel.push(el);
            } else if (index > e.oldIndex) {
              befCheck.push(el);
            } else {
              aftCheck.push(el);
            }
          } else {
            if (index < e.newIndex) {
              aftUnCheck.push(el);
            } else {
              befUnCheck.push(el);
            }
          }
        } else {
          // 原地传送
          if (el.ischeck) {
            if (index == e.newIndex) {
              sltel.push(el);
            } else if (index > e.oldIndex) {
              befCheck.push(el);
            } else {
              aftCheck.push(el);
            }
          } else {
            if (index < e.newIndex) {
              aftUnCheck.push(el);
            } else {
              befUnCheck.push(el);
            }
          }
        }
      });

      this.options = [
        ...aftUnCheck,
        ...aftCheck,
        ...sltel,
        ...befCheck,
        ...befUnCheck,
      ]; // 拼接

      this.options.forEach((el) => {
        console.log(el.ischeck);
        el.ischeck = false; // 取消选中效果
      });
    },

    getLinesParams(val) {
      this.pageParams.is_dispatch = val;
      this.getLines();
    },

    hiddenLeftPanel() {
      this.isHiddenPanel = !this.isHiddenPanel;
    },

    handleClickFilterLine() {
      this.pageParams.page = 1;
      this.getLines();
    },

    getLines() {
      this.$api.getLines(this.pageParams).then((res) => {
        this.lineList = res.data.data;
        this.pageParams.total = res.data.meta.total;
      });
    },

    handleRowClick(row) {
      this.pageInfo.line_id = row.id;
      this.getDate(this.pageInfo.line_id);
    },

    tableRowClassName({ row, rowIndex }) {
      if (row.id == this.pageInfo.line_id) {
        return "check-row";
      } else {
        return "";
      }
    },
  },
};
</script>

<style lang="less">
.intelligent-dispatch {
  .googleMap {
    position: relative;
    width: 100%;
    .left_panel_btn {
      position: absolute;
      left: 0;
      top: 0;
      width: 350px;
      display: flex;
      z-index: 5;
      background: #fff;
      // padding: 5px;
      justify-content: space-between;
      transition: width 0.3s ease-in-out;
      .el-button {
        width: 100%;
      }
      .iconfont {
        font-size: 20px;
        display: inline-block;
      }
      .checkStasus {
        background: rgb(255, 204, 70);
        color: #fff;
        font-size: 14px;
      }
    }
    .hidden_left_panel_btn {
      width: 30px !important;
      display: inline-block;
    }
    .left_panel {
      position: absolute;
      left: 0;
      width: 350px;
      transition: width 0.3s ease-in-out;
      height: calc(100vh - 155px);
      top: 39px;
      z-index: 5;
      background: #fff;
      overflow: auto;
      .left_panel_input {
        display: flex;
        flex-direction: column;
        margin: 10px 0;
        padding: 3px;
        .show_btn {
          position: relative;
          right: 60px;
        }
      }
    }
    .hidden_left_panel {
      width: 0;
      transition: width 0.3s ease-in-out;
    }
    // .show_left_panel {
    //   position: absolute;
    //   top: 50%;
    //   left: 0px;
    //   height: 40px;
    //   width: 16px;
    //   // margin-left: -20px;
    //   // margin-top: -10px;
    //   padding: 2px;
    //   background: #53D9A9;
    //   color: #fff;
    //   cursor: pointer;
    //   border-top-right-radius: 10px;
    //   border-bottom-right-radius: 10px;
    //   z-index: 5;
    // }
  }

  .control_panel {
    position: absolute;
    top: 10px;
    left: 50%;
    width: 340px;
    height: 40px;
    margin-left: -150px;
    z-index: 5;
    display: flex;
    justify-content: space-around;
    align-items: center;

    font-size: 18px;
    cursor: pointer;
    color: #53d9a9;
    span {
      margin: 5px;
      white-space: nowrap;
      padding: 5px;
      background-color: #fff;
      border-radius: 2px;
      box-shadow: rgba(0, 0, 0, 0.3) 0px 1px 4px -1px;
    }
  }

  .updateDriverLocation {
    z-index: 5;
    position: absolute;
    margin-left: 10px;
    padding: 20px;
    top: 10px;
    left: 50%;
    width: 300px;
    height: 120px;
    background-color: #fff;
  }

  .wrapper {
    position: absolute;
    height: calc(100vh - 185px);
    width: 300px;
    transition: width 0.3s ease-in-out;
    top: 0;
    right: 0;
    z-index: 5;
    .station_desc {
      .station_text {
        font-size: 18px;
        margin-bottom: 5px;
      }
    }
    .wrapper_header {
      position: absolute;
      top: 10px;
      left: -7px;
      width: 100%;
      height: 48px;
      z-index: 999;
      background-color: #fff;
      box-sizing: border-box;
      padding: 12px 12px 0 12px;
      color: #53d9a9;
      transition: height 0.3s ease-in-out;
      border-radius: 4px;
      box-shadow: rgba(0, 0, 0, 0.3) 0px 1px 4px -1px;
      .wrapper_header_des {
        display: flex;
        justify-content: space-between;
        align-content: center;
        .wrapper_header_text {
          cursor: pointer;
        }
      }
    }
    .wrapper_header_close {
      height: 380px;
      transition: height 0.3s ease-in-out;
    }
    .wrapper_content {
      box-sizing: border-box;
      padding: 0 18px 6px 6px;
      position: relative;
      top: 70px;
      left: -7px;
      height: 100%;
      width: 100%;
      overflow: auto;
      background: #fff;
      color: #aaaaaa;
      overflow: hidden;
      .station-info {
        // height: 7%;
        box-sizing: border-box;
        text-align: center;
        color: #53d9a9;
        padding-top: 10px;
      }
      .list_selected {
        // height: 10%;
        box-sizing: border-box;
        border-radius: 5px;
        display: flex;
        justify-content: space-between;
        position: sticky;
        top: 0;
        width: 100%;
        // height: 54px;
        padding: 5px 8px 10px 8px;
        background-color: #fff;
        // line-height: 54px;
        font-size: 14px;
        color: #53d9a9;
        .text {
          cursor: pointer;
        }
      }
      .card {
        position: relative;
        box-sizing: border-box;
        width: 100%;
        word-wrap: wrap;
        background-color: #fff;
        margin: 4px;
        padding: 4px;
        border-radius: 4px;
        box-shadow: rgba(0, 0, 0, 0.3) 0px 1px 4px -1px;
        display: flex;
        flex-direction: column;
        cursor: pointer;
        &:hover {
          background-color: #fff !important;
          border: solid #53d9a9 2px !important;
          z-index: 1 !important;
        }
      }
      .serial-number {
        display: flex;
        align-items: center;
        // justify-content: space-around;
        .round-mark {
          display: inline-block;
          padding: 3px 7px;
          border-radius: 50%;
          background-color: #53d9a9;
          color: #000;
          // z-index: 99999;
          margin: 0 5px;
        }
        .card_content_check {
          padding: 3px 10px;
        }
      }
      .stationsList-group {
        height: calc(100vh - 300px);
        box-sizing: border-box;
        overflow-y: auto;
        overflow-x: hidden;
      }
      .control-site-information {
        width: 100%;
        // height: 7%;
        box-sizing: border-box;
        text-align: center;
        background-color: #fff;
        // margin: 30px 0 0;
        padding: 0px 0 10px;
        position: absolute;
        bottom: 0;
        // position: absolute;
        // bottom: 5px;
        span {
          display: inline-block;
          border: 1px #53d9a9 solid;
          color: #53d9a9;
          cursor: pointer;
        }
        .all-check {
          width: 15%;
        }
        .reverse-check {
          width: 20%;
        }
        .show-panel {
          width: 55%;
        }
      }
    }
    .wrapper_switch {
      position: absolute;
      top: 50%;
      height: 40px;
      width: 16px;
      margin-left: -20px;
      margin-top: -10px;
      padding: 2px;
      background: #53d9a9;
      color: #fff;
      cursor: pointer;
      border-top-left-radius: 10px;
      border-bottom-left-radius: 10px;
      i {
        font-size: 20px;
        line-height: 40px;
      }
    }

    .ghost {
      opacity: 0.5;
      color: #000;
      background: #53d9a9 !important;
    }

    .selected {
      background-color: #fff !important;
      border: solid #53d9a9 2px !important;
      z-index: 1 !important;
    }
  }
  .wrapper_close {
    width: 0;
    transition: width 0.3s ease-in-out;
  }

  .card_text {
    color: #000;
    font-size: 14px;
  }
  .desc_title {
    margin-bottom: 5px;
  }
  .desc_value {
    font-size: 14px;
    color: #585858;
    white-space: nowrap;
  }
  .card_hd {
    .hd_clear {
      position: absolute;
      top: 10px;
      right: 4px;
      display: flex;
      flex-direction: column;
      .card_item {
        display: inline-block;
        margin-bottom: 20px;
      }
    }
    .el-icon-circle-close {
      font-size: 16px;
    }
  }
  .card_content {
    display: flex;
    align-items: center;
    .card_content_check {
      padding: 3px 10px;
    }
  }
  #card {
    width: 150px;
    height: 80px;
    background-color: #fff;
    padding: 5px;
  }
  .check-row {
    background: rgb(255, 204, 70);
    color: #fff;
    &:hover {
      cursor: pointer !important;
    }
  }
}
</style>
<style lang="less">
.gmnoprint {
  display: none;
}
.intelligent-dispatch {
  .el-table--enable-row-hover .el-table__body tr:hover > td {
    background-color: rgb(255, 204, 70) !important;
    color: #fff !important;
    cursor: pointer !important;
  }
}
.redistribution-dialog {
  .redistribution-row {
    // display: flex;
    & > div {
      display: table-cell;
      width: 100%;
      height: 100%;
      // border: 2px solid #e3e3e3;
      padding: 0px 20px;
    }
    .right-col {
      max-height: 500px;
      div {
        margin: 20px 0;
      }
    }
  }
}
</style>

