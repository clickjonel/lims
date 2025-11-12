<script setup lang="ts">
    import { ref, onMounted } from 'vue';
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
    import Select from 'primevue/select';
    import Dialog from 'primevue/dialog';
    import { useAuthStore } from '@/stores/authStore';

    const router = useRouter()
    const { fetchRequest, postRequest } = useApi()
    const toast = useToast()
    const actionsPopover = ref()
    const authStore = useAuthStore()

    const properties = ref<Property[]>([])

    const pagination = ref<Pagination>({
        searchKeyword:'',
        page:1,
        total:0,
        perPage:20
    })

    interface PropertyFormData {
        id?: number
        property_no: string
        particulars: string
        unit_cost: number
        status: string
    }

    const updateModal = ref<{
        show: boolean;
        property: PropertyFormData | null;
    }>({
        show: false,
        property: null
    })

    onMounted(async() => {
        fetchProperties()
    })

    const fetchProperties = async () => {
        const response = await fetchRequest('properties/list',{
            searchKeyword: pagination.value.searchKeyword,
            page: pagination.value.page,
            total: pagination.value.total,
            perPage: pagination.value.perPage,
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

    const openUpdateModal = (property: Property) => {
        console.log(property)
        updateModal.value.property = {
            id: property.id,
            property_no: property.property_no,
            particulars: property.particulars,
            unit_cost: property.unit_cost,
            status: property.status,
        };
        updateModal.value.show = true;  
        // console.log(updateModal.value.property)
    }

    const updateProperty = async () => {
       const response = await postRequest('properties/update', updateModal.value.property)
        toast.add({ 
            severity: 'success', 
            summary: 'Success', 
            detail: response.data, 
            life: 3000 
        });
        closeUpdateModal()
        fetchProperties()
    }

    const closeUpdateModal = () => {
        updateModal.value.show = false;
        updateModal.value.property = null;
    }

    // interfaces
    interface Pagination {
        searchKeyword: string
        page: number
        total: number
        perPage: number
        limit?: number
    }

    interface Property {
        id?: number
        property_no: string
        issuance_date: string
        measurement_unit: number
        particulars: string
        unit_cost: number
        status: string
        remarks: string
        acquisition_date: string
        main_category_id: string
        current_user?: {
            user: {
                full_name: string
            }
        }
        first_user?: {
            acquisition_date: string
        }
    }
</script>

<template>
    <div class="min-w-full min-h-full h-full flex flex-col justify-start items-center p-2 gap-4">
        <div class="w-full flex justify-between items-center">
            <FloatLabel variant="on" class="w-1/4">
                <InputText v-model="pagination.searchKeyword" class="w-full" @change="fetchProperties"/>
                <label>Search | Property No, Article</label>
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
            
            <div v-for="property in properties" :key="property.id" class="w-full flex justify-start items-stretch border-x border-b text-center divide-x text-sm font-light">
                <span class="min-w-[10%] p-1">{{ property.property_no }}</span>
                <span class="min-w-[30%] p-1">{{ property.particulars }}</span>
                <span class="min-w-[20%] p-1">{{ property.current_user?.user?.full_name }}</span>
                <span class="min-w-[15%] p-1">{{ property.unit_cost }}</span>
                <span class="min-w-[15%] p-1">{{ property.first_user?.acquisition_date }}</span>
                <span class="min-w-[10%] p-1 flex justify-start items-start gap-2">
                    <Button @click="openUpdateModal(property)" icon="pi pi-file-edit" rounded outlined size="small" class="!h-8 !w-8" severity="info"/>
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

    <Dialog 
        v-model:visible="updateModal.show" 
        modal 
        header="Edit Property Details" 
        :style="{ width: '600px' }" 
        class="!font-poppins"
        @hide="closeUpdateModal"
    >
        <div v-if="updateModal.property" class="w-full flex flex-col justify-start items-start gap-4 p-2">
            <FloatLabel variant="on" class="w-full">
                <InputText v-model="updateModal.property.property_no" class="w-full" size="small"/>
                <label>Property Number</label>
            </FloatLabel>
            
            <FloatLabel variant="on" class="w-full">
                <InputText v-model="updateModal.property.particulars" class="w-full" size="small"/>
                <label>Article</label>
            </FloatLabel>
            
            <FloatLabel variant="on" class="w-full">
                <InputNumber 
                    v-model="updateModal.property.unit_cost" 
                    class="w-full" 
                    size="small" 
                    mode="currency" 
                    currency="PHP"
                />
                <label>Unit Cost</label>
            </FloatLabel>
        
            
            <FloatLabel variant="on" class="w-full">
                <Select 
                    v-model="updateModal.property.status" 
                    :options="['Active', 'Wasted']" 
                    class="w-full" 
                    size="small"
                />
                <label>Property Status</label>
            </FloatLabel>
        </div>
        
        <template #footer>
            <div class="flex justify-end gap-2">
                <Button label="Cancel" severity="secondary" @click="closeUpdateModal" size="small"/>
                <Button label="Update" severity="primary" @click="updateProperty" size="small"/>
            </div>
        </template>
    </Dialog>
</template>