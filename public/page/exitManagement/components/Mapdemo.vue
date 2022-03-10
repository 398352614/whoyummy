<template>
  <div>
    <div class="googleMap">
      <div class="control_box">
        <div class="control_panel">
          <el-input
            clearable
            v-model="searchBoxValue"
            size="small"
            id="pac_input"
            type="text"
            :placeholder="$t('PleaseEnterAddress')"
          ></el-input>
        </div>
      </div>
      <gmap-map
        ref="gmap"
        id="gmap"
        @idle="checkMap"
        :noClear="false"
        :center="center"
        :zoom="center.zoom"
        style="width: 100%; height: 100%"
      >
        <gmap-info-window
          :options="infoOptions"
          :position="infoWindowPos"
          :opened="infoWinOpen"
          @closeclick="infoWinOpen = false"
        >
          <div v-html="infoContent"></div>
        </gmap-info-window>
      </gmap-map>
    </div>
  </div>
</template>

<script>
import { gmapApi } from "vue2-google-maps";

export default {
  name: "Mapdemo",
  data() {
    return {
      searchBoxValue: "",

      center: {
        lat: 52.37306,
        lng: 4.893,
        zoom: 10,
      },
      map: null,
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
    };
  },
  computed: {
    google: gmapApi,
    currentPosition() {
      return {
        lat: 1.38,
        lng: 103.8,
        zoom: 15,
      };
    }, // 当前地图位置
  },
  mounted() {
    this.getDate(this.$route.query.id);
    // this.initUserPosition();
  },
  methods: {
    checkMap() {
      console.log("地图加载完成");
    },
    setMapCenter(position) {
      this.$refs.gmap.$mapPromise.then((map) => {
        map.panTo(position); // 设置地图中心位置
        map.setZoom(position.zoom); // 设置层级
        this.infoWindowPos = {
          lat: position.lat,
          lng: position.lng,
        }; // 设置 信息窗位置
      });
    },
    initUserPosition() {
      if ("geolocation" in navigator) {
        window.navigator.geolocation.getCurrentPosition(
          this.sucess,
          this.error
        );
      } else {
        // eslint-disable-next-line no-alert
        alert("您的浏览器不支持地理位置");
      }
    },
    sucess(position) {
      const lat = position.coords.latitude;
      const lon = position.coords.longitude;
      this.currentPosition.lat = lat;
      this.currentPosition.lng = lon;
      this.currentPosition.zoom = 15;

      // 重新设置地图中心
      this.setMapCenter(this.currentPosition);
      // this.infoWinOpen = true; // 开启 信息窗
      // this.infoContent = '当前位置'; // 设置 信息窗内容
    },
    error(err) {
      if (err.code === 1) {
        // eslint-disable-next-line no-alert
        alert("无法获取您的位置");
      }
    },

    getDate() {
      const input = document.getElementById("pac_input");
      this.$refs.gmap.$mapPromise.then((map) => {
        // eslint-disable-next-line no-undef
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
        // eslint-disable-next-line no-undef
        const autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo("bounds", map);
        autocomplete.setFields([
          "address_components",
          "geometry",
          "icon",
          "name",
        ]);
        // eslint-disable-next-line no-undef
        const infowindow = new google.maps.InfoWindow();
        // eslint-disable-next-line no-undef
        const marker = new google.maps.Marker({
          map,
          // eslint-disable-next-line no-undef
          anchorPoint: new google.maps.Point(0, -29),
        });

        autocomplete.addListener("place_changed", () => {
          console.log(222);
          infowindow.close();
          marker.setVisible(false);
          const place = autocomplete.getPlace();
          if (!place.geometry) {
            // User entered the name of a Place that was not suggested and
            // pressed the Enter key, or the Place Details request failed.
            // eslint-disable-next-line no-alert
            // alert(`No details available for input: '${place.name}'`);
            return;
          }

          // If the place has a geometry, then present it on a map.
          if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
          } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17); // Why 17? Because it looks good.
          }
          marker.setPosition(place.geometry.location);
          marker.setVisible(true);

          const address = [];
          if (place.address_components) {
            place.address_components.forEach((item) => {
              if (item.long_name) {
                address.push(item.long_name);
              }
            });
          }
          infowindow.setContent(`
            ${address.join()}
            <div
              id="selectBtn"
              style="color: #fff;
              text-align: center;
              cursor: pointer;
              width: 36px;
              height: 20px;
              border-radius: 5px;
              background-color: #F6A50d;
            ">选中</div>
          `); // 设置 marker 内·容
          infowindow.open(map, marker);
          // 格式化数据
          const data = {};
          for (const item of place.address_components) {
            const key = item.types[0];
            const value = item.short_name;
            if (
              key === "street_number" ||
              key === "country" ||
              key === "postal_code"
            ) {
              data[key] = value;
            }
          }
          // 全部地址
          data.address = address.join();
          // 获取选择的地址
          setTimeout(() => {
            const selectBtn = document.querySelector("#selectBtn");
            selectBtn.addEventListener("click", () => {
              console.log(data, place.address_components);
            });
          }, 500);
        });
      });
    },
  },
};
</script>

<style lang="less" scoped>
.googleMap {
  position: relative;
  width: 100%;
  height: 80.6vh;
}

.control_box {
  position: relative;
  .control_panel {
    // position: absolute;
    // top: 10px;
    // left: 50%;
    margin-left: -300px;
    z-index: 5;
    font-size: 13px;
    cursor: pointer;
    color: #53d9a9;
  }
  .search_btn {
    position: absolute;
    top: 35px;
    left: 525px;
    z-index: 10;
  }
}

.el-input__inner {
  margin-top: 0;
  width: 500px;
  height: 34px;
}
</style>
<style lang="less">
.gmnoprint {
  display: none;
}
</style>
