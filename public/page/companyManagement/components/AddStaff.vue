<template>
  <!-- 添加员工 -->
  <div class="add-staff">
    <div class="form-main">
      <el-form
        ref="employeeForm"
        :rules="rules"
        :model="employees"
        label-width="150px"
      >
        <div class="form-box">
          <div class="label-info">{{ $t("GeneralInfo") }}</div>
          <el-row :gutter="20">
            <el-col :span="12">
              <!-- 姓名 -->
              <el-form-item :label="$t('name')" prop="fullname">
                <el-input
                  size="small"
                  v-model="employees.fullname"
                  :placeholder="$t('PleaseEnterTheStaffName')"
                  style="width: 70%"
                ></el-input>
              </el-form-item>
              <!-- 所属网点 -->
              <el-form-item
                :label="$t('AffiliatedBranches')"
                prop="warehouse_id"
              >
                <!-- 树形结构 -->
                <el-cascader
                  size="small"
                  :options="warehouseList"
                  :props="optionProps"
                  :clearable="true"
                  v-model="employees.warehouse_id"
                  style="width: 70%"
                >
                </el-cascader>
                <!-- <el-select
                  size="small"
                  v-model="employees.warehouse_id"
                  :clearable="true"
                  style="width: 70%"
                >
                  <el-option
                    v-for="item in warehouseList"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id"
                  >
                  </el-option>
                </el-select> -->
              </el-form-item>
              <!-- 联系电话 -->
              <el-form-item :label="$t('Phone')" prop="phone">
                <el-input
                  size="small"
                  v-model="employees.phone"
                  :placeholder="$t('PleaseEnterPhoneNumber')"
                  style="width: 70%"
                ></el-input>
              </el-form-item>
              <!-- 联系地址 -->
              <!-- <div v-if="template_id == 1">
                <el-row :gutter="20">
                  <el-col :span="12">
                    <el-form-item :label="$t('ZipCode')" prop="post_code">
                      <el-input
                        v-model="employees.post_code"
                        @change="getLocation()"
                        :placeholder="$t('PleaseEnterZipCode')"
                        size="small"
                        style="width: 100%"
                      ></el-input>
                    </el-form-item>
                  </el-col>
                  <el-col :span="12">
                    <el-form-item :label="$t('DoorNo')" prop="house_number">
                      <el-input
                        v-model="employees.house_number"
                        @change="getLocation()"
                        :placeholder="$t('PleaseEnterDoorNo')"
                        style="width: 100%"
                        size="small"
                      ></el-input>
                    </el-form-item>
                  </el-col>
                  <el-col :span="12">
                    <el-form-item :label="$t('City')" prop="city">
                      <el-input
                        v-model="employees.city"
                        :disabled="disabledBtnByCountry"
                        @change="getLocation()"
                        :placeholder="$t('PleaseEnterCity')"
                        style="width: 100%"
                        size="small"
                      ></el-input>
                    </el-form-item>
                  </el-col>
                  <el-col :span="12">
                    <el-form-item :label="$t('Street')" prop="street">
                      <el-input
                        v-model="employees.street"
                        :disabled="disabledBtnByCountry"
                        @change="getLocation()"
                        :placeholder="$t('PleaseEnterStreet')"
                        style="width: 100%"
                        size="small"
                      ></el-input>
                    </el-form-item>
                  </el-col>
                </el-row>
              </div> -->
              <el-form-item :label="$t('address')" prop="address">
                <el-input
                  size="small"
                  v-model="employees.address"
                  style="width: 70%"
                >
                  <el-button
                    slot="append"
                    @click="selectAddressMap"
                    class="locale-btn"
                  >
                    <i class="el-icon-map-location" />{{ $t("positioning") }}
                  </el-button>
                </el-input>
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <div class="avatar-box">
                <el-form-item label=" " ref="avatar">
                  <!-- <div class="avatar_desc">{{ $t("图像") + ":" }}</div> -->
                  <el-upload
                    class="avatar-uploader"
                    :headers="Authorization"
                    :on-success="handleAvatarSuccess"
                    :action="BaseApiFile"
                    :on-remove="handlerClearAvatar"
                    :file-list="fileList"
                    :limit="1"
                    name="image"
                    :data="{
                      dir: 'merchant',
                    }"
                  >
                    <img
                      v-if="this.employees.avatar"
                      :src="this.employees.avatar"
                      class="avatar"
                    />
                    <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                  </el-upload>
                </el-form-item>
              </div>
            </el-col>
          </el-row>
          <div class="label-info">{{ $t("logInInfo") }}</div>
          <el-row :gutter="20">
            <el-col :span="12">
              <!-- 账号 -->
              <el-form-item :label="$t('StaffAccount')" prop="email">
                <el-input
                  size="small"
                  v-model="employees.email"
                  :placeholder="$t('PleaseEnterTheStaffAccount')"
                  auto-complete="off"
                  style="width: 70%"
                ></el-input>
              </el-form-item>
              <!-- 密码 -->
              <el-form-item :label="$t('Password')" prop="password" v-if="edit">
                <el-input
                  size="small"
                  v-model="employees.password"
                  :placeholder="$t('PleaseEnterPassword')"
                  auto-complete="off"
                  style="width: 70%"
                ></el-input>
              </el-form-item>
              <!-- 权限组 -->
              <el-form-item :label="$t('permissionGroup')" prop="role_id">
                <el-select
                  size="small"
                  v-model="employees.role_id"
                  filterable
                  :placeholder="$t('PleaseSelectPermissionGroup')"
                  style="width: 70%"
                >
                  <el-option
                    v-for="(item, index) in roleList"
                    :key="index"
                    :label="item.name"
                    :value="item.id"
                  ></el-option>
                </el-select>
              </el-form-item>
            </el-col>
          </el-row>
        </div>
      </el-form>
      <div class="footer-botton">
        <el-button
          type="primary"
          @click="onSubmit"
          class="submit"
          :loading="this.$store.state.config.button_loading"
          >{{ $t("Save") }}</el-button
        >
      </div>
    </div>
    <!-- 地图弹出框 -->
    <el-dialog
      :title="$t('findAddress')"
      :close-on-click-modal="false"
      :visible.sync="select_map"
      width="90%"
    >
      <b-map
        class="select-map dialog-wrapper"
        v-if="mapType === 'baidu'"
        @addressSelected="handlerAddressSelected"
      ></b-map>
      <google-map
        class="select-map dialog-wrapper"
        v-else
        @addressSelected="handlerAddressSelected"
      ></google-map>
    </el-dialog>
  </div>
</template>

<script>
import baseApi from "@/api/axios/base_api";
import googleMap from "@/components/Map/GoogleMap";
import BMap from "@/components/BaiduMap/getAddressByMap";
export default {
  components: {
    baseApi,
    googleMap,
    BMap,
  },
  data() {
    return {
      // 树形结构字段转换
      optionProps: {
        checkStrictly: true,
        value: "id",
        label: "name",
        children: "children",
      },
      template_id: localStorage.getItem("address_template_id") || 1,
      country:
        localStorage.getItem("country") &&
        JSON.parse(localStorage.getItem("country")).en,
      warehouseList: [],
      employees: {
        fullname: "", //姓名
        warehouse_id: null, //所属网点
        phone: "", //联系电话
        address: "", //地址
        avatar: "", //图片
        email: "", //账号
        post_code: "", //邮编
        house_number: "", //门牌号
        city: "", //城市
        street: "", //街道
        lon: "", //经度
        lat: "", //纬度
        password: "12345678", //密码
        role_id: "", //权限组
      },

      rules: {
        //姓名
        fullname: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        //电话
        phone: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        //账号
        email: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        //密码
        password: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        //权限组
        role_id: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
        // 所属网点
        warehouse_id: [
          {
            required: true,
            message: this.$t("Required"),
            trigger: "blur",
          },
        ],
      },
      fileList: [],
      roleList: [], //权限组
      MerchantGroups: [], //所属网点
      select_map: false, //地图
      edit: true, //判断密码是修改隐藏
    };
  },
  created() {
    this.getNetworkTree(),
      // this.getWarehouse();
      this.getRolePublic(); // 获取权限列表
    if (this.isEdit) {
      this.edit = false;
      this.getEmployeesDetails(this.$route.query.id);
    }
  },
  computed: {
    disabledBtnByCountry() {
      return this.country === "NL";
    },
    mapType() {
      return this.$store.state.config.maptype;
    },
    isEdit() {
      return !!this.$route.query.id;
    },
    Authorization() {
      return { Authorization: this.$store.state.token.token };
    },
    BaseApiFile() {
      return `${baseApi}admin/upload/image/`;
    },
  },
  methods: {
    getLocation() {
      console.log(this.country, "this.country");
      if (
        this.employees.post_code &&
        this.employees.house_number &&
        (this.country === "NL" ||
          (this.employees.city && this.employees.street))
      ) {
        let isNL = false;
        if (this.country === "NL") {
          this.employees.city = "";
          this.employees.street = "";
          isNL = true;
        }
        this.$api
          .getLocation({
            post_code: this.employees.post_code,
            house_number: this.employees.house_number,
          })
          .then((res) => {
            if (!res) return;
            this.employees.lat = res.data.lat;
            this.employees.lon = res.data.lon;
            if (isNL) {
              this.employees.city = res.data.city;
              this.employees.street = res.data.street;
            }
          });
      } else if (
        this.employees.post_code &&
        this.employees.house_number &&
        this.employees.city &&
        this.employees.street
      ) {
        this.$api
          .getLocation({
            post_code: this.employees.post_code,
            house_number: this.employees.house_number,
            city: this.employees.city,
            street: this.employees.street,
          })
          .then((res) => {
            if (!res) return;
            this.employees.lat = res.data.lat;
            this.employees.lon = res.data.lon;
          });
      }
    },
    // 网点树形结构
    getNetworkTree() {
      this.$api.getNetworkTree().then((res) => {
        this.warehouseList = res.data;
      });
    },
    // getWarehouse() {
    //   // 仓库列表
    //   this.$api.getWarehouse().then((res) => {
    //     this.warehouseList = res.data.data;
    //   });
    // },
    // 获取权限列表
    getRolePublic() {
      this.$api.getRolePublic().then((res) => {
        this.roleList = res.data.data;
      });
    },
    // 联系地址
    selectAddressMap() {
      this.select_map = true;
    },
    // 获取地图的定位信息
    handlerAddressSelected(data) {
      this.employees.address = data.address;
      this.select_map = false;
    },
    getEmployeesDetails(id) {
      this.$api.getEmployeesDetails({ id: id }).then((res) => {
        this.employees = { ...res.data };
        // this.employees.group_id = 1;
      });
    },
    // 跳转
    transformRouter() {
      this.$router.replace({
        name: "employeeManagement",
        params: {
          employeeManagement: 1,
        },
      });
    },
    handlerClearAvatar() {
      this.fileList = [];
      this.employees.avatar = "";
    },
    handleAvatarSuccess(response) {
      if (response.code === 200) {
        this.employees.avatar = response.data.path;
        this.$refs.avatar.clearValidate();
      }
    },
    // 保存
    onSubmit() {
      this.$refs.employeeForm.validate((valid) => {
        if (!valid) return;
        this.employees.username = this.employees.email;
        let arr = [];
        let end = "";
        if (this.employees.warehouse_id instanceof Array) {
          arr = this.employees.warehouse_id;
          end = arr[arr.length - 1]; //网点选中项
        } else {
          end = this.employees.warehouse_id; //网点选中项
        }
        if (this.isEdit) {
          this.$api
            .putEmployees({
              ...this.employees,
              warehouse_id: end,
              id: this.$route.query.id,
            })
            .then((res) => {
              if (!res) return;
              this.transformRouter();
            });
        } else {
          this.$api
            .addEmployees({
              ...this.employees,
              warehouse_id: end,
            })
            .then((res) => {
              if (!res) return;
              this.transformRouter();
            });
        }
      });
    },
  },
};
</script>

<style lang="less" scoped>
.add-staff {
  background-color: #fff;
  padding: 20px;
  min-height: calc(100vh - 155px);
  .form-main {
    .form-box {
      .label-info {
        font-size: 15px;
        font-weight: 500;
        margin: 10px 30px;
      }
    }
    .footer-botton {
      margin-left: 150px;
      .el-button {
        padding-left: 40px;
        padding-right: 40px;
      }
    }
  }
}
</style>
