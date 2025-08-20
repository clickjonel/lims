<script setup lang="ts">
    import { ref,onMounted } from 'vue';
    import { useAuthStore } from '@/stores/authStore';
    import Card from 'primevue/card';
    import { useApi } from '@/composables/useApi';

    const { fetchRequest } = useApi()
    const auth = useAuthStore()
    const recents = ref(<Recents>{})
    const totals = ref(<Totals>{})

    onMounted(()=>{
       fetchDashboardData()
       console.log(auth.user)
    })

    const fetchDashboardData = async () => {
        const response = await fetchRequest('dashboard',{
            user_id: auth.isAdmin ? null : auth.user?.user_id ?? null
        })
        recents.value = response.data.recents
        totals.value = response.data.totals
    }

    interface DeliveryReceipt{
        delivery:Delivery
        delivery_date:string
        delivery_place:string
        dr_date:string
        dr_no:string
        id:number
    }

    interface Delivery {
        delivery_items:DeliveryItem[]
        end_user:EndUser
        requisitioning_section: Section
        iar_no:string
        po_no?:string
        ptr_no?:string
    }

    interface DeliveryItem{
        description:string
    }

    interface EndUser {
        full_name:string
    }

    interface Section {
        section_name:string
    }

    interface StockCardTransaction {
        transaction_date:string
        iar_no?:string
        ptr_no?:string
        received:number
        issued:number
        recepient:string
        stock_card:{
            stock_no:string
            stock_name:string
            item_description:string
        }
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

    interface Recents {
        properties:Property[]
        receipts:DeliveryReceipt[]
        transactions:StockCardTransaction[]
    }

    interface Totals {
        deliveries:number
        stocks:number
        properties:number
    }

</script>


<template>
    <div class="w-full h-full flex flex-col justify-start items-start gap-10 p-4 bg-[#E3EEF9] overflow-y-scroll">
        <div class="w-full grid grid-cols-3 gap-6">
            <Card>
                <template #title>Deliveries</template>
                <template #content>
                    <div class="w-full h-full flex justify-start items-center">
                        <span class="text-7xl font-black">{{ totals.deliveries }}</span>
                    </div>
                </template>
            </Card>
            <Card>
                <template #title>Stock Cards</template>
                <template #content>
                    <div class="w-full h-full flex justify-start items-center">
                        <span class="text-7xl font-black">{{ totals.stocks }}</span>
                    </div>
                </template>
            </Card>
            <Card>
                <template #title>Properties</template>
                <template #content>
                    <div class="w-full h-full flex justify-start items-center">
                        <span class="text-7xl font-black">{{ totals.properties }}</span>
                    </div>
                </template>
            </Card>
        </div>

        <div class="w-full flex flex-col justify-start items-center bg-white p-4 rounded-md gap-2">
            <span class="w-full text-2xl text-black font-semibold uppercase">Recent Deliveries</span>
            <div class="w-full flex flex-col justify-start items-center divide-y border-y">
                <div class="w-full h-full flex justify-start items-stretch border-x divide-x bg-[#E3EEF9]">
                    <span class="w-[10%] p-2">Delivery Date</span>
                    <span class="w-[10%] p-2">Delivery Place</span>
                    <span class="w-[10%] p-2">IAR #</span>
                    <span class="w-[10%] p-2">PO/PTR #</span>
                    <span class="w-[15%] p-2">Requisitioning Office</span>
                    <span class="w-[15%] p-2">End User</span>
                    <span class="w-[30%] p-2">Item/s</span>
                </div>
                <div v-for="receipt in recents.receipts" class="w-full h-full flex justify-start items-stretch border-x divide-x text-sm font-light">
                    <span class="w-[10%] py-1 px-2">{{ receipt.delivery_date }}</span>
                    <span class="w-[10%] py-1 px-2">{{ receipt.delivery_place }}</span>
                    <span class="w-[10%] py-1 px-2">{{ receipt.delivery.iar_no }}</span>
                    <span class="w-[10%] py-1 px-2">{{ receipt.delivery.po_no ?? receipt.delivery.ptr_no }}</span>
                    <span class="w-[15%] py-1 px-2">{{ receipt.delivery.requisitioning_section.section_name }}</span>
                    <span class="w-[15%] py-1 px-2">{{ receipt.delivery.end_user.full_name }}</span>
                    <div class="w-[30%] py-1 px-2 flex flex-col align-items-start gap-2">
                        <span :title="item.description" class="text-xs truncate" v-for="item in receipt.delivery.delivery_items" v-html="item.description"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full flex flex-col justify-start items-center bg-white p-4 rounded-md gap-2">
            <span class="w-full text-2xl text-black font-semibold uppercase">Recent Stock Transactions</span>
            <div class="w-full flex flex-col justify-start items-center divide-y border-y">
                <div class="w-full h-full flex justify-start items-stretch border-x divide-x bg-[#E3EEF9]">
                    <span class="w-[10%] p-2">Transaction Date</span>
                    <span class="w-[10%] p-2">Received</span>
                    <span class="w-[10%] p-2">Issued</span>
                    <span class="w-[10%] p-2">PTR/RIS/DR/SI/BL No.</span>
                    <span class="w-[15%] p-2">Recipient</span>
                    <span class="w-[15%] p-2">Stock Card #</span>
                    <span class="w-[30%] p-2">Item Description</span>
                </div>
                <div v-for="transaction in recents.transactions" class="w-full h-full flex justify-start items-stretch border-x divide-x text-sm font-light">
                    <span class="w-[10%] px-2 py-1">{{ transaction.transaction_date }}</span>
                    <span class="w-[10%] px-2 py-1">{{ transaction.received }}</span>
                    <span class="w-[10%] px-2 py-1">{{ transaction.issued }}</span>
                    <span class="w-[10%] px-2 py-1">{{ transaction.iar_no ?? transaction.ptr_no }}</span>
                    <span class="w-[15%] px-2 py-1">{{ transaction.recepient }}</span>
                    <span class="w-[15%] px-2 py-1">{{ transaction.stock_card.stock_no }}</span>
                    <span class="w-[30%] px-2 py-1">{{ transaction.stock_card.stock_name }}</span>
                </div>
            </div>
        </div>

        <div class="w-full flex flex-col justify-start items-center bg-white p-4 rounded-md gap-2">
            <span class="w-full text-2xl text-black font-semibold uppercase">Recent Added Properties</span>
            <div class="w-full flex flex-col justify-start items-center divide-y border-y">
                <div class="w-full h-full flex justify-start items-stretch border-x divide-x bg-[#E3EEF9]">
                    <span class="w-[20%] p-2">Property #</span>
                    <span class="w-[40%] p-2">Article</span>
                    <span class="w-[20%] p-2">End User</span>
                    <span class="w-[10%] p-2">Unit Cost</span>
                    <span class="w-[10%] p-2">Acquisition Date</span>
                </div>
                <div v-for="property in recents.properties" class="w-full h-full flex justify-start items-stretch border-x divide-x text-sm font-light">
                    <span class="w-[20%] px-2 py-1">{{ property.property_no }}</span>
                    <span class="w-[40%] px-2 py-1">{{ property.particulars }}</span>
                    <span class="w-[20%] px-2 py-1">{{ property.current_user?.user.full_name }}</span>
                    <span class="w-[10%] px-2 py-1">{{ property.unit_cost }}</span>
                    <span class="w-[10%] px-2 py-1">{{ property.first_user?.acquisition_date }}</span>
                </div>
            </div>
        </div>

    </div>
</template>

