import Dashboard from './components/Dashboard.vue';
import ListAppointment from './pages/appointments/ListAppointment.vue';
import CreateAppointment from './pages/appointments/CreateAppointment.vue';
import ListUsers from './pages/users/ListUsers.vue';
import CreateUsers from './pages/users/CreateUser.vue';
import Profile from './pages/profile/Profile.vue';
import Settings from './pages/settings/Settings.vue';

export default [
    {
        path: '/admin/dashboard',
        name: 'admin.dashboard',
        component: Dashboard,
    },
    {
        path: '/admin/appointment',
        name: 'admin.appointment',
        component: ListAppointment,
    },
    {
        path: '/admin/create-appointment',
        name: 'admin.create-appointment',
        component: CreateAppointment,
    },
    {
        path: '/admin/users',
        name: 'admin.users',
        component: ListUsers,
    },
    {
        path: '/admin/create-user',
        name: 'admin.create-user',
        component: CreateUsers,
    },
    {
        path: '/admin/profile',
        name: 'admin.profile',
        component: Profile,
    },
    {
        path: '/admin/settings',
        name: 'admin.settings',
        component: Settings,
    },
   

]