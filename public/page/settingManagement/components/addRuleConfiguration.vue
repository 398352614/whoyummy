<template>
  <div class="add-rule-configuration">
    <div class="form_main">
      <el-form :rules="formRules" ref="form" :model="form" label-width="150px">
        <el-form-item :label="$t('danHaoType')" prop="type">
          <el-select v-model="form.type" filterable v-if="!$route.query.id">
            <el-option
              v-for="item in typeList"
              :key="item.id"
              :label="item.name"
              :value="item.id"
            ></el-option>
          </el-select>

          <el-select v-model="form.type_name" filterable v-else disabled>
          </el-select>
        </el-form-item>
        <!-- <el-form-item :label="$t('系统默认生成规则')" prop="name">
          <el-input v-model="form.name"></el-input>
        </el-form-item> -->
        <p class="diy-rules">自定义单号生成规则</p>
        <el-form-item :label="$t('开始字符')" prop="prefix">
          <el-input v-model="form.prefix"></el-input>
        </el-form-item>
        <el-form-item :label="$t('字母位数')">
          <el-input v-model="form.string_length"></el-input>
        </el-form-item>
        <el-form-item :label="$t('数字位数')" prop="int_length">
          <el-input v-model="form.int_length"></el-input>
        </el-form-item>
        <el-form-item>
            <div class="footer-button">
                <el-button @click="handlerCancel">{{$t('Cancel')}}</el-button>
                <el-button
                type="primary"
                @click="onSubmit"
                :loading="this.$store.state.config.button_loading"
                >{{$t('Submit')}}</el-button>
            </div>
        </el-form-item>
      </el-form>
    </div>
  </div>
</template>

<script>
export default {
  name: "addRuleConfiguration",
  data() {
    return {
      typeList: [],
      form: {
        type: null,
        prefix: null,
        string_length: null,
        int_length: null,
      },

      formRules: {
        prefix: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur"
          }
        ],
        type: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "change"
          }
        ],
        int_length: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur"
          }
        ],
      },
    };
  },
  created() {
    this.orderNoRuleInitStore();
    if (this.$route.query.id) {
      this.getOrderNoRuleDetails();
    }
  },
  methods: {
    getOrderNoRuleDetails() {
      this.$api.getOrderNoRuleDetails(this.$route.query.id).then(res => {
        if (!res) return;
        this.form = res.data;
      });
    },

    // 初始化
    orderNoRuleInitStore() {
      this.$api.orderNoRuleInitStore().then(res => {
        if (!res) return;
        this.typeList = res.data.type_list;
      });
    },

    handlerCancel() { // 取消
      this.$router.push({name: 'ruleConfiguration'});
    },

    onSubmit() { // 提交
      this.$refs.form.validate(vaild => {
        if (!vaild) return;
        if (this.$route.query.id) {
          this.$api.putOrderNoRule(this.form)
            .then(res => {
              if (!res) return;
              this.$router.replace({
                name: "ruleConfiguration"
              });
            });
          return;
        } else {
          this.$api.addOrderNoRule(this.form).then(res => {
            if (!res) return;
            this.$router.replace({
              name: "ruleConfiguration"
            });
          });
        }
      });
    }
  }
};
</script>

<style lang="less" scope>
.add-rule-configuration {
  background-color: #fff;
  padding: 47px 20px 47px;
  .form_main {
    .diy-rules {
      font-size: 18px;
    }
    .el-form-item__content {
        max-width: 328px;
    }
    .footer-button {
        text-align: right;
    }
  }
}
</style>
