<template>
  <div class="task-report-details">
    <div class="order-status">
      <el-steps :active="info.status" finish-status="success">
        <el-step :title="$t('Unassigned')"></el-step>
        <el-step :title="$t('Assigned')"></el-step>
        <el-step :title="$t('WaitingForDelivery')"></el-step>
        <el-step :title="$t('Distributing')"></el-step>
        <el-step :title="$t('dispatchCompleted')"></el-step>
      </el-steps>
    </div>
    <el-row class="task-info" :gutter="20">
      <el-col :span="4">
        <p>{{ $t("lineInfo") }}</p>
        <div>{{ $t("lineNumber") }}: {{ info.tour_no }}</div>
        <div>{{ $t("lineName") }}: {{ info.line_name }}</div>
        <div>{{ $t("DistributeDate") }}: {{ info.execution_date }}</div>
        <div>{{ $t("Stops") }}: {{ info.expect_batch_count }}</div>
      </el-col>
      <el-col :span="4">
        <p>{{ $t("DriverInfo") }}</p>
        <div>{{ $t("Driver") }}: {{ info.driver_name }}</div>
        <div>{{ $t("NumberPlate") }}: {{ info.car_no }}</div>
        <div>{{ $t("vehicleMileage") }}: {{ info.begin_distance | addKM }}</div>
        <div>{{ $t("returnMileage") }}: {{ info.end_distance | addKM }}</div>
        <!-- <div>{{$t('RestTime')}}: {{info.driver_rest_time}}</div> -->
      </el-col>
      <el-col :span="4">
        <p>{{ $t("EstimatedConsuming") }}</p>
        <div>
          {{ $t("EstimatedDistance") }}: {{ info.expect_distance | addKM }}
        </div>
        <div>
          {{ $t("EstimatedTimeConsuming") }}:
          {{ info.expect_time_human | secondsToTurn }}
        </div>
      </el-col>
      <el-col :span="4">
        <p>{{ $t("ActualConsuming") }}</p>
        <div>
          {{ $t("ActualDistance") }}: {{ info.actual_distance | addKM }}
        </div>
        <div>
          {{ $t("AtcualTimeConsuming") }}:
          {{ info.actual_time_human | secondsToTurn }}
        </div>
      </el-col>
      <el-col :span="4">
        <p>{{ $t("dispatchSituation") }}</p>
        <div>
          {{ $t("ExpectActualPickup") }}:
          {{ info.expect_pickup_quantity + "/" + info.actual_pickup_quantity }}
        </div>
        <div>
          {{ $t("ExpectActualDelivery") }}:
          {{ info.expect_pie_quantity + "/" + info.actual_pie_quantity }}
        </div>
        <div>
          {{ $t("estimatedActual") }}:
          {{
            info.expect_material_quantity + "/" + info.finish_material_quantity
          }}
        </div>
      </el-col>
      <!-- <el-col :span="4">
        <p>{{$t('银行卡支付情况')}}</p>
        <div>{{$t('到付('+ currency_unit_symbol +')（线路运费总计）')}}: {{info.card_settlement_amount}}</div>
        <div>{{$t('贴单费('+ currency_unit_symbol +')/单数')}}: {{info.card_sticker_amount + '/' + info.card_sticker_count}}</div>
        <div>{{$t('代收货款('+ currency_unit_symbol +')')}}: {{info.card_replace_amount}}</div>
        <div>{{$t('总计('+ currency_unit_symbol +')')}}: {{info.card_total_amount}}</div>
      </el-col>
      <el-col :span="4">
        <p>{{$t('现金支付情况')}}</p>
        <div>{{$t('到付('+ currency_unit_symbol +')（线路运费总计）')}}: {{info.cash_settlement_amount}}</div>
        <div>{{$t('贴单费('+ currency_unit_symbol +')/单数')}}: {{info.cash_sticker_amount + '/' + info.cash_sticker_count}}</div>
        <div>{{$t('代收货款('+ currency_unit_symbol +')')}}: {{info.cash_replace_amount}}</div>
        <div>{{$t('总计('+ currency_unit_symbol +')')}}: {{info.cash_total_amount}}</div>
      </el-col> -->
    </el-row>

    <div class="task-info">
      <p>{{ $t("paymentStatus") }}</p>
      <el-table
        max-height="600px"
        :data="[
          {
            name: this.$t('BankCardPayment'),
            settlement_amount: info.card_settlement_amount,
            replace_amount: info.card_replace_amount,
            sticker_amount: info.card_sticker_amount,
            sticker_count: info.card_sticker_count,
            delivery_amount: info.card_delivery_amount,
            delivery_count: info.card_delivery_count,
            total_amount: info.card_total_amount,
          },
          {
            name: this.$t('cashPayment'),
            settlement_amount: info.cash_settlement_amount,
            replace_amount: info.cash_replace_amount,
            sticker_amount: info.cash_sticker_amount,
            sticker_count: info.cash_sticker_count,
            delivery_amount: info.cash_delivery_amount,
            delivery_count: info.cash_delivery_count,
            total_amount: info.cash_total_amount,
          },
          {
            name: this.$t('thirdPartyPalancePayment'),
            settlement_amount: info.api_settlement_amount,
            replace_amount: info.api_replace_amount,
            sticker_amount: info.api_sticker_amount,
            sticker_count: info.api_sticker_count,
            delivery_amount: info.api_delivery_amount,
            delivery_count: info.api_delivery_count,
            total_amount: info.api_total_amount,
          },
        ]"
        border
        style="width: 100%"
      >
        <el-table-column prop="name" align="center" :label="$t('payment')">
        </el-table-column>
        <el-table-column
          prop="settlement_amount"
          align="center"
          :label="
            $t('COD') +
            '(' +
            currency_unit_symbol +
            ')' +
            $t('totalLineFreight')
          "
        >
        </el-table-column>
        <el-table-column
          prop="replace_amount"
          align="center"
          :label="$t('CollectMoney') + '(' + currency_unit_symbol + ')'"
        >
        </el-table-column>
        <el-table-column
          align="center"
          :label="
            $t('printWaybillFee') +
            '(' +
            currency_unit_symbol +
            ')/' +
            $t('oddNumber')
          "
        >
          <template slot-scope="scope">
            {{ scope.row.sticker_amount + "/" + scope.row.sticker_count }}
          </template>
        </el-table-column>
        <el-table-column
          align="center"
          :label="
            $t('pickFee') + '(' + currency_unit_symbol + ')/' + $t('oddNumber')
          "
        >
          <template slot-scope="scope">
            {{ scope.row.delivery_amount + "/" + scope.row.delivery_count }}
          </template>
        </el-table-column>
        <el-table-column
          prop="total_amount"
          align="center"
          :label="$t('total') + '(' + currency_unit_symbol + ')'"
        >
        </el-table-column>
      </el-table>
    </div>

    <div class="warehouse">
      <div class="warehouse-left">
        <p>{{ warehouseInfo.name }}{{ $t("Warehouse") }}</p>
        <div>{{ $t("Tel") }}: {{ warehouseInfo.phone }}</div>
        <div>{{ $t("ZipCode") }}: {{ warehouseInfo.post_code }}</div>
        <div>{{ $t("City") }}: {{ warehouseInfo.city }}</div>
        <div>{{ $t("DoorNo") }}: {{ warehouseInfo.house_number }}</div>
        <div>{{ $t("Street") }}: {{ warehouseInfo.street }}</div>
        <div>{{ $t("DetailedAddress") }}: {{ warehouseInfo.address }}</div>
        <div>
          {{ $t("warehouseSign") }}:
          <img
            v-if="info.begin_signature"
            class="scaleImg"
            width="100px"
            height="100px"
            :src="info.begin_signature"
            alt=" "
          />
        </div>
      </div>
      <div class="warehouse-right">
        <div class="warehouse-title">
          <span class="warehouse-title-head"
            >{{ $t("outboundPackage") }} ({{
              warehouseInfo.package_list.length
            }})</span
          >
          <span
            class="more"
            v-if="warehouseInfo.package_list.length > 10"
            @click="seeMore(1)"
            >{{ $t("ViewMore")
            }}<i
              :class="[outStatus ? 'el-icon-arrow-up' : 'el-icon-arrow-down']"
            ></i
          ></span>
        </div>
        <div class="warehouse-table">
          <el-table
            max-height="600px"
            :data="outStock"
            border
            style="width: 100%"
          >
            <el-table-column type="index" align="center" width="80">
              <template slot="header">
                <span>{{ $t("Number") }}</span>
              </template>
            </el-table-column>
            <el-table-column
              prop="type_name"
              align="center"
              :label="$t('WaybillType')"
            >
            </el-table-column>
            <el-table-column
              prop="order_no"
              align="center"
              :label="$t('OrderNumber')"
            >
            </el-table-column>
            <el-table-column
              prop="out_order_no"
              align="center"
              :label="$t('ExternalOrderNumber')"
            >
            </el-table-column>
            <el-table-column
              prop="express_first_no"
              align="center"
              :label="$t('parcelNo')"
            >
            </el-table-column>
            <el-table-column
              prop="status_name"
              align="center"
              :label="$t('OrderStatus')"
            >
            </el-table-column>
            <el-table-column prop="remark" align="center" :label="$t('Remark')">
            </el-table-column>
          </el-table>
        </div>
        <div class="warehouse-title">
          <span class="warehouse-title-head"
            >{{ $t("DeliveryMaterials") }} ({{
              warehouseInfo.material_list.length
            }})</span
          >
        </div>
        <div class="warehouse-table">
          <el-table
            max-height="600px"
            :data="warehouseInfo.material_list"
            border
            style="width: 100%"
          >
            <el-table-column type="index" align="center" width="80">
              <template slot="header">
                <span>{{ $t("Number") }}</span>
              </template>
            </el-table-column>
            <el-table-column
              prop="name"
              align="center"
              :label="$t('MaterialName')"
            >
            </el-table-column>
            <el-table-column prop="code" align="center" :label="$t('Code')">
            </el-table-column>
            <el-table-column
              prop="expect_quantity"
              align="center"
              :label="$t('PreDispatchQuantity')"
            >
            </el-table-column>
            <el-table-column
              prop="actual_quantity"
              align="center"
              :label="$t('ActualQuantity')"
            >
            </el-table-column>
          </el-table>
        </div>
      </div>
    </div>

    <!-- 站点信息 -->
    <div class="warehouse" v-for="(item, index) in orderInfo" :key="index">
      <div class="warehouse-left">
        <p>{{ item.sort_id }}. {{ item.name + "(" + item.batch_no + ")" }}</p>
        <div>{{ $t("Tel") }}: {{ item.phone }}</div>
        <div>{{ $t("ZipCode") }}: {{ item.post_code }}</div>
        <div>{{ $t("City") }}: {{ item.city }}</div>
        <div>{{ $t("DoorNo") }}: {{ item.house_number }}</div>
        <div>{{ $t("Street") }}: {{ item.street }}</div>
        <div>{{ $t("DetailedAddress") }}: {{ item.address }}</div>
        <p>{{ $t("DeliveryTime") }}</p>
        <div>
          {{ $t("EstimatedArrivalTime") }}: {{ item.expect_arrive_time }}
        </div>
        <div>{{ $t("ActualArrivalTime") }}: {{ item.actual_arrive_time }}</div>
        <div>
          {{ $t("EstimatedDistance") }}: {{ item.expect_distance | addKM }}
        </div>
        <div>
          {{ $t("ActualDistance") }}: {{ item.actual_distance | addKM }}
        </div>
        <div>
          {{ $t("EstimatedTimeConsuming") }}:
          {{ item.expect_time_human | secondsToTurn }}
        </div>
        <div>
          {{ $t("AtcualTimeConsuming") }}:
          {{ item.actual_time_human | secondsToTurn }}
        </div>
        <div>{{ $t("payment") }}: {{ item.pay_type_name }}</div>
        <el-table
          max-height="600px"
          :data="[
            {
              sticker_amount: item.sticker_amount,
            },
            {
              delivery_amount: item.delivery_amount,
            },
            {
              replace_amount: item.replace_amount,
            },
            {
              settlement_amount: item.settlement_amount,
            },
          ]"
          border
          style="width: 100%"
        >
          <el-table-column prop="item" align="center" :label="$t('Charges')">
            <template slot-scope="scope">
              <span v-if="scope.row['sticker_amount'] !== undefined">{{
                $t("printWaybillFee")
              }}</span>
              <span v-if="scope.row['delivery_amount'] !== undefined">{{
                $t("pickFee")
              }}</span>
              <span v-if="scope.row['replace_amount'] !== undefined">{{
                $t("CollectMoney")
              }}</span>
              <span v-if="scope.row['settlement_amount'] !== undefined">{{
                $t("freigheCost")
              }}</span>
            </template>
          </el-table-column>
          <el-table-column
            prop="out_order_no"
            align="center"
            :label="$t('cost') + currency_unit_symbol"
          >
            <template slot-scope="scope">
              <span v-if="scope.row['sticker_amount'] !== undefined">{{
                scope.row.sticker_amount
              }}</span>
              <span v-if="scope.row['delivery_amount'] !== undefined">{{
                scope.row.delivery_amount
              }}</span>
              <span v-if="scope.row['replace_amount'] !== undefined">{{
                scope.row.replace_amount
              }}</span>
              <span v-if="scope.row['settlement_amount'] !== undefined">{{
                scope.row.settlement_amount
              }}</span>
            </template>
          </el-table-column>
        </el-table>
        <div>
          {{ $t("CustomerSign") }}:
          <img
            v-if="item.signature"
            class="scaleImg"
            width="100px"
            height="100px"
            :src="item.signature"
            alt=" "
          />
        </div>
        <div>
          {{ $t("paymentVoucher") }}:
          <img
            v-if="item.pay_picture"
            class="scaleImg"
            width="100px"
            height="100px"
            :src="item.pay_picture"
            alt=" "
          />
        </div>
      </div>
      <div class="warehouse-right">
        <div class="warehouse-title">
          <span class="warehouse-title-head"
            >{{ $t("OrderInfo") }} ({{ item.order_list.length }})</span
          >
          <el-button
            class="pack-order-details"
            size="small"
            @click="openPackageDialog(item.package_list)"
            >{{ $t("packageDetails") }}</el-button
          >
        </div>
        <div class="warehouse-table">
          <el-table
            max-height="600px"
            :data="item.order_list"
            border
            style="width: 100%"
          >
            <el-table-column type="index" align="center" width="80">
              <template slot="header">
                <span>{{ $t("Number") }}</span>
              </template>
            </el-table-column>
            <el-table-column
              prop="type_name"
              align="center"
              :label="$t('OrderType')"
            >
            </el-table-column>
            <el-table-column
              prop="tracking_order_no"
              align="center"
              :label="$t('waybillNo')"
            >
            </el-table-column>
            <el-table-column
              prop="order_no"
              align="center"
              :label="$t('OrderNumber')"
            >
            </el-table-column>
            <el-table-column
              prop="out_user_id"
              align="center"
              :label="$t('customerNumber')"
            >
            </el-table-column>
            <el-table-column
              prop="status_name"
              align="center"
              :label="$t('OrderStatus')"
            >
            </el-table-column>
            <el-table-column
              prop="sticker_amount"
              align="center"
              :label="
                $t('printWaybillFee') +
                '(' +
                currency_unit_symbol +
                ')/' +
                $t('oddNumber')
              "
            >
              <template slot-scope="scope">
                {{ scope.row.sticker_amount + "/" + scope.row.sticker_count }}
              </template>
            </el-table-column>
            <el-table-column
              prop="sticker_amount"
              align="center"
              :label="
                $t('pickFee') +
                '(' +
                currency_unit_symbol +
                ')/' +
                $t('oddNumber')
              "
            >
              <template slot-scope="scope">
                {{ scope.row.delivery_amount + "/" + scope.row.delivery_count }}
              </template>
            </el-table-column>
            <el-table-column
              prop="replace_amount"
              align="center"
              :label="$t('PreActualPayment') + '(' + currency_unit_symbol + ')'"
            >
              <template slot-scope="scope">
                {{
                  scope.row.expect_replace_amount +
                  "/" +
                  scope.row.actual_replace_amount
                }}
              </template>
            </el-table-column>
            <el-table-column
              prop="settlement_amount"
              align="center"
              :label="
                $t('PreActualShipping') + '(' + currency_unit_symbol + ')'
              "
            >
              <template slot-scope="scope">
                {{
                  scope.row.expect_settlement_amount +
                  "/" +
                  scope.row.actual_settlement_amount
                }}
              </template>
            </el-table-column>
            <el-table-column
              prop="actual_total_amount"
              align="center"
              :label="$t('TotalPaid') + '(' + currency_unit_symbol + ')'"
            >
            </el-table-column>

            <el-table-column prop="remark" align="center" :label="$t('Remark')">
            </el-table-column>
          </el-table>
        </div>
        <div class="warehouse-title">
          <span class="warehouse-title-head"
            >{{ $t("DeliveryMaterials") }} ({{
              item.material_list.length
            }})</span
          >
        </div>
        <div class="warehouse-table">
          <el-table
            max-height="600px"
            :data="item.material_list"
            border
            style="width: 100%"
          >
            <el-table-column type="index" align="center" width="80">
              <template slot="header">
                <span>{{ $t("Number") }}</span>
              </template>
            </el-table-column>
            <el-table-column
              prop="name"
              align="center"
              :label="$t('MaterialName')"
            >
            </el-table-column>
            <el-table-column
              prop="order_no"
              align="center"
              :label="$t('SubordinateOrders')"
            >
            </el-table-column>
            <el-table-column prop="code" align="center" :label="$t('Code')">
            </el-table-column>
            <el-table-column
              prop="out_order_no"
              align="center"
              :label="$t('ExternalIdentification')"
            >
            </el-table-column>
            <el-table-column
              prop="expect_quantity"
              align="center"
              :label="$t('PreDispatchQuantity')"
            >
            </el-table-column>
            <el-table-column
              prop="actual_quantity"
              align="center"
              :label="$t('QuantityDelivered')"
            >
            </el-table-column>
          </el-table>
        </div>
        <div class="warehouse-title">
          <span class="warehouse-title-head">{{ $t("DriverRemark") }}</span>
        </div>
        <el-input
          type="textarea"
          :autosize="{ minRows: 4, maxRows: 10 }"
          disabled
          v-model="item.cancel_remark"
        >
        </el-input>
        <div class="auth-text">
          <img
            v-if="item.cancel_picture"
            class="scaleImg"
            width="100px"
            :src="item.cancel_picture"
            alt=" "
          />
        </div>
        <div
          class="warehouse-title auth-text"
          v-if="item.auth_fullname && item.auth_birth_date"
        >
          <span class="warehouse-title-head">{{
            $t("TobaccoAlcoholRemarks")
          }}</span>
        </div>
        <div class="auth-text" v-if="item.auth_fullname">
          {{ $t("SigneeName") }}: {{ item.auth_fullname }}
        </div>
        <div class="auth-text" v-if="item.auth_birth_date">
          {{ $t("DateOfBirthSignatory") }}: {{ item.auth_birth_date }}
        </div>
      </div>
    </div>

    <div class="warehouse" v-if="warehousingInfo">
      <div class="warehouse-left">
        <p>{{ warehousingInfo.name }}{{ $t("Warehouse") }}</p>
        <div>{{ $t("Tel") }}: {{ warehousingInfo.phone }}</div>
        <div>{{ $t("ZipCode") }}: {{ warehousingInfo.post_code }}</div>
        <div>{{ $t("City") }}: {{ warehousingInfo.city }}</div>
        <div>{{ $t("DoorNo") }}: {{ warehousingInfo.house_number }}</div>
        <div>{{ $t("Street") }}: {{ warehousingInfo.street }}</div>
        <div>{{ $t("DetailedAddress") }}: {{ warehousingInfo.address }}</div>
        <div>
          {{ $t("EstimatedArrivalTime") }}:
          {{ warehousingInfo.warehouse_expect_arrive_time }}
        </div>
        <div>
          {{ $t("ActualArrivalTime") }}:
          {{ warehousingInfo.warehouse_actual_arrive_time }}
        </div>
        <div>
          {{ $t("EstimatedDistance") }}:
          {{ warehousingInfo.warehouse_expect_distance | addKM }}
        </div>
        <div>
          {{ $t("ActualDistance") }}:
          {{ warehousingInfo.warehouse_actual_distance | addKM }}
        </div>
        <div>
          {{ $t("EstimatedTimeConsuming") }}:
          {{ warehousingInfo.warehouse_expect_time_human }}
        </div>
        <div>
          {{ $t("AtcualTimeConsuming") }}:
          {{ warehousingInfo.warehouse_actual_time_human }}
        </div>
        <div>
          {{ $t("warehouseSign") }}:
          <img
            v-if="info.end_signature"
            class="scaleImg"
            width="100px"
            height="100px"
            :src="info.end_signature"
            alt=" "
          />
        </div>
      </div>
      <div class="warehouse-right">
        <div class="warehouse-title">
          <span class="warehouse-title-head"
            >{{ $t("InboundPackage") }} ({{
              warehousingInfo.package_list.length
            }})</span
          >
          <span
            class="more"
            v-if="warehousingInfo.package_list.length > 10"
            @click="seeMore(2)"
            >{{ $t("ViewMore")
            }}<i
              :class="[putStatus ? 'el-icon-arrow-up' : 'el-icon-arrow-down']"
            ></i
          ></span>
        </div>
        <div class="warehouse-table">
          <el-table
            max-height="600px"
            :data="putStock"
            border
            style="width: 100%"
          >
            <el-table-column type="index" align="center" width="80">
              <template slot="header">
                <span>{{ $t("Number") }}</span>
              </template>
            </el-table-column>
            <el-table-column
              prop="type_name"
              align="center"
              :label="$t('WaybillType')"
            >
            </el-table-column>
            <el-table-column
              prop="order_no"
              align="center"
              :label="$t('OrderNumber')"
            >
            </el-table-column>
            <el-table-column
              prop="out_order_no"
              align="center"
              :label="$t('ExternalOrderNumber')"
            >
            </el-table-column>
            <el-table-column
              prop="express_first_no"
              align="center"
              :label="$t('parcelNo')"
            >
            </el-table-column>
            <el-table-column
              prop="status_name"
              align="center"
              :label="$t('OrderStatus')"
            >
            </el-table-column>
            <el-table-column prop="remark" align="center" :label="$t('Remark')">
            </el-table-column>
          </el-table>
        </div>
        <div class="warehouse-title">
          <span class="warehouse-title-head"
            >{{ $t("DeliveryMaterials") }} ({{
              warehousingInfo.material_list.length
            }})</span
          >
        </div>
        <div class="warehouse-table">
          <el-table
            max-height="600px"
            :data="warehousingInfo.material_list"
            border
            style="width: 100%"
          >
            <el-table-column type="index" align="center" width="80">
              <template slot="header">
                <span>{{ $t("Number") }}</span>
              </template>
            </el-table-column>
            <el-table-column
              prop="name"
              align="center"
              :label="$t('MaterialName')"
            >
            </el-table-column>
            <el-table-column prop="code" align="center" :label="$t('Code')">
            </el-table-column>
            <el-table-column
              prop="out_order_no"
              align="center"
              :label="$t('ExternalIdentification')"
            >
            </el-table-column>
            <el-table-column
              prop="expect_quantity"
              align="center"
              :label="$t('PreDispatchQuantity')"
            >
            </el-table-column>
            <el-table-column
              prop="finish_quantity"
              align="center"
              :label="$t('QuantityDelivered')"
            >
            </el-table-column>
          </el-table>
        </div>
      </div>
    </div>
    <!-- 包裹详情 -->
    <el-dialog :title="$t('packageDetails')" :visible.sync="openPackageDetails">
      <el-table max-height="600px" border :data="packageList">
        <el-table-column
          :label="$t('waybillNo')"
          prop="tracking_order_no"
        ></el-table-column>
        <el-table-column
          :label="$t('parcelNo')"
          prop="express_first_no"
        ></el-table-column>
        <el-table-column
          :label="$t('MerchantType')"
          prop="type_name"
        ></el-table-column>
        <el-table-column
          :label="$t('CurrentState')"
          prop="merchant_status_name"
        ></el-table-column>
        <el-table-column :label="$t('waybillPrintingNo')" prop="sticker_no">
          <template slot-scope="scope">
            {{ scope.row.sticker_no || "/" }}
          </template>
        </el-table-column>
        <el-table-column
          :label="$t('printWaybillFee') + '(' + currency_unit_symbol + ')'"
          prop="sticker_amount"
        >
          <template slot-scope="scope">
            {{ +scope.row.sticker_amount || "/" }}
          </template>
        </el-table-column>
      </el-table>
    </el-dialog>
    <!-- 包裹详情 -->
  </div>
</template>

<script>
export default {
  // name: "TaskReportDetails",
  data() {
    return {
      currency_unit_symbol: "", //单位
      info: {},
      warehouseInfo: {
        package_list: [],
        material_list: [],
      },
      orderInfo: [],
      warehousingInfo: {
        package_list: [],
        material_list: [],
      },
      outStatus: false,
      putStatus: false,
      openPackageDetails: false, // 包裹详情弹窗
      packageList: [], //包裹详情数据
    };
  },
  created() {
    this.getReportDetails();
    this.getAdvanceSetting();
  },
  computed: {
    outStock() {
      let arr = [];
      if (this.warehouseInfo.package_list.length > 10 && !this.outStatus) {
        arr = this.warehouseInfo.package_list.filter(
          (item, index) => index < 10
        );
        return arr;
      }
      return this.warehouseInfo.package_list;
    },
    putStock() {
      let arr = [];
      if (this.warehousingInfo.package_list.length > 10 && !this.putStatus) {
        arr = this.warehousingInfo.package_list.filter(
          (item, index) => index < 10
        );
        return arr;
      }
      return this.warehousingInfo.package_list;
    },
    unit() {
      return this.$store.state.globalData.unit;
    },
  },
  filters: {
    secondsToTurn(value) {
      // 待删
      if (!value) return 0 + "min";
      return value;
      // eslint-disable-next-line
      // let hou = value / 60;
      // eslint-disable-next-line
      // let zs = parseInt(hou);
      // eslint-disable-next-line
      // let xs = hou - zs;
      // eslint-disable-next-line
      // return zs + 'h' + Math.round(xs * 60) + 'min';
    },
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
  methods: {
    // 获取单位值
    getAdvanceSetting() {
      this.$api.getAdvanceSetting().then((res) => {
        this.currency_unit_symbol = res.data.currency_unit_symbol; //单价单位
      });
    },
    getReportDetails() {
      this.$api.getReportDetails(this.$route.query.id).then((res) => {
        this.info = res.data;
        this.warehouseInfo = res.data.in_warehouse;
        this.warehousingInfo = res.data.out_warehouse;
        this.orderInfo = res.data.detail_list;
      });
    },
    seeMore(who) {
      if (who === 1) {
        this.outStatus = !this.outStatus;
      } else {
        this.putStatus = !this.putStatus;
      }
    },
    openPackageDialog(val) {
      this.openPackageDetails = true;
      this.packageList = val;
    },
  },
};
</script>

<style lang="less">
.task-report-details {
  padding: 20px 25px 10px;
  .task-info {
    padding: 20px;
    margin-bottom: 23px;
    background: #ffffff;
    box-sizing: border-box;
    // border: 1px solid rgba(228, 235, 241, 1);
    div {
      margin: 15px 0;
    }
    .el-table {
      .el-table__header-wrapper {
        margin: 0;
      }
      .el-table__body-wrapper {
        margin: 0;
      }
      .cell {
        margin: 0;
      }
    }
  }
  .order-status {
    padding: 20px;
  }
  .warehouse {
    overflow: hidden;
    margin-bottom: 23px;
    .warehouse-left {
      display: inline-block;
      box-sizing: border-box;
      width: 30%;
      padding: 20px;
      background: #ffffff;
      div {
        margin: 15px 0;
      }
      .el-table {
        .el-table__header-wrapper {
          margin: 0;
        }
        .el-table__body-wrapper {
          margin: 0;
        }
        .cell {
          margin: 0;
        }
      }
    }
    .warehouse-right {
      display: inline-block;
      box-sizing: border-box;
      width: 68%;
      padding: 20px;
      background: #ffffff;
      float: right;
      .warehouse-title {
        .warehouse-title-head {
          display: inline-block;
          font-weight: bold;
          margin-bottom: 20px;
        }
        .more {
          float: right;
          color: #28d094;
          cursor: pointer;
        }
        .pack-order-details {
          float: right;
        }
      }
      .auth-text {
        margin: 10px 0;
      }
      .warehouse-table {
        margin-bottom: 20px;
      }
    }
  }
  p {
    font-weight: bold;
    margin-top: 0;
  }
  .scaleImg {
    vertical-align: text-top;
    &:hover {
      position: relative;
      z-index: 9999;
      transition: all 0.3s ease-in-out;
      transform: scale(5) translateX(30px);
    }
  }
}
</style>
