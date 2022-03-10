<template>
  <div>
    <div class="googleMap">
      <div class="control_panel">
        <span
          :class="{ 'panel-check': block_local, panel_item: true }"
          @click="addBlock"
          >{{ $t("AddHandPaintedBlocks") }}</span
        >
        <span
          v-show="!block_local"
          class="panel_item"
          @click="openAddressDialog"
          >{{ $t("AddAdministrativeBlock") }}</span
        >
        <span v-show="block_local" class="panel_item" @click="handleDblclick">{{
          $t("Save")
        }}</span>
        <span
          v-show="block_local"
          class="panel_item"
          @click="canclePreviousMarker"
          >{{ $t("Revoke") }}</span
        >
        <span v-show="block_local" class="panel_item" @click="deleteThisArea">{{
          $t("Delete")
        }}</span>
      </div>

      <!-- 地图组件 -->
      <gmap-map
        ref="gmap"
        id="gmap"
        :noClear="false"
        :center="center"
        :zoom="center.zoom"
        @click="handleClick"
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
        v-if="(areaInfoList.length || this.$route.query.id) && !block_local"
      >
        <div class="wrapper_content">
          <div
            :class="{
              wrapper_content_item: true,
              wrapper_content_item_check: cehckInfoItem == index,
            }"
            v-for="(item, index) in areaInfoList"
            :key="index"
            @click="handleClickInfoItem(index)"
          >
            <div class="wrapper-item-tit">
              <span>{{ item.name }}</span>
              <span class="item-edit" @click="openEditDialog(index)">{{
                $t("Edit")
              }}</span>
            </div>
            <div>
              <span>{{ $t("PickupDate") }}</span>
              <div class="get-date">{{ item.work_day_list }}</div>
            </div>
            <div>
              <span>{{ $t("MaxPickOrder") }}</span>
              <span>{{ item.pickup_max_count }}</span>
            </div>
            <div>
              <span>{{ $t("MaxDeliveryOrder") }}</span>
              <span>{{ item.pie_max_count }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 填写区块信息 -->
    <el-dialog
      :title="$t('Edit')"
      @close="closeAreaDialog"
      :visible.sync="edit_area"
    >
      <el-form
        :model="areaInfo"
        :rules="rules"
        ref="areaForm"
        label-position="right"
        label-width="270px"
        class="area-dialog"
      >
        <el-form-item :label="$t('AreaName')" prop="name">
          <el-input v-model.trim="areaInfo.name"></el-input>
        </el-form-item>
        <el-form-item :label="$t('MaxDeliveryOrder')" prop="pie_max_count">
          <el-input v-model.trim="areaInfo.pie_max_count"></el-input>
        </el-form-item>
        <el-form-item :label="$t('MaxPickOrder')" prop="pickup_max_count">
          <el-input v-model.trim="areaInfo.pickup_max_count"></el-input>
        </el-form-item>
        <el-form-item :label="$t('addPickName')">
          <el-switch
            v-model="areaInfo.is_increment"
            :active-value="1"
            :inactive-value="2"
          >
          </el-switch>
        </el-form-item>
        <el-form-item :label="$t('OrderDeadline')" prop="order_deadline">
          <el-time-picker
            arrow-control
            value-format="HH:mm:ss"
            v-model="areaInfo.order_deadline"
            :picker-options="{
              selectableRange: '00:00:00 - 23:59:59',
            }"
          >
          </el-time-picker>
        </el-form-item>
        <el-form-item
          :label="$t('Reservationtimerange')"
          required
          prop="appointment_days"
        >
          <el-input v-model.trim="areaInfo.appointment_days"></el-input>
        </el-form-item>
        <el-form-item :label="$t('WhetherOptimizedOrder')">
          <el-switch
            v-model="areaInfo.can_skip_batch"
            :active-value="1"
            :inactive-value="2"
          >
          </el-switch>
        </el-form-item>
        <el-form-item :label="$t('whetherToEnable')">
          <el-switch
            v-model="areaInfo.status"
            :active-value="1"
            :inactive-value="2"
          >
          </el-switch>
        </el-form-item>
        <el-form-item :label="$t('LineRemark')">
          <el-input
            v-model="areaInfo.remark"
            type="textarea"
            :rows="2"
          ></el-input>
        </el-form-item>
      </el-form>
      <div class="dialog-footer">
        <el-button @click="closeAreaDialog" class="cancel">{{
          $t("Cancel")
        }}</el-button>
        <el-button
          type="primary"
          @click="submit"
          :loading="this.$store.state.config.button_loading"
          class="submit"
          >{{ $t("Confirm") }}</el-button
        >
      </div>
    </el-dialog>
    <!-- 填写区块信息 -->

    <!-- 选择行政区 -->
    <el-dialog
      :title="$t('PleaseAdministrativeRegion')"
      :visible.sync="add_administrative_show"
    >
      <el-form
        :model="areaInfo"
        :rules="rules"
        ref="areaForm"
        label-position="right"
        class="area-dialog"
      >
        <el-row :gutter="20">
          <el-col :span="6">
            <el-form-item :label="$t('Country')" prop="name">
              <el-select clearable size="small" v-model="addressInfo.country">
                <el-option
                  v-for="item in addressSelect.country"
                  :key="item.id"
                  :label="item.name"
                  :value="item.code"
                >
                </el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="6">
            <el-form-item :label="$t('province')" prop="name">
              <el-select
                clearable
                size="small"
                @change="hanldeChangeProvince"
                v-model="addressInfo.province"
              >
                <el-option
                  v-for="item in addressSelect.province"
                  :key="item.id"
                  :label="item.name"
                  :value="item.name"
                >
                </el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="6">
            <el-form-item :label="$t('shi')" prop="name">
              <el-select
                clearable
                size="small"
                @change="hanldeChangeCity"
                v-model="addressInfo.city"
              >
                <el-option
                  v-for="item in addressSelect.city"
                  :key="item.id"
                  :label="item.name"
                  :value="item.name"
                >
                </el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="6">
            <el-form-item :label="$t('qu')" prop="name">
              <el-select clearable size="small" v-model="addressInfo.area">
                <el-option
                  v-for="item in addressSelect.area"
                  :key="item.id"
                  :label="item.name"
                  :value="item.name"
                >
                </el-option>
              </el-select>
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <div class="dialog-footer">
        <el-button @click="closeAreaDialog" class="cancel">{{
          $t("Cancel")
        }}</el-button>
        <el-button
          type="primary"
          @click="checkArea"
          :loading="this.$store.state.config.button_loading"
          class="submit"
          >{{ $t("Confirm") }}</el-button
        >
      </div>
    </el-dialog>
    <!-- 选择行政区 -->
  </div>
</template>

<script>
import draggable from "vuedraggable";
import { gmapApi } from "vue2-google-maps";
import mp from "@/components/map";

export default {
  // name: "AreaPlanning",
  components: {
    draggable,
  },
  data() {
    const { BMap, BMapLib } = window;
    let appointment_days = (rule, value, callback) => {
      if (value === "") {
        callback(new Error(this.$t("Required")));
      } else if (value > 30) {
        callback(new Error(this.$t("MustLessThanDays")));
      } else {
        callback();
      }
    };
    return {
      driverName: "",
      location_info: "", // 地址信息

      currentDriverLocation: {
        latitude: "",
        longitude: "",
      },
      line: "", // 线路 code

      center: {
        lat: 0,
        lng: 0,
        zoom: 8,
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
      selected: [], // 选择的数量

      realTrack: [],
      systemTrack: [],
      stationList: [], // 站点列表

      // 何黎的属性
      latArr: [],
      stagingTags: [], // 暂存的标记对象数组
      edit_area: false, // 编辑弹窗
      areaInfoList: [], // 区域列表
      areaInfo: {
        appointment_days: null,
      },
      block_local: false, // 是否添加区块
      currentObj: null, // 代表当前多边形对象

      add_administrative_show: false, // 添加行政区域弹窗
      countryArr: [],
      addressSelect: {
        country: [
          {
            name: "中国",
            code: "CN",
          },
        ],
        province: [],
        city: [],
        area: [],
      },
      addressInfo: {
        country: "CN",
        province: null,
        city: null,
        area: null,
      },

      // demo
      twoMarker: [],

      // 国家
      country: JSON.parse(localStorage.getItem("country")),

      areaData: [],
      areaPageParams: {
        begin_date: "",
        end_date: "",
        keyword: "",
        total: 0,
        page: 1,
        per_page: 100,
        name: null,
        country: null,
        is_get_area: 1,
      },

      polygonArr: [],
      cehckInfoItem: null,

      rules: {
        name: [
          { required: true, message: this.$t("Mandatory"), trigger: "blur" },
        ],
        pie_max_count: [
          { required: true, message: this.$t("Mandatory"), trigger: "blur" },
        ],
        pickup_max_count: [
          { required: true, message: this.$t("Mandatory"), trigger: "blur" },
        ],
        order_deadline: [
          { required: true, message: this.$t("Mandatory"), trigger: "blur" },
        ],
        appointment_days: [{ validator: appointment_days, trigger: "blur" }],
      },
    };
  },
  computed: {
    mode() {
      return this.$store.state.config.mode;
    },

    google: gmapApi,

    isWrapperClose() {
      return this.switchText !== false;
    },
  },
  watch: {
    "addressInfo.province": {
      handler(val) {
        this.addressInfo.city = null;
      },
      deep: true,
    },
  },
  created() {
    // if (this.$route.query.id) { // 如果是编辑
    //   this.getLineAreaDetails();
    // }
    this.getCountryAddress();
    this.getLineAreaList();
  },
  mounted() {
    // this.$refs.gmap.$mapPromise.then((map) => { // 禁用地图双击放大
    //   map.setOptions({disableDoubleClickZoom: true });
    // });
    this.positionToCountry(this.country.en);

    // 百度地图
    this.$nextTick(() => {
      // mp("feFgNMYSZ38zjACothgQ2NHVX9pnfj6g").then( baiduMap => {
      //   console.log(baiduMap, 'bmap')
      //   var bdary = new baiduMap.Boundary();
      //   console.log(bdary, 'bdary')
      //   bdary.get('长沙市', (rs) => {
      //     var arr = rs.boundaries[0].split(', ')
      //     console.log(rs, 'rsssssss', arr)
      //   })
      // })
    });
  },
  methods: {
    // 获取显示已存在区块线路 -- 开始
    getLineAreaList() {
      // 获取已存在区块数据
      this.$api.getLineAreaList(this.areaPageParams).then((res) => {
        if (!res) return;
        this.areaInfoList = res.data.data;
        this.areaInfoList.forEach((el, index) => {
          let positionArr = [];
          let color = "#0000FF";
          if (el.id == this.$route.query.id) {
            color = "#FF444D";
            this.cehckInfoItem = index;
          }
          el.coordinate_list.forEach((child) => {
            child.forEach((item) => {
              positionArr.push({
                lat: parseFloat(item.lat),
                lng: parseFloat(item.lon),
              });
            });
            // positionArr.push({ lat: +child.lat, lng: +child.lon });
          });

          this.$refs.gmap.$mapPromise.then((map) => {
            var flightPath = new google.maps.Polygon({
              path: positionArr,
              strokeColor: color,
              strokeOpacity: 0.8,
              strokeWeight: 2,
              fillColor: color,
              fillOpacity: 0.4,
            });
            flightPath.setMap(map);
            this.polygonArr.push(flightPath);
          });
          this.drawPolygon(positionArr, "#0000FF"); // 绘制已存在区块
        });
      });
    },
    // 获取显示已存在区块线路 -- 结束

    checkdemo(position) {
      this.$refs.gmap.$mapPromise.then((map) => {
        this.areaInfoList.forEach((el) => {
          let positionArr = [];
          el.coordinate_list.forEach((child) => {
            child.forEach((item) => {
              child.forEach(() => {
                positionArr.push({
                  lat: parseFloat(item.lat),
                  lng: parseFloat(item.lon),
                });
              });
              // positionArr.push({ lat: item.lat, lng: item.lon });
            });
            // positionArr.push({ lat: +child.lat, lng: +child.lon });
          });
          var flightPath = new google.maps.Polygon({
            path: positionArr,
          });
          console.log(
            google.maps.geometry.poly.containsLocation(position, flightPath)
          );
          // if (google.maps.geometry.poly.containsLocation(position, positionArr)) {
          //   console.log('true', el)
          // }
        });
      });
    },

    positionToCountry(country) {
      // 定位到对应国家
      console.log("asa");
      if (!country) return;
      this.$refs.gmap.$mapPromise.then((map) => {
        let geocoder = new google.maps.Geocoder();
        var that = this;
        geocoder.geocode(
          {
            address: country,
            componentRestrictions: {
              country: country,
            },
          },
          function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
              if (results[0]) {
                map.setCenter(results[0].geometry.location);
                results[0].address_components[0].types.forEach((el) => {
                  if (el === "country") {
                    map.setZoom(8);
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

    handleClickInfoItem(index) {
      // 选择区块， 将选中区块绘制为红色
      this.cehckInfoItem = index;
      this.clearAllPolygon();
      this.areaInfoList.forEach((el, num) => {
        let positionArr = [];
        let color = "#0000FF";

        if (index == num) {
          color = "#FF444D";
        }
        el.coordinate_list.forEach((child) => {
          child.forEach((item) => {
            positionArr.push({
              lat: parseFloat(+item.lat),
              lng: parseFloat(+item.lon),
            });
          });
          // positionArr.push({ lat: +child.lat, lng: +child.lon });
        });
        this.$refs.gmap.$mapPromise.then((map) => {
          var flightPath = new google.maps.Polygon({
            path: positionArr,
            strokeColor: color,
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: color,
            fillOpacity: 0.4,
          });
          flightPath.setMap(map);
          this.polygonArr.push(flightPath);
        });
      });
    },
    clearAllPolygon() {
      // 清掉区块
      this.polygonArr.forEach((el) => {
        el.setMap(null);
      });
    },

    demo(country, adress) {
      // 拉取行政区轮廓坐标
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
                    map.setZoom(6);
                  } else if (el === "administrative_area_level_1") {
                    map.setZoom(7);
                  } else if (el === "locality") {
                    map.setZoom(8);
                  } else if (el === "sublocality") {
                    map.setZoom(10);
                  }
                });

                mp("feFgNMYSZ38zjACothgQ2NHVX9pnfj6g").then((baiduMap) => {
                  // 从百度获取中国行政区的轮廓
                  var thatBM = that;
                  var bdary = new baiduMap.Boundary();
                  bdary.get(adress, (rs) => {
                    var positionSun = [];
                    let positionArr = [];
                    rs.boundaries.forEach((el) => {
                      var sun = [];
                      var arr = el.split(", ");
                      for (let i = 1; i < arr.length - 1; i++) {
                        var postion = arr[i].indexOf(";");
                        var lat = parseFloat(arr[i].substr(0, postion)); //经度
                        var lng = parseFloat(arr[i].substr(postion + 1)); //纬度
                        positionArr.push({ lat: lat, lon: lng });
                        sun.push(new google.maps.LatLng(lat, lng));
                        // console.log(lat, lng)
                      }
                      positionSun.push(positionArr);
                      var flightPath = new google.maps.Polygon({
                        path: sun,
                        strokeColor: "#FF444D",
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: "#FF444D",
                        fillOpacity: 0.4,
                      });
                      flightPath.setMap(map);
                      // that.coordinate_list.push(sun)
                    });
                    console.log(thatBM.areaInfoList);
                    console.log(
                      thatBM.areaInfoList,
                      thatBM.areaInfoList.length
                    );
                    thatBM.areaInfoList[
                      thatBM.areaInfoList.length - 1
                    ].coordinate_list = positionSun[0];
                    thatBM.cehckInfoItem = thatBM.areaInfoList.length - 1;

                    // that.coordinate_list = [...that.coordinate_list, polygonList];
                  });
                });
              }
            } else {
              // alert("Geocoder failed due to: " + status);
            }
          }
        );
      });
    },

    getLatLng(lat, lng) {
      // 返回google map经纬度对象
      // eslint-disable-next-line
      return new this.google.maps.LatLng(lat, lng);
    },

    getMarkerLatLng(e) {
      this.location_info = `${e.latLng.lat()}, ${e.latLng.lng()}`;
      this.currentDriverLocation = {
        latitude: `${e.latLng.lat()}`,
        longitude: `${e.latLng.lng()}`,
      };
    },

    // 点击标记
    toggleInfoWindow(marker, idx) {
      this.infoWindowPos = marker.position;
      this.infoContent = this.getInfoWindowContent(marker.data);

      if (+this.currentMidx === +idx) {
        this.infoWinOpen = !this.infoWinOpen;
      } else {
        this.infoWinOpen = true;
        this.currentMidx = idx;
      } // 开关弹窗
    },

    getInfoWindowContent(data) {
      // 拼接信息窗口信息
      return `<div id="card">
            <div class="header">
              定位点:
            </div>
            <div id="content">
              <span class="item">地点: ${data.address}</span>
              <span class="item">描述: ${data.content}</span>
              <span class="item">时间: ${data.created_at}</span>
            </div>
        </div>`;
    },

    // 何黎的函数

    addBlock() {
      // 添加手绘区块
      this.block_local = !this.block_local;
      if (!this.block_local) {
        this.delMarker();
      }
    },

    handleClick(e) {
      // 单击添加标记
      if (this.block_local) {
        this.latArr.push(e.latLng); // 暂存坐标
        if (this.latArr.length >= 3) {
          if (this.currentObj) {
            this.currentObj.setMap(null);
          }
          this.delMarker();
          this.drawPolygon(this.latArr);
        } else {
          this.drawMarker(e.latLng);
          this.drawFlightPath(this.latArr);
        }
      } else {
        this.checkdemo(e.latLng);
      }
    },

    handleDblclick() {
      // 保存区块
      // if (this.block_local) {
      //   debugger
      //   if (this.isOverlap(this.latArr)) {
      //     debugger
      //     this.$message({
      //       type: "error",
      //       message: this.$t("TheAreasCannotOverlap"),
      //     });
      //   } else {
      this.createAreaItem(); // 创建区块信息
      // this.drawPolygon(this.latArr);
      this.latArr.forEach((el) => {
        // 坐标存入区块信息中
        this.areaInfoList[this.areaInfoList.length - 1].coordinate_list.push({
          lat: el.lat(),
          lon: el.lng(),
        });
      });
      // this.handleClickInfoItem(this.areaInfoList.length - 1); // 绘制
      this.delMarker(); // 清除标记及折线
      this.latArr = []; // 暂存坐标清空
      this.block_local = !this.block_local;
      //   }
      // }
    },

    canclePreviousMarker() {
      // 撤销
      if (!this.latArr.length) return;
      this.latArr.splice(-1, 1); // 删除最后一个点
      if (this.latArr.length >= 3) {
        if (this.currentObj) {
          this.currentObj.setMap(null);
        }
        this.drawPolygon(this.latArr);
        this.delMarker();
      } else {
        this.currentObj.setMap(null);
        this.delMarker();
        for (let item in this.latArr) {
          this.drawMarker(this.latArr[item]);
        }
        this.drawFlightPath(this.latArr);
      }
    },

    deleteThisArea() {
      // 删除区块
      this.block_local = !this.block_local;
      if (!this.latArr.length) return;
      this.latArr = [];
      this.delMarker();
      this.currentObj.setMap(null);
    },

    isOverlap(arr) {
      // 判断重叠函数， true: 重叠
      let arr1 = [];
      arr.forEach((el) => {
        arr1.push({ lat: el.lat(), lon: el.lng() });
      });
      for (let i = 0; i < this.areaInfoList.length; i++) {
        let item = this.areaInfoList[i];
        if (this.isPolygonsOverlap(arr1, item.coordinate_list)) {
          return true;
        }
      }
      return false;
    },

    drawMarker(location) {
      // 创建一个坐标点, 参数：
      this.$refs.gmap.$mapPromise.then((map) => {
        let marker = new google.maps.Marker({
          position: location,
          map: map,
        });
        marker.setMap(map);
        this.stagingTags.push(marker);
      });
    },

    drawFlightPath(pathArr) {
      // 画折线, 参数：坐标数组
      this.$refs.gmap.$mapPromise.then((map) => {
        var flightPath = new google.maps.Polyline({
          path: pathArr,
          strokeColor: "#FF444D",
          strokeOpacity: 0.8,
          strokeWeight: 2,
        });
        flightPath.setMap(map);
        this.stagingTags.push(flightPath);
      });
    },

    drawPolygon(pathArr, color = "#FF444D") {
      // 画多边形，参数： 坐标数组
      let moren = "#0000FF";
      this.$refs.gmap.$mapPromise.then((map) => {
        var flightPath = new google.maps.Polygon({
          path: pathArr,
          strokeColor: color,
          strokeOpacity: 0.8,
          strokeWeight: 2,
          fillColor: color,
          fillOpacity: 0.4,
        });
        flightPath.setMap(map);
        this.currentObj = flightPath;
        console.log(flightPath, flightPath.getPaths());
      });
    },

    delMarker() {
      // 清除坐标和折线标记
      this.$refs.gmap.$mapPromise.then((map) => {
        this.stagingTags.forEach((e) => {
          e.setMap(null);
        });

        this.stagingTags = [];
        // this.latArr = [];
      });
    },

    createAreaItem() {
      // 创建区块的信息
      this.areaInfoList.push({
        name: null,
        remark: null,
        pickup_max_count: null,
        pie_max_count: null,
        is_increment: 2,
        order_deadline: null,
        appointment_days: null,
        index: null,
        can_skip_batch: 1,
        status: 2,
        coordinate_list: [],
      });
    },

    openEditDialog(index) {
      // 编辑, 打开弹窗
      this.areaInfo = this.areaInfoList[index];
      this.areaInfo.index = index;
      this.edit_area = true;
    },

    closeAreaDialog() {
      // 关闭弹窗
      this.edit_area = false;
      this.add_administrative_show = false;
      this.$refs.areaForm.resetFields();
    },

    submit() {
      // 提交保存
      if (this.areaInfoList[this.cehckInfoItem].id) {
        this.editSubmit();
        return;
      }
      if (!this.$store.state.globalData.areaData.warehouse_id) {
        this.$message({
          type: "warning",
          message: this.$t("PleaseSelectWarehouseFirst"),
        });
        return;
      }
      this.$refs.areaForm.validate((valid) => {
        if (!valid) return;
        let coordinate_list = [];
        this.areaInfoList[this.cehckInfoItem].coordinate_list.forEach((el) => {
          // 把经纬度转化为字符
          coordinate_list.push({ lat: el.lat + "", lon: el.lon + "" });
        });
        let data = {
          ...this.areaInfo,
          ...this.$store.state.globalData.areaData,
          coordinate_list: JSON.stringify([coordinate_list]),
        };
        this.$api.addLineArea(data).then((res) => {
          if (!res) return;
          this.$router.push({
            name: "Dispatch",
          });
        });
      });
    },

    // // 编辑区块函数 -- 开始
    // getLineAreaDetails() { // 拉取线路详情
    //   this.areaInfoList = [];
    //   this.$api.getLineAreaDetails(this.$route.query.id).then((res) => {
    //     if (!res) return;
    //     this.areaInfoList.push(res.data);
    //     this.demo(res.data.country, res.data.country);
    //     let arr = [];
    //     res.data.coordinate_list.forEach(item => {
    //       arr.push(new google.maps.LatLng(+item.lat, +item.lon));
    //     })
    //     this.drawPolygon(arr);
    //   });
    // },

    editSubmit() {
      // 编辑保存
      this.$refs.areaForm.validate((valid) => {
        if (!valid) return;
        let id = this.areaInfoList[this.cehckInfoItem].id;
        let coordinate_list = JSON.stringify(
          this.areaInfoList[this.cehckInfoItem].coordinate_list
        );
        let data = {
          ...this.areaInfoList[this.cehckInfoItem],
          coordinate_list: coordinate_list,
        };
        this.$api.editLineArea(id, data).then((res) => {
          if (!res) return;
          this.$router.push({
            name: "Dispatch",
          });
        });
      });
    },
    // 编辑区块函数 -- 结束

    // 添加行政区块 -- 开始
    getCountryAddress() {
      // 国家-省-市-区联动
      this.$api.getCountryAddress({ country: "CN" }).then((res) => {
        if (!res) return;
        this.countryArr = res.data;
        this.addressSelect.province = res.data;
      });
    },

    hanldeChangeProvince(val) {
      // 国家-省-市-区联动
      this.addressInfo.city = null;
      this.addressInfo.area = null;
      this.addressSelect.area = [];
      this.countryArr.forEach((el) => {
        if (el.name === val) {
          this.addressSelect.city = el.city;
        }
      });
    },

    hanldeChangeCity(val) {
      // 国家-省-市-区联动
      this.addressInfo.area = null;
      this.addressSelect.city.forEach((el) => {
        if (el.name === val) {
          this.addressSelect.area = el.district;
        }
      });
    },

    openAddressDialog() {
      // 打开添加行政区域弹窗
      this.add_administrative_show = true;
    },

    checkArea() {
      // 选择行政区后，拉取数据, 关闭弹窗
      let address =
        this.addressInfo.area ||
        this.addressInfo.city ||
        this.addressInfo.province ||
        this.addressInfo.country;
      this.demo("CN", address);
      this.createAreaItem();
      this.add_administrative_show = false;
    },
    // 添加行政区块 -- 结束

    // 判断区块重叠函数 -- 开始
    isSegmentsIntersectant(segA, segB) {
      // 判断两折线是否相交, true值表示相交
      const abc =
        (segA[0].lat - segB[0].lat) * (segA[1].lon - segB[0].lon) -
        (segA[0].lon - segB[0].lon) * (segA[1].lat - segB[0].lat);
      const abd =
        (segA[0].lat - segB[1].lat) * (segA[1].lon - segB[1].lon) -
        (segA[0].lon - segB[1].lon) * (segA[1].lat - segB[1].lat);
      if (abc * abd >= 0) {
        return false;
      }

      const cda =
        (segB[0].lat - segA[0].lat) * (segB[1].lon - segA[0].lon) -
        (segB[0].lon - segA[0].lon) * (segB[1].lat - segA[0].lat);
      const cdb = cda + abc - abd;
      return !(cda * cdb >= 0);
    },

    isPolygonsIntersectant(plyA, plyB) {
      // 判断多边形边界是否相交
      for (let i = 0, il = plyA.length; i < il; i++) {
        for (let j = 0, jl = plyB.length; j < jl; j++) {
          const segA = [plyA[i], plyA[i === il - 1 ? 0 : i + 1]];
          const segB = [plyB[j], plyB[j === jl - 1 ? 0 : j + 1]];
          if (this.isSegmentsIntersectant(segA, segB)) {
            return true;
          }
        }
      }
      return false;
    },

    isPointInPolygonBidirectional(plyA, plyB) {
      // 判断多边形是否存在点与区域的包含关系(A的点在B的区域内或B的点在A的区域内)
      const [pA, pB] = [[], []];
      plyA.forEach((item) => {
        pA.push(new this.BMap.Point(item.lon, item.lat));
      });

      plyB.forEach((item) => {
        pB.push(new this.BMap.Point(item.lon, item.lat));
      });

      let [a, b] = [false, false];
      a = pA.some((item) =>
        BMapLib.GeoUtils.isPointInPolygon(item, new BMap.Polygon(pB))
      );
      if (!a) {
        b = pB.some((item) =>
          BMapLib.GeoUtils.isPointInPolygon(item, new BMap.Polygon(pA))
        );
      }

      return a || b;
    },

    isPolygonsOverlap(plyA, plyB) {
      // 判断是否重叠
      // return this.isPolygonsIntersectant(plyA, plyB) || this.isPointInPolygonBidirectional(plyA, plyB);
      return this.isPolygonsIntersectant(plyA, plyB); // 暂只判断边界是否相交
    },
    // 判断区块重叠函数 -- 结束
  },
};
</script>

<style lang="less" scope>
.googleMap {
  position: relative;
  width: 100%;
  height: 70.6vh;
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
      cursor: pointer;
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
    .wrapper_content_item_check {
      // 选中效果
      border: 2px solid #53d9a9;
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
</style>
