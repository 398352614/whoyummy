<template >
  <!-- 新增文章 -->
  <div class="add-article">
    <el-form :model="form">
      <el-form-item>
        <h3>{{ $t("title") }}</h3>
        <el-input v-model="form.tittle" style="width: 400px"></el-input>
      </el-form-item>
      <el-form-item>
        <h3>{{ $t("MerchantType") }}</h3>
        <el-radio-group v-model="form.type">
          <el-radio
            v-for="(item, index) in articleTypeList"
            :key="index"
            :label="item.id"
            >{{ item.name }}</el-radio
          >
        </el-radio-group>
      </el-form-item>
      <el-form-item>
        <h3>{{ $t("CoverPicture") }}</h3>
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
          <img v-if="form.picture_url" :src="form.picture_url" class="avatar" />
          <i v-else class="el-icon-plus avatar-uploader-icon"></i>
        </el-upload>
      </el-form-item>
      <el-form-item>
        <h3>{{ $t("Content") }}</h3>
        <el-row>
          <el-col :span="20">
            <div id="editor" :value="form.text" @input="changeText"></div>
          </el-col>
        </el-row>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="onSubmit"
          >{{ $t("Submit") }}
        </el-button>
      </el-form-item>
    </el-form>
  </div>
</template>
<script>
import Wangeditor from "wangeditor";
import baseApi from "@/api/axios/base_api";
export default {
  data() {
    return {
      articleTypeList: [],
      form: {
        tittle: "",
        type: "",
        text: "",
        picture_url: "",
      },
      editor: null,
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
  mounted() {
    this.editor = new Wangeditor("#editor");
    this.editor.config.menus = [
      "head",
      "fontSize",
      "fontName",
      "bold",
      "italic",
      "underline",
      "strikeThrough",
      "foreColor",
      "backColor",
      "link",
      "list",
      "justify",
      "quote",
      "video",
      "image",
      "table",
    ];
    this.editor.config.onchange = (html) => {
      this.form.text = html;
    };
    this.editor.config.uploadImgServer = `${baseApi}admin/upload/image`;
    this.editor.config.uploadImgParams = {
      dir: "merchant",
    };
    this.editor.config.uploadImgHeaders = {
      Authorization: this.$store.state.token.token,
    };
    this.editor.config.uploadFileName = `image`;
    this.editor.config.uploadImgHooks = {
      customInsert: (insertImg, result) => {
        if (result.code === 200) {
          this.$message.success(this.$t("uploadedSuccessfully"));
          let url = result.data.path;
          insertImg(url);
        }
      },
    };
    this.editor.config.zIndex = 500;
    this.editor.config.showLinkImg = true;
    this.editor.create();
  },
  created() {
    this.getDictionary();
    if (this.$route.query.id) {
      this.getArticleDetails();
    }
  },
  methods: {
    // 详情
    getArticleDetails() {
      this.$api.getArticleDetails(this.$route.query.id).then((res) => {
        if (res.code == 200) {
          this.form = res.data;
          this.editor.txt.html(this.form.text);
        }
      });
    },
    getDictionary() {
      this.$api.getDictionary().then((res) => {
        this.articleTypeList = res.data.articleTypeList;
      });
    },
    onSubmit() {
      if (!this.$route.query.id) {
        // 如果是新增状态
        this.$api.addArticle(this.form).then((res) => {
          if (res.code == 200) {
            this.$router.push({
              name: "H5configuration",
              query: {
                type: 3,
              },
            });
          } else {
            this.$message({
              message: res.msg,
              type: "error",
            });
          }
        });
      } else {
        // 如果是编辑状态
        this.$api.putArticle(this.$route.query.id, this.form).then((res) => {
          if (res.code == 200) {
            this.$router.push({
              name: "H5configuration",
              query: {
                type: 3,
              },
            });
          } else {
            this.$notify({
              message: res.msg,
              type: "warning",
            });
          }
        });
      }
    },
    // 判断是新增 还是 编辑
    changeText() {
      this.$emit("input", this.editor.txt.html());
    },
    handleAvatarSuccess(response) {
      if (response.code === 200) {
        this.form.picture_url = response.data.path;
      }
    },
    handlerClearDeiver() {
      this.form.picture_url = "";
    },
  },
};
</script>

<style lang="less" scoped>
.add-article {
  padding: 20px;
  background-color: #fff;
}
</style>