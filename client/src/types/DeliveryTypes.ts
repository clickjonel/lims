import type { MeasurementUnit } from '@/types/MeasurementUnitTypes.ts';
 
    export interface Delivery{
        entity_name:string,
        fund_source:number|null,
        source_name:string,
        source_address:string,
        iar_no:string,
        iar_date:Date|null,
        po_no?:string,
        po_date?:Date|null,
        ptr_no?:string,
        ptr_date?:Date|null,
        bl_no?:string,
        bl_date?:Date|null,
        dnf_no?:string,
        dnf_date?:Date|null,
        req_office:number|null,
        end_user:number|null,
        payment_term:number|null,
        completion:number|null,
        purpose:string,
        invoices:DeliveryInvoiceFormatted[],
        receipts:DeliveryReceiptFormatted[],
        items:DeliveryItemFormatted[]
    }

    export interface DeliveryFormatted{
        entity_name:string,
        fund_source:number|null,
        source_name:string,
        source_address:string,
        iar_no:string,
        iar_date:string|null,
        po_no?:string,
        po_date?:string|null,
        ptr_no?:string,
        ptr_date?:string|null,
        bl_no?:string,
        bl_date?:string|null,
        dnf_no?:string,
        dnf_date?:string|null,
        req_office:number|null,
        end_user:number|null,
        payment_term:number|null,
        completion:number|null,
        purpose:string,
        invoices:DeliveryInvoiceFormatted[],
        receipts:DeliveryReceiptFormatted[],
        items:DeliveryItemFormatted[]
    }

    export interface DeliveryInvoice{
        invoice_no:string
        invoice_date:Date|null
    }

    export interface DeliveryInvoiceFormatted{
        invoice_no:string
        invoice_date:string
    }

    export interface DeliveryReceipt{
        dr_no:string
        dr_date:Date|null
        delivery_date:Date|null
        delivery_place:string   
    }

    export interface DeliveryReceiptFormatted{
        dr_no:string
        dr_date:string|null
        delivery_date:string|null
        delivery_place:string   
    }

    export interface DeliveryItem{
        availability:number|null
        manufacturer:string
        manufacturing_date:Date|null
        expiry_date:Date|null
        unit_cost:number|null
        quantity:number|null
        batch_lot_number:string
        shelf_life:number|null
        measurement_unit:MeasurementUnit|null
        description:string
    }

    export interface DeliveryItemFormatted{
        availability:number
        manufacturer:string
        manufacturing_date:string|null
        expiry_date:string
        unit_cost:number
        quantity:number
        batch_lot_number:string
        shelf_life:number|null
        measurement_unit:number
        measurement_unit_name:string
        description:string
    }