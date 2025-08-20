<script setup lang="ts">
    import { ref,onMounted } from 'vue';
    import { useRouter } from 'vue-router';
    import { useApi } from '@/composables/useApi';
    import { useToast } from 'primevue';

    import InputText from 'primevue/inputtext';
    import FloatLabel from 'primevue/floatlabel';
    import Button from 'primevue/button';
    import Pagination from '@/components/Pagination.vue';
    import Popover from 'primevue/popover';
    import InputNumber from 'primevue/inputnumber';
    import DatePicker from 'primevue/datepicker';
    import { useAuthStore } from '@/stores/authStore';


    const router = useRouter()
    const { fetchRequest,postRequest } = useApi()
    const toast = useToast()
    const actionsPopover = ref()
    const authStore = useAuthStore()

    const properties = ref<Property[]>([])

    var pagination = ref(<Pagination>{
        searchKeyword:'',
        page:1,
        total:0,
        perPage:20
    })


    onMounted(async() => {
      fetchProperties()
    })

    const fetchProperties = async () => {
       const response = await fetchRequest('properties/list',{
         searchKeyword:pagination.value.searchKeyword,
            page:pagination.value.page,
            total:pagination.value.total,
            perPage:pagination.value.perPage,
       })
       if(response.apiResponseStatus === 200) {
         pagination.value.total = response.data.total
         properties.value = response.data.list
       }
       console.log(response)
    }

    const toggleActionsPopover = (event:Event) => {
        actionsPopover.value.toggle(event)
    }


    // interfaces
    interface Pagination {
        searchKeyword:string
        page:number
        total:number
        perPage:number
        limit:number
    }

    interface Property {
       property_no:string
       issuance_date:string
       measurement_unit:number
       particulars:string
       unit_cost:number
       status:string
       remarks:string
       acquisition_date:string
       main_category_id:string
       current_user?:{
           user:{
               full_name:string
           }
       }
       first_user?:{
           acquisition_date:string
       }
    }

</script>


<template>
    <div class="min-w-full min-h-full h-full flex flex-col justify-start items-center p-2 gap-4">
       <div class="w-full flex justify-between items-center">
            <FloatLabel variant="on" class="w-1/4">
                <InputText v-model="pagination.searchKeyword" class="w-full" @change="fetchProperties"/>
                <label>Search | Property No,Article</label>
            </FloatLabel>
            <div class="flex justify-start items-center gap-2">
                <Button @click="toggleActionsPopover" label="Actions" severity="primary" class="text-xs"/>
            </div>
       </div>
       <div class="w-full h-full flex flex-col justify-start items-start overflow-y-scroll">
            <div class="w-full flex justify-start items-stretch border text-center divide-x font-semibold uppercase bg-[#E3EEF9] sticky top-0">
                <span class="min-w-[10%]">Property #</span>
                <span class="min-w-[30%]">Article</span>
                <span class="min-w-[20%]">Current User</span>
                <span class="min-w-[15%]">Unit Cost</span>
                <span class="min-w-[15%]">Acquisition Date</span>
                <span class="min-w-[10%]">Actions</span>
            </div>
            <div v-for="property in properties" class="w-full flex justify-start items-stretch border-x border-b text-center divide-x text-sm font-light">
                <span class="min-w-[10%] p-1">{{ property.property_no }}</span>
                <span class="min-w-[30%]  p-1">{{ property.particulars }}</span>
                <span class="min-w-[20%]  p-1">{{ property.current_user?.user?.full_name }}</span>
                <span class="min-w-[15%]  p-1">{{ property.unit_cost }}</span>
                <span class="min-w-[15%]  p-1">{{ property.first_user?.acquisition_date }}</span>
                <span class="min-w-[10%]  p-1">

                </span>
            </div>
       </div>

      <Pagination v-model="pagination.page" :total="pagination.total" :perPage="pagination.perPage" @fetchPage="fetchProperties"/>

    </div>

    <Popover ref="actionsPopover" class="w-[300px]">
        <div class="w-full flex flex-col justify-start font-poppins text-sm p-4 gap-4">
            <Button @click="router.push({path:'/property/create'})" label="Create New Property" severity="info"/>
            <Button @click="router.push({path:'/property/transfer'})" label="Transfer Property/ies" severity="info"/>
            <Button @click="router.push({path:'/property/report'})" label="Inventory Report" severity="info"/>
            <Button @click="router.push({path:'/property/sticker'})" label="Print Sticker/s" severity="info"/>
        </div>
    </Popover>

</template>