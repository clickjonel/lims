<script setup lang="ts">
    import Button from 'primevue/button';
    import { useRouter } from 'vue-router';

    const router = useRouter()
    const props = defineProps({
        title:{
            type:String,
            default:'Form Title'
        },
        returnPath:{
            type:String,
            default:'/admin/dashboard'
        }
    })
</script>

<template>
    <div class="w-full min-h-screen h-screen flex flex-col justify-start items-start p-2 bg-[#E3EEF9] font-poppins gap-4 overflow-y-scroll">
       <div class="w-full min-h-[5%] h-[5%] flex justify-between items-center bg-white p-2 shadow-md">
            <span class="w-full uppercase text-xl font-semibold">{{ props.title }}</span>
            <Button @click="router.push({path:props.returnPath})" label="Back" icon="pi pi-arrow-left" severity="help" size="small" class="text-xs"/>
       </div>
       <div class="w-full min-h-[93%] h-[93%] bg-white">
            <slot/>
       </div>
    </div>
</template>