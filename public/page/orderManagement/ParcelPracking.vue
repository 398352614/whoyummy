<template>
  <!-- 包裹物流查询 -->
  <div class="parcel-pracking-query">
    <div class="query">
      <el-input
        :placeholder="$t('PleaseEnterParcelNo')"
        v-model="express_first_no"
        class="query-input"
        @keyup.enter.native="getPackageTrajectory"
      >
        <i slot="suffix" class="el-icon-delete delete" @click="clearData"></i>
      </el-input>
      <el-button
        class="query-bot"
        @click="getPackageTrajectory"
        type="primary"
        :loading="this.$store.state.config.button_loading"
        >{{ $t("Search") }}</el-button
      >
    </div>
    <div v-if="show">
      <div class="box">
        <div class="box-title">
          <div class="box-subtitle">
            <span class="subtitle">{{ $t("parcelNo ") }} :</span>
            <span class="subtitle">{{ trail.express_first_no }}</span>
          </div>
          <div>
            <span class="box-address">{{ trail.place_city }}</span>
            <img src="../../assets/img/arrow.png" alt="" class="box-img" />
            <span class="box-address">{{ trail.second_place_city }}</span>
          </div>
        </div>
        <div class="status">{{ trail.status_name }}</div>
      </div>
      <div class="timeline">
        <el-timeline>
          <el-timeline-item
            v-for="(activity, index) in trail.package_trail_list"
            :key="index"
            :color="getColor(index)"
          >
            <div class="timeline-created_at">
              <div :class="contentColor(index)">
                {{ activity.created_at }}
              </div>
            </div>
            <div class="timeline-content">
              <div :class="contentColor(index)">
                {{ activity.content }}
              </div>
            </div>
          </el-timeline-item>
        </el-timeline>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "ParcelPracking",
  data() {
    return {
      express_first_no: "", //包裹单号
      show: false,
      trail: {}, //轨迹
    };
  },
  activated() {
    if (this.$route.params.express_first_no) {
      // 接收路由参数
      this.express_first_no = this.$route.params.express_first_no;
      // this.$set(
      //   this.pageParams,
      //   "express_first_no",
      //   Number(this.$route.query.express_first_no)
      // );
      this.getPackageTrajectory();
    }
  },
  methods: {
    contentColor(index) {
      if (index == 0) {
        return "color";
      }
    },
    getPackageTrajectory() {
      if (this.express_first_no != "") {
        this.$api.getPackageTrajectory(this.express_first_no).then((res) => {
          if (res.code == 200) {
            this.trail = res.data;
            this.show = true;
          }
        });
      }
    },
    getColor(index) {
      let color = "";
      if (index == 0) {
        color = "red";
      }
      return color;
    },
    clearData() {
      this.express_first_no = "";
      this.show = false;
    },
  },
};
</script>

<style lang="less">
.parcel-pracking-query {
  padding: 10px 10%;
  background: #fff;
  min-height: calc(100vh - 140px);
  .query {
    margin: 20px 0px;
    display: flex;
    .query-input {
      margin-right: 10px;
      .delete {
        font-size: 30px;
        color: #909399;
        width: 30px;
        margin-top: 5px;
        cursor: pointer;
      }
    }
    .query-bot {
      padding-left: 4%;
      padding-right: 4%;
    }
  }

  .box {
    height: 120px;
    background: #f0f0f0;
    display: grid;
    grid-template-columns: auto auto;
    margin-bottom: 45px;
    .box-title {
      margin: 20px 60px;
      .box-subtitle {
        // margin-bottom: 10px;
        font-size: 15px;
        color: #8d8d8d;
      }
      .box-img {
        position: relative;
        top: 15px;
        margin: 0 20px;
      }
      .box-address {
        font-size: 1.5rem;
      }
    }

    .status {
      margin-left: 50%;
      color: red;
      font-size: 2rem;
      line-height: 120px;
    }
  }
  .timeline {
    padding-left: 200px;
    height: 500px;
    overflow: auto;
    .timeline-created_at {
      position: relative;
      right: 200px;
    }
    .timeline-content {
      position: relative;
      top: -15px;
    }
  }
}
.color {
  color: red;
}
</style>