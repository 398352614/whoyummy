<template>
  <!-- 车辆管理 -->
  <div class="content_box">
    <div class="vehicle_add_button">
      <div>
        <el-input
          size="small"
          clearable
          @keyup.enter.native="getCars"
          v-model="pageParams.car_no"
          :placeholder="$t('NumberPlate')"
          class="search-bar"
        ></el-input>
        <el-button
          size="small"
          type="primary"
          @click="getCars"
          :loading="this.$store.state.config.button_loading"
          >{{ $t("Search") }}</el-button
        >
      </div>
      <div>
        <el-tooltip
          :content="$t('AdministratorActivate')"
          placement="top"
          :disabled="hasPerm('car.store') ? true : false"
        >
          <div>
            <el-button
              v-if="hasPerm('car.store')"
              @click="handlerAddClick"
              size="small"
              type="primary"
              >{{ $t("Add") }}</el-button
            >
          </div>
        </el-tooltip>
      </div>
    </div>
    <el-table
      height="calc(100vh - 227px)"
      v-loading="global_loading()"
      element-loading-text="loading..."
      class="table_box"
      align="center"
      header-align="center"
      border
      :data="hasPerm('car.index') ? list : []"
      :cell-style="{ padding: '5px 0px' }"
    >
      <template slot="empty" v-if="!hasPerm('car.index')">
        <div style="font-size: 18px">
          {{ $t("AdministratorActivate") }}
        </div>
      </template>
      <el-table-column type="expand">
        <template slot-scope="props">
          <el-form
            :model="props.row"
            label-position="right"
            label-width="150px"
          >
            <el-card class="box-card">
              <div slot="header" class="clearfix">
                <span>{{ $t("GeneralInfo") }}</span>
              </div>
              <div>
                <div class="basic_box">
                  <div class="base_left">
                    <el-form-item :label="$t('NumberPlate')" prop="car_no">
                      {{ props.row.car_no }}
                    </el-form-item>
                    <el-form-item :label="$t('Brand')" prop="brand_name">
                      {{ props.row.brand_name }}
                    </el-form-item>
                    <el-form-item
                      :label="$t('LeaseType')"
                      prop="ownership_type"
                    >
                      {{ props.row.ownership_type_name }}
                    </el-form-item>
                  </div>
                  <div class="base_right">
                    <!-- <el-form-item :label="$t('DateOfManufacture')" prop="outgoing_time">
                                    {{props.row.outgoing_time}}
                                </el-form-item> -->
                    <el-form-item
                      :label="$t('Model')"
                      prop="car_model_type_name"
                    >
                      {{ props.row.car_model_type_name }}
                    </el-form-item>
                    <el-form-item
                      :label="$t('vehicleLength')"
                      prop="car_length_name"
                    >
                      {{ props.row.car_length_name }}
                    </el-form-item>
                    <el-form-item :label="$t('GPS')" prop="gps_device_number">
                      {{ gps_device_number }}
                    </el-form-item>
                  </div>
                </div>
              </div>
            </el-card>
            <el-card class="box-card">
              <div slot="header" class="clearfix">
                <span>{{ $t("OtherRemark") }}</span>
              </div>
              <div>
                <el-card :body-style="{ padding: '0px' }" shadow="nerver">
                  <div slot="header" class="clearfix">
                    <span>{{ $t("InsuranceInfo") }}</span>
                  </div>
                  <div class="insurance_box">
                    <el-form-item :label="$t('InsuranceCompany')">
                      {{ props.row.insurance_company }}
                    </el-form-item>
                    <el-form-item :label="$t('InsurancePerMonth')">
                      {{ props.row.month_insurance }}
                    </el-form-item>
                    <el-form-item :label="$t('InsuranceType')">
                      {{ props.row.insurance_type }}
                    </el-form-item>
                  </div>
                </el-card>
              </div>
            </el-card>
            <el-card class="box-card">
              <div slot="header" class="clearfix">
                <span>{{ $t("VehicleRemark") }}</span>
              </div>
              <div>
                <el-form-item :label="$t('VehicleFile')">
                  <span
                    v-if="props.row.relate_material !== ''"
                    class="download_file_button"
                    @click="handlerClickFile(props.row.relate_material)"
                    >{{ $t("View") }}</span
                  >
                </el-form-item>
              </div>
            </el-card>
          </el-form>
        </template>
      </el-table-column>
      <el-table-column
        width="120px"
        align="center"
        header-align="center"
        :label="$t('Operation')"
      >
        <template slot-scope="scope">
          <!-- 操作 -->
          <el-dropdown>
            <el-button size="mini" type="primary" class="operation" plain>
              {{ $t("Operation") }}
              <i class="el-icon-arrow-down el-icon--right"></i>
            </el-button>
            <el-dropdown-menu slot="dropdown">
              <!-- 编辑 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('car.update') ? true : false"
              >
                <div
                  :style="hasPerm('car.update') ? '' : 'cursor: not-allowed'"
                >
                  <el-dropdown-item
                    @click.native="handlerEditClick(scope.row)"
                    :disabled="!hasPerm('car.update')"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('car.update')"
                      >{{ $t("Edit") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 解锁 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('car.lock') ? true : false"
              >
                <div :style="hasPerm('car.lock') ? '' : 'cursor: not-allowed'">
                  <el-dropdown-item
                    @click.native="clockItem(scope.row)"
                    v-if="scope.row.is_locked === 2"
                    :disabled="!hasPerm('car.lock')"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('car.lock')"
                      >{{ $t("Unlock") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 锁定 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('car.lock') ? true : false"
              >
                <div :style="hasPerm('car.lock') ? '' : 'cursor: not-allowed'">
                  <el-dropdown-item
                    :disabled="!hasPerm('car.lock')"
                    @click.native="clockItem(scope.row)"
                    v-if="scope.row.is_locked === 1"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('car.lock')"
                      >{{ $t("Locked") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 删除 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('car.destroy') ? true : false"
              >
                <div
                  :style="hasPerm('car.destroy') ? '' : 'cursor: not-allowed'"
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('car.destroy')"
                    @click.native="delCar(scope.row.id)"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('car.destroy')"
                      >{{ $t("Delete") }}</el-button
                    >
                    <!-- <span>{{ $t("Delete") }}</span> -->
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 导出车辆信息 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('car.export-info') ? true : false"
              >
                <div
                  :style="
                    hasPerm('car.export-info') ? '' : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('car.export-info')"
                    @click.native="exportCarInfo(scope.row.id)"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('car.export-info')"
                      >{{ $t("exportVehicleInformation") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 导出车辆里程 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('car.export-distance') ? true : false"
              >
                <div
                  :style="
                    hasPerm('car.export-distance') ? '' : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('car.export-distance')"
                    @click.native="openDialog(scope.row.id)"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('car.export-distance')"
                      >{{ $t("exportVehicleMileage") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
            </el-dropdown-menu>
          </el-dropdown>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        header-align="center"
        :label="$t('NumberPlate')"
        prop="car_no"
      >
      </el-table-column>
      <el-table-column
        align="center"
        header-align="center"
        prop="brand_name"
        :label="$t('Brand')"
      >
      </el-table-column>
      <el-table-column
        align="center"
        header-align="center"
        prop="car_model_type_name"
        :label="$t('Model')"
      >
      </el-table-column>
      <el-table-column
        align="center"
        header-align="center"
        prop="ownership_type_name"
        :label="$t('LeaseType')"
      >
      </el-table-column>
      <el-table-column
        align="center"
        header-align="center"
        prop="insurance_company"
        :label="$t('Insurance')"
      >
      </el-table-column>
      <el-table-column
        align="center"
        header-align="center"
        prop="lisence_type"
        :label="$t('Status')"
      >
        <template slot-scope="scope">
          <i
            v-if="scope.row.is_locked === 2"
            class="el-icon-lock custom_lock"
          ></i>
          {{ scope.row.is_locked === 1 ? $t("normal") : $t("Locked") }}
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        header-align="center"
        prop="created_at"
        :label="$t('CreationTime')"
        width="170"
      >
      </el-table-column>
    </el-table>
    <base-pagination
      :pageParams="pageParams"
      @search="getCars"
      class="pagination-size"
    >
    </base-pagination>
    <el-dialog :title="$t('pleaseSelectDate')" :visible.sync="select_date">
      <el-form :model="exportDistanceInfo" label-width="160px">
        <el-form-item :label="$t('pleaseSelectDate')">
          <el-date-picker
            v-model="search_date"
            type="daterange"
            value-format="yyyy-MM-dd"
            :range-separator="$t('To')"
            :start-placeholder="$t('StartTime')"
            :end-placeholder="$t('EndTime')"
          >
          </el-date-picker>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="select_date = false">{{ $t("Cancel") }}</el-button>
        <el-button
          type="primary"
          @click="exportDistance"
          :disabled="!exportDistanceInfo.begin_date"
          :loading="this.$store.state.config.button_loading"
          >{{ $t("Submit") }}</el-button
        >
      </span>
    </el-dialog>
    <iframe frameborder="0" name="downExcel" style="display: none"></iframe>
  </div>
</template>

<script>
import BasePagination from "@/components/BasePagination/BasePagination";
import BaseButton from "@/components/BaseButton/BaseButton";
import loading from "@/mixin/global_loading";

export default {
  name: "carManagement",
  components: {
    BasePagination,
    BaseButton,
  },
  mixins: [loading],
  data() {
    return {
      activeName: "postcode",
      list: [],
      pageParams: {
        car_no: "", //车牌号
        group_id: "",
        is_lock: "",
        begin_date: "",
        end_date: "",
        from_source: "",
        keyword: "",
        total: 0,
        page: 1,
        per_page: 20,
      },
      gps_device_number: "", //GPS
      search_date: ["", ""],
      exportDistanceInfo: {
        id: null,
        begin_date: null,
        end_date: null,
      },
      select_date: false,
    };
  },
  activated() {
    this.getCars();
  },
  created() {
    this.getCars();
  },
  watch: {
    search_date: {
      // 日期筛选
      handler() {
        if (this.search_date) {
          this.exportDistanceInfo.begin_date = this.search_date[0];
          this.exportDistanceInfo.end_date = this.search_date[1];
        } else {
          this.exportDistanceInfo.begin_date = "";
          this.exportDistanceInfo.end_date = "";
        }
      },
    },
  },
  methods: {
    handlerClickFile(url) {
      if (!url) return;
      window.open(url);
    },
    modeifyItem(row) {
      this.$router.push({
        path: "addDriver",
        query: {
          email: `${row.email}`,
          phone: row.phone,
        },
      });
    },
    clockItem(row) {
      // eslint-disable-next-line camelcase
      let is_locked;
      // eslint-disable-next-line
      row.is_locked === 1 ? (is_locked = 2) : (is_locked = 1);
      this.$api
        .lockCars(row.id, {
          id: row.id,
          is_locked,
        })
        .then((res) => {
          if (!res) return;
          this.getCars();
        });
    },
    delCar(id) {
      // 删除
      this.$confirm(this.$t("ConfirmToCancel") + "?", this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      })
        .then(() => {
          this.$api.delCar(id).then((res) => {
            if (res.code === 200) {
              this.getCars();
            }
          });
        })
        .catch(() => {});
    },
    getCars() {
      this.$api.getCars(this.pageParams).then((res) => {
        this.list = res.data.data;
        this.pageParams.total = res.data.meta.total;
        var data = res.data.data;
        data.map((item) => {
          this.gps_device_number = item.gps_device_number;
        });
      });
    },
    handleClick(tab, event) {
      //console.log(tab, event);
    },
    handlerAddClick() {
      this.$router.push({
        name: "addCar",
      });
    },
    handlerEditClick(row) {
      this.$router.push({
        name: "editCar",
        query: {
          id: row.id,
        },
      });
    },

    exportCarInfo(id) {
      // 信息导出
      this.$api.exportCarInfo(id).then((res) => {
        if (!res.data) return;
        window.open(res.data.path);
      });
    },

    openDialog(id) {
      // 打开弹窗
      this.exportDistanceInfo.id = id;
      this.select_date = true;
    },

    exportDistance() {
      // 里程导出
      this.$api.exportDistance(this.exportDistanceInfo).then((res) => {
        if (!res.data) return;
        this.select_date = false;
        let iframe = window.frames["downExcel"];
        let href = res.data.path; //接口路径地址，返回数据类型为application/binary，后台控制显示信息，前端仅为下载功能
        iframe.location.href = href;
      });
    },
  },
};
</script>

<style lang="less" scoped>
.content_box {
  background-color: #fff;
  padding: 5px 25px 10px;
  position: relative;
  .table_box {
    .el-table__header {
      border-bottom: 3px solid #e4ebf1;
    }
  }
  .vehicle_add_button {
    margin: 10px 0px;
    display: flex;
    justify-content: space-between;
  }
  .el-tabs__content {
    overflow: unset;
  }
  .basic_box {
    flex: 1;
    display: flex;
    padding: 20px;
    .base_left {
      flex: 1;
    }
    .base_right {
      flex: 1;
      display: flex;
      flex-direction: column;
    }
  }
  .insurance_box {
    display: flex;
    flex-direction: column;
    padding: 20px;
  }
}
.el-dropdown-menu__item {
  color: #28d094;
}
.download_file_button {
  color: #28d094;
  cursor: pointer;
}
.basic_box .el-form-item {
  margin-bottom: 0 !important;
}
.custom_lock {
  color: red;
}
</style>

