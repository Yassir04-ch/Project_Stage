import api from "@/api/axios";

export const registerUser = async (userData) => {
  return await api.post("/createEmploiyee", userData, {
    headers: {
      "Content-Type": "multipart/form-data",
    },
  });
};

export const loginUser = async (userData) => {
  return await api.post("/login", userData);
};

export const createProject = async (data) => {
  return await api.post("/projects", data);
};