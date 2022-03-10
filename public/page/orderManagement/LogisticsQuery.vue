<template>
  <!-- 物流跟踪查询 -->
  <div class="logistics-query">
    <div class="content-box">
      <div class="right-box">
        <div class="order-no">
          <el-input
            :placeholder="placeholder"
            @keyup.enter.native="searchTrail"
            v-model="pageParams.order_no"
            style="width: 90%"
          >
            <el-select
              v-model="search_type"
              slot="prepend"
              :placeholder="$t('pleaseSelect')"
            >
              <el-option
                v-for="item in [
                  {
                    label: this.$t('orderTracking'),
                    value: 1,
                  },
                  {
                    label: this.$t('waybillQuery'),
                    value: 2,
                  },
                ]"
                :key="item.value"
                :label="item.label"
                :value="item.value"
              >
              </el-option>
            </el-select>
            <i
              @click="clearData"
              slot="suffix"
              class="el-icon-delete delete"
            ></i>
          </el-input>
          <!-- <el-button
            @click="clearData"
            :loading="this.$store.state.config.button_loading"
            type="text"
          >
          </el-button> -->
          <el-button
            @click="searchTrail"
            type="primary"
            :loading="this.$store.state.config.button_loading"
            >{{ $t("Search") }}</el-button
          >
        </div>
        <div class="logistics">
          <el-timeline>
            <el-timeline-item
              v-for="(activity, index) in activities"
              :key="index"
              :icon="activity.icon"
              :type="activity.type"
              :color="activity.color"
              :size="activity.size"
              :timestamp="activity.timestamp"
            >
              {{ activity.content }}
            </el-timeline-item>
          </el-timeline>
        </div>
      </div>
    </div>
    <!-- <div class="btn-group">
      <el-button
        @click="clearData"
        :loading="this.$store.state.config.button_loading"
        >{{ $t("清空") }}</el-button
      >
      <el-button
        @click="searchTrail"
        type="primary"
        :loading="this.$store.state.config.button_loading"
        :disabled="!this.pageParams.order_no"
        >{{ $t("查询") }}</el-button
      >
    </div> -->
  </div>
</template>

<script>
export default {
  name: "LogisticsQuery",
  data() {
    return {
      pageParams: {
        order_no: null,
      },
      activities: [],
      search_type: 1,
    };
  },
  computed: {
    placeholder() {
      if (this.search_type == 1) {
        return this.$t("PleaseEnterOrderNo");
      } else {
        return this.$t("pleaseEnterWaybillNo");
      }
    },
  },
  methods: {
    // 订单轨迹
    getOrderTrail() {
      if (!this.pageParams.order_no.replace(/ /g, "")) return;
      this.$api.getTrailOrder(this.pageParams.order_no).then((res) => {
        if (!res.data) return;
        this.activities = [];
        res.data.order_trail_list.forEach((ele) => {
          this.activities.push({
            content: ele.content,
            timestamp: `${ele.created_at}  ${ele.operator_name}`,
            color: "#0bbd87",
            order_no: ele.order_no,
          });
        });
        this.activities.reverse();
      });
    },
    // 运单轨迹
    getTrackingOrderTrail() {
      if (!this.pageParams.order_no.replace(/ /g, "")) return;
      this.$api.getTrailTrackingOrder(this.pageParams.order_no).then((res) => {
        if (!res.data) return;
        this.activities = [];
        res.data.tracking_order_trail_list.forEach((ele) => {
          this.activities.push({
            content: ele.content,
            timestamp: `${ele.created_at}  ${ele.operator_name}`,
            color: "#0bbd87",
            order_no: ele.order_no,
          });
        });
        this.activities.reverse();
      });
    },

    searchTrail() {
      if (this.search_type == 1) {
        this.getOrderTrail();
      } else {
        this.getTrackingOrderTrail();
      }
    },

    clearData() {
      this.pageParams.order_no = null;
      this.activities = [];
    },
  },
};
</script>

<style lang="less" scope>
.logistics-query {
  .content-box {
    width: 100%;
    height: 500px;
    display: flex;
    .right-box {
      width: 100%;
      border: 1px solid #c0c4cc;
      border-radius: 4px;
      box-sizing: border-box;
      padding: 0px 0px 20px;
      overflow: auto;
      .order-no {
        box-sizing: border-box;
        padding: 20px 40px 20px 40px;
        margin-bottom: 20px;
        border-bottom: 1px solid #c0c4cc;
        .delete {
          font-size: 30px;
          color: #909399;
          width: 30px;
          margin-top: 5px;
          cursor: pointer;
        }
      }
    }
  }
  .btn-group {
    margin-top: 20px;
  }
  .el-select {
    width: 150px;
  }
}
</style>
