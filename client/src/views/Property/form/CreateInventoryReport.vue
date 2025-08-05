<script setup lang="ts">
    import { ref,onMounted } from 'vue'
    import { useApi } from '@/composables/useApi';
    import InputText from 'primevue/inputtext';
    import FloatLabel from 'primevue/floatlabel';
    import Button from 'primevue/button';
    import MultiSelect from 'primevue/multiselect';

    const { fetchRequest } = useApi()
    const end_users = ref<Object[]>([])
    const selected_users = ref(<number[]>[])
    const users = ref(<User[]>[])

    onMounted(async() => {
        fetchEndUsers()
    })

    const fetchEndUsers = async () => {
         const response = await fetchRequest('users/selection',{})
         end_users.value = response.data.users
    }

    const fetchUserProperties = async () => {
        const response = await fetchRequest('properties/users',{users:selected_users.value})
        users.value = response.data.users
        console.log(response.data)
    }


    interface User {
        full_name:string
        assignment:{
            item:{
                plantilla_title:string
            }
        }
        properties:Property[]
    }

    interface Property {
        property:{
            property_no:string
            first_user:{
                acquisition_date:string
            }
            measurement_unit:{
                name:string
            }
            particulars:string
            unit_cost:number
            remarks:string
        }
    }

</script>

<template>

    <div class="w-full flex flex-col justify-start items-start gap-4 p-4 font-poppins print:hidden">
        <FloatLabel class="w-full" variant="on">
            <MultiSelect display="chip" size="medium" v-model="selected_users" :options="end_users" optionLabel="full_name" optionValue="user_id" filter class="w-full" :overlayStyle="{ fontFamily:'Poppins'}"/>
            <label>Select User/s</label>
        </FloatLabel>
        <Button @click="fetchUserProperties" label="Get User Properties" severity="info" size="small"/>
    </div>

    <div class="w-full flex flex-col justify-start items-start gap-4 p-4 font-poppins">
        <div v-for="user in users" class="w-full flex flex-col justify-start items-start break-after-page">
            <span>Name of Employee: {{ user.full_name }}</span>
            <span>Position: {{ user.assignment.item.plantilla_title }}</span>
            <div class="w-full flex justify-start items-stretch divide-x text-center text-sm border">
                <span class="w-[5%]">Item No.</span>
                <span class="w-[15%]">Property No.</span>
                <span class="w-[10%]">Issuance Date.</span>
                <span class="w-[10%]">Unit</span>
                <span class="w-[5%] break-words">Quantity</span>
                <span class="w-[30%]">Particulars</span>
                <span class="w-[10%]">Unit Cost</span>
                <span class="w-[15%]">Remarks</span>
            </div>
             <div v-for="(propertyUser,index) in user.properties" class="w-full flex justify-start items-stretch divide-x text-center text-xs font-light border-x border-b">
                <span class="w-[5%]">{{ index + 1 }}</span>
                <span class="w-[15%]">{{ propertyUser.property.property_no }}</span>
                <span class="w-[10%]">{{ propertyUser.property.first_user.acquisition_date }}</span>
                <span class="w-[10%]">{{ propertyUser.property.measurement_unit.name }}</span>
                <span class="w-[5%] break-words">1</span>
                <span class="w-[30%]">{{ propertyUser.property.particulars }}</span>
                <span class="w-[10%]">{{ propertyUser.property.unit_cost }}</span>
                <span class="w-[15%]">{{ propertyUser.property.remarks }}</span>
            </div>
        </div>
    </div>

</template>