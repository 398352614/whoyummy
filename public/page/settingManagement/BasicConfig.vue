<template>
  <div class="basic-config">
    <el-tabs v-model="tab">
      <!-- tab-one -->
      <el-tab-pane :label="$t('CompanyInfo')" name="1">
        <el-form
          :model="companyInfo"
          :rules="rules"
          ref="companyForm"
          label-position="right"
          label-width="160px"
          class="company-info"
        >
          <el-form-item :label="$t('CompanyName')" prop="name">
            <el-input v-model="companyInfo.name" :placeholder="$t('PleaseEnterCompanyName')"></el-input>
          </el-form-item>
          <el-form-item :label="$t('ContactPerson')" prop="contacts">
            <el-input v-model="companyInfo.contacts" :placeholder="$t('PleaseEnterContactPerson')"></el-input>
          </el-form-item>
          <el-form-item :label="$t('Tel')" prop="phone">
            <el-input v-model="companyInfo.phone" :placeholder="$t('PleaseEnterCompanyTel')"></el-input>
          </el-form-item>
          <!-- <el-form-item :label="$t('Country')" prop="country">
            <el-select v-model="companyInfo.country" filterable :placeholder="$t('PleaseSelectCountry')">
              <el-option
                v-for="(item, index) in address"
                :key="index"
                :label="item.name"
                :value="item.short"
              ></el-option>
            </el-select>
          </el-form-item> -->
          <el-form-item :label="$t('CompanyAaddress')" prop="address">
            <el-input v-model="companyInfo.address" :placeholder="$t('PleaseEnterCompanyAddress')"></el-input>
          </el-form-item>
        </el-form>
        <div class="save">
          <el-button v-if="hasPerm('company-info.update')" type="primary" @click="putCompanyDetails" :loading="this.$store.state.config.button_loading">{{$t('Save')}}</el-button>
        </div>
      </el-tab-pane>

      <!-- tab-two -->
      <el-tab-pane :label="$t('advancedSettings')" name="2">
        <el-form
          :model="senior"
          :rules="seniorRules"
          ref="seniorForm"
          label-position="right"
          label-width="160px"
          class="company-info"
        >
          <el-form-item :label="$t('RouteAssignRules')" prop="line_rule">
            <el-select v-model="senior.line_rule" @visible-change="handleChangeTemplate" @change="handleChangeLineRule" :placeholder="$t('PleaseSelectRouteAssignRules')">
              <el-option
                v-for="(item, index) in line_rule"
                :key="index"
                :label="item.label"
                :value="item.value"
              ></el-option>
            </el-select>
          </el-form-item>

          <div class="template-box show-template-box">
            <!-- 模板一 -->
            <div :class="{'address-template': true, 'address-template-check': senior.address_template_id === 1}">
              <el-form
                :model="template"
                :rules="rules"
                label-position="right"
                label-width="70px"
                class="warehouse-address"
              >
                <el-row>
                  <el-col>
                    <p>{{$t('地址模板一')}}</p>
                  </el-col>
                  <el-col>
                    <el-form-item :label="$t('Name')" prop="name">
                      <el-input v-model="template.name" disabled></el-input>
                    </el-form-item>
                  </el-col>
                  <el-col>
                    <el-form-item :label="$t('Tel')" prop="name">
                      <el-input v-model="template.name" disabled></el-input>
                    </el-form-item>
                  </el-col>
                  <el-col :span="12">
                    <el-form-item :label="$t('ZipCode')" prop="name">
                      <el-input v-model="template.name" disabled></el-input>
                    </el-form-item>
                  </el-col>
                  <el-col :span="12">
                    <el-form-item :label="$t('DoorNo')" prop="name">
                      <el-input v-model="template.name" disabled></el-input>
                    </el-form-item>
                  </el-col>
                  <el-col :span="12">
                    <el-form-item :label="$t('City')" prop="name">
                      <el-input v-model="template.name" disabled></el-input>
                    </el-form-item>
                  </el-col>
                  <el-col :span="12">
                    <el-form-item :label="$t('Street')" prop="name">
                      <el-input v-model="template.name" disabled></el-input>
                    </el-form-item>
                  </el-col>
                </el-row>
                
              </el-form>
              <div class="address-template-radio">
                <el-radio v-model="senior.address_template_id" :label="1">选择该模板</el-radio>
              </div>
            </div>

            <!-- 模板二 -->
            <div :class="{'address-template': true, 'address-template-check': senior.address_template_id === 2}">
              <el-form
                :model="template"
                :rules="rules"
                label-position="right"
                label-width="70px"
                class="warehouse-address"
              >
                <el-row>
                  <el-col>
                    <p>{{$t('地址模板二')}}</p>
                  </el-col>
                  <el-col>
                    <el-form-item :label="$t('Name')" prop="name">
                      <el-input v-model="template.name" disabled></el-input>
                    </el-form-item>
                  </el-col>
                  <el-col>
                    <el-form-item :label="$t('Tel')" prop="name">
                      <el-input v-model="template.name" disabled></el-input>
                    </el-form-item>
                  </el-col>
                  <el-col>
                    <el-form-item :label="$t('Address')" prop="name">
                      <el-input v-model="template.name" disabled>
                        <el-button slot="append" disabled icon="el-icon-location"></el-button>
                      </el-input>
                    </el-form-item>
                  </el-col>
                </el-row>
                
              </el-form>
              <div class="address-template-radio">
               <el-radio v-model="senior.address_template_id" :label="2">选择该模板</el-radio>
              </div>
            </div>
          </div>

          <el-form-item :label="$t('线路分配显示')" prop="show_type">
            <el-radio-group v-model="senior.show_type">
              <el-radio :label="1">{{$t('显示全部')}}</el-radio>
              <el-radio :label="2">{{$t('显示当前所选的方式')}}</el-radio>
            </el-radio-group>
          </el-form-item>
          <el-form-item :label="$t('Country')">
            <el-tag
              :key="tag.id"
              v-for="tag in address"
              closable
              :disable-transitions="false"
              @close="delCountry(tag)">
              {{tag.name}}
            </el-tag>
            <el-button class="add-country" v-if="!address.length" size="small" @click="openCountryDialog"><i class="el-icon-plus"></i>{{$t('AddCountry')}}</el-button>
          </el-form-item>
          <el-form-item :label="$t('WeightUnit')" prop="weight_unit">
            <el-select v-model="senior.weight_unit" :placeholder="$t('PleaseSelectAWeightUnit')">
              <el-option
                v-for="(item, index) in weightList"
                :key="index"
                :label="item.label"
                :value="item.value"
              ></el-option>
            </el-select>
          </el-form-item>
          <el-form-item :label="$t('CurrencyUnit')" prop="currency_unit">
            <el-select v-model="senior.currency_unit" :placeholder="$t('PleaseSelectACurrencyUnit')">
              <el-option
                v-for="(item, index) in currencyList"
                :key="index"
                :label="item.label"
                :value="item.value"
              ></el-option>
            </el-select>
          </el-form-item>
          <el-form-item :label="$t('VolumeUnit')" prop="volume_unit">
            <el-select v-model="senior.volume_unit" :placeholder="$t('PleaseSelectAVolumeUnit')">
              <el-option
                v-for="(item, index) in volumeList"
                :key="index"
                :label="item.label"
                :value="item.value"
              >
                <!-- <div>{{item.label}}<span style="font-size: 7px; vertical-align: super;">3</span></div> -->
              </el-option>
            </el-select>
          </el-form-item>
          <el-form-item :label="$t('Map')" prop="map">
            <el-select v-model="senior.map" :placeholder="$t('PleaseSelectAMap')">
              <el-option
                v-for="(item, index) in mapList"
                :key="index"
                :label="item.label"
                :value="item.value"
              >
              </el-option>
            </el-select>
          </el-form-item>
        </el-form>
        <div class="save save-senior">
          <el-button v-if="hasPerm('company-config.update')" type="primary" @click="postCompanyConfigure" :loading="this.$store.state.config.button_loading">{{$t('Save')}}</el-button>
        </div>
      </el-tab-pane>
    </el-tabs>

    <!-- 添加国家弹窗 -->
    <el-dialog
      :title="$t('AddCountry')"
      :visible.sync="show_country"
      class="dialog-country"
      @close="close_dialog">
        <el-form
          :model="addCountryInfo"
          :rules="rules"
          ref="countryList"
          label-position="right"
          label-width="100px"
          class="warehouse-address"
        >
          <el-form-item :label="$t('Country')" prop="short">
            <el-select v-model="addCountryInfo.short" clearable filterable :placeholder="$t('PleaseSelectACountry')">
              <el-option
                v-for="(item, index) in allCountry"
                :key="index"
                :label="item.name"
                :value="item.short"
              ></el-option>
            </el-select>
          </el-form-item>
        </el-form>
        <span slot="footer">
          <el-button @click="show_country = false">{{$t('Cancel')}}</el-button>
          <el-button type="primary" @click="addCountry">{{$t('Confirm')}}</el-button>
        </span>
    </el-dialog>

    <!-- 选择模板弹窗 -->
    <el-dialog
      :visible.sync="check_template"
      class="dialog-country"
      width="800px"
      center
      :show-close="false"
      @close="close_dialog">
        <div class="template-box">
          <!-- 模板一 -->
          <div :class="{'address-template': true, 'address-template-check': senior.address_template_id === 1}">
            <el-form
              :model="template"
              :rules="rules"
              label-position="right"
              label-width="70px"
              class="warehouse-address"
            >
              <el-row>
                <el-col>
                  <p>{{$t('地址模板一')}}</p>
                </el-col>
                <el-col>
                  <el-form-item :label="$t('Name')" prop="name">
                    <el-input v-model="template.name" disabled></el-input>
                  </el-form-item>
                </el-col>
                <el-col>
                  <el-form-item :label="$t('Tel')" prop="name">
                    <el-input v-model="template.name" disabled></el-input>
                  </el-form-item>
                </el-col>
                <el-col :span="12">
                  <el-form-item :label="$t('ZipCode')" prop="name">
                    <el-input v-model="template.name" disabled></el-input>
                  </el-form-item>
                </el-col>
                <el-col :span="12">
                  <el-form-item :label="$t('DoorNo')" prop="name">
                    <el-input v-model="template.name" disabled></el-input>
                  </el-form-item>
                </el-col>
                <el-col :span="12">
                  <el-form-item :label="$t('City')" prop="name">
                    <el-input v-model="template.name" disabled></el-input>
                  </el-form-item>
                </el-col>
                <el-col :span="12">
                  <el-form-item :label="$t('Street')" prop="name">
                    <el-input v-model="template.name" disabled></el-input>
                  </el-form-item>
                </el-col>
              </el-row>
              
            </el-form>
          </div>

          <!-- 模板二 -->
          <div :class="{'address-template': true, 'address-template-check': senior.address_template_id === 2}" v-if="senior.line_rule !== 1">
            <el-form
              :model="template"
              :rules="rules"
              label-position="right"
              label-width="70px"
              class="warehouse-address"
            >
              <el-row>
                <el-col>
                  <p>{{$t('地址模板二')}}</p>
                </el-col>
                <el-col>
                  <el-form-item :label="$t('Name')" prop="name">
                    <el-input v-model="template.name" disabled></el-input>
                  </el-form-item>
                </el-col>
                <el-col>
                  <el-form-item :label="$t('Tel')" prop="name">
                    <el-input v-model="template.name" disabled></el-input>
                  </el-form-item>
                </el-col>
                <el-col>
                  <el-form-item :label="$t('Address')" prop="name">
                    <el-input v-model="template.name" disabled>
                      <el-button slot="append" disabled icon="el-icon-location"></el-button>
                    </el-input>
                  </el-form-item>
                </el-col>
              </el-row>
              
            </el-form>
          </div>
        </div>
        <span slot="footer">
          <div class="template-radio">
            <el-radio v-model="senior.address_template_id" :label="1">选择该模板</el-radio>
            <el-radio v-model="senior.address_template_id" :label="2" v-if="senior.line_rule !== 1">选择该模板</el-radio>
          </div>
        </span>
    </el-dialog>
    <!-- 选择模板 -->
  </div>
</template>

<script>
export default {
  name: 'BasicConfig',
  data() {
    return {
      tab: '1',
      companyInfo: { // 公司信息
        name: '',
        contacts: '',
        phone: '',
        address: '',
      },
      senior: { // 高级配置信息
        line_rule: null,
        address_template_id: null,
        weight_unit: '',
        currency_unit: '',
        volume_unit: '',
        map: null,
        show_type: null,
      },
      list: [],
      // allAddress: [],
      address: [],
      allCountry: [],
      show_country: false,
      addCountryInfo: {
        short: '',
      },

      // 选择模板
      check_template: false,
      template: {
        name: null
      },

      rules: {
        name: [{ required: true, message: this.$t('PleaseEnterCompanyName'), trigger: 'blur' }],
        contacts: [
          { required: true, message: this.$t('PleaseEnterContactPerson'), trigger: 'blur' },
        ],
        phone: [{ required: true, message: this.$t('PleaseEnterCompanyTel'), trigger: 'blur' }],
        country: [
          { required: true, message: this.$t('PleaseSelectCountry'), trigger: 'blur' },
        ],
        address: [
          { required: true, message: this.$t('PleaseEnterCompanyAddress'), trigger: 'blur' },
        ],
        short: [
          { required: true, message: this.$t('PleaseSelectACountry'), trigger: 'blur' },
        ],
      },
      seniorRules: {
        line_rule: [{ required: true, message: this.$t('PleaseSelectRouteAssignRules'), trigger: 'change' }],
        show_type: [{ required: true, message: this.$t('Mandatory'), trigger: 'change' }],
        weight_unit: [{ required: true, message: this.$t('PleaseSelectAWeightUnit'), trigger: 'blur' }],
        currency_unit: [{ required: true, message: this.$t('PleaseSelectACurrencyUnit'), trigger: 'blur' }],
        volume_unit: [{ required: true, message: this.$t('PleaseSelectAVolumeUnit'), trigger: 'blur' }],
        map: [{ required: true, message: this.$t('PleaseSelectAMap'), trigger: 'blur' }],
      },
    };
  },
  created() {
    this.getAddedCountry();
    this.getCompanyDetails();
    this.getCountries();
  },
  watch: {
    tab: {
      handler(tabVal) {
        if (tabVal === '2') {
          this.getAdvanceSetting();
        }
      },
    },
    // 'senior.line_rule': { // 选择邮编自动分配后，模板自动选择模板一
    //   handler(val) {
    //     if (val === 1) {
    //       this.senior.address_template_id = 1;
    //     }
    //   },
    //   deep: true
    // }
  },
  computed: {
    line_rule() {
      return [
        {
          value: 1,
          label: this.$t('distributedByPostCode'),
        },
        {
          value: 2,
          label: this.$t('distributedByFeild'),
        },
      ];
    },
    weightList() {
      return [
        {
          value: 'kg',
          label: this.$t('kg'),
        },
        {
          value: 'lb',
          label: this.$t('lb'),
        },
      ];
    },
    currencyList() {
      return [
        {
          value: '￥',
          label: this.$t('CNY'),
        },
        {
          value: '$',
          label: this.$t('USD'),
        }, {
          value: 'C$',
          label: this.$t('CAD'),
        },
        {
          value: '€',
          label: this.$t('EUR'),
        },
      ];
    },
    volumeList() {
      return [
        {
          value: 'cm³',
          label: this.$t('cm3'),
        },
        {
          value: 'm³',
          label: this.$t('m3'),
        },
      ];
    },
    mapList() {
      return [
        {
          value: 'google',
          label: this.$t('google'),
        },
        {
          value: 'baidu',
          label: this.$t('baidu'),
        },
      ];
    },
  },
  methods: {
    // 拉取高级配置
    getAdvanceSetting() {
      this.$api.getAdvanceSetting().then((res) => {
        if(!res.data) {
          this.senior.map = null;
          return
        }
        this.senior = res.data;
      });
    },
    
    // 国家列表
    getCountries() {
      this.$api.getCountries().then((res) => {
        this.allCountry = res.data.country_list;
      });
    },
    // getCountryList() { // 国家列表
    //   this.$api.getCountryList().then((res) => {
    //     this.allAddress = res.data;
    //   });
    // },
    // getCountry() { // 国家列表
    //   this.$api.getCountry().then((res) => {
    //     this.address = res.data.data;
    //   });
    // },
    getAddedCountry() { // 国家列表
      this.$api.getAddedCountry().then((res) => {
        this.address = res.data;
        // 国家
        let countryObj = {cn: res.data[0].name, en: res.data[0].short}
        localStorage.setItem('country', JSON.stringify(countryObj));
      });
    },

    // 公司详情
    getCompanyDetails() {
      this.$api.getCompanyDetails().then((res) => {
        if(!res.data) {
          this.companyInfo = {};
        } else {
          this.companyInfo = res.data.data;
        }
      });
    },

    // 修改公司信息
    putCompanyDetails() {
      this.$refs.companyForm.validate((valid) => {
        if (!valid) return;
        this.$api.putCompanyDetails(this.companyInfo);
      });
    },
    postCompanyConfigure() {
      this.$refs.seniorForm.validate((valid) => {
        if (!valid) return;
        this.$api.postCompanyConfigure(this.senior).then((res) => {
          if (!res) return;
          // 模板标识
          localStorage.setItem('address_template_id', this.senior.address_template_id);
          // 线路分配规则
          localStorage.setItem('line_rule', this.senior.line_rule);
          // 线路分配显示
          localStorage.setItem('show_type', this.senior.show_type);
          // 货币
          this.$store.commit('globalData/setUnit', this.senior.currency_unit); // 设置货币单位
          // 设置地图
          this.$store.commit('globalData/setMaptype', this.senior.map);

          this.getAdvanceSetting();
        });
      });
    },

    // 添加国家函数
    openCountryDialog() {
      this.show_country = true;
    },
    close_dialog() {
      this.addCountryInfo = {
        short: '',
      };
    },
    addCountry() {
      this.$refs.countryList.validate((valid) => {
        if (!valid) return;
        this.$api.addCountry({
          short: this.addCountryInfo.short,
        }).then(() => {
          this.getAddedCountry();
          this.show_country = false;
        });
      });
    },
    delCountry(tag) {
      this.$api.delCountry(tag.id).then((res) => {
        if (!res.code) return;
        this.getAddedCountry();
      });
    },

    handleChangeLineRule(val) {
      // if (val === 1) {
      //   this.senior.address_template_id = 1;
      // }
    },

    // 更改线路分配规则
    handleChangeTemplate(val) {
      if (!val) {
        // this.check_template = true;
      }
    }
  },
};
</script>
<style lang="less" scoped>
.basic-config {
  background: #ffffff;
  border: 1px solid rgba(228, 235, 241, 1);
  padding: 20px 25px 10px;
  .company-info {
    margin-top: 47px;
    .el-input {
      max-width: 628px;
    }
    .el-select {
      width: 100%;
      max-width: 628px;
    }
  }
  .save {
    .el-button {
      margin-left: 160px;
      border-radius: 0;
      padding-left: 44px;
      padding-right: 44px;
    }
  }
  .save-senior {
    margin-left: 0px;
  }
}
</style>
<style lang="less">
.basic-config {
  .company-info {
    .el-input__inner {
      border-radius: 0;
    }
  }
  .el-tag + .el-tag {
    margin-left: 10px;
  }
  .add-country {
    color: #F56C6C;
    border: 1px solid #F56C6C;
    margin-left: 5px;
  }
  .dialog-country {
    overflow: hidden;
    .el-button {
      border-radius: 0;
    }
    .el-input__inner {
      border-radius: 0;
    }
    .template-box {
      display: flex;
      justify-content: space-around;
      .address-template {
        width: 49%;
        border: 1px solid #EBEEF5;
        box-shadow: 0 0 3px 1px #F2F6FC;
        box-sizing: border-box;
        padding: 20px;
      }
      .address-template-check {
        border-color: #c4c4c4;
      }
    }
    .template-radio {
      display: flex;
      justify-content: space-around;
    }
    .el-dialog__body {
      overflow: hidden;
    }
  }
  .el-dialog {
    border-radius: 0;
  }
  .show-template-box {
    display: flex;
    margin-bottom: 30px;
    .address-template {
      max-width: 345px;
      border: 1px solid #EBEEF5;
      box-shadow: 0 0 3px 1px #F2F6FC;
      box-sizing: border-box;
      padding: 20px;
      margin-left: 160px;
    }
    .address-template-radio {
      display: flex;
      justify-content: center;
    }
    .address-template-check {
      border-color: #c4c4c4;
    }
  }
}
/* 全局修改 */
.el-select-dropdown {
  border-radius: 0;
}
</style>
