<template>
  <!-- 流水记录 -->
  <div class="financial-flow-details">
    <div class="financial-flow-box">
      <h2 class="flow-title">{{ $t("FlowRecord") }}</h2>
      <div class="flow-number">
        <h4>{{ $t("serialNo") }}: {{ details.journal_no }}</h4>
        <h4>{{ $t("Created") }}:{{ details.created_at }}</h4>
      </div>
      <div>
        <h3>{{ $t("FlowDetails") }}</h3>
        <div class="flow-table">
          <el-table :data="tableData" border style="width: 70%">
            <el-table-column
              prop="object_no"
              :label="$t('orderNo')"
              width="180"
            >
            </el-table-column>
            <el-table-column
              prop="mode_name"
              :label="$t('TransactionType')"
              width="180"
            >
            </el-table-column>
            <el-table-column prop="actual_amount" :label="$t('fees')">
            </el-table-column>
          </el-table>
        </div>
      </div>
      <div class="flow-details">
        <div>
          <span>{{ $t("cargoOwnerNo") }}: </span><span>{{ details.code }}</span>
        </div>
        <div>
          <span>{{ $t("PaymentTypes") }}: </span
          ><span>{{ details.pay_type_name }}</span>
        </div>
        <div>
          <span>{{ $t("TransactionType") }}: </span
          ><span>{{ details.mode_name }}</span>
        </div>
        <div>
          <span>{{ $t("LumpSum") }}{{ currency_unit_symbol }} : </span
          ><span>{{ details.actual_amount }}</span>
        </div>
      </div>
      <div>
        <h3>{{ $t("AttachedScreenshot") }}</h3>
        <img
          :src="details.picture_list"
          alt=""
          class="flow-img"
          v-if="details.picture_list != ''"
        />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      currency_unit_symbol: null,
      details: {},
      tableData: [],
    };
  },
  created() {
    this.getAdvanceSetting();
    this.getDetails(this.$route.query.row);
  },
  methods: {
    // 获取单位值
    getAdvanceSetting() {
      this.$api.getAdvanceSetting().then((res) => {
        this.currency_unit_symbol = res.data.currency_unit_symbol; // 单价单位
      });
    },
    getDetails(rows) {
      console.log("424");
      console.log(rows);
      this.details = rows;
      this.tableData.push(rows);
    },
  },
};
</script>

<style lang="less" scoped>
.financial-flow-details {
  min-height: calc(100vh - 120px);
  background-color: #fff;
  .financial-flow-box {
    padding: 20px;
    .flow-title {
      text-align: center;
    }
    .flow-number {
      display: flex;
      justify-content: space-between;
    }
    .flow-table {
      margin-bottom: 20px;
    }
    .flow-details {
      display: grid;
      gap: 20px;
      grid-template-columns: repeat(3, 1fr);
    }
    .flow-img {
      width: 300px;
      height: 300px;
    }
  }
}
</style>