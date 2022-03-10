<template>
  <!-- 客户地图 -->
  <div class="intelligent-dispatch">
    <div class="googleMap">
      <div
        :class="[
          'left_panel_btn',
          isHiddenPanel ? 'hidden_left_panel_btn' : '',
        ]"
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
        <el-table border :data="tableData">
          <el-table-column
            :label="$t('Name')"
            prop="place_fullname"
            align="center"
          ></el-table-column>
          <el-table-column
            :label="$t('Tel')"
            prop="place_phone"
            align="center"
          ></el-table-column>
          <el-table-column
            :label="$t('DetailedAddress')"
            prop="place_address"
            align="center"
            width="140"
          ></el-table-column>
        </el-table>
      </div>
      <!-- 地图 -->
      <gmap-map
        ref="gmap"
        id="gmap"
        :noClear="false"
        :center="center"
        :zoom="center.zoom"
        style="width: 100%; height: 100%"
      >
        <gmap-marker
          :key="index"
          v-for="(m, index) in markers"
          :icon="m.icon || null"
          :position="m.position"
          :draggable="m.draggable || true"
        >
        </gmap-marker>
      </gmap-map>
    </div>
    <iframe frameborder="0" name="downExcel" style="display: none"></iframe>
  </div>
</template>

<script>
import draggable from "vuedraggable";
import { gmapApi } from "vue2-google-maps";
import BasePagination from "@/components/BasePagination/BasePagination";

export default {
  name: "CustomerGoogle",
  components: {
    draggable,
    BasePagination,
  },
  data() {
    return {
      tableData: [], //列表
      center: {
        // 地图中心初始化
        lat: 52.37306,
        lng: 4.893,
        zoom: 9,
      },
      map: null,
      markers: [], // marker标记集合
      position: {},
      options: [], // 缓存地址列表
      obj: { lat: 0, lng: 0 },
      isHiddenPanel: false,
    };
  },
  computed: {
    google: gmapApi,
  },
  created() {
    this.center.zoom = this.$store.state.config.zoom || 9;
    this.tableData = this.$route.query.list;
    this.gettubiao();
  },
  methods: {
    // 标识
    gettubiao() {
      let arr = [];
      this.tableData.forEach((item) => {
        let pointer = {
          position: {
            lat: +item.place_lat,
            lng: +item.place_lon,
          },
        };
        arr.push(pointer);
      });
      this.markers = arr;
    },
    zoomChanged(e) {
      this.$store.commit("config/setZoom", e); // 存储当前缩放层次
    },
    // 展开
    hiddenLeftPanel() {
      this.isHiddenPanel = !this.isHiddenPanel;
    },
  },
};
</script>

<style lang="less" scoped>
.intelligent-dispatch {
  .googleMap {
    position: relative;
    width: 100%;
    height: 100vh;
    .left_panel_btn {
      position: absolute;
      left: 0;
      top: 10px;
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
      height: calc(100vh - 49px);
      top: 49px;
      z-index: 5;
      background: #fff;
      overflow: auto;
      .show_btn {
        position: relative;
        right: 60px;
      }
    }
    .hidden_left_panel {
      width: 0;
      transition: width 0.3s ease-in-out;
    }
  }
}
</style>
<style lang="less">
.gmnoprint {
  display: none;
}
// .intelligent-dispatch {
//   .el-table--enable-row-hover .el-table__body tr:hover > td {
//     background-color: rgb(255, 204, 70) !important;
//     color: #fff !important;
//   }
// }
</style>

