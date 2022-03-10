<template>
<!-- 仓库地址设置 -->
  <div class="warehouse">
    <div class="add-warehouse right-angle">
      <el-button size="small" type="primary" v-if="hasPerm('warehouse.store')" @click="open_dialog">{{$t('AddWarehouse')}}</el-button>
    </div>
    <el-table
 max-height="600px"      v-loading="global_loading()"
      element-loading-text="loading..."
      :data="warehouseList"
      border
      style="width: 100%">
      <el-table-column
      type="selection"
      align="center"
      width="50">
      </el-table-column>
      <el-table-column
        prop="date"
        align="center"
        :label="$t('Operation')">
        <template slot-scope="scope">
            <el-dropdown size="mini" trigger="hover" placement="bottom">
                <el-button size="mini" type="primary">
                    {{$t('Operation')}}
                </el-button>
                <el-dropdown-menu slot="dropdown">
                    <el-dropdown-item>
                        <el-button class="operation" v-if="hasPerm('warehouse.update')" @click="open_edit_dislog(scope.row.id)" size="small" type="text">{{$t('Edit')}}</el-button>
                    </el-dropdown-item>
                    <el-dropdown-item>
                        <el-button class="operation" v-if="hasPerm('warehouse.destroy')" @click="delWarehouse(scope.row.id)" size="small" type="text">{{$t('Delete')}}</el-button>
                    </el-dropdown-item>
                </el-dropdown-menu>
            </el-dropdown>
        </template>
      </el-table-column>
      <el-table-column
        prop="name"
        align="center"
        :label="$t('WarehouseName')">
      </el-table-column>
      <el-table-column
        prop="fullname"
        align="center"
        :label="$t('ContactPerson')">
      </el-table-column>
      <el-table-column
        prop="phone"
        align="center"
        :label="$t('Tel')">
      </el-table-column>
      <el-table-column
        prop="country_name"
        align="center"
        :label="$t('Country')">
      </el-table-column>
      <div v-if="template_id == 1">
        <el-table-column
          prop="post_code"
          align="center"
          :label="$t('ZipCode')">
        </el-table-column>
        <el-table-column
          prop="city"
          align="center"
          :label="$t('City')">
        </el-table-column>
      </div>
      <el-table-column
        prop="address"
        align="center"
        :label="$t('Address')">
      </el-table-column>
    </el-table>

    <el-dialog
      class="right-angle"
      :title="edit ? $t('ModifyWarehouse') : $t('AddWarehouse')"
      :visible.sync="show_dialog"
      width="55%"
      @close="close_dialog">
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
            <el-input v-model="warehouseInfo.name" :placeholder="$t('PleaseEnterWarehouseName')"></el-input>
          </el-form-item>
          <el-form-item :label="$t('WarehouseContactPerson')" prop="fullname">
            <el-input v-model="warehouseInfo.fullname" :placeholder="$t('PleaseEnterWarehouseContact')"></el-input>
          </el-form-item>
          <el-form-item :label="$t('WarehouseTel')" prop="phone">
            <el-input v-model="warehouseInfo.phone" :placeholder="$t('PleaseEnterWarehouseTel')"></el-input>
          </el-form-item>

          <div v-if="template_id == 1">
            <el-row class="small-input">
              <el-col :span="12">
                <el-form-item :label="$t('ZipCode')" prop="post_code">
                  <el-input v-model="warehouseInfo.post_code" @change="getLocation()" :placeholder="$t('PleaseEnterZipCode')"></el-input>
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item :label="$t('DoorNo')" prop="house_number">
                  <el-input v-model="warehouseInfo.house_number" @change="getLocation()" :placeholder="$t('PleaseEnterDoorNo')"></el-input>
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item :label="$t('City')" prop="city">
                  <el-input v-model="warehouseInfo.city" :disabled="disabledBtnByCountry" @change="getLocation()" :placeholder="$t('PleaseEnterCity')"></el-input>
                </el-form-item>
              </el-col>
              <el-col :span="12">
                <el-form-item :label="$t('Street')" prop="street">
                  <el-input v-model="warehouseInfo.street" :disabled="disabledBtnByCountry" @change="getLocation()" :placeholder="$t('PleaseEnterStreet')"></el-input>
                </el-form-item>
              </el-col>
            </el-row>
          </div>
          <el-form-item v-else :label="$t('Address')"  prop="address">
            <input-search vi-show="false" @result="getResult"></input-search>
            <el-input id="pac_input_box" v-model="warehouseInfo.address" :placeholder="$t('PleaseEnterAddress')">
              <el-button slot="append" @click="select_map = true" class="locale-btn">
                <i class="el-icon-map-location" />定位
              </el-button>
            </el-input>
          </el-form-item>
          <div class="dialog-footer">
            <el-button @click="show_dialog = false">{{$t('Cancel')}}</el-button>
            <el-button type="primary" @click="addWarehouse" :loading="this.$store.state.config.button_loading">{{$t('Confirm')}}</el-button>
          </div>
        </el-form>
    </el-dialog>

    <el-dialog
      :title="$t('findAddress')"
      :close-on-click-modal="false"
      :visible.sync="select_map"
      width="90%">
      <b-map class="select-map dialog-wrapper" v-if="mapType === 'baidu'" @addressSelected="handlerAddressSelected"></b-map>
      <google-map class="select-map dialog-wrapper" v-else @addressSelected="handlerAddressSelected"></google-map>
    </el-dialog>
  </div>
</template>

<script>
import loading from '@/mixin/global_loading';
import googleMap from '@/components/Map/GoogleMap';
import InputSearch from '@/components/Map/InputSearchMap';
import BMap from '@/components/BaiduMap/getAddressByMap';

export default {
  name: 'WarehouseAddress',
  mixins: [loading],
  components: {
    googleMap,
    InputSearch,
    BMap
  },
  data() {
    return {
      warehouseList: [],
      warehouseInfo: {
        name: '',
        fullname: '',
        phone: '',
        post_code: '',
        house_number: '',
        city: '',
        street: '',
        address: '',
        lon: '0',
        lat: '0',
      }, // 仓库信息
      show_dialog: false,
      edit: false,
      address: [],
      checked: false,
      loading: false,

      // 地图
      select_map: false,
      template_id: localStorage.getItem('address_template_id') || 1,
      country: localStorage.getItem('country') && JSON.parse(localStorage.getItem('country')).en,

      rules: {
        name: [{ required: true, message: this.$t('PleaseEnterWarehouseName'), trigger: 'blur' }],
        fullname: [
          { required: true, message: this.$t('PleaseEnterWarehouseContact'), trigger: 'blur' },
        ],
        phone: [{ required: true, message: this.$t('PleaseEnterWarehouseTel'), trigger: 'blur' }],
        post_code: [
          { required: true, message: this.$t('PleaseEnterZipCode'), trigger: 'blur' },
        ],
        house_number: [
          { required: true, message: this.$t('PleaseEnterDoorNo'), trigger: 'blur' },
        ],
        city: [
          { required: true, message: this.$t('PleaseEnterCity'), trigger: 'blur' },
        ],
        street: [
          { required: true, message: this.$t('PleaseEnterStreet'), trigger: 'blur' },
        ],
        address: [
          { required: true, message: this.$t('PleaseEnterAddress'), trigger: 'blur' },
        ],
      },
    };
  },
  created() {
    console.log(this.hasPerm('warehouse.store'), '功能筛选')
    this.getWarehouse();
  },
  computed: {
    disabledBtnByCountry() {
      return this.country === 'NL';
    },
    mapType() {
      return this.$store.state.globalData.maptype;
    },
    demoCom() {
      return this.hasPerm('warehouse.store')
    }
  },
  watch: {
    demoCom: {
      handler(val) {
        console.log(val, 'demo')
      }
    }
  },
  methods: {
    getResult(data) {
      console.log(data, '获取到的地址');
      // 填充收货人
      this.warehouseInfo.address = data.address;
      this.warehouseInfo.post_code = data.postal_code;
      this.warehouseInfo.house_number = data.street_number;
      this.warehouseInfo.city = data.city;
      this.warehouseInfo.street = `${data.route}`;
      this.warehouseInfo.lat = data.location.lat;
      this.warehouseInfo.lon = data.location.lng;
    },
    getWarehouse() { // 仓库列表
      this.$api.getWarehouse().then((res) => {
        this.warehouseList = res.data.data;
      });
    },
    getCountryList() { // 国家列表
      this.$api.getCountryList().then((res) => {
        this.address = res.data;
      });
    },
    getLocation() {
      console.log(this.country, 'this.country')
      if (
        this.warehouseInfo.post_code
        && this.warehouseInfo.house_number
        && (this.country === "NL" ||
            (this.warehouseInfo.city &&
              this.warehouseInfo.street))
        ) {
        let isNL = false;
        if (this.country === "NL") {
          this.warehouseInfo.city = "";
          this.warehouseInfo.street = "";
          isNL = true;
        }
        this.$api.getLocation({
          post_code: this.warehouseInfo.post_code,
          house_number: this.warehouseInfo.house_number,
          city: this.warehouseInfo.city,
          street: this.warehouseInfo.street,
        }).then((res) => {
          if (!res) return;
          this.warehouseInfo.lat = res.data.lat;
          this.warehouseInfo.lon = res.data.lon;
          if (isNL) {
            this.warehouseInfo.city = res.data.city;
            this.warehouseInfo.street = res.data.street;
          }
        });
      }
    },

    open_dialog() { // 打开弹窗
      this.show_dialog = true;
    },
    addWarehouse() { // 添加新仓库
      this.$refs.companyForm.validate((valid) => {
        if (!valid) return;
        this.loading = true;
        if (this.template_id == 2) {
          this.addOrEditWarehouse();
          return
        }
        this.$api.getLocation({
          post_code: this.warehouseInfo.post_code,
          house_number: this.warehouseInfo.house_number,
          city: this.warehouseInfo.city,
          street: this.warehouseInfo.street,
        }).then((res) => {
          this.warehouseInfo.lon = res.data.lon;
          this.warehouseInfo.lat = res.data.lat;
          this.loading = false;
          this.addOrEditWarehouse();
        }).catch(() => {
          this.loading = false;
        });
      });
    },

    addOrEditWarehouse() {
      if (this.edit) { // 修改
        this.$api.putWarehouse(this.warehouseInfo).then((re) => {
          if (re.code === 200) {
            this.show_dialog = false;
            this.getWarehouse();
          }
        }).catch(() => {
          this.loading = false;
        });
      } else { // 添加
        this.$api.addWarehouse(this.warehouseInfo).then((re) => {
          if (re.code === 200) {
            this.show_dialog = false;
            this.getWarehouse();
          }
        }).catch(() => {
          this.loading = false;
        });
      }
    },

    open_edit_dislog(id) { // 打开修改弹窗
      this.edit = true;
      this.show_dialog = true;
      this.getWarehouseDetails(id);
    },
    getWarehouseDetails(id) { // 获得仓库详情
      this.$api.getWarehouseDetails(id).then((res) => {
        this.warehouseInfo = res.data;
      });
    },
    close_dialog() { // 关闭弹窗
      this.edit = false;
      this.warehouseInfo = {
        name: '',
        fullname: '',
        phone: '',
        post_code: '',
        house_number: '',
        city: '',
        street: '',
        address: '',
        lon: '0',
        lat: '0',
      };
    },
    delWarehouse(id) { // 删除
    // eslint-disable-next-line
      this.$confirm(this.$t('ConfirmToCancel') + '?', this.$t('Notice'), {
        confirmButtonText: this.$t('Confirm'),
        cancelButtonText: this.$t('Cancel'),
        type: 'warning',
      }).then(() => {
        this.$api.delWarehouse(id).then((res) => {
          if (res.code === 200) {
            this.getWarehouse();
          } else {
            this.$message({
              message: res.msg,
              type: 'error',
            });
          }
        });
      }).catch(() => {
      });
    },

    // 获取地图的定位信息
    handlerAddressSelected(data) {
      console.log(data, 'dada')
      this.warehouseInfo.address = data.address;
      this.warehouseInfo.post_code = data.postal_code;
      this.warehouseInfo.house_number = data.street_number;
      this.warehouseInfo.city = data.city;
      this.warehouseInfo.street = `${data.route}`;
      this.warehouseInfo.lat = data.location.lat;
      this.warehouseInfo.lon = data.location.lng;
      this.select_map = false;
    },
  },
};
</script>
<style lang="less" scoped>
.warehouse {
  // background: #ffffff;
  padding: 47px 20px 47px;
  // border:1px solid rgba(228, 235, 241, 1);
  .add-warehouse {
    text-align: right;
    margin-bottom: 20px;
    margin-top: -20px;
  }
  .warehouse-address {
    .el-input {
      max-width: 628px;
    }
    .el-select {
      width: 100%;
      max-width: 628px;
    }
    .small-input {
      max-width: 778px;
    }
    .dialog-footer {
      text-align: right;
    }
  }
}
</style>
<style lang="less">
.warehouse {
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
    .locale-btn {
      color: #F6A50D !important;
      background-color: #fff !important;
    }
  }
}
</style>
