<template>
  <!-- 客户地址 -->
  <div class="customer-management">
    <el-tabs type="border-card" v-model="tab" @tab-click="handleClicktab">
      <el-tab-pane
        :label="this.$t('Sender')"
        :name="'1'"
        v-model="pageParams.type"
      ></el-tab-pane>
      <el-tab-pane
        :label="this.$t('Receiver')"
        :name="'2'"
        v-model="pageParams.type"
      ></el-tab-pane>
    </el-tabs>
    <div class="conditional">
      <div>
        <el-input
          size="small"
          clearable
          :placeholder="$t('pleaseEnterName')"
          @keyup.enter.native="handleChangePageparams"
          v-model="contacts"
          class="search-bar"
        ></el-input>
        <!-- 邮箱 -->
        <!-- <el-col :span="4" :offset="1">
            <el-input size="small" clearable :placeholder="$t('PleaseEnterZipCode')" @keyup.enter.native="handleChangePageparams" @clear="handleChangePageparams" v-model="contacts_code"></el-input>
          </el-col> -->
        <el-select
          size="small"
          v-model="pageParams.merchant_id"
          clearable
          :placeholder="$t('PleaseSelectMerchant')"
          @keyup.enter.native="handleChangePageparams"
          class="search-bar"
        >
          <el-option
            v-for="item in merchantList"
            :label="item.name"
            :value="item.id"
            :key="item.id"
          >
          </el-option>
        </el-select>
        <!-- 查询 -->
        <el-button
          size="small"
          @click="handleChangePageparams"
          type="primary"
          :loading="this.$store.state.config.button_loading"
          >{{ $t("Search") }}</el-button
        >
      </div>
      <div class="but">
        <!-- 新增 -->
        <el-tooltip
          :content="$t('AdministratorActivate')"
          placement="top"
          :disabled="hasPerm('address.export') ? true : false"
        >
          <div>
            <el-button
              :disabled="!hasPerm('address.export')"
              type="primary"
              class="new-create but"
              size="small"
              @click="addContacter"
              >{{ $t("Add") }}</el-button
            >
          </div>
        </el-tooltip>

        <!-- 导出 -->
        <el-tooltip
          :content="$t('AdministratorActivate')"
          placement="top"
          :disabled="hasPerm('address.export') ? true : false"
        >
          <div>
            <el-button
              :disabled="!hasPerm('address.export')"
              :class="[
                hasPerm('address.export') ? 'common' : 'common-disabled',
                'but',
              ]"
              size="small"
              :loading="this.$store.state.config.button_loading"
              @click="handlerExportClick"
              >{{ $t("Export") }}</el-button
            >
          </div>
        </el-tooltip>
        <!-- 导入 -->
        <el-tooltip
          :content="$t('AdministratorActivate')"
          placement="top"
          :disabled="hasPerm('address.import') ? true : false"
        >
          <div>
            <el-button
              :disabled="!hasPerm('address.import')"
              :class="[
                hasPerm('address.import') ? 'common' : 'common-disabled',
                'but',
              ]"
              size="small"
              @click="upload"
              >{{ $t("ToChannel") }}</el-button
            >
          </div>
        </el-tooltip>
      </div>
    </div>
    <!-- <el-tab-pane :name="$t('Receiver')" :label="$t('Receiver')"> -->
    <el-table
      height="calc(100vh - 283px)"
      :data="hasPerm('address.index') ? tableData : []"
      v-loading="global_loading()"
      element-loading-text="loading..."
      @selection-change="handleSelectionChange"
      border
      style="width: 98%; margin: 0 auto"
      :cell-style="{ padding: '5px 0px' }"
    >
      <template slot="empty" v-if="!hasPerm('address.index')">
        <div style="font-size: 18px">
          {{ $t("AdministratorActivate") }}
        </div>
      </template>
      <!-- 多选框 -->
      <el-table-column align="center" type="selection" width="50">
      </el-table-column>
      <!-- 序号 -->
      <!-- <el-table-column
        type="index"
        header-align="center"
        align="center"
        width="80"
      >
        <template slot="header">
          <span>{{ $t("Number") }}</span>
        </template>
      </el-table-column> -->
      <el-table-column
        header-align="center"
        align="center"
        prop="date"
        :label="$t('Operation')"
        width="120"
      >
        <template slot-scope="scope">
          <el-dropdown size="mini" trigger="hover" placement="bottom">
            <el-button size="mini" type="primary" plain class="operation">
              {{ $t("Operation") }}
              <i class="el-icon-arrow-down el-icon--right"></i>
            </el-button>
            <el-dropdown-menu slot="dropdown">
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('address.update') ? true : false"
              >
                <div
                  :style="
                    hasPerm('address.update') ? '' : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    @click.native="editContacter(scope.row.id)"
                    :disabled="!hasPerm('address.update')"
                  >
                    <el-button
                      :disabled="!hasPerm('address.update')"
                      class="operation"
                      size="small"
                      type="text"
                      >{{ $t("Edit") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('address.destroy') ? true : false"
              >
                <div
                  :style="
                    hasPerm('address.destroy') ? '' : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('address.destroy')"
                    @click="delContacter(scope.row.id)"
                  >
                    <el-button
                      class="operation"
                      :disabled="!hasPerm('address.destroy')"
                      @click="delContacter(scope.row.id)"
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
      <el-table-column
        header-align="center"
        align="center"
        prop="place_fullname"
        :label="$t('Name')"
      >
      </el-table-column>
      <!-- 电话 -->
      <el-table-column
        header-align="center"
        align="center"
        prop="place_phone"
        :label="$t('Tel')"
      >
      </el-table-column>
      <!-- 国家 -->
      <el-table-column
        header-align="center"
        align="center"
        prop="place_country_name"
        :label="$t('Country')"
      >
      </el-table-column>
      <div v-if="template_id == 1">
        <!-- 街道 -->
        <!-- <el-table-column
          header-align="center"
          align="center"
          prop="place_street"
          :label="$t('Street')"
        >
        </el-table-column> -->
        <!-- 门牌号 -->
        <!-- <el-table-column
          header-align="center"
          align="center"
          prop="place_house_number"
          :label="$t('DoorNo')"
        >
        </el-table-column> -->
        <!-- 邮编 -->
        <!-- <el-table-column
          header-align="center"
          align="center"
          prop="place_post_code"
          :label="$t('ZipCode')"
        >
        </el-table-column> -->
        <!-- 城市 -->
        <!-- <el-table-column
          header-align="center"
          align="center"
          prop="place_city"
          :label="$t('City')"
        >
        </el-table-column> -->
      </div>
      <!-- 详细地址 -->
      <el-table-column
        header-align="center"
        align="center"
        prop="place_address"
        :label="$t('DetailedAddress')"
        width="350px"
      >
      </el-table-column>
      <!-- 货主名称 -->
      <el-table-column
        header-align="center"
        align="center"
        prop="merchant_id_name"
        :label="$t('Merchantinquiry')"
      >
      </el-table-column>
    </el-table>
    <!-- 客户地图 -->
    <div class="footer-btn">
      <el-button
        type="primary"
        class="footer_left_btn"
        size="small"
        @click="toMap"
        >{{ $t("CustomerMap") }}</el-button
      >
      <base-pagination
        class="pagination"
        :pageParams="pageParams"
        @search="getOrder"
      ></base-pagination>
    </div>

    <!-- </el-tab-pane> -->

    <!-- <el-tab-pane :name="$t('Sender')" :label="$t('Sender')">
        <el-table
 max-height="600px"          :data="tableData"
          v-loading="global_loading()"
          element-loading-text="loading..."
          border
          style="width: 100%">
          <el-table-column
          header-align="center"
          align="center"
          type="index"
          width="80">
            <template slot="header">
              <span>{{$t('Number')}}</span>
            </template>
          </el-table-column>
          <el-table-column
            header-align="center"
            align="center"
            prop="date"
            :label="$t('Operation')"
            width="120">
            <template slot-scope="scope">
                <el-dropdown size="mini" trigger="hover" placement="bottom">
                    <el-button size="mini" type="primary">
                        {{$t('Operation')}}
                    </el-button>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item>
                            <el-button class="operation" @click="editContacter(scope.row.id)" size="small" type="text">{{$t('Edit')}}</el-button>
                        </el-dropdown-item>
                        <el-dropdown-item>
                            <el-button class="operation" @click="delContacter(scope.row.id)" size="small" type="text">{{$t('Delete')}}</el-button>
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </template>
          </el-table-column>
          <el-table-column
            header-align="center"
            align="center"
            prop="second_place_fullname"
            :label="$t('Name')">
          </el-table-column>
          <el-table-column
            header-align="center"
            align="center"
            prop="second_place_phone"
            :label="$t('Tel')">
          </el-table-column>
          <el-table-column
            header-align="center"
            align="center"
            prop="second_place_country_name"
            :label="$t('Country')">
          </el-table-column>
          <div v-if="template_id == 1">
            <el-table-column
              header-align="center"
              align="center"
              prop="second_place_street"
              :label="$t('Street')">
            </el-table-column>
            <el-table-column
              header-align="center"
              align="center"
              prop="second_place_house_number"
              :label="$t('DoorNo')">
            </el-table-column>
            <el-table-column
              header-align="center"
              align="center"
              prop="second_place_post_code"
              :label="$t('ZipCode')">
            </el-table-column>
            <el-table-column
              header-align="center"
              align="center"
              prop="second_place_city"
              :label="$t('City')">
            </el-table-column>
          </div>
          <el-table-column
            header-align="center"
            align="center"
            prop="second_place_address"
            :label="$t('DetailedAddress')">
          </el-table-column>
          <el-table-column
            header-align="center"
            align="center"
            prop="merchant_id_name"
            :label="$t('Merchant')">
          </el-table-column>
        </el-table>
        <base-pagination class="pagination" :pageParams="pageParams" @search="getOrder"></base-pagination>
      </el-tab-pane>
    </el-tabs> -->
    <!-- 导出 -->
    <iframe frameborder="0" name="downExcel" style="display: none"></iframe>
  </div>
</template>

<script>
import BasePagination from "@/components/BasePagination/BasePagination";
import loading from "@/mixin/global_loading";

export default {
  name: "CustomerManagement",
  components: {
    BasePagination,
  },
  mixins: [loading],
  data() {
    return {
      orderList: [
        { name: this.$t("Receiver"), id: 0 },
        { name: this.$t("Sender"), id: 1 },
      ],
      tab: "1",
      tableData: [],
      merchantList: [], //货主
      multipleSelection: [], //选中
      contacts: null,
      contacts_code: null,
      pageParams: {
        merchant_id: null,
        status: "",
        group_id: "",
        is_lock: "",
        begin_date: "",
        end_date: "",
        from_source: "",
        keyword: "",
        total: 0,
        page: 1,
        per_page: 20,
        type: 1,
      },
      edit: false,
      list: [],
      // 模板id
      template_id: localStorage.getItem("address_template_id") || 1,
    };
  },
  activated() {
    if (this.$route.params.CustomerManagement) {
      this.getOrder();
    }
  },
  created() {
    this.getOrder();
    this.getMerchantsList();
  },
  // watch: {
  //   tab: {
  //     handler() {
  //       this.tableData = [];
  //       this.contacts = null;
  //       this.contacts_code = null;
  //     },
  //   },
  // },
  methods: {
    handleClicktab() {
      if (this.tab == 1) {
        this.$set(this.pageParams, "type", 1);
      } else {
        this.$set(this.pageParams, "type", 2);
      }
      this.getOrder();
    },
    // 导入
    upload() {
      this.$router.push({
        name: "upload",
      });
    },
    // 批量导出
    handlerExportClick() {
      this.$confirm(this.$t("AreSureWantExport"), this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      }).then(() => {
        // var data = this.multipleSelection;
        // if (!data.length) {
        //   this.$message({
        //     type: "error",
        //     message: this.$t("pleaseSelectOrder"),
        //   });
        //   return;
        // }
        // let ids = data.map((el) => {
        //   return el.id;
        // });
        this.$api
          .exportCustomerManagement({
            place_fullname: this.contacts,
            ...this.pageParams,
          })
          .then((res) => {
            if (!res) return;
            let iframe = window.frames["downExcel"];
            let href = res.data.path; //接口路径地址，返回数据类型为application/binary，后台控制显示信息，前端仅为下载功能
            iframe.location.href = href;
            this.getOrder();
          });
      });
    },
    // 客户地图
    toMap() {
      var data = this.multipleSelection;
      if (!data.length) {
        this.$message({
          type: "error",
          message: this.$t("PleaseSelectLeastMessage"),
        });
        return;
      } else {
        this.$router.push({
          name: "CustomerGoogle",
          query: {
            list: this.multipleSelection,
          },
        });
      }
    },
    // 列表选中
    handleSelectionChange(val) {
      this.multipleSelection = val;
    },
    getOrder() {
      // 列表
      if (this.tab === this.$t("1")) {
        this.$api
          .getReceiverAddress({
            place_fullname: this.contacts,
            place_post_code: this.contacts_code,
            ...this.pageParams,
          })
          .then((res) => {
            this.tableData = res.data.data;
            // this.pageParams.page = 1;
            this.pageParams.total = res.data.meta.total;
          });
      } else {
        this.$api
          .getSenderAdress({
            place_fullname: this.contacts,
            place_post_code: this.contacts_code,
            ...this.pageParams,
          })
          .then((res) => {
            this.tableData = res.data.data;
            // this.pageParams.page = 1;
            this.pageParams.total = res.data.meta.total;
          });
      }
    },
    // 分页
    handleChangePageparams() {
      if (this.pageParams.type == 1) {
        this.pageParams.page = 1;
        this.getOrder();
      } else if (this.pageParams.type == 2) {
        this.pageParams.page = 1;
        this.getOrder();
      }
    },
    // 货主信息
    getMerchantsList() {
      let pageParam = {
        status: "",
        begin_date: "",
        end_date: "",
        keyword: "",
        total: 0,
        page: 1,
        per_page: 200,
      };
      this.$api.getMerchantsPublic(pageParam).then((res) => {
        this.merchantList = res.data.data;
      });
    },
    // 新建
    addContacter() {
      if (this.tab === this.$t("1")) {
        this.$router.push({
          name: "addReceiver",
        });
      } else {
        this.$router.push({
          name: "addReceiver",
        });
      }
    },
    // 修改
    editContacter(id) {
      if (this.tab === this.$t("1")) {
        this.$router.push({
          name: "editReceiver",
          query: {
            id,
          },
        });
      } else {
        this.$router.push({
          name: "editReceiver",
          query: {
            id,
          },
        });
      }
    },
    // 删除
    delContacter(id) {
      this.$confirm(this.$t("ConfirmToCancel") + "?", this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      })
        .then(() => {
          if (this.tab === this.$t("1")) {
            this.$api.delReceiverAddress(id).then((res) => {
              if (!res) return;
              this.getOrder();
            });
          } else {
            this.$api.delSenderAdress(id).then((res) => {
              if (!res) return;
              this.getOrder();
            });
          }
        })
        .catch(() => {});
    },
  },
};
</script>

<style lang="less" scoped>
.customer-management {
  border: 1px solid rgba(228, 235, 241, 1);
  background: #ffffff;
  padding: 0px 0px 10px;
  .el-tabs--border-card {
    box-shadow: none;
    border-style: none;
  }
  .conditional {
    margin-top: -30px;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    .but {
      display: flex;
      gap: 5px;
    }
    .new-create {
      display: inline-block;
    }
  }
  .footer-btn {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
    margin-left: 18px;
  }
}
</style>
