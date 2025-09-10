import { createRouter, createWebHistory } from 'vue-router'
import DefaultLayout from '@/components/DefaultLayout.vue'
import Home from '@/views/Home.vue'
import Dashboard from '@/views/Dashboard.vue'
import Login from '@/views/Login.vue'
import Signup from '@/views/Signup.vue'
import NotFound from '@/views/NotFound.vue'
import ForgotPassword from '@/views/ForgotPassword.vue'
import ResetPassword from '@/views/ResetPassword.vue'
import EmailConfirmation from '@/views/EmailConfirmation.vue'
import UserProfile from '@/views/UserProfile.vue'

//AlPINE USER
import { useUserStore } from '@/stores/user'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'DefaultLayout',
      component: DefaultLayout,
      meta: { requiresAuth: true },
      children: [
        { path: '/', name: 'Home', component: Home, meta: { requiresAuth: true } },
        { path: '/dashboard', name: 'Dashboard', component: Dashboard, meta: { requiresAuth: true, role: 'admin' } },
        { path: '/perfil', name: 'UserProfile', component: UserProfile, meta: { requiresAuth: true } },
      ]
    },
    {
      path: '/login',
      name: 'Login',
      component: Login,
      meta: { requiresGuest: true }
    },
    // {
    //   path: '/perfil',
    //   name: 'UserProfile',
    //   component: UserProfile,
    //   meta: { requiresAuth: true }
    // },
    {
      path: '/signup',
      name: 'Signup',
      component: Signup,
      meta: { requiresGuest: true }
    },
    {
      path: '/forgotPassword',
      name: 'ForgotPassword',
      component: ForgotPassword,
      meta: { requiresGuest: true }
    },
    {
      path: '/resetPassword',
      name: 'ResetPassword',
      component: ResetPassword,
      meta: { requiresGuest: true }
    },
    {
      path: '/emailConfirmation',
      name: 'EmailConfirmation',
      component: EmailConfirmation,
      meta: { requiresGuest: true }
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'NotFound',
      component: NotFound
    }
  ],
})


// Guard global
//Verifica se está logado ou não
//Se você tiver que acessar uma rota só se estiver logado, utilize "meta: { requiresAuth: true }" na rota
//Se você tiver que acessar uma rota quando não estiver logado, utilize "meta: { requiresGuest: true }" na rota

// router.beforeEach((to, from, next) => {
//   const token = localStorage.getItem('token')

//   if (to.meta.requiresAuth && !token) {
//     // precisa estar logado mas não tem token
//     next({ name: 'Login' })
//   } else if (to.meta.requiresGuest && token) {
//     // já está logado e tenta acessar login/signup
//     next({ name: 'Home' })
//   } else {
//     next()
//   }
// })




// Guard Global + Role User

router.beforeEach(async (to, from, next) => {
  const userStore = useUserStore()
  const token = localStorage.getItem('token')

  // Se exige login mas não tem token
  if (to.meta.requiresAuth && !token) {
    return next({ name: 'Login' })
  }

  // Se exige guest mas já tem token
  if (to.meta.requiresGuest && token) {
    return next({ name: 'Home' })
  }

  // Se tem token mas ainda não carregou usuário -> busca do backend
  if (token && !userStore.user) {
    try {
      await userStore.fetchUser()
    } catch (e) {
      console.error('Erro ao buscar usuário:', e)
      userStore.logout()
      return next({ name: 'Login' })
    }
  }

  // Se a rota exige role específica
  if (to.meta.role && userStore.user?.role !== to.meta.role) {
    return next({ name: 'NotFound' }) // ou uma página Unauthorized
  }

  next()
})



export default router
