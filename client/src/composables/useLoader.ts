import { Loading } from "notiflix";

Loading.pulse('Loading Data, Please Wait...',{
    fontFamily:'Lexend Deca'
})

Loading.remove()

export function useLoader(text:string = 'Loading Data, Please Wait...'){
    const showLoader = () => {
        Loading.pulse(text,{
            fontFamily:'Lexend Deca'
        })
    }

    const hideLoader = () => {
        Loading.remove();
    };

     return {
        showLoader,
        hideLoader
    };
}