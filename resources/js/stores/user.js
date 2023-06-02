import { defineStore } from 'pinia';

export const useUserStore = defineStore('user', {
    state: () => {
        return {
            token: '',
            user: null,
            isLogin: false,
            isAdmin: null,
            isNotVerification: null
        }
    },
    actions: {
        async setToken(token) {
            this.token = token;

            const config = {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            }

            try {
                const userData = await axios.get('/api/get-profile', config);

                const user = userData.data.data.user;
                this.user = user;

                if (user.role.name == 'user') {
                    this.isAdmin = false;
                } else if (user.role.name == 'admin') {
                    this.isAdmin = true;
                } else {
                    this.isAdmin = null;
                }
                
                if (user.email_verified_at != null) {
                    this.isNotVerification = false;
                } else {
                    this.isNotVerification = true;
                }
            } catch (error) {
                console.log(error);
            }

            this.isLogin = true;
        },
        removeAuth() {
            this.token = null;
            this.user = null;
            this.isLogin = false;
            this.isAdmin = null;
            this.isNotVerification = null;
        }
    },
    persist: true
});