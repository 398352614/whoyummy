<template>
  <!-- 智能管车 -->
  <div>
    <div class="googleMap" v-if="mapType === 'baidu'">
      <div class="left_panel">
        <div class="left_panel_status">
          <span
            :class="{ check_status: pageParams.is_online == 1 }"
            @click="handleChangeStatus(1)"
            >{{ $t("online") }}</span
          >
          <span
            :class="{ check_status: pageParams.is_online == 2 }"
            @click="handleChangeStatus(2)"
            >{{ $t("offline") }}</span
          >
        </div>
        <div class="left_panel_input">
          <el-input
            size="small"
            clearable
            @keyup.enter.native="geTallTrack"
            :placeholder="$t('driverName')"
            v-model="pageParams.driver_name"
            style="width: 70%"
          >
          </el-input>
          <el-button
            size="small"
            @click="geTallTrack"
            type="primary"
            :loading="this.$store.state.config.button_loading"
            >{{ $t("Search") }}</el-button
          >
        </div>
        <div>
          <el-table max-height="600px" border :data="tallTrackData">
            <el-table-column
              :label="$t('driverName')"
              prop="driver_name"
              align="center"
            ></el-table-column>
          </el-table>
        </div>
      </div>
      <baidu-map
        class="bm-view"
        :center="center"
        :zoom="center.zoom"
        :scroll-wheel-zoom="true"
        @ready="init"
      >
        <!-- <bm-marker v-for="(item, index) in merker" :key="index" :position="item" :dragging="false" @click="handleClickMarker(item)" :icon="item.icon">
              <bm-info-window :show="item.show" :title="item.content">
              </bm-info-window>
            </bm-marker> -->
      </baidu-map>
      <!-- <div class="google-map-top-info">
            <div class="left-info">全部 <span>{{tallTrackData.length}}</span></div>
            <div>
              <el-input size="small" clearable @clear="geTallTrack" @keyup.enter.native="geTallTrack" :placeholder="$t('请输入司机姓名')" v-model="pageParams.driver_name">
                <el-button slot="append" size="small" @click="geTallTrack" type="primary" :loading="this.$store.state.config.button_loading">{{$t('Search')}}</el-button>
              </el-input>
            </div>
          </div> -->
    </div>

    <div class="googleMap" v-else>
      <div class="left_panel">
        <div class="left_panel_status">
          <span
            :class="{ check_status: pageParams.is_online == 1 }"
            @click="handleChangeStatus(1)"
            >{{ $t("online") }}</span
          >
          <span
            :class="{ check_status: pageParams.is_online == 2 }"
            @click="handleChangeStatus(2)"
            >{{ $t("offline") }}</span
          >
        </div>
        <div class="left_panel_input">
          <el-input
            size="small"
            clearable
            @keyup.enter.native="geTallTrack"
            :placeholder="$t('driverName')"
            v-model="pageParams.driver_name"
            style="width: 70%"
          >
          </el-input>
          <el-button
            size="small"
            @click="geTallTrack"
            type="primary"
            :loading="this.$store.state.config.button_loading"
            >{{ $t("Search") }}</el-button
          >
        </div>
        <div>
          <el-table
            max-height="600px"
            border
            :data="tallTrackData"
            @row-click="handleRowClick"
          >
            <el-table-column
              :label="$t('driverName')"
              prop="driver_name"
              align="center"
            ></el-table-column>
            <!-- 车辆 -->
            <el-table-column
              :label="$t('carManagement')"
              prop="car_no"
              align="center"
            ></el-table-column>
          </el-table>
        </div>
      </div>
      <!-- 地图组件 -->
      <gmap-map
        ref="gmap"
        id="gmap"
        :noClear="false"
        :center="center"
        :zoom="center.zoom"
        style="width: 100%; height: 100%"
      >
      </gmap-map>
      <!-- 地图组件 -->

      <!-- <div class="google-map-top-info">
              <div class="left-info">全部 <span>{{tallTrackData.length}}</span></div>
              <div>
                <el-input size="small" clearable @clear="geTallTrack" @keyup.enter.native="geTallTrack" :placeholder="$t('请输入司机姓名')" v-model="pageParams.driver_name">
                  <el-button slot="append" size="small" @click="geTallTrack" type="primary" :loading="this.$store.state.config.button_loading">{{$t('Search')}}</el-button>
                </el-input>
              </div>
            </div> -->
    </div>
  </div>
</template>

<script>
import { gmapApi } from "vue2-google-maps";
import mp from "@/components/map";

export default {
  name: "intelligentTubeCar",
  data() {
    return {
      location_info: "", // 地址信息

      currentDriverLocation: {
        latitude: "",
        longitude: "",
      },
      line: "", // 线路 code

      // 地图中心
      center: {
        lat: 52.37306,
        lng: 4.893,
        zoom: 7,
      },
      markers: [],

      // 信息显示
      infoOptions: {
        pixelOffset: {
          width: 0,
          height: -35,
        },
      },
      infoWindowPos: {
        lat: 0,
        lng: 0,
      },
      infoWinOpen: false,
      infoContent: "",

      // 车辆数据
      tallTrackData: [],

      pageParams: {
        is_online: 1,
        driver_name: null,
      },

      merker: [],
      BMap: null,
      map: null,
      markerSign: [],
      // 国家
      country: JSON.parse(localStorage.getItem("country")),
    };
  },
  computed: {
    google: gmapApi,
    mapType() {
      return this.$store.state.globalData.maptype;
    },
  },
  created() {
    this.geTallTrack();
  },
  mounted() {
    this.locationToCouncry(this.country.en, this.country.en);

    // this.$refs.gmap.$mapPromise.then((map) => { // 禁用地图双击放大
    //   map.setOptions({disableDoubleClickZoom: true });
    // });
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
    init({ BMap, map }) {
      this.BMap = BMap;
      this.map = map;
    },
    handleChangeStatus(val) {
      this.pageParams.is_online = val;
      this.geTallTrack();
    },
    geTallTrack() {
      this.$api.geTallTrack(this.pageParams).then((res) => {
        if (!res) return;
        this.tallTrackData = res.data;
        if (this.mapType === "google") {
          this.createMarker();
        } else {
          this.createBDMarker();
        }
      });
    },

    getLatLng(lat, lng) {
      // 返回经纬度对象
      // eslint-disable-next-line
      return new this.google.maps.LatLng(lat, lng);
    },

    getContentTemplate(data, type) {
      if (type == 2) {
        return `<div class="show-car-info-windows" id="${data.id}">
          ${this.$t("Driver")} : ${data.driver_name} <br/>
          ${this.$t("contactDetails")}: ${data.driver_phone} <br/>
          ${this.$t("NumberPlate")} : ${data.car_no} <br/>
          ${this.$t("PickUpLine")}: ${data.line_name} <br/>
          ${this.$t("VehicleLocation")}: ${data.adress} <br/>
        `;
      } else {
        return `<div class="show-car-info-windows" id="${data.id}">
        ${this.$t("Driver")}: ${data.driver_name} <br/></div>
      `;
      }
    },
    getDriverContentTemplate(data) {
      if (data.show_details) {
        return `
          ${this.$t("Driver")} : ${data.driver_name} <br/>
          ${this.$t("contactDetails")}: ${data.driver_phone} <br/>
          ${this.$t("NumberPlate")} : ${data.car_no} <br/>
          ${this.$t("PickUpLine")}: ${data.line_name} <br/>
          ${this.$t("VehicleLocation")}: ${data.adress} <br/>
        `;
      } else {
        return `
           ${this.$t("Driver")}: ${data.driver_name} <br/>
        `;
      }
    },

    // 地图创建坐标点
    createMarker() {
      let that = this;
      let image = require("@/assets/img/blue_car.png");
      this.$refs.gmap.$mapPromise.then((map) => {
        if (this.pageParams.is_online == 2) {
          that.markerSign.map((el) => {
            el.setMap(null);
          });
          that.markerSign = [];
          return;
        }
        let timer = 780;
        that.tallTrackData.forEach((el, index) => {
          console.log(el);
          el.show_details = false;
          if (el.lat && el.lon) {
            setTimeout(function () {
              let location = that.getLatLng(el.lat, el.lon);
              let marker = new google.maps.Marker({
                position: location,
                map: map,
                icon: image,
              });
              that.markerSign.push(marker);

              let geocoder = new google.maps.Geocoder();
              geocoder.geocode(
                {
                  location: location,
                },
                function (results, status) {
                  if (status == google.maps.GeocoderStatus.OK) {
                    if (results[0]) {
                      el.adress = results[0].formatted_address;
                      let infowindow = new google.maps.InfoWindow({
                        content: that.getContentTemplate(el),
                      });
                      infowindow.open(map, marker);
                      infowindow.addListener("domready", function () {
                        let sele = document.getElementById(el.id + "");
                        sele.addEventListener("click", function (e) {
                          el.show_details = !el.show_details;
                          sele.innerHTML = that.getDriverContentTemplate(el);
                        });
                        marker.addListener("click", function () {
                          el.show_details = !el.show_details;
                          sele.innerHTML = that.getDriverContentTemplate(el);
                        });
                      });
                    }
                  } else {
                    // alert("Geocoder failed due to: " + status);
                  }
                }
              );
            }, timer * index);
          }
        });
      });
    },
    handleClickMarker(item) {
      item.show = !item.show;
    },

    // 地图创建坐标点
    createBDMarker() {
      if (this.pageParams.is_online == 2) {
        this.BMap.clearOverlays();
        return;
      }
      let BMap = this.BMap;
      let icon = new BMap.Icon(
        require("@/assets/img/blue_car.png"),
        new BMap.Size(52, 26)
      );
      this.tallTrackData.forEach((el) => {
        el.show_details = false;
        var pt = new BMap.Point(+el.lon, +el.lat);
        var myIcon = new BMap.Icon(
          require("@/assets/img/blue_car.png"),
          new BMap.Size(50, 50),
          { anchor: new BMap.Size(23, 45) }
        );
        var marker = new BMap.Marker(pt, {
          icon: myIcon,
        }); // 创建标注
        this.map.addOverlay(marker);
        var infoWindow = new BMap.InfoWindow(this.getContentTemplate(el)); // 创建信息窗口对象
        infoWindow.addEventListener("click", function (e) {
          let sele = document.getElementById(el.id + "");
          sele.addEventListener("click", function (e) {
            el.show_details = !el.show_details;
            sele.innerHTML = that.getDriverContentTemplate(el);
          });
        });
        marker.addEventListener("click", function () {
          this.map.openInfoWindow(infoWindow, pt); //开启信息窗口
        });
      });
      this.center.lat = +this.tallTrackData[0].lat;
      this.center.lng = +this.tallTrackData[0].lon;
    },

    handleRowClick(row) {
      if (this.pageParams.is_online == 2) return;
      if (this.mapType === "google") {
        this.handleGoogleMarker(row);
      } else {
        this.handleBaiduMarker(row);
      }
    },

    handleGoogleMarker(row) {
      const that = this;
      this.$refs.gmap.$mapPromise.then((map) => {
        let sele = document.getElementById(row.id + "");
        if (sele == null) {
          row.show_details = true;
          let image = require("@/assets/img/blue_car.png");
          let location = that.getLatLng(row.lat, row.lon);
          let marker = new google.maps.Marker({
            position: location,
            map: map,
            icon: image,
          });
          let type = 2;
          let infowindow = new google.maps.InfoWindow({
            content: that.getContentTemplate(row, type),
          });
          infowindow.open(map, marker);
          infowindow.addListener("domready", function () {
            let sele = document.getElementById(row.id + "");
            sele.addEventListener("click", function (e) {
              row.show_details = !row.show_details;
              sele.innerHTML = that.getDriverContentTemplate(row);
            });
            marker.addListener("click", function () {
              row.show_details = !row.show_details;
              sele.innerHTML = that.getDriverContentTemplate(row);
            });
          });
        } else {
          sele.click();
        }

        map.panTo(that.getLatLng(+row.lat, +row.lon));
      });
    },
    handleBaiduMarker(row) {
      let sele = document.getElementById(row.id + "");
      sele.click();
    },
  },
};
</script>

<style lang="less" scope>
.googleMap {
  .el-table--enable-row-hover .el-table__body tr:hover > td {
    background-color: rgb(255, 204, 70) !important;
    color: #fff !important;
    cursor: pointer !important;
  }
  position: relative;
  width: 100%;
  height: calc(100vh - 118px);
  .bm-view {
    width: 100%;
    height: 100%;
  }
  .left_panel {
    position: absolute;
    // top: 20px;
    left: 0;
    width: 300px;
    height: calc(100vh - 118px);

    overflow: auto;
    z-index: 5;
    background: #fff;
    .left_panel_status {
      display: flex;
      span {
        display: inline-block;
        width: 60%;
        height: 40px;
        line-height: 40px;
        text-align: center;
        border-radius: 40px;
        cursor: pointer;
        border: 1px solid #53d9a9;
        color: #53d9a9;
      }
      .check_status {
        background: #53d9a9;
        color: #fff;
      }
    }
    .left_panel_input {
      margin: 10px 0;
      text-align: center;
    }
  }
}

.google-map-top-info {
  position: absolute;
  top: 5px;
  width: 100%;
  display: flex;
  justify-content: space-between;
  .left-info {
    width: 150px;
    height: 30px;
    background: #fff;
    line-height: 30px;
    box-sizing: border-box;
    padding: 0 5px;
    margin-left: 5px;
    color: #0adaf0;
    span {
      margin-left: 15px;
    }
  }
}

.control_panel {
  position: absolute;
  top: 10px;
  left: 50%;
  width: 340px;
  height: 40px;
  margin-left: -170px;
  z-index: 5;
  display: flex;
  justify-content: space-around;
  align-items: center;
  font-size: 18px;
  cursor: pointer;
  color: #53d9a9;
  .panel_item {
    margin: 5px;
    white-space: nowrap;
    padding: 5px;
    background-color: #fff;
    border-radius: 2px;
    box-shadow: rgba(0, 0, 0, 0.3) 0px 1px 4px -1px;
  }
  .panel-check {
    border: 2px solid #53d9a9;
  }
}

.area-dialog {
  .el-input {
    max-width: 328px;
  }
  .el-textarea {
    max-width: 328px;
  }
}

.dialog-footer {
  text-align: right;
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
  .wrapper_content {
    box-sizing: border-box;
    padding: 18px;
    position: relative;
    top: 70px;
    left: -7px;
    height: 100%;
    width: 100%;
    overflow: auto;
    background: #fff;
    color: #aaaaaa;
    border-radius: 4px;
    box-shadow: 0 0 3px 1px #c4c4c4;
    .wrapper_content_item {
      padding: 6px;
      box-shadow: 0 0 3px 1px #c4c4c4;
      color: #53d9a9;
      margin-bottom: 15px;
      .wrapper-item-tit {
        display: flex;
        justify-content: space-between;
        .item-edit {
          cursor: pointer;
        }
      }
      .get-date {
        color: #000;
        padding-left: 5px;
      }
      & > div {
        margin: 6px 0;
      }
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
.gm-style .gm-style-iw-c {
  padding: 0;
}
</style>
<style lang="less">
.gmnoprint {
  display: none;
}
.show-car-info-windows {
  padding: 15px;
  line-height: 20px;
}
.check-row {
  background: rgb(255, 204, 70);
  color: #fff;
}
</style>

