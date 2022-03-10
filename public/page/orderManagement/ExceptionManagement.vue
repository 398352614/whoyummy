<template>
<!-- 没有用到 -->
  <div class="dispatch-order">
    <el-tabs v-model="tab">
      <div class="conditional">
        <el-row :gutter="10">
          <el-col :span="6">
            <el-date-picker
              v-model="param.begin_date"
              type="date"
              placeholder="开始日期">
            </el-date-picker>
          </el-col>
          <el-col :span="6">
            <el-date-picker
              v-model="param.end_date"
              type="date"
              placeholder="开始日期">
            </el-date-picker>
          </el-col>
          <el-col :span="6" :offset="6" class="search">
            <el-input placeholder="请输入订单号/识别号" v-model="value1">
              <el-button slot="append" type="primary" >搜索</el-button>
            </el-input>
          </el-col>
        </el-row>
      </div>
      <el-tab-pane v-for="(item, index) in orderList" :label="item.name" :name="item.name + ''" :key="index">
        <el-table
 max-height="600px"          :data="tableData"
          style="width: 100%">
          <el-table-column
          type="selection"
          width="50">
          </el-table-column>
          <el-table-column
            prop="date"
            label="操作"
            width="180">
            <template>
                <el-dropdown size="mini" trigger="click" placement="bottom">
                    <el-button size="mini">
                        操作<i class="el-icon-arrow-down el-icon--right"></i>
                    </el-button>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item>
                            <el-button class="operation" size="small" type="text">详细</el-button>
                        </el-dropdown-item>
                        <el-dropdown-item>
                            <el-button class="operation" size="small" type="text">查看轨迹</el-button>
                        </el-dropdown-item>
                        <el-dropdown-item>
                            <el-button class="operation" size="small" type="text">操作日志</el-button>
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </template>
          </el-table-column>
          <el-table-column
            prop="name"
            label="状态"
            width="180">
          </el-table-column>
          <el-table-column
            prop="address"
            label="异常状态">
          </el-table-column>
          <el-table-column
            prop="address"
            label="订单号">
          </el-table-column>
          <el-table-column
            prop="address"
            label="订单来源">
          </el-table-column>
          <el-table-column
            prop="address"
            label="外部订单号">
          </el-table-column>
          <el-table-column
            prop="address"
            label="邮编">
          </el-table-column>
          <el-table-column
            prop="address"
            label="门牌号">
          </el-table-column>
          <el-table-column
            prop="address"
            label="派送日期">
          </el-table-column>
          <el-table-column
            prop="address"
            label="派送司机">
          </el-table-column>
          <el-table-column
            prop="address"
            label="站点编号">
          </el-table-column>
          <el-table-column
            prop="address"
            label="取件线路编号">
          </el-table-column>
          <el-table-column
            prop="address"
            label="创建时间">
          </el-table-column>
        </el-table>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script>
export default {
  name: 'ExceptionManagement',
  data() {
    return {
      name: '全部',
      orderList: [
        { name: '全部', id: null, code: '' },
        { name: '未取派', id: 1, code: '' },
        { name: '已分配', id: 2, code: '' },
        { name: '待出库', id: null, code: '' },
        { name: '取派中', id: 3, code: '' },
        { name: '已签收', id: 4, code: '' },
        { name: '取派失败', id: null, code: '' },
        { name: '异常订单', id: 5, code: '' },
        { name: '回收站', id: 6, code: '' },
      ],
      tab: '全部',
      tableData: [
        {
          address: 'a',
        },
      ],
      value1: '',
      param: {
        status: '',
        type: '2',
        begin_date: '',
        end_date: '',
      },
    };
  },
  created() {
    this.getInitPieIndex();
    this.getOrder();
  },
};
</script>

<style lang="less" scoped>
.dispatch-order {
  padding: 20px 25px 10px;
  background: #ffffff;
  border: 1px solid rgba(228, 235, 241, 1);
  .conditional {
    padding: 5px 0 13px;
    .search {
      .el-input-group__append {
        .el-button .el-button--primary {
          background: #28D094;
          color: #ffffff;
        }
      }
    }
  }
}
</style>
