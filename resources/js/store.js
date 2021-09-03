import axios from "axios";
import { createStore } from "vuex";
import createPersistedState from "vuex-persistedstate";

const store = createStore({
    //será definido a posição user
    state: {
        user: {},
    },

    //o que faz atualizar o valor do state
    mutations: {
        setUserState: (state, value) => {
            state.user = value;
        },
    },

    //disparar a ação para mutations para que possa atualizar o state
    actions: {
        userStateAction: ({ commit }) => {
            axios.get("api/user/authenticated").then((response) => {
                commit("setUserState", response.data.user);
            });
        },
    },

    plugins: [createPersistedState()],
});

export default store;
