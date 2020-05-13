import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

export const store = new Vuex.Store({
    state: {
        /*
        authenticated: false,
        accessToken: '',
        */

        // @TODO: Only for debug purposes (hardcoded access token)
        authenticated: true,
        accessToken: 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiZDY2NmMyMmEyY2ZmOWMyNDc4NGRlMjI3OTMyYzAwYmQ4ZmQzODU4ZTkxNTVmNGQ4M2EzMDI1YzhjMTY1ZGRiNGFlOTgwODQwNTFjNzczZjAiLCJpYXQiOjE1ODkwNjcyNjQsIm5iZiI6MTU4OTA2NzI2NCwiZXhwIjoxNjIwNjAzMjY0LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.o7C6cNpXIQytcb_wfMBGWVWO9e4ZICamtuyUEuAWWCZ1XnLb-hZJo3qTzb__WZdseWrkrpLxeFtWwtNXog_J2BpAXc2Gq5oNUTKCyrytxCalXALKrKA279n3G_VjuhSOpsnOmb-vVMzQWurejjeyt2jUtcF9D1xRcM2s37bMmOfXiOOsDsiSVT19k_lRWUTFmaZ2mJ4U_TZaQH7S0YCl7hu-FtmL6xxKnqQ8fPdDgnKPQlG7iHgjtqMUCNXXg5shWD0M-NCYCSxRdsP8MC1GXPCmERXzqsU9Ojxr9SApLQJjjsXfY98d02OSsCBLTMgVkhQ1pJuyDyizcmgE0MqqnuJOIUp-1MVSuR6TyX_5PGRH2pWmjmQdC0tKbi8Yho0AttObPZmM4sjRjpGTA8wpQA2wEg-wBgbw-s6rHZtYK9bGraCAVeFV22HQIW3P8Sk-5o6wuLxzGxX9XdVYhhgPIrJJGikIXKuZHgFdnnoZE2RwnyKdBi_Mrso3PjREA8g6s9aDVqn8u1nsg-SN-3h1-hhKxJN0H2efhp4RgKyC5wTj8YzKbS4EEEjyNtCqXTbuhGTjiIZnuUX4dhamYTq0B3Gx7pCmEhk_YGYQa0XXRVHVYoE484iW_JkPSEK-4L969iuPH_rS45Ojm_oYYC_Eee1NKkRMp6QoNWJb6K8cdBg',
    },

    actions: {
        isAuthenticated(state) {
            return this.state.authenticated;
        }
    },
});
