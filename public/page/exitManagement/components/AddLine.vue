<template>
  <div class="AddLinePlanning1">
    <!-- 添加线路规划 -->
    <div class="form">
      <el-form
        ref="lineForm"
        :model="form"
        :rules="rules"
        label-position="left"
        label-width="200px"
      >
        <!-- 线路名称 -->
        <el-form-item :label="$t('LineName')" prop="name">
          <el-input
            v-model="form.name"
            :placeholder="$t('pleaseEnterLineName')"
            style="width: 20%"
            size="small"
          ></el-input>
        </el-form-item>
        <!-- 服务范围 -->
        <el-form-item :label="$t('ScopeOfServices')" prop="item_list">
          <div class="add-row">
            <el-button
              @click="addRow"
              class="add-but"
              size="small"
              type="primary"
              plain
              >{{ $t("Add") }}</el-button
            >
          </div>
          <!-- 表格 -->
          <el-table
            max-height="600px"
            :data="postCodeList"
            style="width: 90%"
            border
          >
            <el-table-column
              type="index"
              align="center"
              label="#"
            ></el-table-column>
            <!-- 国家 -->
            <el-table-column :label="$t('Country')" align="center" width="200">
              <template slot-scope="scope">
                <el-select
                  v-model="scope.row.country"
                  :placeholder="$t('pleaseSelectCountry')"
                  size="small"
                >
                  <el-option
                    v-for="item in address"
                    :key="item.short"
                    :label="item.name"
                    :value="item.short"
                  >
                  </el-option>
                </el-select>
                <!-- <el-input v-model="scope.row.country" size="small"></el-input> -->
              </template>
            </el-table-column>
            <!-- 开始邮编 -->
            <el-table-column :label="$t('StartPostCode')" align="center">
              <template slot-scope="scope">
                <el-input
                  v-model="scope.row.post_code_start"
                  size="small"
                ></el-input>
              </template>
            </el-table-column>
            <!-- 结束邮编 -->
            <el-table-column :label="$t('EndPostCode')" align="center">
              <template slot-scope="scope">
                <el-input
                  v-model="scope.row.post_code_end"
                  size="small"
                ></el-input>
              </template>
            </el-table-column>
            <!-- 操作 -->
            <el-table-column
              :label="$t('Operation')"
              align="center"
              width="150"
            >
              <template slot-scope="scope">
                <el-button
                  @click.native.prevent="deleteRow(scope.$index, postCodeList)"
                  type="danger"
                  size="small"
                  >{{ $t("Remove") }}
                </el-button>
              </template>
            </el-table-column>
          </el-table>
          <div>
            <div class="add-row">
              <el-button
                @click="addRowPrecise"
                class="add-but"
                size="small"
                type="primary"
                plain
                >{{ $t("Add") }}</el-button
              >
            </div>
            <!-- 表格 -->
            <el-table
              max-height="600px"
              :data="preciseList"
              style="width: 90%"
              border
              :header-cell-style="{ 'text-align': 'center' }"
            >
              <el-table-column
                type="index"
                align="center"
                label="#"
              ></el-table-column>
              <!-- 国家 -->
              <el-table-column
                :label="$t('Country')"
                align="center"
                width="200"
              >
                <template slot-scope="scope">
                  <el-select
                    v-model="scope.row.country"
                    :placeholder="$t('pleaseSelectCountry')"
                    size="small"
                    :change="changeType(scope.row.country)"
                  >
                    <el-option
                      v-for="item in addressPreciseList"
                      :key="item.short"
                      :label="item.name"
                      :value="item.short"
                      :disabled="item.disabled"
                    >
                    </el-option>
                  </el-select>
                  <!-- <el-input v-model="scope.row.country" size="small"></el-input> -->
                </template>
              </el-table-column>
              <!-- 精准邮编 -->
              <el-table-column
                :label="$t('PreciseZipCode')"
                align="left
              "
              >
                <template slot-scope="scope">
                  <el-tag
                    :key="tag"
                    v-for="tag in scope.row.dynamicTags"
                    closable
                    :disable-transitions="false"
                    @close="handleClose(tag, scope.$index)"
                  >
                    {{ tag }}
                  </el-tag>
                  <el-input
                    class="input-new-tag"
                    v-show="scope.row.ishow"
                    v-model="scope.row.post_code_start"
                    :ref="'saveTagInput' + scope.$index"
                    size="small"
                    @keyup.enter.native="
                      handleInputConfirm(scope.$index, scope.row)
                    "
                    @blur="handleInputConfirm(scope.$index, scope.row)"
                  >
                  </el-input>
                  <el-button
                    v-show="!scope.row.ishow"
                    class="button-new-tag"
                    size="small"
                    @click="showInput(scope.$index, scope.row)"
                    >{{ $t("Addition") }} +</el-button
                  >
                  <!-- <el-input
                  v-model="scope.row.post_code_start"
                  size="small"
                ></el-input> -->
                </template>
              </el-table-column>
              <!-- 结束邮编 -->
              <!-- <el-table-column :label="$t('EndPostCode')" align="center">
              <template slot-scope="scope">
                <el-input
                  v-model="scope.row.post_code_end"
                  size="small"
                ></el-input>
              </template>
            </el-table-column> -->
              <!-- 操作 -->
              <el-table-column
                :label="$t('Operation')"
                align="center"
                width="150"
              >
                <template slot-scope="scope">
                  <el-button
                    @click.native.prevent="
                      deleteRowPreciseList(scope.$index, preciseList)
                    "
                    type="danger"
                    size="small"
                    >{{ $t("Remove") }}
                  </el-button>
                </template>
              </el-table-column>
            </el-table>
          </div>
        </el-form-item>
        <!-- 日最大承接量 -->
        <el-form-item :label="$t('MaxCapacity')" prop="pickup_max_count">
          <!-- 最大取件订单量 -->
          <div class="form-box">
            <el-row :gutter="20">
              <el-col :span="12">
                <span style="color: #606265; margin-right: 20px"
                  >{{ $t("MaxPickOrder") }}
                </span>
                <el-input
                  v-model="form.pickup_max_count"
                  style="width: 200px"
                  size="small"
                ></el-input>
              </el-col>
              <el-col :span="12" style="text-align: right">
                <el-button @click="addqj" size="small" type="primary" plain>
                  {{ $t("Special") }}
                </el-button>
              </el-col>
            </el-row>
            <!-- 取件表格-->
            <div>
              <el-table
                max-height="600px"
                :data="qujianList"
                border
                style="width: 100%"
                class="form-table"
              >
                <el-table-column
                  type="index"
                  align="center"
                  label="#"
                  width="50"
                ></el-table-column>
                <el-table-column
                  prop="name"
                  :label="this.$t('MerchantGroup')"
                  header-align="center"
                  align="center"
                >
                </el-table-column>
                <el-table-column
                  prop="pickup_min_count"
                  :label="this.$t('minimumQuantity')"
                  header-align="center"
                  align="center"
                >
                  <template slot-scope="scope">
                    <el-input-number
                      v-model="scope.row.pickup_min_count"
                      :min="1"
                      size="small"
                      style="width: 250px"
                    ></el-input-number>
                  </template>
                </el-table-column>
                <el-table-column
                  prop="address"
                  :label="this.$t('Operation')"
                  header-align="center"
                  align="center"
                  width="150"
                >
                  <template slot-scope="scope">
                    <el-button
                      @click.native.prevent="
                        removeRow(scope.$index, qujianList)
                      "
                      type="danger"
                      size="small"
                    >
                      {{ $t("Remove") }}
                    </el-button>
                  </template>
                </el-table-column>
              </el-table>
            </div>
          </div>
          <!-- 最大派件订单量 -->
          <div class="form-box">
            <el-row :gutter="20">
              <el-col :span="12">
                <span style="color: #606265; margin-right: 20px"
                  >{{ $t("MaxDeliveryOrder") }}
                </span>
                <el-input
                  v-model="form.pie_max_count"
                  style="width: 200px"
                  size="small"
                ></el-input>
              </el-col>
              <el-col :span="12" style="text-align: right">
                <el-button @click="addpj" size="small" type="primary" plain>
                  {{ $t("Special") }}
                </el-button>
              </el-col>
            </el-row>
            <!-- 派件表格-->
            <div>
              <el-table
                max-height="600px"
                :data="paijianList"
                border
                style="width: 100%"
                class="form-table"
              >
                <el-table-column
                  type="index"
                  align="center"
                  label="#"
                ></el-table-column>
                <el-table-column
                  prop="name"
                  :label="this.$t('MerchantGroup')"
                  header-align="center"
                  align="center"
                >
                </el-table-column>
                <el-table-column
                  prop="pie_min_count"
                  :label="this.$t('MinimumPieQuantity')"
                  header-align="center"
                  align="center"
                >
                  <template slot-scope="scope">
                    <el-input-number
                      v-model="scope.row.pie_min_count"
                      :min="1"
                      size="small"
                      style="width: 250px"
                    ></el-input-number>
                  </template>
                </el-table-column>
                <el-table-column
                  prop="address"
                  :label="this.$t('Operation')"
                  header-align="center"
                  align="center"
                  width="150"
                >
                  <template slot-scope="scope">
                    <el-button
                      @click.native.prevent="
                        deleteRow(scope.$index, paijianList)
                      "
                      type="danger"
                      size="small"
                    >
                      {{ $t("Remove") }}
                    </el-button>
                  </template>
                </el-table-column>
              </el-table>
            </div>
          </div>
          <!-- 是否新增取件线路 -->
          <div class="form-box">
            <el-switch
              v-model="form.is_increment"
              active-color="#13ce66"
              inactive-color="#ccc"
              :active-value="1"
              :inactive-value="2"
            >
            </el-switch>
            <span style="color: #606265; margin-left: 20px">{{
              $t("CreateOverloaded")
            }}</span>
            <div style="color: #acacac">
              {{ $t("CreateOverloadedState") }}
            </div>
            <!-- <el-tooltip
            effect="dark"
            content="开关开启之后，取件或派件订单其中一个达到最大订单量后会自动创建新路线任务"
            placement="top"
          >
            <span class="el-icon-warning icon-question"></span>
          </el-tooltip> -->
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
              v-for="item in weekList"
              :label="item.id"
              :key="item.id"
            >
              {{ item.name }}
            </el-checkbox>
          </el-checkbox-group>
        </el-form-item>
        <!-- 下单截止时间 -->
        <el-form-item :label="$t('orderDeadline')" prop="order_deadline">
          <el-time-picker
            arrow-control
            value-format="HH:mm:ss"
            v-model="form.order_deadline"
            size="small"
            :picker-options="{
              selectableRange: '00:00:00 - 23:59:59',
            }"
            style="width: 300px"
          >
          </el-time-picker>
          <span style="color: #acacac; margin-left: 20px">{{
            $t("outOfTime")
          }}</span>

          <!-- <el-tooltip
            effect="dark"
            content="表示该线路当天截止下单时间"
            placement="top"
          >
            <span class="el-icon-warning icon-question"></span>
          </el-tooltip> -->
        </el-form-item>
        <!-- 可预约时间范围(天) -->
        <el-form-item
          :label="$t('Reservationtimerange')"
          prop="appointment_days"
        >
          <div class="tooltip-pisition">
            <el-input
              v-model="form.appointment_days"
              type="number"
              style="width: 300px"
              size="small"
            ></el-input>
            <span style="color: #acacac; margin-left: 20px">{{
              $t("daysRecommended")
            }}</span>
            <!-- <el-tooltip
              effect="dark"
              content="表示第三方系统可获取该时间段内可预约的所有预约时间"
              placement="top"
            >
              <span class="el-icon-warning icon-question"></span>
            </el-tooltip> -->
          </div>
        </el-form-item>
        <!-- 是否按优化顺序派送 -->
        <el-form-item :label="$t('')">
          <el-switch
            v-model="form.can_skip_batch"
            :active-value="1"
            :inactive-value="2"
          >
          </el-switch>
          <span>{{ $t("prioritySequence") }}</span>
          <div style="color: #acacac">
            {{ $t("afterBeing") }}
          </div>
        </el-form-item>
        <!-- 是否启用 -->
        <el-form-item :label="$t('')">
          <el-switch
            v-model="form.status"
            :active-value="1"
            :inactive-value="2"
          >
          </el-switch>
          <span>{{ $t("whetherToEnable") }}</span>
        </el-form-item>
        <!-- 备注 -->
        <el-form-item :label="$t('remark')">
          <el-input
            v-model="form.remark"
            type="textarea"
            :rows="5"
            style="width: 700px"
          ></el-input>
        </el-form-item>
        <!-- 按钮 -->
        <el-form-item>
          <el-row>
            <el-col :span="4">
              <el-button @click="onSubmit" class="form-but">{{
                $t("Save")
              }}</el-button>
            </el-col>
          </el-row>
        </el-form-item>
      </el-form>
    </div>
    <!-- 取件表单 -->
    <el-dialog
      :title="this.$t('pleaseSelecConfigured')"
      :visible.sync="dialogTableVisible"
    >
      <el-table
        max-height="600px"
        ref="multipleTable"
        :data="qjlist"
        border
        style="width: 100%"
        @selection-change="handleSelectionChange"
      >
        <el-table-column type="selection" width="55"> </el-table-column>
        <el-table-column prop="name" :label="this.$t('MerchantGroup')">
        </el-table-column>
      </el-table>
      <!-- 分页 -->
      <base-pagination
        :pageParams="pageParams"
        @search="getList"
      ></base-pagination>
      <div slot="footer" class="dialog-footer">
        <el-button
          type="primary"
          @click="dialogTableVisible = false"
          class="cancel"
          >{{ $t("Cancel") }}
        </el-button>
        <el-button type="primary" @click="qjsubmit" class="submit">{{
          $t("Submit")
        }}</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
// 分页
import BasePagination from "@/components/BasePagination/BasePagination";

export default {
  components: {
    BasePagination,
  },
  data() {
    return {
      disabled: false,
      inputValue: "",
      inputVisible: false,
      postCodeList: [], //邮编列表
      preciseList: [], //精准邮编
      address: [], //国家下拉
      addressPreciseList: [], //精准国家下拉
      typeDialog: 1, //判断弹出框
      dialogTableVisible: false, //弹出框
      isIndeterminate: true, //全选
      checkAll: false,
      qujianList: [], //取件列表
      paijianList: [], //派件列表
      qjlist: [], //表单列表
      selected: [], //选中项
      weekList: [], //取件日期
      hasSelectList: [],
      old_item_list: "", // 旧的邮编json
      form: {
        name: "", //线路名称
        pickup_max_count: "", //最大取件量
        pie_max_count: "", //最大派件订单量
        order_deadline: "23:59:59", // 下单截止时间
        appointment_days: 30, // 可预约天数
        can_skip_batch: 1, // 是否优化
        status: 2, //是否启用
        remark: "", //备注
        item_list: [], // 邮编范围列表
        work_day_list: [], // 取件日期
      },
      //   分页
      pageParams: {
        page: 1,
        per_page: 20,
        total: 0,
      },
      rules: {
        // 线路名称
        name: [
          { required: true, message: this.$t("Required"), trigger: "change" },
        ],
        // 服务范围
        item_list: [
          { required: true, message: this.$t("Required"), trigger: "change" },
        ],
        // 最大承接量
        pickup_max_count: [
          { required: true, message: this.$t("Required"), trigger: "change" },
        ],
        // 取件日期
        work_day_list: [
          { required: true, message: this.$t("Required"), trigger: "change" },
        ],
        // 下单截止时间
        order_deadline: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "change",
          },
        ],
        // 可预约天数
        appointment_days: [
          {
            validator: (rule, value, callback) => {
              if (!value) {
                return callback(new Error(this.$t("Required")));
              } else if (value > 30) {
                callback(new Error(this.$t("MustLessThanDays") + "!"));
              } else {
                callback();
              }
            },
            trigger: "blur",
          },
        ],
      },
    };
  },
  created() {
    this.getList(); //获取表单数据
    this.getDictionary(); //数据字典
    this.getAddedCountry();
    // 编辑
    if (this.$route.query.id) {
      this.handlerEdit(this.$route.query.id);
    }
  },
  methods: {
    changeType(country) {
      this.addressPreciseList.map((item) => {
        item.disabled = false;
        for (let i = 0; i < this.preciseList.length; i++) {
          if (this.preciseList[i].country === item.short) {
            item.disabled = true;
          }
        }
      });
    },
    // 精准邮编删除
    handleClose(tag, index) {
      let preciseList = this.preciseList;
      preciseList.forEach((item, i) => {
        if (i === index) {
          item.dynamicTags.splice(item.dynamicTags.indexOf(tag), 1);
        }
      });
    },
    // 精准邮编input
    showInput(index, row) {
      var that = this;
      this.$set(row, "ishow", true);
      this.$nextTick((_) => {
        that.$refs["saveTagInput" + index].$refs.input.focus();
      });
    },
    // 精准邮编添加
    handleInputConfirm(tableindex, row) {
      let preciseList = this.preciseList;
      preciseList.forEach((item, index) => {
        if (tableindex == index) {
          if (item.post_code_start) {
            item.dynamicTags.push(item.post_code_start);
          }
          item.post_code_start = "";
        }
      });
      this.$set(row, "ishow", false);
    },
    getAddedCountry() {
      // 国家列表
      this.$api.getAddedCountry().then((res) => {
        this.address = res.data;
        this.addressPreciseList = res.data;
        // 国家
        // let countryObj = { cn: res.data[0].name, en: res.data[0].short };
        // localStorage.setItem("country", JSON.stringify(countryObj));
      });
    },
    //   数据字典
    getDictionary() {
      this.$api.getDictionary().then((res) => {
        this.weekList = res.data.weekList;
      });
    },
    // 添加邮编行
    addRow() {
      this.form.item_list.push(1); //去除验证
      this.postCodeList.push({
        country: "",
        post_code_start: "", //开始邮编
        post_code_end: "", //结束邮编
        type: 1,
      });
    },
    // 精准邮编
    addRowPrecise() {
      this.form.item_list.push(1); //去除验证
      // if (this.preciseList.length < this.addressPreciseList.length) {
      this.preciseList.push({
        country: "",
        post_code_start: "", //开始邮编
        post_code_end: "", //结束邮编
        dynamicTags: [],
        isshow: false,
        type: 2,
      });
      // }ƒ
    },
    // 订单量取件弹出框
    addqj() {
      this.typeDialog = 1;
      this.dialogTableVisible = true;
      // this.$nextTick(() => {
      //   this.qjlist.forEach((row) => {
      //     this.hasSelectList.map((el) => {
      //       if (row.id == el.id) {
      //         this.$refs.multipleTable.toggleRowSelection(row, true);
      //       }
      //     });
      //   });
      // });
    },
    // 删除取件行
    removeRow(index, rows) {
      rows.splice(index, 1);
    },
    // 订单量派件弹出框
    addpj() {
      this.typeDialog = 2;
      this.dialogTableVisible = true;
    },
    // 精准邮编删除
    deleteRowPreciseList(index, rows) {
      let addressPreciseList = this.addressPreciseList;
      addressPreciseList.forEach((item) => {
        if (item.short == rows[index].country) {
          item.disabled = false;
        }
      });
      rows.splice(index, 1);
    },
    // 删除派件行
    deleteRow(index, rows) {
      rows.splice(index, 1);
    },
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
    // 取件日期 全选
    handleCheckAllChange(val) {
      const temp = [];
      this.weekList.forEach((item) => {
        temp.push(item.id);
      });
      this.form.work_day_list = val ? temp : [];
      this.isIndeterminate = false;
    },
    // 取件日期 单个选
    handleCheckedDateChange(value) {
      const checkedCount = value.length;
      this.checkAll = checkedCount === this.weekList.length;
      this.isIndeterminate =
        checkedCount > 0 && checkedCount < this.weekList.length;
    },
    // 提交
    qjsubmit() {
      //取件
      this.dialogTableVisible = false;
      var selected = this.selected;
      if (this.typeDialog == 1) {
        this.qujianList = selected;
      } else {
        this.paijianList = selected;
      }
    },
    // 邮编范围
    forDate() {
      let list = [];
      let preciseList = this.preciseList;
      preciseList.map((item) => {
        let dynamicTags = item.dynamicTags;
        dynamicTags.map((el) => {
          list.push({
            country: item.country,
            post_code_start: el,
            post_code_end: "", //结束邮编
            type: 2,
          });
        });
        // let start = item.dynamicTags.join(",");
        // console.log("post_code_start");
      });
      this.postCodeList.forEach((el) => {
        el.post_code_start = el.post_code_start + "";
        el.post_code_end = el.post_code_end + "";
        el.post_code_start = el.post_code_start.trim();
        el.post_code_end = el.post_code_end.trim();
      });
      this.form.item_list = list.concat(this.postCodeList);

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
      // console.log(resarr);
      console.log(this.form.item_list);
      return {
        ...this.form,
        work_day_list: this.form.work_day_list.join(","), //取件日期
        item_list: JSON.stringify(this.form.item_list),
        // item_list: this.form.item_list,
        merchant_group_count_list: JSON.stringify(resarr), //最小量的表格
      };
    },
    // 编辑数据请求
    handlerEdit(id) {
      this.isEdit = true;
      this.$api.getLineItem(id).then((res) => {
        var pinkuparr = [];
        var piekuparr = [];
        res.data.merchant_group_count_list.forEach((item) => {
          if (item.pickup_min_count != 0) {
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
        this.hasSelectList = pinkuparr;
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
          if (el.type == 1) {
            this.postCodeList.push(el);
          } else if (el.type == 2) {
            this.arrayTransfer(res.data.line_range);
          }
          return {
            post_code_start: el.post_code_start + "",
            post_code_end: el.post_code_end + "",
          };
        });
        this.old_item_list = JSON.stringify(line_range); // 用于检测变化
      });
    },
    // 拼接精准邮编 js数组对象对相同键值进行归类
    arrayTransfer(data) {
      const listArr = [];
      data.forEach(function (el) {
        if (el.type == 2) {
          for (let i = 0; i < listArr.length; i++) {
            if (listArr[i].country === el.country) {
              listArr[i].dynamicTags.push(el.post_code_start);
              return;
            }
          }
          listArr.push({
            country: el.country,
            country_name: el.country_name,
            post_code_end: el.post_code_end,
            type: el.type,
            dynamicTags: [el.post_code_start],
          });
        }
      });
      this.preciseList = listArr;
    },

    // 保存
    onSubmit() {
      this.$refs.lineForm.validate((valid) => {
        // if (!this.checkCode()) return;
        if (valid) {
          if (this.isEdit) {
            let data = this.forDate();
            if (this.old_item_list == data.item_list) {
              this.$api.editLineItem(this.$route.query.id, data).then((res) => {
                if (!res) return;
                this.$router.push({
                  name: "Dispatch",
                  query: {
                    type: 2,
                  },
                });
              });
            } else {
              this.$confirm(
                this.$t("ThePostcodeModifiedContains"),
                this.$t("Notice"),
                {
                  confirmButtonText: this.$t("Confirm"),
                  cancelButtonText: this.$t("Cancel"),
                  type: "warning",
                }
              )
                .then(() => {
                  this.$api
                    .editLineItem(this.$route.query.id, data)
                    .then((res) => {
                      if (!res) return;
                      this.$router.push({
                        name: "Dispatch",
                        query: {
                          type: 2,
                        },
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
                name: "Dispatch",
              });
            });
        }
      });
    },
  },
};
</script>

<style lang="less" scoped>
.AddLinePlanning1 {
  background-color: #fff;
  padding-top: 20px;
  padding-left: 40px;
  .form {
    .add-row {
      margin-bottom: 20px;
      .add-but {
        margin-left: 82%;
        padding-left: 30px;
        padding-right: 30px;
      }
    }
    .form-box {
      width: 86%;
      padding: 20px;
      background-color: #f7f7f7;
      .form-table {
        width: 100%;
        margin-top: 20px;
      }
    }
    span {
      margin-left: 20px;
    }
    .form-but {
      margin-bottom: 30px;
      background-color: #4899d0;
      border: #4899d0;
      color: #fff;
      // .el-button {
      //   padding-left: 40px;
      //   padding-right: 40px;
      // }
    }
  }
  .el-tag + .el-tag {
    margin-left: 5px;
  }
  .input-new-tag {
    width: 80px;
  }
}
</style>
