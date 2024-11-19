import { createRouter, createWebHistory } from 'vue-router';
import Home from '../components/HomeUser.vue';
import Register from '../components/RegisterUser.vue';
import Login from '../components/LoginUser.vue';
import Cartas from '../components/CartasComp.vue'
import MyDecks from 'src/components/MyDecks.vue';

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/register',
    name: 'Register',
    component: Register
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
  },
  {
    path: '/cartas',
    name: 'Cartas',
    component: Cartas
  },
  {
    path: '/my-decks',
    name: 'MyDecks',
    component: MyDecks
  }
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
});

export default router;
