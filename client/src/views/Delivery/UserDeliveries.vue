<script setup lang="ts">
    // composables
    import { ref,onMounted } from 'vue';
    import { useApi } from '@/composables/useApi';
    import { useRouter } from 'vue-router';
    import { useAuthStore } from '@/stores/authStore';

    // components
    import InputText from 'primevue/inputtext';
    import FloatLabel from 'primevue/floatlabel';
    import Button from 'primevue/button';
    import Pagination from '@/components/Pagination.vue';
    import Popover from 'primevue/popover';


    const router = useRouter()
    const user = useAuthStore()
    const deliveries = ref(<any>[])
    const { fetchRequest } = useApi()
    const deliveryInvoicesPopover = ref()
    const deliveryReceiptsPopover = ref()

    var pagination = ref(<Pagination>{
        searchKeyword:'',
        page:1,
        total:0,
        perPage:15
    })
    var selectedDeliveryInvoices = ref(<DeliveryInvoice[]>{})
    var selectedDeliveryReceipts = ref(<DeliveryReceipt[]>{})

    onMounted(async()=>{
       fetchDeliveries()
    })

    const fetchDeliveries = async () => {
        const response = await fetchRequest('deliveries/list',{
            searchKeyword:pagination.value.searchKeyword,
            page:pagination.value.page,
            total:pagination.value.total,
            perPage:pagination.value.perPage,
            user_id:user.user?.user_id
        })
         deliveries.value = response.data.list
         pagination.value.total = response.data.total
    } 

    const toggleDeliveryInvoices = (event:any,invoices: DeliveryInvoice[]) => {
        selectedDeliveryInvoices.value = invoices
        deliveryInvoicesPopover.value.toggle(event);
    }

    const toggleDeliveryReceipts = (event:any,receipts: DeliveryReceipt[]) => {
        selectedDeliveryReceipts.value = receipts
        deliveryReceiptsPopover.value.toggle(event);
    }

    // interfaces
    interface Pagination {
        searchKeyword:string
        page:number
        total:number
        perPage:number
        limit:number
    }

    interface DeliveryInvoice {
        id:number
        invoice_date:string
        invoice_no:string
    }

    interface DeliveryReceipt {
        id:number
        dr_date:string
        dr_no:string
        delivery_date:string
        delivery_place:string
    }

</script>


<template>
    <div class="min-w-full min-h-full h-full flex flex-col justify-start items-center p-2 gap-4">
       <div class="w-full flex justify-between items-center">
            <FloatLabel variant="on" class="w-1/4">
                <InputText v-model="pagination.searchKeyword" class="w-full" @change="fetchDeliveries"/>
                <label>Search | IAR No</label>
            </FloatLabel>
            <!-- <div class="flex justify-start items-center gap-2">
                <Button @click="router.push({path:'/delivery/nod'})" label="Notice of Delivery" icon="pi pi-info" severity="info" class="text-xs"/>
                <Button @click="router.push({path:'/delivery/create'})" label="New Delivery" icon="pi pi-plus" severity="primary" class="text-xs"/>
            </div> -->
       </div>
       <div class="w-full h-full flex flex-col justify-start items-start overflow-y-scroll">
            <div class="w-full flex justify-start items-stretch gap-2 border text-center divide-x font-semibold uppercase bg-[#E3EEF9] sticky top-0">
                <span class="min-w-[10%]">IAR No</span>
                <span class="min-w-[10%]">PO/PTR Details</span>
                <span class="min-w-[20%]">Requisitioning Office <br> End User</span>
                <span class="min-w-[15%]">Source Details</span>
                <span class="min-w-[15%]">Receipts</span>
                <span class="min-w-[15%]">Fund Source <br> Payment Terms</span>
                <span class="min-w-[15%]">Actions</span>
            </div>
            <div v-for="delivery in deliveries" class="w-full flex justify-start items-stretch gap-2 border-x border-b text-center divide-x text-sm font-light">
                <span class="min-w-[10%]"> {{ delivery.iar_no }} </span>
                <span class="min-w-[10%]"> {{ delivery.ptr_no || delivery.po_no }} <br> {{ delivery.ptr_date || delivery.po_date }} </span>
                <span class="min-w-[20%]"> <span class="text-xs">{{ delivery.requisitioning_section.section_name }}</span> - <br> <span class="font-medium">{{ delivery.end_user.full_name }}</span> </span>
                <span class="min-w-[15%]"> <span class="font-medium">{{ delivery.source_name }}</span> - <br> <span class="text-xs">{{ delivery.source_address }}</span> </span>
                <span class="min-w-[15%] flex flex-col justify-center items-center gap-2 py-2"> 
                    <Button v-if="delivery.delivery_invoices_count >= 1" @click="toggleDeliveryInvoices($event,delivery.delivery_invoices)" severity="help" label="Delivery Invoices" icon="pi pi-receipt" :badge="String(delivery.delivery_invoices_count ?? 0)" badgeSeverity="contrast" outlined  size="small" class="text-xs"/>
                    <Button @click="toggleDeliveryReceipts($event,delivery.delivery_receipts)" severity="info" label="Delivery Receipts" icon="pi pi-truck" :badge="String(delivery.delivery_receipts_count)" badgeSeverity="contrast" outlined  size="small" class="text-xs"/>
                </span>
                <span class="min-w-[15%]"> {{ delivery.fund_source.name }} <br> {{ delivery.payment_term_label }} </span>
                <span class="min-w-[15%] flex justify-start items-center gap-2">
                    <!-- <Button @click="router.push({path:`/delivery/update/${delivery.id}`})" v-tooltip="{ value: 'Update Delivery', showDelay: 100, hideDelay: 300, pt: {text: {class: 'font-poppins text-xs'}}}" severity="contrast" icon="pi pi-file-edit" outlined  size="small" rounded class="text-xs"/>
                    <Button @click="router.push({path:`/delivery/iar/${delivery.id}`})" v-tooltip="{ value: 'Print IAR', showDelay: 100, hideDelay: 300, pt: {text: {class: 'font-poppins text-xs'}}}" severity="contrast" icon="pi pi-print" outlined  size="small" rounded class="text-xs"/> -->
                </span>
            </div>
       </div>

      <Pagination v-model="pagination.page" :total="pagination.total" :perPage="pagination.perPage" @fetchPage="fetchDeliveries"/>

    </div>

    <Popover ref="deliveryInvoicesPopover" class="w-[500px]">
        <div class="w-full flex flex-col justify-start divide-y outline font-poppins text-sm">
            <div class="w-full flex justify-between items-stretch divide-x font-medium bg-[#E3EEF9] text-center">
                <span class="w-1/2 p-1">Invoice Number</span>
                <span class="w-1/2 p-1">Invoice Date</span>
            </div>
            <div v-for="invoice in selectedDeliveryInvoices" class="w-full flex justify-between items-stretch divide-x font-light text-center">
                <span class="w-1/2 p-1">{{ invoice.invoice_no }}</span>
                <span class="w-1/2 p-1">{{ invoice.invoice_date }}</span>
            </div>
        </div>
    </Popover>

    <Popover ref="deliveryReceiptsPopover" class="w-[1000px]">
        <div class="w-full flex flex-col justify-start divide-y outline font-poppins text-sm">
            <div class="w-full flex justify-between items-stretch divide-x font-medium bg-[#E3EEF9] text-center">
                <span class="w-[20%] p-1">Receipt No</span>
                <span class="w-[20%] p-1">Receipt Date</span>
                <span class="w-[20%] p-1">Delivery Date</span>
                <span class="w-[40%] p-1">Delivery Place</span>
            </div>
            <div v-for="receipt in selectedDeliveryReceipts" class="w-full flex justify-between items-stretch divide-x font-light text-center">
                <span class="w-[20%] p-1">{{ receipt.dr_no }}</span>
                <span class="w-[20%] p-1">{{ receipt.dr_date }}</span>
                <span class="w-[20%] p-1">{{ receipt.delivery_date }}</span>
                <span class="w-[40%] p-1">{{ receipt.delivery_place }}</span>
            </div>
        </div>
    </Popover>

</template>