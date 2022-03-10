<template>
  <!-- 备品列表 -->
  <div class="spare-list">
    <!-- 查询开始 -->
    <el-card class="box-card" shadow="never">
      <div class="conditional">
        <div>
          <span class="search-title"> {{ $t("Created") }}</span>
          <el-date-picker
            size="small"
            v-model="search_date"
            type="daterange"
            value-format="yyyy-MM-dd"
            :range-separator="$t('To')"
            :start-placeholder="$t('StartTime')"
            :end-placeholder="$t('EndTime')"
            :picker-options="pickerOptionsSearch"
            class="search-bar-date"
          >
          </el-date-picker>
          <!-- <span class="search-title">{{ $t("sparePartNo") }}:</span> -->
          <el-input
            size="small"
            clearable
            :placeholder="$t('enterSparNo')"
            @keyup.enter.native="handleChangePageParams"
            v-model="pageParams.sp_no"
            class="search-bar"
          >
          </el-input>
          <!-- <span class="search-title">{{ $t("sparePartName") }}:</span> -->
          <el-input
            size="small"
            clearable
            :placeholder="$t('enterSpareName')"
            @keyup.enter.native="handleChangePageParams"
            v-model="pageParams.sp_name"
            class="search-bar"
          >
          </el-input>

          <el-button
            size="small"
            @click="handleChangePageParams"
            :loading="this.$store.state.config.button_loading"
            type="primary"
            >{{ $t("Search") }}</el-button
          >
        </div>

        <!-- 新增 -->
        <el-tooltip
          :content="$t('AdministratorActivate')"
          placement="top"
          :disabled="hasPerm('spare-parts.store') ? true : false"
        >
          <div>
            <el-button
              :disabled="!hasPerm('spare-parts.store')"
              size="small"
              @click="handlerAddClick"
              :loading="this.$store.state.config.button_loading"
              type="primary"
              >{{ $t("Add") }}</el-button
            >
          </div>
        </el-tooltip>
        <!-- <el-button
              size="small"
              @click="handleResetPageParams"
              :loading="this.$store.state.config.button_loading"
              type="info"
              >{{ $t("reset") }}</el-button
            > -->
      </div>
    </el-card>
    <!-- 表格 -->
    <el-card shadow="never" class="card-box">
      <el-table
        height="calc(100vh - 277px)"
        :data="hasPerm('spare-parts.index') ? tableData : []"
        v-loading="global_loading()"
        element-loading-text="loading..."
        border
        style="width: 100%"
        :cell-style="{ padding: '5px 0px' }"
      >
        <template slot="empty" v-if="!hasPerm('spare-parts.index')">
          <div style="font-size: 18px">
            {{ $t("AdministratorActivate") }}
          </div>
        </template>
        <!-- 序号 -->
        <el-table-column type="index" align="center" width="50" prop="id">
          <template slot="header">
            <span>{{ $t("Number") }}</span>
          </template>
        </el-table-column>
        <!-- 操作 -->
        <el-table-column width="120" align="center" :label="$t('Operation')">
          <template slot-scope="scope">
            <el-dropdown>
              <el-button size="mini" type="primary" plain class="operation">
                {{ $t("Operation") }}
                <i class="el-icon-arrow-down el-icon--right"></i>
              </el-button>
              <el-dropdown-menu slot="dropdown">
                <!-- 编辑 -->
                <el-tooltip
                  :content="$t('AdministratorActivate')"
                  placement="top"
                  :disabled="hasPerm('spare-parts.update') ? true : false"
                >
                  <div
                    :style="
                      hasPerm('spare-parts.update') ? '' : 'cursor: not-allowed'
                    "
                  >
                    <el-dropdown-item
                      @click.native="putSpare(scope.row)"
                      :disabled="!hasPerm('spare-parts.update')"
                    >
                      <el-button
                        class="operation"
                        size="small"
                        type="text"
                        :disabled="!hasPerm('spare-parts.update')"
                        >{{ $t("Edit") }}</el-button
                      >
                    </el-dropdown-item>
                  </div>
                </el-tooltip>
                <!-- 删除 -->
                <el-tooltip
                  :content="$t('AdministratorActivate')"
                  placement="top"
                  :disabled="hasPerm('spare-parts.delete') ? true : false"
                >
                  <div
                    :style="
                      hasPerm('spare-parts.delete') ? '' : 'cursor: not-allowed'
                    "
                  >
                    <el-dropdown-item
                      @click.native="delSpare(scope.row)"
                      :disabled="!hasPerm('spare-parts.delete')"
                    >
                      <el-button
                        class="operation"
                        size="small"
                        type="text"
                        :disabled="!hasPerm('spare-parts.delete')"
                        >{{ $t("Delete") }}</el-button
                      >
                    </el-dropdown-item>
                  </div>
                </el-tooltip>
              </el-dropdown-menu>
            </el-dropdown>
          </template>
        </el-table-column>
        <!-- 备品编号 -->
        <el-table-column prop="sp_no" align="center" :label="$t('sparePartNo')">
        </el-table-column>
        <el-table-column
          prop="sp_name"
          align="center"
          :label="$t('sparePartName')"
        >
        </el-table-column>
        <el-table-column
          prop="sp_brand"
          align="center"
          :label="$t('spareBrand')"
        >
        </el-table-column>
        <el-table-column
          prop="sp_model"
          align="center"
          width="130"
          :label="$t('specification')"
        >
        </el-table-column>
        <el-table-column
          prop="sp_unit_name"
          align="center"
          width="120"
          :label="$t('unit')"
        >
        </el-table-column>

        <el-table-column
          prop="created_at"
          align="center"
          :label="$t('Created')"
        >
        </el-table-column>
        <el-table-column prop="operator" align="center" :label="$t('Creator')">
        </el-table-column>
      </el-table>
      <base-pagination
        :pageParams="pageParams"
        @search="getSpare"
        class="pagination-size"
      ></base-pagination>
      <iframe frameborder="0" name="downExcel" style="display: none"></iframe>
    </el-card>
    <!-- 弹出框 -->
    <el-dialog
      :title="type == 1 ? this.$t('addSpare') : this.$t('putSpare')"
      :visible.sync="dialogVisible"
      width="700px"
    >
      <hr style="margin-top: -20px" />
      <el-form
        :label-position="labelPosition"
        label-width="130px"
        :model="form"
        :rules="formRules"
        ref="form"
      >
        <div style="display: inline-flex">
          <el-form-item :label="this.$t('sparePartNo') + ':'" prop="sp_no">
            <el-input
              v-model="form.sp_no"
              style="width: 200px"
              size="small"
            ></el-input>
          </el-form-item>
          <el-form-item :label="this.$t('sparePartName') + ':'" prop="sp_name">
            <el-input
              v-model="form.sp_name"
              style="width: 200px"
              size="small"
            ></el-input>
          </el-form-item>
        </div>
        <div style="display: inline-flex">
          <el-form-item :label="this.$t('spareBrand') + ':'" prop="sp_brand">
            <el-input
              v-model="form.sp_brand"
              style="width: 200px"
              size="small"
            ></el-input>
          </el-form-item>
          <el-form-item :label="this.$t('specification') + ':'" prop="sp_model">
            <el-input
              v-model="form.sp_model"
              style="width: 200px"
              size="small"
            ></el-input>
          </el-form-item>
        </div>
        <el-form-item :label="this.$t('unit')" prop="sp_unit">
          <el-select
            v-model="form.sp_unit"
            :placeholder="this.$t('PleaseSelect')"
            style="width: 200px"
            size="small"
          >
            <el-option
              v-for="(item, index) in sp_unit_List"
              :key="index"
              :label="item.name"
              :value="item.id"
            ></el-option>
          </el-select>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <hr />
        <el-button @click="dialogVisible = false" class="cancel">{{
          $t("Cancel")
        }}</el-button>
        <el-button type="primary" @click="sure" class="submit"
          >{{ $t("Confirm") }}
        </el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import BasePagination from "@/components/BasePagination/BasePagination";
import loading from "@/mixin/global_loading";

export default {
  name: "Spare",
  components: {
    BasePagination,
  },
  mixins: [loading],
  data() {
    return {
      tableData: [],
      search_date: ["", ""],
      dialogVisible: false,
      labelPosition: "right",
      type: 1,
      sp_unit_List: [],
      pageParams: {
        sp_no: "", //备品编号
        sp_name: "", //备品名称
        deal_type: "", //处理方式
        begin_date: "", //开始时间
        end_date: "", //结束时间
        total: 0,
        page: 1,
        per_page: 20,
      },
      form: {
        sp_no: "", //备品编号
        sp_name: "", //备品名称
        sp_brand: "", //品牌
        sp_model: "", //型号
        sp_unit: "", //单位
      },
      isDisabled: false,
    };
  },
  // 时间筛选
  watch: {
    search_date: {
      handler() {
        if (this.search_date) {
          this.pageParams.begin_date = this.search_date[0];
          this.pageParams.end_date = this.search_date[1];
        } else {
          this.pageParams.begin_date = "";
          this.pageParams.end_date = "";
        }

        this.pageParams.page = 1;
      },
    },
  },
  computed: {
    // 表单验证
    formRules() {
      return {
        sp_no: [
          // 备品编号
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        sp_name: [
          // 备品名称
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        sp_brand: [
          // 品牌
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],

        sp_model: [
          // 规格型号
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        sp_unit: [
          // 单位
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
      };
    },
    pickerOptionsSearch() {
      return {
        shortcuts: [
          {
            text: this.$t("zuiJingYiZhou"),
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
              picker.$emit("pick", [start, end]);
            },
          },
          {
            text: this.$t("zuiJingYiYue"),
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
              picker.$emit("pick", [start, end]);
            },
          },
          {
            text: this.$t("LastThreeMonths"),
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
              picker.$emit("pick", [start, end]);
            },
          },
        ],
      };
    },
  },
  mounted() {
    this.getSpareIndex();
    this.getSpare();
  },
  methods: {
    // 新增
    handlerAddClick() {
      this.type = 1;
      this.dialogVisible = true;
      this.form = {
        sp_no: "", //备品编号
        sp_name: "", //备品名称
        sp_brand: "", //品牌
        sp_model: "", //型号
        sp_unit: "", //单位
      };
    },
    getSpare() {
      // 列表
      this.$api.getSpare(this.pageParams).then((res) => {
        this.tableData = res.data.data;
        this.pageParams.total = res.data.meta.total;
        // var data = res.data.data;
        // // data.map((item) => {
        // //   if (item) {
        // //     if (item.stock_now > 1) {
        // //       this.isDisabled = true;
        // //     }
        // //   }
        // // });
      });
    },
    // 单位下拉框
    getSpareIndex() {
      this.$api.getSpareIndex().then((res) => {
        this.sp_unit_List = res.data.unit_list;
      });
    },
    // 查询
    handleChangePageParams() {
      this.pageParams.page = 1;
      this.getSpare();
    },
    // 重置
    handleResetPageParams() {
      this.pageParams.sp_no = "";
      this.pageParams.sp_name = "";
      this.pageParams.sp_brand = "";
      this.pageParams.sp_model = "";
      this.pageParams.sp_unit = "";
      this.search_date = "";
      this.getSpare();
    },
    // 确定
    sure() {
      // 新增
      if (this.type == 1) {
        this.$refs.form.validate((valid) => {
          if (valid) {
            this.$api.addSpare({ ...this.form }).then((res) => {
              if (res.code === 200) {
                this.pageParams.page = 1;
                this.getSpare();
                this.dialogVisible = false;
              }
            });
          }
        });
      } else if (this.type == 2) {
        // 修改
        this.$refs.form.validate((valid) => {
          if (valid) {
            this.$api.putSpare({ ...this.form }).then((res) => {
              if (res.code === 200) {
                this.pageParams.page = 1;
                this.getSpare();
                this.dialogVisible = false;
              }
            });
          }
        });
      }
    },
    // 修改
    putSpare(row) {
      this.type = 2;
      if (row.stock_now > 0) {
        this.$confirm(this.$t("theProductAlready") + "?", this.$t("Notice"), {
          confirmButtonText: this.$t("Confirm"),
          cancelButtonText: this.$t("Cancel"),
          type: "warning",
        });
      } else {
        this.dialogVisible = true;
        this.form = {
          id: row.id,
          sp_no: row.sp_no,
          sp_name: row.sp_name,
          sp_brand: row.sp_brand,
          sp_model: row.sp_model,
          sp_unit: row.sp_unit,
        };
      }
    },
    // 删除
    delSpare(row) {
      var id = row.id;
      // if (row.stock_now > 0) {
      //   this.$confirm(this.$t("theProductAlready") + "?", this.$t("Notice"), {
      //     confirmButtonText: this.$t("Confirm"),
      //     cancelButtonText: this.$t("Cancel"),
      //     type: "warning",
      //   });
      // } else {
      this.$confirm(this.$t("ConfirmToCancel") + "?", this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      }).then(() => {
        this.$api.delSpare(id).then((res) => {
          if (res.code === 200) {
            this.getSpare();
          }
        });
      });
      // .catch(() => {});
      // }
    },
  },
};
</script>
<style lang="less" scoped>
.spare-list {
  .box-card {
    margin-bottom: 10px;
  }
  .conditional {
    display: flex;
    justify-content: space-between;
  }
}
</style>
<style lang="less">
.spare-list {
  .conditional {
    .el-input__inner {
      border-radius: 0;
    }
  }
  .el-date-editor {
    width: 100%;
  }
  .el-select {
    width: 100%;
  }
  .el-dialog__footer {
    text-align: right;
  }
}
</style>
