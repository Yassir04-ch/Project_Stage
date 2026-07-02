import api from "@/api/axios";

export const getEmploiyee = async (page = 1, role = "") => {
  const response = await api.get("/emploiyee", {
    params: {
      page,
      role,
    },
  });

  return response.data;
};