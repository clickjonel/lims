import { defineStore } from 'pinia'
import axios from '../axios/axios'

interface User {
  full_name: string
  nickname: string
  image: string
  user_id: number
  assignment:{
    section:{
      section_id:number
      section_name:string
      short_name:string
    }
  }
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as User | null,
    token: localStorage.getItem('token') || '',
    isAuthenticated: false
  }),

  actions: {
    setUser(user: User) {
      this.user = user
      this.isAuthenticated = true
      // console.log(this.user)
    },

    setToken(token: string) {
      this.token = token
      localStorage.setItem('token', token)
    },

    clearUser() {
      this.user = null
      this.isAuthenticated = false
      this.token = ''
      localStorage.removeItem('token')
    },

    async fetchUser() {
      try {
        const response = await axios.get('user')
        this.setUser(response.data)
      } 
      catch (error) {
        this.clearUser()
      }
    },

    async initAuth() {
      if (this.token) {
        await this.fetchUser()
      }
    }
  }
})

