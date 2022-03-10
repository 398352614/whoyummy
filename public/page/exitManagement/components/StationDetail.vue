<template>
  <div class="content_box">
    <el-form>
      <el-card class="top_box" shadow="never">
        <div class="base_box">
          <div class="item_box">
            <div class="item_text">
              {{ $t("Stopinfo") + `(${list.batch_no})` }}
            </div>
            <div class="item_label">
              <span>{{ $t("lineName") }}:</span>&nbsp;&nbsp;{{ list.line_name }}
            </div>
            <div class="item_label">
              <span>{{ $t("DisDate") }}:</span>&nbsp;&nbsp;{{
                list.execution_date
              }}
            </div>
            <div class="item_label">
              <span>{{ $t("OrderQty") }}:</span>&nbsp;&nbsp;{{
                list.order_count
              }}
            </div>
          </div>

          <div class="item_box">
            <div class="item_text">{{ $t("DriverInfo") }}:</div>
            <div class="item_label">
              <span>{{ $t("Name") }}:</span>&nbsp;&nbsp;{{ list.driver_name }}
            </div>
            <div class="item_label">
              <span>{{ $t("NumberPlate") }}:</span>&nbsp;&nbsp;{{ list.car_no }}
            </div>
            <div class="item_label">
              <span>{{ $t("Resttime") }}:</span>&nbsp;&nbsp;{{
                list.driver_rest_time
              }}
            </div>
          </div>

          <div class="item_box">
            <div class="item_text">{{ $t("DisAddress") }}</div>
            <div class="item_label">
              <span>{{ $t("ContactName") }}:</span>&nbsp;&nbsp;{{
                list.place_fullname
              }}
            </div>
            <div class="item_label">
              <span>{{ $t("Tel") }}:</span>&nbsp;&nbsp;{{ list.place_phone }}
            </div>
            <div class="item_label">
              <span>{{ $t("ZipCode") }}:</span>&nbsp;&nbsp;{{
                list.place_post_code
              }}
            </div>
            <div class="item_label">
              <span>{{ $t("Addressanddoorno") }}:</span>&nbsp;&nbsp;{{
                list.place_address
              }}
            </div>
            <div class="item_label">
              <span>{{ $t("City") }}:</span>&nbsp;&nbsp;{{ list.place_city }}
            </div>
          </div>

          <div class="item_box">
            <div class="item_text">{{ $t("Otherinfo") }}</div>
            <div class="item_label">
              <span>{{ $t("EstimatedArrivalTime") }}:</span>&nbsp;&nbsp;{{
                list.expect_arrive_time
              }}
            </div>
            <div class="item_label">
              <span>{{ $t("ActualArrivalTime") }}:</span>&nbsp;&nbsp;{{
                list.actual_arrive_time
              }}
            </div>
            <div class="item_label">
              <span>{{ $t("EstimatedDistance") }}:</span>&nbsp;&nbsp;{{
                list.expect_distance | addKM
              }}
            </div>
            <div class="item_label">
              <span>{{ $t("EstimatedTimeConsuming") }}:</span>&nbsp;&nbsp;{{
                list.expect_time_human
              }}
            </div>
            <div class="item_label">
              <span>{{ $t("AtcualTimeConsuming") }}:</span>&nbsp;&nbsp;{{
                list.actual_time_human
              }}
            </div>
          </div>
        </div>
      </el-card>
      <el-card class="list_box" shadow="never">
        <el-form-item :label="$t('OrderDetails')"></el-form-item>
        <el-table max-height="600px" border :data="list.orders">
          <el-table-column type="expand">
            <template slot-scope="props">
              <div class="list-tit">{{ $t("PackageList") }}</div>
              <el-table
                max-height="600px"
                border
                :data="props.row.package_list"
              >
                <el-table-column
                  align="center"
                  :label="$t('MerchantName')"
                  prop="name"
                >
                </el-table-column>
                <el-table-column
                  align="center"
                  :label="$t('parcelNo')"
                  prop="express_first_no"
                >
                </el-table-column>
                <el-table-column
                  align="center"
                  :label="$t('ExternalIdentification')"
                  prop="out_order_no"
                >
                </el-table-column>
                <el-table-column
                  align="center"
                  :label="$t('waybillPrintingNo')"
                  prop="sticker_no"
                >
                </el-table-column>
                <el-table-column
                  align="center"
                  :label="$t('DispatchResult')"
                  prop="status_name"
                >
                </el-table-column>
              </el-table>

              <div class="list-tit">{{ $t("MaterialList") }}</div>
              <el-table
                max-height="600px"
                border
                :data="props.row.material_list"
              >
                <el-table-column
                  align="center"
                  :label="$t('MerchantName')"
                  prop="name"
                >
                </el-table-column>
                <el-table-column
                  align="center"
                  :label="$t('MaterialCode')"
                  prop="code"
                >
                </el-table-column>
                <el-table-column
                  align="center"
                  :label="$t('ExternalIdentification')"
                  prop="out_order_no"
                >
                </el-table-column>
                <el-table-column
                  align="center"
                  :label="$t('actualEstimatedDispatches')"
                  prop="sticker_no"
                >
                  <template slot-scope="scope">
                    {{
                      scope.row.actual_quantity +
                      "/" +
                      scope.row.expect_quantity
                    }}
                  </template>
                </el-table-column>
              </el-table>
            </template>
          </el-table-column>
          <el-table-column
            :label="$t('OrderType')"
            prop="type_name"
            align="center"
          ></el-table-column>
          <el-table-column
            :label="$t('OrderStatus')"
            prop="status_name"
            align="center"
          ></el-table-column>
          <el-table-column
            :label="$t('Merchantinquiry')"
            prop="merchant.name"
            align="center"
          ></el-table-column>
          <el-table-column
            :label="$t('OrderNumber')"
            prop="order_no"
            align="center"
          >
            <template slot-scope="scope">
              <el-link
                :underline="false"
                @click="
                  hasPerm('order.index')
                    ? $router.push({
                        name: 'OrderList',
                        params: { order_no: scope.row.order_no },
                      })
                    : ''
                "
                >{{ scope.row.order_no }}</el-link
              >
            </template>
          </el-table-column>
          <el-table-column
            :label="$t('waybillNo')"
            prop="tracking_order_no"
            align="center"
          ></el-table-column>
          <el-table-column
            :label="$t('ExternalOrderNumber')"
            prop="out_order_no"
            align="center"
          ></el-table-column>
        </el-table>
      </el-card>
    </el-form>
  </div>
</template>

<script>
export default {
  name: "stationDetail",
  data() {
    return {
      list: {},
    };
  },
  activated() {
    this.getInfo();
  },
  created() {
    this.getInfo();
  },
  filters: {
    addMin(val) {
      if (!val) {
        return 0 + "min";
      } else {
        return val + "min";
      }
    },
    addKM(val) {
      if (!val) {
        return 0 + "km";
      } else {
        return (val / 1000).toFixed(2) + "km";
      }
    },
  },
  methods: {
    getInfo() {
      this.$api.getStationDetail(this.$route.query.id).then((res) => {
        if (!res) return;
        this.list = res.data;
      });
    },
  },
};
</script>

<style lang="less">
.content_box {
  .top_box {
    .base_box {
      display: grid;
      grid-template-columns: 1fr 1fr 1.5fr 1fr;
      .item_box {
        // flex: 1;
        // display: flex;
        // flex-direction: column;
        .item_text {
          font-size: 16px;
          font-weight: bold;
          margin-bottom: 15px;
        }
        .item_label {
          line-height: 2.2;
        }
      }
    }
  }
  .list_box {
    margin-top: 10px;
    .list-tit {
      margin: 10px 0;
      font-size: 16px;
      font-weight: bold;
    }
  }
}
</style>
