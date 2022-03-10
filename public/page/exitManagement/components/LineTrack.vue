<template>
  <div class="line-track">
    <div class="map-container">
      <div class="container-top">
        <span @click="checkAll">{{ $t("viewAll") }}</span>
        <span @click="handlerSystemTrack">{{ $t("systemPlanningRoute") }}</span>
        <span @click="handlerRealTrack">{{ $t("actualTrajectory") }}</span>
      </div>

      <!-- 地图组件 -->
      <gmap-map
        ref="gmap"
        id="gmap"
        @idle="checkMap"
        :noClear="false"
        :center="center"
        :zoom="center.zoom"
        style="width: 100%; height: 100%"
      >
        <!-- 标示 -->
        <gmap-marker
          ref="marker"
          :key="index"
          v-for="(m, index) in markers"
          :icon="m.icon || null"
          :position="m.position"
          :draggable="m.draggable || false"
          @dragend="getMarkerLatLng($event)"
          :label="{
            text: `${m.description}`,
            color: 'white',
          }"
          @click="toggleInfoWindow(m, index)"
        >
        </gmap-marker>
        <!-- 标示 -->

        <!-- 信息弹窗 -->
        <gmap-info-window
          :options="infoOptions"
          :position="infoWindowPos"
          :opened="infoWinOpen"
          @closeclick="infoWinOpen = false"
        >
          <div v-html="infoContent"></div>
        </gmap-info-window>
        <!-- 信息弹窗 -->
      </gmap-map>
      <!-- 地图组件 -->

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
              >{{ lineData.tour_no }}&nbsp;&nbsp;{{ $t("Line") }}</span
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
                    {{ $t("Driver") }}:&nbsp;&nbsp;{{ lineData.driver_name }}
                  </div>
                  <div>
                    {{ $t("NumberPlate") }}:&nbsp;&nbsp;{{ lineData.car_no }}
                  </div>
                  <div>
                    {{ $t("vehicleMileage") }}:&nbsp;&nbsp;{{
                      distance | addKM
                    }}
                  </div>
                  <div>
                    {{ $t("totalTime") }}:&nbsp;&nbsp;{{
                      costTime || 0 + "min"
                    }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="wrapper_content">
          <div id="stationsList" v-if="!isWrapperClose">
            <draggable v-if="mode === 2" ghost-class="ghost" v-model="options">
              <transition-group>
                <div
                  class="card"
                  @click="handlerClickItem(item)"
                  v-for="(item, index) in options"
                  :key="item.id"
                >
                  <div class="card_hd">
                    <div class="hd_clear">
                      <i
                        class="el-icon-circle-close card_item"
                        @click.stop="handlerRemoveClick(item.id)"
                      ></i>
                      <i
                        v-if="item.status === 1 || item.status === 2"
                        class="el-icon-edit card_item"
                        @click.stop="replaceLine(item.id)"
                      ></i>
                    </div>
                  </div>
                  <div class="serial-number">
                    <span>{{
                      item["sort_id"] && item.sort_id !== 1000
                        ? item.sort_id
                        : index + 1
                    }}</span>
                  </div>
                  <div>
                    <span>{{ $t("Name") }}:</span>
                    <span class="card_text">{{ item.place_fullname }}</span>
                  </div>
                  <div>
                    <span>{{ $t("Address") }}:</span>
                    <span>
                      {{ item.place_address }}
                    </span>
                  </div>
                  <div>
                    <span>{{ $t("EstimatedDistance") }}:</span>
                    <span>{{ item.expect_distance | addKM }}</span>
                  </div>
                  <div>
                    <span>{{ $t("EstimatedArrivalTime") }}:</span>
                    <span>{{ item.expect_arrive_time }}</span>
                  </div>
                  <div>
                    <span>{{ $t("ActualArrivalTime") }}:</span>
                    <span>{{ item.actual_arrive_time }}</span>
                  </div>
                </div>
              </transition-group>
            </draggable>
            <div v-if="mode === 1 || mode === 3">
              <div
                :class="{ card_mode: true, card_mode_disabled: mode == 1 }"
                @click="handlerClickItem(item)"
                v-for="(item, index) in realOptios"
                :key="index"
              >
                <div class="station-panel">
                  <span class="serial-number">
                    <span v-if="index === 0">{{ "W" }}</span>
                    <span v-else>{{
                      item["sort_id"] && item.sort_id !== 1000
                        ? item.sort_id
                        : index
                    }}</span>
                  </span>
                  <span>
                    <div>{{ item.place_fullname }}</div>
                    <div>{{ item.place_address }}</div>
                  </span>
                </div>
                <div
                  class="station-panel"
                  v-for="(child, index) in item.event"
                  :key="index"
                >
                  <span>
                    <span class="car_icon"></span>
                  </span>
                  <span class="station-info">
                    <span class="station-content">{{ child.content }}</span>
                    <span>{{ child.created_at }}</span>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div
          class="wrapper_switch"
          v-if="mode === 3 && trackEventData.length !== 0"
          @click="handleSwitchClick"
        >
          {{ switchText ? $t("Expand") : $t("Hide") }}
        </div>
      </div>
    </div>

    <div class="line-track-tuglie">
      <div class="line-track-row">
        <div class="line-track-col">
          <span class="line-xt"></span>
          <span>{{ $t("systemPlanningRoute") }}</span>
        </div>
        <div class="line-track-col">
          <span class="not-arrived"></span>
          <span>{{ $t("missedSte") }}</span>
        </div>
      </div>
      <div class="line-track-row">
        <div class="line-track-col">
          <span class="line-sj"></span>
          <span>{{ $t("actualTrajectory") }}</span>
        </div>
        <div class="line-track-col">
          <span class="arrived"></span>
          <span>{{ $t("arrivedAtTheSite") }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import draggable from "vuedraggable";
import { gmapApi } from "vue2-google-maps";

export default {
  // name: "lineTrack",
  components: {
    draggable,
  },
  data() {
    return {
      loadOnlyOnce: false, // 只加载一次
      distance: "",
      costTime: "",
      realOptios: [],
      isSortStation: true, // 判断站点有没有排序
      mapInstance: null,
      options1: [],

      // heli
      lineData: {}, // 线路数据
      trackEventData: [
        // {
        //   content: '到达[二娃]客户家',
        //   time: '2020-06-02 14:19:08',
        //   type: 'station'
        // }
      ], // 实际运动轨迹事件数据c
      markerList: [],
      infowindows: [],
      // heli

      line_form: {
        id: "",
        execution_date: "",
        tour_no: "",
      },
      no_show_road: true, // 已经显示路线
      showDesc: false,
      line_name: "",
      batch_count: "", // 订单总量
      expect_pickup_quantity: "", // 取件量
      expect_pie_quantity: "", // 派件量
      driver_name: "", // 司机名称
      car_no: "", // 车牌号

      location_info: "", // 地址信息
      next_code: "", // 下一地点的 code

      total_arrival_time: "", // 总时间
      total_distance: "", // 总里程
      execution_date: "", // 取派日期

      currentDriverLocation: {
        latitude: "",
        longitude: "",
      },
      disabled: false,
      line: "", // 线路 code
      dialogVisible: false,

      center: {
        lat: 52.37306,
        lng: 4.893,
        zoom: 5,
      },
      map: null,

      // 弹窗
      infoContent: "",
      infoWindowPos: {
        lat: 0,
        lng: 0,
      },
      infoWinOpen: false,
      infoOptions: {
        pixelOffset: {
          width: 0,
          height: -35,
        },
      },

      currentMidx: null,
      markers: [],

      options: [], // 缓存地址列表

      currentPosition: {
        lat: 1.38,
        lng: 103.8,
        zoom: 18,
      }, // 当前地图位置
      switchText: false,
      headerClose: false,
      selected: [], // 选择的数量

      realTrack: [],
      systemTrack: [],
      stationList: [], // 站点列表
      // 国家
      country: JSON.parse(localStorage.getItem("country")),
    };
  },
  computed: {
    mode() {
      return this.$store.state.config.mode;
    },

    google: gmapApi, // map对象

    isWrapperClose() {
      return this.switchText !== false;
    },

    isWrapperHeaderClose() {
      return this.headerClose !== false;
    },

    isOver() {
      return this.selected.length === 0 || this.selected.length > 25;
    },
    isFinshedLine() {
      return this.$route.query.status === 5;
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
  },
  created() {
    // init
    // this.getDate(
    //   this.$route.query.id,
    //   this.$route.query.tour_no,
    //   {
    //     model: this.mode,
    //   },
    // );
    // this.locationToCouncry(this.country.en, this.country.en);
  },
  mounted() {
    // this.getDate(this.$route.query.id);
    // this.infowindow = new google.maps.InfoWindow();
    // this.initUserPosition();
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
      this.getDate(this.$route.query.id, this.$route.query.tour_no, {
        model: this.mode,
      });
    },
    // 查看全部
    checkAll() {
      this.$store.commit("config/refreshMode", 1);
      this.$store.commit("config/refreshRouter", Math.random()); // refresh current router
    },

    // 系统规划路线
    handlerSystemTrack() {
      this.$store.commit("config/refreshRouter", Math.random()); // 刷新当前路由
      this.$store.commit("config/refreshMode", 2);
    },

    // 实际运动轨迹
    handlerRealTrack() {
      this.$store.commit("config/refreshRouter", Math.random()); // 刷新当前路由
      this.$store.commit("config/refreshMode", 3);
    },
    // getLineList() {
    //   if (!this.line_form.execution_date) return;
    //   this.$api.getDistributableLine(
    //     this.line_form.id,
    //     { id: this.line_form.id,
    //       execution_date: this.line_form.execution_date,
    //     }).then((res) => {
    //     if (!res) return false;
    //     this.options1 = res.data;
    //   });
    // },

    handlerRemoveClick(id) {
      // 系统规划线路--移除站点
      this.$confirm("确定移除站点?", "提示", {
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        type: "warning",
      }).then(() => {
        this.$api.stationRemove(id).then((res) => {
          if (!res) return;
          this.$message({
            type: "success",
            message: this.$t("SuccessfullDeleted"),
          });
          this.getDate(this.$route.query.id);
        });
      });
    },

    getLatLng(lat, lng) {
      // eslint-disable-next-line
      return new this.google.maps.LatLng(lat, lng);
    },

    handlerClickItem(item) {
      // 点击面板
      if (this.mode === 1) return;
      let info = null;
      this.currentPosition = {
        lat: +item.place_lat,
        lng: +item.place_lon,
        zoom: 12,
      };
      if (!item["place_address"]) {
        let event = item.event[0];
        info = {
          address: event.address,
          content: event.content,
          created_at: event.created_at,
        };
      } else {
        info = {
          address: item.place_address,
          expect_arrive_time: item.expect_arrive_time,
          actual_arrive_time: item.actual_arrive_time,
          content: item.place_address,
          created_at: item.created_at,
        };
      }

      this.$refs.gmap.$mapPromise.then((map) => {
        map.panTo(this.currentPosition); // 设置地图中心
        map.setZoom(this.currentPosition.zoom); // 设置层级
        this.infoWindowPos = {
          lat: this.currentPosition.lat,
          lng: this.currentPosition.lng,
        }; // 设置 信息窗位置
        this.infoWinOpen = true; // 开启 信息窗
        if (!item["place_address"]) {
          this.infoContent = this.getCarTrackInfoWindowContent(info); // 设置 信息窗内容
        } else {
          this.infoContent = this.getInfoWindowContent(info); // 设置 信息窗内容
        }
      });
    },

    toggleInfoWindow(marker, idx) {
      // 点击地图上的标记
      let info = null;
      let item = marker.data ? marker.data : marker.event[0];
      if (item["place_address"]) {
        info = {
          address: item.place_address,
          expect_arrive_time: item.expect_arrive_time,
          actual_arrive_time: item.actual_arrive_time,
          content: item.place_address,
          created_at: item.created_at,
        };
        this.infoContent = this.getInfoWindowContent(info);
      } else {
        let event = item.event[0];
        info = {
          address: event.address || item.address,
          content: event.content,
          created_at: event.created_at || item.created_at,
        };
        this.infoContent = this.getCarTrackInfoWindowContent(info);
      }
      this.infoWindowPos = marker.position;

      if (+this.currentMidx === +idx) {
        this.infoWinOpen = !this.infoWinOpen;
      } else {
        this.infoWinOpen = true;
        this.currentMidx = idx;
      } // 开关弹窗
    },

    handlerCloseHeaderDesc() {
      // 打开查看面板
      this.headerClose = !this.headerClose;
    },

    handleSwitchClick() {
      // 收起右侧面板
      this.switchText = !this.switchText;
    },

    getMarkerLatLng(e) {
      this.location_info = `${e.latLng.lat()}, ${e.latLng.lng()}`;
      this.currentDriverLocation = {
        latitude: `${e.latLng.lat()}`,
        longitude: `${e.latLng.lng()}`,
      };
    },

    getInfoWindowContent(data) {
      // 信息弹窗文字
      return `<div id="card">
            <div class="header">
              定位点:
            </div>
            <div id="content">
              <span class="item">当前地址: ${data.address}</span>
              <span class="item">预计到达时间: ${data.expect_arrive_time}</span>
              ${
                data.actual_arrive_time
                  ? `<span class="item">实际到达时间: ${data.actual_arrive_time}</span>`
                  : ""
              }
            </div>
        </div>`;
    },

    getCarTrackInfoWindowContent(data) {
      // 信息弹窗文字
      return `<div id="card">
            <div class="header">
              定位点:
            </div>
            <div id="content">
              <span class="item">内容: ${data.content}</span>
              ${
                data.address
                  ? `<span class="item">位置: ${data.address}</span>`
                  : ""
              }
              <span class="item">时间: ${data.created_at}</span>
            </div>
        </div>`;
    },

    async getDriverRealTrack(tourNo) {
      return this.$api.getDriverRealTrack({ tourNo });
    },
    async getLineDetail(id) {
      return this.$api.getLineDetail(id);
    }, // return staitions detail
    handleDataFormat(data) {
      return ` ${data.warehouse_street},
              ${data.warehouse_house_number},
              ${data.warehouse_city},
              ${data.warehouse_post_code},
              ${data.warehouse_country},`;
    },

    // 创建点对象
    createdMarker(positions, iconUrl, lat, lon, description = null) {
      const tempArray = [];
      positions.forEach((item, index) => {
        const pointer = {
          position: {
            lat: +item[lat],
            lng: +item[lon],
          },
          icon: iconUrl,
          description: `${
            description ||
            (item["sort_id"] && item.sort_id !== 1000
              ? item.sort_id
              : index + 1)
          }`,
          data: item,
        };
        tempArray.push(pointer);
      });
      return tempArray;
    },

    async getDate(id, tourNo, { model, cleanRoute }) {
      const systemTrack = await this.$api.getLineDetail(id); // 线路详情
      const actualLine = await this.$api.getCarTrack({ tour_no: tourNo }); // 线路追踪
      console.log("线路追踪");
      console.log(actualLine);
      this.lineData = systemTrack.data; // 线路总数据
      console.log("this.lineData 线路总数据");
      console.log(this.lineData);
      this.systemTrack = systemTrack.data.batchs; // systemTrack points
      console.log("this.systemTrack ");
      console.log(this.systemTrack);
      const warehousePoint = this.handleDataFormat(systemTrack.data); // 仓库地址

      // let actualLineIcon = require('../../../assets/img/marker2.png');
      // let systemLineIcon = require('../../../assets/img/marker1.png');

      // 实际线路数据处理
      let actualLineStation = actualLine.data.tour_event; // 实际线路所有站点
      let actualLineData = actualLine.data.route_tracking; // 实际线路路线坐标
      const realMarkers = this.createdMarker(
        // 实际运动轨迹站点生成坐标对象
        actualLineStation,
        require("../../../assets/img/water.png"),
        // "https://dev-tms.nle-tech.com/storage/water.png",
        "place_lat",
        "place_lon"
      ); // create markers
      realMarkers[0]
        ? (realMarkers[0].icon = require("../../../assets/img/ck.png"))
        : ""; // 仓库位置
      // 实际线路数据
      let actualLineStartStation = null;
      let actualLineEndStation = null;
      if (actualLineData.length !== 0) {
        actualLineStartStation = this.getLatLng(
          actualLineData[0].lat,
          actualLineData[0].lon
        ); // 实际线路开始坐标
        // actualLineEndStation = this.getLatLng(
        //   actualLineData[actualLineData.length - 1].lat,
        //   actualLineData[actualLineData.length - 1].lon
        // ); // 实际线路结束坐标
      }
      let actualLineMiddleStation = actualLineData.map((item) => {
        item.location = {};
        item.location.location = {
          lat: +item.lat,
          lng: +item.lon,
        };
        return item.location;
      }); // 实际线路中间点坐标

      console.log(systemTrack.data.batchs, "systemTrack.data.batchs");
      let systemMarkers = this.createdMarker(
        // 系统规划路线标记点
        systemTrack.data.batchs,
        // "https://dev-tms.nle-tech.com/storage/water.png",
        require("../../../assets/img/water.png"),

        "place_lat",
        "place_lon"
      );

      let sysWarehouse = {
        lat: systemTrack.data.warehouse_lat,
        lon: systemTrack.data.warehouse_lon,
        place_address: systemTrack.data.warehouse_address,
        expect_arrive_time: systemTrack.data.warehouse_expect_arrive_time,
      };
      systemMarkers = systemMarkers.concat(
        this.createdMarker(
          // 系统规划仓库
          [sysWarehouse],
          require("../../../assets/img/ck.png"),
          "lat",
          "lon"
        )
      );

      // 取司机坐标
      let routeTracking = this.createdMarker(
        // 实际运动轨迹标记点
        actualLineData,
        require("../../../assets/img/water.png"),

        // "https://dev-tms.nle-tech.com/storage/water.png",
        "lat",
        "lon"
      ); // create markers
      let carMarker = null;
      if (actualLineData.length !== 0) {
        carMarker = routeTracking[routeTracking.length - 1]; // 司机位置
        // carMarker.icon = "https://dev-tms.nle-tech.com/storage/siji.png";
        carMarker.icon = require("../../../assets/img/siji.png");
      }

      // 行驶里程，总耗时
      if (this.lineData.status > 4) {
        this.distance = this.lineData.actual_distance;
        this.costTime = this.lineData.actual_time_human; // 总耗时
      } else {
        this.distance = this.lineData.expect_distance;
        this.costTime = this.lineData.expect_time_human; // 总耗时
      }

      if (model === 1) {
        this.markers = systemMarkers; // render all markers in map
        this.markers.push(carMarker);
        console.log(systemMarkers, carMarker, "asd", this.markers);
        this.realOptios = actualLineStation; // 设置面板信息

        this.drawEstimatedLine(systemTrack.data, cleanRoute);
        this.drawActualLine(systemTrack.data, actualLine.data, cleanRoute);

        // render realTrack
        let waypoints1 =
          actualLineMiddleStation.length >= 3
            ? actualLineMiddleStation.slice(
                1,
                actualLineMiddleStation.length - 1
              )
            : [];
        let pointP = actualLineData.filter((el) => {
          return !!el.event;
        });
        let markerP = this.createdMarker(
          // 实际运动轨迹站点生成坐标对象
          pointP,
          require("../../../assets/img/water.png"),

          // "https://dev-tms.nle-tech.com/storage/water.png",
          "lat",
          "lon",
          "P"
        ); // create markers
        this.markers = this.markers.concat(markerP);
        // if (waypoints1.length > 25) {
        //   let maxNum = Math.ceil(waypoints1.length / 25);
        //   let lastLen = 0;
        //   for (let i = 0; i < maxNum; i++) {
        //     let approachPoint = [];
        //     if (lastLen < waypoints1.length) {
        //       lastLen = lastLen + 25;
        //       approachPoint = waypoints1.slice(i * 25, lastLen);
        //     } else {
        //       approachPoint = waypoints1.slice(i * 25, waypoints1.length + 1);
        //     }
        //     this.renderWaypoints({
        //       origin: actualLineStartStation,
        //       destination: actualLineStartStation,
        //       travelMode: "DRIVING",
        //       waypoints: approachPoint,
        //       optimizeWaypoints: true,
        //       lineColor: "#dc704c",
        //       strokeWeight: 5, // 线条粗细
        //       zIndex: 2,
        //       cleanRoute,
        //     }); // rende
        //   }
        // } else {
        //   this.renderWaypoints({
        //     origin: actualLineStartStation,
        //     destination: actualLineStartStation,
        //     travelMode: "DRIVING",
        //     waypoints: waypoints1,
        //     optimizeWaypoints: true,
        //     lineColor: "#dc704c",
        //     strokeWeight: 4, // 线条粗细
        //     zIndex: 2,
        //     cleanRoute,
        //   }); // render systemTrack in map
        // }
        return;
      }

      if (model === 2) {
        this.markers = systemMarkers; // render all markers in map
        this.options = this.systemTrack; // show all stations
        this.options.push({
          place_fullname: systemTrack.data.warehouse_name,
          place_address: systemTrack.data.warehouse_address,
          expect_distance: systemTrack.data.warehouse_expect_distance,
          expect_arrive_time: systemTrack.data.warehouse_expect_arrive_time,
          actual_arrive_time: systemTrack.data.warehouse_actual_arrive_time,
          created_at: null,
          place_lat: systemTrack.data.warehouse_lat,
          place_lon: systemTrack.data.warehouse_lon,
          sort_id: "w",
          id: systemTrack.data.warehouse_id,
          status: null,
        });
        this.drawEstimatedLine(systemTrack.data, cleanRoute);
        return;
      }

      if (model === 3) {
        // 线路追踪
        const carTrackLen = actualLine.data.route_tracking.length;
        const middlePointMap = actualLine.data.route_tracking.slice(
          1,
          carTrackLen - 1
        ); // 中间点集合
        // let middleMarkerMap = middlePointMap.map(item => {
        //   item.location = {};
        //   item.location.location = {
        //     lat: +item.lat,
        //     lng: +item.lon,
        //   };
        //   return item.location;
        // }); // 中间点坐标集合
        // this.createMarker(middlePointMap);
        // 线路追踪
        this.markers = realMarkers; // render all markers in map
        this.realOptios = actualLineStation; // 设置面板信息
        this.drawActualLine(systemTrack.data, actualLine.data, cleanRoute);

        let waypoints =
          actualLineMiddleStation.length >= 3
            ? actualLineMiddleStation.slice(
                1,
                actualLineMiddleStation.length - 1
              )
            : [];

        let pointP = actualLineData.filter((el) => {
          return !!el.event;
        });
        let markerP = this.createdMarker(
          // 实际运动轨迹站点生成坐标对象
          pointP,
          // "https://dev-tms.nle-tech.com/storage/water.png",
          require("../../../assets/img/water.png"),
          "lat",
          "lon",
          "P"
        ); // create markers
        this.markers = this.markers.concat(markerP);
        // if (waypoints.length > 25) {
        //   let maxNum = Math.ceil(waypoints.length / 25);
        //   let lastLen = 0;
        //   for (let i = 0; i < maxNum; i++) {
        //     let approachPoint = [];
        //     if (lastLen < waypoints.length) {
        //       lastLen = lastLen + 25;
        //       approachPoint = waypoints.slice(i * 25, lastLen);
        //       ;
        //     } else {
        //       approachPoint = waypoints.slice(i * 25, waypoints.length + 1);
        //       ;
        //     }
        //     this.renderWaypoints({
        //       origin: actualLineStartStation,
        //       destination: actualLineStartStation,
        //       travelMode: "DRIVING",
        //       waypoints: approachPoint,
        //       optimizeWaypoints: true,
        //       lineColor: "#dc704c",
        //       strokeWeight: 4, // 线条粗细
        //       zIndex: 2,
        //       cleanRoute,
        //     }); // rende
        //   }
        // } else {
        //   this.renderWaypoints({
        //     origin: actualLineStartStation,
        //     destination: actualLineStartStation,
        //     travelMode: "DRIVING",
        //     waypoints: waypoints,
        //     optimizeWaypoints: true,
        //     lineColor: "#dc704c",
        //     strokeWeight: 4, // 线条粗细
        //     zIndex: 2,
        //     cleanRoute,
        //   }); // render systemTrack in map
        // }
      }
    },

    createMarker(list) {
      // 创建坐标点
      this.$refs.gmap.$mapPromise.then((map) => {
        list.forEach((el) => {
          let marker = new google.maps.Marker({
            position: this.getLatLng(el.lat, el.lon),
            map: map,
            // icon: require('@/assets/img/circle.png')
          });
          this.markerList.push(marker);
          const that = this;
          marker.addListener("click", function () {
            if (el.event.length) {
              infowindow.open(map, marker);
            }
            that.trackEventData = el.event || [];
          });
        });
      });
    },

    drawEstimatedLine(lineData, cleanRoute) {
      // 绘制系统线路路线
      //仓库位置
      const warehouseWaypoint = {
        location: this.getLatLng(
          +lineData.warehouse_lat,
          +lineData.warehouse_lon
        ),
      };
      var currentWaypoints = [];
      lineData.batchs.forEach((item) => {
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
        this.renderWaypoints({
          origin: beginWaypoint,
          destination: endWaypoint,
          travelMode: "DRIVING",
          waypoints: finalWaypoints,
          optimizeWaypoints: true,
          lineColor: "#6d93dc",
          strokeWeight: 5,
          zIndex: 1,
          cleanRoute: cleanRoute,
        }); // render

        lastWaypoint = endWaypoint;
      }
    },

    drawActualLine(lineData, actualData, cleanRoute) {
      // 绘制实际线路路线
      //仓库位置
      const warehouseWaypoint = {
        location: this.getLatLng(
          +lineData.warehouse_lat,
          +lineData.warehouse_lon
        ),
      };
      var currentWaypoints = [];
      actualData.route_tracking.forEach((item) => {
        currentWaypoints.push({
          location: this.getLatLng(+item.lat, +item.lon),
        });
      });

      const maxNum = 25;
      var lastWaypoint = {};
      for (var i = 0; i < currentWaypoints.length; i += maxNum) {
        var newWaypoints = currentWaypoints.slice(i, i + maxNum);

        console.log(newWaypoints, "newWaypoints");

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
          // endWaypoint = warehouseWaypoint;
          finalWaypoints = newWaypoints;

          console.log("set endWaypoint,  i+maxNum", endWaypoint, i + maxNum);
        }

        console.log("beginWaypoint", beginWaypoint);
        console.log("waypoints", finalWaypoints);
        console.log("endWaypoint", endWaypoint);
        this.renderWaypoints({
          origin: beginWaypoint,
          destination: endWaypoint,
          travelMode: "DRIVING",
          waypoints: finalWaypoints,
          optimizeWaypoints: true,
          lineColor: "#dc704c",
          strokeWeight: 4,
          zIndex: 2,
          cleanRoute: cleanRoute,
        }); // render

        lastWaypoint = endWaypoint;
      }
    },

    async renderWaypoints({
      origin, // 起始位置
      destination, // 目的地位置
      travelMode, // 驾驶模式
      waypoints, // 途径点
      optimizeWaypoints, // 优化沿途点
      lineColor,
      strokeWeight, // 线条粗细
      zIndex, // 线条的z-index
      // iconUrl,
      cleanRoute,
    }) {
      return new Promise((resolve, reject) => {
        this.$refs.gmap.$mapPromise.then((map) => {
          // eslint-disable-next-line
          const directionsService = new google.maps.DirectionsService();
          // eslint-disable-next-line
          const directionsRenderer = new google.maps.DirectionsRenderer({
            suppressMarkers: true, // 去掉起点、终点的坐标图标
            polylineOptions: {
              strokeColor: lineColor,
              strokeWeight,
              zIndex,
            },
            // markerOptions: {
            //   visible: true,
            // },
          });
          // waypoints = waypoints.slice(0, waypoints[25])
          const request = {
            origin,
            destination,
            travelMode: travelMode || "DRIVING",
            waypoints,
            optimizeWaypoints,
          };

          directionsService.route(request, (result, status) => {
            console.log("result", result);
            if (status === "OK") {
              directionsRenderer.setDirections(result);
              if (cleanRoute) {
                directionsRenderer.setMap(null);
              } else {
                directionsRenderer.setMap(map);
              }
              this.no_show_road = false; // 查询成功
              resolve();
            } else {
              // eslint-disable-next-line no-alert
              // alert('存在错误地址, 查询失败');
              // this.$store.commit('config/refreshRouter', Math.random()); // refresh current router
              directionsRenderer.setDirections(null);
              reject();
            }
          });
        });
      });
    },
  },
};
</script>

<style lang="less">
.line-track {
  .map-container {
    position: relative;
    width: 100%;
    height: 70.6vh;
    .container-top {
      position: absolute;
      top: 10px;
      left: 50%;
      width: 340px;
      height: 40px;
      margin-left: -300px;
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
  }

  .wrapper {
    position: absolute;
    height: calc(76.6vh - 140px);
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
      .list_selected {
        border-radius: 5px;
        display: flex;
        justify-content: space-between;
        position: sticky;
        top: 0;
        width: 100%;
        height: 54px;
        padding: 0 8px 0 8px;
        background-color: #fff;
        line-height: 54px;
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
        // height: 140px;
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
      .card_mode {
        position: relative;
        box-sizing: border-box;
        width: 100%;
        // height: 80px;
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
        .station-panel {
          display: flex;
          .serial-number {
            span {
              display: inline-block;
              padding: 3px 7px;
              border-radius: 50%;
              background-color: #53d9a9;
              color: #000;
            }
          }
          & > span {
            &:first-child {
              width: 20%;
              display: flex;
              align-items: center;
              justify-content: center;
            }
            &:last-child {
              width: 80%;
            }
          }
          .car_icon {
            display: block;
            width: 20px;
            height: 20px;
            background-size: 100%, 100%;
            background-image: url("../../../assets/img/circle.png");
          }
          .station-info {
            margin: 5px;
            .station-content {
              color: #000;
              display: block;
            }
          }
        }
      }
      .card_mode_disabled {
        cursor: default;
        &:hover {
          border-style: none !important;
        }
      }
      .serial-number {
        span {
          display: inline-block;
          padding: 3px 7px;
          border-radius: 50%;
          background-color: #53d9a9;
          color: #000;
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
      background: #fff;
      cursor: pointer;
      border-top-left-radius: 10px;
      border-bottom-left-radius: 10px;
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
    color: #767676;
    font-size: 18px;
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
  #card {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    // width: 180px;
    overflow: hidden;
    .header {
      width: 100%;
      height: 18px;
      padding: 2px 0 0 2px;
      background-color: #636363;
      color: white;
    }
    #content {
      margin: 8px 0 0 0;
      padding-left: 8px;
      display: flex;
      flex-direction: column;
      .item {
        display: inline-block;
        line-height: 20px;
      }
    }
  }
  .gm-style .gm-style-iw-c {
    padding: 0;
  }

  .line-track-tuglie {
    // 图例
    display: table;
    margin-top: 20px;
    .line-track-row {
      display: table-row;
      .line-track-col {
        display: table-cell;
        padding: 10px;
        .line-xt {
          display: inline-block;
          width: 80px;
          height: 10px;
          background-color: #6d93dc;
        }
        .line-sj {
          display: inline-block;
          width: 80px;
          height: 10px;
          background-color: #dc704c;
        }
        .not-arrived {
          display: inline-block;
          width: 32px;
          height: 32px;
          background-image: url("../../../assets/img/marker1.png");
          vertical-align: bottom;
        }
        .arrived {
          display: inline-block;
          width: 32px;
          height: 32px;
          background-image: url("../../../assets/img/marker2.png");
          vertical-align: bottom;
        }
      }
    }
  }
}
</style>
<style lang="less">
.gmnoprint {
  display: none;
}
</style>

