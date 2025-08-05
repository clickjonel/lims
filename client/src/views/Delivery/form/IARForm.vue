<script setup lang="ts">
    import { ref, onMounted } from 'vue';
    import { useApi } from '@/composables/useApi';
    import { useRoute } from 'vue-router';
    import  dohLogo from '@/assets/images/doh_logo.png';
    import Button from 'primevue/button';

    const { fetchRequest } = useApi();
    const route = useRoute();

    const iar = ref(<IAR>{})
    const invoiceNumbersString = ref(<string>'')
    const invoiceDatesString = ref(<string>'')
    const receiptNumbersString = ref(<string>'')
    const receiptDatesString = ref(<string>'')
    const deliveryDatesString = ref(<string>'')
    const deliveryPlacesString = ref(<string>'')
    const itemMeasurementsString = ref(<string>'')
    const itemUnitCostsString = ref(<string>'')

    onMounted(async () => {
        await fetchIARData();
    });

    const fetchIARData = async () => {
        const response = await fetchRequest('deliveries/iar',{ id:route.params.id })
        iar.value = response.data.delivery

        invoiceNumbersString.value = iar.value.delivery_invoices.map(invoice => invoice.invoice_no).filter(no => no).join(', ')
        invoiceDatesString.value = iar.value.delivery_invoices.map(invoice => invoice.invoice_date).filter(no => no).join(', ')

        receiptNumbersString.value = iar.value.delivery_receipts.map(receipt =>receipt.dr_no).filter(no => no).join(', ')
        receiptDatesString.value = iar.value.delivery_receipts.map(receipt =>receipt.dr_date).filter(no => no).join(', ')
        deliveryDatesString.value = iar.value.delivery_receipts.map(receipt =>receipt.delivery_date).filter(no => no).join(', ')
        deliveryPlacesString.value = iar.value.delivery_receipts.map(receipt =>receipt.delivery_place).filter(no => no).join(', ')
        itemMeasurementsString.value = iar.value.delivery_items.map(item => item.measurement_unit?.name).filter(no => no).join(', ')
        itemUnitCostsString.value = iar.value.delivery_items.map(item => item.unit_cost).filter(no => no).join(', ')
    } 


    interface IAR {
        id: number
        po_no?: string
        ptr_no?: string
        po_date?: string
        ptr_date?: string
        delivery_items: IARItem[]
        delivered_items: IARItem[]
        balance_items: IARItem[]
        purpose: string
        completion:number
        total_sum_delivered:number
        entity_name:string
        fund_source:{
            name:string
        }
        iar_no:string
        source_name:string
        iar_date:string
        source_address:string
        delivery_invoices:IARInvoice[]
        delivery_receipts:IARReceipt[]
        requisitioning_section:{
            section_name:string
        }
        payment_term_label:string
        total_quantity_items:number
        total_sum_items:number
        total_quantity_delivered:number
        end_user:{
            full_name:string
            assignment:{
                item:{
                    plantilla_title:string
                }
            }
        }
    }

    interface IARItem {
        id: number
        description: string
        availability: number
        batch_lot_number:string
        manufacturer?:string
        manufacturing_date?:string
        expiry_date?:string
        shelf_life?:number
        measurement_unit:{
            name:string
        }
        quantity:number
        unit_cost:number
    }

    interface IARInvoice {
        invoice_no: string
        invoice_date: string
    }

    interface IARReceipt {
        dr_no: string
        dr_date: string
        delivery_date:string
        delivery_place:string
    }

</script>



<template>

    <div class="w-full min-h-screen flex flex-col justify-start items-start font-poppins p-2">

        <div class="w-full flex justify-center items-center p-4">
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

            <span class="w-full text-center uppercase text-sm bg-gray-400">Inspection and Acceptance Report</span>

            <div class="w-full flex justify-start items-stretch divide-x font-light">
                <span class="min-w-[20%] pl-1">Entity Name:</span>
                <span class="min-w-[30%] pl-1">{{ iar.entity_name }}</span>
                <span class="min-w-[20%] pl-1">Fund Cluster:</span>
                <span class="min-w-[30%] pl-1">{{ iar.fund_source?.name }}</span>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-light">
                <span class="min-w-[20%] pl-1">Suplier/Source Name:</span>
                <span class="min-w-[30%] pl-1">{{ iar.source_name }}</span>
                <span class="min-w-[20%] pl-1">IAR No:</span>
                <span class="min-w-[30%] pl-1">{{ iar.iar_no }}</span>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-light">
                <span class="min-w-[20%] pl-1">Suplier/Source Address:</span>
                <span class="min-w-[30%] pl-1">{{ iar.source_address }}</span>
                <span class="min-w-[20%] pl-1">IAR Date:</span>
                <span class="min-w-[30%] pl-1">{{ iar.iar_date }}</span>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-light">
                <span class="min-w-[20%] pl-1">PO/PTR No:</span>
                <span class="min-w-[30%] pl-1">{{ iar.ptr_no ?? iar.po_no }}</span>
                <span class="min-w-[20%] pl-1">Invoice No/s:</span>
                <span class="min-w-[30%] pl-1">{{ invoiceNumbersString }}</span>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-light">
                <span class="min-w-[20%] pl-1">PO/PTR Date:</span>
                <span class="min-w-[30%] pl-1">{{ iar.ptr_date ?? iar.po_date }}</span>
                <span class="min-w-[20%] pl-1">Invoice Date/s:</span>
                <span class="min-w-[30%] pl-1">{{ invoiceDatesString }}</span>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-light">
                <span class="min-w-[20%] pl-1">Requisitioning Office:</span>
                <span class="min-w-[30%] pl-1">{{ iar.requisitioning_section?.section_name }}</span>
                <span class="min-w-[20%] pl-1">Delivery Receipt No/s:</span>
                <span class="min-w-[30%] pl-1">{{ receiptNumbersString }}</span>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-light">
                <span class="min-w-[20%] pl-1">Payment Term:</span>
                <span class="min-w-[30%] pl-1">{{ iar.payment_term_label }}</span>
                <span class="min-w-[20%] pl-1">Delivery Receipt Date:</span>
                <span class="min-w-[30%] pl-1">{{ receiptDatesString }}</span>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-light">
                <span class="min-w-[20%] pl-1">Grand Total Quantity Procured Based on PO/Contract:</span>
                <span class="min-w-[30%] pl-1">{{ iar.total_quantity_items }}</span>
                <span class="min-w-[20%] pl-1">Delivery Date/s:</span>
                <span class="min-w-[30%] pl-1">{{ deliveryDatesString }}</span>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-light">
                <span class="min-w-[20%] pl-1">Grand Total Amount of Procured Quantity:</span>
                <span class="min-w-[30%] pl-1">{{ iar.total_sum_items }}</span>
                <span class="min-w-[20%] pl-1">Place/s of Delivery:</span>
                <span class="min-w-[30%] pl-1">{{ deliveryPlacesString }}</span>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-light">
                <span class="min-w-[20%] pl-1">Inspected Quantity:</span>
                <span class="min-w-[30%] pl-1">{{ iar.total_quantity_delivered }}</span>
                <span class="min-w-[20%] pl-1">Unit/s of Measures:</span>
                <span class="min-w-[30%] pl-1">{{ itemMeasurementsString }}</span>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-light">
                <span class="min-w-[20%] pl-1">Inspected Total Cost:</span>
                <span class="min-w-[30%] pl-1">{{ iar.total_sum_delivered }}</span>
                <span class="min-w-[20%] pl-1">Unit Cost/s:</span>
                <span class="min-w-[30%] pl-1">{{ itemUnitCostsString }}</span>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-light">
                <span class="min-w-[20%] pl-1">Responsibility Center Code:</span>
                <span class="min-w-[30%] pl-1">{{ iar.requisitioning_section?.section_name }}</span>
                <span class="min-w-[20%] pl-1">Dimensions and Volume of Carton/Case:</span>
                <div class="min-w-[30%] flex flex-col justify-start items-stretch divide-y">
                    <div class="w-full flex justify-start items-stretch divide-x text-center">
                        <span class="min-w-[25%] text-[10px]">Height(cm)</span>
                        <span class="min-w-[25%] text-[10px]">Width(cm)</span>
                        <span class="min-w-[25%] text-[10px]">Length(cm)</span>
                        <span class="min-w-[25%] text-[10px]">Weight(kl)</span>
                    </div>
                    <div class="w-full flex justify-start items-stretch divide-x text-center">
                        <span class="min-w-[25%]  h-4"></span>
                        <span class="min-w-[25%]  h-4"></span>
                        <span class="min-w-[25%]  h-4"></span>
                        <span class="min-w-[25%]  h-4"></span>
                    </div>
                </div>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x bg-gray-400">
                <div class="min-w-[50%] text-center text-sm uppercase">Description of Items Purchased</div>
                <span class="min-w-[50%] text-center text-sm uppercase">Items Delivered</span>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-medium">
                <div class="min-w-[50%] text-center text-sm uppercase">As Per Purchased Order</div>
                <div class="min-w-[50%] flex flex-col justify-start items-start divide-y text-center text-sm uppercase">
                    <span class="w-full text-center">As per Inspection on Actual Product</span>
                    <div class="w-full flex justify-start items-start divide-x">
                        <span class="min-w-[50%] text-center">Compliant</span>
                        <span class="min-w-[50%] text-center">Non-Compliant</span>
                    </div>
                </div>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-light">
                <span class="min-w-[20%] pl-1">Item Description</span>
                <div class="w-full flex flex-col justify-start divide-y">
                    <div v-for="item in iar.delivered_items">
                        <div class="w-full flex justify-start items-stretch divide-x">
                            <div class="min-w-[37.5%] flex flex-col">
                                <span class="min-w-full pl-1 underline">{{ iar.po_no ?? iar.ptr_no }}</span>
                                <span class="min-w-full pl-1" v-html="item.description.replace(/\n/g, '<br>')"></span>
                            </div>
                            <span class="min-w-[31.3%]"></span>
                            <span class="min-w-[31.3%]"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-light text-left">
                <span class="max-w-[20%] w-[20%] pl-1">Brand Name/Model if possible</span>
                <span class="max-w-[80%] w-[80%] pl-1"></span>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-light text-left">
                <span class="max-w-[20%] w-[20%] pl-1">Packaging</span>
                <span class="max-w-[80%] w-[80%] pl-1"></span>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-light text-left">
                <span class="max-w-[20%] w-[20%] pl-1">Shelf-Life based on PO/Contract</span>
                <span class="max-w-[80%] w-[80%] pl-1"></span>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-light text-left">
                <span class="max-w-[20%] w-[20%] pl-1">Labelling Requirements - Blister, pack, foil strip/bottle, box, and kits</span>
                <span class="max-w-[80%] w-[80%] pl-1"></span>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-light text-left">
                <span class="max-w-[20%] w-[20%] pl-1">Labelling Requirements - corrugated carton</span>
                <span class="max-w-[80%] w-[80%] pl-1"></span>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-light text-left">
                <span class="max-w-[20%] w-[20%] pl-1">Manufacturer if applicable</span>
                <span class="max-w-[80%] w-[80%] pl-1"></span>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-light text-left">
                <span class="max-w-[20%] w-[20%] pl-1">Other Specifications(please indicate of neccesary)</span>
                <span class="max-w-[80%] w-[80%] pl-1"></span>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-light text-left">
                <span class="max-w-[20%] w-[20%] pl-1">Purpose</span>
                <span class="max-w-[80%] w-[80%] pl-1">{{ iar.purpose }}</span>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-light text-left bg-gray-400">
                <span class="max-w-[100%] w-[100%] text-center text-sm font-medium uppercase">Description of Findings / Non-Compliance</span>
            </div>

            <div class="w-full flex justify-start items-stretch">
                <span class="py-6"></span>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-light text-left bg-gray-400">
                <span class="max-w-[100%] w-[100%] text-center text-sm font-medium uppercase">Breakdown of actual item and quantity delivered</span>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-light text-left">
                <span class="max-w-[20%] w-[20%] pl-1">Number/Type of Delivery</span>
                <div class="max-w-[80%] w-[80%] flex flex-col justify-start items-stretch divide-y">
                    <div class="w-full flex justify-between items-center divide-x">
                        <div class="w-[20%] flex justify-center items-center gap-1">
                            <input type="checkbox">
                            <span>1st Tranche</span>
                        </div>
                        <div class="w-[20%] flex justify-center items-center gap-1">
                            <input type="checkbox">
                            <span>1st Tranche</span>
                        </div>
                        <div class="w-[20%] flex justify-center items-center gap-1">
                            <input type="checkbox">
                            <span>2nd Tranche</span>
                        </div>
                        <div class="w-[20%] flex justify-center items-center gap-1">
                            <input type="checkbox">
                            <span>3rd Tranche</span>
                        </div>
                        <div class="w-[20%] flex justify-center items-center gap-1">
                            <input type="checkbox">
                            <span>4th Tranche</span>
                        </div>
                        <div class="w-[20%] flex justify-center items-center gap-1">
                            <input type="checkbox">
                            <span>Others</span>
                        </div>
                    </div>
                    <div class="w-full flex justify-between items-center divide-x">
                        <div class="w-1/3 flex justify-center items-center gap-1">
                            <input type="checkbox" :checked="iar.completion === 1">
                            <span>Partial</span>
                        </div>
                        <div class="w-1/3 flex justify-center items-center gap-1">
                            <input type="checkbox" :checked="iar.completion === 2">
                            <span>Complete</span>
                        </div>
                        <div class="w-1/3 flex justify-center items-center gap-1">
                            <input type="checkbox" :checked="iar.completion === 3">
                            <span>Completion</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-medium text-left">
                <span class="max-w-[20%] w-[20%] pl-1">Item Description</span>
                <div class="max-w-[80%] w-[80%] flex justify-start items-stretch divide-x text-center">
                    <span class="min-w-[12.5%] max-w-[12.5%]">Stock #</span>
                    <span class="min-w-[12.5%] max-w-[12.5%]">Lot/Batch #</span>
                    <span class="min-w-[12.5%] max-w-[12.5%]">Manufacturer</span>
                    <span class="min-w-[12.5%] max-w-[12.5%]">Manufacturing Date</span>
                    <span class="min-w-[12.5%] max-w-[12.5%]">Expiry Date</span>
                    <span class="min-w-[12.5%] max-w-[12.5%]">Shelf Life</span>
                    <span class="min-w-[12.5%] max-w-[12.5%]">Unit</span>
                    <span class="min-w-[12.5%] max-w-[12.5%]">Quantity</span>
                </div>
            </div>

            <div class="w-full flex flex-col justify-start items-start divide-y">
                <div v-for="item in iar.delivered_items" v-bind:key="item.id" class="w-full flex justify-start items-stretch divide-x font-light text-left">
                    <span class="max-w-[20%] w-[20%] pl-1" v-html="item.description.replace(/\n/g, '<br>')"></span>
                    <div class="max-w-[80%] w-[80%] flex justify-start items-stretch divide-x text-center">
                        <span class="min-w-[12.5%] max-w-[12.5%]"></span>
                        <span class="min-w-[12.5%] max-w-[12.5%]">{{ item.batch_lot_number }}</span>
                        <span class="min-w-[12.5%] max-w-[12.5%]">{{ item.manufacturer }}</span>
                        <span class="min-w-[12.5%] max-w-[12.5%]">{{ item.manufacturing_date }}</span>
                        <span class="min-w-[12.5%] max-w-[12.5%]">{{ item.expiry_date }}</span>
                        <span class="min-w-[12.5%] max-w-[12.5%]">{{ item.shelf_life !== 0 ? item.shelf_life : '' }}</span>
                        <span class="min-w-[12.5%] max-w-[12.5%]">{{ item.measurement_unit.name }}</span>
                        <span class="min-w-[12.5%] max-w-[12.5%]">{{item.quantity}}</span>
                    </div>
                </div>

                <span v-if="iar.balance_items?.length > 0" class="w-full text-center">Balance</span>

                <div v-if="iar.balance_items?.length > 0" v-for="item in iar.balance_items" v-bind:key="item.id" class="w-full flex justify-start items-stretch divide-x font-light text-left">
                    <span class="max-w-[20%] w-[20%] pl-1" v-html="item.description.replace(/\n/g, '<br>')"></span>
                    <div class="max-w-[80%] w-[80%] flex justify-start items-stretch divide-x text-center">
                        <span class="min-w-[12.5%] max-w-[12.5%]"></span>
                        <span class="min-w-[12.5%] max-w-[12.5%]">{{ item.batch_lot_number }}</span>
                        <span class="min-w-[12.5%] max-w-[12.5%]">{{ item.manufacturer }}</span>
                        <span class="min-w-[12.5%] max-w-[12.5%]">{{ item.manufacturing_date }}</span>
                        <span class="min-w-[12.5%] max-w-[12.5%]">{{ item.expiry_date }}</span>
                        <span class="min-w-[12.5%] max-w-[12.5%]">{{ item.shelf_life }}</span>
                        <span class="min-w-[12.5%] max-w-[12.5%]">{{ item.measurement_unit.name }}</span>
                        <span class="min-w-[12.5%] max-w-[12.5%]">{{item.quantity}}</span>
                    </div>
                </div>
            </div> 

            <div class="w-full flex justify-start items-stretch divide-x font-light text-left">
                <span class="max-w-[20%] w-[20%] pl-1">Total Quantity and Unit Delivered</span>
                <span class="max-w-[80%] w-[80%] pl-1">{{ iar.total_sum_delivered }}</span>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-light text-left">
                <span class="max-w-[20%] w-[20%] pl-1">Date Inspected</span>
                <span class="max-w-[80%] w-[80%] pl-1"></span>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-light text-left">
                <span class="max-w-[20%] w-[20%] pl-1">Are Items Subject for FDA Analysis?</span>
                <div class="max-w-[80%] w-[80%] flex justify-start items-stretch divide-x">
                    <div class="min-w-1/2 flex justify-center items-center gap-1">
                        <input type="checkbox">
                        <span>Yes</span>
                    </div>
                    <div class="min-w-1/2 flex justify-center items-center gap-1">
                        <input type="checkbox" checked>
                        <span>No</span>
                    </div>
                </div>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-light text-left">
                <span class="max-w-[20%] w-[20%] pl-1">If Yes, How many boxes/bottles are taken for FDA Test Analysis?</span>
                <span class="max-w-[80%] w-[80%] pl-1"></span>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-light text-left bg-gray-400">
                <span class="max-w-[100%] w-[100%] text-center text-sm font-medium uppercase">All Items inspected are subject to final acceptance of end-user</span>
            </div>

            <div class="w-full flex justify-start items-stretch divide-x font-light text-left bg-gray-400">
                <span class="max-w-[100%] w-[100%] text-center text-sm font-medium uppercase">Inspection Team</span>
                <span class="max-w-[100%] w-[100%] text-center text-sm font-medium uppercase">Acceptance</span>
            </div>

            <div class="w-full flex justify-center items-stretch divide-x font-light">
                <div class="min-w-1/2 max-w-1/2 flex flex-col justify-center items-start gap-4 py-4 font-lexend">
                    <div class="w-full flex flex-col justify-center items-center gap-1 pt-4">
                        <span class="w-[80%] text-center font-medium uppercase border-b">Joycelyn Z. Rillorta</span>
                        <span class="w-[80%] text-center">Nurse V</span>
                    </div>
                    <div class="w-full flex flex-col justify-center items-center gap-1 pt-4">
                        <span class="w-[80%] text-center font-medium">End User/Representative</span>
                        <span class="w-[80%] text-center pt-4 border-b"></span>
                        <span class="w-[80%] text-center italic">Printed Name and Signature</span>
                    </div>
                    <div class="w-full flex flex-col justify-center items-center gap-1 pt-4">
                        <span class="w-[80%] text-center font-medium">Technical Inspection Committee</span>
                        <span class="w-[80%] text-center pt-4 border-b"></span>
                        <span class="w-[80%] text-center italic">Printed Name and Signature</span>
                    </div>
                    <div class="w-full flex flex-col justify-center items-center gap-1 pt-4">
                        <span class="w-[80%] text-center font-medium">Technical Inspection Committee</span>
                        <span class="w-[80%] text-center pt-4 border-b"></span>
                        <span class="w-[80%] text-center italic">Printed Name and Signature</span>
                    </div>
                </div>

                <div class="min-w-1/2 max-w-1/2 flex flex-col justify-start items-stretch gap-2">
                    <span class="w-full p-2 font-noto italic font-medium">Please check if items delivered are:</span>
                    <div class="w-full flex justify-start items-start">
                        <div class="w-1/2 flex flex-col justify-start items-center">
                            <div class="flex justify-center items-center gap-1">
                                <input type="checkbox">
                                <span>Accepted</span>
                            </div>
                            <div class="w-full flex justify-center items-center gap-1 p-2">
                                <span class="">Date:</span>
                                <span class="w-full border-b pb-2"></span>
                            </div>
                        </div>
                        <div class="w-1/2 flex flex-col justify-start items-center">
                            <div class="flex justify-center items-center gap-1">
                                <input type="checkbox">
                                <span>Rejected</span>
                            </div>
                            <div class="w-full flex justify-center items-center gap-1 p-2">
                                <span class="">Date:</span>
                                <span class="w-full border-b pb-2"></span>
                            </div>
                        </div>
                    </div>
                    <div class="w-full flex flex-col justify-start items-start p-2">
                        <span>Remarks(if rejected):</span>
                        <span class="w-full pt-6 border-b"></span>
                        <span class="w-full pt-6 border-b"></span>
                        <span class="w-full pt-6 border-b"></span>
                        <span class="w-full pt-6 border-b"></span>
                    </div>
                    <div class="w-full flex justify-center items-center gap-2 p-2">
                        <div class="w-2/3 flex flex-col justify-center items-center">
                            <span class="w-full font-semibold uppercase border-b text-center">{{ iar.end_user?.full_name }}</span>
                            <span class="w-full font-medium text-center">{{ iar.end_user?.assignment?.item.plantilla_title }}</span>
                            <span class="w-full font-light text-center">End User</span>
                        </div>
                    </div>
                    <div class="w-full flex justify-center items-center gap-2 p-2">
                        <div class="w-2/3 flex flex-col justify-center items-center">
                            <span class="w-full font-medium uppercase border-b text-center">Felina S. Carlos</span>
                            <span>Administrative Officer V</span>
                            <span>Head of Supply Office (Property Custodian)</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        

    </div>

</template>