<template>
  <div class="cash-recharge-docking">
    <el-form
      class="sen_form"
      :model="info"
      :rules="rules"
      ref="senderForm"
      label-position="right"
      label-width="200px"
    >
      <el-form-item label="现金充值对接URL" prop="url">
        <el-input v-model="info.url" :placeholder="$t('pleaseEnterName')"></el-input>
      </el-form-item>
      <el-form-item label="是否现金充值功能" prop="is_xj">
        <el-switch
          v-model="info.is_xj"
          :active-value="1"
          :inactive-value="2"
          >
        </el-switch>
      </el-form-item>
      
      <div class="sen_btn">
        <el-button type="primary" @click="onSave" :loading="this.$store.state.config.button_loading">{{$t('Cancel')}}</el-button>
        <el-button type="primary" @click="onSubmit" :loading="this.$store.state.config.button_loading">{{$t('Submit')}}</el-button>
      </div>
    </el-form>
  </div>
</template>

<script>
export default {
  name: 'AddReceiver',
  data() {
    return {
      info: {
        url: '',
        is_xj: 2
      },
      isEdit: false,

      rules: {
        url: [{ required: true, message: this.$t('Required'), trigger: 'change' }],
        is_xj: [{ required: true, message: this.$t('Required'), trigger: 'change' }],
      },
    };
  },
  created() {
  },
  methods: {
    onSave() {
      this.$refs.senderForm.validate((valid) => {
        if (!valid) return;
        if (this.isEdit) {
          this.$api.putSenderAdress(this.senderInfo).then((res) => {
            if (!res) return;
            this.$store.commit('config/refreshRouter', Math.random());
          });
        } else {
          this.$api.addSenderAdress(this.senderInfo).then((res) => {
            if (!res) return;
            this.$store.commit('config/refreshRouter', Math.random());
          });
        }
      });
    },
    onSubmit() {
      this.$refs.senderForm.validate((valid) => {
        if (!valid) return;
        if (this.isEdit) {
          this.$api.putSenderAdress(this.senderInfo).then((res1) => {
            if (!res1) return;
            this.$router.push({
              name: 'customerManagement',
            });
          });
        } else {
          this.$api.addSenderAdress(this.senderInfo).then((res) => {
            if (!res) return;
            this.$router.push({
              name: 'customerManagement',
            });
          });
        }
      });
    },
  },
};

</script>

<style lang="less" scoped>
.cash-recharge-docking {
  background: #fff;
  padding: 20px;
  .sen_form {
    // width: 80%;
    .sen_btn {
      margin-left: 200px;
    }
  }
  .locale-btn {
    color: #F6A50D !important;
    background-color: #fff !important;
  }
  .el-input {
    max-width: 328px;
  }
}
</style>
