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



const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/register",
      name: "register",
      component: Register,
    },
    {
      path:"/login",
      name:"login",
      component:Login,
    },
     {
      path:"/project",
      name:"project",
      component:Project,
    },
    {
      path: "/users",
      name: "dashboard",
      component: Users,
    },
    {
      path: "/",
      name: "home",
      component: Home,
    },
    {
    path:"/absences",
    name:"absences",
    component:Absences
    },
    {
    path:"/absences_create",
    name:"absences_create",
    component:CreateAbsences
    },
    {
    path:"/justification_create",
    name:"justification_create",
    component:CreateJustification
    },
    {
    path: "/justifications/absence/:absenceId",
    name: "justifications",
    component: JustificationView,
    },
    {
    path: "/projects",
    name: "Projects",
    component: Projects,
    },
    {
    path: '/projects/:id',
    name: 'project.details',
    component: DetailProjects,
    },
    {
      path: '/projects/:id/assignments',
      name: 'project.assignments',
      component: Assignments,
    },
    {
      path: '/getUser/:id',
      name: 'user.details',
      component: GetUser,
    },
  ],
});

export default router;