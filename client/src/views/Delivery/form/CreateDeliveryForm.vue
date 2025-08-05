<script setup lang="ts">
    import { ref,onMounted } from 'vue';
    import { useApi } from '@/composables/useApi';
    import { useToast } from 'primevue';
    import type { Delivery, DeliveryFormatted, DeliveryInvoice, DeliveryItem, DeliveryReceipt } from '@/types/DeliveryTypes.ts';
    import type { FundSource } from '@/types/FundSourceTypes.ts';
    import type { Section, SectionPersonnel } from '@/types/SectionTypes.ts';
    import type { MeasurementUnit } from '@/types/MeasurementUnitTypes.ts';


    import FormLayout from '@/layouts/FormLayout.vue';
    import Select from 'primevue/select';
    import FloatLabel from 'primevue/floatlabel';
    import InputText from 'primevue/inputtext';
    import DatePicker from 'primevue/datepicker';
    import Button from 'primevue/button';
    import Panel from 'primevue/panel';
    import Popover from 'primevue/popover';
    import InputNumber from 'primevue/inputnumber';
    import Textarea from 'primevue/textarea';


    const { fetchRequest,postRequest } = useApi()
    const toast = useToast()

    const fundSources = ref(<FundSource[]>[])
    const sections = ref(<Section[]>[])
    const sectionPersonnel = ref(<SectionPersonnel[]>[])
    const measurementUnits = ref(<MeasurementUnit[]>[])

    const invoicePopover = ref()
    const receiptPopover = ref()
    const itemPopover = ref()

    var invoice = ref(<DeliveryInvoice>{
        invoice_no: '',
        invoice_date: null
    })

    var receipt = ref(<DeliveryReceipt>{
        dr_no:'',
        dr_date:null,
        delivery_date:null,
        delivery_place:'DOH-CHD-CAR' 
    })

    var item = ref(<DeliveryItem>{
        availability:null,
        manufacturer:'',
        manufacturing_date:null,
        expiry_date:null,
        unit_cost:null,
        quantity:null,
        batch_lot_number:'',
        shelf_life:null,
        measurement_unit:null,
        description:''
    })

    const delivery = ref(<Delivery>{
        entity_name:'',
        fund_source:null,
        source_name:'',
        source_address:'',
        iar_no:'',
        iar_date:null,
        po_no:'',
        po_date:null,
        ptr_no:'',
        ptr_date:null,
        bl_no:'',
        bl_date:null,
        dnf_no:'',
        dnf_date:null,
        req_office:null,
        end_user:null,
        payment_term:null,
        completion:null,
        purpose:'',
        invoices:[],
        receipts:[],
        items:[]
    })

    onMounted(()=>{
        fetchFundSources()
        fetchSections()
        fetchMeasurementUnits()
    })

    const fetchFundSources = async () => {
         const response = await fetchRequest('fund_sources/selection',{})
         fundSources.value = response.data.fund_sources
    }

    const fetchSections = async () => {
         const response = await fetchRequest('sections/selection',{})
         sections.value = response.data.sections
    }

    const fetchSectionPersonnel = async () => {
        const response = await fetchRequest('sections/selection/personnel',{section_id:delivery.value.req_office})
        sectionPersonnel.value = response.data.personnel
    }

    const fetchMeasurementUnits = async () => {
         const response = await fetchRequest('measurement_units/selection',{})
         measurementUnits.value = response.data.measurement_units
    }

    const toggleInvoicePopover = (event:any) => {
         invoicePopover.value.toggle(event);
    }

    const toggleReceiptPopover = (event:any) => {
         receiptPopover.value.toggle(event);
    }

    const toggleItemPopover = (event:any) => {
         itemPopover.value.toggle(event);
    }

    const pushInvoice = () => {
       if (invoice.value.invoice_no !== '' || invoice.value.invoice_date !== null) {
            const invoiceFormatted = {
                invoice_no:invoice.value.invoice_no,
                invoice_date:invoice.value.invoice_date ? invoice.value.invoice_date.toISOString().split('T')[0] : ''
            }

            delivery.value.invoices.push(invoiceFormatted);
            invoice.value = { invoice_no: '', invoice_date: null };
            invoicePopover.value.hide();
        } 
        else {
            toast.add({ 
                severity: 'error', 
                summary: 'Missing Fields', 
                detail: 'Invoice Must at Least Either have a Number or a Date', 
                life: 3000 
            });
        }
    }

    const pushReceipt = () => {
       if (receipt.value.delivery_date !== null && receipt.value.delivery_place !== '') {
            const receiptFormatted = {
                dr_no:receipt.value.dr_no,
                dr_date:receipt.value.dr_date ? receipt.value.dr_date.toISOString().split('T')[0] : '',
                delivery_date:receipt.value.delivery_date ? receipt.value.delivery_date.toISOString().split('T')[0] : '',
                delivery_place:receipt.value.delivery_place 
            }

            delivery.value.receipts.push(receiptFormatted);
            receipt.value = { dr_no:'',
                dr_date:null,
                delivery_date:null,
                delivery_place:'DOH-CHD-CAR'
            };

            receiptPopover.value.hide();
        } 
        else {
            toast.add({ 
                severity: 'error', 
                summary: 'Missing Fields', 
                detail: 'Required Fields: Delivery Date and Delivery Place', 
                life: 3000 
            });
        }
    }

    const pushItem = () => {
       if (item.value.availability !== null && item.value.quantity !== null && item.value.unit_cost !== null && item.value.measurement_unit !== null && item.value.description !== null) {
            const itemFormatted = {
                availability:item.value.availability,
                manufacturer:item.value.manufacturer,
                manufacturing_date:item.value.manufacturing_date ? item.value.manufacturing_date.toISOString().split('T')[0] : '',
                expiry_date:item.value.expiry_date ? item.value.expiry_date.toISOString().split('T')[0] : '',
                unit_cost:item.value.unit_cost,
                quantity:item.value.quantity,
                batch_lot_number:item.value.batch_lot_number,
                shelf_life:item.value.shelf_life,
                measurement_unit:item.value.measurement_unit?.id,
                measurement_unit_name:item.value.measurement_unit?.name,
                description:item.value.description
            }

            delivery.value.items.push(itemFormatted);
            item.value = {
                availability:null,
                manufacturer:'',
                manufacturing_date:null,
                expiry_date:null,
                unit_cost:null,
                quantity:null,
                batch_lot_number:'',
                shelf_life:null,
                measurement_unit:null,
                description:''
            }

            itemPopover.value.hide();
        } 
        else {
            toast.add({ 
                severity: 'error', 
                summary: 'Missing Fields', 
                detail: 'Required Fields: Availability, Quantity, Unit Cost, Measurement Unit, Description', 
                life: 3000 
            });
        }
    }

    const save = async () => {
        formatDelivery()
    }

    const formatDelivery = async () => {
        var deliveryFormatted:DeliveryFormatted = {
            entity_name:delivery.value.entity_name,
            fund_source:delivery.value.fund_source,
            source_name:delivery.value.source_name,
            source_address:delivery.value.source_address,
            iar_no:delivery.value.iar_no,
            iar_date:delivery.value.iar_date ? delivery.value.iar_date.toISOString().split('T')[0] : '',
            po_no:delivery.value.po_no,
            po_date:delivery.value.po_date ? delivery.value.po_date.toISOString().split('T')[0] : '',
            ptr_no:delivery.value.ptr_no,
            ptr_date:delivery.value.ptr_date ? delivery.value.ptr_date.toISOString().split('T')[0] : '',
            bl_no:delivery.value.bl_no,
            bl_date:delivery.value.bl_date ? delivery.value.bl_date.toISOString().split('T')[0] : '',
            dnf_no:delivery.value.dnf_no,
            dnf_date:delivery.value.dnf_date ? delivery.value.dnf_date.toISOString().split('T')[0] : '',
            req_office:delivery.value.req_office,
            end_user:delivery.value.end_user,
            payment_term:delivery.value.payment_term,
            completion:delivery.value.completion,
            purpose:delivery.value.purpose,
            invoices:delivery.value.invoices,
            receipts:delivery.value.receipts,
            items:delivery.value.items
        }

        const response = await postRequest('deliveries/create',deliveryFormatted)
        toast.add({ 
            severity: 'success', 
            summary: 'Delivery Created', 
            detail: response.data.message, 
            life: 3000 
        });

        delivery.value.entity_name = '',
        delivery.value.fund_source = null,
        delivery.value.source_name = '',
        delivery.value.source_address = '',
        delivery.value.iar_no = '',
        delivery.value.iar_date = null,
        delivery.value.po_no = '',
        delivery.value.po_date = null,
        delivery.value.ptr_no = '',
        delivery.value.ptr_date = null,
        delivery.value.bl_no = '',
        delivery.value.bl_date = null,
        delivery.value.dnf_no = '',
        delivery.value.dnf_date = null,
        delivery.value.req_office = null,
        delivery.value.end_user = null,
        delivery.value.payment_term = null,
        delivery.value.completion = null,
        delivery.value.purpose = '',
        delivery.value.invoices = [],
        delivery.value.receipts = [],
        delivery.value.items = []
    }

</script>

<template>
    <FormLayout title="Create New Delivery" return-path="/admin/deliveries">
        <div class="w-full h-full flex flex-col justify-start items-center gap-4 p-4 overflow-y-scroll">
            <Panel header="DELIVERY DETAILS" toggleable class="p-2 w-full">
                <div class="w-full h-full flex flex-col justify-start items-start gap-4 p-2 border-t">
                    <!-- selections -->
                    <div class="w-full flex flex-col justify-start items-start gap-2">
                        <div class="w-full flex justify-between items-center gap-2"> 
                            <FloatLabel class="w-full" variant="on">
                                <Select v-model="delivery.fund_source" :options="fundSources" optionLabel="name" optionValue="id" size="small" class="w-full" />
                                <label>Fund Source</label>
                            </FloatLabel>
                            <FloatLabel class="w-full" variant="on">
                                <Select v-model="delivery.req_office" :options="sections" optionLabel="section_name" optionValue="section_id" filter size="small" class="w-full" @change="fetchSectionPersonnel"/>
                                <label>Requisitioning Office</label>
                            </FloatLabel>
                            <FloatLabel class="w-full" variant="on">
                                <Select v-model="delivery.end_user" :options="sectionPersonnel" optionLabel="full_name" optionValue="user_id" size="small" class="w-full" />
                                <label>End User</label>
                            </FloatLabel>
                            <FloatLabel class="w-full" variant="on">
                                <Select v-model="delivery.payment_term" :options="[{id:1,name:'Charge'},{id:2,name:'Donation'}]" optionLabel="name" optionValue="id" size="small" class="w-full" />
                                <label>Payment Terms</label>
                            </FloatLabel>
                            <FloatLabel class="w-full" variant="on">
                                <Select v-model="delivery.completion" :options="[{id:1,name:'Partial'},{id:2,name:'Complete'},{id:3,name:'Completion'}]" optionLabel="name" optionValue="id" size="small" class="w-full" />
                                <label>Completion</label>
                            </FloatLabel>
                        </div>
                    </div>

                    <!-- entity -->
                    <div class="w-full flex flex-col justify-start items-start gap-2">
                        <div class="w-full flex justify-between items-center gap-2"> 
                            <FloatLabel variant="on" class="w-full">
                                <InputText v-model="delivery.entity_name"  class="w-full" size="small"/>
                                <label>Entity</label>
                            </FloatLabel>
                        </div>
                    </div>

                        <!-- porpuse -->
                    <div class="w-full flex flex-col justify-start items-start gap-2">
                        <div class="w-full flex justify-between items-center gap-2"> 
                            <FloatLabel variant="on" class="w-full">
                                <InputText v-model="delivery.purpose"  class="w-full" size="small"/>
                                <label>Purpose</label>
                            </FloatLabel>
                        </div>
                    </div>

                    <!-- Source -->
                    <div class="w-full flex flex-col justify-start items-start gap-2">
                        <div class="w-full flex justify-between items-center gap-2"> 
                            <FloatLabel variant="on" class="w-full">
                                <InputText v-model="delivery.source_name"  class="w-full" size="small"/>
                                <label>Source Name</label>
                            </FloatLabel>
                            <FloatLabel variant="on" class="w-full">
                                <InputText v-model="delivery.source_address"  class="w-full" size="small"/>
                                <label>Source Address</label>
                            </FloatLabel>
                        </div>
                    </div>

                    <!-- IAR -->
                    <div class="w-full flex flex-col justify-start items-start gap-2">
                        <div class="w-full flex justify-between items-center gap-2"> 
                            <FloatLabel variant="on" class="w-full">
                                <InputText v-model="delivery.iar_no"  class="w-full" size="small"/>
                                <label>IAR No</label>
                            </FloatLabel>
                            <FloatLabel variant="on" class="w-full">
                                <DatePicker v-model="delivery.iar_date"  class="w-full" size="small"/>
                                <label>IAR Date</label>
                            </FloatLabel>
                        </div>
                    </div>

                        <!-- PO -->
                    <div class="w-full flex flex-col justify-start items-start gap-2">
                        <div class="w-full flex justify-between items-center gap-2"> 
                            <FloatLabel variant="on" class="w-full">
                                <InputText v-model="delivery.po_no"  class="w-full" size="small"/>
                                <label>Purchase Order No</label>
                            </FloatLabel>
                                <FloatLabel variant="on" class="w-full">
                                <DatePicker v-model="delivery.po_date"  class="w-full" size="small"/>
                                <label>Purchase Order Date</label>
                            </FloatLabel>
                        </div>
                    </div>

                    <!-- Ptr -->
                    <div class="w-full flex flex-col justify-start items-start gap-2">
                        <div class="w-full flex justify-between items-center gap-2"> 
                            <FloatLabel variant="on" class="w-full">
                                <InputText v-model="delivery.ptr_no"  class="w-full" size="small"/>
                                <label>Property Transfer No</label>
                            </FloatLabel>
                                <FloatLabel variant="on" class="w-full">
                                <DatePicker v-model="delivery.ptr_date"  class="w-full" size="small"/>
                                <label>Property Transfer Date</label>
                            </FloatLabel>
                        </div>
                    </div>

                        <!-- bl -->
                    <div class="w-full flex flex-col justify-start items-start gap-2">
                        <div class="w-full flex justify-between items-center gap-2"> 
                            <FloatLabel variant="on" class="w-full">
                                <InputText v-model="delivery.bl_no"  class="w-full" size="small"/>
                                <label>Bill of Laiding No</label>
                            </FloatLabel>
                                <FloatLabel variant="on" class="w-full">
                                <DatePicker v-model="delivery.bl_date"  class="w-full" size="small"/>
                                <label>Bill of Laiding Date</label>
                            </FloatLabel>
                        </div>
                    </div>

                        <!-- dnf -->
                    <div class="w-full flex flex-col justify-start items-start gap-2">
                        <div class="w-full flex justify-between items-center gap-2"> 
                            <FloatLabel variant="on" class="w-full">
                                <InputText v-model="delivery.dnf_no"  class="w-full" size="small"/>
                                <label>Delivery Notification Form No</label>
                            </FloatLabel>
                                <FloatLabel variant="on" class="w-full">
                                <DatePicker v-model="delivery.dnf_date"  class="w-full" size="small"/>
                                <label>Delivery Notification Form Date</label>
                            </FloatLabel>
                        </div>
                    </div>

                </div>
            </Panel>
        
            <Panel header="DELIVERY INVOICES" toggleable class="p-2 w-full">
                <div class="w-full flex flex-col justify-start items-start border-t">
                    <Button @click="toggleInvoicePopover" label="Add Invoice" icon="pi pi-plus" severity="info" class="my-4 text-xs" size="small"/>
                    <div class="w-full flex justify-start items-stretch gap-2 border font-semibold uppercase divide-x bg-[#E3EEF9]">
                        <span class="w-1/2 p-2">Invoice Number</span>
                        <span class="w-1/2 p-2">Invoice Date</span>
                    </div>
                     <div v-if="delivery.invoices.length > 0" v-for="invoice in delivery.invoices" class="w-full flex justify-start items-stretch gap-2 border-b border-x font-light text-sm divide-x">
                        <span class="w-1/2 p-1">{{ invoice.invoice_no }}</span>
                        <span class="w-1/2 p-1">{{ invoice.invoice_date }}</span>
                    </div>
                    <div v-if="delivery.invoices.length === 0" class="w-full flex justify-center items-center border-x border-b p-4">
                        <span class="w-full text-center animate-bounce">No Invoice/s Added</span>
                    </div>
                </div>
            </Panel>

            <Panel header="DELIVERY RECEIPTS" toggleable class="p-2 w-full">
                <div class="w-full flex flex-col justify-start items-start border-t">
                    <Button @click="toggleReceiptPopover" label="Add Receipt" icon="pi pi-plus" severity="info" class="my-4 text-xs" size="small"/>
                    <div class="w-full flex justify-start items-stretch gap-2 border font-semibold uppercase divide-x bg-[#E3EEF9]">
                        <span class="w-1/4 p-2">Delivery Receipt Number</span>
                        <span class="w-1/4 p-2">Delivery Receipt Date</span>
                        <span class="w-1/4 p-2">Delivery Date</span>
                        <span class="w-1/4 p-2">Delivery Place</span>
                    </div>
                     <div v-if="delivery.receipts.length > 0" v-for="receipt in delivery.receipts" class="w-full flex justify-start items-stretch gap-2 border-b border-x font-light text-sm divide-x">
                        <span class="w-1/4 p-1">{{ receipt.dr_no }}</span>
                        <span class="w-1/4 p-1">{{ receipt.dr_date }}</span>
                        <span class="w-1/4 p-1">{{ receipt.delivery_date }}</span>
                        <span class="w-1/4 p-1">{{ receipt.delivery_place }}</span>
                    </div>
                    <div v-if="delivery.receipts.length === 0" class="w-full flex justify-center items-center border-x border-b p-4">
                        <span class="w-full text-center animate-bounce">No Receipt/s Added</span>
                    </div>
                </div>
            </Panel>

            <Panel header="DELIVERY ITEMS" toggleable class="p-2 w-full">
                <div class="w-full flex flex-col justify-start items-start border-t">
                    <Button @click="toggleItemPopover" label="Add Item" icon="pi pi-plus" severity="info" class="my-4 text-xs" size="small"/>
                    <div class="w-full flex justify-start items-stretch gap-2 border font-semibold uppercase divide-x bg-[#E3EEF9]">
                        <span class="w-[15%] min-w-[15%] p-2">Manufacturer -<br> Manufacturing Date</span>
                        <span class="w-[10%] min-w-[10%] p-2">Expiry Date -<br> Shelf Life</span>
                        <span class="w-[10%] p-2 min-w-[10%]">Batch/Lot Numbers</span>
                        <span class="w-[10%] p-2 min-w-[10%]">Quantity</span>
                        <span class="w-[10%] p-2 min-w-[10%]">Unit Cost</span>
                        <span class="w-[10%] p-2 min-w-[10%]">Measurement Unit</span>
                        <span class="w-[35%] p-2 min-w-[35%]">Description</span>
                    </div>
                     <div v-if="delivery.items.length > 0" v-for="item in delivery.items" class="w-full flex justify-start items-stretch gap-2 border-b border-x font-light text-sm divide-x">
                        <span class="w-[15%]  min-w-[15%] p-2">{{ item.manufacturer ?? '' }} <br> {{ item.manufacturing_date }}</span>
                        <span class="w-[10%]  min-w-[10%] p-2"> {{ item.expiry_date }} <br> {{ item.shelf_life }} </span>
                        <span class="w-[10%] p-2  min-w-[10%]">{{ item.batch_lot_number }}</span>
                        <span class="w-[10%] p-2  min-w-[10%]">{{ item.quantity }}</span>
                        <span class="w-[10%] p-2  min-w-[10%]">{{ item.unit_cost }}</span>
                        <span class="w-[10%] p-2  min-w-[10%]">{{ item.measurement_unit_name }}</span>
                        <span class="w-[35%] p-2  min-w-[35%] whitespace-pre-wrap">{{ item.description }}</span>
                    </div>
                    <div v-if="delivery.items.length === 0" class="w-full flex justify-center items-center border-x border-b p-4">
                        <span class="w-full text-center animate-bounce">No Item/s Added</span>
                    </div>
                </div>
            </Panel>

            <div class="w-full flex justify-start items-center">
                <Button @click="save" label="Create Delivery" icon="pi pi-save" severity="success"/>
            </div>
        </div>
    </FormLayout>

    <Popover ref="invoicePopover" class="font-poppins w-[300px]">
        <div class="flex flex-col justify-start items-start gap-4">
            <FloatLabel variant="on" class="w-full">
                <InputText v-model="invoice.invoice_no"  class="w-full" size="small"/>
                <label>Invoice No</label>
            </FloatLabel>
            <FloatLabel variant="on" class="w-full">
            <DatePicker v-model="invoice.invoice_date"  class="w-full" size="small"/>
                <label>Invoice Date</label>
            </FloatLabel>
            <Button @click="pushInvoice" label="Push to List" size="small" icon="pi pi-arrow-right" class="w-full"  severity="info"/>
        </div>
    </Popover>

     <Popover ref="receiptPopover" class="font-poppins w-[500px]">
        <div class="flex flex-col justify-start items-start gap-4">
            <FloatLabel variant="on" class="w-full">
                <InputText v-model="receipt.dr_no"  class="w-full" size="small"/>
                <label>Delivery Receipt No</label>
            </FloatLabel>
            <FloatLabel variant="on" class="w-full">
                <DatePicker v-model="receipt.dr_date"  class="w-full" size="small"/>
                <label>Delivery Receipt Date</label>
            </FloatLabel>
            <FloatLabel variant="on" class="w-full">
                <InputText v-model="receipt.delivery_place"  class="w-full" size="small"/>
                <label>Delivery Place</label>
            </FloatLabel>
            <FloatLabel variant="on" class="w-full">
            <DatePicker v-model="receipt.delivery_date"  class="w-full" size="small"/>
                <label>Delivery Date</label>
            </FloatLabel>
            <Button @click="pushReceipt" label="Push to List" size="small" icon="pi pi-arrow-right" class="w-full"  severity="info"/>
        </div>
    </Popover>

    <Popover ref="itemPopover" class="font-poppins w-[1500px]">
        <div class="flex justify-start items-start gap-4 mb-4">
            <FloatLabel variant="on" class="w-full">
                <InputText v-model="item.manufacturer"  class="w-full" size="small"/>
                <label>Manufacturer</label>
            </FloatLabel>
            <FloatLabel variant="on" class="w-full">
                <DatePicker v-model="item.manufacturing_date"  class="w-full" size="small"/>
                <label>Manufacturing Date</label>
            </FloatLabel>
            <FloatLabel variant="on" class="w-full">
                <DatePicker v-model="item.expiry_date"  class="w-full" size="small"/>
                <label>Expiry Date</label>
            </FloatLabel>
            <FloatLabel variant="on" class="w-full">
                <InputNumber v-model="item.shelf_life"  class="w-full" size="small"/>
                <label>Shelf Life (in Months)</label>
            </FloatLabel>
        </div>
        <div class="flex justify-start items-start gap-4 mb-4">
            <FloatLabel variant="on" class="w-full">
                <InputText v-model="item.batch_lot_number"  class="w-full" size="small"/>
                <label>Batch/Lot Number</label>
            </FloatLabel>
            <FloatLabel variant="on" class="w-full">
                <InputNumber v-model="item.quantity"  class="w-full" size="small"/>
                <label>Quantity</label>
            </FloatLabel>
            <FloatLabel variant="on" class="w-full">
                <InputNumber v-model="item.unit_cost"  class="w-full" size="small" :minFractionDigits="2" :maxFractionDigits="5" />
                <label>Unit Cost</label>
            </FloatLabel>
        </div>
        <div class="flex justify-start items-start gap-4 mb-4">
            <FloatLabel class="w-full" variant="on">
                <Select v-model="item.availability" :options="[{code:1,name:'Delivered'},{code:2,name:'Balance'}]" optionLabel="name" optionValue="code" size="small" class="w-full" />
                <label>Item Availability</label>
            </FloatLabel>
            <FloatLabel class="w-full" variant="on">
                <Select v-model="item.measurement_unit" :options="measurementUnits" optionLabel="name" filter size="small" class="w-full" />
                <label>Measurement Unit</label>
            </FloatLabel>
        </div>
        <div class="flex justify-start items-start gap-4 mb-4">
            <FloatLabel variant="on" class="w-full">
                <Textarea v-model="item.description" rows="5" cols="30" style="resize: none" autoResize class="w-full"/>
                <label>Item Description</label>
            </FloatLabel>
        </div>
        <Button @click="pushItem" label="Push to List" size="small" icon="pi pi-arrow-right" class="w-full"  severity="info"/>
    </Popover>

</template>