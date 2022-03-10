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

      <div style="width: 100%; height: 100%" id="MyMap"></div>

      <div
        class="wrapper"
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
              <span>{{ $t(PickupDate) }}</span>
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
        label-width="170px"
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
        <el-button @click="closeAreaDialog">{{ $t("Cancel") }}</el-button>
        <el-button
          type="primary"
          @click="submit"
          :loading="this.$store.state.config.button_loading"
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
        <el-button @click="closeAreaDialog">{{ $t("Cancel") }}</el-button>
        <el-button
          type="primary"
          @click="checkArea"
          :loading="this.$store.state.config.button_loading"
          >{{ $t("Confirm") }}</el-button
        >
      </div>
    </el-dialog>
    <!-- 选择行政区 -->
  </div>
</template>

<script>
import loadBaiduJS from "@/components/BaiduMap/loadBaiduJS";
import { bd09togcj02 } from "@/components/BaiduMap/bd09togcj02";

export default {
  name: "AreaPlanning",
  data() {
    const appointment_days_check = (rule, value, callback) => {
      if (value === "") {
        callback(new Error(this.$t("Required")));
      } else if (value > 30) {
        callback(new Error(this.$t("MustLessThanDays") + "!"));
      } else {
        callback(new Error(this.$t("Required")));
      }
    };
    return {
      // 何黎的属性
      latArr: [],
      stagingTags: [], // 暂存的标记对象数组
      edit_area: false, // 编辑弹窗
      areaInfoList: [], // 区域列表
      areaInfo: {},
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

      BMap: null,

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
        appointment_days: [
          { validator: appointment_days_check, trigger: "blur" },
        ],
      },
    };
  },
  watch: {
    "addressInfo.province": {
      handler(val) {
        this.addressInfo.city = null;
      },
      deep: true,
    },
  },
  mounted() {
    // this.positionToCountry(this.country.en);
    // 百度地图
    this.$nextTick(() => {
      loadBaiduJS(
        `https://api.map.baidu.com/getscript?v=2.0&ak=feFgNMYSZ38zjACothgQ2NHVX9pnfj6g&s=1`
      )
        .then((res) => {
          this.BMap = new BMap.Map("MyMap", { enableMapClick: false });
          this.BMap.centerAndZoom(new BMap.Point(116.404, 39.915), 7);
          this.BMap.enableScrollWheelZoom(true);
          this.BMap.addEventListener("click", (e) => {
            console.log(e, "map click");
            this.handleClick(e);
          });
          this.getCountryAddress();
          this.getLineAreaList();
        })
        .catch((err) => {
          console.log("地图错误！请刷新重试/n" + err);
        });
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
          el.coordinate_list = coordinate_list[0];
          console.log(coordinate_list);
          // el.coordinate_list.forEach((child) => {
          //   positionArr.push(new BMap.Point(+child.lon, +child.lat));
          // });

          var polygon = new BMap.Polygon(positionArr, {
            strokeColor: color,
            strokeWeight: 2,
            strokeOpacity: 0.8,
          }); //创建多边形
          polygon.enableMassClear(); // 允许被clearOverlays方法清除
          this.BMap.addOverlay(polygon);
          this.polygonArr.push(polygon);

          // this.drawPolygon(positionArr, '#0000FF'); // 绘制已存在区块
        });
        if (this.cehckInfoItem == 0 || this.cehckInfoItem) {
          this.BMap.setViewport(this.polygonArr[this.cehckInfoItem].getPath());
        } else {
          this.BMap.setViewport(this.polygonArr[0].getPath());
        }
      });
    },
    // 获取显示已存在区块线路 -- 结束

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
                let marker = new google.maps.Marker({
                  position: results[0].geometry.location,
                  map: map,
                });
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
          positionArr.push(new BMap.Point(+child.lon, +child.lat));
        });

        var polygon = new BMap.Polygon(positionArr, {
          strokeColor: color,
          strokeWeight: 2,
          strokeOpacity: 0.8,
        }); //创建多边形
        polygon.enableMassClear(); // 允许被clearOverlays方法清除
        this.BMap.addOverlay(polygon);
        this.polygonArr.push(polygon);
      });
    },

    clearAllPolygon() {
      // 清掉区块
      this.polygonArr = [];
      this.BMap.clearOverlays(); //清除地图覆盖物
    },

    demo(country, adress) {
      // 拉取行政区轮廓坐标
      if (!country) return;
      const that = this;
      var bdary = new BMap.Boundary();
      bdary.get(adress, function (rs) {
        //获取行政区域
        var count = rs.boundaries.length; //行政区域的点有多少个
        if (count === 0) {
          alert("未能获取当前输入行政区域");
          return;
        }
        var pointArray = [];
        for (var i = 0; i < count; i++) {
          var ply = new BMap.Polygon(rs.boundaries[i], {
            strokeWeight: 2,
            strokeColor: "#FF444D",
          }); //建立多边形覆盖物
          that.BMap.addOverlay(ply); //添加覆盖物
          pointArray.push(ply.getPath());
          // pointArray = pointArray.concat(ply.getPath());
        }
        console.log(pointArray, "diand");
        that.areaInfoList[that.areaInfoList.length - 1].coordinate_list =
          pointArray[0];
        that.cehckInfoItem = that.areaInfoList.length - 1;
        that.BMap.setViewport(pointArray[0]); //调整视野
      });
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
        this.latArr.push(e.point); // 暂存坐标
        if (this.latArr.length >= 3) {
          if (this.currentObj) {
            this.BMap.removeOverlay(this.currentObj);
          }
          this.delMarker();
          this.drawPolygon(this.latArr);
        } else {
          this.drawMarker(e.point);
          this.drawFlightPath(this.latArr);
        }
      }
    },

    handleDblclick() {
      // 保存区块
      if (this.block_local) {
        if (this.isOverlap(this.latArr)) {
          this.$message({
            type: "error",
            message: this.$t("TheAreasCannotOverlap"),
          });
        } else {
          this.createAreaItem(); // 创建区块信息
          // this.drawPolygon(this.latArr);
          this.latArr.forEach((el) => {
            // 坐标存入区块信息中
            this.areaInfoList[
              this.areaInfoList.length - 1
            ].coordinate_list.push({ lat: el.lat, lon: el.lng });
          });
          this.handleClickInfoItem(this.areaInfoList.length - 1); // 绘制
          this.delMarker(); // 清除标记及折线
          this.latArr = []; // 暂存坐标清空
          this.block_local = !this.block_local;
        }
      }
    },

    canclePreviousMarker() {
      // 撤销
      if (!this.latArr.length) return;
      this.latArr.splice(-1, 1); // 删除最后一个点
      if (this.latArr.length >= 3) {
        if (this.currentObj) {
          this.BMap.removeOverlay(this.currentObj);
        }
        this.drawPolygon(this.latArr);
        this.delMarker();
      } else {
        this.BMap.removeOverlay(this.currentObj);
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
      this.BMap.removeOverlay(this.currentObj);
    },

    isOverlap(arr) {
      // 判断重叠函数， true: 重叠
      let arr1 = [];
      arr.forEach((el) => {
        arr1.push({ lat: el.lat, lon: el.lng });
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
      var marker = new BMap.Marker(location);
      this.BMap.addOverlay(marker);
      this.stagingTags.push(marker);
    },

    drawFlightPath(pathArr) {
      // 画折线, 参数：坐标数组
      var polyline = new BMap.Polyline(pathArr, {
        strokeColor: "#FF444D",
        strokeWeight: 2,
        strokeOpacity: 0.8,
      });
      this.BMap.addOverlay(polyline);
      this.stagingTags.push(polyline);
    },

    drawPolygon(pathArr, color = "#FF444D") {
      // 画多边形，参数： 坐标数组
      let moren = "#0000FF";
      var polygon = new BMap.Polygon(pathArr, {
        strokeColor: color,
        strokeWeight: 2,
        strokeOpacity: 0.8,
      }); //创建多边形
      polygon.enableMassClear(); // 允许被clearOverlays方法清除
      this.BMap.addOverlay(polygon);
      this.currentObj = polygon;
    },

    delMarker() {
      // 清除坐标和折线标记
      this.stagingTags.forEach((e) => {
        this.BMap.removeOverlay(e);
      });
      this.stagingTags = [];
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
      this.add_administrative_show = false;
      this.edit_area = false;
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
          let point = bd09togcj02(el.lon || el.lng, el.lat);
          coordinate_list.push({ lat: point[1] + "", lon: point[0] + "" });
        });
        console.log(
          this.areaInfoList[this.cehckInfoItem].coordinate_list,
          coordinate_list
        );
        let data = {
          ...this.areaInfo,
          ...this.$store.state.globalData.areaData,
          coordinate_list: JSON.stringify(coordinate_list),
        };
        this.$api.addLineArea(data).then((res) => {
          if (!res) return;
          this.$router.push({ name: "linePlanningArea" });
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
          this.$router.push({ name: "linePlanningArea" });
        });
      });
    },

    baiduPointToGCPoint(data) {
      let newData = [];
      data.forEach((el) => {
        bd09togcj02();
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
}
</style>
