<template>
  <div class="online-recharge">
    <!--手动扣款 -->
    <div class="recharge-box">
      <el-form ref="form" :rules="rules" :model="form" label-width="150px">
        <el-form-item :label="$t('SelectMerchant')" prop="merchant_id">
          <el-input
            v-model="form.merchant_id"
            disabled
            style="width: 252px; margin-right: 15px"
          ></el-input>
          <el-button type="primary" @click="getSelectMerchant">{{
            $t("SelectMerchant")
          }}</el-button>
        </el-form-item>
        <el-form-item :label="$t('DeductionMethod')" prop="amount">
          {{ "余额" }}
        </el-form-item>
        <!-- 费用类型 -->
        <el-form-item :label="$t('TypesOfFee')" prop="name">
          <el-select
            v-model="form.merchant_id"
            placeholder=" "
            style="width: 370px"
          >
            <el-option
              v-for="item in options"
              :key="item.value"
              :label="item.label"
              :value="item.value"
            >
            </el-option>
          </el-select>
        </el-form-item>
        <!-- 系统编号 -->
        <el-form-item :label="$t('SystemNumber')" prop="amount">
          <el-input v-model="form.amount"></el-input>
        </el-form-item>
        <!-- 扣除金额 -->
        <el-form-item
          :label="$t('DeductionAmount') + currency_unit_symbol"
          prop="amount"
        >
          <span>-</span>
          <el-input
            v-model.number="form.amount"
            style="width: 361px"
          ></el-input>
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
            :limit="1"
            :data="{
              dir: 'driver',
            }"
            name="image"
          >
            <img
              v-if="form.accident_picture"
              :src="form.accident_picture"
              class="avatar"
            />
            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
          </el-upload>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="onSubmit"
            >{{ $t("Submit") }}
          </el-button>
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
          @keyup.enter.native="handleChangePageParams"
          v-model="pageParams.key_word"
          clearable
          :placeholder="$t('enterContent')"
          size="small"
          class="search-bar"
        >
        </el-input>
        <el-button
          size="small"
          @click="handleChangePageParams"
          type="primary"
          :loading="this.$store.state.config.button_loading"
          >{{ $t("Search") }}</el-button
        >
        <el-table
          :data="merchantData"
          max-height="400px"
          border
          @current-change="handleCarSelectionChange"
        >
          <el-table-column width="50" align="center">
            <template slot-scope="scope">
              <el-radio v-model="merchant.id" :label="scope.row.id">{{
                ""
              }}</el-radio>
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
      let reg = /^[+-]?(0.1|([1-9]\d*))(\.\d+)?$/g;
      if (value === "") {
        callback(new Error(this.$t("Required")));
      } else if (!reg.test(value)) {
        callback(new Error(this.$t("TheAmountMustBeGreater")));
      } else {
        if (value > 1000000) {
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
      options: [],
      form: {
        merchant_id: "",
        name: "",
        amount: "",
        remark: "",
        accident_picture: "",
      },
      pageParams: {
        key_word: "",
      },
      merchant: {
        id: "",
        name: "",
      },
      rules: {
        merchant_id: [
          { required: true, message: this.$t("Required"), trigger: "blur" },
        ],
        name: [
          { required: true, message: this.$t("Required"), trigger: "blur" },
        ],

        amount: [
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
  },
  methods: {
    // 获取单位值
    getAdvanceSetting() {
      this.$api.getAdvanceSetting().then((res) => {
        this.currency_unit_symbol = res.data.currency_unit_symbol; //单价单位
      });
    },
    getMerchants() {
      //货主列表
      this.$api.getMerchants().then((res) => {
        this.merchantData = res.data.data;
      });
    },
    // 查询
    handleChangePageParams() {},
    getSelectMerchant() {
      this.dialogTableVisible = true;
      this.merchant.id = "";

      this.getMerchants();
    },
    // 单选
    handleCarSelectionChange(rows) {
      this.merchant.id = rows.id;
      this.merchant.name = rows.name;
    },
    // 确定
    confirm() {
      this.form.merchant_id = this.merchant.name;
      this.dialogTableVisible = false;
    },
    // 提交
    onSubmit() {
      this.$router.push({ name: "FinancialReviewList" });
    },
    // 图片上传成功
    handleAvatarSuccess(res) {
      this.form.accident_picture = res.data.path;
    },
    // 清除图片
    handlerClearDeiver() {
      this.form.accident_picture = "";
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