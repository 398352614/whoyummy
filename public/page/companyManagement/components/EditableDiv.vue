<template>
  <div v-if="!editable" class="editable__div" @click="toggleEditable(true)">
    {{ value }}
  </div>
  <el-input
    ref="editableDiv"
    v-else
    type="text"
    :value="value"
    class="editable__input"
    @input="$emit('change', $event)"
    @blur="toggleEditable(false)"
  ></el-input>
</template>
<script>
export default {
  props: ["value"],
  data() {
    return {
      editable: false,
    };
  },
  methods: {
    toggleEditable(bool) {
      this.editable = bool;
      bool &&
        this.$nextTick(() => {
          this.$refs.editableDiv.$el.children[0].focus();
        });
    },
  },
};
</script>
<style lang="less" scoped>
.editable__div {
  padding: 3px 10px;
  line-height: 1.9;
}
.editable__input {
  font-size: 100%;
  ::v-deep .el-input__inner {
    min-width: 100px;
    border: none;
  }
}
</style>