<template>
  <!-- 司机列表 -->
  <div class="content_box">
    <div class="driver_add_button">
      <div>
        <el-input
          size="small"
          clearable
          @keyup.enter.native="getDrivers"
          v-model="pageParams.fullname"
          :placeholder="$t('pleaseEnterName')"
          class="search-bar"
        ></el-input>
        <el-button
          size="small"
          type="primary"
          @click="getDrivers"
          :loading="this.$store.state.config.button_loading"
          >{{ $t("Search") }}</el-button
        >
      </div>
      <!-- 新增 -->
      <div>
        <el-tooltip
          :content="$t('AdministratorActivate')"
          placement="top"
          :disabled="hasPerm('driver.store') ? true : false"
        >
          <div>
            <el-button
              :disabled="!hasPerm('driver.store')"
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
      class="table_box"
      label-position="right"
      v-loading="global_loading()"
      element-loading-text="loading..."
      border
      :data="hasPerm('driver.index') ? list : []"
      :cell-style="{ padding: '5px 0px' }"
    >
      <template slot="empty" v-if="!hasPerm('driver.index')">
        <div style="font-size: 18px">
          {{ $t("AdministratorActivate") }}
        </div>
      </template>
      <el-table-column type="expand">
        <template slot-scope="props">
          <el-form
            label-position="right"
            label-width="180px"
            :model="props.row"
          >
            <el-card class="box-card">
              <div slot="header" class="clearfix">
                <!-- 基本信息 -->
                <span>{{ $t("GeneralInfo") }}</span>
              </div>
              <div class="top_inner_box">
                <div class="top_inner_left">
                  <el-form-item :label="$t('Avatar')" prop="car_no">
                    <img
                      width="78px"
                      height="78px"
                      v-if="props.row.avatar !== ''"
                      :src="props.row.avatar"
                      alt=""
                    />
                  </el-form-item>
                  <el-form-item :label="$t('Name')" prop="car_no">
                    {{ props.row.fullname }}
                  </el-form-item>
                  <el-form-item :label="$t('Phone')">
                    {{ props.row.phone }}
                  </el-form-item>
                  <el-form-item :label="$t('BTWNO')" prop="duty_paragraph">
                    {{ props.row.duty_paragraph }}
                  </el-form-item>
                </div>
                <div class="top_inner_right">
                  <el-form-item :label="$t('Email')">
                    {{ props.row.email }}
                  </el-form-item>
                  <el-form-item :label="$t('Country')">
                    {{ props.row.country }}
                  </el-form-item>
                  <el-form-item :label="$t('Address')" prop="brand_name">
                    {{ props.row.address }}
                  </el-form-item>
                  <el-form-item :label="$t('DriverType')" prop="type_name">
                    {{ props.row.type_name }}
                  </el-form-item>
                </div>
              </div>
            </el-card>

            <div class="center_box">
              <el-card class="box-card center_left">
                <div slot="header" class="clearfix">
                  <span>{{ $t("Drivinglicense") }}</span>
                </div>
                <div>
                  <el-form-item :label="$t('Drivinglicenseno')">
                    {{ props.row.lisence_number }}
                  </el-form-item>
                  <el-form-item :label="$t('Expirydate')">
                    {{ props.row.lisence_valid_date }}
                  </el-form-item>
                  <el-form-item :label="$t('Drivinglicensetype')">
                    {{ props.row.lisence_type }}
                  </el-form-item>
                </div>
              </el-card>
              <el-card class="box-card center_right">
                <div slot="header" class="clearfix">
                  <span>{{ $t("Bankinfo") }}</span>
                </div>
                <div>
                  <div class="basic_box">
                    <el-form-item :label="$t('BankName')" prop="car_no">
                      {{ props.row.bank_name }}
                    </el-form-item>
                    <el-form-item label="IBAN:" prop="brand_name">
                      {{ props.row.iban }}
                    </el-form-item>
                    <el-form-item label="BIC:">
                      {{ props.row.bic }}
                    </el-form-item>
                  </div>
                </div>
              </el-card>
            </div>

            <el-card class="box-card bottom_box">
              <div slot="header" class="clearfix">
                <span>{{ $t("Relateddoc") }}</span>
              </div>
              <div>
                <el-form-item :label="$t('Drivinglicense')">
                  <span
                    v-if="props.row.lisence_material !== '' || null"
                    class="show_lisence_material"
                    @click="handleClickDriver(props.row.lisence_material)"
                  >
                    {{ $t("View") }}
                  </span>
                </el-form-item>
              </div>
            </el-card>
          </el-form>
        </template>
      </el-table-column>
      <el-table-column width="120" align="center" :label="$t('Operation')">
        <template slot-scope="scope">
          <el-dropdown>
            <el-button size="mini" type="primary" class="operation" plain>
              {{ $t("Operation") }}
              <i class="el-icon-arrow-down el-icon--right"></i>
            </el-button>
            <el-dropdown-menu slot="dropdown">
              <!-- 修改密码 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('driver.reset-password') ? true : false"
              >
                <div
                  :style="
                    hasPerm('driver.reset-password')
                      ? ''
                      : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('driver.reset-password')"
                    v-if="hasPerm('driver.reset-password')"
                    @click.native="modeifyPassword(scope.row)"
                  >
                    <el-button
                      :disabled="!hasPerm('driver.reset-password')"
                      class="operation"
                      size="small"
                      type="text"
                      >{{ $t("EditPassword") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 编辑 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('driver.update') ? true : false"
              >
                <div
                  :style="hasPerm('driver.update') ? '' : 'cursor: not-allowed'"
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('driver.update')"
                    @click.native="modeifyItem(scope.row)"
                  >
                    <el-button
                      :disabled="!hasPerm('driver.update')"
                      class="operation"
                      size="small"
                      type="text"
                      >{{ $t("Edit") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 解锁 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('driver.lock') ? true : false"
              >
                <div
                  :style="hasPerm('driver.lock') ? '' : 'cursor: not-allowed'"
                >
                  <el-dropdown-item
                    @click.native="clockItem(scope.row)"
                    v-if="scope.row.is_locked === 2"
                    :disabled="!hasPerm('driver.lock')"
                  >
                    <el-button
                      :disabled="!hasPerm('driver.lock')"
                      class="operation"
                      size="small"
                      type="text"
                      >{{ $t("Unlock") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 锁定 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('driver.lock') ? true : false"
              >
                <div
                  :style="hasPerm('driver.lock') ? '' : 'cursor: not-allowed'"
                >
                  <el-dropdown-item
                    @click.native="clockItem(scope.row)"
                    v-if="scope.row.is_locked === 1"
                    :disabled="!hasPerm('driver.lock')"
                  >
                    <el-button
                      :disabled="!hasPerm('driver.lock')"
                      class="operation"
                      size="small"
                      type="text"
                      >{{ $t("Locked") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 删除 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('driver.destroy') ? true : false"
              >
                <div
                  :style="
                    hasPerm('driver.destroy') ? '' : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('driver.destroy')"
                    @click.native="delDrivers(scope.row.id)"
                  >
                    <el-button
                      :disabled="!hasPerm('driver.destroy')"
                      class="operation"
                      size="small"
                      type="text"
                      >{{ $t("Delete") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
            </el-dropdown-menu>
          </el-dropdown>
        </template>
      </el-table-column>
      <!-- 姓名 -->
      <el-table-column align="center" :label="$t('name')" prop="fullname">
      </el-table-column>
      <!-- 所属网点 -->
      <el-table-column
        header-align="center"
        align="center"
        prop="warehouse_name"
        :label="$t('AffiliatedBranches')"
      >
      </el-table-column>
      <el-table-column align="center" prop="phone" :label="$t('Phone')">
      </el-table-column>
      <el-table-column
        align="center"
        prop="email"
        :label="$t('Email')"
        width="170"
      >
      </el-table-column>

      <el-table-column
        align="center"
        prop="lisence_type"
        :label="$t('Drivinglicensetype')"
        width="180"
      >
      </el-table-column>
      <el-table-column align="center" prop="is_locked" :label="$t('Status')">
        <template slot-scope="scope">
          <i
            v-if="scope.row.is_locked === 2"
            class="el-icon-lock custom_lock"
          ></i>
          {{ scope.row.is_locked_name }}
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        prop="created_at"
        :label="$t('CreationTime')"
        width="170"
      >
      </el-table-column>
    </el-table>
    <base-pagination
      :pageParams="pageParams"
      @search="getDrivers"
      class="pagination-size"
    >
    </base-pagination>
    <el-dialog
      :title="$t('EditPassword')"
      :visible.sync="visibleModeifyPassword"
    >
      <el-form
        ref="password_form"
        :model="password_form"
        :rules="password_rules"
        label-width="170px"
      >
        <el-form-item
          :label="$t('NewPassword')"
          prop="new_password"
          style="margin-bottom: 30px"
        >
          <el-input
            v-model="password_form.new_password"
            type="password"
          ></el-input>
        </el-form-item>
        <el-form-item
          :label="$t('ConfirmPassword')"
          prop="confirm_new_password"
          class="new_password"
        >
          <el-input
            v-model="password_form.confirm_new_password"
            type="password"
          ></el-input>
        </el-form-item>
        <el-form-item class="password_button">
          <el-row>
            <!-- 取消 -->
            <el-col :span="2" :offset="16">
              <el-button
                @click="visibleModeifyPassword = false"
                class="cancel"
                >{{ $t("Cancel") }}</el-button
              >
            </el-col>
            <!-- 提交 -->
            <el-col :span="2" :offset="2">
              <el-button
                @click="handlerPasswordSubmit"
                type="primary"
                class="submit"
                >{{ $t("Submit") }}</el-button
              >
            </el-col>
          </el-row>
        </el-form-item>
      </el-form>
    </el-dialog>
  </div>
</template>

<script>
import BasePagination from "@/components/BasePagination/BasePagination";
import loading from "@/mixin/global_loading";

export default {
  name: "driverManagement",
  components: {
    BasePagination,
  },
  mixins: [loading],
  data() {
    return {
      visibleModeifyPassword: false,
      activeName: "postcode",
      list: [],
      pageParams: {
        group_id: "",
        is_lock: "",
        begin_date: "",
        end_date: "",
        from_source: "",
        fullname: "",
        total: 0,
        page: 1,
        per_page: 20,
      },
      password_form: {
        id: "", // 司机 id
        new_password: "", // 新密码
        confirm_new_password: "",
      },
    };
  },
  activated() {
    if (this.$route.params.driverManagement) {
      this.getDrivers();
    }
  },
  created() {
    this.getDrivers();
  },
  watch: {
    visibleModeifyPassword(value) {
      if (!value) {
        this.password_form.id = "";
        this.password_form.new_password = "";
        this.password_form.confirm_new_password = "";
      }
    },
  },
  computed: {
    password_rules() {
      return {
        new_password: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        confirm_new_password: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
      };
    },
  },
  methods: {
    handlerPasswordSubmit() {
      this.$refs.password_form.validate((valid) => {
        if (valid) {
          this.$api.changePassword(this.password_form).then((res) => {
            if (!res) return;
            this.visibleModeifyPassword = !this.visibleModeifyPassword;
            this.getDrivers();
          });
        }
      });
    },
    modeifyPassword(row) {
      this.password_form.id = row.id;
      this.visibleModeifyPassword = !this.visibleModeifyPassword;
    },
    handleClickDriver(url) {
      if (!url) return;
      window.open(url);
    },
    modeifyItem(row) {
      this.$router.push({
        name: "editDriver",
        query: {
          email: `${row.email}`,
          phone: row.phone,
          id: row.id,
        },
      });
    },
    clockItem(row) {
      // eslint-disable-next-line camelcase
      let is_locked;
      // eslint-disable-next-line
      row.is_locked === 1 ? (is_locked = 2) : (is_locked = 1);
      this.$api
        .lockDriver(row.id, {
          id: row.id,
          is_locked,
        })
        .then((res) => {
          if (!res) return;
          this.getDrivers();
        });
    },
    // 列表
    getDrivers() {
      this.$api.getDrivers(this.pageParams).then((res) => {
        this.list = res.data.data;
        this.pageParams.total = res.data.meta.total;
      });
    },
    delDrivers(id) {
      // 删除
      this.$confirm(this.$t("ConfirmToCancel") + "?", this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      })
        .then(() => {
          this.$api.delDrivers(id).then((res) => {
            if (res.code === 200) {
              this.getDrivers();
            }
          });
        })
        .catch(() => {});
    },
    handleClick(tab, event) {
      //console.log(tab, event);
    },
    handlerAddClick() {
      this.$router.push({
        name: "addDriver",
      });
    },
  },
};
</script>

<style lang="less" scoped>
.content_box {
  padding: 5px 25px 10px;
  background-color: #fff;
  position: relative;
  .table_box {
    .el-table__header {
      border-bottom: 3px solid #e4ebf1;
    }
  }
  .driver_add_button {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
    margin-bottom: 10px;

    .add_button {
      overflow: hidden;
      white-space: nowrap;
    }
  }
  .el-tabs__content {
    overflow: unset;
  }
}
.el-dropdown-menu__item {
  color: #28d094;
}
.top_inner_box {
  display: flex;
  .top_inner_left {
    flex: 1;
  }
  .top_inner_right {
    flex: 1;
  }
}

.center_box {
  margin-top: 10px;
  display: flex;
  .center_left {
    flex: 1;
  }
  .center_right {
    flex: 1;
  }
}
.bottom_box {
  margin-top: 10px;
}
.el-form-item {
  margin-bottom: 0;
}
.show_lisence_material {
  cursor: pointer;
  color: #28d094;
}
.password_button {
  margin-top: 20px;
}
.el-form-item {
  margin-bottom: 0;
}
.custom_lock {
  color: red;
}
.new_password {
  margin-top: 10px;
}
</style>
