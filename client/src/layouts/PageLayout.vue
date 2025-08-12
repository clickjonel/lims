<script setup lang="ts">
  import { ref } from 'vue';
  import 'primeicons/primeicons.css'
  import Button from "primevue/button"
  import Drawer from 'primevue/drawer';
  import Popover from 'primevue/popover';
  import { useRouter,useRoute } from 'vue-router';
  import { useAuthStore } from '@/stores/authStore';
  import { useApi } from '@/composables/useApi';
  import { useToast } from 'primevue';

  const router = useRouter()
  const route = useRoute()
  const auth = useAuthStore()
  const { postRequest } = useApi()
  const toast = useToast()

  var drawerVisible = ref(false);
  var profilePopover = ref()

  const toggleProfile = (event:any) => {
      profilePopover.value.toggle(event);
  }

  const navigateTo = (path:string) => {
    router.push({path:path})
    drawerVisible.value = false
  } 

  const logout = async () => {
    const response = await postRequest('logout',{})
      if (response.apiResponseStatus === 200) {
        auth.setToken('')
        auth.isAuthenticated = false
        router.push({name:'Login'})
      } else {
         toast.add({ 
            severity: 'error', 
            summary: 'Login Failed', 
            detail: response.data.message, 
            life: 2000 
        });
      }
  }

</script>

<template>

    <div class="w-full h-screen bg-[#E3EEF9] flex flex-col justify-start items-start gap-4 p-4 font-poppins">
        <div class="w-full min-h-[6%] h-[6%] flex justify-between items-center px-4 bg-white">
            <Button @click="drawerVisible = true" icon="pi pi-arrow-right" rounded severity="info" variant="filled" size="small"/>
            <span class="text-base font-lexend">LIMS | {{ route.name }}</span>
            <Button type="button" :label="auth.user?.nickname" @click="toggleProfile" rounded size="small"/>
        </div>
        <div class="w-full min-h-[93.5%] h-[93.5%] bg-white">
            <RouterView />
        </div>
    </div>

    <Drawer v-model:visible="drawerVisible" header="LIMS | Navigation" class="font-poppins">
        <div class="w-full h-full flex flex-col jutify-start items-start">
            <div class="w-full flex flex-col justify-start items-start gap-2 p-2">
              <span class="uppercase font-medium text-lg">Dashboard</span>
              <span @click="navigateTo('/admin/dashboard')" class="w-full py-2 pl-2 cursor-pointer hover:bg-emerald-700 hover:text-white" :class="route.name === 'Dashboard' ? 'bg-emerald-700 text-white shadow-md shadow-slate-600' : '' ">Dashboard</span>
            </div>
            <div class="w-full flex flex-col justify-start items-start gap-2 p-2">
              <span class="uppercase font-medium text-lg">Delivery</span>
              <span v-if="auth.isAdmin" @click="navigateTo('/admin/deliveries')" class="w-full py-2 pl-2 cursor-pointer hover:bg-emerald-700 hover:text-white" :class="route.name === 'Deliveries' ? 'bg-emerald-700 text-white shadow-md shadow-slate-600' : '' ">Deliveries</span>
              <span @click="navigateTo('/admin/deliveries/user')" class="w-full py-2 pl-2 cursor-pointer hover:bg-emerald-700 hover:text-white" :class="route.name === 'User Deliveries' ? 'bg-emerald-700 text-white shadow-md shadow-slate-600' : '' ">My Deliveries</span>
              <span @click="navigateTo('/admin/deliveries/section')" class="w-full py-2 pl-2 cursor-pointer hover:bg-emerald-700 hover:text-white" :class="route.name === 'Section Deliveries' ? 'bg-emerald-700 text-white shadow-md shadow-slate-600' : '' ">Section Deliveries</span>
            </div>
            <div class="w-full flex flex-col justify-start items-start gap-2 p-2">
              <span class="uppercase font-medium text-lg">Stock Cards</span>
              <span v-if="auth.isAdmin" @click="navigateTo('/admin/stock_cards')" class="w-full py-2 pl-2 cursor-pointer hover:bg-emerald-700 hover:text-white" :class="route.name === 'Stock Cards' ? 'bg-emerald-700 text-white shadow-md shadow-slate-600' : '' ">Stock Cards</span>
              <span @click="navigateTo('/admin/stock_cards/section')" class="w-full py-2 pl-2 cursor-pointer hover:bg-emerald-700 hover:text-white" :class="route.name === 'Section Stock Cards' ? 'bg-emerald-700 text-white shadow-md shadow-slate-600' : '' ">Section Stock Cards</span>
            </div>
            <div class="w-full flex flex-col justify-start items-start gap-2 p-2">
              <span class="uppercase font-medium text-lg">Properties</span>
              <span v-if="auth.isAdmin" @click="navigateTo('/admin/properties')" class="w-full py-2 pl-2 cursor-pointer hover:bg-emerald-700 hover:text-white" :class="route.name === 'Properties' ? 'bg-emerald-700 text-white shadow-md shadow-slate-600' : '' ">Properties</span>
              <span @click="navigateTo('/admin/properties/user')" class="w-full py-2 pl-2 cursor-pointer hover:bg-emerald-700 hover:text-white" :class="route.name === 'User Properties' ? 'bg-emerald-700 text-white shadow-md shadow-slate-600' : '' ">My Properties</span>
            </div>
        </div>
    </Drawer>

    <Popover ref="profilePopover">
        <div class="flex flex-col gap-2">
          <Button label="Logout" @click="logout" size="small" class="w-full" variant="outlined" severity="danger"/>
        </div>
    </Popover>


</template>
