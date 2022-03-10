<template>
  <div class="item-type-configuration">
    <div class="add-warehouse right-angle">
      <el-button size="small" type="primary" @click="open_dialog">{{
        $t("添加物品类型")
      }}</el-button>
    </div>
    <el-table
      max-height="600px"
      v-loading="global_loading()"
      element-loading-text="loading..."
      :data="warehouseList"
      style="width: 100%"
    >
      <el-table-column type="index" :label="$t('Number')" width="50">
      </el-table-column>
      <el-table-column prop="date" :label="$t('Operation')">
        <template slot-scope="scope">
          <el-dropdown size="mini" trigger="hover" placement="bottom">
            <el-button size="mini" type="primary">
              {{ $t("Operation") }}
            </el-button>
            <el-dropdown-menu slot="dropdown">
              <el-dropdown-item>
                <el-button
                  class="operation"
                  @click="open_edit_dislog(scope.row.id)"
                  size="small"
                  type="text"
                  >{{ $t("Edit") }}</el-button
                >
              </el-dropdown-item>
              <el-dropdown-item>
                <el-button
                  class="operation"
                  @click="delWarehouse(scope.row.id)"
                  size="small"
                  type="text"
                  >{{ $t("Delete") }}</el-button
                >
              </el-dropdown-item>
            </el-dropdown-menu>
          </el-dropdown>
        </template>
      </el-table-column>
      <el-table-column prop="name" :label="$t('MerchantType')">
      </el-table-column>
      <el-table-column prop="contacter" :label="$t('级别')"> </el-table-column>
    </el-table>

    <el-dialog
      class="right-angle"
      :title="edit ? $t('ModifyWarehouse') : $t('AddWarehouse')"
      :visible.sync="show_dialog"
      @close="close_dialog"
    >
      <el-form
        :model="warehouseInfo"
        :rules="rules"
        ref="companyForm"
        v-loading="loading"
        label-position="right"
        label-width="210px"
        class="warehouse-address"
      >
        <el-form-item :label="$t('WarehouseName')" prop="name">
          <el-input
            v-model="warehouseInfo.name"
            :placeholder="$t('PleaseEnterWarehouseName')"
          ></el-input>
        </el-form-item>
        <el-form-item :label="$t('WarehouseName')">
          <el-switch v-model="warehouseInfo.name"></el-switch>
        </el-form-item>
        <el-form-item :label="$t('WarehouseName')">
          <el-switch v-model="warehouseInfo.name"></el-switch>
        </el-form-item>

        <el-form-item :label="$t('DetailedAddress')">
          <el-input
            type="textarea"
            v-model="warehouseInfo.address"
            :placeholder="$t('PleaseEnterAddress')"
          ></el-input>
        </el-form-item>
        <div class="dialog-footer">
          <el-button @click="show_dialog = false">{{ $t("Cancel") }}</el-button>
          <el-button
            type="primary"
            @click="addWarehouse"
            :loading="this.$store.state.config.button_loading"
            >{{ $t("Confirm") }}</el-button
          >
        </div>
      </el-form>
    </el-dialog>
  </div>
</template>

<script>
import loading from "@/mixin/global_loading";

export default {
  name: "WarehouseAddress",
  mixins: [loading],
  data() {
    return {
      warehouseList: [],
      warehouseInfo: {
        name: "",
        contacter: "",
      }, // 仓库信息
      show_dialog: false,
      edit: false,
      address: [],
      checked: false,
      loading: false,
      rules: {
        name: [
          {
            required: true,
            message: this.$t("PleaseEnterWarehouseName"),
            trigger: "blur",
          },
        ],
      },
    };
  },
  created() {
    this.getWarehouse();
    this.getCountryList();
  },
  methods: {
    getWarehouse() {
      // 仓库列表
      this.$api.getWarehousePublic().then((res) => {
        this.warehouseList = res.data.data;
      });
    },
    getCountryList() {
      // 国家列表
      this.$api.getCountryList().then((res) => {
        this.address = res.data;
      });
    },
    open_dialog() {
      // 打开弹窗
      this.show_dialog = true;
    },
    addWarehouse() {
      // 添加新仓库
      this.$refs.companyForm.validate((valid) => {
        if (!valid) return;
        this.loading = true;
        this.$api
          .getLocation({
            country: this.warehouseInfo.country,
            post_code: this.warehouseInfo.post_code,
            house_number: this.warehouseInfo.house_number,
            city: this.warehouseInfo.city,
            street: this.warehouseInfo.street,
          })
          .then((res) => {
            this.warehouseInfo.lon = res.data.lon;
            this.warehouseInfo.lat = res.data.lat;
            this.loading = false;
            if (this.edit) {
              // 修改
              this.$api.putWarehouse(this.warehouseInfo).then((re) => {
                if (re.code === 200) {
                  this.show_dialog = false;
                  this.getWarehouse();
                }
              });
            } else {
              // 添加
              this.$api.addWarehouse(this.warehouseInfo).then((re) => {
                if (re.code === 200) {
                  this.show_dialog = false;
                  this.getWarehouse();
                }
              });
            }
          })
          .catch(() => {
            this.loading = false;
          });
      });
    },
    open_edit_dislog(id) {
      // 打开修改弹窗
      this.edit = true;
      this.show_dialog = true;
      this.getWarehouseDetails(id);
    },
    getWarehouseDetails(id) {
      // 获得仓库详情
      this.$api.getWarehouseDetails(id).then((res) => {
        this.warehouseInfo = res.data;
      });
    },
    close_dialog() {
      // 关闭弹窗
      this.edit = false;
      this.warehouseInfo = {
        name: "",
        contacter: "",
        phone: "",
        country: null,
        post_code: "",
        house_number: "",
        city: "",
        street: "",
        address: "",
        lon: "0",
        lat: "0",
      };
    },
    delWarehouse(id) {
      // 获得仓库详情
      // eslint-disable-next-line
      this.$confirm(this.$t("ConfirmToCancel") + "?", this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      })
        .then(() => {
          this.$api.delWarehouse(id).then((res) => {
            if (res.code === 200) {
              this.getWarehouse();
            } else {
              this.$message({
                message: res.msg,
                type: "error",
              });
            }
          });
        })
        .catch(() => {});
    },
  },
};
</script>
<style lang="less" scoped>
.item-type-configuration {
  background: #ffffff;
  padding: 47px 20px 47px;
  border: 1px solid rgba(228, 235, 241, 1);
  .add-warehouse {
    text-align: right;
    margin-bottom: 20px;
    margin-top: -20px;
  }
  .warehouse-address {
    .el-input {
      max-width: 328px;
    }
    .el-textarea {
      max-width: 328px;
    }
    .dialog-footer {
      text-align: right;
    }
  }
}
</style>
<style lang="less">
.item-type-configuration {
  .el-dialog {
    border-radius: 0;
  }
  .right-angle {
    .el-input__inner {
      border-radius: 0;
    }
    .el-button {
      border-radius: 0;
    }
  }
}
</style>
