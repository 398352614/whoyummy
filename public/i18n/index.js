import VueI18n from 'vue-i18n';
import Vue from 'vue';
import ElementUILocale from 'element-ui/lib/locale';
import cn from './zh-CN'; // 中文
import en from './en'; // 英语

Vue.use(VueI18n);

const i18n = new VueI18n({
  silentTranslationWarn: true,
  locale: localStorage.getItem('lang') || 'cn', // set locale
  messages: {
    cn,
    en,
  }, // set locale messages
});


ElementUILocale.i18n((key, value) => i18n.t(key, value));

export default i18n;
