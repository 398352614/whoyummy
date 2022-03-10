<template>
  <!-- 运价管理 -->
  <div class="content_box">
    <div class="search">
      <div>
        <el-input
          clearable
          size="small"
          v-model="pageParams.name"
          @keyup.enter.native="selectChange"
          :placeholder="$t('SchemeName')"
          class="search-bar"
        >
        </el-input>
        <el-select
          clearable
          size="small"
          v-model="pageParams.payer_type"
          @keyup.enter.native="getFee"
          :placeholder="$t('PleasePayer')"
          class="search-bar"
        >
          <el-option
            v-for="(item, index) in feePayerTypeList"
            :key="index"
            :label="item.name"
            :value="item.id"
          ></el-option>
        </el-select>
        <el-button
          size="small"
          @click="selectChange"
          :loading="this.$store.state.config.button_loading"
          type="primary"
          >{{ $t("Search") }}</el-button
        >
      </div>
      <!-- 新增 -->
      <el-tooltip
        :content="$t('AdministratorActivate')"
        placement="top"
        :disabled="hasPerm('transport-price.store') ? true : false"
      >
        <div>
          <el-button
            :disabled="!hasPerm('transport-price.store')"
            type="primary"
            size="small"
            @click="handlerAddClick"
            >{{ $t("Add") }}</el-button
          >
        </div>
      </el-tooltip>
    </div>
    <el-table
      height="calc(100vh - 276px)"
      :data="hasPerm('transport-price.index') ? list : []"
      border
      v-loading="global_loading()"
      element-loading-text="loading…"
      :cell-style="{ padding: '5px 0px' }"
    >
      <template slot="empty" v-if="!hasPerm('transport-price.index')">
        <div style="font-size: 18px">
          {{ $t("AdministratorActivate") }}
        </div>
      </template>
      <el-table-column
        width="120"
        :label="$t('Operation')"
        align="center"
        header-align="center"
      >
        <template slot-scope="scope">
          <el-dropdown>
            <el-button size="mini" type="primary" plain class="operation">
              {{ $t("Operation") }}
              <i class="el-icon-arrow-down el-icon--right"></i>
            </el-button>
            <el-dropdown-menu slot="dropdown">
              <!-- 编辑 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('transport-price.update') ? true : false"
              >
                <div
                  :style="
                    hasPerm('transport-price.update')
                      ? ''
                      : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('transport-price.update')"
                    @click.native="handlerEdit(scope.row)"
                  >
                    <el-button
                      :disabled="!hasPerm('transport-price.update')"
                      class="operation"
                      size="small"
                      type="text"
                      >{{ $t("Edit") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 方案详情 -->
              <el-dropdown-item @click.native="showProgrammeDetails(scope.row)">
                <el-button class="operation" size="small" type="text">{{
                  $t("programDetails")
                }}</el-button>
              </el-dropdown-item>
              <!-- 查看日志 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('transport-price.log') ? true : false"
              >
                <div
                  :style="
                    hasPerm('transport-price.log') ? '' : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('transport-price.log')"
                    @click.native="showLog(scope.row)"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('transport-price.log')"
                      >{{ $t("viewLog") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 价格测试 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('transport-price.test') ? true : false"
              >
                <div
                  :style="
                    hasPerm('transport-price.test') ? '' : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('transport-price.test')"
                    @click.native="handlerPriceTest(scope.row)"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('transport-price.test')"
                      >{{ $t("CheckPrice") }}</el-button
                    ></el-dropdown-item
                  >
                </div>
              </el-tooltip>
              <!-- 状态 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('transport-price.status') ? true : false"
              >
                <div
                  :style="
                    hasPerm('transport-price.status')
                      ? ''
                      : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('transport-price.status')"
                    @click.native="handlerStatus(scope.row)"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('transport-price.status')"
                    >
                      {{
                        scope.row.status === 1 ? $t("disable") : $t("enable")
                      }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
            </el-dropdown-menu>
          </el-dropdown>
        </template>
      </el-table-column>
      <el-table-column
        prop="name"
        :label="$t('SchemeName')"
        align="center"
        header-align="center"
      >
      </el-table-column>
      <el-table-column
        header-align="center"
        align="center"
        prop="payer_type_name"
        :label="$t('payer')"
      >
      </el-table-column>
      <el-table-column
        prop="part"
        :label="$t('valuationMethod')"
        align="center"
        header-align="center"
      >
      </el-table-column>
      <el-table-column
        prop="type_name"
        width="220"
        :label="$t('costCalculationMethod')"
        align="center"
        header-align="center"
      >
      </el-table-column>
      <el-table-column
        :label="$t('Status')"
        align="center"
        header-align="center"
      >
        <template slot-scope="scope">
          <i v-if="scope.row.status === 2" class="el-icon-lock custom_lock"></i>
          {{ scope.row.status === 1 ? $t("enable") : $t("disable") }}
        </template>
      </el-table-column>
      <el-table-column
        prop="created_at"
        :label="$t('CreationTime')"
        align="center"
        header-align="center"
      >
      </el-table-column>
    </el-table>
    <base-pagination
      class="pagination"
      ref="searchRef"
      :pageParams="pageParams"
      @search="getList"
    ></base-pagination>

    <el-dialog :title="$t('CheckPrice')" :visible.sync="dialogVisible">
      <div>
        <el-form ref="formData" :model="formData" label-width="160px">
          <div class="form_box">
            <div>
              <el-form-item
                :label="$t('km')"
                v-if="formData.type != 4 && formData.type != 5"
              >
                <el-input size="small" v-model="formData.distance"></el-input>
              </el-form-item>
              <!-- <el-form-item :label="$t('Specialtime')" >
                <el-time-picker
                  arrow-control
                  value-format="HH:mm:ss"
                  v-model="formData.special_time"
                  :picker-options="{
                    selectableRange: '00:00:00 - 23:59:59'
                  }">
                </el-time-picker>
              </el-form-item> -->
              <el-form-item
                :label="
                  formData.type == 4 ? $t('NumberPackages') : $t('weight')
                "
              >
                <el-row>
                  <el-col :span="6">
                    <!-- 首包裹 -->
                    <el-input-number
                      v-if="formData.type == 4"
                      v-model="formData.starting_count"
                      controls-position="right"
                      size="small"
                      :min="1"
                      :precision="0"
                    ></el-input-number>
                    <!-- 首重 -->
                    <el-input-number
                      v-else-if="formData.type == 5"
                      v-model="formData.starting_count"
                      controls-position="right"
                      size="small"
                      :min="1"
                    ></el-input-number>
                    <!-- <el-input
                      type="number"
                      v-model="formData.starting_count"
                    ></el-input> -->
                    <el-input-number
                      v-else
                      v-model="formData.package_list[0].weight"
                      controls-position="right"
                      size="small"
                      :min="1"
                    ></el-input-number>
                    <!-- <el-input
                      size="small"
                      type="number"
                      v-model="formData.package_list[0].weight"
                    ></el-input> -->
                  </el-col>
                  <el-col :span="4" :offset="1">
                    <el-button
                      size="small"
                      type="primary"
                      @click="handlerClaculatePrice"
                      :loading="this.$store.state.config.button_loading"
                      >{{ $t("calculatecost") }}</el-button
                    >
                  </el-col>
                  <el-col :span="2" :offset="1">
                    <el-button
                      size="small"
                      type="info"
                      @click="handlerClearPrice"
                      >{{ $t("reset") }}</el-button
                    >
                  </el-col>
                </el-row>
              </el-form-item>
            </div>
            <div>
              <hr />
              <el-form-item :label="$t('result')">
                <br />
                <div v-if="totalPrice.count_settlement_amount !== undefined">
                  {{ $t("totalCost") }}: {{ currency_unit_symbol
                  }}<span>{{ totalPrice.count_settlement_amount }}</span>
                </div>
              </el-form-item>
              <el-form-item v-if="totalPrice.starting_price">
                <div>
                  {{ $t("component") }}:<br />
                  <span style="margin-left: 60px"
                    >{{ $t("fixedCosts") }}: {{ currency_unit_symbol
                    }}{{ totalPrice.starting_price }}</span
                  ><br />
                  <span
                    style="margin-left: 60px"
                    v-if="formData.type != 4 && formData.type != 5"
                    >{{ $t("weightMileageFee") }}: {{ currency_unit_symbol
                    }}{{ totalPrice.package_settlement_amount }}</span
                  ><br />
                  <!-- <span>{{`超出${totalPrice.distance || ''}公里, 收费${currency_unit_symbol}`}}{{((+totalPrice.count_settlement_amount) - (+totalPrice.starting_price)) | keepDecimals}}</span><br>
                  <div v-for="(item, index) in totalPrice.package_list || []" :key="index">
                    <span>{{`${item.weight}${weight}加价${currency_unit_symbol}${item.count_settlement_amount}`}}</span><br>
                  </div> -->
                </div>
              </el-form-item>
            </div>
          </div>
        </el-form>
      </div>
    </el-dialog>

    <!-- 方案详情 -->
    <el-dialog :title="$t('programDetails')" :visible.sync="show_programme">
      <p
        style="margin-bottom: 40px"
        v-if="
          priceDetial.count_list.length == 0 &&
          priceDetial.first_weight_list.length == 0
        "
      >
        {{ $t("fixedCosts") }}: {{ currency_unit_symbol
        }}{{ priceDetial.starting_price }}
      </p>
      <!-- 重量费用 -->
      <div v-if="priceDetial.weight_list.length !== 0">
        <p>{{ $t("weightFee") }}:</p>
        <el-table max-height="600px" :data="priceDetial.weight_list" border>
          <el-table-column
            :label="$t('weightRange') + '(' + weight_unit_symbol + ')'"
            align="center"
            header-align="center"
          >
            <template slot-scope="scope">
              {{ `${scope.row.start} ~ ${scope.row.end}` }}
            </template>
          </el-table-column>
          <el-table-column
            prop="price"
            :label="$t('fees') + '(' + currency_unit_symbol + ')'"
            align="center"
            header-align="center"
          >
          </el-table-column>
        </el-table>
      </div>
      <!-- 里程费用 -->
      <div v-if="priceDetial.km_list.length !== 0">
        <p style="margin-top: 40px">{{ $t("mileageFee") }}:</p>
        <el-table max-height="600px" :data="priceDetial.km_list" border>
          <el-table-column
            :label="$t('mileageRange') + '(KM)'"
            align="center"
            header-align="center"
          >
            <template slot-scope="scope">
              {{ `${scope.row.start} ~ ${scope.row.end}` }}
            </template>
          </el-table-column>
          <el-table-column
            prop="price"
            :label="$t('fees') + '(' + currency_unit_symbol + ')'"
            align="center"
            header-align="center"
          >
          </el-table-column>
        </el-table>
      </div>
      <!-- 首重 -->
      <div v-if="priceDetial.first_weight_list.length !== 0">
        <p style="margin-top: 40px">{{ $t("FirstCost") }}:</p>
        <el-table max-height="600px" :data="priceDetial.firstWeghtList" border>
          <el-table-column
            :label="$t('FirstWeight') + '(' + weight_unit_symbol + ')'"
            align="center"
            header-align="center"
            prop="starting_count"
          >
          </el-table-column>
          <el-table-column
            :label="$t('cost') + '(' + currency_unit_symbol + ')'"
            align="center"
            header-align="center"
            prop="starting_price"
          >
          </el-table-column>
        </el-table>
        <!-- 续重计费 -->
        <p style="margin-top: 40px">{{ $t("RenewedBilling") }}:</p>
        <el-table
          max-height="600px"
          :data="priceDetial.first_weight_list"
          border
        >
          <el-table-column
            :label="$t('StartinWeight')"
            align="center"
            header-align="center"
            prop="start"
          >
          </el-table-column>
          <el-table-column
            :label="$t('OffWeight')"
            align="center"
            header-align="center"
            prop="end"
          >
          </el-table-column>
          <el-table-column
            prop="price"
            :label="$t('unitPrice') + '(' + currency_unit_symbol + ')'"
            align="center"
            header-align="center"
          >
          </el-table-column>
        </el-table>
      </div>
      <!-- 首包裹数量 -->
      <div v-if="priceDetial.count_list.length !== 0">
        <p style="margin-top: 40px">{{ $t("FirstParcelCost") }}:</p>
        <el-table max-height="600px" :data="priceDetial.firstParcelList" border>
          <el-table-column
            :label="$t('NumberFirstParcels')"
            align="center"
            header-align="center"
            prop="starting_count"
          >
          </el-table-column>
          <el-table-column
            :label="$t('cost') + '(' + currency_unit_symbol + ')'"
            align="center"
            header-align="center"
            prop="starting_price"
          >
          </el-table-column>
        </el-table>
        <!-- 首包裹续费 -->
        <p style="margin-top: 40px">{{ $t("RenewPackageBilling") }}:</p>
        <el-table max-height="600px" :data="priceDetial.count_list" border>
          <el-table-column
            :label="$t('StartingQuantity')"
            align="center"
            header-align="center"
            prop="start"
          >
          </el-table-column>
          <el-table-column
            :label="$t('CutOffQuantity')"
            align="center"
            header-align="center"
            prop="end"
          >
          </el-table-column>
          <el-table-column
            prop="price"
            :label="$t('fees') + '(' + currency_unit_symbol + ')'"
            align="center"
            header-align="center"
          >
          </el-table-column>
        </el-table>
      </div>

      <!-- <p style="margin-top: 40px">特殊时段:</p>
      <el-table  max-height="600px":data="priceDetial.special_time_list" border>
        <el-table-column
          :label="$t('时间范围')"
          align="center"
          header-align="center"
        >
          <template slot-scope="scope">
            {{ `${scope.row.start} - ${scope.row.end}` }}
          </template>
        </el-table-column>
        <el-table-column
          prop="price"
          :label="$t('费用') + '(' + currency_unit_symbol + ')'"
          align="center"
          header-align="center"
        >
        </el-table-column>
      </el-table> -->
    </el-dialog>

    <!-- 查看日志 -->
    <el-dialog :title="$t('viewLog')" :visible.sync="show_log">
      <el-table max-height="600px" :data="logInfo" border>
        <el-table-column
          prop="operation_name"
          :label="$t('MerchantType')"
          align="center"
          header-align="center"
        >
        </el-table-column>
        <el-table-column
          prop="created_at"
          :label="$t('Created')"
          align="center"
          header-align="center"
        >
        </el-table-column>
        <el-table-column
          prop="operator"
          :label="$t('operator')"
          align="center"
          header-align="center"
        >
        </el-table-column>
      </el-table>
    </el-dialog>
  </div>
</template>

<script>
import BasePagination from "@/components/BasePagination/BasePagination";
import loading from "@/mixin/global_loading";

export default {
  name: "FreightManagement",
  components: {
    BasePagination,
  },
  mixins: [loading],
  data() {
    return {
      currency_unit_symbol: "", //价格单位
      formData: {
        type: null,
        id: "",
        starting_count: "",
        distance: "",
        package_list: [
          {
            weight: "",
          },
        ],
        special_time: "",
      },
      dialogVisible: false,
      list: [],
      pageParams: {
        page: 1,
        per_page: 20,
        total: 0,
        name: "",
      },
      totalPrice: {
        price: "",
        starting_price: "",
      },
      copy_form: null,
      weightList: null,

      show_programme: false,
      priceDetial: {
        weight_list: [],
        km_list: [],
        count_list: [],
        first_weight_list: [],
        firstWeghtList: [],
        firstParcelList: [],
      },
      show_log: false,
      logInfo: [],
      feePayerTypeList: [], //支付方类型
    };
  },
  activated() {
    this.getList();
  },
  created() {
    this.getAdvanceSetting();
    this.getList();
    this.getDictionary();
    this.copy_form = JSON.parse(JSON.stringify(this.formData));
  },
  filters: {
    keepDecimals: function (value) {
      console.log(value);
      value = +value;
      return value.toFixed(2);
    },
  },

  methods: {
    getDictionary() {
      this.$api.getDictionary().then((res) => {
        this.feePayerTypeList = res.data.feePayerTypeList;
      });
    },
    // 获取单位值
    getAdvanceSetting() {
      this.$api.getAdvanceSetting().then((res) => {
        this.currency_unit_symbol = res.data.currency_unit_symbol; // 单价单位
        this.weight_unit_symbol = res.data.weight_unit_symbol; //重量单位
      });
    },
    handlerClearPrice() {
      const id = this.formData.id;
      const type = this.formData.type;
      this.formData = JSON.parse(JSON.stringify(this.copy_form));
      this.formData.id = id;
      this.formData.type = type;
      this.totalPrice = "";
    },
    handlerClaculatePrice() {
      if (
        !this.formData.distance &&
        !this.formData.package_list[0].weight &&
        !this.formData.starting_count
      ) {
        return;
      }
      let formData = {};
      if (this.formData.type == 4) {
        formData = {
          type: this.formData.type,
          id: this.formData.id,
          count: this.formData.starting_count,
        };
      } else if (this.formData.type == 5) {
        this.formData.package_list[0].weight = this.formData.starting_count;
        formData = {
          type: this.formData.type,
          id: this.formData.id,
          package_list: this.formData.package_list,
        };
      } else {
        formData = {
          type: this.formData.type,
          id: this.formData.id,
          distance: this.formData.distance,
          package_list: this.formData.package_list,
          special_time: this.formData.special_time,
        };
      }
      this.$api.testTransportPrice(formData).then((res) => {
        if (!res) return;
        this.totalPrice = res.data;
      });
    },
    handlerPriceTest(row) {
      this.dialogVisible = !this.dialogVisible;
      this.formData.id = row.id;
      this.formData.type = row.type;
      this.formData = {
        ...this.formData,
        starting_count: "",
        distance: "",
        package_list: [
          {
            weight: "",
          },
        ],
        special_time: "",
      };
      this.totalPrice = {};
    },
    showLog(row) {
      this.show_log = true;
      this.$api.getTransportPriceLog(row.id).then((res) => {
        if (!res) return;
        this.logInfo = res.data;
      });
    },
    handlerEdit(row) {
      this.$router.push({
        name: "editFreight",
        query: {
          id: row.id,
        },
      });
    },
    showProgrammeDetails(row) {
      // 方案详情
      this.show_programme = true;
      this.$api.getTransportPriceDetial(row.id).then((res) => {
        if (!res) return;
        this.priceDetial = res.data;
        let firstParcelList = [
          {
            starting_count: res.data.starting_count,
            starting_price: res.data.starting_price,
          },
        ];
        this.priceDetial.firstParcelList = firstParcelList;
        this.priceDetial.firstWeghtList = firstParcelList;
      });
    },
    handlerStatus(row) {
      this.$api
        .disableTransportPrice(row.id, {
          status: row.status === 1 ? 2 : 1,
        })
        .then((res) => {
          if (!res) return;
          this.getList();
        });
    },
    handlerAddClick() {
      this.$router.push({
        name: "addFreight",
      });
    },
    selectChange() {
      if (this.pageParams.name) {
        this.pageParams.page = 1;
      }
      this.getList();
    },
    getList() {
      this.$api.getTransportPrice(this.pageParams).then((res) => {
        this.list = res.data.data;
        this.pageParams.total = res.data.meta.total;
        this.pageParams.page = res.data.meta.current_page;
      });
    },
  },
};
</script>
<style lang="less" scoped>
.content_box {
  padding: 0px 25px 10px;
  background-color: #fff;
  position: relative;
  .search {
    margin-bottom: 10px;
    display: flex;
    justify-content: space-between;
  }
  .total_price {
    font-size: 20px;
  }
  .custom_lock {
    color: red;
  }
  .pagination {
    margin-top: 10px;
  }
}
</style>
