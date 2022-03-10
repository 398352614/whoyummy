<template>
  <div>
      <top-nav></top-nav>
      <router-view />
  </div>
</template>
<script>
import TopNav from '@/components/topNav';

export default {
  name: 'Top',
  components: {
    TopNav,
  },
};
</script>

