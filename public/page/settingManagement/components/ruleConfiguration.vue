<template>
  <!-- 单号规则 -->
  <div class="rule-configuration">
    <!-- <div class="add-warehouse right-angle">
      <el-button size="small" type="primary" @click="addRuleConfig()">{{$t('添加单号规则')}}</el-button>
    </div> -->
    <el-table
 max-height="600px"      v-loading="global_loading()"
      element-loading-text="loading..."
      :data="warehouseList"
      border
      style="width: 100%"
    >
      <el-table-column
        type="index"
        align="center"
        :label="$t('Number')"
        width="50"
      >
      </el-table-column>
      <el-table-column prop="date" align="center" :label="$t('Operation')">
        <template slot-scope="scope">
          <el-dropdown size="mini" trigger="hover" placement="bottom">
            <el-button size="mini" type="primary">
              {{ $t("Operation") }}
            </el-button>
            <el-dropdown-menu slot="dropdown">
              <el-dropdown-item v-if="hasPerm('order-no-rule.update')">
                <el-button
                  class="operation"
                  @click="addRuleConfig(scope.row.id)"
                  size="small"
                  type="text"
                  >{{ $t("Edit") }}</el-button
                >
              </el-dropdown-item>
              <!-- <el-dropdown-item>
                        <el-button class="operation" @click="delOrderNoRule(scope.row.id)" size="small" type="text">{{$t('Delete')}}</el-button>
                    </el-dropdown-item> -->
            </el-dropdown-menu>
          </el-dropdown>
        </template>
      </el-table-column>
      <el-table-column prop="prefix" align="center" :label="$t('开始字符')">
      </el-table-column>
      <el-table-column
        prop="string_length"
        align="center"
        :label="$t('字母位数')"
      >
      </el-table-column>
      <el-table-column prop="int_length" align="center" :label="$t('数字位数')">
      </el-table-column>
      <el-table-column
        prop="max_no"
        align="center"
        :label="$t('可生成最大单号')"
      >
      </el-table-column>
      <el-table-column prop="type_name" align="center" :label="$t('类型')">
      </el-table-column>
    </el-table>
    <base-pagination
      :pageParams="pageParams"
      @search="getOrderNoRuleList"
    ></base-pagination>
  </div>
</template>

<script>
import loading from "@/mixin/global_loading";
import BasePagination from "@/components/BasePagination/BasePagination";
export default {
  mixins: [loading],
  components: {
    BasePagination,
  },
  data() {
    return {
      warehouseList: [],
      loading: false,
      pageParams: {
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
    this.getOrderNoRuleList();
  },
  methods: {
    getOrderNoRuleList() {
      // 规则列表
      this.$api.getOrderNoRuleList(this.pageParams).then((res) => {
        this.warehouseList = res.data.data;
        this.pageParams.total = res.data.meta.total;
      });
    },

    addRuleConfig(id = null) {
      if (id) {
        this.$router.push({ name: "addRuleConfiguration", query: { id: id } });
      } else {
        this.$router.push({ name: "addRuleConfiguration" });
      }
    },

    delOrderNoRule(id) {
      // 规则列表
      this.$api.delOrderNoRule(id).then((res) => {
        this.getOrderNoRuleList();
      });
    },
  },
};
</script>
<style lang="less" scoped>
.rule-configuration {
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
.rule-configuration {
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
