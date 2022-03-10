<template>
  <!-- 自定义界面 -->
  <div class="custom-interface">
    <el-form :model="companyInfo" label-position="right" label-width="90px">
      <div class="custom-title">
        <h2>{{ $t("customDomainAccess") }}</h2>
        <div>{{ $t("customDomain") }}</div>
      </div>
      <!--管理员 -->
      <el-form-item :label="$t('Administrator') + ':'" prop="admin_url">
        <el-input
          v-model="companyInfo.admin_url"
          :placeholder="$t('pleaseEnterDomainName')"
        ></el-input>
      </el-form-item>
      <!-- 商户端 -->
      <el-form-item :label="$t('merchantSide') + ':'" prop="merchant_url">
        <el-input
          v-model="companyInfo.merchant_url"
          :placeholder="$t('pleaseEnterDomainName')"
        ></el-input>
      </el-form-item>
      <!-- H5 -->
      <el-form-item :label="$t('H5') + ':'" prop="consumer_url">
        <el-input
          v-model="companyInfo.consumer_url"
          :placeholder="$t('pleaseEnterDomainName')"
        ></el-input>
      </el-form-item>
      <div class="custom-title">
        <h2>{{ $t("customInterface") }}</h2>
        <el-button
          size="mini"
          style="margin-left: 20px"
          @click="blockDescription"
          >{{ $t("blockDescription") }}</el-button
        >
      </div>
      <h4>
        {{ $t("AdministratorInterfaceConfiguration") }}
      </h4>
      <div class="custom-img">
        <!-- 登入界面左侧 -->
        <el-upload
          :headers="Authorization"
          :on-success="administratorSuccess1"
          :action="BaseApiImg"
          :on-remove="administratorClear1"
          :on-preview="handlePictureCardPreview1"
          list-type="picture-card"
          name="image"
          :data="{
            dir: 'driver',
          }"
          class="avatar-uploader"
          :class="{ disabled: uploadDisabled1 }"
        >
          <div>
            <img
              v-if="
                companyInfo.admin_login_background &&
                companyInfo.admin_login_background != ''
              "
              :src="companyInfo.admin_login_background"
              class="avatar"
            />
            <i v-else class="avatar-uploader-icon">
              <img
                src="../../../assets/img/configuration-img/picture.png"
                :alt="$t('uploadImage')"
              />
            </i>
          </div>
          <div slot="tip" class="el-upload-tip">
            <div>
              {{ $t("PictureLeftLoginInterface") }}
            </div>
            <div class="tip-title">{{ $t("recommendedSize") }}:960px*969px</div>
          </div>
        </el-upload>
        <el-dialog :visible.sync="dialogImage1">
          <img width="100%" :src="companyInfo.admin_login_background" alt="" />
        </el-dialog>
        <!-- 登录输入框上方的图片 -->
        <el-upload
          :headers="Authorization"
          :on-success="administratorSuccess2"
          :action="BaseApiImg"
          :on-remove="administratorClear2"
          :on-preview="handlePictureCardPreview2"
          list-type="picture-card"
          name="image"
          :data="{
            dir: 'driver',
          }"
          class="avatar-uploader"
          :class="{ disabled: uploadDisabled2 }"
        >
          <div>
            <img
              v-if="
                companyInfo.admin_login_title &&
                companyInfo.admin_login_title != ''
              "
              :src="companyInfo.admin_login_title"
              class="avatar"
            />
            <i v-else class="avatar-uploader-icon">
              <img
                src="../../../assets/img/configuration-img/picture.png"
                :alt="$t('uploadImage')"
              />
            </i>
          </div>
          <div slot="tip" class="el-upload-tip">
            <div>
              {{ $t("ThePictureAboveLoginInputBox") }}
            </div>
            <div class="tip-title">{{ $t("recommendedSize") }}:960px*969px</div>
          </div>
        </el-upload>
        <el-dialog :visible.sync="dialogImage2">
          <img width="100%" :src="companyInfo.admin_login_title" alt="" />
        </el-dialog>

        <!-- 系统左上角logo -->
        <el-upload
          :headers="Authorization"
          :on-success="administratorSuccess3"
          :action="BaseApiImg"
          :on-remove="administratorClear3"
          :on-preview="handlePictureCardPreview3"
          list-type="picture-card"
          name="image"
          :data="{
            dir: 'driver',
          }"
          class="avatar-uploader"
          :class="{ disabled: uploadDisabled3 }"
        >
          <img
            v-if="
              companyInfo.admin_main_logo && companyInfo.admin_main_logo != ''
            "
            :src="companyInfo.admin_main_logo"
            class="avatar"
          />
          <i v-else class="avatar-uploader-icon">
            <img
              src="../../../assets/img/configuration-img/picture.png"
              :alt="$t('uploadImage')"
            />
          </i>
          <div slot="tip" class="el-upload-tip">
            <div>
              {{ $t("UpperLeftCornerSystem") }}
            </div>
            <div class="tip-title">{{ $t("recommendedSize") }}:32px*32px</div>
          </div>
        </el-upload>
        <el-dialog :visible.sync="dialogImage3">
          <img width="100%" :src="companyInfo.admin_main_logo" alt="" />
        </el-dialog>
      </div>
      <!-- 商户端 -->
      <h4>
        {{ $t("MerchantInterfaceConfiguration") }}
      </h4>
      <div class="custom-img">
        <!-- 登入界面左侧 -->
        <el-upload
          :headers="Authorization"
          :on-success="merchantSuccess1"
          :action="BaseApiImg"
          :on-remove="merchantClear1"
          :on-preview="handlePictureCardPreview4"
          name="image"
          :data="{
            dir: 'driver',
          }"
          list-type="picture-card"
          class="avatar-uploader"
          :class="{ disabled: uploadDisabled4 }"
        >
          <div>
            <img
              v-if="companyInfo.merchant_login_background"
              :src="companyInfo.merchant_login_background"
              class="avatar"
            />
            <i v-else class="avatar-uploader-icon">
              <img
                src="../../../assets/img/configuration-img/picture.png"
                :alt="$t('uploadImage')"
              />
            </i>
          </div>
          <div slot="tip" class="el-upload-tip">
            <div>
              {{ $t("PictureLeftLoginInterface") }}
            </div>
            <div class="tip-title">{{ $t("recommendedSize") }}:960px*969px</div>
          </div>
        </el-upload>
        <el-dialog :visible.sync="dialogImage4">
          <img
            width="100%"
            :src="companyInfo.merchant_login_background"
            alt=""
          />
        </el-dialog>
        <!-- 登录输入框上方的图片 -->
        <el-upload
          :headers="Authorization"
          :action="BaseApiImg"
          :on-success="merchantSuccess2"
          :on-remove="merchantClear2"
          :on-preview="handlePictureCardPreview5"
          name="image"
          list-type="picture-card"
          class="avatar-uploader"
          :class="{ disabled: uploadDisabled5 }"
          :data="{
            dir: 'driver',
          }"
        >
          <img
            v-if="companyInfo.merchant_login_title"
            :src="companyInfo.merchant_login_title"
            class="avatar"
          />
          <i v-else class="avatar-uploader-icon">
            <img
              src="../../../assets/img/configuration-img/picture.png"
              :alt="$t('uploadImage')"
            />
          </i>
          <div slot="tip" class="el-upload-tip">
            <div>
              {{ $t("ThePictureAboveLoginInputBox") }}
            </div>
            <div class="tip-title">{{ $t("recommendedSize") }}:384px*67px</div>
          </div>
        </el-upload>
        <el-dialog :visible.sync="dialogImage5">
          <img width="100%" :src="companyInfo.admin_login_background" alt="" />
        </el-dialog>

        <!-- 系统左上角logo -->
        <el-upload
          class="avatar-uploader"
          :headers="Authorization"
          :action="BaseApiImg"
          :on-success="merchantSuccess3"
          :on-remove="merchantClear3"
          :on-preview="handlePictureCardPreview6"
          name="image"
          list-type="picture-card"
          :data="{
            dir: 'driver',
          }"
          :class="{ disabled: uploadDisabled6 }"
        >
          <img
            v-if="companyInfo.merchant_main_logo"
            :src="companyInfo.merchant_main_logo"
            class="avatar"
          />
          <i v-else class="avatar-uploader-icon">
            <img
              src="../../../assets/img/configuration-img/picture.png"
              :alt="$t('uploadImage')"
            />
          </i>
          <div slot="tip" class="el-upload-tip">
            <div>
              {{ $t("UpperLeftCornerSystem") }}
            </div>
            <div class="tip-title">{{ $t("recommendedSize") }}:32px*32px</div>
          </div>
        </el-upload>
        <el-dialog :visible.sync="dialogImage1">
          <img width="100%" :src="companyInfo.merchant_main_logo" alt="" />
        </el-dialog>
      </div>
      <!-- H5端 -->
      <h4>
        {{ $t("H5Configuration") }}
      </h4>
      <el-upload
        class="avatar-uploader"
        :headers="Authorization"
        :action="BaseApiImg"
        :on-success="h5Success"
        :on-remove="h5Clear"
        :on-preview="handlePictureCardPreview7"
        name="image"
        list-type="picture-card"
        :data="{
          dir: 'driver',
        }"
        :class="{ disabled: uploadDisabled7 }"
      >
        <img
          v-if="companyInfo.consumer_login_title"
          :src="companyInfo.consumer_login_title"
          class="avatar"
        />
        <i v-else class="avatar-uploader-icon">
          <img
            src="../../../assets/img/configuration-img/picture.png"
            :alt="$t('uploadImage')"
          />
        </i>
        <div slot="tip" class="el-upload-tip">
          <div>
            {{ $t("LoginInterfacePicture") }}
          </div>
          <div class="tip-title">{{ $t("recommendedSize") }}:32px*32px</div>
        </div>
      </el-upload>
      <el-dialog :visible.sync="dialogImage1">
        <img width="100%" :src="companyInfo.consumer_login_title" alt="" />
      </el-dialog>
    </el-form>
    <!-- 提交 -->
    <el-tooltip
      :content="$t('AdministratorActivate')"
      placement="top"
      :disabled="hasPerm('company-customize.index') ? true : false"
      style="width: 80px"
    >
      <div class="submit-btn">
        <el-button
          type="primary"
          @click="submit"
          :class="[
            hasPerm('company-customize.index') ? 'submit' : 'submit-disabled',
          ]"
          :disabled="!hasPerm('company-customize.index')"
          >{{ $t("Submit") }}</el-button
        >
      </div>
    </el-tooltip>
    <el-dialog
      :title="$t('DefaultBlockInformationModification')"
      :visible.sync="dialogVisible"
      width="95%"
    >
      <h2 class="dialog-title">{{ $t("Administrator") }}</h2>
      <img
        src="../../../assets/img/configuration/img-1.jpg"
        alt=""
        width="95%"
      />
      <img
        src="../../../assets/img/configuration/img-2.jpg"
        alt=""
        width="95%"
      />
      <h2 class="dialog-title">{{ $t("merchantSide") }}</h2>
      <img
        src="../../../assets/img/configuration/img-3.jpg"
        alt=""
        width="95%"
      />
      <img
        src="../../../assets/img/configuration/img-4.jpg"
        alt=""
        width="95%"
      />
    </el-dialog>
  </div>
</template>

<script>
import baseApi from "@/api/axios/base_api";
export default {
  data() {
    return {
      dialogVisible: false,
      // 预览
      dialogImage1: false,
      dialogImage2: false,
      dialogImage3: false,
      dialogImage4: false,
      dialogImage5: false,
      dialogImage6: false,
      dialogImage7: false,
      // 限制大小
      uploadDisabled1: false,
      uploadDisabled2: false,
      uploadDisabled3: false,
      uploadDisabled4: false,
      uploadDisabled5: false,
      uploadDisabled6: false,
      uploadDisabled7: false,

      fileList: [],
      companyInfo: {
        status: 1, //默认
        admin_url: "",
        merchant_url: "",
        consumer_url: "",
        admin_login_background: "",
        admin_login_title: "",
        admin_main_logo: "",
        merchant_login_background: "",
        merchant_login_title: "",
        merchant_main_logo: "",
        consumer_login_title: "",
      },
    };
  },
  computed: {
    Authorization() {
      return { Authorization: this.$store.state.token.token };
    },
    BaseApiImg() {
      return `${baseApi}admin/upload/image`;
    },
  },
  created() {
    this.getInterface();
  },
  methods: {
    // 预览
    handlePictureCardPreview1(file) {
      this.dialogImage1 = true;
    },
    handlePictureCardPreview2(file) {
      this.dialogImage2 = true;
    },
    handlePictureCardPreview3(file) {
      this.dialogImage3 = true;
    },
    handlePictureCardPreview4(file) {
      this.dialogImage4 = true;
    },
    handlePictureCardPreview5(file) {
      this.dialogImage5 = true;
    },
    handlePictureCardPreview6(file) {
      this.dialogImage6 = true;
    },
    handlePictureCardPreview7(file) {
      this.dialogImage7 = true;
    },
    // 提交
    submit() {
      this.$api.postInterface(this.companyInfo).then((res) => {
        if (!res) return;
        this.getInterface();
      });
    },
    getInterface() {
      this.$api.getInterface().then((res) => {
        this.companyInfo = res.data;
      });
    },
    blockDescription() {
      this.dialogVisible = true;
    },
    //   图片上传成功
    administratorSuccess1(response) {
      if (response.code === 200) {
        this.uploadDisabled1 = true;
        this.companyInfo.admin_login_background = response.data.path;
      } else {
        this.$message({
          message: this.$t("FileUploadFormatError"),
          type: "warning",
        });
      }
    },

    administratorSuccess2(response) {
      if (response.code === 200) {
        this.uploadDisabled2 = true;
        this.companyInfo.admin_login_title = response.data.path;
      } else {
        this.$message({
          message: this.$t("FileUploadFormatError"),
          type: "warning",
        });
      }
    },
    administratorSuccess3(response) {
      if (response.code === 200) {
        this.uploadDisabled3 = true;
        this.companyInfo.admin_main_logo = response.data.path;
      } else {
        this.$message({
          message: this.$t("FileUploadFormatError"),
          type: "warning",
        });
      }
    },

    merchantSuccess1(response) {
      if (response.code === 200) {
        this.uploadDisabled4 = true;

        this.companyInfo.merchant_login_background = response.data.path;
      } else {
        this.$message({
          message: this.$t("FileUploadFormatError"),
          type: "warning",
        });
      }
    },
    merchantSuccess2(response) {
      if (response.code === 200) {
        this.uploadDisabled5 = true;

        this.companyInfo.merchant_login_title = response.data.path;
      } else {
        this.$message({
          message: this.$t("FileUploadFormatError"),
          type: "warning",
        });
      }
    },
    merchantSuccess3(response) {
      if (response.code === 200) {
        this.uploadDisabled6 = true;

        this.companyInfo.merchant_main_logo = response.data.path;
      } else {
        this.$message({
          message: this.$t("FileUploadFormatError"),
          type: "warning",
        });
      }
    },

    h5Success(response) {
      if (response.code === 200) {
        this.uploadDisabled7 = true;

        this.companyInfo.consumer_login_title = response.data.path;
      } else {
        this.$message({
          message: this.$t("FileUploadFormatError"),
          type: "warning",
        });
      }
    },
    // 清除图片
    administratorClear1() {
      this.uploadDisabled1 = false;
      this.companyInfo.admin_login_background = "";
    },
    administratorClear2() {
      this.uploadDisabled2 = false;
      this.companyInfo.admin_login_title = "";
    },
    administratorClear3() {
      this.uploadDisabled3 = false;
      this.companyInfo.admin_main_logo = "";
    },

    merchantClear1() {
      this.uploadDisabled4 = false;
      this.companyInfo.merchant_login_background = "";
    },
    merchantClear2() {
      this.uploadDisabled5 = false;
      this.companyInfo.merchant_login_title = "";
    },
    merchantClear3() {
      this.uploadDisabled6 = false;
      this.companyInfo.merchant_main_logo = "";
    },

    h5Clear() {
      this.uploadDisabled7 = false;
      this.companyInfo.consumer_login_title = "";
    },
  },
};
</script>

<style lang="less" scoped>
.custom-interface {
  padding: 10px 20px;
  .custom-title {
    display: flex;
    align-items: center;
    div {
      margin-left: 10px;
      color: #f5b338;
    }
  }
  .el-input {
    width: 50%;
  }
  /deep/ .avatar-uploader .el-upload {
    border: 1px solid #d9d9d9;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    width: 300px;
    height: 178px;
    margin-right: 70px;
  }
  /deep/ .avatar-uploader .el-upload:hover {
    border-color: #409eff;
  }
  // /deep/ .el-upload-list {
  //   width: 300px;
  // }
  /deep/ .el-upload-list--picture-card .el-upload-list__item {
    width: 300px;
    height: 178px;
  }
  /deep/ .el-upload-list__item {
    transition: none !important;
  }
  .el-upload-tip {
    width: 300px;
    text-align: center;
    .tip-title {
      color: #c2c0c0;
      font-size: 13px;
    }
  }
  .avatar {
    width: 300px;
  }
  .custom-img {
    display: flex;
  }
  /deep/ .el-dialog__title {
    color: red;
    font-size: 23px;
    font-weight: bold;
  }
  .dialog-title {
    color: red;
  }
  .submit-btn {
    margin-top: 30px;
  }
}
</style>
<style lang="less" >
.custom-interface {
  .disabled .el-upload--picture-card {
    display: none !important;
  }
}
</style>
