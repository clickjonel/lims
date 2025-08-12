<script setup lang="ts">
    import { ref,onMounted } from 'vue';
    import { useApi } from '@/composables/useApi';
    import { useToast } from 'primevue';
    import { useRoute } from 'vue-router'

    import InputText from 'primevue/inputtext';
    import FloatLabel from 'primevue/floatlabel';
    import Button from 'primevue/button';
    import Select from 'primevue/select';
    import FormLayout from '@/layouts/FormLayout.vue';

    const { fetchRequest,postRequest } = useApi()
    const toast = useToast()
    const route = useRoute()
    const delivery = ref(<Delivery>{})
    const warehouses = ref([])
    const stockCardCategories = ref([])

     onMounted( async ()=>{
        await findDelivery()
        await fetchWarehouses()
        await fetchStockCardCategories()
    })

    const findDelivery = async () => {
        const response = await fetchRequest('deliveries/find',{id:route.params.id})
        delivery.value = response.data.delivery
    }

    const fetchWarehouses = async () => {
        const response = await fetchRequest('warehouses/selection',{})
        warehouses.value = response.data.warehouses
    }

    const fetchStockCardCategories = async () => {
        const response = await fetchRequest('stock_card_categories/selection',{})
        stockCardCategories.value = response.data.categories
    }

    const stock = async (item:DeliveryItem) => {
        const payload = formatPayload(item)
        const response = await postRequest('stock_cards/stock/item', payload)
        toast.add({ 
            severity: 'success', 
            summary: 'Created', 
            detail: response.data.message, 
            life: 3000 
        });
        findDelivery()
    }

    const formatPayload = (item:DeliveryItem) => {
        const payload = {
            stock_no: item.stock_no,
            stock_name: item.stock_name,
            contract_no: delivery.value.po_no ?? delivery.value.ptr_no,
            entity_name: delivery.value.entity_name,
            iar_no: delivery.value.iar_no,
            iar_date:delivery.value.iar_date,
            supplier_name: delivery.value.source_name,
            supplier_address: delivery.value.source_address,
            item_description: item.description,
            dosage_form: item.dosage_form,
            dosage_strength: item.dosage_strength,
            measurement_unit: item.measurement_unit,
            unit_cost: item.unit_cost,
            procurement_mode: delivery.value.payment_term,
            fund_cluster: delivery.value.fund_source,
            warehouse: item.warehouse,
            batch_no: item.batch_no,
            expiry_date: item.expiry_date,
            category: item.category,
            req_office: delivery.value.req_office,
            quantity: item.quantity,
            item_id:item.id
        }

        return payload;
    }

    interface Delivery {
        delivery_items:DeliveryItem[]
        po_no:string
        ptr_no:string
        entity_name:string
        iar_no:string
        iar_date:string,
        source_name:string
        source_address:string
        payment_term:number
        fund_source:number
        req_office:number
    }

    interface DeliveryItem {
        stock_no: string,
        stock_name: string,
        contract_no: string,
        entity_name: string,
        iar_no: string,
        supplier_name: string,
        supplier_address: string,
        item_description: string,
        dosage_form: string,
        dosage_strength: string,
        measurement_unit: number,
        unit_cost: number,
        procurement_mode: number,
        fund_cluster: number,
        warehouse: number,
        batch_no: string,
        expiry_date: string,
        category: number,
        req_office: number,
        quantity: number,
        description:string,
        availability:number,
        id:number
        stocked:number
    }


</script>

<template>
    <FormLayout title="Create Stock Card for Delivery Items" return-path="/admin/deliveries">
        <div class="w-full flex flex-col justify-start items-start gap-4 p-2">
            <div v-for="item in delivery.delivery_items" class="w-full flex justify-between items-center gap-4 p-2 border-y bg-[#E3EEF9]">
                <div class="w-full flex flex-col justify-start items-start gap-4">
                    <span class="text-sm font-light">{{ item.description }}</span>

                    <div class="w-full flex justify-start items-start gap-2">
                        <FloatLabel variant="on" class="w-1/3">
                            <InputText v-model="item.stock_no" class="w-full" size="small"/>
                            <label>Stock Number</label>
                        </FloatLabel>
                        <FloatLabel variant="on" class="w-full">
                            <InputText v-model="item.stock_name" class="w-full" size="small"/>
                            <label>Stock Name</label>
                        </FloatLabel>
                    </div>

                    <div class="w-full flex justify-start items-start gap-2">
                        <FloatLabel variant="on" class="w-full">
                            <InputText v-model="item.dosage_form" class="w-full" size="small"/>
                            <label>Dosage Form</label>
                        </FloatLabel>
                        <FloatLabel variant="on" class="w-full">
                            <InputText v-model="item.dosage_strength" class="w-full" size="small"/>
                            <label>Dosage Strength</label>
                        </FloatLabel>
                    </div>

                    <div class="w-full flex justify-start items-start gap-2">
                        <FloatLabel class="w-full" variant="on">
                            <Select v-model="item.category" :options="stockCardCategories" optionLabel="name" optionValue="id" size="small" class="w-full" />
                            <label>Category</label>
                        </FloatLabel>
                        <FloatLabel class="w-full" variant="on">
                            <Select v-model="item.warehouse" :options="warehouses" optionLabel="name" optionValue="id" size="small" class="w-full" />
                            <label>Warehouse</label>
                        </FloatLabel>
                    </div>

                    <Button @click="stock(item)" label="Create Stock Card for This Item" severity="info" class="text-xs" size="small" :disabled = "item.availability === 2 || item.stocked === 1"/>
                    
                    <div class="w-full flex flex-col justify-start items-start text-red-800 font-lexend italic font-light text-xs">
                        <span v-if="item.availability === 2">*Item is already added to stock card</span>
                        <span v-if="item.stocked === 1">*Item is a balance delivery</span>
                    </div>

                </div>
            </div>
        </div>
    </FormLayout>
</template>