<script setup lang="ts">
  import { ref } from 'vue';
  import 'primeicons/primeicons.css'
  import Toast from 'primevue/toast';
  // import Button from "primevue/button"
  // import Drawer from 'primevue/drawer';
  // import Popover from 'primevue/popover';

  // var drawerVisible = ref(false);
  // var profilePopover = ref()

  // const toggleProfile = (event:any) => {
  //     profilePopover.value.toggle(event);
  // }

</script>

<template>
  <RouterView />
  <Toast class="font-lexend"/>
</template>
