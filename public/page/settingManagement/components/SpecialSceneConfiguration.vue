<template>
  <div class="special_scene_configuration">
    <p style="font-size: 20px">{{ $t("abnormal") }}</p>

    <div style="margin-left: 20px">
      <span>{{ $t("examinationPassed") }}</span>
      <el-tooltip
        :content="$t('AdministratorActivate')"
        placement="top"
        :disabled="hasPerm('special-scenes-config.update') ? true : false"
      >
        <el-switch
          v-model="companyInfo.stock_exception_verify"
          :active-value="2"
          :inactive-value="1"
          :disabled="!hasPerm('special-scenes-config.update')"
          @change="postCompanyConfigure"
        >
        </el-switch>
      </el-tooltip>
    </div>
  </div>
</template>

<script>
export default {
  name: "SpecialSceneConfiguration",
  data() {
    return {
      companyInfo: {
        stock_exception_verify: 2,
      },
    };
  },
  created() {
    this.getCompanyDetails();
  },
  methods: {
    // 特殊场景配置 -- 开始
    getCompanyDetails() {
      // 详情
      this.$api.getCompanyConfigShow().then((res) => {
        if (res.code == 200) {
          this.companyInfo = res.data;
        }
      });
    },

    postCompanyConfigure() {
      // 修改
      this.$api.putCompanyConfigUpdate(this.companyInfo).then((res) => {});
    },
    // 特殊场景配置 -- 结束
  },
};
</script>
<style lang="less" scoped>
.special_scene_configuration {
  background: #ffffff;
  border: 1px solid rgba(228, 235, 241, 1);
  padding: 20px 25px 10px;
}
</style>
