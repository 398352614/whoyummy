<template>
  <!-- 设备管理 -->
  <div class="device-management">
    <div class="conditional">
      <!-- <el-col :span="6" :offset="8">
        <el-select size="small" clearable v-model="pageParams.driver_id"  @change="handleChangePageParams" :placeholder="$t('请选择司机')">
          <el-option
            v-for="(item, index) in allDriverList"
            :key="index"
            :label="item.fullname"
            :value="item.id"
          ></el-option>
        </el-select>
      </el-col> -->
      <div>
        <el-input
          size="small"
          clearable
          :placeholder="$t('deviceNumber')"
          @keyup.enter.native="handleChangePageParams"
          v-model="pageParams.keyword"
          class="search-bar"
        >
        </el-input>
        <el-input
          size="small"
          clearable
          :placeholder="$t('driverName')"
          @keyup.enter.native="handleChangePageParams"
          v-model="pageParams.driver_name"
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
      <el-tooltip
        :content="$t('AdministratorActivate')"
        placement="top"
        :disabled="hasPerm('device.store') ? true : false"
      >
        <div>
          <el-button
            :disabled="!hasPerm('device.store')"
            size="small"
            @click="openAddDeviceDialog"
            type="primary"
            >{{ $t("Add") }}</el-button
          >
        </div>
      </el-tooltip>
    </div>

    <el-table
      height="calc(100vh - 234px)"
      :data="hasPerm('device.index') ? tableData : []"
      v-loading="global_loading()"
      element-loading-text="loading..."
      border
      style="width: 100%"
      :cell-style="{ padding: '5px 0px' }"
    >
      <template slot="empty" v-if="!hasPerm('device.index')">
        <div style="font-size: 18px">
          {{ $t("AdministratorActivate") }}
        </div>
      </template>
      <el-table-column type="index" align="center" width="50">
        <template slot="header">
          <span>{{ $t("Number") }}</span>
        </template>
      </el-table-column>
      <el-table-column
        :label="$t('Operation')"
        align="center"
        header-align="center"
      >
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
                :disabled="hasPerm('device.update') ? true : false"
              >
                <div
                  :style="hasPerm('device.update') ? '' : 'cursor: not-allowed'"
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('device.update')"
                    @click.native="getDeviceDetails(scope.row.id)"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('device.update')"
                      >{{ $t("Edit") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 绑定账号 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('device.bind') ? true : false"
              >
                <div
                  :style="hasPerm('device.bind') ? '' : 'cursor: not-allowed'"
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('device.bind')"
                    v-if="scope.row.driver_id == null"
                    @click.native="openBindDialog(scope.row.id)"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('device.bind')"
                      >{{ $t("bindAccount") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 解绑 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('device.unBind') ? true : false"
              >
                <div
                  :style="hasPerm('device.unBind') ? '' : 'cursor: not-allowed'"
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('device.unBind')"
                    @click.native="putUnBingDevice(scope.row.id)"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('device.unBind')"
                      >{{ $t("untie") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 删除 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('device.destroy') ? true : false"
              >
                <div
                  :style="
                    hasPerm('device.destroy') ? '' : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('device.destroy')"
                    @click.native="delDevice(scope.row.id)"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('device.destroy')"
                      >{{ $t("Delete") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
            </el-dropdown-menu>
          </el-dropdown>
        </template>
      </el-table-column>
      <el-table-column prop="number" align="center" :label="$t('deviceModel')">
      </el-table-column>
      <el-table-column
        prop="driver_name"
        align="center"
        :label="$t('bindTheDriver')"
        width="200"
      >
      </el-table-column>
      <el-table-column
        prop="status_name"
        align="center"
        :label="$t('deviceStatus')"
      >
      </el-table-column>
      <el-table-column
        prop="mode"
        align="center"
        :label="$t('positioningMode')"
      >
      </el-table-column>
      <el-table-column prop="created_at" align="center" :label="$t('Created')">
      </el-table-column>
    </el-table>
    <base-pagination
      class="pagination-size"
      :pageParams="pageParams"
      @search="getDevice"
    ></base-pagination>

    <el-dialog
      align="center"
      :visible.sync="device_show"
      @close="handleDialogClose"
    >
      <el-form ref="form" :model="addInfo" label-width="100px">
        <el-form-item :label="$t('deviceModel')">
          <el-input v-model="addInfo.number"></el-input>
        </el-form-item>
      </el-form>
      <div class="btn-group">
        <el-button
          @click="closeAddDeviceDialog"
          type="primary"
          class="cancel"
          >{{ $t("Cancel") }}</el-button
        >
        <el-button
          v-if="edit"
          @click="putDevice"
          :disabled="!addInfo.number"
          :loading="this.$store.state.config.button_loading"
          type="primary"
          class="submit"
          >{{ $t("Confirm") }}</el-button
        >
        <el-button
          v-else
          @click="addDevice"
          :disabled="!addInfo.number"
          :loading="this.$store.state.config.button_loading"
          type="primary"
          class="submit"
          >{{ $t("Confirm") }}</el-button
        >
      </div>
    </el-dialog>

    <el-dialog
      :title="this.$t('PleaseSelectDriver')"
      @close="closeBindDialog"
      :visible.sync="bind_show"
    >
      <el-table
        max-height="600px"
        element-loading-text="loading..."
        border
        :data="driverList"
        @row-click="handlerRowClick($event)"
      >
        <el-table-column
          :label="this.$t('select')"
          header-align="center"
          align="center"
        >
          <template slot-scope="scope">
            <el-radio v-model="bindInfo.driver_id" :label="scope.row.id">
              <span class="el-radio__label"></span>
            </el-radio>
          </template>
        </el-table-column>
        <el-table-column
          :label="$t('Driver')"
          prop="fullname"
          align="center"
        ></el-table-column>
        <el-table-column
          :label="$t('Email')"
          prop="email"
          align="center"
        ></el-table-column>
      </el-table>
      <div class="pagination-box">
        <base-pagination
          :pageParams="driverPageParams"
          @search="getDriverIndex()"
        >
        </base-pagination>
      </div>
      <div class="footer-btn-box">
        <el-button @click="bind_show = false" class="cancel"
          >{{ $t("Cancel") }}
        </el-button>
        <el-button
          @click="putBingDevice()"
          :disabled="!bindInfo.driver_id"
          type="primary"
          class="submit"
          >{{ $t("Submit") }}</el-button
        >
      </div>
    </el-dialog>
  </div>
</template>

<script>
import BasePagination from "@/components/BasePagination/BasePagination";
import loading from "@/mixin/global_loading";

export default {
  name: "TaskReport",
  components: {
    BasePagination,
  },
  mixins: [loading],
  data() {
    return {
      tableData: [],
      allDriverList: [],
      pageParams: {
        driver_name: "",
        begin_date: "",
        end_date: "",
        keyword: "",
        total: 0,
        page: 1,
        per_page: 20,
      },

      device_show: false,

      addInfo: {
        number: null,
      },

      edit: false,

      // 绑定
      bind_show: false,
      driverList: [],
      lineDataRadio: null,
      bindInfo: {
        id: null,
        driver_id: null,
      },
      driverPageParams: {
        begin_date: "",
        end_date: "",
        keyword: "",
        total: 0,
        page: 1,
        per_page: 10,
      },
    };
  },
  created() {
    this.getDevice();
    this.getDrivers();
  },
  methods: {
    getDevice() {
      // 列表
      this.$api.getDevice(this.pageParams).then((res) => {
        this.tableData = res.data.data;
        this.pageParams.total = res.data.meta.total;
      });
    },

    getDrivers() {
      this.$api
        .getDriversPublic({
          page: 1,
          per_page: 200,
        })
        .then((res) => {
          this.allDriverList = res.data.data;
        });
    },

    handleChangePageParams() {
      this.pageParams.page = 1;
      this.getDevice();
    },

    openAddDeviceDialog() {
      // 打开弹窗
      this.device_show = true;
    },

    closeAddDeviceDialog() {
      // 关闭弹窗
      this.device_show = false;
    },

    handleDialogClose() {
      this.addInfo = {
        number: null,
      };
      this.edit = false;
    },

    addDevice() {
      // 新增
      this.$api.addDevice(this.addInfo).then((res) => {
        if (res.code !== 200) return;
        this.device_show = false;
        this.getDevice();
      });
    },

    getDeviceDetails(id) {
      // 详情
      this.device_show = true;
      this.edit = true;
      this.$api.getDeviceDetails(id).then((res) => {
        this.addInfo = res.data;
      });
    },

    putDevice() {
      // 修改
      this.$api.putDevice(this.addInfo).then((res) => {
        if (res.code !== 200) return;
        this.device_show = false;
        this.getDevice();
      });
    },

    delDevice(id) {
      // 删除
      this.$confirm(this.$t("ConfirmToCancel") + "?", this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      })
        .then(() => {
          this.$api.delDevice(id).then((res) => {
            if (res.code !== 200) return;
            this.device_show = false;
            this.getDevice();
          });
        })
        .catch(() => {});
    },

    // 绑定
    getDriverIndex() {
      // 司机查询
      this.$api.getDriverIndex(this.driverPageParams).then((res) => {
        if (res.code !== 200) return;
        this.driverList = res.data.data;
        this.driverPageParams.total = res.data.meta.total;
      });
    },

    handlerRowClick(e) {
      //列表项选取
      this.bindInfo.driver_id = e.id;
    },

    openBindDialog(id) {
      this.bindInfo.id = id;
      this.bind_show = true;
      this.getDriverIndex();
    },

    closeBindDialog() {
      this.bindInfo = {
        id: null,
        driver_id: null,
      };
      this.driverList = [];
      this.driverPageParams.page = 1;
    },

    putBingDevice() {
      // 绑定司机
      this.$api.putBingDevice(this.bindInfo).then((res) => {
        if (res.code !== 200) return;
        this.bind_show = false;
        this.getDevice();
      });
    },

    putUnBingDevice(id) {
      // 解绑
      this.$api.putUnBingDevice(id).then((res) => {
        if (res.code !== 200) return;
        this.getDevice();
      });
    },
  },
};
</script>
<style lang="less" scoped>
.device-management {
  padding: 20px 25px 10px;
  background: #ffffff;
  border: 1px solid rgba(228, 235, 241, 1);
  .conditional {
    padding: 0px 0 10px;
    display: flex;
    justify-content: space-between;
  }
  .btn-group {
    text-align: right;
    // display: flex;
    // justify-content: space-between;
    // padding-left: 100px;
  }
  .pagination-box {
    padding: 10px;
  }
  .footer-btn-box {
    text-align: right;
    padding: 10px;
  }
}
</style>
<style lang="less">
.device-management {
  .conditional {
    .el-input__inner {
      border-radius: 0;
    }
    .el-input {
      max-width: 328px;
    }
  }
  .el-date-editor {
    width: 100%;
  }
  .el-select {
    width: 100%;
  }
}
</style>
