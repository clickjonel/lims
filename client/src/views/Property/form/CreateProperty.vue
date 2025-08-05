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
    const { fetchRequest,postRequest } = useApi()

    const measurement_units = ref<any[]>([])
    const end_users = ref(<any[]>[])
    const categories = ref(<any[]>[])

    var property = ref(<Property>{})

    onMounted(async() => {
      fetchMeasurementUnits()
      fetchEndUsers()
      fetchCategories()
    })

    const fetchMeasurementUnits = async () => {
         const response = await fetchRequest('measurement_units/selection',{})
         measurement_units.value = response.data.measurement_units
    }

    const fetchEndUsers = async () => {
         const response = await fetchRequest('users/selection',{})
         end_users.value = response.data.users
    }

    const fetchCategories = async () => {
        const response = await fetchRequest('categories/selection',{})
        categories.value = response.data.categories
    }


    const create = async () => {
        const payload = {
            property_no: property.value.property_no,
            measurement_unit: property.value.measurement_unit,
            particulars: property.value.particulars,
            unit_cost: property.value.unit_cost,
            remarks: null,
            acquisition_date:property.value.acquisition_date ? property.value.acquisition_date.toISOString().split('T')[0] : '', 
            main_category_id:property.value.main_category_id,    
            end_user:property.value.end_user
        }
        const response = await postRequest('properties/create',payload)
        if(response.apiResponseStatus === 200){
             toast.add({ 
                severity: 'success', 
                summary: 'Created', 
                detail: response.data.message, 
                life: 3000 
            });
            property.value = {
                property_no: '',
                measurement_unit: null,
                particulars: '',
                unit_cost: null,
                remarks: '',
                acquisition_date: null,
                main_category_id: null,
                end_user: null
            }
        }
    }

        // interfaces
    interface Property {
        property_no: string
        measurement_unit: number|null
        particulars: string
        unit_cost: number|null
        remarks: string
        acquisition_date: Date|null
        main_category_id: number|null   
        end_user:number|null
    }




</script>

<template>
    <FormLayout title="Create New Property" return-path="/admin/properties">
        <div class="w-full h-full flex flex-col justify-start items-center gap-4 p-4 font-poppins overflow-y-scroll">
            <Panel header="PROPERTY DETAILS" toggleable class="p-2 w-full">
                <div class="w-full h-full flex flex-col justify-start items-start gap-4 px-2 pb-2 pt-6 border-t">
                    <FloatLabel variant="on" class="w-full">
                        <InputText v-model="property.property_no" class="w-full" size="small"/>
                        <label>Property Number</label>
                    </FloatLabel>

                    <FloatLabel variant="on" class="w-full">
                        <DatePicker v-model="property.acquisition_date"  class="w-full" size="small"/>
                        <label>Acquisition Date</label>
                    </FloatLabel>

                    <FloatLabel variant="on" class="w-full">
                        <InputNumber v-model="property.unit_cost" :minFractionDigits="2" :maxFractionDigits="5" class="w-full" size="small"/>
                        <label>Unit Cost</label>
                    </FloatLabel>

                    <FloatLabel variant="on" class="w-full">
                        <Textarea v-model="property.particulars"  class="w-full"/>
                        <label>Particulars</label>
                    </FloatLabel>

                    <FloatLabel class="w-full" variant="on">
                        <Select v-model="property.measurement_unit" :options="measurement_units" filter optionLabel="name" optionValue="id" size="small" class="w-full" />
                        <label>Measurement Unit</label>
                    </FloatLabel>

                    <FloatLabel class="w-full" variant="on">
                        <Select v-model="property.end_user" :options="end_users" optionLabel="full_name" optionValue="user_id" size="small" class="w-full" filter/>
                        <label>End User</label>
                    </FloatLabel>

                    <FloatLabel class="w-full" variant="on">
                        <Select v-model="property.main_category_id" :options="categories" optionLabel="name" optionValue="id" size="small" class="w-full" filter/>
                        <label>Category</label>
                    </FloatLabel>
                </div>
            </Panel>

            <div class="w-full flex justify-start items-center">
                <Button @click="create" label="Create Property" icon="pi pi-save" severity="success"/>
            </div>
        </div>
    </FormLayout>

</template>