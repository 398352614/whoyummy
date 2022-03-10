<template>
  <div>
    <intelligent-dispatch-google
      v-if="mapType == 'google'"
    ></intelligent-dispatch-google>
    <intelligent-dispatch-baidu v-else></intelligent-dispatch-baidu>
  </div>
</template>

<script>
import IntelligentDispatchGoogle from "./IntelligentDispatchGoogle";
import IntelligentDispatchBaidu from "./IntelligentDispatchBaidu";
export default {
  name: "intelligentDispatch",
  components: {
    IntelligentDispatchGoogle,
    IntelligentDispatchBaidu,
  },
  data() {
    return {};
  },
  computed: {
    mapType() {
      return this.$store.state.globalData.maptype;
    },
  },
  methods: {},
};
</script>

<style lang="less">
</style>