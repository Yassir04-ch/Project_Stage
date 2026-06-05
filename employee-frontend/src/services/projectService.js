import api from "@/api/axios";

export const createProject = async (data) => {
  const response = await api.post("/projects", data);
  return response.data;
};

export const getProjects = async () => {
  const response = await api.get("/projects", {
    headers: {
      Authorization: `Bearer ${localStorage.getItem("token")}`,
    },
  });

  return response.data;
};