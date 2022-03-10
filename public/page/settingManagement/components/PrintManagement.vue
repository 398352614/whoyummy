<template>
  <!-- 面单设置 -->
  <div class="print-management">
    <!-- <div class="form_main">
      <div v-for="item in typeList" class="show-template" :key="item.id">
        <h2>{{ item.name }}</h2>
        <img :src="item.url" />
        <el-button @click="openTheDrawer" size="small" type="info"
          >{{$t("configuration")}}</el-button
        >
        <el-button
          v-if="item.is_default === 2"
          @click="updateToDefault(item.id)"
          size="small"
          type="primary"
          :loading="$store.state.config.button_loading"
          >{{$t("setDefault")}}</el-button
        >
      </div>
    </div> -->

    <div class="template-list">
      <div class="standard">
        <h2 class="standard-title">{{ $t("standardTemplate") }}</h2>
        <div class="standard-box">
          <div class="header">
            <div class="left-logo">
              <img v-if="normalTemplate.logo" :src="normalTemplate.logo" />
              <img v-else src="@/assets/img/trucks.png" />
              <span>LOGO</span>
            </div>
            <div class="right-barcode">
              <img src="@/assets/img/template-code.png" />
            </div>
          </div>
          <div class="sender">
            <div class="left">
              {{ normalTemplate.sender }}
            </div>
            <div class="right">
              <p>{{ $t("name") }}</p>
              <p>
                <span>{{ $t("Street") }}</span
                ><span>{{ $t("DoorNo") }}</span>
              </p>
              <p>
                <span>{{ $t("ZipCode") }}</span
                ><span>{{ $t("City") }}</span
                ><span>{{ $t("Country") }}</span>
              </p>
            </div>
          </div>
          <div class="sender">
            <div class="left">
              <div>{{ normalTemplate.receiver }}</div>
              <span class="postcode">415000</span>
            </div>
            <div class="right">
              <p>{{ $t("name") }}</p>
              <p>
                <span>{{ $t("Street") }}</span
                ><span>{{ $t("DoorNo") }}</span>
              </p>
              <p>
                <span>{{ $t("ZipCode") }}</span
                ><span>{{ $t("City") }}</span
                ><span>{{ $t("Country") }}</span>
              </p>
            </div>
          </div>
          <div style="padding: 0 10px">
            <h2 style="margin: 0 0 30px 0">0000</h2>
          </div>
          <div class="sender" style="min-height: 80px">
            <div class="left">
              <h2>{{ normalTemplate.destination }}</h2>
            </div>
            <div class="right">
              <p>
                <span>{{ normalTemplate.destination_mode_name }}</span>
              </p>
            </div>
          </div>
          <div class="barcode">
            <img src="@/assets/img/template-code.png" />
          </div>
        </div>
        <el-button
          @click="openTheDrawer(normalTemplate.id)"
          size="small"
          type="info"
          >{{ $t("configuration") }}</el-button
        >
        <el-button
          v-if="normalTemplate.is_default === 2"
          @click="updateToDefault(normalTemplate.id)"
          size="small"
          type="primary"
          :loading="$store.state.config.button_loading"
          >{{ $t("setDefault") }}</el-button
        >
      </div>

      <div class="common">
        <h2 class="common-title">{{ $t("universalTemplate") }}</h2>
        <div class="common-box">
          <div class="header">
            <div class="left-logo">
              <img v-if="commonTemplate.logo" :src="commonTemplate.logo" />
              <img v-else src="@/assets/img/trucks.png" />
              <span>LOGO</span>
            </div>
            <div class="right-barcode">
              <img src="@/assets/img/template-code.png" />
            </div>
          </div>
          <div class="sender">
            <div class="left">
              {{ commonTemplate.sender }}
            </div>
            <div class="right">
              <p>{{ $t("name") }}</p>
              <p>{{ $t("Phone") }}</p>
              <p>
                <span>{{ $t("district") }}</span
                ><span>{{ $t("Street") }}</span
                ><span>{{ $t("DetailedAddress") }}</span>
              </p>
            </div>
          </div>
          <div class="sender">
            <div class="left">
              <div>{{ commonTemplate.receiver }}</div>
            </div>
            <div class="right">
              <p>{{ $t("name") }}</p>
              <p>{{ $t("Phone") }}</p>
              <p>
                <span>{{ $t("district") }}</span
                ><span>{{ $t("Street") }}</span
                ><span>{{ $t("DetailedAddress") }}</span>
              </p>
            </div>
          </div>
          <div class="destination">
            <div class="left">
              <h2 style="margin-right: 10px">
                {{ commonTemplate.destination }}
              </h2>
              <h2>{{ commonTemplate.destination_mode_name }}</h2>
            </div>
            <div class="right">
              <p>{{ commonTemplate.replace_amount }}：0.00</p>
              <p>{{ commonTemplate.settlement_amount }}：0.00</p>
            </div>
          </div>
          <div class="item-info">
            <p style="margin: 0">{{ commonTemplate.contents }}</p>
            <div class="info">
              <div>
                <div>{{ commonTemplate.package }}</div>
                <div>{{ commonTemplate.material }}</div>
              </div>
              <div>
                <div>{{ commonTemplate.count }}：3</div>
                <div>{{ commonTemplate.count }}：2</div>
              </div>
            </div>
          </div>
          <div class="barcode">
            <img src="@/assets/img/template-code.png" />
          </div>
        </div>
        <el-button
          @click="openTheDrawer(commonTemplate.id)"
          size="small"
          type="info"
          >{{ $t("configuration") }}</el-button
        >
        <el-button
          v-if="commonTemplate.is_default === 2"
          @click="updateToDefault(commonTemplate.id)"
          size="small"
          type="primary"
          :loading="$store.state.config.button_loading"
          >{{ $t("setDefault") }}</el-button
        >
      </div>
    </div>

    <el-drawer
      :title="$t('SetTemplateContent')"
      :visible.sync="templateDrawer"
      custom-class="demo-drawer"
      ref="drawer"
    >
      <div v-loading="drawerLoading" class="demo-drawer__content">
        <el-form :model="drawerForm">
          <el-form-item label="Logo" ref="avatar">
            <el-upload
              class="avatar-uploader"
              :headers="Authorization"
              :on-error="handleAvatarError"
              :on-success="handleAvatarSuccess"
              :action="BaseApiFile"
              :on-remove="handlerClearAvatar"
              :show-file-list="false"
              name="image"
              :data="{
                dir: 'merchant',
              }"
            >
              <img
                v-if="drawerForm.logo"
                :src="drawerForm.logo"
                class="avatar"
              />
              <i v-else class="el-icon-plus avatar-uploader-icon"></i>
            </el-upload>
          </el-form-item>
          <el-form-item :label="this.$t('translation')" prop="sender">
            <el-input v-model="drawerForm.sender" autocomplete="off"></el-input>
          </el-form-item>
          <el-form-item
            :label="this.$t('recipientTranslation')"
            prop="receiver"
          >
            <el-input
              v-model="drawerForm.receiver"
              autocomplete="off"
            ></el-input>
          </el-form-item>
          <el-row>
            <el-col :span="11">
              <el-form-item :label="this.$t('destination')" prop="destination">
                <el-input
                  v-model="drawerForm.destination"
                  autocomplete="off"
                ></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="11" :offset="2">
              <el-form-item
                :label="this.$t('destinationValue')"
                prop="destination_mode"
              >
                <el-select
                  v-model="drawerForm.destination_mode"
                  :placeholder="this.$t('pleaseArea')"
                >
                  <el-option
                    :label="this.$t('district')"
                    :value="1"
                  ></el-option>
                  <el-option
                    :label="this.$t('cityProvince')"
                    :value="2"
                  ></el-option>
                  <el-option
                    :label="this.$t('districtCity')"
                    :value="3"
                  ></el-option>
                  <el-option :label="this.$t('ZipCode')" :value="4"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>

          <el-form-item :label="this.$t('carrier')" prop="carrier">
            <el-input
              v-model="drawerForm.carrier"
              autocomplete="off"
            ></el-input>
          </el-form-item>
          <el-form-item
            :label="this.$t('carrierTranslation')"
            prop="carrier_address"
          >
            <el-input
              v-model="drawerForm.carrier_address"
              type="textarea"
              autocomplete="off"
            ></el-input>
          </el-form-item>
          <template v-if="drawerForm.type === 2">
            <el-form-item :label="this.$t('Article')">
              <el-input
                v-model="drawerForm.contents"
                autocomplete="off"
              ></el-input>
            </el-form-item>
            <el-form-item :label="this.$t('parcelTranslation')">
              <el-input
                v-model="drawerForm.package"
                autocomplete="off"
              ></el-input>
            </el-form-item>
            <el-form-item :label="this.$t('materialTranslation')">
              <el-input
                v-model="drawerForm.material"
                autocomplete="off"
              ></el-input>
            </el-form-item>
            <el-form-item :label="this.$t('quantityTranslation')">
              <el-input
                v-model="drawerForm.count"
                autocomplete="off"
              ></el-input>
            </el-form-item>
            <el-form-item :label="this.$t('collection')">
              <el-input
                v-model="drawerForm.replace_amount"
                autocomplete="off"
              ></el-input>
            </el-form-item>
            <el-form-item :label="this.$t('freightTranslation')">
              <el-input
                v-model="drawerForm.settlement_amount"
                autocomplete="off"
              ></el-input>
            </el-form-item>
          </template>
          <el-form-item :label="this.$t('TopQRCode')">
            <el-select
              v-model="drawerForm.header_barcode_type"
              placeholder="请选择"
              style="width: 100%"
            >
              <el-option
                v-for="item in barcodeTypeList"
                :key="item.id"
                :label="item.name"
                :value="item.id"
              >
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item :label="this.$t('QRCodeAtBottom')">
            <el-select
              v-model="drawerForm.footer_barcode_type"
              placeholder="请选择"
              style="width: 100%"
            >
              <el-option
                v-for="item in barcodeTypeList"
                :key="item.id"
                :label="item.name"
                :value="item.id"
              >
              </el-option>
            </el-select>
          </el-form-item>
        </el-form>
        <div class="demo-drawer__footer">
          <el-button @click="cancelForm" style="margin-right: 20px">{{
            $t("Cancel")
          }}</el-button>
          <el-tooltip
            :content="$t('AdministratorActivate')"
            placement="top"
            :disabled="hasPerm('print-template.update') ? true : false"
          >
            <div>
              <el-button
                :disabled="!hasPerm('print-template.update')"
                type="primary"
                @click="updateTheConfig"
                :loading="drawerBtnLoading"
                >{{
                  drawerBtnLoading ? $t("submitting") + "..." : $t("Confirm")
                }}</el-button
              >
            </div>
          </el-tooltip>
        </div>
      </div>
    </el-drawer>
  </div>
</template>

<script>
import baseApi from "@/api/axios/base_api";

export default {
  name: "PrintManagement",
  data() {
    return {
      typeList: [],
      barcodeTypeList: [],
      form: {
        type: null,
      },
      drawerForm: {
        type: "",
        logo: "",
        sender: "",
        receiver: "",
        destination_mode: "",
        destination: "",
        carrier: "",
        carrier_address: "",
        contents: "",
        package: "",
        material: "",
        count: "",
        replace_amount: "",
        settlement_amount: "",
        header_barcode_type: 1,
        footer_barcode_type: 1,
      },
      pic_url: null,
      formRules: {
        type: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
      },
      templateDrawer: false,
      drawerBtnLoading: false,
      drawerLoading: false,
      fileList: [],
      normalTemplate: {},
      commonTemplate: {},
    };
  },
  created() {
    this.getPrintTemplat();
    this.getDictionary();
  },
  computed: {
    Authorization() {
      return { Authorization: this.$store.state.token.token };
    },
    BaseApiFile() {
      return `${baseApi}admin/upload/image/`;
    },
  },
  methods: {
    getDictionary() {
      this.$api.getDictionary().then((res) => {
        this.barcodeTypeList = res.data.barcodeTypeList;
      });
    },
    handlerClearAvatar() {
      this.fileList = [];
      this.drawerForm.logo = "";
    },
    handleAvatarError(err) {
      console.log(err, "err");
    },
    handleAvatarSuccess(response) {
      console.log(response, "response");
      if (response.code === 200) {
        this.drawerForm.logo = response.data.path;
        this.$refs.avatar.clearValidate();
      } else {
        this.$message({
          message: response.msg,
          type: "warning",
        });
      }
    },
    updateToDefault(id) {
      this.$api.updateDefaultPrintTemplate(id).then((res) => {
        if (res.code === 200) {
          this.$message.success("操作成功");
          this.getPrintTemplat();
        }
      });
    },
    updateTheConfig() {
      this.drawerBtnLoading = true;
      this.$api
        .updatePrintTemplatDetail(this.drawerForm.id, this.drawerForm)
        .then((res) => {
          if (res.code === 200) {
            this.getPrintTemplat();
            this.$message.success("操作成功");
            this.$refs.drawer.closeDrawer();
          }
        })
        .finally(() => {
          this.drawerBtnLoading = false;
        });
    },
    openTheDrawer(id) {
      this.templateDrawer = true;
      this.drawerLoading = true;
      this.$api
        .getPrintTemplatDetail(id)
        .then((res) => {
          if (res.code === 200) {
            this.drawerForm = res.data;
          }
        })
        .finally(() => {
          this.drawerLoading = false;
        });
    },
    cancelForm() {
      this.templateDrawer = false;
      this.drawerBtnLoading = false;
    },
    getPrintTemplat() {
      this.$api.getPrintTemplatInit().then((res) => {
        if (!res) return;
        this.typeList = res.data.data;
        if (this.typeList[0].type === 1) {
          this.normalTemplate = this.typeList[0];
          this.commonTemplate = this.typeList[1];
        } else {
          this.commonTemplate = this.typeList[0];
          this.normalTemplate = this.typeList[1];
        }
      });
    },
  },
};
</script>

<style lang="less" scope>
.print-management {
  background-color: #fff;
  padding: 47px 20px 47px;
  .template-list {
    display: flex;
  }
  .standard {
    min-width: 340px;
    border: 1px solid gray;
    padding: 10px;
    margin-right: 40px;
    .standard-title {
      margin: 10px auto;
      text-align: center;
    }
    .standard-box {
      border: 1px solid gray;
      margin-bottom: 10px;
      .header {
        padding: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        .left-logo {
          display: flex;
          align-items: center;
          img {
            width: 80px;
            height: 65px;
            margin-right: 5px;
          }
        }
        .right-barcode {
          img {
            width: 150px;
          }
          text-align: center;
          h4 {
            margin: 0;
          }
        }
      }
      .sender {
        padding: 0 10px;
        display: flex;
        align-items: center;
        border-top: 1px solid gray;
        .postcode {
          display: inline-block;
          border: 1px solid gray;
          padding: 3px;
          margin-top: 5px;
        }
        .right {
          span {
            margin-right: 5px;
          }
        }
        .left {
          min-width: 120px;
        }
      }
      .barcode {
        border-top: 1px solid gray;
        padding: 10px;
        text-align: center;
        img {
          width: 80%;
          height: 60px;
        }
        p {
          margin: 0;
        }
      }
    }
  }
  .common {
    min-width: 340px;
    border: 1px solid gray;
    padding: 10px;
    .common-title {
      margin: 10px auto;
      text-align: center;
    }
    .common-box {
      border: 1px solid gray;
      margin-bottom: 10px;
      .header {
        padding: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        .left-logo {
          display: flex;
          align-items: center;
          img {
            width: 80px;
            height: 65px;
            margin-right: 5px;
          }
        }
        .right-barcode {
          img {
            width: 150px;
          }
          text-align: center;
          h4 {
            margin: 0;
          }
        }
      }
      .sender {
        padding: 0 10px;
        display: flex;
        align-items: center;
        border-top: 1px solid gray;
        .postcode {
          display: inline-block;
          border: 1px solid gray;
          padding: 3px;
          margin-top: 5px;
        }
        .right {
          span {
            margin-right: 5px;
          }
        }
        .left {
          min-width: 120px;
        }
      }
      .destination {
        padding: 0 10px;
        display: flex;
        align-items: center;
        border-top: 1px solid gray;
        .left {
          display: flex;
          min-width: 200px;
          border-right: 1px solid gray;
          h2 {
            margin: 12px 0;
          }
        }
        .right {
          padding-left: 10px;
          p {
            margin: 0;
          }
        }
      }
      .item-info {
        padding: 10px;
        border-top: 1px solid gray;
        .info {
          margin-left: 60px;
          display: grid;
          grid-template-columns: 1fr 1fr;
        }
      }
      .barcode {
        border-top: 1px solid gray;
        padding: 10px;
        text-align: center;
        img {
          width: 80%;
          height: 60px;
        }
        p {
          margin: 0;
        }
      }
    }
  }
  .form_main {
    display: flex;
    .diy-rules {
      font-size: 18px;
    }
    .el-form-item__content {
      max-width: 328px;
    }
    .footer-button {
      text-align: right;
    }
    .show-template {
      width: 328px;
      margin: 0 20px;
      h2 {
        font-weight: 400;
        color: #465468;
      }
      img {
        width: 100%;
        margin-bottom: 20px;
      }
    }
  }
  .demo-drawer {
    .el-drawer__body {
      overflow: auto;
    }

    .demo-drawer__content {
      padding: 0 20px 20px 20px;
    }
  }
  .demo-drawer__footer {
    display: flex;
  }
}
</style>
