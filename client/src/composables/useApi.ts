// // composables/useApi.js
// import { ref } from 'vue'
import axios from '../axios/axios'
import { useLoader } from './useLoader'
import { useToast } from 'primevue/usetoast';

const { showLoader,hideLoader } = useLoader('Loading, Please Wait...')

export function useApi() {
    const toast = useToast();

    const fetchRequest = async (endpoint: string, params: object | null = null) => {
        showLoader()

        try {
            const response = await axios.get(endpoint, { params:params });
            return {
                data:response.data,
                apiResponseStatus:response.status
            }
        } 
        catch (error:any) {
            toast.add({ severity: 'error', summary: 'Failed', detail: 'Something Went Wrong With the Action Request, Please Try Again or Contact you System Administator.', life: 3000 });
            console.log(error)
            return {
                error:error.response?.data,
            }
        
        } 
        finally {
          hideLoader()
        }
    }

    const postRequest = async (endpoint: string, params: object | null = null) => {
        showLoader()

        try {
            const response = await axios.post(endpoint, params);
            return {
                data:response.data,
                apiResponseStatus:response.status
            }
        } 
        catch (error:any) {
            console.log(error)
            toast.add({ severity: 'error', summary: 'Failed', detail: error.response.data?.message ?? 'Something Went Wrong With the Action Request, Please Try Again or Contact you System Administator.', life: 3000 });
            return {
                error:error.response?.data,
            }
        
        } 
        finally {
          hideLoader()
        }
    }


    const fetchRequests = async (requests: Array<{endpoint: string;params?: object;}>) => {
        showLoader();

        try {
            const responses = await Promise.all(
                requests.map(({ endpoint, params}) => 
                    fetchRequest(endpoint, params)
                )
            );

            return responses.map(response => ({
                response: response
            }));
        } 
        catch (error: any) {
            toast.add({
            severity: 'error',
            summary: 'Failed',
            detail: 'Request failed. Please try again or contact your administrator.',
            life: 3000
            });
            console.error('Fetch error:', error);
            return { error: error.response?.data };
        } 
        finally {
            hideLoader();
        }
    };

    return {
        fetchRequest,
        postRequest,
        fetchRequests
    }
  
}