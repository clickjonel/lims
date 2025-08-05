<script setup lang="ts">
    import { ref } from 'vue';
    import { useApi } from '@/composables/useApi';
    import { useToast } from 'primevue/usetoast';
    import  dohLogo from '@/assets/images/doh_logo.png';
    import Button from 'primevue/button';
    import Popover from 'primevue/popover';
    import InputText from 'primevue/inputtext';
    import FloatLabel from 'primevue/floatlabel';
    
    const toast = useToast();
    const { fetchRequest } = useApi();
    const items = ref<any[]>([])
    const iarPopover = ref()
    const deliveryDatesString = ref('')

    var iar_no = ref('')

    const toggleIarPopover = (event:any) => {
        iarPopover.value.toggle(event);
    }

    const fetchIARItems = async () => {
        const response = await fetchRequest('deliveries/nod/find', {
            iar_no: iar_no.value
        });
       if(response.data.status){
            toast.add({ 
                severity: 'success', 
                summary: 'Added to List', 
                detail: response.data.message, 
                life: 3000 
            });
            items.value.push(...response.data.items);
            iar_no.value = '';
            iarPopover.value.hide();
            console.log(items.value)
       }
       else{
            toast.add({ 
                severity: 'error', 
                summary: 'IAR Number Not Found', 
                detail: response.data.message, 
                life: 3000 
            });
       }
    }


</script>

<template>

     <div class="w-full min-h-screen flex flex-col justify-start items-center gap-4 px-2 pt-6 pb-2 font-poppins">

        <div class="flex justify-center items-center gap-2">
            <div class="flex w-20 h-20">
                <img :src="dohLogo" class="size-full" alt="">
            </div>
            <div class="flex flex-col justify-start items-start font-noto gap-0 text-xs uppercase">
                <span class="font-bold">Republic of the Philippines</span>
                <span class="font-bold">Department of Health</span>
                <span class="font-bold">Center for Health Development</span>
                <span>Cordillera Administrative Region</span>
                <span>Baguio City</span>
            </div>
        </div>

        <div class="w-full flex flex-col justify-start items-start outline divide-y font-lexend text-sm font-light">
            <div class="w-full flex justify-start items-start divide-x">
                <div class="max-w-[50%] min-w-[50%] flex justify-start items-stretch divide-x">
                    <span class="min-w-[50%] text-left pl-1">NOD Control Number</span>
                    <span class="min-w-[50%] text-left pl-1"><input type="text" class="w-full outline-none"></span>
                </div>
                <div class="max-w-[50%] min-w-[50%] flex justify-start items-stretch divide-x">
                    <span class="min-w-[50%] text-left pl-1">Date Prepared</span>
                    <span class="min-w-[50%] text-left pl-1"><input type="text" class="w-full outline-none"></span>
                </div>
            </div>

            <div class="w-full flex justify-start items-start divide-x">
                <div class="max-w-[50%] min-w-[50%] flex flex-col justify-start items-stretch">
                    <span class="min-w-[50%] text-left text-xs italic font-noto p-1">To:</span>
                    <div class="min-w-[50%] flex flex-col justify-start items-stretch">
                        <span class="min-w-[50%] text-center underline font-semibold uppercase">Tracey B. Camacho</span>
                        <span class="min-w-[50%] text-center text-xs">Audit Team Leader/State Auditor IV</span>
                        <span class="min-w-[50%] text-center text-xs font-medium uppercase">Resident COA Auditor</span>
                    </div>
                </div>
                <div class="max-w-[50%] min-w-[50%] flex flex-col justify-start items-stretch">
                    <span class="min-w-[50%] text-left text-xs italic font-noto p-1">From:</span>
                    <div class="min-w-[50%] flex flex-col justify-start items-stretch">
                        <span class="min-w-[50%] text-center underline font-semibold uppercase">Ferdinand M. Benbenen MD, DPCP, MHA, FPSMS</span>
                        <span class="min-w-[50%] text-center text-xs">Director IV</span>
                    </div>
                </div>
            </div>

            <span class="w-full bg-gray-400 text-center font-medium uppercase">I. Details of Delivery</span>

            <div class="w-full flex justify-start items-stretch divide-x text-xs font-lexend break-words text-center">
                <span class="max-w-[5%] min-w-[5%] w-[5%] text-center">No.</span>
                <span class="max-w-[20%] min-w-[20%]">Item Description</span>
                <span class="max-w-[10%] min-w-[10%]">Source/Supplier</span>
                <span class="max-w-[10%] min-w-[10%]">Delivery Date/s</span>
                <span class="max-w-[10%] min-w-[10%]">DR/SI/CI No.</span>
                <span class="max-w-[10%] min-w-[10%]">Date of DR/SI</span>
                <span class="max-w-[10%] min-w-[10%]">PO / Contract No.</span>
                <span class="max-w-[10%] min-w-[10%]">Date of PO/Contract</span>
                <span class="max-w-[10%] min-w-[10%]">Amount(PHP)</span>
            </div>

            <div class="w-full flex flex-col justify-start items-start text-xs divide-y">
                <div v-for="(item,index) in items" v-bind:key="item.id" class="w-full flex justify-start items-stretch divide-x text-[10px] font-lexend break-words text-left">
                    <span class="max-w-[5%] min-w-[5%] text-center">{{ index+1 }}</span>
                    <span class="max-w-[20%] min-w-[20%]" v-html="item.description.replace(/\n/g, '<br>')"></span>
                    <span class="max-w-[10%] min-w-[10%]">{{ item.delivery.source_name }}</span>
                    <span class="max-w-[10%] min-w-[10%] flex flex-col justify-start items-start px-1">
                        <span v-for="receipt in item.delivery.delivery_receipts">{{ receipt.delivery_date }}</span>
                    </span>
                    <span class="max-w-[10%] min-w-[10%] flex flex-col justify-start items-start px-1">
                        <span v-for="receipt in item.delivery.delivery_receipts">{{ receipt.dr_no }}</span>
                    </span>
                   <span class="max-w-[10%] min-w-[10%] flex flex-col justify-start items-start px-1">
                        <span v-for="receipt in item.delivery.delivery_receipts">{{ receipt.dr_date }}</span>
                    </span>
                    <span class="max-w-[10%] min-w-[10%]">{{ item.delivery.po_no ?? item.delivery.ptr_no }}</span>
                    <span class="max-w-[10%] min-w-[10%]">{{ item.delivery.po_date ?? item.delivery.ptr_date }}</span>
                    <span class="max-w-[10%] min-w-[10%]">{{ 
                        (item.unit_cost * item.quantity).toLocaleString('en-PH', { 
                                style: 'currency', 
                                currency: 'PHP' 
                            }) 
                    }}</span>
                </div>
            </div>

            <span class="w-full bg-gray-400 text-center font-medium uppercase">II. Attachments</span>

            <div class="w-full flex justify-start items-stretch divide-x text-xs font-lexend break-words text-center">
                <div class="w-1/3 flex justify-center items-center p-2 gap-2">
                    <span>Sales Invoice</span>
                    <input type="checkbox">
                </div>
                <div class="w-1/3 flex justify-center items-center p-2 gap-2">
                    <span>Delivery Receipt</span>
                    <input type="checkbox">
                </div>
                <div class="w-1/3 flex justify-center items-center p-2 gap-2">
                    <span>Purchase Order/Contract</span>
                    <input type="checkbox">
                </div>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x text-xs font-lexend break-words text-center">
                <div class="w-1/2 flex justify-center items-center p-2 gap-2">
                    <span>Others</span>
                    <input type="checkbox">
                </div>
                <div class="w-1/4 flex justify-center items-center p-2 gap-2">
                    <span>Place of Delivery</span>
                </div>
                <div class="w-1/4 flex justify-center items-center p-2 gap-2">
                    <span>DOH-CHD-CAR, BGHMC Compound, Baguio City</span>
                </div>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x text-xs font-lexend break-words text-center">
               <span class="w-1/2">Prepared by</span>
               <span class="w-1/2">Checked by</span>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x text-xs font-lexend break-words text-center">
                <div class="w-1/2 flex flex-col justify-center items-center p-2">
                    <span class="underline uppercase font-medium">Irene G. Oyawon</span>
                    <span class="text-xs">Administrative Assistant V</span>
                </div>
                <div class="w-1/2 flex flex-col justify-center items-center p-2">
                    <span class="underline uppercase font-medium">Felina S. Carlos</span>
                    <span class="text-xs">Administrative Officer V</span>
                </div>
            </div>


        </div>

        <div class="w-full flex flex-col justify-start items-start gap-2 print:hidden">
            <Button @click="toggleIarPopover" label="Add IAR" icon="pi pi-plus" severity="info"/>
        </div>

        <Popover ref="iarPopover" class="font-poppins">
            <div class="flex flex-col gap-4 w-[500px]">
                 <FloatLabel variant="on" class="w-full">
                    <InputText v-model="iar_no" class="w-full"/>
                    <label>Search | IAR No</label>
                </FloatLabel>
                <Button @click="fetchIARItems" label="Check and Push to List" icon="pi pi-plus" severity="help"/>
            </div>
        </Popover>

    </div>
</template>