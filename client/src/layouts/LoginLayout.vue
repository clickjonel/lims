<script setup lang="ts">
    import { onMounted, ref } from 'vue';
    import axios from '../axios/axios';
    import FloatLabel from 'primevue/floatlabel';
    import InputText from 'primevue/inputtext';
    import Button from "primevue/button"
    import Password from 'primevue/password';
    import { useAuthStore } from '@/stores/authStore';
    import { useRouter } from 'vue-router';
    import { useToast } from 'primevue';



    // constants
    const authStore = useAuthStore()
    const router = useRouter()
    const toast = useToast()

    // variables
    var credentials = ref(<Credentials>{
        username:'',
        password:''
    });


    // functions
    const login = () => {
        axios.post('login',{
            username:credentials.value.username,
            password:credentials.value.password,
        })
        .then((response)=>{
            authStore.setUser(response.data.user)
            console.log(response.data.user)
            authStore.setToken(response.data.token)
            authStore.isAuthenticated = true
            toast.add({ 
                severity: 'success', 
                summary: 'Login Successfull', 
                detail: 'You have successfully logged in the system.', 
                life: 2000 
            });

            router.push({name:'Dashboard'})
        })
        .catch((error)=>{
            toast.add({ 
                severity: 'error', 
                summary: 'Login Failed', 
                detail: 'Please Input Valid Credentials, Credentials are The Same as your DOHIS Credentials', 
                life: 2000 
            });
            console.log(error)
        })
    }


    // Interfaces
    interface Credentials {
        username:string,
        password:string
    }

</script>

<template>
    <div class="w-full h-screen flex justify-center items-center bg-[#E3EEF9]">
        <div class="w-1/2 h-1/2 flex flex-col justify-start items-start gap-4 font-poppins">
            <div class="w-full flex flex-col justify-center items-center">
                <span class="text-2xl uppercase font-semibold text-center">Logistics and Inventory Management System(LIMS)</span>
                <span class="text-xl font-medium">Center for Health Development - Cordillera</span>
                <span class="text-lg">Department of Health</span>
            </div>
            <div class="w-full flex flex-col justify-center items-center gap-6">
                <FloatLabel variant="on" class="w-2/3">
                    <InputText v-model="credentials.username" class="w-full"/>
                    <label>Username</label>
                </FloatLabel>
                <FloatLabel variant="on" class="w-2/3">
                     <Password v-model="credentials.password" :feedback="false" toggle-mask class="w-full" 
                        :pt="{
                            pcInputText:{
                                root: { class: 'w-full' },
                                input: { class: 'w-full' }
                            }
                        }"
                    />
                    <label>Password</label>
                </FloatLabel>
                <Button @click="login" label="Login" icon="pi pi-sign-in" class="w-2/3"/>
            </div>
        </div>
    </div>
</template>