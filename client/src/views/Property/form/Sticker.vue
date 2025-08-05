<script setup lang="ts">
    import { ref,onMounted } from 'vue';
    import { useApi } from '@/composables/useApi';
    import { useToast } from 'primevue';

    import InputText from 'primevue/inputtext';
    import FloatLabel from 'primevue/floatlabel';
    import Button from 'primevue/button';
    import Select from 'primevue/select';
    import Popover from 'primevue/popover';
    import DatePicker from 'primevue/datepicker';
    import QrcodeVue from 'qrcode.vue'
    import dohLogo from '@/assets/images/doh_logo.png'

    const toast = useToast()
    const { fetchRequest,postRequest } = useApi()

    var properties = ref<Property[]>([])
    var propertySearch = ref(<string>'')
    var options = ref({
        sticker_type:'',
        date_counted:null
    })

    const addPropertyPopover = ref()


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
            console.log(response.data.property)
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
        particulars:string
        serial_number:string|null
        current_user:{
            user:{
                full_name:string
            }
            issuance_date:string
        }
    }

</script>

<template>

    <div class="w-full min-h-screen flex flex-col justify-start items-star gap-2 p-4 font-poppins">
        <div class="w-full flex justify-between items-center border-b pb-2 print:hidden">
                <span class="text-left text-xl font-semibold">Properties Selected</span>
                <Button @click="toggleAddPropertyPopover" label="Add Property To List" severity="info" size="small"/>
        </div>

        <div class="w-full flex flex-col justify-start items-start gap-4 print:hidden">
            <FloatLabel class="w-full" variant="on">
                <Select v-model="options.sticker_type" :options="[{name:'Small Sticker'},{name:'Large Sticker'}]" optionLabel="name" optionValue="name" class="w-full" :overlayStyle="{fontFamily:'Lexend Deca'}"/>
                <label>Select Sticker Type</label>
            </FloatLabel>

            <FloatLabel class="w-full" variant="on">
                <DatePicker v-model="options.date_counted" class="w-full" dateFormat="mm/dd/yy"/>
                <label>Date Counted</label>
            </FloatLabel>
        </div>

        <div class="w-full flex flex-col justify-start items-start border divide-y print:hidden">
                <div class="w-full flex justify-start items-stretch text-center divide-x font-semibold uppercase bg-[#E3EEF9]">
                    <span class="w-1/3">Property No</span>
                    <span class="w-1/3">Particulars</span>
                    <span class="w-1/3">Unit Cost</span>
                </div>
                <div v-for="property in properties" class="w-full flex justify-start items-stretch text-center divide-x font-light text-sm">
                    <span class="w-1/3">{{ property.property_no }}</span>
                    <span class="w-1/3">{{ property.particulars }}</span>
                    <span class="w-1/3">{{ property.unit_cost }}</span>
                </div>
        </div>

        <div v-if="options.sticker_type === 'Large Sticker'" class="w-full grid grid-cols-2 gap-6 p-4 font-poppins">
            <div v-for="property in properties" class="w-full flex flex-col justify-start items-start gap-2 p-2 text-xs border">
                <div class="w-full flex justify-center items-center gap-2 border-b pb-1">
                    <div class="w-full flex flex-col justify-start items-start text-base font-bold">
                        <span>Government Property</span>
                        <span>CHD-CAR, Baguio City</span>
                    </div>
                    <div class="w-full flex justify-end items-center">
                        <QrcodeVue :value="'asdasd'" :size="50" class=""/>
                    </div>
                </div>
                <span>Article: {{ property.particulars }}</span>
                <span>Serial Number: {{ property.serial_number ?? null }}</span>
                <span>Property Number: {{ property.property_no }}</span>
                <span>Date Acquired: {{ property.current_user.issuance_date }}</span>
                <span>Acquisition Cost: {{ property.unit_cost }}</span>
                <span>End User/MR to: {{ property.current_user.user.full_name }}</span>
                <span>Date Counted: {{ options.date_counted ?  new Date(options.date_counted).toLocaleDateString() : ''}}</span>
                <span>Inventory Conducted By: Shayne</span>
                
                <div class="w-full flex justify-center items-center">
                    <span class="w-[90%] text-center mt-2 px-4 py-2 font-bold border rounded-lg uppercase">Tampering this sticker is punishable by law</span>
                </div>
            </div>
        </div>

        <div v-if="options.sticker_type === 'Small Sticker'" class="w-full grid grid-cols-4 gap-4 p-4">
            <div v-for="property in properties" class="w-full flex flex-col justify-start items-start gap-2 border p-2 font-poppins text-xs">
                <div class="w-full flex justify-between items-center">
                    <img :src="dohLogo" alt="asd" class="size-[50px]">
                    <QrcodeVue :value="'asdasd'" :size="50" class=""/>
                </div>
                <span class="w-full border-b"></span>
                <span>PN: {{ property.property_no }}</span>
                <span>EU: {{ property.current_user.user.full_name }}</span>
                <span>DA: {{ property.current_user.issuance_date }}</span>
                <span>UC: Shayne</span>
            </div>
        </div>

    </div>

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