import { createRouter, createWebHistory } from "vue-router";

import Register from "@/views/auth/Register.vue";
import Login from "@/views/auth/Login.vue";
import Users from "@/views/dashboard/users.vue";
import Project from "@/views/projects/create.vue";
import Home from "@/views/home.vue";
import Absences from "@/views/absences/absence.vue";
import CreateAbsences from "@/views/absences/create.vue";

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
    }
  ],
});

export default router;