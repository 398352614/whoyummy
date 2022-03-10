<template>
  <!-- 旧添加线路规划 -->
  <div class="AddLinePlanning">
    <!-- 添加线路规划 -->
    <div class="from">
      <el-form
        ref="lineForm"
        :model="form"
        :rules="rules"
        label-position="left"
        label-width="160px"
      >
        <!-- 线路名称 -->
        <el-form-item :label="$t('LineName')" prop="name">
          <el-input
            v-model="form.name"
            :placeholder="$t('pleaseEnterLineName')"
          ></el-input>
        </el-form-item>
        <!-- 仓库 -->
        <!-- <el-form-item :label="$t('Warehouse')" prop="warehouse_id">
          <el-select v-model="form.warehouse_id">
            <el-option
              :label="item.name"
              :value="item.id"
              v-for="item in warehouseList"
              :key="item.id"
            >
            </el-option>
          </el-select>
        </el-form-item> -->
        <!-- 最大取件订单量 -->
        <el-form-item :label="$t('MaxPickOrder')" prop="pickup_max_count">
          <div class="tooltip-pisition">
            <el-input v-model="form.pickup_max_count"></el-input>
            <el-tooltip
              effect="dark"
              content="表示该线路当天允许的最大取件订单量"
              placement="top"
            >
              <span class="el-icon-warning icon-question"></span>
            </el-tooltip>
            <el-button @click="addqj"> 配置货主组取件数量 </el-button>
          </div>
        </el-form-item>
        <!-- 表格 -->
        <div v-if="show">
          <el-table
 max-height="600px"            :data="qujianList"
            border
            style="width: 70%; margin: 0 0 20px 180px"
          >
            <el-table-column
              prop="name"
              label="货主组"
              header-align="center"
              align="center"
            >
            </el-table-column>
            <el-table-column
              prop="pickup_min_count"
              label="最小取件订单量"
              width="180px"
              header-align="center"
              align="center"
            >
              <template slot-scope="scope">
                <el-input-number
                  v-model="scope.row.pickup_min_count"
                  size="mini"
                  :min="0"
                ></el-input-number>
              </template>
            </el-table-column>
            <el-table-column
              prop="address"
              label="操作"
              header-align="center"
              align="center"
            >
              <template slot-scope="scope">
                <el-button
                  @click.native.prevent="removeRow(scope.$index, qujianList)"
                  type="text"
                  size="small"
                >
                  移除
                </el-button>
              </template>
            </el-table-column>
          </el-table>
        </div>

        <!-- 最大派件订单量 -->
        <el-form-item :label="$t('MaxDeliveryOrder')" prop="pie_max_count">
          <div class="tooltip-pisition">
            <el-input v-model="form.pie_max_count"></el-input>
            <el-tooltip
              effect="dark"
              content="表示该线路当天允许的最大派件订单量"
              placement="top"
            >
              <span class="el-icon-warning icon-question"></span>
            </el-tooltip>
            <el-button @click="addpj"> 配置货主组派件数量 </el-button>
          </div>
        </el-form-item>
        <!-- 表格 -->
        <div v-if="hidden">
          <el-table
 max-height="600px"            :data="paijianList"
            border
            style="width: 70%; margin: 0 0 10px 180px"
          >
            <el-table-column
              prop="name"
              label="货主组"
              header-align="center"
              align="center"
            >
            </el-table-column>
            <el-table-column
              prop="pie_min_count"
              label="最小派件订单量"
              header-align="center"
              align="center"
            >
              <template slot-scope="scope">
                <el-input-number
                  v-model="scope.row.pie_min_count"
                  :min="0"
                  size="mini"
                ></el-input-number>
              </template>
            </el-table-column>
            <el-table-column
              prop="address"
              label="操作"
              header-align="center"
              align="center"
            >
              <template slot-scope="scope">
                <el-button
                  @click.native.prevent="deleteRow(scope.$index, paijianList)"
                  type="text"
                  size="small"
                >
                  移除
                </el-button>
              </template>
            </el-table-column>
          </el-table>
        </div>

        <!-- 是否新增取件线路 -->
        <el-form-item :label="$t('addPickName')" prop="">
          <el-switch
            v-model="form.is_increment"
            active-color="#13ce66"
            inactive-color="#ccc"
            :active-value="1"
            :inactive-value="2"
          >
          </el-switch>
          <el-tooltip
            effect="dark"
            content="开关开启之后，取件或派件订单其中一个达到最大订单量后会自动创建新路线任务"
            placement="top"
          >
            <span class="el-icon-warning icon-question"></span>
          </el-tooltip>
        </el-form-item>
        <!-- 下单截止时间 -->
        <el-form-item :label="$t('orderDeadline')" prop="order_deadline">
          <el-time-picker
            arrow-control
            value-format="HH:mm:ss"
            v-model="form.order_deadline"
            :picker-options="{
              selectableRange: '00:00:00 - 23:59:59',
            }"
          >
          </el-time-picker>
          <el-tooltip
            effect="dark"
            content="表示该线路当天截止下单时间"
            placement="top"
          >
            <span class="el-icon-warning icon-question"></span>
          </el-tooltip>
        </el-form-item>
        <!-- 邮编范围 -->
        <el-form-item :error="codeErr">
          <template slot="label">
            <span class="is-required">*</span>{{ $t("Zipcoderange") }}
          </template>
          <div
            class="postcode_box"
            v-for="(item, index) in form.item_list"
            :key="index"
          >
            <div class="postcode_range">
              <el-input
                v-model="item.post_code_start"
                :placeholder="$t('StartPostCode')"
              ></el-input>
              <el-input
                class="last_input"
                v-model="item.post_code_end"
                :placeholder="$t('EndPostCode')"
              ></el-input>
            </div>
            <div class="postcode_add_button">
              <el-button v-if="index === 0" @click="createPostCode">{{
                $t("Add")
              }}</el-button>
              <el-button type="danger" v-else @click="removePostCode(index)">{{
                $t("Delete")
              }}</el-button>
            </div>
          </div>
        </el-form-item>
        <!-- 取件日期 -->
        <el-form-item :label="$t('PickupDate')" prop="work_day_list">
          <el-checkbox
            :indeterminate="isIndeterminate"
            v-model="checkAll"
            @change="handleCheckAllChange"
          >
            {{ $t("AddAll") }}
          </el-checkbox>
          <el-checkbox-group
            v-model="form.work_day_list"
            @change="handleCheckedDateChange"
          >
            <el-checkbox
              v-for="item in dateOptions"
              :label="item.value"
              :key="item.value"
            >
              {{ item.label }}
            </el-checkbox>
          </el-checkbox-group>
        </el-form-item>
        <!-- 可预约时间范围(天) -->
        <el-form-item
          :label="$t('Reservationtimerange')"
          prop="appointment_days"
        >
          <div class="tooltip-pisition">
            <el-input v-model="form.appointment_days" type="number"></el-input>
            <el-tooltip
              effect="dark"
              content="表示第三方系统可获取该时间段内可预约的所有预约时间"
              placement="top"
            >
              <span class="el-icon-warning icon-question"></span>
            </el-tooltip>
          </div>
        </el-form-item>
        <!-- 是否按优化顺序派送 -->
        <el-form-item :label="$t('是否按优化顺序派送')">
          <el-switch
            v-model="form.can_skip_batch"
            :active-value="1"
            :inactive-value="2"
          >
          </el-switch>
        </el-form-item>
        <!-- 是否启用 -->
        <el-form-item :label="$t('是否启用')">
          <el-switch
            v-model="form.status"
            :active-value="1"
            :inactive-value="2"
          >
          </el-switch>
        </el-form-item>
        <!-- 路线备注 -->
        <el-form-item :label="$t('LineRemark')">
          <el-input v-model="form.remark" type="textarea"></el-input>
        </el-form-item>
        <!-- 创建按钮 -->
        <el-form-item>
          <el-row>
            <el-col :span="4" :offset="20">
              <el-button type="primary" @click="onSubmit">{{
                $t("Create")
              }}</el-button>
            </el-col>
          </el-row>
        </el-form-item>
      </el-form>
    </div>
    <!-- 表格表单 -->
    <div>
      <!-- 取件表单 -->
      <el-dialog
        :title="
          typeDialog == 1 ? '请选择需要配置的货主组' : '请选择需要配置的货主组'
        "
        :visible.sync="dialogTableVisible"
      >
        <el-table
 max-height="600px"          ref="multipleTable"
          :data="qjlist"
          border
          style="width: 100%"
          @selection-change="handleSelectionChange"
        >
          <el-table-column type="selection" width="55"> </el-table-column>
          <el-table-column prop="name" label="货主组"> </el-table-column>
        </el-table>
        <!-- 分页 -->
        <base-pagination
          :pageParams="pageParams"
          @search="getList"
        ></base-pagination>
        <div slot="footer" class="dialog-footer">
          <el-button type="primary" @click="dialogTableVisible = false"
            >取消</el-button
          >
          <el-button type="primary" @click="qjsubmit">提交</el-button>
        </div>
      </el-dialog>
      <!-- 派件表单 -->
      <!-- <el-dialog title="请选择需要配置的货主组" :visible.sync="dialogVisible">
        <el-table
 max-height="600px"          ref="multipleList"
          :data="pjlist"
          border
          style="width: 100%"
          @selection-change="handleSelectionChange"
        >
          <el-table-column type="selection" width="55"> </el-table-column>
          <el-table-column prop="name" label="货主组"> </el-table-column>
        </el-table> -->
      <!-- 分页 -->
      <!-- <base-pagination
          :pageParams="pageParams"
          @search="getList"
        ></base-pagination>
        <div slot="footer" class="dialog-footer">
          <el-button type="primary" @click="dialogVisible = false"
            >取消</el-button
          >
          <el-button type="primary" @click="pjsubmit">提交</el-button>
        </div>
      </el-dialog> -->
    </div>
  </div>
</template>

<script>
// 分页
import BasePagination from "@/components/BasePagination/BasePagination";

export default {
  name: "AddLinePlanning",
  components: {
    BasePagination,
  },
  data() {
    return {
      typeDialog: 1, //判断弹出框
      dialogTableVisible: false,
      dialogVisible: false,
      checkAll: false,
      checkedCities: [],
      cities: [],
      isIndeterminate: true,
      show: false, //表格的显示隐藏
      hidden: false, //表格的显示隐藏
      countryForm: {
        name: "",
      },
      pageParams: {
        //分页
        page: 1,
        per_page: 10,
        total: 0,
      },
      qujianList: [], //取件列表
      paijianList: [], //派件列表
      selected: [], //选中项

      form: {
        name: "", // 线路名称
        warehouse_id: "", // 仓库 id
        pickup_max_count: "", // 最大取件订单量
        pie_max_count: "", // 最大派件订单量
        is_increment: 2, // 是否新增取件线路1-是2-否
        order_deadline: "23:59:59", // 下单截止时间
        appointment_days: 30, // 可预约天数
        work_day_list: [], // 取件日期

        // 邮件范围列表
        item_list: [
          {
            post_code_start: "",
            post_code_end: "",
          },
        ],

        status: 2,
        remark: "", // 线路备注,
        can_skip_batch: 1, // 是否优化
      },
      old_item_list: "", // 旧的邮编json
      add_item: {
        //新增
        post_code_start: "",
        post_code_end: "",
      },
      codeErr: null,
      warehouseList: [], //仓库
      qjlist: [],
      // pjlist: [],
    };
  },
  created() {
    this.getWarehouse(); // 仓库数据
    this.getList();
    // 编辑
    if (this.$route.query.id) {
      this.handlerEdit(this.$route.query.id);
    }
  },
  computed: {
    countryFormRules() {
      return {
        name: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
      };
    },
    rules() {
      return {
        name: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        warehouse_id: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "change",
          },
        ],
        pickup_max_count: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        pie_max_count: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        work_day_list: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        // 邮件范围列表
        item_list: [
          {
            required: false,
            message: this.$t("Required"),
            trigger: "change",
          },
        ],
        order_deadline: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "change",
          },
        ],
        appointment_days: [
          {
            validator: (rule, value, callback) => {
              if (!value) {
                return callback(new Error(this.$t("Required")));
              } else if (value > 30) {
                callback(new Error(this.$t("MustLessThanDays") + "!"));
              } else {
                callback(new Error(this.$t("Required")));
              }
            },
            trigger: "blur",
          },
        ],
      };
    },
    dateOptions() {
      return [
        {
          value: 0,
          label: this.$t("Sunday"),
        },
        {
          value: 1,
          label: this.$t("Monday"),
        },
        {
          value: 2,
          label: this.$t("Tuesday"),
        },
        {
          value: 3,
          label: this.$t("Wednesday"),
        },
        {
          value: 4,
          label: this.$t("Thursday"),
        },
        {
          value: 5,
          label: this.$t("Friday"),
        },
        {
          value: 6,
          label: this.$t("Saturday"),
        },
      ];
    },
  },
  methods: {
    // 提交
    qjsubmit() {
      //取件
      this.dialogTableVisible = false;
      this.show = true;
      var selected = this.selected;
      this.qujianList = selected;

      // console.log(this.qujianList);
      // var arr = [];
      // selected.map((item, index) => {
      //   arr.push(item.id);
      // });
      // // var str = arr.join("/");
      // console.log(str);
    },
    // pjsubmit() {
    //   //派件
    //   this.dialogVisible = false;
    //   this.hidden = true;
    //   var selected = this.selected;
    //   this.paijianList = selected;
    // },
    // 表格选中项
    handleSelectionChange(val) {
      this.selected = val;
    },
    // 获取表单数据 分页
    getList() {
      this.$api.getMerchantsGroup(this.pageParams).then((res) => {
        this.qjlist = res.data.data;
        // this.pjlist = res.data.data;
        this.pageParams.total = res.data.meta.total;
      });
    },
    // // 取消
    // quxiao() {
    //   this.dialogTableVisible = false;
    // },

    // 订单量取件弹出框
    addqj() {
      this.typeDialog = 1;
      this.dialogTableVisible = true;
      // this.$nextTick(function () {
      //   this.qujianList.forEach((data) => {
      //     this.qjlist.forEach((item) => {
      //       if (data.id == item.id) {
      //         this.$refs.multipleTable.toggleRowSelection(item);
      //       }
      //     });
      //   });
      // });
    },
    // 订单量派件弹出框
    addpj() {
      this.typeDialog = 2;
      this.dialogTableVisible = true;
      // this.$nextTick(function () {
      //   this.paijianList.forEach((data) => {
      //     this.pjlist.forEach((item) => {
      //       if (data.id == item.id) {
      //         this.$refs.multipleTable(item);
      //       }
      //     });
      //   });
      // });
    },
    // 取件日期 全选
    handleCheckAllChange(val) {
      const temp = [];
      this.dateOptions.forEach((item) => {
        temp.push(item.value);
      });

      this.form.work_day_list = val ? temp : [];
      this.isIndeterminate = false;
    },
    // 取件日期 单个选
    handleCheckedDateChange(value) {
      const checkedCount = value.length;
      this.checkAll = checkedCount === this.dateOptions.length;
      this.isIndeterminate =
        checkedCount > 0 && checkedCount < this.dateOptions.length;
    },
    // 编辑数据请求
    handlerEdit(id) {
      this.isEdit = true;
      this.$api.getLineItem(id).then((res) => {
        var pinkuparr = [];
        var piekuparr = [];
        res.data.merchant_group_count_list.forEach((item) => {
          if (item.pickup_min_count != 0) {
            this.show = true;
            var pinkuparrobj = {
              id: item.merchant_group_id,
              name: item.merchant_group_name,
              pickup_min_count: item.pickup_min_count,
              // pie_min_count: item.pie_min_count,
            };
            pinkuparr.push(pinkuparrobj);
          }
          if (item.pie_min_count != 0) {
            this.hidden = true;
            var piekuparrobj = {
              id: item.merchant_group_id,
              name: item.merchant_group_name,
              // pickup_min_count: item.pickup_min_count,
              pie_min_count: item.pie_min_count,
            };
            piekuparr.push(piekuparrobj);
          }
        });
        this.qujianList = pinkuparr;
        this.paijianList = piekuparr;
        this.form.name = res.data.name;
        this.form.warehouse_id = res.data.warehouse_id;
        this.form.pickup_max_count = res.data.pickup_max_count;
        this.form.pie_max_count = res.data.pie_max_count;
        this.form.appointment_days = res.data.appointment_days;
        this.form.order_deadline = res.data.order_deadline;
        this.form.is_increment = res.data.is_increment;
        this.form.status = res.data.status;
        this.form.remark = res.data.remark;
        this.form.can_skip_batch = res.data.can_skip_batch;
        const temp = [...res.data.work_day_list.split(",")];
        temp.forEach((item) => {
          this.form.work_day_list.push(+item);
        });
        this.form.item_list = res.data.line_range;
        let line_range = res.data.line_range.map((el) => {
          return {
            post_code_start: el.post_code_start + "",
            post_code_end: el.post_code_end + "",
          };
        });
        this.old_item_list = JSON.stringify(line_range); // 用于检测变化
      });
    },

    // 移除
    // 配置货主组派件数量
    deleteRow(index, rows) {
      rows.splice(index, 1);
    },
    // 配置货主组取件数量
    removeRow(index, rows) {
      rows.splice(index, 1);
    },
    // 新增邮编范围
    createPostCode() {
      // eslint-disable-next-line prefer-const
      let tempItem = { ...this.add_item };
      this.form.item_list.push(tempItem);
    },
    // 移除邮编范围
    removePostCode(index) {
      for (let i = 0, l = this.form.item_list.length; i < l; i += 1) {
        if (index === i) {
          this.form.item_list.splice(index, 1);
        }
      }
    },
    // 仓库数据
    getWarehouse() {
      this.$api.getWarehousePublic().then((res) => {
        this.warehouseList = res.data.data;
      });
    },
    // 邮编范围
    forDate() {
      this.form.item_list.forEach((el) => {
        el.post_code_start = el.post_code_start + "";
        el.post_code_end = el.post_code_end + "";
        el.post_code_start = el.post_code_start.trim();
        el.post_code_end = el.post_code_end.trim();
      });
      // 取件
      var pinkuparr = [];
      this.qujianList.forEach((item) => {
        var obj = {
          merchant_group_id: item.id,
          pickup_min_count: item.pickup_min_count,
          pie_min_count: 0,
          type: "pickup",
        };
        pinkuparr.push(obj);
      });
      // 派件
      var piekuparr = [];
      this.paijianList.forEach((item) => {
        var obj = {
          merchant_group_id: item.id,
          pickup_min_count: 0,
          pie_min_count: item.pie_min_count,
          type: "pie",
        };
        piekuparr.push(obj);
      });
      //合并
      var allarr = pinkuparr.concat(piekuparr);

      //去重
      var oldarr = [];
      allarr.map((item) => {
        var dataobj = {
          merchant_group_id: item.merchant_group_id,
          data: [],
        };
        var obj = {
          pickup_min_count: item.pickup_min_count,
          pie_min_count: item.pie_min_count,
          type: item.type,
        };
        dataobj.data.push(obj);
        oldarr.push(dataobj);
      });
      var newarr = [];
      var obj = {};
      oldarr.map((item, index) => {
        if (!obj[item.merchant_group_id]) {
          obj[item.merchant_group_id] = true;
          newarr.push(item);
        } else {
          newarr.map((newitem) => {
            if (oldarr[index].merchant_group_id == newitem.merchant_group_id) {
              newitem.data = newitem.data.concat(oldarr[index].data);
            }
          });
        }
      });

      //分配派件和取件
      var resarr = [];
      newarr.map((item) => {
        if (item.data.length == 2) {
          var obj = {
            merchant_group_id: item.merchant_group_id,
            pickup_min_count: item.data[0].pickup_min_count,
            pie_min_count: item.data[1].pie_min_count,
          };
        } else {
          var type = item.data[0].type;
          if (type == "pickup") {
            var obj = {
              merchant_group_id: item.merchant_group_id,
              pickup_min_count: item.data[0].pickup_min_count,
              pie_min_count: 0,
            };
          } else if (type == "pie") {
            var obj = {
              merchant_group_id: item.merchant_group_id,
              pickup_min_count: 0,
              pie_min_count: item.data[0].pie_min_count,
            };
          }
        }
        resarr.push(obj);
      });
      console.log(resarr);
      return {
        ...this.form,
        work_day_list: this.form.work_day_list.join(","), //取件日期
        item_list: JSON.stringify(this.form.item_list),
        merchant_group_count_list: JSON.stringify(resarr), //最小量的表格
      };
    },
    // 检查邮编
    checkCode() {
      let passInspection = true;
      for (let i = 0; i < this.form.item_list.length; i++) {
        if (
          !(
            this.form.item_list[i].post_code_start &&
            this.form.item_list[i].post_code_end
          )
        ) {
          this.codeErr = this.$t("Required");
          passInspection = false;
          break;
        }
      }
      if (!passInspection) {
        return false;
      } else {
        this.codeErr = null;
        return true;
      }
    },
    // 立即创建
    onSubmit() {
      this.$refs.lineForm.validate((valid) => {
        if (!this.checkCode()) return;
        if (valid) {
          if (this.isEdit) {
            let data = this.forDate();
            if (this.old_item_list == data.item_list) {
              this.$api.editLineItem(this.$route.query.id, data).then((res) => {
                if (!res) return;
                this.$router.push({
                  name: "LinePlanningPostCode",
                });
              });
            } else {
              this.$confirm(
                "您修改的邮编包含高级配置，修改后高级配置将恢复默认，是否确定修改?",
                "提示",
                {
                  confirmButtonText: "确定",
                  cancelButtonText: "取消",
                  type: "warning",
                }
              )
                .then(() => {
                  this.$api
                    .editLineItem(this.$route.query.id, data)
                    .then((res) => {
                      if (!res) return;
                      this.$router.push({
                        name: "LinePlanningPostCode",
                      });
                    });
                })
                .catch(() => {});
            }
            return;
          }

          this.$api
            .addPostCode(this.forDate())

            .then((res) => {
              if (!res) return;
              this.$router.push({
                name: "LinePlanningPostCode",
              });
            });
        }
      });
    },
  },
};
</script>

<style lang="less" scoped >
@import "../../../less/public_variable.less";

.AddLinePlanning {
  background-color: #fff;
  padding-top: 20px;
  padding-left: 40px;
  min-height: 600px;
  display: flex;
  justify-content: center;
  .from {
    position: relative;
    top: 0;
    right: 80px;
    width: 660px;
    .el-select {
      width: 100%;
    }
    .tooltip-pisition {
      display: flex;
      align-items: center;
      position: relative;
      .el-tooltip {
        margin-left: 5px;
        position: absolute;
        left: 100%;
      }
    }
  }
  .postcode_box {
    position: relative;
    margin-top: 10px;
    .postcode_range {
      display: flex;
      .last_input {
        margin-left: 20px;
      }
    }
    .postcode_add_button {
      position: absolute;
      top: 0;
      right: -80px;
      .el-button--default {
        color: @ThemeColor;
      }
    }
  }
  .button_box {
    text-align: center;
  }
  .is-required {
    color: #f56c6c;
    margin-right: 4px;
  }
  .el-icon-warning {
    color: #efb336;
  }
}
</style>
