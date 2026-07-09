import { createRouter, createWebHistory } from "vue-router";

import Register from "@/views/auth/Register.vue";
import Login from "@/views/auth/Login.vue";
import Users from "@/views/dashboard/users.vue";
import Project from "@/views/projects/create.vue";
import Home from "@/views/home.vue";
import Absences from "@/views/absences/absence.vue";
import CreateAbsences from "@/views/absences/create.vue";
import CreateJustification from "@/views/justifications/create.vue";
import JustificationView from "@/views/justifications/justification.vue";
import Projects from "@/views/dashboard/projects.vue";
import DetailProjects from '@/views/projects/ProjectDetail.vue'
import Assignments from '@/views/assignments/ProjectPlanning.vue'
import GetUser from '@/views/auth/Profile.vue'
import Profile from '@/views/auth/me.vue'
import UserEditView from '@/views/employees/Edit.vue'
import Skills from '@/views/skills/Skills.vue'
import Notifications from '@/views/notifications/list.vue'
import Myassignement from '@/views/auth/assignment.vue'
import MyAbsences from '@/views/auth/absences.vue'
import AllProjects from '@/views/projects/index.vue'
import AllEmploiyee from '@/views/employees/List.vue'
import Contact from '@/views/contacts/contact.vue'
import Service from '@/views/services/Index.vue'


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/register",
      name: "register",
      component: Register,
    },
    {
      path: "/login",
      name: "login",
      component: Login,
    },
    {
      path: "/project",
      name: "project",
      component: Project,
      meta: { requiresAuth: true, roles: ['Administrateur', 'Manager'] }
    },
    {
      path: "/users",
      name: "dashboard",
      component: Users,
      meta: { requiresAuth: true, roles: ['Administrateur', 'Ressources Humaines'] }
    },
    {
      path: "/",
      name: "home",
      component: Home,
     },
    {
      path: "/absences",
      name: "absences",
      component: Absences,
      meta: { requiresAuth: true, roles: ['Administrateur', 'Ressources Humaines'] }
    },
    {
      path: "/absences_create",
      name: "absences_create",
      component: CreateAbsences,
      meta: { requiresAuth: true, roles: ['Administrateur', 'Ressources Humaines'] }
    },
    {
      path: "/justification_create",
      name: "justification_create",
      component: CreateJustification,
      meta: { requiresAuth: true }
    },
    {
      path: "/justifications/absence/:absenceId",
      name: "justifications",
      component: JustificationView,
      meta: { requiresAuth: true }
    },
    {
      path: "/projects",
      name: "Projects",
      component: Projects,
      meta: { requiresAuth: true, roles: ['Administrateur', 'Manager'] }
    },
    {
      path: '/projects/:id',
      name: 'project.details',
      component: DetailProjects,
      meta: { requiresAuth: true }
    },
    {
      path: '/projects/:id/assignments',
      name: 'project.assignments',
      component: Assignments,
      meta: { requiresAuth: true }
    },
    {
      path: '/getUser/:id',
      name: 'user.details',
      component: GetUser,
      meta: { requiresAuth: true }
    },
    {
      path: '/profile',
      name: 'profile',
      component: Profile,
      meta: { requiresAuth: true }
    },
    {
      path: '/users/edit/:id',
      name: 'user-edit',
      component: UserEditView,
      meta: { requiresAuth: true }
    },
    {
      path: '/skills',
      name: 'skills',
      component: Skills,
      meta: { requiresAuth: true, roles: ['Administrateur', 'Ressources Humaines'] }
    },
    {
      path: '/notifications',
      name: 'notifications',
      component: Notifications,
      meta: { requiresAuth: true }
    },
    {
      path: '/Myassignement',
      name: 'Myassignement',
      component: Myassignement,
      meta: { requiresAuth: true }
    },
    {
      path: '/myabsences',
      name: 'my.absences',
      component: MyAbsences,
      meta: { requiresAuth: true }
    },
    {
      path: '/allprojects',
      name: 'allprojects',
      component: AllProjects,
      meta: { requiresAuth: true }
    },
    {
      path: '/emploiyee',
      name: 'emploiyee',
      component: AllEmploiyee,
      meta: { requiresAuth: true }
    },
    {
      path: '/services',
      name: 'Service',
      component: Service,
      meta: { requiresAuth: true, roles: ['Administrateur', 'Manager'] }
    },
    {
      path: '/contacts',
      name: 'contact',
      component: Contact,
    }

  ]
});

router.beforeEach((to, from) => {
  const token = localStorage.getItem('token')
  const userRole = localStorage.getItem('userRole')

  if (to.meta.requiresAuth && !token) {
    return { name: 'login' }
  }

  if (to.name === 'login' && token) {
    return { name: 'profile' }
  }

  if (to.meta.roles && !to.meta.roles.includes(userRole)) {
    return { name: 'profile' }
  }

  return true
})

export default router;