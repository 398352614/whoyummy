<template>
  <div class="addFreight">
    <div class="from">
      <div class="form_box">
        <el-form
          :rules="formRules"
          ref="formData"
          :model="formData"
          label-width="170px"
        >
          <el-form-item :label="$t('pleaseSelectPriceMode')">
            <el-select v-model="formData.type" placeholder="请选择" size="mini">
              <el-option
                v-for="item in transportPriceTypeList"
                :key="item.id"
                :label="item.name"
                :value="item.id"
              >
              </el-option>
            </el-select>
            <!-- <el-radio-group v-model="formData.type">
              <el-radio :label="1">{{
                $t("calculateTieredProductValue")
              }}</el-radio>
              <el-radio :label="2">{{
                $t("calculateTieredFixedValue")
              }}</el-radio>
              <el-radio :label="5">{{
                $t("FirstRenewalCalculation")
              }}</el-radio>
              <el-radio :label="4">{{
                $t("FirstParcelRenewalCalculation")
              }}</el-radio>
            </el-radio-group> -->
            <div v-if="formData.type == 1" style="color: #e76767">
              {{ $t("calculation") }}
            </div>
            <div v-if="formData.type == 2" style="color: #e76767">
              {{ $t("calculation2") }}
            </div>
            <div v-if="formData.type == 5" style="color: #e76767">
              {{ $t("CalculateFeeRenewalRenewal") }}
            </div>
            <div v-if="formData.type == 4" style="color: #e76767">
              {{ $t("Calculate") }}
            </div>
          </el-form-item>
          <el-form-item :label="$t('payer')" prop="payer_type">
            <el-select
              v-model="formData.payer_type"
              placeholder=" "
              size="mini"
            >
              <el-option
                v-for="item in feePayerTypeList"
                :key="item.id"
                :label="item.name"
                :value="item.id"
              >
              </el-option>
            </el-select>
          </el-form-item>
          <!-- 方案名称 -->
          <el-form-item :label="$t('SchemeName')" prop="name">
            <el-input size="mini" v-model="formData.name"></el-input>
          </el-form-item>
          <!-- 包裹 -->
          <div v-if="formData.type == 5">
            <!-- 首重费用 -->
            <el-form-item :label="$t('FirstCost')" prop="firstWeightCostList">
              <el-table max-height="600px" border :data="firstWeightCostList">
                <el-table-column
                  header-align="center"
                  align="center"
                  prop="starting_count"
                  :label="
                    '*' + $t('FirstWeight') + '(' + weight_unit_symbol + ')'
                  "
                >
                  <template slot-scope="scope">
                    <el-input
                      maxlength="30"
                      size="mini"
                      @input="firstWeightValue(scope.row.starting_count)"
                      v-model="scope.row.starting_count"
                    ></el-input>
                  </template>
                </el-table-column>
                <el-table-column
                  header-align="center"
                  align="center"
                  prop="starting_price"
                  :label="'*' + $t('cost')"
                >
                  <template slot-scope="scope">
                    <el-input
                      maxlength="30"
                      size="mini"
                      v-model="scope.row.starting_price"
                    ></el-input>
                  </template>
                </el-table-column>
              </el-table>
            </el-form-item>
            <!-- 续重计费 -->
            <el-form-item
              :label="$t('RenewedBilling')"
              prop="renewPackageBilling"
            >
              <div class="add_button">
                <el-button
                  size="mini"
                  type="primary"
                  plain
                  @click="handlerAddFirstWeght"
                  >{{ $t("Add") }}</el-button
                >
              </div>
              <el-table
                max-height="600px"
                border
                :data="formData.first_weight_list"
              >
                <el-table-column
                  header-align="center"
                  align="center"
                  type="index"
                >
                </el-table-column>
                <el-table-column
                  header-align="center"
                  align="center"
                  prop="prop"
                  :label="$t('StartinWeight')"
                >
                  <template slot-scope="scope">
                    <el-input
                      maxlength="30"
                      size="mini"
                      @input="
                        syncModifyTableValue(
                          formData.first_weight_list,
                          scope.$index,
                          $event,
                          0
                        )
                      "
                      :disabled="scope.$index == 0"
                      v-model="scope.row.start"
                    ></el-input>
                  </template>
                </el-table-column>
                <el-table-column
                  header-align="center"
                  align="center"
                  :label="$t('OffWeight')"
                >
                  <template slot-scope="scope">
                    <el-input
                      maxlength="30"
                      size="mini"
                      @input="
                        syncModifyTableValue(
                          formData.first_weight_list,
                          scope.$index,
                          $event,
                          1
                        )
                      "
                      :disabled="
                        formData.first_weight_list.length - 1 == scope.$index
                      "
                      v-model="scope.row.end"
                    ></el-input>
                  </template>
                </el-table-column>
                <el-table-column
                  header-align="center"
                  align="center"
                  :label="$t('unitPrice')"
                >
                  <template slot-scope="scope">
                    <el-input
                      maxlength="30"
                      size="mini"
                      v-model="scope.row.price"
                    ></el-input>
                  </template>
                </el-table-column>
                <el-table-column
                  header-align="center"
                  align="center"
                  :label="$t('Operation')"
                >
                  <template slot-scope="scope">
                    <el-button
                      size="mini"
                      type="danger"
                      @click="
                        handlerDelKm(formData.first_weight_list, scope.$index)
                      "
                      >{{ $t("Delete") }}</el-button
                    >
                  </template>
                </el-table-column>
              </el-table>
            </el-form-item>
          </div>
          <div v-else-if="formData.type == 4">
            <!-- 首包裹费用 -->
            <el-form-item :label="$t('FirstParcelCost')" prop="firstParcelList">
              <el-table max-height="600px" border :data="firstParcelList">
                <el-table-column
                  header-align="center"
                  align="center"
                  prop="starting_count"
                  :label="'*' + $t('NumberFirstParcels')"
                >
                  <template slot-scope="scope">
                    <el-input
                      maxlength="30"
                      size="mini"
                      @input="regularValue(scope.row.starting_count)"
                      v-model="scope.row.starting_count"
                    ></el-input>
                  </template>
                </el-table-column>
                <el-table-column
                  header-align="center"
                  align="center"
                  prop="starting_price"
                  :label="'*' + $t('cost')"
                >
                  <template slot-scope="scope">
                    <el-input
                      maxlength="30"
                      size="mini"
                      v-model="scope.row.starting_price"
                    ></el-input>
                  </template>
                </el-table-column>
              </el-table>
            </el-form-item>
            <!-- 续包裹计费 -->
            <el-form-item
              :label="$t('RenewPackageBilling')"
              prop="renewPackageBilling"
            >
              <div class="add_button">
                <el-button
                  size="mini"
                  type="primary"
                  plain
                  @click="handlerAddCount"
                  >{{ $t("Add") }}</el-button
                >
              </div>
              <el-table max-height="600px" border :data="formData.count_list">
                <el-table-column
                  header-align="center"
                  align="center"
                  type="index"
                >
                </el-table-column>
                <el-table-column
                  header-align="center"
                  align="center"
                  prop="prop"
                  :label="$t('StartingQuantity')"
                >
                  <template slot-scope="scope">
                    <el-input
                      maxlength="30"
                      size="mini"
                      @input="
                        syncModifyTableValue(
                          formData.count_list,
                          scope.$index,
                          $event,
                          0
                        )
                      "
                      :disabled="scope.$index == 0"
                      v-model="scope.row.start"
                    ></el-input>
                  </template>
                </el-table-column>
                <el-table-column
                  header-align="center"
                  align="center"
                  prop="prop"
                  :label="$t('CutOffQuantity')"
                >
                  <template slot-scope="scope">
                    <el-input
                      maxlength="30"
                      size="mini"
                      @input="
                        syncModifyTableValue(
                          formData.count_list,
                          scope.$index,
                          $event,
                          1
                        )
                      "
                      :disabled="formData.count_list.length - 1 == scope.$index"
                      v-model="scope.row.end"
                    ></el-input>
                  </template>
                </el-table-column>
                <el-table-column
                  header-align="center"
                  align="center"
                  prop="prop"
                  :label="'*' + $t('UnitPrice')"
                >
                  <template slot-scope="scope">
                    <el-input
                      maxlength="30"
                      size="mini"
                      v-model="scope.row.price"
                    ></el-input>
                  </template>
                </el-table-column>
                <el-table-column
                  header-align="center"
                  align="center"
                  prop="prop"
                  :label="$t('Operation')"
                >
                  <template slot-scope="scope">
                    <el-button
                      size="mini"
                      type="danger"
                      @click="handlerDelKm(formData.count_list, scope.$index)"
                      >{{ $t("Delete") }}</el-button
                    >
                  </template>
                </el-table-column>
              </el-table>
            </el-form-item>
          </div>
          <div v-else>
            <el-form-item :label="$t('fixedCosts')">
              <el-input
                size="mini"
                v-model="formData.starting_price"
              ></el-input>
            </el-form-item>

            <el-form-item :label="$t('KilometerBilling')">
              <div class="add_button">
                <el-button
                  size="mini"
                  type="primary"
                  plain
                  @click="handlerAddKm"
                  >{{ $t("Add") }}</el-button
                >
              </div>
              <el-table max-height="600px" border :data="formData.km_list">
                <el-table-column
                  header-align="center"
                  align="center"
                  type="index"
                >
                </el-table-column>
                <el-table-column
                  header-align="center"
                  align="center"
                  prop="prop"
                  :label="$t('startKm')"
                >
                  <template slot-scope="scope">
                    <el-input
                      maxlength="30"
                      size="mini"
                      @input="
                        syncModifyTableValue(
                          formData.km_list,
                          scope.$index,
                          $event,
                          0
                        )
                      "
                      :disabled="scope.$index == 0"
                      v-model="scope.row.start"
                    ></el-input>
                  </template>
                </el-table-column>
                <el-table-column
                  header-align="center"
                  align="center"
                  prop="prop"
                  :label="$t('endKm')"
                >
                  <template slot-scope="scope">
                    <el-input
                      maxlength="30"
                      size="mini"
                      @input="
                        syncModifyTableValue(
                          formData.km_list,
                          scope.$index,
                          $event,
                          1
                        )
                      "
                      :disabled="formData.km_list.length - 1 == scope.$index"
                      v-model="scope.row.end"
                    ></el-input>
                  </template>
                </el-table-column>
                <el-table-column
                  header-align="center"
                  align="center"
                  prop="prop"
                  :label="$t('Markup')"
                >
                  <template slot-scope="scope">
                    <el-input
                      maxlength="30"
                      size="mini"
                      v-model="scope.row.price"
                    ></el-input>
                  </template>
                </el-table-column>
                <el-table-column
                  header-align="center"
                  align="center"
                  prop="prop"
                  :label="$t('Operation')"
                >
                  <template slot-scope="scope">
                    <el-button
                      size="mini"
                      type="danger"
                      @click="handlerDelKm(formData.km_list, scope.$index)"
                      >{{ $t("Delete") }}</el-button
                    >
                  </template>
                </el-table-column>
              </el-table>
            </el-form-item>
            <el-form-item :label="$t('WeightBilling')">
              <div class="add_button">
                <el-button
                  size="mini"
                  type="primary"
                  plain
                  @click="handlerAddWeight"
                  >{{ $t("Add") }}</el-button
                >
              </div>
              <el-table max-height="600px" border :data="formData.weight_list">
                <el-table-column
                  header-align="center"
                  align="center"
                  type="index"
                >
                </el-table-column>
                <el-table-column
                  header-align="center"
                  align="center"
                  prop="prop"
                  :label="$t('startWeight')"
                >
                  <template slot-scope="scope">
                    <el-input
                      maxlength="30"
                      size="mini"
                      @input="
                        syncModifyTableValue(
                          formData.weight_list,
                          scope.$index,
                          $event,
                          0
                        )
                      "
                      :disabled="scope.$index == 0"
                      v-model="scope.row.start"
                    ></el-input>
                  </template>
                </el-table-column>
                <el-table-column
                  header-align="center"
                  align="center"
                  :label="$t('endWeight')"
                >
                  <template slot-scope="scope">
                    <el-input
                      maxlength="30"
                      size="mini"
                      @input="
                        syncModifyTableValue(
                          formData.weight_list,
                          scope.$index,
                          $event,
                          1
                        )
                      "
                      :disabled="
                        formData.weight_list.length - 1 == scope.$index
                      "
                      v-model="scope.row.end"
                    ></el-input>
                  </template>
                </el-table-column>
                <el-table-column
                  header-align="center"
                  align="center"
                  :label="$t('Markup')"
                >
                  <template slot-scope="scope">
                    <el-input
                      maxlength="30"
                      size="mini"
                      v-model="scope.row.price"
                    ></el-input>
                  </template>
                </el-table-column>
                <el-table-column
                  header-align="center"
                  align="center"
                  :label="$t('Operation')"
                >
                  <template slot-scope="scope">
                    <el-button
                      size="mini"
                      type="danger"
                      @click="handlerDelKm(formData.weight_list, scope.$index)"
                      >{{ $t("Delete") }}</el-button
                    >
                  </template>
                </el-table-column>
              </el-table>
            </el-form-item>
          </div>

          <!-- <el-form-item :label="$t('SpecialtimeCost')">
            <div class="add_button">
              <el-button size="mini" @click="handlerAddSpecialTime">{{
                $t("Add")
              }}</el-button>
            </div>
            <el-table  max-height="600px"border :data="formData.special_time_list">
              <el-table-column
                header-align="center"
                align="center"
                type="index"
              >
              </el-table-column>
              <el-table-column
                header-align="center"
                min-width="300"
                align="center"
                :label="$t('startime')"
              >
                <template slot-scope="scope">
                  <template>
                    <el-time-picker
                      size="mini"
                      is-range
                      arrow-control
                      v-model="scope.row.period"
                      value-format="HH:mm:ss"
                      format="HH:mm:ss"
                      :default-value="scope.row.period"
                      :range-separator="$t('To')"
                      :start-placeholder="$t('StartTime')"
                      :end-placeholder="$t('EndTime')"
                    >
                    </el-time-picker>
                  </template>
                </template>
              </el-table-column>
              <el-table-column
                header-align="center"
                align="center"
                :label="$t('Markup')"
              >
                <template slot-scope="scope">
                  <el-input size="mini" v-model="scope.row.price"></el-input>
                </template>
              </el-table-column>
              <el-table-column
                header-align="center"
                align="center"
                prop="prop"
                :label="$t('Operation')"
              >
                <template slot-scope="scope">
                  <el-button
                    size="mini"
                    type="danger"
                    @click="handlerDelSpecialTime(scope.$index)"
                    >{{ $t("Delete") }}</el-button
                  >
                </template>
              </el-table-column>
            </el-table>
          </el-form-item> -->
          <el-form-item :label="$t('note')">
            <el-input
              size="mini"
              type="textarea"
              maxlength="50"
              v-model="formData.remark"
            ></el-input>
          </el-form-item>

          <el-form-item :label="$t('Status')">
            <el-radio-group v-model="formData.status">
              <el-radio :label="1">{{ $t("enable") }}</el-radio>
              <el-radio :label="2">{{ $t("disable") }}</el-radio>
            </el-radio-group>
          </el-form-item>
        </el-form>
        <el-row>
          <el-col :span="2" :offset="22">
            <el-button
              :label="$t('SchemeName')"
              size="samll"
              type="primary"
              @click="onSubmit"
              class="submit"
              :loading="this.$store.state.config.button_loading"
              >{{ $t("Submit") }}</el-button
            >
          </el-col>
        </el-row>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  // name: "addFreight",
  data() {
    return {
      transportPriceTypeList: [], //类型
      currency_unit_symbol: "",
      weight_unit_symbol: "",
      firstParcelList: [
        {
          starting_count: 1,
          starting_price: null,
        },
      ],
      firstWeightCostList: [
        {
          starting_count: 1,
          starting_price: null,
        },
      ],
      formRules: {
        name: [
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        starting_price: [
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        remark: [
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        payer_type: [
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        status: [
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        firstParcelList: [
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        firstWeightCostList: [
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        renewPackageBilling: [
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
      },
      dialogVisible: false,
      formData: {
        name: "",
        starting_price: "",
        starting_count: "",
        type: 1,
        count_list: [
          {
            start: 1,
            end: 999999999,
            price: "",
          },
        ],
        first_weight_list: [
          {
            start: 1,
            end: 999999999,
            price: "",
          },
        ],
        km_list: [
          {
            start: 0,
            end: 999999999,
            price: "",
          },
        ],
        weight_list: [
          {
            start: 0,
            end: 999999999,
            price: "",
          },
        ],
        special_time_list: [
          {
            start: "",
            end: "",
            period: ["00:00:00", "23:59:59"],
            price: "",
          },
        ],
        remark: "",
        status: 1,
      },
      feePayerTypeList: [],
    };
  },
  created() {
    if (this.isEdit) {
      this.getList(this.$route.query.id);
    }
    this.getDictionary();
    this.getAdvanceSetting();
  },
  computed: {
    isEdit() {
      return !!this.$route.query.id;
    },

    kmLen() {
      return this.formData.km_list.length;
    },

    wtLen() {
      return this.formData.weight_list.length;
    },
  },
  methods: {
    // 获取单位值
    getAdvanceSetting() {
      this.$api.getAdvanceSetting().then((res) => {
        this.currency_unit_symbol = res.data.currency_unit_symbol; // 单价单位
        this.weight_unit_symbol = res.data.weight_unit_symbol; //重量单位
      });
    },
    getDictionary() {
      this.$api.getDictionary().then((res) => {
        this.feePayerTypeList = res.data.feePayerTypeList;
        this.transportPriceTypeList = res.data.transportPriceTypeList;
      });
    },
    handlerDatePicker(row, period) {
      // 处理数据
      row.start = period[0];
      row.end = period[1];
    },
    getList(id) {
      this.$api.getTransportPriceDetial(id).then((res) => {
        if (!res) return;
        this.formData = res.data;
        if (this.formData.km_list.length == 0) {
          this.formData.km_list.push({
            start: 0,
            end: 999999999,
            price: "",
          });
        }
        if (this.formData.weight_list.length == 0) {
          this.formData.weight_list.push({
            start: 0,
            end: 999999999,
            price: "",
          });
        }
        // 首包裹
        if (this.formData.count_list.length == 0) {
          this.formData.count_list.push({
            start: 1,
            end: 999999999,
            price: "",
          });
        }
        // 首重
        if (this.formData.first_weight_list.length == 0) {
          this.formData.first_weight_list.push({
            start: 1,
            end: 999999999,
            price: "",
          });
        }
        // 首包裹
        let firstParcelList = [];
        firstParcelList.push({
          starting_count: this.formData.starting_count,
          starting_price: this.formData.starting_price,
        });
        this.firstParcelList = firstParcelList;
        // 首重
        let firstWeightCostList = [];
        firstWeightCostList.push({
          starting_count: this.formData.starting_count,
          starting_price: this.formData.starting_price,
        });
        this.firstWeightCostList = firstWeightCostList;
      });
    },
    handlerDelSpecialTime(index) {
      this.delIndexItem(this.formData.special_time_list, index);
    },
    regularValue(val) {
      this.formData.count_list[0].start = val;
    },
    firstWeightValue(val) {
      this.formData.first_weight_list[0].start = val;
    },
    syncModifyTableValue(arr, index, value, type) {
      if (type) {
        arr[index + 1].start = value;
      } else {
        arr[index - 1].end = value;
      }
    },

    handlerDelKm(arr, index) {
      if (index == 0) {
        arr[index + 1].start = arr[index].start;
      } else if (index == arr.length - 1) {
        arr[index - 1].end = arr[index].end;
      } else {
        arr[index - 1].end = arr[index + 1].start;
        arr[index + 1].start = arr[index - 1].end;
      }
      this.delIndexItem(arr, index);
    },

    delIndexItem(arr, index) {
      /**
       * 删除数组中的某个元素（改变原数组）
       */
      if (!Array.isArray(arr) || arr.length === 1) return;
      arr.splice(index, 1);
    },
    checkObjectKey(obj, key) {
      /**
       * 检测对象的某个属性的值为 truely or falsely
       */
      let result = true;
      for (const item in obj) {
        if (`${item}` === `${key}` && !obj[item]) {
          result = false;
          break;
        }
      }
      return result;
    },
    handlerAddKm() {
      let kmData = this.formData.km_list;
      let lastValue = kmData[kmData.length - 1].end;
      kmData[kmData.length - 1].end = "";
      kmData.push({
        start: "",
        end: lastValue,
        price: "",
      });
    },
    handlerAddWeight() {
      let wtData = this.formData.weight_list;
      let lastValue = wtData[wtData.length - 1].end;
      wtData[wtData.length - 1].end = "";
      wtData.push({
        start: "",
        end: lastValue,
        price: "",
      });
    },
    // 首包裹
    handlerAddCount() {
      let countData = this.formData.count_list;
      let lastValue = countData[countData.length - 1].end;
      countData[countData.length - 1].end = "";
      countData.push({
        start: "",
        end: lastValue,
        price: "",
      });
    },
    // 首重
    handlerAddFirstWeght() {
      let FirstWeghtData = this.formData.first_weight_list;
      let lastValue = FirstWeghtData[FirstWeghtData.length - 1].end;
      FirstWeghtData[FirstWeghtData.length - 1].end = "";
      FirstWeghtData.push({
        start: "",
        end: lastValue,
        price: "",
      });
    },
    handlerAddSpecialTime() {
      this.formData.special_time_list.push({
        start: "",
        end: "",
        period: ["00:00:00", "23:59:59"],
        price: "",
      });
    },
    checkArray(ArrayObj, cbArray) {
      for (const item of ArrayObj) {
        if (item.period && item.period.length > 0) {
          const data = {
            start: item.period[0],
            end: item.period[1],
            price: item.price,
          };
          cbArray.push(data);
        } else {
          const data = {
            start: item.start,
            end: item.end,
            price: item.price,
          };
          cbArray.push(data);
        }
      }
    },
    checkObj(objArray) {
      let result;
      for (const obj of objArray) {
        result = Object.values(obj).every((attr) => !!attr === false);
        if (!result) {
          break;
        }
      }
      return result;
    },
    onSubmit() {
      let weightList = [];
      let kmList = [];
      let countList = [];
      let firstWeghtList = [];

      if (
        this.formData.weight_list.length == 1 &&
        !this.formData.weight_list[0].price
      ) {
        weightList = [];
      } else {
        weightList = this.formData.weight_list;
      }
      if (
        this.formData.km_list.length == 1 &&
        !this.formData.km_list[0].price
      ) {
        kmList = [];
      } else {
        kmList = this.formData.km_list;
      }
      if (
        this.formData.count_list.length == 1 &&
        !this.formData.count_list[0].price
      ) {
        countList = [];
      } else {
        countList = this.formData.count_list;
      }
      if (
        this.formData.first_weight_list.length == 1 &&
        !this.formData.first_weight_list[0].price
      ) {
        firstWeghtList = [];
      } else {
        firstWeghtList = this.formData.first_weight_list;
      }
      if (this.formData.type == 4) {
        // 取出首包裹费用
        this.firstParcelList.map((item) => {
          this.formData.starting_count = item.starting_count;
          this.formData.starting_price = item.starting_price;
        });
        kmList = [];
        weightList = [];
        firstWeghtList = [];
      } else {
        countList = [];
      }
      if (this.formData.type == 5) {
        // 取出首重
        this.firstWeightCostList.map((item) => {
          this.formData.starting_count = item.starting_count;
          this.formData.starting_price = item.starting_price;
        });
        kmList = [];
        weightList = [];
        countList = [];
      } else {
        firstWeghtList = [];
      }
      // const specialTimeList = this.formData.special_time_list;
      // let weightList = this.formData.weight_list;
      // let kmList = this.formData.km_list;
      const weightListArray = [];
      const kmListArray = [];
      const countArray = [];
      const firstWeghtArray = [];
      const specialTimeArray = []; // 用于提交
      // let isPassed = true;
      // this.checkArray(specialTimeList, specialTimeArray);
      this.checkArray(weightList, weightListArray);
      this.checkArray(kmList, kmListArray);
      this.checkArray(countList, countArray);
      this.checkArray(firstWeghtList, firstWeghtArray);

      if (
        // this.checkObj(specialTimeArray) &&
        this.checkObj(weightListArray) &&
        this.checkObj(kmListArray) &&
        this.checkObj(countArray) &&
        this.checkObj(firstWeghtArray)
      ) {
        this.$message({
          type: "error",
          message: this.$t("pleaseEnterCheck"),
        });
        return;
      }

      const obj = Object.assign(
        {
          type: this.formData.type,
          name: this.formData.name,
          remark: this.formData.remark,
          starting_price: this.formData.starting_price,
          status: this.formData.status,
          payer_type: this.formData.payer_type,
          starting_count: this.formData.starting_count,
        },
        {
          km_list: JSON.stringify(kmListArray),
          weight_list: JSON.stringify(weightListArray),
          count_list: JSON.stringify(countArray),
          first_weight_list: JSON.stringify(firstWeghtArray),
          special_time_list: JSON.stringify(specialTimeArray),
        }
      ); // 转成 JSON格式
      if (this.isEdit) {
        this.$refs.formData.validate((valid) => {
          if (!valid) return;
          this.$api
            .editTransportPrice(this.$route.query.id, obj)
            .then((res) => {
              if (!res) return;
              this.$router.push({
                name: "Charging",
              });
            });
        });
        return;
      }
      this.$refs.formData.validate((valid) => {
        if (!valid) return;
        this.$api.addTransportPrice(obj).then((res) => {
          if (!res) return;
          this.$router.push({
            name: "Charging",
          });
        });
      });
    },
    copyObj(arr) {
      const copyArr = [];
      for (let i = 0, l = arr.length; i < l; i += 1) {
        const objTemp = {
          start: arr[i].start,
          end: arr[i].end,
          price: arr[i].price,
        };
        copyArr.push(objTemp);
      }
      return copyArr;
    },
    checkFormData(arrObj) {
      /**
       * 功能： 检查一个对象数组中，所有对象的键的键值是否均为 truely, 检测通过返回 true，反之返回 false
       * @params arrObj { Array } 需要被检查的对象数组
       * @return { Boolean }
       */
      // eslint-disable-next-line
      let isPassed = true;
      const len = arrObj.length;

      if (!Array.isArray(arrObj)) return;

      for (let i = 0; i < len; i += 1) {
        const item = arrObj[i];
        for (const j in item) {
          if (!item[j]) {
            isPassed = false;
            break;
          }
        }
      }
      return isPassed;
    },
  },
};
</script>

<style lang="less">
@import "../../../less/public_variable.less";

.addFreight {
  background-color: #fff;
  padding: 20px;
  .form_box {
    margin: 0 auto;
  }
  .add_button {
    float: right;
  }
}
</style>
