import Vue from 'vue'
import Vuex from 'vuex'

import createPersistedState from "vuex-persistedstate";

Vue.use(Vuex)
export default new Vuex.Store(
    {
        state: {
            auth: false,
            hr: false,
            user: "",
        },
        getters: {
            logged: (state) => state.auth,
            hr: (state) => state.hr,
            user: (state) => state.user,
            isLoggedIn: (state) => state.user !== null,
        },
        mutations: {
            setAuthUser(state, user) {
                if(typeof user !== 'undefined' ){
                    state.user = user;
                    if (typeof user.id !== 'undefined') state.auth = true;                    
                    if (user.id === 1) state.hr = true;
                }
            },
            logoutUser(state) {
                state.auth = false;
                state.hr = false;
                state.user = "";
            },
        },
        actions: {
            logout({ commit }) {
                    commit('logoutUser');
            },
        },
        plugins: [createPersistedState()],
    }
)
