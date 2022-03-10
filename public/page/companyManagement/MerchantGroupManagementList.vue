<template>
  <div class="content_box">
    <!-- 货主组列表 -->
    <div class="search">
      <div>
        <el-input
          clearable
          @keyup.enter.native="selectChange"
          size="small"
          v-model="pageParams.name"
          :placeholder="$t('groupName')"
          class="search-bar"
        >
        </el-input>
        <el-button
          type="primary"
          size="small"
          @click="selectChange"
          :loading="this.$store.state.config.button_loading"
          >{{ $t("Search") }}
        </el-button>
      </div>
      <el-tooltip
        :content="$t('AdministratorActivate')"
        placement="top"
        :disabled="hasPerm('merchant-group.store') ? true : false"
      >
        <div>
          <el-button
            :disabled="!hasPerm('merchant-group.store')"
            type="primary"
            size="small"
            @click="handlerAddClick"
            >{{ $t("Add") }}</el-button
          >
        </div>
      </el-tooltip>
    </div>
    <el-table
      height="calc(100vh - 242px)"
      :data="hasPerm('merchant-group.index') ? list : []"
      v-loading="global_loading()"
      element-loading-text="loading…"
      @selection-change="handleSelectionChange"
      border
      :cell-style="{ padding: '5px 0px' }"
    >
      <template slot="empty" v-if="!hasPerm('merchant-group.index')">
        <div style="font-size: 18px">
          {{ $t("AdministratorActivate") }}
        </div>
      </template>
      <el-table-column
        type="selection"
        width="55"
        align="center"
        header-align="center"
      >
      </el-table-column>
      <el-table-column
        :label="$t('Operation')"
        align="center"
        header-align="center"
      >
        <template slot-scope="scope">
          <el-dropdown>
            <el-button size="small" type="primary" plain class="operation">
              {{ $t("Operation") }}
              <i class="el-icon-arrow-down el-icon--right"></i>
            </el-button>
            <el-dropdown-menu slot="dropdown">
              <!-- 编辑 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('merchant-group.update') ? true : false"
              >
                <div
                  :style="
                    hasPerm('merchant-group.update')
                      ? ''
                      : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('merchant-group.update')"
                    @click.native="handlerEdit(scope.row)"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('merchant-group.update')"
                    >
                      {{ $t("Edit") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 编辑权限 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('merchant-group.config') ? true : false"
              >
                <div
                  :style="
                    hasPerm('merchant-group.config')
                      ? ''
                      : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('merchant-group.config')"
                    @click.native="editJurisdiction(scope.row)"
                  >
                    <el-button
                      :disabled="!hasPerm('merchant-group.config')"
                      class="operation"
                      size="small"
                      type="text"
                    >
                      {{ $t("EditJurisdiction") }}</el-button
                    ></el-dropdown-item
                  >
                </div>
              </el-tooltip>
              <!-- <el-dropdown-item @click.native="showMember(scope.row)">{{
                $t("showMenbers")
              }}</el-dropdown-item> -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('merchant-group.status') ? true : false"
              >
                <div
                  :style="
                    hasPerm('merchant-group.status')
                      ? ''
                      : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('merchant-group.status')"
                    @click.native="openMerchanDialog(scope.row)"
                  >
                    <el-button
                      :disabled="!hasPerm('merchant-group.status')"
                      class="operation"
                      size="small"
                      type="text"
                    >
                      {{ $t("DisableEnable") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('merchant-group.destroy') ? true : false"
              >
                <div
                  :style="
                    hasPerm('merchant-group.destroy')
                      ? ''
                      : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('merchant-group.destroy')"
                    @click.native="handlerDel(scope.row)"
                  >
                    <el-button
                      :disabled="!hasPerm('merchant-group.destroy')"
                      class="operation"
                      size="small"
                      type="text"
                    >
                      {{ $t("Delete") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
            </el-dropdown-menu>
          </el-dropdown>
        </template>
      </el-table-column>
      <el-table-column
        prop="name"
        :label="$t('groupName')"
        align="center"
        header-align="center"
      >
      </el-table-column>
      <el-table-column
        prop="count"
        :label="$t('groupMember')"
        align="center"
        header-align="center"
      >
      </el-table-column>
      <el-table-column
        prop="transport_price_name"
        :label="$t('groupFreightRateScheme')"
        align="center"
        header-align="center"
      >
      </el-table-column>
      <el-table-column
        :label="$t('defaultGroup')"
        align="center"
        header-align="center"
      >
        <template slot-scope="scope">
          {{ scope.row.is_default === 1 ? $t("Yes") : $t("No") }}
        </template>
      </el-table-column>
      <el-table-column
        prop="created_at"
        :label="$t('CreationTime')"
        align="center"
        header-align="center"
      >
      </el-table-column>
    </el-table>

    <div class="footer_btn">
      <el-tooltip
        :content="$t('AdministratorActivate')"
        placement="top"
        :disabled="hasPerm('merchant-group.transport-price') ? true : false"
      >
        <div>
          <el-button
            :class="[
              hasPerm('merchant-group.transport-price')
                ? 'footer_left_btn'
                : 'common-disabled',
              ,
            ]"
            size="mini"
            type="primary"
            @click="handlerSetting"
            :disabled="
              !multipleSelection.length &&
              !hasPerm('merchant-group.transport-price')
            "
            >{{ $t("BatchPriceSetting") }}</el-button
          >
        </div>
      </el-tooltip>
      <base-pagination
        class="footer_right_btn"
        ref="searchRef"
        :pageParams="pageParams"
        @search="getList"
      ></base-pagination>
    </div>

    <el-dialog
      :title="this.$t('DisableEnable')"
      :visible.sync="show_merchan_dialog"
    >
      <el-table
        max-height="600px"
        :data="merchanList"
        @selection-change="handleSelectionMerchanChange"
        border
      >
        <el-table-column
          type="selection"
          width="55"
          align="center"
          header-align="center"
        >
        </el-table-column>
        <el-table-column
          type="index"
          label="#"
          width="55"
          align="center"
          header-align="center"
        >
        </el-table-column>
        <el-table-column
          prop="type"
          :label="$t('MerchantType')"
          align="center"
          header-align="center"
        >
          <template slot-scope="scope">
            {{ scope.row.type === 1 ? $t("personal") : $t("business") }}
          </template>
        </el-table-column>
        <el-table-column
          prop="email"
          :label="$t('Email')"
          align="center"
          header-align="center"
        >
        </el-table-column>
        <el-table-column
          prop="name"
          :label="$t('MerchantGroup')"
          align="center"
          header-align="center"
          width="180"
        >
        </el-table-column>
        <el-table-column
          prop="contacter"
          :label="$t('ContactName')"
          align="center"
          header-align="center"
          width="150"
        >
        </el-table-column>
        <el-table-column
          prop="phone"
          :label="$t('Tel')"
          align="center"
          header-align="center"
          width="120"
        >
        </el-table-column>
        <el-table-column
          prop="status"
          :label="$t('Status')"
          align="center"
          header-align="center"
        >
          <template slot-scope="scope">
            <i
              v-if="scope.row.status === 2"
              class="el-icon-lock custom_lock"
            ></i>
            {{ scope.row.status === 1 ? $t("enable") : $t("disable") }}
          </template>
        </el-table-column>
      </el-table>
      <base-pagination
        :pageParams="merchanParams"
        @search="getMerchants"
      ></base-pagination>
      <div slot="footer">
        <!-- 禁用 -->
        <el-button
          size="small"
          type="primary"
          @click="editStatus(2)"
          :loading="$store.state.config.button_loading"
          class="footer_left_btn"
          >{{ $t("disable") }}</el-button
        >
        <!-- 启用 -->
        <el-button
          size="small"
          type="primary"
          @click="editStatus(1)"
          :loading="$store.state.config.button_loading"
          class="footer_left_btn"
          >{{ $t("enable") }}</el-button
        >
      </div>
    </el-dialog>

    <el-dialog :title="$t('BatchPriceSetting')" :visible.sync="priceDlg">
      <div>
        <el-form>
          <el-form-item :label="$t('pricingScheme')">
            <el-select size="small" v-model="pricesId" placeholder="">
              <el-option
                v-for="item in prices"
                :key="item.id"
                :label="item.name"
                :value="item.id"
              >
              </el-option>
            </el-select>
          </el-form-item>
        </el-form>
      </div>
      <div slot="footer">
        <el-button
          size="small"
          @click="priceDlg = false"
          type="primary"
          class="cancel"
          >{{ $t("Cancel") }}</el-button
        >
        <el-button
          size="small"
          type="primary"
          @click="omSubmitSetting"
          :loading="$store.state.config.button_loading"
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
  name: "MerchantGroupManagement",
  components: {
    BasePagination,
  },
  mixins: [loading],
  data() {
    return {
      prices: [],
      pricesId: "",
      priceDlg: false,
      multipleSelection: [],
      memberList: [],
      form: {
        name: "",
        transport_price_id: "",
        is_default: 1,
      },
      show_merchan_dialog: false,
      groupDialogVisible: false,
      list: [],
      search_date: ["", ""],
      pageParams: {
        keyword: "",
        page: 1,
        per_page: 20,
        total: 0,
        status: "" || this.$route.query.status,
        begin_date: "",
        end_date: "",
        name: "",
      },
      merchanList: [],
      checkMerchanList: [],
      merchanParams: {
        keyword: "",
        page: 1,
        per_page: 10,
        total: 0,
        merchant_group_id: "",
      },
      drivers: [],
    };
  },
  activated() {
    if (this.$route.params.MerchantGroupManagement) {
      this.getList();
    }
  },
  created() {
    this.getList();
  },
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
        this.getList();
      },
    },
  },
  methods: {
    getTransportPrice() {
      this.$api
        .getTransportPrice({
          per_page: 200,
        })
        .then((res) => {
          if (!res) return;
          this.prices = res.data.data;
        });
    },
    handlerSetting() {
      if (!this.multipleSelection.length) return;
      this.getTransportPrice();
      this.priceDlg = !this.priceDlg;
    },
    omSubmitSetting() {
      if (!this.pricesId) return;
      this.$api
        .multSetPrice({
          ids: JSON.stringify(this.multipleSelection.map((item) => item.id)),
          transport_price_id: this.pricesId,
        })
        .then((res) => {
          if (!res) return;
          this.priceDlg = !this.priceDlg;
          this.getList();
        });
    },
    handlerRemove() {},
    handleSelectionChange(val) {
      this.multipleSelection = val;
    },
    getMemberList(id) {
      this.$api.queryMerchantsMember(id).then((res) => {
        if (!res) return;
        this.memberList = res.data.data;
      });
    },
    handlerDel(row) {
      this.$confirm(this.$t("ConfirmToCancel") + "?", this.$t("Notice"), {
        confirmButtonText: this.$t("Confirm"),
        cancelButtonText: this.$t("Cancel"),
        type: "warning",
      })
        .then(() => {
          this.$api.delMerchantsGroup(row.id).then((res) => {
            if (!res) return;
            this.getList();
          });
        })
        .catch(() => {});
    },
    showMember(row) {
      // this.getMemberList(row.id);
      // this.memberDialogVisible = !this.memberDialogVisible;
      this.$router.push({
        name: "merchantManagement",
        params: {
          id: row.id,
        },
      });
    },
    handlerEdit(row) {
      this.$router.push({
        name: "editMerchantGroup",
        query: {
          id: row.id,
        },
      });
    },
    editJurisdiction(row) {
      // 编辑权限
      this.$router.push({
        name: "EditJurisdiction",
        query: {
          id: row.id,
        },
      });
    },

    // 启用禁用 --开始
    openMerchanDialog(row) {
      this.show_merchan_dialog = !this.show_merchan_dialog;
      this.merchanParams.merchant_group_id = row.id;
      this.getMerchants();
    },
    getMerchants() {
      this.$api.getMerchantsPublic(this.merchanParams).then((res) => {
        this.merchanList = res.data.data;
        this.merchanParams.total = res.data.meta.total;
        this.merchanParams.page = res.data.meta.current_page;
      });
    },
    handleSelectionMerchanChange(val) {
      this.checkMerchanList = val;
    },
    editStatus(status) {
      // 修改状态
      if (this.checkMerchanList.length == 0) return;
      this.$api
        .multMerchantsSetting({
          status: status,
          ids: JSON.stringify(this.checkMerchanList.map((item) => item.id)),
        })
        .then((res) => {
          if (!res) return;
          this.getMerchants();
        });
      // this.$api
      //   .editMerchantStatus({
      //     id: row.id,
      //     status: 1,
      //   })
      //   .then((res) => {
      //     this.getList();
      //   });
    },
    // 启用禁用 --结束

    handlerAddClick() {
      this.$router.push({
        name: "addMerchantGroupManagement",
      });
    },
    selectChange() {
      if (this.pageParams.name) {
        this.pageParams.page = 1;
      }
      this.getList();
    },

    handlerTabClick() {
      this.$route.query.page = 1;
      this.pageParams.page = 1;
      this.$refs.searchRef.onChangeValue("status", this.pageParams.status);
    },
    toStationRecord(row) {
      this.$router.push({
        name: "stationDetail",
        query: {
          id: row.id,
        },
      });
    },
    getList() {
      this.$api.getMerchantsGroup(this.pageParams).then((res) => {
        if (!res) return;
        this.list = res.data.data;
        this.pageParams.total = res.data.meta.total;
        this.pageParams.page = res.data.meta.current_page;
      });
    },
  },
};
</script>
<style lang="less" scoped>
.content_box {
  padding: 20px 25px 10px;
  background-color: #fff;
  position: relative;
  .search {
    margin-bottom: 20px;
    display: flex;
    justify-content: space-between;
  }
  .footer_btn {
    margin-top: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .footer_left_btn {
    width: 142px;
    height: 32px;
  }
}
</style>
