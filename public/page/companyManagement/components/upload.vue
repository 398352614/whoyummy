<template>
  <!-- 导入 -->
  <div class="upload">
    <div class="domestic-batch-uploading" style="min-height: auto">
      <div class="model-download">
        <div class="batch-icon">1</div>
        <div class="model-text">
          <div class="model-title">{{ $t("DownloadTemplate") }}</div>
        </div>
      </div>
      <div class="domestic-batch-uploading-model">
        <div class="model-content">{{ $t("GenericTemplate") }}</div>
        <div class="model-div" @click="getTemplate">
          <div class="model-div-icon">
            <img
              src="../../../assets/img/configuration-img/excel.png"
              style="width: 30px"
            />
          </div>
          <div class="model-div-content">
            {{ $t("standardTemplate") }}
          </div>
        </div>
        <div class="update-time">
          {{ $t("templateUpdatedTime") }}:
          <span>{{ " 2021-05-29" }}</span>
        </div>
      </div>
    </div>
    <div class="domestic-batch-uploading">
      <div class="model-uploading">
        <div class="model-download">
          <div class="batch-icon">2</div>
          <div class="uploading-buttton">
            <el-upload
              class="upload-demo"
              :action="url"
              :show-file-list="false"
              :on-success="uploadExcelSuccess"
              :on-error="uploadError"
              :on-progress="uploadProgress"
              :headers="Authorization"
            >
              <el-button icon="el-icon-upload2" style="width: 160px">{{
                $t("uploadedTemplate")
              }}</el-button>
            </el-upload>
          </div>
          <div class="uploading-plan">
            <div class="uploading-img">
              <img
                src="../../../assets/img/configuration-img/excel.png"
                style="width: 30px"
              />
            </div>
            <p>{{ excel_name }}</p>
            <div class="progress" v-show="progress">
              <el-progress :percentage="progress"></el-progress>
            </div>
          </div>
        </div>
      </div>
    </div>
    <el-form :rules="rules" ref="ruleForm" :model="ruleForm">
      <div v-if="is_started">
        <el-table
          max-height="600px"
          :data="tableData"
          v-loading="global_loading()"
          element-loading-text="loading..."
          border
          style="width: 93%; margin: 20px auto"
          :header-cell-style="{ background: '#25ad7a', color: '#fff' }"
          :cell-style="{ padding: '0px 0px' }"
        >
          <el-table-column type="index" width="50" align="center">
            <template slot="header">
              <span>{{ $t("Number") }}</span>
            </template>
            <template slot-scope="scope">
              <span>{{ scope.$index + 1 }}</span>
              <span v-if="checkModle[scope.$index].log != ''">
                <el-popover
                  trigger="hover"
                  :content="checkModle[scope.$index].log"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </span>
            </template>
          </el-table-column>
          <!-- <el-table-column type="index" width="50" align="center">
            <template slot="header">
              <span>{{ $t("Number") }}</span>
            </template>
            <template slot-scope="scope">
              <span v-if="checkModle[scope.$index].log">
                <el-popover
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].log"
                >
                  <i slot="reference" class="check-icon" />
                </el-popover>
              </span>
              <span v-else>{{ scope.$index + 1 }}</span>
            </template>
          </el-table-column> -->
          <el-table-column
            prop="date"
            :label="$t('Operation')"
            width="50"
            align="center"
          >
            <template slot-scope="scope">
              <el-tooltip effect="dark" placement="top">
                <div slot="content">{{ $t("Delete") }}</div>
                <i
                  class="el-icon-circle-close"
                  @click="delItem(scope.$index)"
                ></i>
              </el-tooltip>
            </template>
          </el-table-column>
          <!-- <el-table-column
            prop="date"
            :label="$t('状态')"
            width="100"
            align="center"
          >
            {{ status }}
          </el-table-column> -->
          <el-table-column
            prop="type"
            :label="$t('MerchantType')"
            align="center"
            width="180"
          >
            <template slot-scope="scope">
              <el-form-item>
                <el-select v-model="scope.row.type">
                  <el-option
                    v-for="item in orderTypeList"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                  ></el-option>
                </el-select>

                <el-popover
                  v-if="checkModle[scope.$index].type"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].type"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>
            </template>
          </el-table-column>
          <el-table-column
            prop="place_fullname"
            :label="$t('Name')"
            align="center"
            width="150"
          >
            <template slot-scope="scope">
              <!-- :error="checkModle[scope.$index].place_fullname" -->
              <el-form-item>
                <el-input
                  v-model="scope.row.place_fullname"
                  @input="getChange"
                ></el-input>
                <el-popover
                  v-if="checkModle[scope.$index].place_fullname"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].place_fullname"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>
            </template>
          </el-table-column>
          <el-table-column
            prop="place_phone"
            :label="$t('Phone')"
            align="center"
            width="200"
          >
            <template slot-scope="scope">
              <el-form-item>
                <el-input
                  v-model="scope.row.place_phone"
                  :disabled="scope.row.place_phone === 1"
                  @input="getChange"
                ></el-input>
                <el-popover
                  v-if="checkModle[scope.$index].place_phone"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].place_phone"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>
            </template>
          </el-table-column>
          <el-table-column
            prop="place_country"
            :label="$t('Country')"
            align="center"
            width="150"
          >
            <template slot-scope="scope">
              <el-form-item>
                <el-input
                  v-model="scope.row.place_country"
                  @input="getChange"
                ></el-input>
                <el-popover
                  v-if="checkModle[scope.$index].place_country"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].place_country"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>
            </template>
          </el-table-column>
          <el-table-column
            prop="place_province"
            :label="$t('provinceState')"
            align="center"
            width="150"
          >
            <template slot-scope="scope">
              <el-form-item>
                <el-input
                  v-model="scope.row.place_province"
                  @input="getChange"
                ></el-input>
                <el-popover
                  v-if="checkModle[scope.$index].place_province"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].place_province"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>
            </template>
          </el-table-column>
          <el-table-column
            prop="place_post_code"
            :label="$t('ZipCode')"
            align="center"
            width="150"
          >
            <template slot-scope="scope">
              <el-form-item>
                <el-input
                  v-model="scope.row.place_post_code"
                  @input="getChange"
                ></el-input>
                <el-popover
                  v-if="checkModle[scope.$index].place_post_code"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].place_post_code"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>
            </template>
          </el-table-column>
          <el-table-column
            prop="place_house_number"
            :label="$t('DoorNo')"
            align="center"
            width="150"
          >
            <template slot-scope="scope">
              <el-form-item>
                <el-input
                  v-model="scope.row.place_house_number"
                  @input="getChange"
                ></el-input>
                <el-popover
                  v-if="checkModle[scope.$index].place_house_number"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].place_house_number"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>
            </template>
          </el-table-column>
          <el-table-column
            prop="place_city"
            :label="$t('City')"
            align="center"
            width="150"
          >
            <template slot-scope="scope">
              <el-form-item>
                <el-input
                  v-model="scope.row.place_city"
                  @input="getChange"
                ></el-input>
                <el-popover
                  v-if="checkModle[scope.$index].place_city"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].place_city"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>
            </template>
          </el-table-column>
          <el-table-column
            prop="place_district"
            :label="$t('area')"
            align="center"
            width="150"
          >
            <template slot-scope="scope">
              <el-form-item>
                <el-input
                  v-model="scope.row.place_district"
                  @input="getChange"
                ></el-input>
                <el-popover
                  v-if="checkModle[scope.$index].place_district"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].place_district"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>
            </template>
          </el-table-column>
          <el-table-column
            prop="place_street"
            :label="$t('Street')"
            align="center"
            width="200"
          >
            <template slot-scope="scope">
              <el-form-item>
                <el-input
                  v-model="scope.row.place_street"
                  @input="getChange"
                ></el-input>
                <el-popover
                  v-if="checkModle[scope.$index].place_street"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].place_street"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>
            </template>
          </el-table-column>
          <el-table-column
            prop="email"
            :label="$t('DetailedAddress')"
            align="center"
            width="300"
          >
            <template slot-scope="scope">
              <el-form-item>
                <el-input
                  v-model="scope.row.place_address"
                  @input="getChange"
                ></el-input>
                <el-popover
                  v-if="checkModle[scope.$index].place_address"
                  placement="top-start"
                  trigger="hover"
                  :content="checkModle[scope.$index].place_address"
                >
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </el-form-item>
            </template>
          </el-table-column>
        </el-table>
      </div>
      <div class="uploading-save">
        <div class="model-download">
          <div class="batch-icon">3</div>
          <el-button
            style="width: auto"
            @click="ImportCheck"
            class="but"
            type="primary"
            :disabled="!is_started"
            >{{ $t("anExamination") }}</el-button
          >
        </div>
      </div>
      <div class="uploading-save">
        <div class="model-download">
          <div class="batch-icon">4</div>
          <el-form-item prop="merchant_id">
            <el-select
              v-model="ruleForm.merchant_id"
              :placeholder="$t('Merchant')"
              style="width: 150px"
            >
              <el-option
                v-for="item in merchantList"
                :key="item.id"
                :label="item.name"
                :value="item.id"
              >
              </el-option>
            </el-select>
          </el-form-item>
        </div>
      </div>
    </el-form>

    <div>
      <div class="uploading-save">
        <div class="model-download">
          <div class="batch-icon">5</div>
          <el-button
            style="width: auto"
            @click="Submit"
            class="but"
            :disabled="is_no_problem"
            >{{ $t("Submit") }}</el-button
          >
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import baseApi from "@/api/axios/base_api";
import loading from "@/mixin/global_loading";
import EditableDiv from "@/page/companyManagement/components/EditableDiv.vue";

export default {
  mixins: [loading],
  components: {
    EditableDiv,
  },
  data() {
    return {
      ruleForm: {
        merchant_id: "",
      },
      rules: {
        merchant_id: [
          { required: true, message: this.$t("Required"), trigger: "change" },
        ],
      },
      tableData: [],
      checkModle: [],
      errorList: [],
      merchantList: [], //货主
      orderTypeList: [
        { id: 1, name: this.$t("Sender") },
        { id: 2, name: this.$t("Receiver") },
      ],

      url: baseApi + "admin/address/excel", //上传地址
      excel_name: this.$t("orderForm"),
      status: "待检查",
      progress: 0, // 上传execel的进度
      is_started: false,
      loading: null,
      is_no_problem: true,
    };
  },
  computed: {
    Authorization() {
      return { Authorization: this.$store.state.token.token };
    },
  },
  created() {
    this.getMerchantsList();
  },
  methods: {
    getChange() {
      var status = "待检查";
      this.status = status;
    },
    // 货主信息
    getMerchantsList() {
      this.$api.getMerchantsPublic().then((res) => {
        this.merchantList = res.data.data;
      });
    },
    // 获得模板 下载模板
    getTemplate() {
      this.$api.getTemplate().then((res) => {
        const link = document.createElement("a");
        let fname = "templateTips.docx";
        link.href = res.data.path;
        link.setAttribute("download", fname);
        document.body.appendChild(link);
        link.click();
      });
    },
    uploadExcelSuccess(res, file, fileList) {
      if (res.code == 200) {
        this.is_started = true;
        this.tableData = [];
        // for (const item in res.data) {
        //   console.log(res.data[item]);
        //   for (let i = 0; i < 5; i++) {
        //     console.log(
        //       res.data[item][`item_type_${i + 1}`],
        //       res.data[item][`item_count_${i + 1}`]
        //     );
        //     ;
        //     if (
        //       res.data[item][`item_type_${i + 1}`] &&
        //       res.data[item][`item_type_${i + 1}`] !== 1
        //     ) {
        //       if (!res.data[item][`item_count_${i + 1}`]) {
        //         res.data[item][`item_count_${i + 1}`] = 1;
        //       }
        //       if (!res.data[item][`item_weight_${i + 1}`]) {
        //         res.data[item][`item_weight_${i + 1}`] = 1;
        //       }
        //     } else if (res.data[item][`item_type_${i + 1}`] === 1) {
        //       res.data[item][`item_count_${i + 1}`] = 1;
        //       if (!res.data[item][`item_weight_${i + 1}`]) {
        //         res.data[item][`item_weight_${i + 1}`] = 1;
        //       }
        //     }
        //     ;
        //   }
        // }
        this.tableData = this.tableData.concat(res.data);
        this.checkModle = [];
        for (let i = 0; i < res.data.length; i++) {
          const obj = {};
          for (let item in res.data[i]) {
            obj[item] = null;
          }
          this.checkModle.push({ ...obj, log: "" });
        }
        // this.loading.close();
        this.is_started = true;
        this.$notify({
          title: this.$t("uploadedSuccessfully"),
          // message: res.msg,
          type: "success",
        });
        this.progress = 0;
        return;
      } else {
        this.uploadError(res);

        // this.$message({
        //   type: "error",
        //   message: res.msg,
        // });
        // this.loading.close();
      }
    },
    // 上传Excel进度条
    uploadProgress(event, file) {
      this.excel_name = file.name;
      this.progress = +event.percent.toFixed(2);

      console.log("event, file");
      console.log(event, file);
    },
    // 上传失败
    uploadError(res) {
      this.excel_name = this.$t("PleaseReUpload");
      this.resetDate();
      this.$notify({
        title: this.$t("uploadFailed"),
        message: res.msg,
        type: "error",
      });
    },
    // 重新上传,重置数据
    resetDate() {
      this.tableData = [];
      this.progress = 0;
    },
    // 删除表单项
    delItem(index) {
      console.log("index");
      console.log(index);
      let checkModle = this.checkModle;
      checkModle.map((item, l) => {
        if (l == index) {
          this.checkModle.splice(l, 1);
          this.tableData.splice(index, 1);
        }
      });
      // if (this.checkModle[index] == index) {
      // }
    },
    // 检查
    ImportCheck() {
      let list = JSON.stringify(this.tableData);
      this.$api.ImportCheck({ list: list }).then((res) => {
        console.log(res);
        let is_log = false;
        this.errorList = [];
        let tableData = [];
        res.data.map((item, index) => {
          let obj = item.error;
          this.errorList.push(obj);
          if (item.status === 2) {
            is_log = true;
            this.status = this.$t("failure");
          } else {
            this.status = this.$t("success");
          }
          tableData.push(item.data);
          this.tableData = tableData;
          // this.tableData[index].place_lat = item.data.place_lat;
          // this.tableData[index].place_lon = item.data.place_lon;
        });
        this.errorList.forEach((ele, index) => {
          console.log("ele, index");
          console.log(ele, index);
          // this.tableData[index].place_lat = ele.place_lat;
          // this.tableData[index].place_lon = ele.place_lon;
          // this.tableData[index].receiver_city = ele.receiver_city;
          // this.tableData[index].receiver_street = ele.receiver_street;
          for (const item in this.checkModle[index]) {
            if (ele[item] === undefined) {
              this.checkModle[index][item] = "";
            } else {
              this.checkModle[index][item] = ele[item];
            }
          }
        });
        this.is_no_problem = is_log;
        if (is_log) {
          // res.data.forEach((ele, index) => {
          //   this.tableData[index].place_lat = "";
          //   this.tableData[index].place_lon = "";
          // });
          this.$message({
            type: "error",
            message: this.$t("failure"),
          });
        } else {
          this.$message({
            type: "success",
            message: this.$t("success"),
          });
        }
      });
    },

    // 提交
    Submit() {
      this.$refs.ruleForm.validate((valid) => {
        if (valid) {
          let list = JSON.stringify(this.tableData);
          this.$api
            .submitTemplate({
              list: list,
              merchant_id: this.ruleForm.merchant_id,
            })
            .then((res) => {
              if (res.code === 200) {
                this.$message({
                  type: "success",
                  message: res.msg,
                });
                this.tableData = [];
                this.$router.push({
                  name: "CustomerManagement",
                  params: {
                    CustomerManagement: 1,
                  },
                });
              } else {
                this.$message({
                  type: "error",
                  message: res.msg,
                });
              }
            });
        }
      });
    },
  },
};
</script>

<style lang="less" scoped>
.upload {
  width: 95%;
  margin: 20px auto;
  // text-align: center;
}
.batch-icon {
  width: 30px;
  height: 30px;
  line-height: 30px;
  text-align: center;
  border-radius: 50%;
  border: 1px solid rgb(209, 206, 206);
  background: #fff;
  font-weight: 400;
  color: #010101;
  margin-right: 1rem;
}
.domestic-batch-uploading {
  //   min-height: 500px;
  //   background: #fff;
  //   border: 1px solid #fff;
  //   margin-top: -1px;
  //   padding: 30px;
  .model-download {
    display: flex;
    flex-direction: row;
    align-items: center;
    .model-text {
      display: flex;
      flex-direction: row;
      align-items: center;
      .model-title {
        font-size: 1.2rem;
        color: #010101;
        font-weight: 600;
      }
    }
  }
  .domestic-batch-uploading-model {
    margin: 20px 50px;
    width: 450px;
    height: 200px;
    border: 1px solid rgb(209, 206, 206);
    background-color: #fff;
    .model-content {
      margin-left: 30px;
      margin-top: 20px;
      font-weight: 500;
    }
    .update-time {
      margin-left: 30px;
      font-size: 13px;
      span {
        color: red;
      }
    }
    .model-div {
      width: 150px;
      position: relative;
      display: flex;
      margin: 30px;
      border: 1px solid #390;
      padding: 10px 20px;
      cursor: pointer;
      .model-div-icon {
        width: 35px;
        text-align: right;
      }
      .model-div-content {
        text-indent: 5px;
        line-height: 30px;
        font-size: 10px;
      }
      &::before {
        content: "";
        position: absolute;
        width: 0;
        height: 0;
        border-width: 10px;
        right: -3px;
        bottom: -4px;
        border-style: solid;
        border-color: #fff #fff #fff #fff;
        transform: rotate(-40deg);
      }
      &:after {
        content: "";
        position: absolute;
        width: 0;
        height: 0;
        border-width: 10px;
        right: 3px;
        bottom: 3px;
        border-style: solid;
        border-color: transparent transparent #390 transparent;
        transform: rotate(-40deg);
      }
    }
    // .model-div:hover {
    //   border-color: #390;

    //   &:after {
    //     border-color: transparent transparent #390 transparent;
    //   }
    // }
  }
  .model-uploading {
    display: flex;
    flex-direction: row;
    align-items: center;
    .uploading-plan {
      margin: 0 40px;
      height: 30px;
      display: flex;
      padding: 5px;
      // background: #fff;

      .uploading-img {
        margin-top: 1px;
      }
      p {
        font-size: 12px;
        color: grey;
        margin-top: 10px;
        margin-left: 5px;
        margin-right: 20px;
      }
      .progress {
        margin-top: 6px;
        margin-right: 5px;
        width: 200px;
      }
    }
  }
}
.uploading-save {
  margin-top: 20px;
  .model-download {
    display: flex;
    flex-direction: row;
    align-items: center;
  }
}
.check-icon {
  color: #d81d06;
  font-size: 15px;
  position: absolute;
  top: 25px;
}
.el-input {
  top: 10px !important;
  width: 90% !important;
}
.el-select {
  top: 10px !important;
}
/deep/.el-form-item__error {
  padding-top: 12px !important;
}
.but {
  padding-left: 60px;
  padding-right: 60px;
  background: #83aabf !important;
  border-color: #83aabf !important;
  color: #fff !important;
}
</style>
