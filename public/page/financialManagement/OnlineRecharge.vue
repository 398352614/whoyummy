<template>
  <div class="online-recharge">
    <!-- 在线充值 -->
    <div class="recharge-box">
      <el-form ref="form" :rules="rules" :model="form" label-width="150px">
        <el-form-item :label="$t('SelectMerchant')" prop="merchantName">
          <el-input
            v-model="form.merchantName"
            disabled
            style="width: 252px; margin-right: 15px"
          ></el-input>
          <el-button type="primary" @click="getSelectMerchant">{{
            $t("SelectMerchant")
          }}</el-button>
        </el-form-item>
        <el-form-item :label="$t('RechargeMethod')" prop="pay_type">
          <el-select
            v-model="form.pay_type"
            placeholder=" "
            style="width: 370px"
          >
            <el-option
              v-for="item in payTypeList"
              :key="item.id"
              :label="item.name"
              :value="item.id"
            >
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item
          :label="$t('RechargeAmount') + currency_unit_symbol"
          prop="expect_amount"
        >
          <el-input v-model="form.expect_amount"></el-input>
        </el-form-item>
        <el-form-item :label="$t('remark')" prop="remark">
          <el-input v-model="form.remark"></el-input>
        </el-form-item>
        <el-form-item :label="$t('screenshot')" prop="image">
          <el-upload
            class="avatar-uploader"
            :headers="Authorization"
            :on-success="handleAvatarSuccess"
            :action="BaseApiImg"
            :on-remove="handlerClearDeiver"
            ref="my-upload"
            :limit="1"
            :data="{
              dir: 'driver',
            }"
            name="image"
          >
            <img
              v-if="form.picture_list"
              :src="form.picture_list"
              class="avatar"
            />
            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
          </el-upload>
        </el-form-item>
        <el-form-item>
          <el-tooltip
            :content="$t('AdministratorActivate')"
            placement="top"
            :disabled="hasPerm('bill.merchant-recharge') ? true : false"
          >
            <div>
              <el-button
                type="primary"
                @click="onSubmit"
                :disabled="!hasPerm('bill.merchant-recharge')"
                >{{ $t("Submit") }}
              </el-button>
            </div>
          </el-tooltip>
        </el-form-item>
      </el-form>
    </div>
    <div>
      <el-dialog
        :title="$t('SelectMerchant')"
        :visible.sync="dialogTableVisible"
        width="65%"
      >
        <!-- 所有内容 -->
        <el-input
          @keyup.enter.native="getMerchants"
          v-model="pageParams.keyword"
          clearable
          :placeholder="$t('enterContent')"
          size="small"
          class="search-bar"
        >
        </el-input>
        <el-button
          size="small"
          @click="getMerchants"
          type="primary"
          :loading="this.$store.state.config.button_loading"
          >{{ $t("Search") }}</el-button
        >
        <el-table
          :data="merchantData"
          @current-change="handleCarSelectionChange"
          max-height="400px"
          border
        >
          <el-table-column width="50" align="center" style="text-align: center">
            <template slot-scope="scope">
              <el-radio v-model="merchant.id" :label="scope.row.id"
                >{{ " " }}
              </el-radio>
            </template>
          </el-table-column>
          <el-table-column type="index" align="center" width="50">
            <template slot="header">
              <span>{{ $t("Number") }}</span>
            </template>
          </el-table-column>
          <el-table-column
            property="code"
            align="center"
            :label="$t('cargoOwnerNo')"
          ></el-table-column>
          <el-table-column
            property="name"
            align="center"
            :label="$t('Merchant')"
          ></el-table-column>
          <el-table-column
            property="email"
            align="center"
            :label="$t('Email')"
          ></el-table-column>
          <el-table-column
            property="phone"
            align="center"
            :label="$t('Tel')"
          ></el-table-column>
        </el-table>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogTableVisible = false" class="cancel">
            {{ $t("Cancel") }}</el-button
          >
          <el-button type="primary" @click="confirm" class="submit">
            {{ $t("Confirm") }}</el-button
          >
        </div>
      </el-dialog>
    </div>
  </div>
</template>

<script>
import baseApi from "@/api/axios/base_api";
export default {
  data() {
    // 数值范围
    var valiNumberPass = (rule, value, callback) => {
      //包含小数的数字
      let reg = /^[+-]?(0|([1-9]\d*))(\.\d+)?$/g;
      if (value === "") {
        callback(new Error(this.$t("Required")));
      } else if (!reg.test(value)) {
        callback(new Error(this.$t("TheAmountMustBeGreater")));
      } else if (value > 1000000) {
        callback(new Error(this.$t("TheAmountMustBeGreater")));
      } else {
        if (value < 0) {
          callback(new Error(this.$t("TheAmountMustBeGreater")));
        } else {
          callback();
        }
      }
    };
    return {
      imageUrl: "",
      currency_unit_symbol: "",
      dialogTableVisible: false,
      merchantData: [],
      payTypeList: [],
      form: {
        sign: 1,
        merchant_id: "",
        merchantName: "",
        pay_type: "",
        expect_amount: "",
        remark: "",
        picture_list: "",
      },
      merchant: {
        id: "",
        name: "",
      },
      pageParams: {
        keyword: "",
        status: 1,
      },
      rules: {
        merchantName: [
          { required: true, message: this.$t("Required"), trigger: "change" },
        ],
        pay_type: [
          { required: true, message: this.$t("Required"), trigger: "change" },
        ],

        expect_amount: [
          { required: true, validator: valiNumberPass, trigger: "blur" },
        ],
        remark: [
          {
            required: false,
            max: 255,
            message: this.$t("EnterCharacters"),
            trigger: "blur",
          },
        ],
      },
    };
  },
  computed: {
    Authorization() {
      return { Authorization: this.$store.state.token.token };
    },
    BaseApiImg() {
      return `${baseApi}admin/upload/image`;
    },
  },
  created() {
    this.getAdvanceSetting();
    this.getDictionary();
  },
  methods: {
    // 获取单位值
    getAdvanceSetting() {
      this.$api.getAdvanceSetting().then((res) => {
        this.currency_unit_symbol = res.data.currency_unit_symbol; //单价单位
      });
    },
    getDictionary() {
      this.$api.getDictionary().then((res) => {
        this.payTypeList = res.data.rechargePayTypeList;
      });
    },
    //货主列表-查询
    getMerchants() {
      this.$api.getMerchants(this.pageParams).then((res) => {
        this.merchantData = res.data.data;
      });
    },
    // 选择货主
    getSelectMerchant() {
      this.dialogTableVisible = true;
      this.merchant.id = "";
      this.merchant.name = "";
      this.getMerchants();
    },
    //  列表单选
    handleCarSelectionChange(selected) {
      if (!selected) return;
      this.merchant.id = selected.id;
      this.merchant.name = selected.name;
    },
    // 确定
    confirm() {
      if (this.merchant.name == "") {
        this.$message({
          type: "warning",
          message: this.$t("PleaseSelectMerchant"),
        });
        return;
      } else {
        this.form.merchant_id = this.merchant.id;
        this.form.merchantName = this.merchant.name;
        this.dialogTableVisible = false;
      }
    },
    // 提交
    onSubmit() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          this.$api.getOnlineRecharge(this.form).then((res) => {
            if (res.code == 200) {
              this.form = {
                merchant_id: "",
                merchantName: "",
                pay_type: "",
                expect_amount: "",
                remark: "",
                picture_list: "",
              };
              this.$refs["my-upload"].clearFiles();
              this.$refs.form.resetFields();
            }
          });
        }
      });
    },
    // 图片上传成功
    handleAvatarSuccess(res) {
      this.form.picture_list = res.data.path;
    },
    // 清除图片
    handlerClearDeiver() {
      this.form.picture_list = "";
    },
  },
};
</script>

<style lang="less" scoped>
.online-recharge {
  min-height: calc(100vh - 120px);
  background-color: #fff;
  .recharge-box {
    padding: 20px;
    .el-input {
      width: 370px;
    }
  }
  .search-bar {
    margin-bottom: 10px;
  }
}
</style>