<script setup lang="ts">
    import { ref,onMounted } from 'vue';
    import { useApi } from '@/composables/useApi';
    import { useToast } from 'primevue';

    import InputText from 'primevue/inputtext';
    import FloatLabel from 'primevue/floatlabel';
    import Button from 'primevue/button';
    import Select from 'primevue/select';
    import FormLayout from '@/layouts/FormLayout.vue';
    import Popover from 'primevue/popover';

    const toast = useToast()
    const { fetchRequest,postRequest } = useApi()

    const addPropertyPopover = ref()
    
    var properties = ref<Property[]>([])
    var end_users = ref<object[]>([])

    var propertySearch = ref(<string>'')
    var end_user = ref(<number|null>null)

    onMounted(async() => {
      fetchEndUsers()
    })

    const fetchEndUsers = async () => {
         const response = await fetchRequest('users/selection',{})
         end_users.value = response.data.users
    }



    const transfer = async () => {
        const payload = {
            properties: properties.value,
            end_user: end_user.value,
        }
        const response = await postRequest('properties/transfer',payload)
        if(response.apiResponseStatus === 200){
             toast.add({ 
                severity: 'success', 
                summary: 'Transfered', 
                detail: response.data.message, 
                life: 3000 
            });
            properties.value = []
        }
    }


    const toggleAddPropertyPopover = (event:Event) => {
        addPropertyPopover.value.toggle(event)
    }

    const getProperty = async () => {
        const response = await fetchRequest('properties/find/property_no',{property_no:propertySearch.value})
        if(response.data.status){
             toast.add({ 
                severity: 'success', 
                summary: 'Found', 
                detail: 'Property Added', 
                life: 3000 
            });
            properties.value.push(response.data.property)
            propertySearch.value = ''
            addPropertyPopover.value.hide()
        }
        else{
             toast.add({ 
                severity: 'error', 
                summary: 'Failed', 
                detail: 'Property Not Found', 
                life: 3000 
            });
        }
    }


    interface Property{
        property_no:string
        unit_cost:number
        article:string
    }



</script>

<template>
    <FormLayout title="Transfer Properties" return-path="/admin/properties">

        <div class="w-full h-full flex flex-col justify-start items-center gap-4 p-4 font-poppins overflow-y-scroll">

           <div class="w-full flex justify-between items-center border-b pb-2">
                <span class="text-left text-xl font-semibold">Properties Selected</span>
                <Button @click="toggleAddPropertyPopover" label="Add Property To List" severity="info" size="small"/>
           </div>

           <div class="w-full flex flex-col justify-start items-start border divide-y">
                <div class="w-full flex justify-start items-stretch text-center divide-x font-semibold uppercase bg-[#E3EEF9]">
                    <span class="w-1/3">Property No</span>
                    <span class="w-1/3">Particulars</span>
                    <span class="w-1/3">Unit Cost</span>
                </div>
                 <div v-for="property in properties" class="w-full flex justify-start items-stretch text-center divide-x font-light text-sm bg-[#E3EEF9]">
                    <span class="w-1/3">{{ property.property_no }}</span>
                    <span class="w-1/3">{{ property.article }}</span>
                    <span class="w-1/3">{{ property.unit_cost }}</span>
                </div>
           </div>

            <FloatLabel class="w-full" variant="on">
                <Select v-model="end_user" :options="end_users" optionLabel="full_name" optionValue="user_id" size="small" class="w-full" filter/>
                <label>End User</label>
            </FloatLabel>

            <div class="w-full flex justify-start items-center">
                <Button @click="transfer" label="Transfer" icon="pi pi-save" severity="success"/>
            </div>

        </div>

    </FormLayout>

     <Popover ref="addPropertyPopover" class="w-[500px]">
        <div class="w-full flex flex-col justify-start font-poppins text-sm p-4 gap-4">
           <FloatLabel variant="on" class="w-full">
                <InputText v-model="propertySearch" class="w-full" size="small"/>
                <label>Property Number</label>
            </FloatLabel>
            <Button @click="getProperty" label="Search and Add" severity="info" size="small"/>
        </div>
    </Popover>

</template>