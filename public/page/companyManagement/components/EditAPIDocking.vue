<template>
  <div class="addMerchantManagement">
    <div class="form_main">
      <el-form :rules="formRules" ref="form" :model="form" label-width="200px">
        <el-form-item :label="$t('ApiKey')">
          {{ form.key }}
        </el-form-item>
        <el-form-item :label="$t('Api Secret')">
          {{ form.secret }}
        </el-form-item>
        <el-form-item :label="$t('是否推送')" prop="status">
          <el-switch
            v-model="form.status"
            :active-value="1"
            :inactive-value="2"
          >
          </el-switch>
        </el-form-item>
        <el-form-item :label="$t('现金充值')" prop="recharge_status">
          <el-switch
            v-model="form.recharge_status"
            :active-value="1"
            :inactive-value="2"
          >
          </el-switch>
        </el-form-item>
        <el-form-item :label="$t('推送URL')" prop="name">
          <el-input v-model="form.url"></el-input>
        </el-form-item>
        <el-form-item label="" prop="name">
          <el-input
            v-model="form.white_ip_list"
            type="textarea"
            :rows="4"
          ></el-input>
        </el-form-item>
        <el-form-item label="" prop="name">
          <p>一个IP地址为一行，多个请按回车，不填为代表所有</p>
          <p>例如:</p>
          <p>1.1.1.1</p>
          <p>2.2.2.2</p>
        </el-form-item>
      </el-form>
      <div class="footer_button">
        <el-button size="small" @click="handlerCancel">{{
          $t("Cancel")
        }}</el-button>
        <el-button
          size="small"
          type="primary"
          @click="onSubmit"
          :loading="this.$store.state.config.button_loading"
          >{{ $t("Submit") }}</el-button
        >
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "addMerchantGroupManagement",
  data() {
    return {
      formRules: {
        status: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        recharge_status: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        url: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        white_ip_list: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
      },
      form: {
        status: "",
        recharge_status: "",
        url: "",
        white_ip_list: "",
      },
    };
  },
  created() {
    this.getMerchantApiDetails(this.$route.query.id);
  },
  methods: {
    handlerCancel() {
      this.$router.replace({
        name: "APIDockingManagement",
      });
    },
    getMerchantApiDetails(id) {
      this.$api.getMerchantApiDetails(id).then((res) => {
        if (!res) return;
        this.form = res.data;
      });
    },

    onSubmit() {
      this.$refs.form.validate((vaild) => {
        if (!vaild) return;
        this.$api
          .putMerchantApi(this.$route.query.id, this.form)
          .then((res) => {
            if (!res) return;
            this.handlerCancel();
          });
        return;
      });
    },
  },
};
</script>

<style lang="less">
@import "../../../less/public_variable.less";

.addMerchantManagement {
  background-color: #fff;
  padding: 20px;
  min-height: 500px;
  .form_main {
    margin: 0 auto;
    max-width: 660px;
  }
  .form_box {
    display: flex;
    justify-content: space-between;
  }
  .add_button {
    float: right;
  }
  .footer_button {
    position: relative;
    top: 0;
    right: -90%;
  }
  .label_info {
    position: relative;
    left: -40px;
    font-size: 20px;
  }
  .avatar_box {
    margin: 30px;
  }
  .avatar_desc {
    margin-top: -20px;
    text-align: center;
  }
}
</style>
