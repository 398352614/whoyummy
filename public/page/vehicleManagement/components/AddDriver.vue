<template>
  <!-- 添加司机 -->
  <div class="content_box">
    <el-form :model="form" :rules="formRules" ref="form" label-width="160px">
      <el-card
        class="box-card"
        :body-style="{ padding: '0px' }"
        shadow="nerver"
      >
        <!-- 基本信息 -->
        <div slot="header" class="clearfix">
          <span>{{ $t("GeneralInfo") }}</span>
        </div>
        <div class="base_box">
          <div class="base_left">
            <el-form-item :label="$t('Name')" prop="fullname">
              <el-input size="small" v-model="form.fullname"></el-input>
            </el-form-item>
            <el-form-item :label="$t('Email')" prop="email">
              <el-input
                size="small"
                v-model="form.email"
                auto-complete="off"
              ></el-input>
            </el-form-item>
            <el-form-item
              :label="$t('PassWord')"
              v-if="!this.isEdit"
              prop="password"
            >
              <el-input
                size="small"
                v-model="form.password"
                type="text"
                auto-complete="off"
              ></el-input>
            </el-form-item>
            <!-- 所属网点 -->
            <el-form-item :label="$t('AffiliatedBranches')" prop="warehouse_id">
              <!-- 树形结构 -->
              <el-cascader
                size="small"
                :options="warehouseList"
                :props="optionProps"
                :clearable="true"
                v-model="form.warehouse_id"
                style="width: 100%"
              >
              </el-cascader>
              <!-- <el-select
                size="small"
                v-model="form.warehouse_id"
                :clearable="true"
                style="width: 100%"
              >
                <el-option
                  v-for="item in warehouseList"
                  :key="item.id"
                  :label="item.name"
                  :value="item.id"
                >
                </el-option>
              </el-select> -->
            </el-form-item>
            <!-- <el-form-item
              :label="$t('ConfirmPassword')"
              v-if="!this.isEdit"
              prop="confirm_password"
            >
              <el-input
                size="small"
                v-model="form.confirm_password"
                type="password"
                show-password
                auto-complete="off"
              ></el-input>
            </el-form-item> -->
            <el-form-item :label="$t('DriverType')" prop="type">
              <el-select
                size="small"
                :placeholder="$t('PleaseSelectTheDriverType')"
                v-model="form.type"
                style="width: 100%"
                :clearable="true"
              >
                <el-option
                  v-for="(item, index) in driverTypeList"
                  :key="index"
                  :label="item.name"
                  :value="item.id"
                >
                </el-option>
              </el-select>
            </el-form-item>
            <el-form-item :label="this.$t('Address')">
              <el-input v-model="form.address" type="textarea"></el-input>
            </el-form-item>
          </div>
          <div class="base_center">
            <el-form-item :label="$t('Phone')" prop="phone">
              <el-input size="small" v-model="form.phone"></el-input>
            </el-form-item>
            <el-form-item :label="$t('BTWNO')">
              <el-input size="small" v-model="form.duty_paragraph"></el-input>
            </el-form-item>
          </div>
          <div class="base_right">
            <el-upload
              :headers="Authorization"
              :before-upload="beforeAvatarUpload"
              :on-success="handleAvatarSuccess"
              :action="BaseApiImg"
              :on-remove="handlerClearDeiver"
              :show-file-list="false"
              :limit="1"
              name="image"
              :data="{
                dir: 'driver',
              }"
              class="avatar-uploader"
            >
              <img v-if="form.avatar" :src="form.avatar" class="avatar" />
              <i v-else class="el-icon-plus avatar-uploader-icon"></i>
            </el-upload>
            <div class="driver_avatar">
              {{ $t("Avatar") }}
            </div>
          </div>
        </div>
      </el-card>

      <div class="center_box">
        <el-card
          class="box-card"
          :body-style="{ padding: '0px' }"
          shadow="nerver"
        >
          <div slot="header" class="clearfix">
            <span>{{ $t("Drivinglicense") }}</span>
          </div>
          <div class="base_box">
            <div>
              <el-form-item :label="$t('Drivinglicenseno')">
                <el-input
                  class="fixed-input-box"
                  size="small"
                  v-model="form.lisence_number"
                ></el-input>
              </el-form-item>
              <el-form-item :label="$t('Expirydate')">
                <el-date-picker
                  v-model="form.lisence_valid_date"
                  type="date"
                  size="small"
                  value-format="yyyy-MM-dd"
                >
                </el-date-picker>
              </el-form-item>
              <el-form-item :label="$t('Drivinglicensetype')">
                <el-radio-group
                  class="lisence_type"
                  v-model="form.lisence_type"
                >
                  <el-radio
                    class="lisence_type_item"
                    v-for="item in lisence_type"
                    :key="item.label"
                    :label="item.label"
                  >
                    {{ item.label }}
                  </el-radio>
                </el-radio-group>
              </el-form-item>
            </div>
          </div>
        </el-card>
        <el-card
          class="box-card"
          :body-style="{ padding: '0px' }"
          shadow="nerver"
        >
          <div slot="header" class="clearfix">
            <span>{{ $t("Bankinfo") }}</span>
          </div>
          <div class="base_box" label-width="160px">
            <div>
              <el-form-item :label="$t('BankName')">
                <el-input size="small" v-model="form.bank_name"></el-input>
              </el-form-item>
              <el-form-item label="IBAN">
                <el-input size="small" v-model="form.iban"></el-input>
              </el-form-item>
              <el-form-item label="BIC">
                <el-input size="small" v-model="form.bic"></el-input>
              </el-form-item>
            </div>
          </div>
        </el-card>
      </div>

      <div class="footer_box">
        <el-card
          class="box-card"
          :body-style="{ padding: '0px' }"
          shadow="nerver"
        >
          <div slot="header" class="clearfix">
            <span>{{ $t("Relateddoc") }}</span>
          </div>
          <div class="footer_inner">
            <div class="footer_left">
              <div class="footer_text">{{ $t("Drivinglicense") }}</div>
              <el-form-item ref="lisence_material">
                <el-upload
                  :headers="Authorization"
                  :before-upload="beforeAvatarUpload"
                  :on-success="handleDriverSuccess"
                  :action="BaseApiFile"
                  :on-remove="handlerClearLisence"
                  :file-list="driverFile"
                  :limit="1"
                  name="file"
                  :data="{
                    dir: 'driver',
                  }"
                >
                  <el-button slot="trigger" size="small" type="primary">{{
                    $t("Select")
                  }}</el-button>
                  <div slot="tip" class="el-upload__tip">
                    {{ $t("UploadTips1") }}
                  </div>
                </el-upload>
              </el-form-item>
            </div>
            <div class="footer_right">
              <div class="footer_text">{{ $t("GovermentLetters") }}</div>
              <el-form-item ref="government_material">
                <el-upload
                  :headers="Authorization"
                  :before-upload="beforeAvatarUpload"
                  :on-success="handleGovSuccess"
                  :action="BaseApiFile"
                  :on-remove="handlerClearGov"
                  :file-list="govFile"
                  :limit="1"
                  name="file"
                  clearFiles
                  :data="{
                    dir: 'driver',
                  }"
                >
                  <el-button slot="trigger" size="small" type="primary">{{
                    $t("Select")
                  }}</el-button>
                  <div slot="tip" class="el-upload__tip">
                    {{ $t("UploadTips2") }}
                  </div>
                </el-upload>
              </el-form-item>
            </div>
          </div>
        </el-card>
      </div>
      <div class="submit_button_box">
        <el-row>
          <el-col :span="2" :offset="22">
            <el-button
              type="primary"
              @click="onSubmit"
              :loading="this.$store.state.config.button_loading"
              class="submit"
              >{{ $t("Submit") }}</el-button
            >
          </el-col>
        </el-row>
      </div>
    </el-form>
  </div>
</template>

<script>
import baseApi from "@/api/axios/base_api";

export default {
  name: "AddDriver",
  data() {
    return {
      // 树形结构字段转换
      optionProps: {
        checkStrictly: true,
        value: "id",
        label: "name",
        children: "children",
      },
      driverFile: [],
      govFile: [],
      warehouseList: [], //所属网点列表
      imageUrl: "",
      form: {
        address: "",
        fullname: "", // 姓名
        email: "",
        password: "12345678",
        confirm_password: "",
        type: "", //司机类型
        phone: "",
        duty_paragraph: "", // 税号
        gender: "",
        post_code: "",
        door_no: "",
        street: "",
        city: "",
        warehouse_id: "", //所属网点
        avatar: "",

        lisence_number: "", // 驾照编号
        lisence_valid_date: "", // 有效编码
        lisence_type: "", // 驾照类型
        lisence_material: "", // 驾照材料
        lisence_material_name: "", // 材料名

        bank_name: "", // 银行名称
        iban: "",
        bic: "",
        government_material: "", // 政府信件
        government_material_name: "", // 材料名
      },
      //司机类型
      driverTypeList: [],
      // 驾照类型
      lisence_type: [
        {
          label: "A1",
        },
        {
          label: "A2",
        },
        {
          label: "A",
        },
        {
          label: "B1",
        },
        {
          label: "B",
        },
        {
          label: "AM",
        },
        {
          label: "C1",
        },
        {
          label: "C",
        },
        {
          label: "D1",
        },
        {
          label: "D",
        },
        {
          label: "BE",
        },
        {
          label: "C1E",
        },
        {
          label: "CE",
        },
        {
          label: "D1E",
        },
        {
          label: "DE",
        },
        {
          label: "T",
        },
      ],
      avatarURL: "",
      isEdit: false,
      avatar_cache: "", // 缓存
    };
  },
  created() {
    this.getDictionary();
    // this.getWarehouse(); //所属网点
    this.getNetworkTree(); //所属网点

    if (this.$route.query.email && this.$route.query.phone) {
      this.isEdit = true;
      this.getDriverDetail({
        email: this.$route.query.email,
        phone: this.$route.query.phone,
      });
    } // 编辑
  },
  computed: {
    Authorization() {
      return { Authorization: this.$store.state.token.token };
    },
    BaseApiImg() {
      return `${baseApi}admin/upload/image`;
    },
    BaseApiFile() {
      return `${baseApi}admin/upload/file`;
    },
    // 表单
    formRules() {
      return {
        fullname: [
          // 姓名
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        email: [
          //邮箱
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],

        password: [
          //密码
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        phone: [
          //电话
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        post_code: [
          //邮编
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        // 所属网点
        warehouse_id: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        // 司机类型
        type: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
      };
    },
    gender() {
      return [
        {
          label: this.$t("Male"),
        },
        {
          label: this.$t("Female"),
        },
      ];
    },
  },
  methods: {
    // 网点树形结构
    getNetworkTree() {
      this.$api.getNetworkTree().then((res) => {
        this.warehouseList = res.data;
      });
    },
    // getWarehouse() {
    //   // 所属网点列表
    //   this.$api.getWarehouse().then((res) => {
    //     this.warehouseList = res.data.data;
    //   });
    // },
    getDictionary() {
      this.$api.getDictionary().then((res) => {
        this.driverTypeList = res.data.driverTypeList;
      });
    },
    handlerClearDeiver() {
      // eslint-disable-next-line
      this.form.avatar = "";
    },
    handlerClearLisence() {
      // eslint-disable-next-line
      this.form.lisence_material = "";
      this.form.lisence_material_name = "";
    },
    handlerClearGov() {
      // eslint-disable-next-line
      this.form.government_material = "";
      this.form.government_material_name = "";
    },
    getDriverDetail(params) {
      this.$api.getDriverDetail(params).then((res) => {
        if (!res) return false;
        const data = res.data.data[0];
        const formKeys = Object.keys(this.form);
        // eslint-disable-next-line guard-for-in
        for (const i in data) {
          if (formKeys.includes(i)) {
            this.form[i] = data[i]; // i 不能是对象
          }
        }
        if (data.lisence_material) {
          this.driverFile.push({
            name: data.lisence_material_name,
            url: data.lisence_material,
          });
        }
        if (data.government_material) {
          this.govFile.push({
            name: data.government_material_name,
            url: data.government_material,
          });
        }
      });
    },
    handleAvatarSuccess(response) {
      if (response.code === 200) {
        this.form.avatar = response.data.path;
      }
    },
    handleGovSuccess(response) {
      if (response.code === 200) {
        this.$refs.government_material.clearValidate();
        this.form.government_material = response.data.path;
        this.form.government_material_name = response.data.name;
      }
    },
    handleDriverSuccess(response) {
      if (response.code === 200) {
        this.$refs.lisence_material.clearValidate();
        this.form.lisence_material = response.data.path;
        this.form.lisence_material_name = response.data.name;
      }
    },
    beforeAvatarUpload(file) {
      console.log("beforeAvatarUpload", file);
    },
    onSubmit() {
      this.$refs.form.validate((valid) => {
        if (valid) {
          let arr = this.form.warehouse_id;
          let end = arr[arr.length - 1]; //网点选中项
          if (this.isEdit) {
            this.$api
              .editDrivers(this.$route.query.id, {
                ...this.form,
                confirm_password: this.form.password,
                warehouse_id: end,
              })
              .then((res) => {
                if (!res) return;
                this.$router.push({
                  name: "driverManagement",
                  params: { driverManagement: 1 },
                });
              });
            return;
          }
          this.$api
            .addDrivers({
              ...this.form,
              confirm_password: this.form.password,
              warehouse_id: end,
            })
            .then((res) => {
              if (!res) return;
              this.$router.push({
                name: "driverManagement",
                params: { driverManagement: 1 },
              });
            });
        }
      });
    },
  },
};
</script>

<style lang="less" scoped>
.content_box {
  background-color: #fff;
  .fixed-input-box {
    max-width: 220px;
  }
  .base_box {
    display: flex;
    padding: 20px;
    .base_left {
      flex: 1;
    }
    .base_center {
      flex: 1;
    }
    .base_right {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      .driver_avatar {
        margin-top: 10px;
      }
    }
  }
  .center_box {
    margin-top: 2px;
    display: flex;
    justify-content: space-between;
    .box-card {
      flex: 1;
    }
  }
  .footer_box {
    margin-top: 2px;
    .footer_inner {
      display: flex;
      .footer_left {
        padding: 20px;
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
      }
      .footer_right {
        padding: 20px;
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
      }
      .footer_text {
        position: relative;
        top: -10px;
        right:10px;
        white-space: nowrap;
      }
    }
  }
  .submit_button_box {
    padding: 20px 0;
    text-align: center;
  }
  .lisence_type {
    width: 400px;
    display: flex;
    flex-flow: row wrap;
    .lisence_type_item {
      width: 80px;
      margin: 4px 0;
    }
  }
}
</style>
<style lang="less" scoped>
.avatar {
  width: 178px;
  height: 178px;
  display: block;
}
.avatar-uploader .el-upload {
  border: 1px dashed #d9d9d9;
  border-radius: 6px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}
.avatar-uploader .el-upload:hover {
  border-color: #409eff;
}
.avatar-uploader {
  .avatar-uploader-icon {
    font-size: 28px;
    color: #8c939d;
    width: 178px;
    height: 178px;
    line-height: 178px !important;
    text-align: center;
  }
}
</style>
