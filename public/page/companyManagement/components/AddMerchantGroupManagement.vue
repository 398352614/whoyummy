<template>
  <!-- 添加组 -->
  <div class="addMerchantManagement">
    <div class="form_main">
      <el-form :rules="formRules" ref="form" :model="form" label-width="200px">
        <el-form-item :label="$t('groupName')" prop="name">
          <el-input size="small" v-model="form.name"></el-input>
        </el-form-item>
        <el-form-item :label="$t('pricingScheme')" prop="transport_price_id">
          <el-select size="small" v-model="form.transport_price_id" filterable>
            <el-option
              v-for="item in transportPrices"
              :key="item.id"
              :label="item.name"
              :value="item.id"
            ></el-option>
          </el-select>
        </el-form-item>
        <el-form-item :label="$t('DefaultGroup')" prop="is_default">
          <el-switch
            v-model="form.is_default"
            :active-value="1"
            :inactive-value="2"
          >
          </el-switch>
          <div>{{ $t("defaultGroupTips") }}</div>
        </el-form-item>
      </el-form>
      <div class="footer_button">
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
  </div>
</template>

<script>
export default {
  name: "addMerchantGroupManagement",
  data() {
    return {
      formRules: {
        name: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        transport_price_id: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        is_default: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
      },
      dialogVisible: false,
      settlementTypes: [],
      drivers: [],
      transportPrices: [],
      form: {
        name: "",
        transport_price_id: "",
        is_default: 2,
      },
      pageParams: {
        page: 1,
        per_page: 10,
        total: 0,
        status: "" || this.$route.query.status,
        driver_id: "", // 司机ID
        begin_date: "",
        end_date: "",
        keyword: "" || this.$route.params.id,
      },
    };
  },
  created() {
    this.getPrices({
      per_page: 200,
    });
    if (this.isEdit) {
      this.getDetials(this.$route.query.id);
    }
  },
  computed: {
    isEdit() {
      return !!this.$route.query.id;
    },
  },
  methods: {
    handlerCancel() {
      this.$router.replace({
        name: "MerchantGroupManagement",
      });
    },
    getPrices(params) {
      this.$api.getTransportPricePublic(params).then((res) => {
        if (!res) return;
        this.transportPrices = res.data.data;
      });
    },
    getDetials(id) {
      this.$api.getMerchantsGroupDetial(id).then((res) => {
        if (!res) return;
        this.form = res.data;
      });
    },
    onSubmit() {
      this.$refs.form.validate((vaild) => {
        if (!vaild) return;
        if (this.isEdit) {
          this.$api
            .editMerchantsGroup(this.$route.query.id, this.form)
            .then((res) => {
              if (!res) return;
              this.$router.replace({
                name: "MerchantGroupManagement",
                params: {
                  MerchantGroupManagement: 1,
                },
              });
            });
          return;
        }
        this.$api.addMerchantsGroup(this.form).then((res) => {
          if (!res) return;
          this.$router.replace({
            name: "MerchantGroupManagement",
            params: {
              MerchantGroupManagement: 1,
            },
          });
        });
      });
    },
  },
};
</script>

<style lang="less" scoped>
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
