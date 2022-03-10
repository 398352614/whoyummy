<template>
  <div class="cash-recharge-list-details">
    <div class="tit">{{ $t("DetailsOnLine") }}</div>
    <div class="line-details">
      <span
        >{{ $t("rechargeCargoOwner") }}: &nbsp;&nbsp;{{
          info.merchant_name
        }}</span
      >
      <span>{{ $t("lineName") }}: &nbsp;&nbsp;{{ info.line_name }}</span>
      <span>{{ $t("PickupDriver") }}: &nbsp;&nbsp;{{ info.driver_name }}</span>
      <span
        >{{ $t("rechargedOÎrdersStatistics") }}: &nbsp;&nbsp;{{
          info.recharge_count
        }}</span
      >
    </div>

    <div class="tit">{{ $t("FinancialDetails") }}</div>
    <el-table
      max-height="600px"
      :data="info.recharge_list"
      v-loading="global_loading()"
      element-loading-text="loading..."
      border
      style="width: 100%"
    >
      <el-table-column
        align="center"
        prop="transaction_number"
        :label="$t('serialNo')"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="out_user_id"
        :label="$t('customerID')"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="recharge_amount"
        :label="$t('rechargeAmount') + '(' + currency_unit_symbol + ')'"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="recharge_time"
        :label="$t('rechargeTime')"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="date"
        :label="$t('Operation')"
        width="100"
      >
        <template slot-scope="scope">
          <el-button
            class="operation"
            @click="showDetails(scope.row.id)"
            size="small"
            type="text"
            >{{ $t("details") }}</el-button
          >
        </template>
      </el-table-column>
    </el-table>

    <div class="tit">{{ $t("ReviewTheCollectionResults") }}</div>
    <el-table
      max-height="600px"
      :data="tableData"
      v-loading="global_loading()"
      element-loading-text="loading..."
      border
      style="width: 100%"
    >
      <el-table-column
        align="center"
        prop="total_recharge_amount"
        :label="$t('TotalAountReceivable') + '(' + currency_unit_symbol + ')'"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="verify_recharge_amount"
        :label="$t('routeTurnInAmount') + '(' + currency_unit_symbol + ')'"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="verify_time"
        :label="$t('reviewTime')"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="verify_remark"
        :label="$t('reviewNotes')"
      >
      </el-table-column>
      <el-table-column
        align="center"
        prop="verify_name"
        :label="$t('operator')"
      >
      </el-table-column>
    </el-table>

    <!-- 详情 -->
    <el-dialog :visible.sync="detail_show">
      <div class="tit">{{ $t("imageData") }}</div>
      <div class="image-list">
        <el-image
          style="width: 100px; height: 100px"
          v-if="infoDetails.recharge_first_pic"
          :src="infoDetails.recharge_first_pic"
          fit="fill"
        >
        </el-image>
        <el-image
          style="width: 100px; height: 100px"
          v-if="infoDetails.recharge_second_pic"
          :src="infoDetails.recharge_second_pic"
          fit="fill"
        >
        </el-image>
        <el-image
          style="width: 100px; height: 100px"
          v-if="infoDetails.signature"
          :src="infoDetails.signature"
          fit="fill"
        >
        </el-image>
      </div>
      <div class="tit">{{ $t("remarksInformation") }}</div>
      <el-input type="info" :rows="4" disabled v-model="infoDetails.remark">
      </el-input>
    </el-dialog>
  </div>
</template>

<script>
import loading from "@/mixin/global_loading";

export default {
  // name: "FinancialDetails",
  mixins: [loading],
  data() {
    return {
      currency_unit_symbol: "", //价格单位
      tableData: [],
      info: {},
      detail_show: false,
      infoDetails: {},
    };
  },

  created() {
    this.getRechargeDetails();
    this.getAdvanceSetting();
  },
  methods: {
    // 获取单位值
    getAdvanceSetting() {
      this.$api.getAdvanceSetting().then((res) => {
        this.currency_unit_symbol = res.data.currency_unit_symbol; //单价单位
      });
    },
    getRechargeDetails() {
      // 列表
      this.$api.getRechargeDetails(this.$route.query.id).then((res) => {
        this.info = res.data;
        this.tableData.push(res.data);
      });
    },

    showDetails(id) {
      this.detail_show = true;
      this.$api.getRechargePic(id).then((res) => {
        this.infoDetails = res.data;
      });
    },
  },
};
</script>
<style lang="less" scoped>
.cash-recharge-list-details {
  padding: 20px 25px 10px;
  background: #ffffff;
  border: 1px solid rgba(228, 235, 241, 1);
  .tit {
    font-size: 18px;
    font-weight: 400;
    padding: 20px 0;
  }
  .line-details {
    span {
      margin-right: 20px;
    }
  }
  .image-list {
    padding: 0 10px;
    .el-image {
      border: 1px solid #ebeef5;
      &:hover {
        position: relative;
        z-index: 9999;
        transition: all 0.3s ease-in-out;
        transform: scale(5) translateX(30px);
      }
    }
  }
  .conditional {
    padding: 0px 0 10px;
  }
  .image-slot {
    width: 100px;
    height: 100px;
    display: flex;
    justify-content: center;
    align-items: center;
    border: 1px solid red;
  }
}
</style>
<style lang="less">
.cash-recharge-list-details {
  .conditional {
    .el-input__inner {
      border-radius: 0;
    }
  }
  .el-date-editor {
    width: 100%;
  }
  .el-select {
    width: 100%;
  }
}
</style>
