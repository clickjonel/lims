<script setup lang="ts">
    import { ref,onMounted } from 'vue';
    import { useApi } from '@/composables/useApi';
    import { useToast } from 'primevue';
    import { useRoute } from 'vue-router'

    import InputText from 'primevue/inputtext';
    import FloatLabel from 'primevue/floatlabel';
    import Button from 'primevue/button';
    import Select from 'primevue/select';
    import Textarea from 'primevue/textarea';
    import InputNumber from 'primevue/inputnumber';
    import DatePicker from 'primevue/datepicker';
    import Panel from 'primevue/panel';
    import FormLayout from '@/layouts/FormLayout.vue';

    const { fetchRequest,postRequest } = useApi()
    const toast = useToast()
    const route = useRoute()
    const delivery = ref([])

     onMounted( async ()=>{
        await findDelivery()
    })

    const findDelivery = async () => {
        const response = await fetchRequest('deliveries/find',{id:route.params.id})
        delivery.value = response.data.delivery
    }

</script>

<template>
    <FormLayout title="Create Stock Card for Delivery Items" return-path="/admin/deliveries">
        <div class="w-full flex flex-col justify-start items-start gap-2 p-2">
            <div v-for="item in delivery.delivery_items" class="w-full flex justify-between items-center gap-4 p-2 border-y bg-[#E3EEF9]">
                <span class="font-light text-sm w-3/4">{{ item.description }}</span>
                <Button label="Stockable" severity="info" class="text-xs" size="small" :disabled = "item.availability === 2"/>
            </div>
        </div>
        <div class="w-full flex flex-col justify-start items-start gap-2 p-2">
           <span class="text-xl uppercase font-medium">Input Stock Card Details</span>
            
        </div>
    </FormLayout>
</template>