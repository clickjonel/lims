<script setup lang="ts">
    import { ref,onMounted } from 'vue';
    import { useApi } from '@/composables/useApi';
    import { useToast } from 'primevue';
    import { useRoute } from 'vue-router'
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
    const route = useRoute()

    const fundSources = ref(<FundSource[]>[])
    const sections = ref(<Section[]>[])
    const sectionPersonnel = ref(<SectionPersonnel[]>[])
    const measurementUnits = ref(<MeasurementUnit[]>[])

    const invoicePopover = ref()
    const invoicePopoverUpdate = ref()
    const receiptPopover = ref()
    const receiptPopoverUpdate = ref()
    const itemPopover = ref()
    const itemPopoverUpdate = ref()

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
    var currentUpdateIndex = ref<number>(0)

    onMounted( async ()=>{
        await fetchFundSources()
        await fetchSections()
        await fetchMeasurementUnits()
        await findDelivery()
        await fetchSectionPersonnel()
        
    })

    const findDelivery = async () => {
        const response = await fetchRequest('deliveries/find',{id:route.params.id})
        setDeliveryFormatted(response.data.delivery)
    }
        
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

    const togglePopover = (event:Event,popoverRef:any,index:any=null) => {
        if(index !== null){
            currentUpdateIndex.value = index
        }
        popoverRef.toggle(event);
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
        var deliveryFormatted =  formatDelivery()

        const response = await postRequest('deliveries/update',deliveryFormatted)
        toast.add({ 
            severity: 'success', 
            summary: 'Delivery Update', 
            detail: response.data.message, 
            life: 3000 
        });

    }

    const setDeliveryFormatted = (deliveryFetched:any) => {
        delivery.value.entity_name = deliveryFetched.entity_name,
        delivery.value.fund_source = deliveryFetched.fund_source,
        delivery.value.source_name = deliveryFetched.source_name,
        delivery.value.source_address = deliveryFetched.source_address,
        delivery.value.iar_no = deliveryFetched.iar_no,
        delivery.value.iar_date = deliveryFetched.iar_date,
        delivery.value.po_no = deliveryFetched.po_no,
        delivery.value.po_date = deliveryFetched.po_date,
        delivery.value.ptr_no = deliveryFetched.ptr_no,
        delivery.value.ptr_date = deliveryFetched.ptr_date,
        delivery.value.bl_no = deliveryFetched.bl_no,
        delivery.value.bl_date = deliveryFetched.bl_date,
        delivery.value.dnf_no = deliveryFetched.dnf_no,
        delivery.value.dnf_date = deliveryFetched.dnf_date,
        delivery.value.req_office = deliveryFetched.req_office,
        delivery.value.end_user = deliveryFetched.end_user,
        delivery.value.payment_term = deliveryFetched.payment_term,
        delivery.value.completion = deliveryFetched.completion,
        delivery.value.purpose = deliveryFetched.purpose,
        delivery.value.invoices = deliveryFetched.delivery_invoices,
        delivery.value.receipts = deliveryFetched.delivery_receipts,
        delivery.value.items = deliveryFetched.delivery_items
    }

    const formatDelivery = () => {
        var deliveryFormatted:any = {
            id:route.params.id,
            entity_name:delivery.value.entity_name,
            fund_source:delivery.value.fund_source,
            source_name:delivery.value.source_name,
            source_address:delivery.value.source_address,
            iar_no:delivery.value.iar_no,
            iar_date:delivery.value.iar_date,
            po_no:delivery.value.po_no,
            po_date:delivery.value.po_date,
            ptr_no:delivery.value.ptr_no,
            ptr_date:delivery.value.ptr_date,
            bl_no:delivery.value.bl_no,
            bl_date:delivery.value.bl_date,
            dnf_no:delivery.value.dnf_no,
            dnf_date:delivery.value.dnf_date,
            req_office:delivery.value.req_office,
            end_user:delivery.value.end_user,
            payment_term:delivery.value.payment_term,
            completion:delivery.value.completion,
            purpose:delivery.value.purpose,
            invoices:delivery.value.invoices,
            receipts:delivery.value.receipts,
            items:delivery.value.items
        }

        return deliveryFormatted;

    }


</script>

<template>
    <FormLayout title="Update Delivery" return-path="/admin/deliveries">
        <div class="w-full h-full flex flex-col justify-start items-center gap-4 p-4 overflow-y-scroll">
            <Panel header="DELIVERY DETAILS" toggleable class="p- w-full">
                <div class="w-full h-full flex flex-col justify-start items-start gap-4 px-2 pt-6 border-t">
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
                    <Button @click="togglePopover($event, invoicePopover)" label="Add Invoice" icon="pi pi-plus" severity="info" class="my-4 text-xs" size="small"/>
                    <div class="w-full flex justify-start items-stretch gap-2 border font-semibold uppercase divide-x bg-[#E3EEF9]">
                        <span class="min-w-[45%] w-[45%] p-2">Invoice Number</span>
                        <span class="min-w-[45%] w-[45%] p-2">Invoice Date</span>
                        <span class="min-w-[10%] w-[10%] p-2">Actions</span>
                    </div>
                     <div v-if="delivery.invoices.length > 0" v-for="(invoice,index) in delivery.invoices" class="w-full flex justify-start items-stretch gap-2 border-b border-x font-light text-sm divide-x">
                        <span class="min-w-[45%] w-[45%] p-1">{{ invoice.invoice_no }}</span>
                        <span class="min-w-[45%] w-[45%] p-1">{{ invoice.invoice_date }}</span>
                        <span class="min-w-[10%] w-[10%] p-1 flex justify-start items-center gap-2">
                            <Button @click="togglePopover($event, invoicePopoverUpdate,index)" v-tooltip="{ value: 'Update Invoice', showDelay: 100, hideDelay: 300, pt: {text: {class: 'font-poppins text-xs'}}}" severity="info" icon="pi pi-file-edit" outlined  size="small" rounded/>
                            <Button v-tooltip="{ value: 'Delete Invoice', showDelay: 100, hideDelay: 300, pt: {text: {class: 'font-poppins text-xs'}}}" severity="danger" icon="pi pi-trash" outlined  size="small" rounded/>
                        </span>
                    </div>
                    <div v-if="delivery.invoices.length === 0" class="w-full flex justify-center items-center border-x border-b p-4">
                        <span class="w-full text-center animate-bounce">No Invoice/s Added</span>
                    </div>
                </div>
            </Panel>

            <Panel header="DELIVERY RECEIPTS" toggleable class="p-2 w-full">
                <div class="w-full flex flex-col justify-start items-start border-t">
                    <Button @click="togglePopover($event,receiptPopover)" label="Add Receipt" icon="pi pi-plus" severity="info" class="my-4 text-xs" size="small"/>
                    <div class="w-full flex justify-start items-stretch gap-2 border font-semibold uppercase divide-x bg-[#E3EEF9]">
                        <span class="min-w-[22.5%] w-[22.5%] p-2">Delivery Receipt Number</span>
                        <span class="min-w-[22.5%] w-[22.5%] p-2">Delivery Receipt Date</span>
                        <span class="min-w-[22.5%] w-[22.5%] p-2">Delivery Date</span>
                        <span class="min-w-[22.5%] w-[22.5%] p-2">Delivery Place</span>
                        <span class="min-w-[10%] w-[10%] p-2">Actions</span>
                    </div>
                     <div v-if="delivery.receipts.length > 0" v-for="(receipt,index) in delivery.receipts" class="w-full flex justify-start items-stretch gap-2 border-b border-x font-light text-sm divide-x">
                        <span class="min-w-[22.5%] w-[22.5%] p-1">{{ receipt.dr_no }}</span>
                        <span class="min-w-[22.5%] w-[22.5%] p-1">{{ receipt.dr_date }}</span>
                        <span class="min-w-[22.5%] w-[22.5%] p-1">{{ receipt.delivery_date }}</span>
                        <span class="min-w-[22.5%] w-[22.5%] p-1">{{ receipt.delivery_place }}</span>
                         <span class="min-w-[10%] w-[10%] p-1 flex justify-start items-center gap-2">
                            <Button @click="togglePopover($event, receiptPopoverUpdate,index)" v-tooltip="{ value: 'Update Receipt', showDelay: 100, hideDelay: 300, pt: {text: {class: 'font-poppins text-xs'}}}" severity="info" icon="pi pi-file-edit" outlined  size="small" rounded/>
                            <Button v-tooltip="{ value: 'Delete Invoice', showDelay: 100, hideDelay: 300, pt: {text: {class: 'font-poppins text-xs'}}}" severity="danger" icon="pi pi-trash" outlined  size="small" rounded/>
                         </span>
                    </div>
                    <div v-if="delivery.receipts.length === 0" class="w-full flex justify-center items-center border-x border-b p-4">
                        <span class="w-full text-center animate-bounce">No Receipt/s Added</span>
                    </div>
                </div>
            </Panel>

            <Panel header="DELIVERY ITEMS" toggleable class="p-2 w-full">
                <div class="w-full flex flex-col justify-start items-start border-t">
                    <Button @click="togglePopover($event,itemPopover)" label="Add Item" icon="pi pi-plus" severity="info" class="my-4 text-xs" size="small"/>
                    <div class="w-full flex justify-start items-stretch gap-2 border font-semibold uppercase divide-x bg-[#E3EEF9]">
                        <span class="w-[15%] min-w-[15%] p-2">Manufacturer -<br> Manufacturing Date</span>
                        <span class="w-[10%] min-w-[10%] p-2">Expiry Date -<br> Shelf Life</span>
                        <span class="w-[10%] p-2 min-w-[10%]">Batch/Lot Numbers</span>
                        <span class="w-[10%] p-2 min-w-[10%]">Quantity</span>
                        <span class="w-[10%] p-2 min-w-[10%]">Unit Cost</span>
                        <span class="w-[5%] p-2 min-w-[5%] break-words">Unit</span>
                        <span class="w-[30%] p-2 min-w-[30%]">Description</span>
                        <span class="w-[10%] p-2 min-w-[10%]">Actions</span>
                    </div>
                    <div v-if="delivery.items.length > 0" v-for="(item,index) in delivery.items" class="w-full flex justify-start items-stretch gap-2 border-b border-x font-light text-sm divide-x">
                        <span class="w-[15%]  min-w-[15%] p-2">{{ item.manufacturer ?? '' }} <br> {{ item.manufacturing_date }}</span>
                        <span class="w-[10%]  min-w-[10%] p-2"> {{ item.expiry_date }} <br> {{ item.shelf_life }} </span>
                        <span class="w-[10%] p-2  min-w-[10%]">{{ item.batch_lot_number }}</span>
                        <span class="w-[10%] p-2  min-w-[10%]">{{ item.quantity }}</span>
                        <span class="w-[10%] p-2  min-w-[10%]">{{ item.unit_cost }}</span>
                        <span class="w-[5%] p-2  min-w-[5%]">{{ item.measurement_unit_name }}</span>
                        <span class="w-[30%] p-2  min-w-[30%] whitespace-pre-wrap">{{ item.description }}</span>
                        <span class="w-[10%] p-2 min-w-[10%] flex justify-start items-center gap-2">
                            <Button @click="togglePopover($event, itemPopoverUpdate,index)" v-tooltip="{ value: 'Update Item', showDelay: 100, hideDelay: 300, pt: {text: {class: 'font-poppins text-xs'}}}" severity="info" icon="pi pi-file-edit" outlined  size="small" rounded/>
                            <Button v-tooltip="{ value: 'Delete Invoice', showDelay: 100, hideDelay: 300, pt: {text: {class: 'font-poppins text-xs'}}}" severity="danger" icon="pi pi-trash" outlined  size="small" rounded/>
                        </span>
                    </div>
                    <div v-if="delivery.items.length === 0" class="w-full flex justify-center items-center border-x border-b p-4">
                        <span class="w-full text-center animate-bounce">No Item/s Added</span>
                    </div>
                </div>
            </Panel>

            <div class="w-full flex justify-start items-center">
                <Button @click="save" label="Update Delivery" icon="pi pi-save" severity="success"/>
            </div>
        </div>
    </FormLayout>

    <!-- popovers -->
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

    <Popover ref="invoicePopoverUpdate" class="font-poppins w-[300px]">
        <div class="flex flex-col justify-start items-start gap-4">
            <FloatLabel variant="on" class="w-full">
                <InputText v-model="delivery.invoices[currentUpdateIndex].invoice_no"  class="w-full" size="small"/>
                <label>Invoice No</label>
            </FloatLabel>
            <FloatLabel variant="on" class="w-full">
                <InputText v-model="delivery.invoices[currentUpdateIndex].invoice_date"  class="w-full" size="small"/>
                <label>Invoice Date</label>
            </FloatLabel>
        </div>
    </Popover>

    <Popover ref="receiptPopoverUpdate" class="font-poppins w-[500px]">
        <div class="flex flex-col justify-start items-start gap-4">
            <FloatLabel variant="on" class="w-full">
                <InputText v-model="delivery.receipts[currentUpdateIndex].dr_no"  class="w-full" size="small"/>
                <label>Delivery Receipt No</label>
            </FloatLabel>
            <FloatLabel variant="on" class="w-full">
                 <InputText v-model="delivery.receipts[currentUpdateIndex].dr_date"  class="w-full" size="small"/>
                <label>Delivery Receipt Date</label>
            </FloatLabel>
            <FloatLabel variant="on" class="w-full">
                <InputText v-model="delivery.receipts[currentUpdateIndex].delivery_place"  class="w-full" size="small"/>
                <label>Delivery Place</label>
            </FloatLabel>
            <FloatLabel variant="on" class="w-full">
            <InputText v-model="delivery.receipts[currentUpdateIndex].delivery_date"  class="w-full" size="small"/>
                <label>Delivery Date</label>
            </FloatLabel>
        </div>
    </Popover>

    <Popover ref="itemPopoverUpdate" class="font-poppins w-[1500px]">
        <div class="flex justify-start items-start gap-4 mb-4">
            <FloatLabel variant="on" class="w-full">
                <InputText v-model="delivery.items[currentUpdateIndex].manufacturer"  class="w-full" size="small"/>
                <label>Manufacturer</label>
            </FloatLabel>
            <FloatLabel variant="on" class="w-full">
                 <InputText v-model="delivery.items[currentUpdateIndex].manufacturing_date"  class="w-full" size="small"/>
                <label>Manufacturing Date</label>
            </FloatLabel>
            <FloatLabel variant="on" class="w-full">
                 <InputText v-model="delivery.items[currentUpdateIndex].expiry_date"  class="w-full" size="small"/>
                <label>Expiry Date</label>
            </FloatLabel>
            <FloatLabel variant="on" class="w-full">
                <InputNumber v-model="delivery.items[currentUpdateIndex].shelf_life"  class="w-full" size="small"/>
                <label>Shelf Life (in Months)</label>
            </FloatLabel>
        </div>
        <div class="flex justify-start items-start gap-4 mb-4">
            <FloatLabel variant="on" class="w-full">
                <InputText v-model="delivery.items[currentUpdateIndex].batch_lot_number"  class="w-full" size="small"/>
                <label>Batch/Lot Number</label>
            </FloatLabel>
            <FloatLabel variant="on" class="w-full">
                <InputNumber v-model="delivery.items[currentUpdateIndex].quantity"  class="w-full" size="small"/>
                <label>Quantity</label>
            </FloatLabel>
            <FloatLabel variant="on" class="w-full">
                <InputNumber v-model="delivery.items[currentUpdateIndex].unit_cost"  class="w-full" size="small" :minFractionDigits="2" :maxFractionDigits="5" />
                <label>Unit Cost</label>
            </FloatLabel>
        </div>
        <div class="flex justify-start items-start gap-4 mb-4">
            <FloatLabel class="w-full" variant="on">
                <Select v-model="delivery.items[currentUpdateIndex].availability" :options="[{code:1,name:'Delivered'},{code:2,name:'Balance'}]" optionLabel="name" optionValue="code" size="small" class="w-full" />
                <label>Item Availability</label>
            </FloatLabel>
            <FloatLabel class="w-full" variant="on">
                <Select v-model="delivery.items[currentUpdateIndex].measurement_unit" :options="measurementUnits" optionLabel="name" filter size="small" class="w-full" />
                <label>Measurement Unit</label>
            </FloatLabel>
        </div>
        <div class="flex justify-start items-start gap-4 mb-4">
            <FloatLabel variant="on" class="w-full">
                <Textarea v-model="delivery.items[currentUpdateIndex].description" rows="5" cols="30" style="resize: none" autoResize class="w-full"/>
                <label>Item Description</label>
            </FloatLabel>
        </div>
    </Popover>

</template>