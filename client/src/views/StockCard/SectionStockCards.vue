<script setup lang="ts">
    import { ref,onMounted } from 'vue';
    import { useRouter } from 'vue-router';
    import { useApi } from '@/composables/useApi';
    import { useToast } from 'primevue';
    import { useAuthStore } from '@/stores/authStore';

    import InputText from 'primevue/inputtext';
    import FloatLabel from 'primevue/floatlabel';
    import Button from 'primevue/button';
    import Pagination from '@/components/Pagination.vue';
    import Popover from 'primevue/popover';
    import InputNumber from 'primevue/inputnumber';
    import DatePicker from 'primevue/datepicker';


    const router = useRouter()
    const authStore = useAuthStore()
    const { fetchRequest,postRequest } = useApi()
    const toast = useToast()
    const stockCards = ref(<StockCard[]>[])
    const transactionsPopover = ref()
    const issuancePopover = ref()

    var pagination = ref(<Pagination>{
        searchKeyword:'',
        page:1,
        total:0,
        perPage:50
    })
    var selectedTransactions = ref(<StockCardTransaction[]>[])
    var stockIssuance = ref(<StockIssuance>{})

    onMounted(async() => {
      fetchStockCards()
    })

    const fetchStockCards = async () => {
        const response = await fetchRequest('stock_cards/list',{
            searchKeyword:pagination.value.searchKeyword,
            page:pagination.value.page,
            total:pagination.value.total,
            perPage:pagination.value.perPage,
            section_id:authStore.user?.assignment.section.section_id
        })
        stockCards.value = response.data.list
        pagination.value.total = response.data.total
        // console.log(stockCards.value)
    }

    const toggleTransactions = (event:any, transactions: StockCardTransaction[]) => {
        selectedTransactions.value = transactions
        transactionsPopover.value.toggle(event);
    }

    const toggleIssuancePopover = (event:Event,stockID:number) => {
        stockIssuance.value.stock_card_id = stockID
        issuancePopover.value.toggle(event);
    }

    const issueStocks = async () => {
        const payload = {
            stock_card_id:stockIssuance.value.stock_card_id,
            transaction_date: stockIssuance.value.transaction_date ? stockIssuance.value.transaction_date.toISOString().split('T')[0] : '',
            quantity:stockIssuance.value.quantity,
            recepient:stockIssuance.value.recepient,
            ptr_no:stockIssuance.value.ptr_no,
            remarks:stockIssuance.value.remarks
        }

        const response = await postRequest('stock_cards/issue', payload)
        if(response.apiResponseStatus === 200){
            stockIssuance.value = <StockIssuance>{}
            toast.add({ 
                severity: 'success', 
                summary: 'Stock Card Created', 
                detail: response.data.message, 
                life: 3000 
            });
            issuancePopover.value.hide()
            fetchStockCards()
        }
    }

    // interfaces
    interface Pagination {
        searchKeyword:string
        page:number
        total:number
        perPage:number
        limit:number
    }

    interface StockCard {
        id:number
        stock_no:string
        stock_name:string
        req_office:string
        quantity:number
        remaining:number
        unit_cost:number
        contract_no:string
        latest_transaction: {
            balance:number
        }
        stock_card_transactions_count:number
        stock_card_transactions:StockCardTransaction[]
        section:{
            section_name:string
        }
    }

    interface StockCardTransaction {
        transaction_date:string
        iar_no?:string
        ptr_no?:string
        received:number
        issued:number
        recepient:string
    }

    interface StockIssuance {
        stock_card_id:number
        transaction_date:Date
        quantity:number
        recepient:string
        ptr_no:string
        remarks?:string
    }

</script>


<template>
    <div class="min-w-full min-h-full h-full flex flex-col justify-start items-center p-2 gap-4">
       <div class="w-full flex justify-between items-center">
            <FloatLabel variant="on" class="w-1/4">
                <InputText v-model="pagination.searchKeyword" class="w-full" @change="fetchStockCards"/>
                <label>Search | Stock No, Description</label>
            </FloatLabel>
            <div class="flex justify-start items-center gap-2">
                <!-- <Button @click="router.push({path:'/delivery/nod'})" label="Notice of Delivery" icon="pi pi-info" severity="info" class="text-xs"/> -->
                <!-- <Button @click="router.push({path:'/stock_card/create'})" label="New Stock Card" icon="pi pi-plus" severity="primary" class="text-xs"/> -->
            </div>
       </div>
       <div class="w-full h-full flex flex-col justify-start items-start overflow-y-scroll">
            <div class="w-full flex justify-start items-stretch border text-center divide-x font-semibold uppercase bg-[#E3EEF9] sticky top-0">
                <span class="min-w-[10%]">Stock No</span>
                <span class="min-w-[20%]">Stock Name</span>
                <span class="min-w-[15%]">Office</span>
                <span class="min-w-[10%] break-words">Remaining/Quantity</span>
                <span class="min-w-[15%]">Transactions</span>
                <span class="min-w-[10%]">Unit Cost</span>
                 <span class="min-w-[10%] break-words">PO/PTR/Contract Number</span>
                <span class="min-w-[10%]">Actions</span>
            </div>
            <div v-for="stock_card in stockCards" class="w-full flex justify-start items-stretch border-x border-b text-center divide-x text-sm font-light">
                <span class="min-w-[10%]">{{ stock_card.stock_no }}</span>
                <span class="min-w-[20%]">{{ stock_card.stock_name }}</span>
                <span class="min-w-[15%]">{{ stock_card.section?.section_name }}</span>
                <span class="min-w-[10%]">{{ stock_card.latest_transaction.balance }} / {{ stock_card.quantity }}</span>
                <span class="min-w-[15%] flex justify-center items-center p-2">
                    <Button @click="toggleTransactions($event,stock_card.stock_card_transactions)" severity="help" label="Transactions" icon="pi pi-receipt" :badge="String(stock_card.stock_card_transactions_count)" badgeSeverity="contrast" outlined  size="small" class="text-xs"/>
                </span>
                <span class="min-w-[10%]">{{ stock_card.unit_cost }}</span>
                <span class="min-w-[10%]">{{ stock_card.contract_no }}</span>
                <span class="min-w-[10%] flex justify-center items-center p-2 gap-2">
                     <Button v-tooltip="{ value: 'Create Allocation List ', showDelay: 100, hideDelay: 300, pt: {text: {class: 'font-poppins text-xs'}}}" severity="help" icon="pi pi-file-edit" outlined  size="small" rounded class="text-xs"/>
                </span>
            </div>
       </div>

      <Pagination v-model="pagination.page" :total="pagination.total" :perPage="pagination.perPage" @fetchPage="fetchStockCards"/>

    </div>

     <Popover ref="transactionsPopover" class="w-[1000px]">
        <div class="w-full flex flex-col justify-start divide-y outline font-poppins text-sm overflow-y-scroll" :class="selectedTransactions.length > 15 ? 'h-[500px]' : '' ">
            <div class="w-full flex justify-between items-stretch divide-x font-medium bg-[#E3EEF9] text-center sticky top-0">
                <span class="w-[10%] p-1">Transaction Date</span>
                <span class="w-[15%] p-1">Contract #</span>
                <span class="w-[20%] p-1">Received</span>
                <span class="w-[20%] p-1">Issued</span>
                <span class="w-[35%] p-1">Recipient</span>
            </div>
            <div v-for="transaction in selectedTransactions" class="w-full flex justify-between items-stretch divide-x font-light text-center">
                <span class="w-[10%] p-1">{{ transaction.transaction_date }}</span>
                <span class="w-[15%] p-1">{{ transaction.iar_no ?? transaction.ptr_no }}</span>
                <span class="w-[20%] p-1">{{ transaction.received }}</span>
                <span class="w-[20%] p-1">{{ transaction.issued }}</span>
                <span class="w-[35%] p-1">{{ transaction.recepient }}</span>
            </div>
        </div>
    </Popover>

    <Popover ref="issuancePopover" class="w-[500px]">
        <div class="w-full flex flex-col justify-start font-poppins text-sm p-4 gap-4">
            <span class="text-xl uppercase font-semibold">Issue Stocks</span>
            <FloatLabel variant="on" class="w-full">
                <DatePicker v-model="stockIssuance.transaction_date"  class="w-full" size="small"/>
                <label>Transaction Date</label>
            </FloatLabel>
            <FloatLabel variant="on" class="w-full">
                <InputNumber v-model="stockIssuance.quantity" class="w-full" size="small"/>
                <label>Quantity</label>
            </FloatLabel>
            <FloatLabel variant="on" class="w-full">
                <InputText v-model="stockIssuance.recepient" class="w-full" size="small"/>
                <label>Recipient</label>
            </FloatLabel>
            <FloatLabel variant="on" class="w-full">
                <InputText v-model="stockIssuance.ptr_no" class="w-full" size="small"/>
                <label>PTR #</label>
            </FloatLabel>
            <FloatLabel variant="on" class="w-full">
                <InputText v-model="stockIssuance.remarks" class="w-full" size="small"/>
                <label>Remarks | Optional</label>
            </FloatLabel>
            <Button @click="issueStocks" label="Issue Stocks" icon="pi pi-save" severity="info"/>
        </div>
    </Popover>

</template>