<script setup lang="ts">
    import { ref, onMounted } from 'vue';
    import { useApi } from '@/composables/useApi';
    import dohLogo from '@/assets/images/doh_logo.png';
    import { useRoute } from 'vue-router';

    const { fetchRequest } = useApi();
    const route = useRoute();
    const stock_card = ref(<StockCard>{});

    onMounted(async () => {
        await fetchStockCard();
    });

    const fetchStockCard = async () => {
        const response = await fetchRequest('stock_cards/find',{id: route.params.id});
        if(response.apiResponseStatus === 200){
            stock_card.value = response.data.stock_card;
            console.log(stock_card.value);
        }
    }

     interface StockCard {
        stock_no:string
        stock_name:string
        contract_no:string
        entity_name:string
        iar_no:string
        supplier_name:string
        supplier_address:string
        item_description:string
        dosage_form?:string
        dosage_strength?:string
        measurement_unit:{
            name:string
        }
        unit_cost:number|null
        procurement_mode:number|null
        fund_cluster:number|null
        warehouse:number|null
        batch_no?:string
        expiry_date?:Date|null
        category?:number|null
        req_office:number|null
        quantity:number|null
        received_date:Date|null
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
        balance:number
        remarks?:string
        recepient?:string
        total_cost:number
    }

</script>

<template>

    <div class="w-full flex flex-col justify-start items-start p-2 font-poppins">

        <div class="w-full flex justify-center items-center py-2">
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
        </div>

        <div class="w-full flex flex-col justify-start items-stretch divide-y font-lexend text-[10px] outline">
            <div class="min-w-full flex justify-start items-stretch divide-x">
                <span class="min-w-[60%] pl-1">Stock Card</span>
                <span class="min-w-[20%] pl-1">Stock Keeping Unit(SKU Code)</span>
                <span class="min-w-[20%] pl-1">{{ stock_card.stock_no }}</span>
            </div>
            <div class="min-w-full flex justify-start items-stretch divide-x">
                <span class="min-w-[20%] pl-1">PO/PTR/Contract Number</span>
                <span class="min-w-[40%] pl-1">{{ stock_card.contract_no }}</span>
                <span class="min-w-[20%] pl-1">Entity Name</span>
                <span class="min-w-[20%] pl-1">{{ stock_card.entity_name }}</span>
            </div>
            <div class="min-w-full flex justify-start items-stretch divide-x">
                <span class="min-w-[20%] pl-1">Supplier</span>
                <span class="min-w-[40%] pl-1">{{ stock_card.supplier_name }}</span>
                <span class="min-w-[20%] pl-1">Fund Source</span>
                <span class="min-w-[20%] pl-1">{{ stock_card.fund_cluster }}</span>
            </div>
            <div class="min-w-full flex justify-start items-stretch divide-x">
                <span class="min-w-[20%] pl-1">Item Description</span>
                <span class="min-w-[40%] pl-1">{{ stock_card.item_description }}</span>
                <span class="min-w-[20%] pl-1">Unit Cost</span>
                <span class="min-w-[20%] pl-1">{{ stock_card.unit_cost }}</span>
            </div>
            <div class="min-w-full flex justify-start items-stretch divide-x">
                <span class="min-w-[20%] pl-1">Dosage Form</span>
                <span class="min-w-[40%] pl-1">{{ stock_card.dosage_form }}</span>
                <span class="min-w-[20%] pl-1">IAR Number</span>
                <span class="min-w-[20%] pl-1">{{ stock_card.iar_no }}</span>
            </div>
            <div class="min-w-full flex justify-start items-stretch divide-x">
                <span class="min-w-[20%] pl-1">Dosage Strength</span>
                <span class="min-w-[40%] pl-1">{{ stock_card.dosage_strength }}</span>
                <span class="min-w-[20%] pl-1">Mode of Procurement</span>
                <span class="min-w-[20%] pl-1">{{ stock_card.procurement_mode }}</span>
            </div>
            <div class="min-w-full flex justify-start items-stretch divide-x">
                <span class="min-w-[20%] pl-1">Unit of</span>
                <span class="min-w-[40%] pl-1">{{ stock_card.measurement_unit?.name }}</span>
                <span class="min-w-[20%] pl-1">End User</span>
                <span class="min-w-[20%] pl-1">{{ stock_card.section?.section_name }}</span>
            </div>
            <div class="min-w-full flex justify-start items-stretch divide-x">
                <span class="min-w-[20%] pl-1">Batch Number</span>
                <span class="min-w-[40%] pl-1">{{ stock_card.batch_no }}</span>
                <span class="min-w-[20%] pl-1">Expiry Date</span>
                <span class="min-w-[20%] pl-1">{{ stock_card.expiry_date }}</span>
            </div>
            <div class="min-w-full flex justify-start items-stretch divide-x bg-gray-400">
                <span class="min-w-[20%] py-2"></span>
            </div>
            <div class="min-w-full flex justify-start items-stretch divide-x">
                <span class="min-w-[10%] pl-1">Date</span>
                <div class="min-w-[60%] flex flex-col justify-start items-stretch divide-y">
                    <div class="w-full flex justify-start items-stretch">
                        <span class="w-full text-center font-medium uppercase">Quantity</span>
                    </div>
                    <div class="w-full flex justify-start items-stretch divide-x text-center">
                        <span class="min-w-[25%]">Received</span>
                        <span class="min-w-[25%]">Issued</span>
                        <span class="min-w-[25%]">Balance</span>
                        <span class="min-w-[25%]">Total Cost</span>
                    </div>
                </div>
                <span class="min-w-[15%] pl-1">DR/SI/RIS/PTR/BL No.</span>
                <span class="min-w-[15%] pl-1">Remarks</span>
            </div>
            <div class="w-full flex flex-col justify-start items-start divide-y">
                <div v-for="transaction in stock_card.stock_card_transactions" class="min-w-full flex justify-start items-stretch divide-x text-center font-light">
                    <span class="min-w-[10%]">{{ transaction.transaction_date }}</span>
                    <span class="min-w-[15%]">{{ transaction.received }}</span>
                    <span class="min-w-[15%]">{{ transaction.issued }}</span>
                    <span class="min-w-[15%]">{{ transaction.balance }}</span>
                    <span class="min-w-[15%]">{{ transaction.total_cost }}</span>
                    <span class="min-w-[15%]">{{ transaction.iar_no ?? transaction.ptr_no }}</span>
                    <span class="min-w-[15%]">{{ transaction.remarks }}</span>
                </div>
            </div>

        </div>

    </div>

</template>