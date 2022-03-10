<template>
  <!-- 计量单位设置 -->
  <div class="measure-box">
    <el-form
      :model="form"
      :rules="rules"
      ref="form"
      label-position="right"
      label-width="160px"
    >
      <!-- 重量单位 -->
      <el-form-item :label="$t('WeightUnit')" prop="weight_unit">
        <el-select
          v-model="form.weight_unit"
          :placeholder="$t('PleaseSelectAWeightUnit')"
          style="width: 30%"
        >
          <el-option
            v-for="(item, index) in weightUnitTypeList"
            :key="index"
            :label="item.name"
            :value="item.id"
          ></el-option>
        </el-select>
      </el-form-item>
      <!-- 货币单位 -->
      <el-form-item :label="$t('CurrencyUnit')" prop="currency_unit">
        <el-select
          v-model="form.currency_unit"
          :placeholder="$t('PleaseSelectACurrencyUnit')"
          style="width: 30%"
        >
          <el-option
            v-for="(item, index) in currencyUnitTypeSymbol"
            :key="index"
            :label="item.name"
            :value="item.id"
          ></el-option>
        </el-select>
      </el-form-item>
      <!-- 体积单位 -->
      <el-form-item :label="$t('VolumeUnit')" prop="volume_unit">
        <el-select
          v-model="form.volume_unit"
          :placeholder="$t('PleaseSelectAVolumeUnit')"
          style="width: 30%"
        >
          <el-option
            v-for="(item, index) in volumeUnitTypeList"
            :key="index"
            :label="item.name"
            :value="item.id"
          >
          </el-option>
        </el-select>
      </el-form-item>
    </el-form>
    <el-tooltip
      :content="$t('AdministratorActivate')"
      placement="top"
      :disabled="hasPerm('company-config.unit-update') ? true : false"
      style="width: 440px"
    >
      <div class="save">
        <el-button
          :disabled="!hasPerm('company-config.unit-update')"
          type="primary"
          @click="submit"
          :class="[
            hasPerm('company-config.unit-update')
              ? 'submit'
              : 'submit-disabled',
          ]"
          :loading="this.$store.state.config.button_loading"
          >{{ $t("Submit") }}</el-button
        >
      </div>
    </el-tooltip>
  </div>
</template>

<script>
export default {
  name: "Measure",
  data() {
    return {
      tab: "1",
      form: {
        // 计量单位设置
        weight_unit: "",
        currency_unit: "",
        volume_unit: "",
      },
      weightUnitTypeList: [
        {
          id: 1,
          name: this.$t("kg"),
        },
        {
          id: 2,
          name: this.$t("lb"),
        },
      ], //重量
      currencyUnitTypeSymbol: [
        {
          id: 1,
          name: this.$t("RMB"),
        },
        {
          id: 2,
          name: this.$t("USD"),
        },
        {
          id: 3,
          name: this.$t("Euro"),
        },
      ], //货币
      volumeUnitTypeList: [
        {
          id: 1,
          name: this.$t("cubicCentimeter"),
        },
        {
          id: 2,
          name: this.$t("cubicMeter"),
        },
      ], //体积
      //   表单验证
      rules: {
        weight_unit: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "change",
          },
        ],
        currency_unit: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "change",
          },
        ],
        volume_unit: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "change",
          },
        ],
      },
    };
  },
  created() {
    // this.getDictionary(); //数据字典
    this.getMeasure(); //拉取计量详情
  },
  methods: {
    //   数据字典
    // getDictionary() {
    //   this.$api.getDictionary().then((res) => {
    //     this.weightUnitTypeList = res.data.weightUnitTypeList; //重量单位
    //     this.currencyUnitTypeSymbol = res.data.currencyUnitTypeSymbol; //货币单位
    //     this.volumeUnitTypeList = res.data.volumeUnitTypeList; //体积单位
    //   });
    // },
    // 拉取计量详情
    getMeasure() {
      this.$api.getMeasure().then((res) => {
        this.form = res.data;
      });
    },
    submit() {
      this.$refs.form.validate((valid) => {
        if (!valid) return;
        this.$api.putMeasure(this.form).then((res) => {
          if (!res) return;
          // 货币
          this.$store.commit("globalData/setUnit", this.form.currency_unit); // 设置货币单位
          // 重量
          this.$store.commit("globalData/setWeight", this.form.weight_unit); // 设置重量单位
          // 体积
          this.$store.commit("globalData/setVolume", this.form.volume_unit); // 设置体积单位
          // 详情
          this.getMeasure();
        });
      });
    },
  },
};
</script>
<style lang="less" scoped>
.measure-box {
  padding: 20px 25px 10px;
  .save {
    .el-button {
      margin-left: 160px;
      border-radius: 0;
      padding-left: 44px;
      padding-right: 44px;
    }
  }
}
</style>
