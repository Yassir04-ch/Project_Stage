import api from "@/api/axios";

export const createProject = async (data) => {
  const response = await api.post("/projects", data);
  return response.data;
};