<template>
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
            {{ $t("标椎模板") }}
          </div>
        </div>
        <div class="update-time">
          {{ $t("模板更新时间") }}:
          <span>{{ " 2021-03-04" }}</span>
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
              <el-button icon="el-icon-upload2" style="width: auto">{{
                $t("上传模板")
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
    <template v-if="is_started">
      <div class="uploading-table" ref="tableBox">
        <table border="1">
          <thead>
            <th>{{ $t("操作") }}</th>
            <th>{{ $t("状态") }}</th>
            <th v-for="(head, index) in excel_header" :key="index"></th>
          </thead>
          <tbody ref="tableBody">
            <tr v-for="(items, ii) in excel_data_paging" :key="ii">
              <td style="padding-right: 0">
                <el-tooltip effect="dark" placement="top">
                  <div slot="content">{{ $t("删除") }}</div>
                  <i
                    class="el-icon-circle-close"
                    @click="deleteTr((page_no - 1) * page_size + ii)"
                  ></i>
                </el-tooltip>
              </td>
              <td style="padding-right: 0">
                {{ items.status | validate_status }}
              </td>
              <td
                v-for="(item, i) in items"
                :key="i"
                :class="{ error: !item.substr }"
              >
                <editable-div
                  class="editable-div"
                  :value="!item.substr ? item.value : item"
                  @change="changeTd((page_no - 1) * page_size + ii, i, $event)"
                ></editable-div>
                <el-tooltip
                  effect="dark"
                  v-if="!item.substr"
                  placement="top-start"
                >
                  <div
                    slot="content"
                    style="line-height: 1.8"
                    v-for="(tip, index) in item.tips"
                    :key="tip"
                  >
                    <span v-if="item.tips.length - 1">{{ index + 1 }}. </span
                    >{{ tip }}
                  </div>
                  <i class="el-icon-warning el-input__icon"></i>
                </el-tooltip>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <el-pagination
        class="paging"
        v-if="excel_data.length"
        @current-change="page_no = $event"
        :current-page="page_no"
        :page-size="page_size"
        @size-change="page_size = $event"
        layout="prev, pager, next, sizes, total"
        :page-sizes="[10, 20, 30, 40, 50]"
        :total="excel_data.length"
      >
      </el-pagination>
    </template>
    <!-- <div>
      <el-form>
        <el-table
 max-height="600px"          :data="tableData"
          v-loading="global_loading()"
          element-loading-text="loading..."
          border
          style="width: 95%; margin: 20px auto"
          :header-cell-style="{ background: '#416f16', color: '#fff' }"
        >
          <el-table-column type="index" width="50">
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
                  <i slot="reference" class="el-icon-warning check-icon" />
                </el-popover>
              </span>
              <span v-else>{{ scope.$index + 1 }}</span>
            </template>
          </el-table-column>
          <el-table-column
            prop="date"
            :label="$t('Operation')"
            width="100"
            align="center"
          >
            <template slot-scope="scope">
              <el-button
                class="operation"
                @click="delItem(scope.$index)"
                size="small"
                type="text"
                >{{ $t("Delete") }}</el-button
              >
            </template>
          </el-table-column>
          <el-table-column
            prop="date"
            :label="$t('状态')"
            width="100"
            align="center"
          >
            {{ "待检查" }}
          </el-table-column>
          <el-table-column prop="email" :label="$t('客户姓名')" align="center">
            <template slot-scope="scope">
              <el-form-item :error="checkModle[scope.$index].place_fullname">
                <el-input v-model="scope.row.place_fullname" size=""></el-input>
              </el-form-item>
            </template>
          </el-table-column>
          <el-table-column prop="email" :label="$t('客户电话')" align="center">
            <template slot-scope="scope">
              <el-form-item :error="checkModle[scope.$index].place_phone">
                <el-input
                  v-model="scope.row.place_phone"
                  :disabled="scope.row.place_phone === 1"
                ></el-input>
              </el-form-item>
            </template>
          </el-table-column>
          <el-table-column prop="email" :label="$t('详细地址')" align="center">
            <template slot-scope="scope">
              <el-form-item :error="checkModle[scope.$index].place_address">
                <el-input v-model="scope.row.place_address"></el-input>
              </el-form-item>
            </template>
          </el-table-column>
        </el-table>
      </el-form>
    </div> -->
    <div class="uploading-save">
      <div class="model-download">
        <div class="batch-icon">3</div>
        <el-button style="width: auto" @click="orderImportCheck">{{
          $t("检查")
        }}</el-button>
        <!-- <el-button
          type="primary"
          @click="batchValidatePackage"
          :disabled="!is_started || is_validate_success"
          >{{ $t("Check") }}</el-button
        > -->
      </div>
    </div>
    <div class="uploading-save">
      <div class="model-download">
        <div class="batch-icon">4</div>
        <el-select v-model="value" placeholder="请选择货主">
          <el-option
            v-for="item in options"
            :key="item.value"
            :label="item.label"
            :value="item.value"
          >
          </el-option>
        </el-select>
        <!-- <el-button
          type="primary"
          @click="batchValidatePackage"
          :disabled="!is_started || is_validate_success"
          >{{ $t("Check") }}</el-button
        > -->
      </div>
    </div>
    <div>
      <div class="uploading-save">
        <div class="model-download" style="margin-left: 45px">
          <!-- <div class="batch-icon"></div> -->
          <el-button style="width: auto">{{ $t("提交") }}</el-button>
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
      url: baseApi + "admin/address/excel", //上传地址
      excel_data: [], // excel数据
      excel_header: [], // excel 表头数据
      type_name: "", // excel 类型
      page_no: 1,
      page_size: 10,
      pre_submit_data: [],
      submit_data: [], // 提交检查的列表
      submit_data_total: 1, // 待提交列表总数
      submit_dialog: false, // 提交弹窗
      excel_name: this.$t("下单表（待上传）"),
      progress: 0, // 上传execel的进度
      submit_loading: false,
      templateList: [], // execel模板列表
      cur_template: 0,
      validate_dialog: false, // 检查进度弹窗
      validate_result: {
        // 检查结果统计
        success: 0,
        error: 0,
        total: 1,
      },
      validate_queue: [], // 检查任务队列
      is_cancel: false, // 是否中途取消检查
      is_scroll: false, // 是否滚到到错误地点
    };
    // return {
    //   tableData: [],
    //   templateList: {}, //模板
    //   url: baseApi + "admin/address/excel", //上传地址
    //   excel_name: "下单表（待上传）",
    //   progress: 0, // 上传execel的进度
    //   is_started: false,
    // };
  },
  computed: {
    Authorization() {
      return { Authorization: this.$store.state.token.token };
    },
    excel_data_paging() {
      // 分页的excel列表数据
      if (!this.is_started) return [];
      const no = this.page_no;
      const size = this.page_size;
      return this.excel_data.slice((no - 1) * size, no * size);
    },
    is_started() {
      // 是否开始上传
      return !!this.excel_data.length;
    },
  },
  created() {},
  methods: {
    // 获得模板 下载模板
    getTemplate() {
      this.$api.getTemplate().then((res) => {
        const link = document.createElement("a");
        let fname = "templateTips.docx";
        link.href = res.data.path;
        link.setAttribute("download", fname);
        document.body.appendChild(link);
        link.click();
        this.templateList = res.data;
      });
    },
    // 获取上传时的url
    uploadExcelSuccess(res) {
      if (res) {
        this.excel_data = res.data;
        this.excel_header = res.data;
        this.type_name = res.data;
        this.$notify({
          title: this.$t("您的文件已上载成功"),
          message: res.tips,
          type: "success",
        });
      } else {
        this.uploadError(res);
      }
    },
    // uploadExcelSuccess(res, file, fileList) {
    //   if (res.code == 200) {
    //     this.$message({
    //       type: "success",
    //       message: res.msg,
    //     });
    //     this.tableData = [];
    //     for (const item in res.data) {
    //       console.log(res.data[item]);
    //       // for (let i = 0; i < 5; i++) {
    //       //   console.log(
    //       //     res.data[item][`item_type_${i + 1}`],
    //       //     res.data[item][`item_count_${i + 1}`]
    //       //   );
    //       //   if (
    //       //     res.data[item][`item_type_${i + 1}`] &&
    //       //     res.data[item][`item_type_${i + 1}`] !== 1
    //       //   ) {
    //       //     if (!res.data[item][`item_count_${i + 1}`]) {
    //       //       res.data[item][`item_count_${i + 1}`] = 1;
    //       //     }
    //       //     if (!res.data[item][`item_weight_${i + 1}`]) {
    //       //       res.data[item][`item_weight_${i + 1}`] = 1;
    //       //     }
    //       //   } else if (res.data[item][`item_type_${i + 1}`] === 1) {
    //       //     res.data[item][`item_count_${i + 1}`] = 1;
    //       //     if (!res.data[item][`item_weight_${i + 1}`]) {
    //       //       res.data[item][`item_weight_${i + 1}`] = 1;
    //       //     }
    //       //   }
    //       // }
    //     }
    //     this.tableData = this.tableData.concat(res.data);
    //     this.checkModle = [];
    //     for (let i = 0; i < res.data.length; i++) {
    //       const obj = {};
    //       for (let item in res.data[i]) {
    //         obj[item] = null;
    //       }
    //       this.checkModle.push({ ...obj, log: "" });
    //     }
    //     this.loading.close();
    //     this.uploading = 100;
    //     this.uploadText = this.$t("Config.uploadSuccess");
    //     this.btnType = "success";
    //     this.dialogVisible = true;
    //     this.is_started = true;
    //     return;
    //   } else {
    //     this.$message({
    //       type: "error",
    //       message: res.msg,
    //     });
    //     this.loading.close();
    //   }
    // },
    // 删除表单项
    delItem(index) {
      this.tableData.splice(index, 1);
    },
    // 检查
    // 检查
    orderImportCheck() {
      console.log(this.tableData, "tableData");
      let list = JSON.stringify(this.tableData);
      this.$api.orderImportCheck({ list: list }).then((res) => {
        console.log(res);
        let is_log = false;
        res.data.forEach((ele, index) => {
          this.tableData[index].lat = ele.lat;
          this.tableData[index].lon = ele.lon;
          this.tableData[index].receiver_city = ele.receiver_city;
          this.tableData[index].receiver_street = ele.receiver_street;
          for (const item in this.checkModle[index]) {
            if (ele[item] === undefined) {
              this.checkModle[index][item] = "";
            } else {
              this.checkModle[index][item] = ele[item];
            }
          }
          if (ele.status === 0) {
            is_log = true;
          }
        });
        this.is_no_problem = is_log;
        if (is_log) {
          res.data.forEach((ele, index) => {
            this.tableData[index].lat = "";
            this.tableData[index].lon = "";
          });
          this.$message({
            type: "error",
            message: "失败",
          });
        } else {
          this.$message({
            type: "success",
            message: "成功",
          });
        }
      });
    },
    // uploadExcelSuccess(res) {
    //   this.tableData = res.data;
    //   console.log("res");
    //   console.log(res);
    // },
    // 上传Excel进度条
    uploadProgress(event, file) {
      this.excel_name = file.name;
      this.progress = +event.percent.toFixed(2);

      console.log("event, file");
      console.log(event, file);
    },
    // 上传失败
    uploadError(res) {
      console.log("res");
      console.log(res);
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
      width: 100px;
      position: relative;
      display: flex;
      margin: 30px;
      border: 1px solid #d2d2d2;
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
        border-color: transparent transparent #d2d2d2 transparent;
        transform: rotate(-40deg);
      }
    }
    .model-div:hover {
      border-color: #390;

      &:after {
        border-color: transparent transparent #390 transparent;
      }
    }
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
.uploading-table {
  margin-left: 30px;
  margin-top: 30px;
  overflow-x: scroll;
  overflow-y: hidden;
  table {
    white-space: nowrap;
    border-collapse: collapse;
    position: relative;
    tr {
      position: relative;
    }
    th {
      background-color: #375623;
      color: #fff;
      font-weight: normal;
      font-size: 16px;
      padding: 0.2em 0.5em;
    }
    th,
    td {
      min-width: 0;
      overflow: hidden;
      min-height: 34px;
      margin: 1px;
    }
    // td {
    //   // padding-right: 35px;
    // }
    .el-icon-circle-cross {
      cursor: pointer;
    }
  }
}
</style>
