<template>
  <!-- 订单设置 -->
  <div class="indent-set">
    <el-form
      :model="indentForm"
      :rules="indentRules"
      ref="seniorForm"
      label-position="right"
      label-width="200px"
      class="company-info"
    >
      <div class="indent-set-form">
        <!-- 默认订单类型 -->
        <el-form-item :label="$t('DefaultOrderType')" prop="type">
          <el-select
            v-model="indentForm.type"
            :placeholder="$t('DefaultOrderType')"
            size="medium"
            style="width: 345px"
          >
            <el-option
              v-for="(item, index) in packageTypeList"
              :key="index"
              :label="item.name"
              :value="item.id"
            ></el-option>
          </el-select>
        </el-form-item>
        <!-- 默认回单类型 -->
        <el-form-item :label="$t('DefaultReceiptType')" prop="receipt_type">
          <el-select
            v-model="indentForm.receipt_type"
            :placeholder="$t('DefaultReceiptType')"
            size="medium"
            style="width: 345px"
          >
            <el-option
              v-for="(item, index) in orderReceiptTypeList"
              :key="index"
              :label="item.name"
              :value="item.id"
            ></el-option>
          </el-select>
        </el-form-item>
      </div>
      <div class="indent-set-form">
        <!-- 默认控货方式-->
        <el-form-item :label="$t('DefaultMode')" prop="control_mode">
          <el-select
            v-model="indentForm.control_mode"
            :placeholder="$t('DefaultMode')"
            size="medium"
            style="width: 345px"
          >
            <el-option
              v-for="(item, index) in orderControlModeList"
              :key="index"
              :label="item.name"
              :value="item.id"
            ></el-option>
          </el-select>
        </el-form-item>
        <!-- 默认付款方式 -->
        <el-form-item
          :label="$t('DefaultPaymentMethod')"
          prop="settlement_type"
        >
          <el-select
            v-model="indentForm.settlement_type"
            :placeholder="$t('DefaultPaymentMethod')"
            size="medium"
            style="width: 345px"
          >
            <el-option
              v-for="(item, index) in orderSettlementTypeList"
              :key="index"
              :label="item.name"
              :value="item.id"
            ></el-option>
          </el-select>
        </el-form-item>
      </div>
      <div class="indent-set-form">
        <!-- 默认回单数量 -->
        <el-form-item :label="$t('DefaultReceiptNumber')" prop="receipt_count">
          <el-input-number
            v-model="indentForm.receipt_count"
            :min="0"
            size="medium"
            style="width: 345px"
          ></el-input-number>
        </el-form-item>
        <!-- 包裹内容物 -->
        <el-form-item :label="$t('PackageContents')" prop="nature">
          <el-select
            v-model="indentForm.nature"
            :placeholder="$t('PackageContents')"
            size="medium"
            style="width: 345px"
          >
            <el-option
              v-for="(item, index) in orderNatureList"
              :key="index"
              :label="item.name"
              :value="item.id"
            ></el-option>
          </el-select>
        </el-form-item>
      </div>
      <el-form-item :label="$t('AddressLibrary')" prop="nature"> </el-form-item>
      <!-- 地址库模板 -->
      <div class="template-box show-template-box">
        <!-- 模板一 -->
        <!-- <span>地址库模板</span> -->
        <div
          :class="{
            'address-template': true,
            'address-template-check': indentForm.address_template_id === 1,
          }"
        >
          <el-form
            :model="template"
            :rules="rules"
            label-position="right"
            label-width="120px"
          >
            <el-row>
              <el-col>
                <p>{{ $t("AddressLibrary1") }}</p>
              </el-col>
              <div style="margin-left: -50px">
                <el-col :span="12">
                  <el-form-item :label="$t('Name')" prop="name">
                    <el-input v-model="template.name" disabled></el-input>
                  </el-form-item>
                </el-col>
                <el-col :span="12">
                  <el-form-item :label="$t('Tel')" prop="name">
                    <el-input v-model="template.name" disabled></el-input>
                  </el-form-item>
                </el-col>
                <el-col>
                  <el-form-item :label="$t('Country')" prop="name">
                    <el-input v-model="template.name" disabled></el-input>
                  </el-form-item>
                </el-col>
                <el-col :span="12">
                  <el-form-item :label="$t('ZipCode')" prop="name">
                    <el-input v-model="template.name" disabled></el-input>
                  </el-form-item>
                </el-col>
                <el-col :span="12">
                  <el-form-item :label="$t('DoorNo')" prop="name">
                    <el-input v-model="template.name" disabled></el-input>
                  </el-form-item>
                </el-col>
                <el-col :span="12">
                  <el-form-item :label="$t('City')" prop="name">
                    <el-input v-model="template.name" disabled></el-input>
                  </el-form-item>
                </el-col>
                <el-col :span="12">
                  <el-form-item :label="$t('Street')" prop="name">
                    <el-input v-model="template.name" disabled></el-input>
                  </el-form-item>
                </el-col>
              </div>
            </el-row>
          </el-form>
          <div class="address-template-radio">
            <el-radio v-model="indentForm.address_template_id" :label="1">{{
              $t("SelectTemplate")
            }}</el-radio>
          </div>
        </div>

        <!-- 模板二 -->
        <div
          :class="{
            'address-template': true,
            'address-template-check': indentForm.address_template_id === 2,
          }"
        >
          <el-form
            :model="template"
            :rules="rules"
            label-position="right"
            label-width="100px"
          >
            <el-row>
              <el-col>
                <p>{{ $t("AddressLibrary2") }}</p>
              </el-col>
              <div style="margin-left: -20px">
                <el-col>
                  <el-form-item :label="$t('Name')" prop="name">
                    <el-input v-model="template.name" disabled></el-input>
                  </el-form-item>
                </el-col>
                <el-col>
                  <el-form-item :label="$t('Tel')" prop="name">
                    <el-input v-model="template.name" disabled></el-input>
                  </el-form-item>
                </el-col>
                <el-col>
                  <el-form-item :label="$t('Address')" prop="name">
                    <el-input v-model="template.name" disabled>
                      <el-button
                        slot="append"
                        disabled
                        icon="el-icon-location"
                      ></el-button>
                    </el-input>
                  </el-form-item>
                </el-col>
              </div>
            </el-row>
          </el-form>
          <div class="address-template-radio">
            <el-radio v-model="indentForm.address_template_id" :label="2">{{
              $t("SelectTemplate")
            }}</el-radio>
          </div>
        </div>
      </div>
    </el-form>
    <!-- 提交 -->
    <el-tooltip
      :content="$t('AdministratorActivate')"
      placement="top"
      :disabled="hasPerm('order-default-config.update') ? true : false"
      style="width: 440px"
    >
      <div class="save save-indentForm">
        <el-button
          :disabled="!hasPerm('order-default-config.update')"
          type="primary"
          @click="putIndentSet"
          :loading="this.$store.state.config.button_loading"
          :class="[
            hasPerm('order-default-config.update')
              ? 'submit'
              : 'submit-disabled',
          ]"
          >{{ $t("Submit") }}</el-button
        >
      </div>
    </el-tooltip>
  </div>
</template>

<script>
export default {
  name: "IndentSet",
  data() {
    return {
      tab: "1",
      indentForm: {
        // 订单设置信息
        type: "", //默认订单类型
        receipt_type: "", //默认回单类型
        control_mode: "", //默认控货方式
        settlement_type: "", //默认付款方式
        receipt_count: 0, //默认回单数量
        nature: "", //包裹内容物
        address_template_id: null, //地址模板ID
        show_type: null,
      },
      packageTypeList: [], //默认订单类型
      orderReceiptTypeList: [], //默认回单类型
      orderControlModeList: [], //默认控货方式
      orderSettlementTypeList: [], //默认付款方式
      orderNatureList: [], //包裹内容物

      address: [],
      allCountry: [],
      show_country: false,
      addCountryInfo: {
        short: "",
      },
      // 选择模板
      // check_template: false,
      // 表单
      template: {
        name: null,
      },

      indentRules: {
        // 表单验证
        type: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        receipt_type: [
          { required: true, message: this.$t("Required"), trigger: "blur" },
        ],
        control_mode: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        settlement_type: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        receipt_count: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        nature: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
      },

      rules: {
        // 地址库模板
        name: [
          {
            required: true,
            message: this.$t("PleaseEnterCompanyName"),
            trigger: "blur",
          },
        ],
        contacts: [
          {
            required: true,
            message: this.$t("PleaseEnterContactPerson"),
            trigger: "blur",
          },
        ],
        phone: [
          {
            required: true,
            message: this.$t("PleaseEnterCompanyTel"),
            trigger: "blur",
          },
        ],
        country: [
          {
            required: true,
            message: this.$t("PleaseSelectCountry"),
            trigger: "blur",
          },
        ],
        address: [
          {
            required: true,
            message: this.$t("PleaseEnterCompanyAddress"),
            trigger: "blur",
          },
        ],
        short: [
          {
            required: true,
            message: this.$t("PleaseSelectACountry"),
            trigger: "blur",
          },
        ],
      },
    };
  },
  created() {
    this.getDictionary(); //数据字典
    this.getIndentSet(); //拉取订单设置详情
  },

  methods: {
    // 数据字典
    getDictionary() {
      this.$api.getDictionary().then((res) => {
        this.packageTypeList = res.data.packageTypeList; //默认订单类型
        this.orderReceiptTypeList = res.data.orderReceiptTypeList; //默认回单类型
        this.orderControlModeList = res.data.orderControlModeList; //默认控货方式
        this.orderSettlementTypeList = res.data.orderSettlementTypeList; //默认付款方式
        this.orderNatureList = res.data.orderNatureList; //包裹内容物
      });
    },
    // 拉取订单设置详情
    getIndentSet() {
      this.$api.getIndentSet().then((res) => {
        this.indentForm = res.data;
      });
    },
    // 修改订单设置
    putIndentSet() {
      this.$refs.seniorForm.validate((valid) => {
        if (!valid) return;
        this.$api.putIndentSet(this.indentForm).then((res) => {
          if (!res) return;
          // 模板标识
          localStorage.setItem(
            "address_template_id",
            this.indentForm.address_template_id
          );
          this.getIndentSet();
        });
      });
    },
  },
};
</script>
<style lang="less" scoped>
.indent-set {
  padding: 20px 25px 10px;
  .indent-set-form {
    display: flex;
  }
  .save {
    .el-button {
      margin-left: 160px;
      border-radius: 0;
      padding-left: 44px;
      padding-right: 44px;
    }
  }
  .save-indentForm {
    margin-left: 0px;
  }
}
</style>
<style lang="less">
.indent-set {
  .show-template-box {
    display: grid;
    grid-template-columns: 1fr 2fr;
    margin-top: -50px;
    margin-bottom: 30px;
    margin-left: 200px;
    .address-template {
      width: 345px;
      border: 1px solid #ebeef5;
      box-shadow: 0 0 3px 1px #f2f6fc;
      box-sizing: border-box;
      padding: 20px;
      margin-right: 200px;
    }
    .address-template-radio {
      display: flex;
      justify-content: center;
    }
    .address-template-check {
      border-color: #c4c4c4;
    }
  }
}
/* 全局修改 */
.el-select-dropdown {
  border-radius: 0;
}
</style>
