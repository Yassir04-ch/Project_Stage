import api from "@/api/axios";

export const registerUser = async (userData) => {
  return await api.post("/register", userData, {
    headers: {
      "Content-Type": "multipart/form-data",
    },
  });
};

export const loginUser = async (userData) => {
  return await api.post("/login", userData);
};