<template>
  <div class="line-track">
    <div class="map-container">
      <!-- 顶部面板 -->
      <div class="container-top">
        <span @click="checkAll">查看全部</span>
        <span @click="handlerSystemTrack">系统规划路线</span>
        <span @click="handlerRealTrack">实际运动轨迹</span>
      </div>

      <!-- 地图容器 -->
      <div style="width: 100%; height: 100%" id="allmap"></div>

      <!-- 右侧面板 -->
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
              >{{ lineData.tour_no }}&nbsp;&nbsp;线路</span
            >
            <span
              v-if="!isWrapperClose"
              class="wrapper_header_text"
              @click="handlerCloseHeaderDesc"
            >
              查看
            </span>
          </div>
          <div v-show="isWrapperHeaderClose">
            <hr />
            <div class="station_desc" v-show="showDesc">
              <div class="station_text">
                <div class="desc_title">线路信息</div>
                <div class="desc_value">
                  <div>司机:&nbsp;&nbsp;{{ lineData.driver_name }}</div>
                  <div>车牌号:&nbsp;&nbsp;{{ lineData.car_no }}</div>
                  <div>行驶里程:&nbsp;&nbsp;{{ distance | addKM }}</div>
                  <div>总耗时:&nbsp;&nbsp;{{ costTime || 0 + "min" }}</div>
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
                    <span>姓名:</span>
                    <span class="card_text">{{ item.place_fullname }}</span>
                  </div>
                  <div>
                    <span>地址:</span>
                    <span>
                      {{ item.place_address }}
                    </span>
                  </div>
                  <div>
                    <span>预计里程:</span>
                    <span>{{ item.expect_distance | addKM }}</span>
                  </div>
                  <div>
                    <span>预计到达时间:</span>
                    <span>{{ item.expect_arrive_time }}</span>
                  </div>
                  <div>
                    <span>实际到达时间:</span>
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
                  v-for="(child, index) in item.event || []"
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
          {{ switchText ? "展开" : "收起" }}
        </div>
      </div>
    </div>

    <!-- 底部图例 -->
    <div class="line-track-tuglie">
      <div class="line-track-row">
        <div class="line-track-col">
          <span class="line-xt"></span>
          <span>系统规划轨迹</span>
        </div>
        <div class="line-track-col">
          <span class="not-arrived"></span>
          <span>未到达站点</span>
        </div>
      </div>
      <div class="line-track-row">
        <div class="line-track-col">
          <span class="line-sj"></span>
          <span>实际运动轨迹</span>
        </div>
        <div class="line-track-col">
          <span class="arrived"></span>
          <span>已到达站点</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import draggable from "vuedraggable";
import loadBaiduJS from "@/components/BaiduMap/loadBaiduJS";

export default {
  name: "LineTrack",
  components: {
    draggable,
  },
  data() {
    return {
      distance: "",
      costTime: "",
      realOptios: [],

      // heli
      lineData: {}, // 线路数据
      trackEventData: [], // 实际运动轨迹事件数据c

      showDesc: false,
      driver_name: "", // 司机名称
      car_no: "", // 车牌号
      line: "", // 线路 code
      map: null,

      options: [], // 缓存地址列表

      switchText: false,
      headerClose: false,
      selected: [], // 选择的数量

      realTrack: [],
      systemTrack: [],

      // 百度地图
      BMap: null,
    };
  },
  computed: {
    mode() {
      return this.$store.state.config.mode;
    },

    isWrapperClose() {
      return this.switchText !== false;
    },

    isWrapperHeaderClose() {
      return this.headerClose !== false;
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
    loadBaiduJS(
      `https://api.map.baidu.com/getscript?v=2.0&ak=feFgNMYSZ38zjACothgQ2NHVX9pnfj6g&s=1`
    )
      .then((res) => {
        this.BMap = new BMap.Map("allmap", { enableMapClick: false });
        this.BMap.centerAndZoom(new BMap.Point(116.404, 39.915), 4);
        this.BMap.enableScrollWheelZoom(true);
        this.getDate(this.$route.query.id, this.$route.query.tour_no, {
          model: this.mode,
        });
      })
      .catch((err) => {
        console.log("地图错误！请刷新重试/n" + err);
      });
  },
  methods: {
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
            message: "删除成功!",
          });
          this.getDate(this.$route.query.id);
        });
      });
    },

    // 面板事件--开始
    handlerClickItem(item) {
      // 点击面板
      if (this.mode === 1) return;
      let info = null;
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

      let content = "";
      if (!item["place_address"]) {
        content = this.getCarTrackInfoWindowContent(info); // 设置 信息窗内容
      } else {
        content = this.getInfoWindowContent(info); // 设置 信息窗内容
      }
      let point = new BMap.Point(+item.place_lon, +item.place_lat);
      let opts = {
        height: 100, // 信息窗口高度
        enableMessage: true, //设置允许信息窗发送短息
      };
      let infoWindow = new BMap.InfoWindow(content, opts); // 创建信息窗口对象
      this.BMap.openInfoWindow(infoWindow, point); //开启信息窗口
      this.BMap.panTo(point);
    },
    // 面板事件--结束

    // 地图坐标点事件--开始
    toggleInfoWindow(marker) {
      // 点击地图上的标记
      let info = null;
      let item = marker ? marker : marker.event[0];
      if (item["place_address"]) {
        info = {
          address: item.place_address,
          expect_arrive_time: item.expect_arrive_time,
          actual_arrive_time: item.actual_arrive_time,
          content: item.place_address,
          created_at: item.created_at,
        };
        return this.getInfoWindowContent(info);
      } else {
        let event = item.event[0];
        info = {
          address: event.address || item.address,
          content: event.content,
          created_at: event.created_at || item.created_at,
        };
        return this.getCarTrackInfoWindowContent(info);
      }
    },
    // 地图坐标点事件--结束

    // 面板操作--开始
    handlerCloseHeaderDesc() {
      // 打开查看面板
      this.headerClose = !this.headerClose;
    },

    handleSwitchClick() {
      // 收起右侧面板
      this.switchText = !this.switchText;
    },
    // 面板操作--结束

    //信息弹窗模板--开始
    getInfoWindowContent(data) {
      // 信息弹窗文字
      return `<div id="card">
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
    //信息弹窗模板--结束

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

    // 切换路线地图--开始
    checkAll() {
      //查看全部
      this.$store.commit("config/refreshMode", 1);
      this.$store.commit("config/refreshRouter", Math.random()); // refresh current router
    },

    handlerSystemTrack() {
      //系统规划路线
      this.$store.commit("config/refreshRouter", Math.random()); // 刷新当前路由
      this.$store.commit("config/refreshMode", 2);
    },

    handlerRealTrack() {
      //实际运动轨迹
      this.$store.commit("config/refreshRouter", Math.random()); // 刷新当前路由
      this.$store.commit("config/refreshMode", 3);
    },
    // 切换路线地图--结束

    async getDate(id, tourNo, { model, cleanRoute }) {
      const systemTrack = await this.$api.getLineDetail(id); // 线路详情
      const actualLine = await this.$api.getCarTrack({ tour_no: tourNo }); // 线路追踪
      this.lineData = systemTrack.data; // 线路总数据
      this.systemTrack = systemTrack.data.batchs; // systemTrack points

      // 实际线路数据处理
      let actualLineStation = actualLine.data.tour_event; // 实际线路所有站点

      // 取司机坐标--开始
      let driverData = actualLine.data.route_tracking;
      let driverPoint = null;
      if (driverData.length !== 0) {
        driverPoint = {
          lat: driverData[driverData.length - 1].lat,
          lon: driverData[driverData.length - 1].lon,
        };
      }
      // 取司机坐标--结束

      // 仓库数据--开始
      let ckData = {
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
      };
      // 仓库数据--结束

      // 行驶里程，总耗时--开始
      if (this.lineData.status > 4) {
        this.distance = this.lineData.actual_distance;
        this.costTime = this.lineData.actual_time_human; // 总耗时
      } else {
        this.distance = this.lineData.expect_distance;
        this.costTime = this.lineData.expect_time_human; // 总耗时
      }
      // 行驶里程，总耗时--结束

      if (model === 1) {
        // 查看全部
        this.realOptios = actualLineStation; // 设置面板信息
        this.addMArkerOnMap([ckData], "place_lon", "place_lat", "w");
        if (systemTrack.data.status !== 5) {
          this.addMArkerOnMap([driverPoint], "lon", "lat", "s", true);
        }

        this.drawActualLineByBaidu(systemTrack.data, actualLine.data); // 实际线路
        this.drawEstimatedLineByBaidu(systemTrack.data); // 系统规划线路
      } else if (model === 2) {
        // 系统规划线路
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
        this.drawEstimatedLineByBaidu(systemTrack.data); // 实际线路
      } else if (model === 3) {
        // 实际运动线路
        // 线路追踪
        this.realOptios = actualLineStation; // 设置面板信息

        this.addMArkerOnMap([ckData], "place_lon", "place_lat", "w");
        if (systemTrack.data.status !== 5) {
          this.addMArkerOnMap([driverPoint], "lon", "lat", "s", true);
        }

        this.drawActualLineByBaidu(systemTrack.data, actualLine.data);
      }
    },

    addMArkerOnMap(arr, lon, lat, type = null, onlyShowIcon = false) {
      // 在地图上设置坐标点
      const that = this;
      arr.forEach((el, index) => {
        let m = new BMap.Marker(new BMap.Point(+el[lon], +el[lat]));
        if (type === "w") {
          // 设置仓库图标
          m.setIcon(
            new BMap.Icon(
              require("../../../assets/img/ck.png"),
              new BMap.Size(27, 43),
              { anchor: new BMap.Size(13.5, 43) }
            )
          );
        } else if (type === "s") {
          m.setIcon(
            new BMap.Icon(
              "https://dev-tms.nle-tech.com/storage/siji.png",
              new BMap.Size(27, 43),
              { anchor: new BMap.Size(13.5, 43) }
            )
          );
        } else {
          m.setIcon(
            new BMap.Icon(
              "https://dev-tms.nle-tech.com/storage/water.png",
              new BMap.Size(31, 31),
              { anchor: new BMap.Size(15.5, 31) }
            )
          );
          // m.setLabel(this.getNumberLabel(el.sort_id || index))
        }
        if (!onlyShowIcon) {
          m.customData = {
            // 加入事件参数中
            data: el,
          };
          var opts = {
            height: 100, // 信息窗口高度
            enableMessage: true, //设置允许信息窗发送短息
          };
          var infoWindow = new BMap.InfoWindow(this.toggleInfoWindow(el), opts); // 创建信息窗口对象
          m.addEventListener("click", function (e) {
            that.BMap.openInfoWindow(
              infoWindow,
              new BMap.Point(+el[lon], +el[lat])
            ); //开启信息窗口
            console.log(e.target.customData.data);
          });
        }
        this.BMap.addOverlay(m);
      });
    },

    drawEstimatedLineByBaidu(lineData) {
      // 绘制系统线路路线
      let that = this;
      //仓库位置
      const warehouseWaypoint = new BMap.Point(
        +lineData.warehouse_lon,
        +lineData.warehouse_lat
      );
      var currentWaypoints = [];
      lineData.batchs.forEach((item) => {
        currentWaypoints.push(new BMap.Point(+item.place_lon, +item.place_lat));
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

        var driving = new BMap.DrivingRoute(this.BMap, {
          renderOptions: { map: this.BMap, autoViewport: true },
          drivingPolicy: "BMAP_DRIVING_POLICY_LEAST_DISTANCE",
          policy: "BMAP_DRIVING_POLICY_LEAST_DISTANCE",
          onPolylinesSet: function (Route) {
            //当线条添加完成时调用
            for (var i = 0; i < Route.length; i++) {
              var polyline = Route[i].getPolyline(); //获取线条遮挡物
              polyline.setStrokeColor("#6d93dc"); //设置颜色
              polyline.setStrokeWeight(5); //设置宽度
              polyline.setStrokeOpacity(1); //设置透明度
            }
            console.log(Route);
          },
          onMarkersSet: function (routes) {
            //当地图标记添加完成时调用
            console.log(routes, "routes");
            for (var i = 0; i < routes.length; i++) {
              //判断是否是途经点
              if (typeof routes[i].Um == "undefined") {
                that.BMap.removeOverlay(routes[i].marker); //删除起始默认图标
              } else {
                that.BMap.removeOverlay(routes[i].Um); //删除途经默认图标
              }
            }
          },
        });
        driving.search(beginWaypoint, endWaypoint, {
          waypoints: finalWaypoints,
        }); //waypoints表示途经点

        lastWaypoint = endWaypoint;
      }
      lineData.batchs.forEach((el, index) => {
        let m = new BMap.Marker(new BMap.Point(+el.place_lon, +el.place_lat));
        if (el.sort_id === "w") {
          // 设置仓库图标
          m.setIcon(
            new BMap.Icon(
              require("../../../assets/img/ck.png"),
              new BMap.Size(27, 43),
              { anchor: new BMap.Size(13.5, 43) }
            )
          );
        } else {
          m.setIcon(
            new BMap.Icon(
              "https://dev-tms.nle-tech.com/storage/water.png",
              new BMap.Size(31, 31),
              { anchor: new BMap.Size(15.5, 31) }
            )
          );
          m.setLabel(this.getNumberLabel(el.sort_id || index));
        }
        m.customData = {
          // 加入事件参数中
          data: el,
        };
        var opts = {
          height: 100, // 信息窗口高度
          enableMessage: true, //设置允许信息窗发送短息
        };
        var infoWindow = new BMap.InfoWindow(this.toggleInfoWindow(el), opts); // 创建信息窗口对象
        m.addEventListener("click", function (e) {
          that.BMap.openInfoWindow(
            infoWindow,
            new BMap.Point(+el.place_lon, +el.place_lat)
          ); //开启信息窗口
          console.log(e.target.customData.data);
        });
        this.BMap.addOverlay(m);
      });
    },

    drawActualLineByBaidu(lineData, actualData) {
      // 绘制实际线路路线
      //仓库位置
      const warehouseWaypoint = new BMap.Point(
        +lineData.warehouse_lon,
        +lineData.warehouse_lat
      );
      var currentWaypoints = [];
      actualData.route_tracking.forEach((item) => {
        currentWaypoints.push(new BMap.Point(+item.lon, +item.lat));
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

        let that = this;
        var driving = new BMap.DrivingRoute(this.BMap, {
          renderOptions: { map: this.BMap, autoViewport: true },
          drivingPolicy: "BMAP_DRIVING_POLICY_LEAST_DISTANCE",
          policy: "BMAP_DRIVING_POLICY_LEAST_DISTANCE",
          onPolylinesSet: function (Route) {
            //当线条添加完成时调用
            for (var i = 0; i < Route.length; i++) {
              var polyline = Route[i].getPolyline(); //获取线条遮挡物
              polyline.setStrokeColor("#dc704c"); //设置颜色
              polyline.setStrokeWeight(7); //设置宽度
              polyline.setStrokeOpacity(1); //设置透明度
            }
            console.log(Route);
          },
          onMarkersSet: function (routes) {
            //当地图标记添加完成时调用
            console.log(routes, "routes");
            for (var i = 0; i < routes.length; i++) {
              //判断是否是途经点
              if (typeof routes[i].Um == "undefined") {
                that.BMap.removeOverlay(routes[i].marker); //删除起始默认图标
              } else {
                that.BMap.removeOverlay(routes[i].Um); //删除途经默认图标
              }
            }
          },
        });
        driving.search(beginWaypoint, endWaypoint, {
          waypoints: finalWaypoints,
        }); //waypoints表示途经点

        lastWaypoint = endWaypoint;
      }
    },

    getNumberLabel(number) {
      // 设置marker面板数字
      var offsetSize = new BMap.Size(0, 0);
      var labelStyle = {
        color: "#fff",
        backgroundColor: "0.05",
        border: "0",
      };

      //不同数字长度需要设置不同的样式。
      switch ((number + "").length) {
        case 1:
          labelStyle.fontSize = "14px";
          offsetSize = new BMap.Size(10, 2);
          break;
        case 2:
          labelStyle.fontSize = "12px";
          offsetSize = new BMap.Size(8, 4);
          break;
        case 3:
          labelStyle.fontSize = "10px";
          offsetSize = new BMap.Size(6, 4);
          break;
        default:
          break;
      }

      var label = new BMap.Label(number, {
        offset: offsetSize,
      });
      label.setStyle(labelStyle);
      return label;
    },
  },
};
</script>

<style lang="less" scoped>
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
