import { h, resolveComponent } from 'vue'
import { createRouter, createWebHashHistory } from 'vue-router'

import Login from '@/views/pages/Login'

const routes = [
  {
    path: '/',
    name: 'Login',
    component: Login,
  },
  {
    path: '/',
    component: () => import('@/layouts/DefaultLayout.vue'), // Assuming there's a DefaultLayout.vue
    children: [
      {
        path: 'dashboard',
        name: 'Dashboard',
        component: () =>
          import('@/views/dashboard/Dashboard.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: 'companies',
        name: 'Companies',
        component: () =>
          import('@/views/companies/Companies.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: 'events',
        name: 'Events',
        component: () =>
          import('@/views/events/Events.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: 'booking',
        name: 'Booking',
        component: () =>
          import('@/views/booking/Booking.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: 'people',
        name: 'People',
        component: {
          render() {
            return h(resolveComponent('router-view'))
          },
        },
        meta: { requiresAuth: true },
        children: [
          {
            path: 'attendee-groups',
            name: 'Attendee Groups',
            component: () => import('@/views/people/AttendeeGroups.vue'),
            meta: { requiresAuth: true }
          },
          {
            path: 'attendees',
            name: 'Attendees',
            component: () => import('@/views/people/Attendees.vue'),
            meta: { requiresAuth: true }
          },
        ],
      },
      {
        path: 'places',
        name: 'Places',
        component: {
          render() {
            return h(resolveComponent('router-view'))
          },
        },
        meta: { requiresAuth: true },
        children: [
          {
            path: 'locations',
            name: 'Locations',
            component: () => import('@/views/places/Locations.vue'),
            meta: { requiresAuth: true }
          },
          {
            path: 'tables',
            name: 'Tables',
            component: () => import('@/views/places/Tables.vue'),
            meta: { requiresAuth: true }
          },
        ],
      },
    ],
  },
  {
    path: '/login',
    name: 'LoginRedirect',
    redirect: '/'
  },
]

const router = createRouter({
  history: createWebHashHistory(import.meta.env.BASE_URL),
  routes,
  scrollBehavior() {
    return { top: 0 }
  },
})

router.beforeEach((to, from, next) => {
  const loggedIn = localStorage.getItem('token');
  if (to.matched.some(record => record.meta.requiresAuth) && !loggedIn) {
    next('/login');
  } else {
    next();
  }
});

export default router
