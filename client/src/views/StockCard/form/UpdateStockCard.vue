<script setup lang="ts">
 import { ref,onMounted } from 'vue';
    import { useRouter,useRoute } from 'vue-router';
    import { useApi } from '@/composables/useApi';
    import { useAuthStore } from '@/stores/authStore';
    import { useToast } from 'primevue';

    import InputText from 'primevue/inputtext';
    import FloatLabel from 'primevue/floatlabel';
    import Button from 'primevue/button';
    import Popover from 'primevue/popover';
    import Select from 'primevue/select';
    import Textarea from 'primevue/textarea';
    import InputNumber from 'primevue/inputnumber';
    import DatePicker from 'primevue/datepicker';
    import Panel from 'primevue/panel';
    import FormLayout from '@/layouts/FormLayout.vue';

    const toast = useToast()
    const route = useRoute()
    const { fetchRequest,fetchRequests,postRequest } = useApi()
    const fund_sources = ref([])
    const sections = ref([])
    const measurement_units = ref([])
    const warehouses = ref([])
    const stockCardCategories = ref([])
    const stockCard = ref(<StockCard>{})

    onMounted(async() => {
        fetchStockCard()
        fetchFundSources()
        fetchSections()
        fetchMeasurementUnits()
        fetchWarehouses()
        fetchStockCardCategories()
    })

    const fetchStockCard = async () => {
        const response = await fetchRequest('stock_cards/find',{id:route.params.id})
        stockCard.value = response.data.stock_card
        console.log(stockCard.value)
    }

    const fetchFundSources = async () => {
        const response = await fetchRequest('fund_sources/selection')
        fund_sources.value = response.data.fund_sources
    }

    const fetchSections = async () => {
         const response = await fetchRequest('sections/selection',{})
         sections.value = response.data.sections
    }

    const fetchMeasurementUnits = async () => {
         const response = await fetchRequest('measurement_units/selection',{})
         measurement_units.value = response.data.measurement_units
    }

    const fetchWarehouses = async () => {
         const response = await fetchRequest('warehouses/selection',{})
         warehouses.value = response.data.warehouses
    }

    const fetchStockCardCategories = async () => {
         const response = await fetchRequest('stock_card_categories/selection',{})
         stockCardCategories.value = response.data.categories
    }

    const update = async () => {
        const response = await postRequest('stock_cards/update', stockCard.value)
        toast.add({ 
            severity: 'success', 
            summary: 'Stock Card Created', 
            detail: response.data.message, 
            life: 3000 
        });
        fetchStockCard()
    }

    // interfaces
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
        measurement_unit:number|null
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
    }




</script>

<template>
    <FormLayout title="Update Stock Card" return-path="/admin/stock_cards">
        <div class="w-full h-full flex flex-col justify-start items-center gap-4 p-4 font-poppins overflow-y-scroll">
            <Panel header="STOCK CARD DETAILS" toggleable class="p-2 w-full">
                <div class="w-full h-full flex flex-col justify-start items-start gap-4 p-2 border-t">
                    <div class="w-full flex justify-center items-center gap-2">
                        <FloatLabel class="w-full" variant="on">
                            <Select v-model="stockCard.fund_cluster" :options="fund_sources" optionLabel="name" optionValue="id" size="small" class="w-full" />
                            <label>Fund Source</label>
                        </FloatLabel>
                        <FloatLabel class="w-full" variant="on">
                            <Select v-model="stockCard.req_office" :options="sections" optionLabel="section_name" optionValue="section_id" size="small" class="w-full" filter/>
                            <label>Section</label>
                        </FloatLabel>
                        <FloatLabel class="w-full" variant="on">
                            <Select v-model="stockCard.measurement_unit" :options="measurement_units" optionLabel="name" optionValue="id" size="small" class="w-full" filter/>
                            <label>Measurement Unit</label>
                        </FloatLabel>
                        <FloatLabel class="w-full" variant="on">
                            <Select v-model="stockCard.warehouse" :options="warehouses" optionLabel="name" optionValue="id" size="small" class="w-full" />
                            <label>Warehouse</label>
                        </FloatLabel>
                        <FloatLabel class="w-full" variant="on">
                            <Select v-model="stockCard.category" :options="stockCardCategories" optionLabel="name" optionValue="id" size="small" class="w-full" />
                            <label>Category</label>
                        </FloatLabel>
                    </div>

                    <div class="w-full flex justify-center items-center gap-2">
                        <FloatLabel variant="on" class="w-full">
                            <InputText v-model="stockCard.supplier_name" class="w-full"/>
                            <label>Supplier Name</label>
                        </FloatLabel>
                        <FloatLabel variant="on" class="w-full">
                            <InputText v-model="stockCard.supplier_address" class="w-full"/>
                            <label>Supplier Address</label>
                        </FloatLabel>
                    </div>

                    <div class="w-full flex justify-center items-center gap-2">
                        <FloatLabel variant="on" class="w-full">
                            <InputText v-model="stockCard.iar_no" class="w-full"/>
                            <label>IAR No.</label>
                        </FloatLabel>
                        <FloatLabel class="w-full" variant="on">
                            <Select v-model="stockCard.procurement_mode" :options="[{id:1,name:'Procurement'},{id:2,name:'Donation'}]" optionLabel="name" optionValue="id" class="w-full" />
                            <label>Acquire Mode</label>
                        </FloatLabel>
                        <FloatLabel variant="on" class="w-full">
                            <InputText v-model="stockCard.contract_no" class="w-full"/>
                            <label>PO/PTR/Contract No.</label>
                        </FloatLabel>
                        <FloatLabel variant="on" class="w-full">
                            <InputText v-model="stockCard.batch_no" class="w-full"/>
                            <label>Batch/Lot No.</label>
                        </FloatLabel>
                    </div>

                    <div class="w-full flex justify-center items-center gap-2">
                        <FloatLabel variant="on" class="w-full">
                            <InputText v-model="stockCard.entity_name" class="w-full"/>
                            <label>Entity Name</label>
                        </FloatLabel>
                    </div>

                    <div class="w-full flex justify-center items-center gap-2">
                        <FloatLabel variant="on" class="w-full">
                            <InputText v-model="stockCard.stock_name" class="w-full"/>
                            <label>Stock Name</label>
                        </FloatLabel>
                        <FloatLabel variant="on" class="w-full">
                            <InputText v-model="stockCard.stock_no" class="w-full"/>
                            <label>Stock Number</label>
                        </FloatLabel>
                    </div>

                    <div class="w-full flex justify-center items-center gap-2">
                        <FloatLabel variant="on" class="w-full">
                            <Textarea v-model="stockCard.item_description"  class="w-full"/>
                            <label>Description</label>
                        </FloatLabel>
                    </div>

                    <div class="w-full flex justify-center items-center gap-2">
                        <FloatLabel variant="on" class="w-full">
                            <InputText v-model="stockCard.dosage_strength" class="w-full"/>
                            <label>Dosage Strength</label>
                        </FloatLabel>
                        <FloatLabel variant="on" class="w-full">
                            <InputText v-model="stockCard.dosage_form" class="w-full"/>
                            <label>Dosage Form</label>
                        </FloatLabel>
                    </div>

                    <div class="w-full flex justify-center items-center gap-2">
                        <FloatLabel variant="on" class="w-full">
                            <InputNumber v-model="stockCard.unit_cost" :minFractionDigits="2" :maxFractionDigits="5" class="w-full"/>
                            <label>Unit Cost</label>
                        </FloatLabel>
                        <FloatLabel variant="on" class="w-full">
                            <InputNumber v-model="stockCard.quantity" class="w-full"/>
                            <label>Quantity</label>
                        </FloatLabel>
                        <FloatLabel variant="on" class="w-full">
                            <DatePicker v-model="stockCard.expiry_date"  class="w-full" size="small"/>
                            <label>Expiry Date</label>
                        </FloatLabel>
                    </div>

                </div>
            </Panel>

            <Panel header="STOCK CARD Transactions" toggleable class="p-2 w-full">
                <div class="w-full h-full flex flex-col justify-start items-start p-2 border-t divide-y">
                    <div class="w-full flex justify-start items-start divide-x border-t border-x text-center font-semibold uppercase bg-[#E3EEF9] sticky top-0">
                        <span class="w-1/8">Transaction Date</span>
                        <span class="w-1/8">Received</span>
                        <span class="w-1/8">Issued</span>
                        <span class="w-1/8">Balance</span>
                        <span class="w-1/8">IAR #</span>
                        <span class="w-1/8">PTR #</span>
                        <span class="w-1/8">Remarks</span>
                        <span class="w-1/8">Recipient</span>
                    </div>
                    <div v-for="transaction in stockCard.stock_card_transactions" class="w-full flex justify-start items-start divide-x text-center font-light text-sm last:border-b border-x">
                        <span class="w-1/8"><input v-model="transaction.transaction_date" type="date" class="w-full outline-none p-1"></span>
                        <span class="w-1/8"><input v-model="transaction.received" type="number" class="w-full outline-none p-1"></span>
                        <span class="w-1/8"><input v-model="transaction.issued" type="number" class="w-full outline-none p-1"></span>
                        <span class="w-1/8"><input v-model="transaction.balance" type="number" class="w-full outline-none p-1"></span>
                        <span class="w-1/8"><input v-model="transaction.iar_no" type="text" class="w-full outline-none p-1"></span>
                         <span class="w-1/8"><input v-model="transaction.ptr_no" type="text" class="w-full outline-none p-1"></span>
                        <span class="w-1/8"><input v-model="transaction.remarks" type="text" class="w-full outline-none p-1"></span>
                        <span class="w-1/8"><input v-model="transaction.recepient" type="text" class="w-full outline-none p-1"></span>
                    </div>
                </div>
            </Panel>

            <div class="w-full flex justify-start items-center">
                <Button @click="update" label="Update Stock Card" icon="pi pi-save" severity="success"/>
            </div>
        </div>
    </FormLayout>

</template>