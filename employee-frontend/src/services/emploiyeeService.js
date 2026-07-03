import api from "@/api/axios";

export const getEmploiyee = async (page = 1, role = "", search = "") => {
  const response = await api.get("/emploiyee", {
    params: {
      page,
      role:   role   || undefined,  
      search: search || undefined, 
    },
  });

  return response.data;
};