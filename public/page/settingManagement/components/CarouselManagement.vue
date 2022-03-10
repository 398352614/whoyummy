<template>
  <!-- 轮播图管理 -->
  <div class="carousel-management">
    <h2>{{ $t("CarouselPicture") }}</h2>
    <p>{{ $t("AddCarouselAdjustCarouselOrder") }}</p>
    <el-form :model="form">
      <!-- <el-form-item :label="$t('')" class="updateChe"> -->
      <div class="update">
        <div class="img-item" v-for="(item, index) in baleImgList" :key="index">
          <div class="btn">
            <div class="index">{{ index + 1 }}</div>
            <div>
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('carousel.update') ? true : false"
              >
                <el-button
                  :disabled="!hasPerm('carousel.update')"
                  type="text"
                  size="mini"
                  @click="putCarousel(item.id)"
                  >{{ $t("Edit") }}</el-button
                >
              </el-tooltip>
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('carousel.destroy') ? true : false"
              >
                <el-button
                  :disabled="!hasPerm('carousel.destroy')"
                  type="text"
                  size="mini"
                  @click="delCarousel(item.id)"
                  >{{ $t("Delete") }}
                </el-button>
              </el-tooltip>
            </div>
          </div>
          <div class="img-box">
            <div>
              <img :src="item.picture_url" alt="" class="goods-img" />
              <span class="operat-box">
                <i
                  class="el-icon-zoom-in"
                  @click="onPreview(item.picture_url)"
                ></i>
              </span>
            </div>
            <div class="img-title">
              <div class="title-box">
                <div class="heading">{{ $t("JumpWay") }} :</div>
                <div>
                  {{ item.jump_type_name }}
                </div>
              </div>
              <div v-if="item.jump_type == 1" class="title-box">
                <div class="heading">{{ $t("InternalJump") }} :</div>
                <div>
                  {{ item.inside_jump_type_name }}
                </div>
              </div>
              <div v-if="item.jump_type == 2" class="title-box">
                <div class="heading">{{ "url : " }}</div>
                <div class="outside-url">
                  {{ item.outside_jump_url }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <el-tooltip
          :content="$t('AdministratorActivate')"
          placement="top"
          :disabled="hasPerm('carousel.store') ? true : false"
        >
          <div
            v-show="baleImgList.length < 6"
            class="update-img"

            @click="hasPerm('carousel.store') ? updateImg() : ''"
          >
            <i class="el-icon-plus"> </i>
          </div>
        </el-tooltip>
      </div>
      <!-- </el-form-item> -->
      <h2>{{ $t("ScrollInterval") }}</h2>
      <el-form-item>
        <el-select
          v-model="form.rolling_time"
          size="small"
          style="width: 300px"
        >
          <el-option
            v-for="item in carouselRollingTimeList"
            :key="item.id"
            :label="item.name"
            :value="item.id"
          >
          </el-option>
        </el-select>
      </el-form-item>
      <el-form-item>
        <el-tooltip
          :content="$t('AdministratorActivate')"
          placement="top"
          :disabled="hasPerm('carousel.sort') ? true : false"
          style="width: 70px"
        >
          <el-button
            type="primary"
            @click="onSubmit"
            :disabled="!hasPerm('carousel.sort')"
            :class="[hasPerm('carousel.sort') ? 'submit' : 'submit-disabled']"
            >{{ $t("Submit") }}
          </el-button>
        </el-tooltip>
      </el-form-item>
    </el-form>

    <!-- 弹出框 -->
    <el-form
      :model="formDialogImg"
      ref="formDialogImgRules"
      :rules="formDialogImgRules"
      label-width="120px"
    >
      <el-dialog
        :title="type == 1 ? $t('AddCarousel') : $t('ModifyTheCarousel')"
        :visible.sync="dialogUploadImage"
        width="60%"
      >
        <el-form-item :label="$t('uploadImage')" prop="picture_url">
          <el-upload
            :headers="Authorization"
            :on-success="handleAvatarSuccess"
            :action="BaseApiImg"
            :on-remove="handlerClearDeiver"
            :show-file-list="false"
            name="image"
            :data="{
              dir: 'driver',
            }"
            class="avatar-uploader"
          >
            <img
              v-if="formDialogImg.picture_url"
              :src="formDialogImg.picture_url"
              class="avatar"
            />
            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
          </el-upload>
        </el-form-item>
        <el-form-item :label="$t('JumpWay')" prop="jump_type">
          <el-select
            v-model="formDialogImg.jump_type"
            size="small"
            style="width: 300px"
          >
            <el-option
              v-for="item in carouselJumpTypeList"
              :key="item.name"
              :label="item.name"
              :value="item.id"
            >
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item
          :label="$t('MerchantType')"
          prop="inside_jump_type"
          v-if="formDialogImg.jump_type == 1"
        >
          <el-select
            v-model="formDialogImg.inside_jump_type"
            size="small"
            style="width: 300px"
          >
            <el-option
              v-for="item in articleTypeList"
              :key="item.name"
              :label="item.name"
              :value="item.id"
            >
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item
          :label="$t('ExternalLink')"
          prop="outside_jump_url"
          v-if="formDialogImg.jump_type == 2"
        >
          <el-input
            v-model="formDialogImg.outside_jump_url"
            size="small"
            style="width: 300px"
          ></el-input>
        </el-form-item>
        <el-form-item :label="$t('Sorting')" prop="sort_id">
          <el-input-number
            v-model="formDialogImg.sort_id"
            :min="1"
            :max="6"
            label=" "
            size="small"
          ></el-input-number>
        </el-form-item>
        <span slot="footer" class="dialog-footer">
          <el-button @click="dialogUploadImage = false" size="mini">{{
            $t("Cancel")
          }}</el-button>
          <el-button type="primary" @click="addCarousel" size="mini">{{
            $t("Confirm")
          }}</el-button>
        </span>
      </el-dialog>
    </el-form>
    <!-- 图片放大 -->
    <el-dialog :visible.sync="dialogImg" width="80%">
      <img :src="img" alt="" style="width: 100%" />
    </el-dialog>
  </div>
</template>

<script>
import Dispatch from "../Dispatch.vue";
import baseApi from "@/api/axios/base_api";
export default {
  components: { Dispatch },
  data() {
    return {
      dialogUploadImage: false,
      dialogImg: false,
      img: "",
      type: "",
      form: {
        rolling_time: "",
      },
      formDialogImg: {
        picture_url: "",
        jump_type: null,
        sort_id: "",
        inside_jump_type: "",
        outside_jump_url: "",
      },
      baleImgList: [],
      carouselJumpTypeList: [],
      articleTypeList: [],
      formDialogImgRules: {
        picture_url: [
          {
            required: true,
            message: this.$t("uploadImage"),
            trigger: "blur",
          },
        ],
        jump_type: [
          {
            required: true,
            message: this.$t("pleaseSelect"),
            trigger: "blur",
          },
        ],
        inside_jump_type: [
          {
            required: true,
            message: this.$t("pleaseSelect"),
            trigger: "blur",
          },
        ],
        outside_jump_url: [
          {
            required: true,
            message: this.$t("enterContent"),
            trigger: "blur",
          },
        ],
      },

      carouselRollingTimeList: [],
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
    this.getDictionary();
    this.getCarousel();
  },
  methods: {
    getDictionary() {
      this.$api.getDictionary().then((res) => {
        this.carouselJumpTypeList = res.data.carouselJumpTypeList;
        this.articleTypeList = res.data.articleTypeList;
        this.carouselRollingTimeList = res.data.carouselRollingTimeList;
      });
    },
    getCarousel() {
      this.$api.getCarousel().then((res) => {
        this.baleImgList = res.data.data;
        res.data.data.map((item) => {
          this.form.rolling_time = item.rolling_time;
        });
      });
    },
    delCarousel(id) {
      this.$confirm(this.$t("ConfirmToCancel") + "?", this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      }).then(() => {
        this.$api.delCarousel(id).then((res) => {
          if (res.code == 200) {
            this.getCarousel();
          }
        });
      });
    },
    onPreview(img) {
      this.dialogImg = true;
      this.img = img;
    },
    updateImg() {
      this.type = 1;
      this.dialogUploadImage = true;
      this.formDialogImg = {
        picture_url: "",
        jump_type: null,
        sort_id: "",
        inside_jump_type: "",
        outside_jump_url: "",
      };
      this.$refs.formDialogImgRules.resetFields();
    },
    putCarousel(id) {
      this.type = 2;
      this.dialogUploadImage = true;
      this.$api.getCarouselDetails(id).then((res) => {
        this.formDialogImg = res.data;
      });
    },
    addCarousel() {
      this.$refs.formDialogImgRules.validate((valid) => {
        if (valid) {
          if (this.type == 1) {
            this.$api.addCarousel(this.formDialogImg).then((res) => {
              if (res.code == 200) {
                this.dialogUploadImage = false;
                this.getCarousel();
              }
            });
          } else {
            this.$api.putCarousel(this.formDialogImg).then((res) => {
              if (res.code == 200) {
                this.dialogUploadImage = false;
                this.getCarousel();
              }
            });
          }
        }
      });
    },
    onSubmit() {
      this.form = {
        ...this.form,
        id_list: this.baleImgList.map((item) => item.id).join(","),
      };
      this.$api.putSortCarousel(this.form).then();
    },
    handleAvatarSuccess(response) {
      if (response.code === 200) {
        this.formDialogImg.picture_url = response.data.path;
      }
    },
    handlerClearDeiver() {
      this.formDialogImg.picture_url = "";
    },
  },
};
</script>

<style lang="less" scoped>
.carousel-management {
  padding: 0px 20px;
  p {
    color: #b6b6b6;
  }
  .update {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    .update-img {
      border: 1px solid #d9d9d9;
      text-align: center;
      cursor: pointer;
      i {
        cursor: pointer;
        font-size: 20px;
        color: #d9d9d9;
        line-height: 148px;
      }
    }
    .img-item {
      border: 1px solid #d9d9d9;
      .btn {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 5px;
        .index {
          width: 25px;
          height: 25px;
          border: 1px solid #d9d9d9;
          border-radius: 50%;
          text-align: center;
          line-height: 25px;
          font-size: 10px;
        }
      }
      .img-box {
        display: flex;
        align-items: center;
        margin: 5px;
        position: relative;
        .goods-img {
          width: 130px;
          height: 100px;
        }
        .operat-box {
          position: absolute;
          left: 105px;
          bottom: 5px;
          i {
            color: rgb(211, 204, 204);
            cursor: pointer;
            font-size: 20px;
            font-weight: 600;
          }
        }
        .img-title {
          .title-box {
            display: flex;
            .heading {
              margin-left: 10px;
              font-weight: 500;
              margin-right: 5px;
            }
            .outside-url {
              width: 160px;
              max-width: 300px;
              overflow: hidden;
            }
          }
        }
      }
    }
  }
}
</style>