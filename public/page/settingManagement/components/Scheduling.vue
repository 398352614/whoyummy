<template>
  <!-- 调度规则 -->
  <div class="scheduling-config">
    <el-form
      :model="form"
      :rules="formRules"
      ref="form"
      label-position="right"
      label-width="100px"
    >
      <div class="scheduling-title">{{ $t("RouteAssignRules") }}</div>
      <div class="show-template-box">
        <!-- 模板一 -->
        <div
          :class="{
            'address-template': true,
            'address-template-check': form.line_rule === 1,
          }"
        >
          <p>{{ $t("ZipCode") }}</p>
          <el-row :gutter="20" class="template">
            <el-col :span="7" class="template-title">{{
              $t("Zipcoderange")
            }}</el-col>
            <el-col :span="6" class="template-box"
              >{{ $t("pleaseEnter") }}
            </el-col>
            <el-col :span="6" :offset="1" class="template-line"></el-col>
            <el-col :span="6" :offset="1" class="template-box"
              >{{ $t("pleaseEnter") }}
            </el-col>
          </el-row>
          <el-row :gutter="20" class="template">
            <el-col :span="7" class="template-title">{{
              $t("Zipcoderange")
            }}</el-col>
            <el-col :span="6" class="template-box"
              >{{ $t("pleaseEnter") }}
            </el-col>
            <el-col :span="6" :offset="1" class="template-line"></el-col>
            <el-col :span="6" :offset="1" class="template-box"
              >{{ $t("pleaseEnter") }}
            </el-col>
          </el-row>
          <!-- </div> -->
          <div class="address-template-radio">
            <el-radio v-model="form.line_rule" :label="1">{{
              $t("SelectTemplate")
            }}</el-radio>
          </div>
        </div>

        <!-- 模板二 -->
        <!-- <div
          :class="{
            'address-template': true,
            'address-template-check': form.line_rule === 2,
          }"
        >
          <div class="template-area">
            <div class="area-title">{{ $t("Area") }}</div>
            <img
              src="../../../assets/img/configuration-img/atlas.png"
              alt=""
              class="template-img"
            />
          </div>

          <div class="address-template-radio">
            <el-radio v-model="form.line_rule" :label="2">{{
              $t("SelectTemplate")
            }}</el-radio>
          </div>
        </div> -->
      </div>

      <!-- <el-form-item :label="$t('线路分配显示')" prop="show_type">
        <el-radio-group v-model="form.show_type">
          <el-radio :label="1">{{ $t("显示全部") }}</el-radio>
          <el-radio :label="2">{{ $t("显示当前所选的方式") }}</el-radio>
        </el-radio-group>
      </el-form-item> -->
      <div class="scheduling-title">{{ $t("schedulingRule") }}</div>
      <el-form-item :label="$t('')" prop="scheduling_rule">
        <el-radio-group v-model="form.scheduling_rule">
          <el-row>
            <el-col class="show-type">
              <el-radio :label="1"
                >{{ $t("automaticScheduling") }}
                <el-tooltip
                  effect="dark"
                  :content="$t('automaticSchedulingState')"
                  placement="right"
                  class="dark"
                >
                  <span class="el-icon-warning icon-question"></span>
                </el-tooltip>
              </el-radio>
            </el-col>
            <el-col class="show-type">
              <el-radio :label="2"
                >{{ $t("manualScheduling") }}
                <el-tooltip
                  effect="dark"
                  :content="$t('manualSchedulingState')"
                  placement="right"
                  class="dark"
                >
                  <span class="el-icon-warning icon-question"></span>
                </el-tooltip>
              </el-radio>
            </el-col>
          </el-row>
        </el-radio-group>
      </el-form-item>
      <div class="scheduling-title">
        {{ $t("SiteScheduledTimeConfiguration") }}
      </div>
      <el-form-item :label="$t('')" prop="stop_time">
        <div class="show-type">
          {{ $t("PreArrivalTimeExtension") }}
          <el-input
            v-model="form.stop_time"
            autocomplete="off"
            style="width: 70px"
            size="mini"
          ></el-input>
          {{ $t("minute") }}
          <el-tooltip
            effect="dark"
            :content="$t('TheStationIntelligentScheduling')"
            placement="right"
            class="dark"
          >
            <span class="el-icon-warning icon-question"></span>
          </el-tooltip>
        </div>
      </el-form-item>
    </el-form>
    <el-tooltip
      :content="$t('AdministratorActivate')"
      placement="top"
      :disabled="hasPerm('company-config.rule-update') ? true : false"
      style="width: 70px"
    >
      <div class="save save-form">
        <el-button
          type="primary"
          @click="putScheduling"
          :loading="this.$store.state.config.button_loading"
          :disabled="!hasPerm('company-config.rule-update')"
          :class="[
            hasPerm('company-config.rule-update')
              ? 'submit'
              : 'submit-disabled',
          ]"
          >{{ $t("Submit") }}</el-button
        >
      </div>
    </el-tooltip>
  </div>
</template>
<script>
export default {
  name: "Scheduling",
  data() {
    var valiNumberPass = (rule, value, callback) => {
      //正则判断大于零的整数
      let reg = /^[1-9]\d*$/;
      if (value === "") {
        callback(new Error(this.$t("Required")));
      } else if (!reg.test(value)) {
        callback(new Error(this.$t("IntegerGreaterThanZero")));
      } else {
        if (value <= 0) {
          callback(new Error(this.$t("IntegerGreaterThanZero")));
        } else {
          callback();
        }
      }
    };
    return {
      tab: "1",
      form: {
        // 调度规则
        line_rule: null,
        scheduling_rule: null,
        show_type: null,
        stop_time: null,
      },
      // 选择模板
      check_template: false,
      template: {
        name: null,
      },
      formRules: {
        line_rule: [
          {
            required: true,
            message: this.$t("PleaseSelectRouteAssignRules"),
            trigger: "blur",
          },
        ],
        // show_type: [
        //   { required: true, message: this.$t("Mandatory"), trigger: "blur" },
        // ],
        scheduling_rule: [
          {
            required: true,
            message: this.$t("PleaseSelectAWeightUnit"),
            trigger: "change",
          },
        ],
        stop_time: [
          { required: true, validator: valiNumberPass, trigger: "blur" },
        ],
      },
    };
  },
  created() {
    this.getScheduling();
  },
  methods: {
    // 拉取高级配置
    getScheduling() {
      this.$api.getScheduling().then((res) => {
        if (!res.data) {
          this.form.map = null;
          return;
        }
        this.form = res.data;
      });
    },
    // 修改
    putScheduling() {
      this.$refs.form.validate((valid) => {
        if (!valid) return;
        this.$api.putScheduling(this.form).then((res) => {
          if (!res) return;
          // 线路分配规则
          localStorage.setItem("line_rule", this.form.line_rule);
          // 线路分配显示
          localStorage.setItem("show_type", this.form.show_type);
          this.getScheduling();
        });
      });
    },
  },
};
</script>
<style lang="less" scoped>
.scheduling-config {
  padding: 20px 25px 10px;
  .show-type {
    color: #606266;
    // font-weight: 500;
    margin-bottom: 20px;
    .el-icon-warning {
      color: #efb336;
      margin-left: 10px;
    }
  }
  .save {
    .el-button {
      margin-left: 100px;
      border-radius: 0;
      padding-left: 44px;
      padding-right: 44px;
    }
  }
  .save-form {
    margin-left: 0px;
  }
  /deep/.el-form-item__error {
    margin-top: -25px;
    margin-left: 85px;
  }
}
</style>
<style lang="less">
.scheduling-config {
  .scheduling-title {
    font-size: 15px;
    font-weight: 600;
    color: #636d7c;
    margin: 20px 100px;
  }
  .show-template-box {
    display: flex;
    margin-bottom: 30px;
    .address-template {
      width: 500px;
      height: 250px;
      border: 1px solid #ebeef5;
      box-shadow: 0 0 3px 1px #f2f6fc;
      box-sizing: border-box;
      padding: 20px;
      margin-left: 100px;
      .template {
        margin-bottom: 20px;
        .template-title {
          height: 10%;
          line-height: 35px;
        }
        .template-box {
          width: 25%;
          height: 10%;
          border: 1px solid #e4e3e3;
          color: #e4e3e3;
          text-align: center;
          line-height: 35px;
          cursor: default;
        }
        .template-line {
          width: 30px;
          height: 2px;
          background-color: rgb(196, 195, 195);
          margin-top: 15px;
        }
      }
      .template-area {
        margin-top: 15px;
        .area-title {
          float: left;
          padding-right: 20px;
        }
        .template-img {
          float: left;
          width: 350px;
          height: 150px;
          margin-bottom: 20px;
        }
      }
    }
    .address-template-radio {
      clear: both;
      display: flex;
      justify-content: center;
      margin-top: 40px;
    }
    .address-template-check {
      border-color: #c4c4c4;
    }
  }
}
/* 全局修改 */
.el-select-dropdown {
  border-radius: 0;
}
</style>
