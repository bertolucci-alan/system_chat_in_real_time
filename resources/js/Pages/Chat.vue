<template>
    <app-layout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Chat
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg flex" style="min-height: 400px; max-height: 400px;">

                    <!-- List User -->
                    <div class="w-3/12 bg-gray-200 bg-opacity-25 border-r border-gray-200 overflow-y-scroll">
                        <ul>
                            <li 
                            v-for="user in users" :key="user.id"
                            @click="() => {loadMessage(user.id)}"
                            :class="(userActive && userActive.id == user.id) ? 'bg-gray-200' : ''"
                            class="p-6 text-lg text-gray-600 leading-7 font-semibold border-b border-gray-200 hover:bg-opacity-50 hover:cursor-pointer hover:bg-gray-300">
                                <p class="flex items-center">
                                    {{user.name}}
                                    <span  v-if="user.notification" class="ml-2 w-2 h-2 bg-blue-500 rounded-full"></span>    
                                </p>
                            </li>
                        </ul>
                    </div>

                    <!-- Box Message -->
                    <div class="w-9/12 flex flex-col justify-between">


                        <!-- Message -->
                        <div class="w-full p-6 flex flex-col overflow-y-scroll">
                            <div 
                            v-for="message in messages" :key="message.id"
                            :class="(message.from === $page.props.user.id) ? 'text-right' : '' "
                            class="w-full mb-3 message">

                                <p 
                                :class="(message.from == $page.props.user.id) ? 'messageFromMe' : 'messageToMe' "
                                class="inline-block p-2 rounded-md" style="max-width: 75%;">
                                    {{message.content}}
                                </p>
                               <span class="block mt-1 text-xs text-gray-500">{{ moment(message.created_at).format('MM/DD/YYYY HH:mm') }}</span>

                            </div>
                        </div>

                        <!-- Form -->
                        <div v-if="userActive" class="w-full bg-gray-200 bg-opacity-25 p-6 border-t border-gray-200">
                            <form v-on:submit.prevent="sendMessage">
                                <div class="flex rounded-md overflow-hidden border-gray-300">
                                    <input v-model="message" type="text" name="message" class="flex-1 px-4 py-2 text-sn focus:outline-none border-none bg-gray-200" autocomplete="off">
                                    <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2">Enviar</button>
                                </div>
                            </form>
                        </div>

                    <!-- userActive null -->
                        <div v-if="!userActive" class="w-full bg-gray-200 bg-opacity-25 p-6 border-t border-gray-200">        
                            E.team, aqui você pode interagir hahahaha
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import moment, { now } from 'moment';
    import store from '../store';
    import Vue from 'vue';

    

    export default defineComponent({

        install: function (Vue) {
            Vue.prototype ? Vue.prototype.$cookies = this : Vue.config.globalProperties.$cookies = this;
            Vue.cookie = this;
        },

        components: {
            AppLayout,
        },

        data() {
            return {
                users: [],
                messages: [],
                userActive: null,
                message: '',
            }
        },

        computed: {
            user() {
                return store.state.user;
            }
        },

        methods: {

            scrollToButton: function(){
                if(this.messages.length){
                    document.querySelectorAll('.message:last-child')[0].scrollIntoView();
                }
            },

            loadMessage: async function(userId){

                axios.get(`api/users/${userId}`).then(response => {
                    this.userActive = response.data.user;
                });

                await axios.get(`api/messages/${userId}`).then(response => {
                    this.messages = response.data.messages;
                })

                const user =this.users.filter((user) => {
                        if(user.id === userId){
                            return user;
                        } 
                    });

                    if(user) {
                        //{setando propriedade notification ao usuário:}
                        user[0].notification = false;
                    }

                this.scrollToButton();
            },
            
            sendMessage:  async function() {
             await axios.post('api/messages/store', {

                'content': this.message,
                'to': this.userActive.id,

             }).then(response => {

                 this.messages.push({
                     'from': this.user.id,
                     'to':  this.userActive.id,
                     'content': this.message,
                     'created_at': new Date().toISOString(),
                     'updated_at': new Date().toISOString(),
                 })

                 this.message = '';
             });

                this.scrollToButton();
            },

            moment(value) {
             return moment(value);
            }
        },
        
        mounted() {
            axios.get('api/users').then(response => {
                this.users = response.data.users;
            });

            //conectando ao canal do usuário
            Echo.private(`user.${this.user.id}`).listen('.SendMessage', async (data) => {
                console.log(data);

                if(this.userActive && this.userActive.id === data.message.from){
                    await this.messages.push(data.message);
                    this.scrollToButton();
                } 
                else {

                    const user =this.users.filter((user) => {
                        if(user.id === data.message.from){
                            return user;
                        } 
                    });

                    if(user) {
                        //{setando propriedade notification ao usuário:}
                        user[0].notification = true;
                    }
                }
            }); 

            
        },
    });
</script>

<style>

.messageFromMe {
   @apply bg-gray-500 bg-opacity-25;
}

.messageToMe {
    background-color: #e5e7eb;
}

input {
    outline:0;
}

</style>
