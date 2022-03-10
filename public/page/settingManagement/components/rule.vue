<template>
  <!-- 单号规则设置 -->
  <div class="rule-configuration">
    <div class="rule-bg">
      <div class="side-bar">
        <ul>
          <li class="li-title" style="cursor: default">
            {{ $t("danHaoType") }}
          </li>
          <li
            v-for="(item, index) in ruleList"
            :key="index"
            :class="{ 'li-light': high_light == item.id }"
            @click="getRule(item)"
          >
            <span>{{ item.type_name }}</span>
            <span class="operation">
              <el-button type="text"
                ><i class="el-icon-edit" style="color: black"></i
              ></el-button>
            </span>
          </li>
        </ul>
      </div>
      <div class="content-bar">
        <div v-if="show">
          <div class="content-title">{{ $t("customized") }}</div>
          <hr />
          <div class="form_main">
            <el-form
              :rules="formRules"
              ref="form"
              :model="form"
              label-width="200px"
            >
              <el-form-item :label="$t('startCharacter')" prop="prefix">
                <el-input v-model="form.prefix" style="width: 500px"></el-input>
              </el-form-item>
              <el-form-item :label="$t('alphabeticDigit')">
                <el-input
                  v-model="form.string_length"
                  style="width: 500px"
                ></el-input>
              </el-form-item>
              <el-form-item :label="$t('numberDigit')" prop="int_length">
                <el-input
                  v-model="form.int_length"
                  style="width: 500px"
                ></el-input>
              </el-form-item>
              <el-form-item>
                <!-- 保存 -->
                <el-tooltip
                  :content="$t('AdministratorActivate')"
                  placement="top"
                  :disabled="hasPerm('order-no-rule.update') ? true : false"
                  style="width: 70px"
                >
                  <div class="footer-button">
                    <el-button
                      :disabled="!hasPerm('order-no-rule.update')"
                      type="primary"
                      @click="onSubmit"
                      :loading="this.$store.state.config.button_loading"
                      :class="[
                        hasPerm('order-no-rule.update')
                          ? 'submit'
                          : 'submit-disabled',
                      ]"
                      >{{ $t("Save") }}</el-button
                    >
                  </div>
                </el-tooltip>
              </el-form-item>
            </el-form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import loading from "@/mixin/global_loading";
export default {
  mixins: [loading],
  data() {
    return {
      show: false, //右边内容展示
      ruleList: [], //单号类型
      //   表单
      form: {
        type: null,
        prefix: null,
        string_length: null,
        int_length: null,
      },
      //   表单验证
      formRules: {
        prefix: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        type: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "change",
          },
        ],
        int_length: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
      },
      high_light: null,
    };
  },
  created() {
    this.getOrderNoRuleList();
  },
  computed: {
    reset() {
      return this.$store.state.lang;
    },
  },
  methods: {
    // 单号规则
    getOrderNoRuleList() {
      this.$api.getOrderNoRuleList().then((res) => {
        this.ruleList = res.data.data;
      });
    },
    getRule(item) {
      const id = item.id;
      this.high_light = id;
      this.form = item;
      this.show = true; ///右边内容展示
    },
    // 提交
    onSubmit() {
      this.$refs.form.validate((vaild) => {
        if (!vaild) return;
        this.$api
          .putOrderNoRule({ ...this.form, id: this.high_light })
          .then((res) => {});
      });
    },
  },
};
</script>
<style lang="less" scoped="scoped">
.rule-configuration {
  height: 100%;
  .rule-bg {
    overflow: hidden;
    display: flex;
  }
  .side-bar {
    width: 350px;
    margin-right: 20px;
    padding-bottom: 100px;
    float: left;
    border-radius: 3px;
    background: white;
    border: 1px rgb(234, 234, 234) solid;
    position: relative;
    overflow: hidden;
    ul {
      list-style: none;
      position: relative;
      padding: 0;
      li {
        line-height: 40px;
        padding-left: 10px;
        border-bottom: 1px rgb(234, 234, 234) solid;
        display: flex;
        align-items: center;
        // flex-direction: column;
        justify-content: space-between;
        cursor: pointer;
        .operation {
          margin-right: 20px;
        }
      }
      .li-title {
        margin-top: -10px;
        height: 55px;
        color: #28d094;
      }
      .li-light {
        background: #28d094;
        color: #ffffff;
      }
      .li-disable {
        background: #f6f6f6;
        cursor: default;
      }
    }
  }
  .content-bar {
    width: 70%;
    // height: 100%;
    float: left;
    padding: 0 10px 10px;
    border-radius: 3px;
    border: 1px rgb(234, 234, 234) solid;
    background: white;
    box-sizing: border-box;
    .content-title {
      margin-top: 20px;
      color: #28d094;
    }
  }
}
</style>
