<template>
  <!-- 文章管理 -->
  <div class="article-management">
    <div class="conditional">
      <div>
        <!-- 类型 -->
        <el-select
          size="small"
          clearable
          v-model="pageParams.type"
          @keyup.enter.native="handlerSearch"
          :placeholder="$t('PleaseSelect')"
          class="search-bar"
        >
          <el-option
            v-for="(item, index) in articleTypeList"
            :key="index"
            :label="item.name"
            :value="item.id"
          ></el-option>
        </el-select>

        <!-- 查询 -->
        <el-button
          size="small"
          type="primary"
          @click="getArticle"
          :loading="this.$store.state.config.button_loading"
          >{{ $t("Search") }}</el-button
        >
      </div>
      <div>
        <!-- 新增 -->
        <el-tooltip
          :content="$t('AdministratorActivate')"
          placement="top"
          :disabled="hasPerm('article.store') ? true : false"
        >
          <div>
            <el-button
              :disabled="!hasPerm('article.store')"
              size="small"
              type="primary"
              @click="add"
              >{{ $t("Add") }}</el-button
            >
          </div>
        </el-tooltip>
      </div>
    </div>
    <el-table
      :data="hasPerm('article.index') ? tableData : []"
      element-loading-text="loading..."
      border
      style="width: 100%"
      height="calc(100vh - 300px)"
      :cell-style="{ padding: '5px 0px' }"
    >
      <template slot="empty" v-if="!hasPerm('article.index')">
        <div style="font-size: 18px">
          {{ $t("AdministratorActivate") }}
        </div>
      </template>
      <el-table-column
        type="index"
        header-align="center"
        align="center"
        width="50"
      >
        <template slot="header">
          <span>{{ $t("Number") }}</span>
        </template>
      </el-table-column>
      <el-table-column :label="$t('Operation')" align="center" width="120">
        <template slot-scope="scope">
          <!-- 操作 -->
          <el-dropdown size="mini" trigger="hover" placement="bottom">
            <el-button size="mini" type="primary" plain class="operation">
              {{ $t("Operation") }}
              <i class="el-icon-arrow-down el-icon--right"></i>
            </el-button>
            <el-dropdown-menu slot="dropdown">
              <!-- 编辑 -->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('article.update') ? true : false"
              >
                <div
                  :style="
                    hasPerm('article.update') ? '' : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    :disabled="!hasPerm('article.update')"
                    @click.native="getEdit(scope.row.id)"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('article.update')"
                      >{{ $t("Edit") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
              <!-- 删除-->
              <el-tooltip
                :content="$t('AdministratorActivate')"
                placement="top"
                :disabled="hasPerm('article.destroy') ? true : false"
              >
                <div
                  :style="
                    hasPerm('article.destroy') ? '' : 'cursor: not-allowed'
                  "
                >
                  <el-dropdown-item
                    @click.native="del(scope.row.id)"
                    :disabled="!hasPerm('article.destroy')"
                  >
                    <el-button
                      class="operation"
                      size="small"
                      type="text"
                      :disabled="!hasPerm('article.destroy')"
                      >{{ $t("Delete") }}</el-button
                    >
                  </el-dropdown-item>
                </div>
              </el-tooltip>
            </el-dropdown-menu>
          </el-dropdown>
        </template>
      </el-table-column>
      <!-- 标题 -->
      <el-table-column
        header-align="center"
        align="center"
        prop="tittle"
        :label="$t('title')"
      >
      </el-table-column>
      <!-- 类型 -->
      <el-table-column
        header-align="center"
        align="center"
        prop="type_name"
        :label="$t('MerchantType')"
      >
      </el-table-column>
      <!-- 最后修改人 -->
      <el-table-column
        header-align="center"
        align="center"
        prop="operator_name"
        :label="$t('LastModifiedBy')"
      >
      </el-table-column>
      <!-- 最后修改时间 -->
      <el-table-column
        header-align="center"
        align="center"
        prop="updated_at"
        :label="$t('LastModified')"
      >
      </el-table-column>
    </el-table>
    <base-pagination
      :pageParams="pageParams"
      @search="getArticle"
    ></base-pagination>
  </div>
</template>

<script>
export default {
  data() {
    return {
      pageParams: {
        type: "", //类型
        page: 1,
        per_page: 20,
        total: 0,
      },
      articleTypeList: [],
      tableData: [],
    };
  },
  created() {
    this.getArticle();
    this.getDictionary();
  },
  methods: {
    getDictionary() {
      this.$api.getDictionary().then((res) => {
        this.articleTypeList = res.data.articleTypeList;
        console.log('articleTypeListjljl');
        console.log(res.data.articleTypeList);
      });
    },
    getArticle() {
      this.$api.getArticle(this.pageParams).then((res) => {
        this.tableData = res.data.data;
        this.pageParams.total = res.data.meta.total;
      });
    },
    add() {
      this.$router.push({
        name: "AddArticleManagement",
      });
    },
    getEdit(id) {
      this.$router.push({
        name: "AddArticleManagement",
        query: {
          id: id,
        },
      });
    },
    del(id) {
      this.$api.delArticl(id).then((res) => {
        if (res.code == 200) {
          this.getArticle();
        }
      });
    },
  },
};
</script>
<style lang="less" scoped>
.article-management {
  padding: 0px 20px;
  .conditional {
    display: flex;
    justify-content: space-between;
  }
  .el-table {
    margin: 10px 0px;
  }
}
</style>