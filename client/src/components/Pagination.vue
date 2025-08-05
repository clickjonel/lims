<script setup lang="ts">
    import { ref,computed } from 'vue';
    import Button from 'primevue/button';

    const props = defineProps({
        total:{
            default:0,
            type:Number
        },
        perPage:{
            default:0,
            type:Number
        }
    })

    const emit = defineEmits(<Array<string>>['fetchPage'])

    const totalPages = computed(() => Math.ceil(props.total / props.perPage));

    var currentPage = defineModel<number>({default:1})

    const goToPreviousPage = () => {
        if(currentPage.value > 1){
            currentPage.value -= 1
            emit('fetchPage','asdasda')
        }
    }

    const goToNextPage = () => {
        if(currentPage.value < totalPages.value){
            currentPage.value += 1
            emit('fetchPage',currentPage.value  +1)
        }
    }

</script>

<template>
    <div class="w-full flex justify-between items-center">
        <span class="font-lexend font-light text-sm">Total Entries of {{ total }}</span>
        <div class="flex justify-center items-center gap-2">
            <Button @click="goToPreviousPage" icon="pi pi-angle-left" rounded size="small" severity="info" />
                <span class="outline rounded-full text-sm size-10 font-lexend flex justify-center items-center">{{ currentPage }}</span>
            <Button @click="goToNextPage" icon="pi pi-angle-right" rounded size="small" severity="info" />
        </div>
    </div>
</template>