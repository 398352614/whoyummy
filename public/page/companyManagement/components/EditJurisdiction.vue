<template>
  <!-- 编辑权限 -->
  <div class="editJurisdiction">
    <div class="label-info">{{ $t("advancedSettings") }}</div>
    <el-form
      :rules="formRules"
      ref="formData"
      :model="formData"
      label-width="300px"
    >
      <el-form-item :label="$t('orderTimeConfiguration') + $t('day')">
        <el-select size="small" v-model="formData.advance_days">
          <el-option
            v-for="item in [0, 1, 2, 3, 4, 5, 6, 7]"
            :key="item"
            :label="item"
            :value="item"
          >
          </el-option>
        </el-select>
        <el-tooltip
          effect="dark"
          :content="this.$t('afterConfiguringTheTime')"
          placement="top"
        >
          <span class="el-icon-warning icon-question"></span>
        </el-tooltip>
      </el-form-item>
      <el-form-item
        :label="$t('availableTimeRange') + $t('day')"
        prop="appointment_days"
      >
        <el-input size="small" v-model="formData.appointment_days"></el-input>
      </el-form-item>
      <el-form-item
        :label="$t('delayAppointmentTime') + $t('minute')"
        prop="delay_time"
      >
        <el-input
          size="small"
          :max="60"
          :min="0"
          v-model="formData.delay_time"
        ></el-input>
        <el-tooltip
          effect="dark"
          :content="this.$t('afterConfiguringTheThreshold')"
          placement="top"
        >
          <span class="el-icon-warning icon-question"></span>
        </el-tooltip>
      </el-form-item>
      <el-form-item :label="$t('valueAddedServiceConfiguration')">
        <el-select size="small" multiple v-model="fee_code_list">
          <el-option
            v-for="item in feeList"
            :key="item.id"
            :label="item.name"
            :value="item.code"
          >
          </el-option>
        </el-select>
      </el-form-item>
      <el-form-item :label="$t('whetherBusiness')">
        <el-switch
          v-model="formData.additional_status"
          :active-value="1"
          :inactive-value="2"
        >
        </el-switch>
      </el-form-item>
      <el-form-item :label="$t('automaticPickupFailure')">
        <el-select size="small" v-model="formData.pickup_count">
          <el-option
            v-for="item in failList"
            :key="item.value"
            :label="item.label"
            :value="item.value"
          >
          </el-option>
        </el-select>
      </el-form-item>
      <el-form-item :label="$t('dispatchTerminate')">
        <el-select size="small" v-model="formData.pie_count">
          <el-option
            v-for="item in failList"
            :key="item.value"
            :label="item.label"
            :value="item.value"
          >
          </el-option>
        </el-select>
      </el-form-item>
    </el-form>
    <div class="footer-button">
      <el-button size="small" @click="handlerCancel" class="cancel">{{
        $t("Cancel")
      }}</el-button>
      <el-button
        size="small"
        type="primary"
        @click="onSubmit"
        :loading="this.$store.state.config.button_loading"
        class="submit"
        >{{ $t("Submit") }}</el-button
      >
    </div>
  </div>
</template>

<script>
import baseApi from "@/api/axios/base_api";

export default {
  name: "editJurisdiction",
  data() {
    return {
      formData: {
        advance_days: 0,
        appointment_days: "",
        delay_time: 0,
        fee_code_list: "",
        additional_status: 1,
        pie_count: null,
        pickup_count: null,
      },
      fee_code_list: [],
      feeList: [],
      feePageParams: {
        merchant_group_id: "",
      },

      failList: [
        {
          label: this.$t("ManualTermination"),
          value: 0,
        },
        {
          label: this.$t("automaticTerminationAfterFailure"),
          value: 1,
        },
        {
          label: this.$t("automaticTerminationAfter2Failure"),
          value: 2,
        },
        {
          label: this.$t("automaticTerminationAfter3Failure"),
          value: 3,
        },
        {
          label: this.$t("automaticTerminationAfter4Failure"),
          value: 4,
        },
      ],
    };
  },
  created() {
    this.getFee();
    this.getMerchantsGroupDetial(this.$route.query.id);
  },
  computed: {
    isEdit() {
      return !!this.$route.query.id;
    },
    formRules() {
      return {
        name: [
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        email: [
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        contacter: [
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        phone: [
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        address: [
          { required: true, trigger: "blur", message: this.$t("Required") },
        ],
        appointment_days: [
          {
            validator: (rule, value, callback) => {
              if (value < 0) {
                callback(new Error("必须大于0"));
              } else {
                callback();
              }
            },
            trigger: "change",
          },
        ],
        delay_time: [
          {
            validator: (rule, value, callback) => {
              let isnum2 = /^\d+$/.test(value);
              if (!isnum2 && value) {
                callback(new Error("必须是纯数字"));
              } else if (value < 0) {
                callback(new Error("必须大于0"));
              } else if (value > 60) {
                callback(new Error("必须小于60"));
              } else {
                callback();
              }
            },
            trigger: "change",
          },
        ],
      };
    },
  },
  methods: {
    getFee() {
      // 增值服务列表
      this.$api.getFeeList(this.feePageParams).then((res) => {
        this.feeList = res.data;
        console.log(this.feeList);
      });
    },

    getMerchantsGroupDetial(id) {
      // 详情
      this.$api.getMerchantsGroupDetial(id).then((res) => {
        if (!res) return;
        this.formData = res.data;
        this.fee_code_list = res.data.fee_list.map((el) => {
          return el.code;
        });
      });
    },

    handlerCancel() {
      // 取消编辑
      this.transformRouter();
    },

    transformRouter() {
      // 回到组列表
      this.$router.replace({
        name: "MerchantGroupManagement",
      });
    },

    onSubmit() {
      // 提交
      this.$refs.formData.validate((validateRes) => {
        if (!validateRes) return;
        this.formData.fee_code_list = this.fee_code_list.join(",");
        delete this.formData.fee_list;
        this.$api
          .putMerchantGroupConfig(this.$route.query.id, this.formData)
          .then((res) => {
            if (!res) return;
            this.transformRouter();
          });
      });
    },
  },
};
</script>

<style lang="less" scoped>
@import "../../../less/public_variable.less";

.editJurisdiction {
  background-color: #fff;
  padding: 20px;
  .add-button {
    float: right;
  }
  .footer-button {
    text-align: right;
  }
  .label-info {
    margin-left: 70px;
    // position: relative;
    // // left: 50px;
    font-size: 20px;
  }
  .el-form {
    margin-left: 50px;
  }
  .el-form-item {
    max-width: 600px;
  }
  .el-input {
    width: 204px;
  }
  .el-icon-warning {
    color: #efb336;
  }
}
</style>
