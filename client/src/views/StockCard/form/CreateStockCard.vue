<script setup lang="ts">
 import { ref,onMounted } from 'vue';
    import { useApi } from '@/composables/useApi';
    import { useToast } from 'primevue';

    import InputText from 'primevue/inputtext';
    import FloatLabel from 'primevue/floatlabel';
    import Button from 'primevue/button';
    import Select from 'primevue/select';
    import Textarea from 'primevue/textarea';
    import InputNumber from 'primevue/inputnumber';
    import DatePicker from 'primevue/datepicker';
    import Panel from 'primevue/panel';
    import FormLayout from '@/layouts/FormLayout.vue';

    const toast = useToast()
    const { fetchRequest,fetchRequests,postRequest } = useApi()
    const fund_sources = ref([])
    const sections = ref([])
    const measurement_units = ref([])
    const warehouses = ref([])
    const stockCardCategories = ref([])


    var stockCard = ref(<StockCardCreate>{
        stock_no:'',
        stock_name:'',
        contract_no:'',
        entity_name:'',
        iar_no:'',
        supplier_name:'',
        supplier_address:'',
        item_description:'',
        dosage_form:'',
        dosage_strength:'',
        measurement_unit:null,
        unit_cost:null,
        procurement_mode:null,
        fund_cluster:null,
        warehouse:null,
        batch_no:'',
        expiry_date:null,
        category:null,
        req_office:null,
        quantity:null,
    })
    

    onMounted(async() => {
      fetchFundSources()
      fetchSections()
      fetchMeasurementUnits()
      fetchWarehouses()
      fetchStockCardCategories()
    })

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

    const create = async () => {
        const payload = {
            stock_no: stockCard.value.stock_no,
            stock_name: stockCard.value.stock_name,
            contract_no: stockCard.value.contract_no,
            entity_name: stockCard.value.entity_name,
            iar_no: stockCard.value.iar_no,
            supplier_name: stockCard.value.supplier_name,
            supplier_address: stockCard.value.supplier_address,
            item_description: stockCard.value.item_description,
            dosage_form: stockCard.value.dosage_form,
            dosage_strength: stockCard.value.dosage_strength,
            measurement_unit: stockCard.value.measurement_unit,
            unit_cost: stockCard.value.unit_cost,
            procurement_mode: stockCard.value.procurement_mode,
            fund_cluster: stockCard.value.fund_cluster,
            warehouse: stockCard.value.warehouse,
            batch_no: stockCard.value.batch_no,
            expiry_date: stockCard.value.expiry_date ? stockCard.value.expiry_date.toISOString().split('T')[0] : '',
            category: stockCard.value.category,
            req_office: stockCard.value.req_office,
            quantity: stockCard.value.quantity,
            received_date: stockCard.value.received_date ? stockCard.value.received_date.toISOString().split('T')[0] : ''
        }

        const response = await postRequest('stock_cards/create', payload)
        toast.add({ 
            severity: 'success', 
            summary: 'Stock Card Created', 
            detail: response.data.message, 
            life: 3000 
        });

        stockCard.value.stock_no = ''
        stockCard.value.stock_name = ''
        stockCard.value.contract_no = ''
        stockCard.value.entity_name = ''
        stockCard.value.iar_no = ''
        stockCard.value.supplier_name = ''
        stockCard.value.supplier_address = ''
        stockCard.value.item_description = ''
        stockCard.value.dosage_form = ''
        stockCard.value.dosage_strength = ''
        stockCard.value.measurement_unit = null
        stockCard.value.unit_cost = null
        stockCard.value.procurement_mode = null
        stockCard.value.fund_cluster = null
        stockCard.value.warehouse = null
        stockCard.value.batch_no = ''
        stockCard.value.expiry_date = null
        stockCard.value.category = null
        stockCard.value.req_office = null
        stockCard.value.quantity = null
    }

    // interfaces
    interface StockCardCreate {
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
    }




</script>

<template>
    <FormLayout title="Create New Stock Card" return-path="/admin/stock_cards">
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
                            <Select v-model="stockCard.procurement_mode" :options="[{id:1,name:'Procurement'},{id:2,name:'Donation'}]" optionLabel="name" optionValue="id" size="small" class="w-full" />
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
                        <FloatLabel variant="on" class="w-full">
                            <DatePicker v-model="stockCard.received_date"  class="w-full" size="small"/>
                            <label>Received Date</label>
                        </FloatLabel>
                    </div>

                </div>
            </Panel>

            <div class="w-full flex justify-start items-center">
                <Button @click="create" label="Create Stock Card" icon="pi pi-save" severity="success"/>
            </div>
        </div>
    </FormLayout>

</template>