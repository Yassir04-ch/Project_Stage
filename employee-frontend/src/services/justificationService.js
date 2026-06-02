import api from "@/api/axios";

export const createJustification = async (data) => {
  const response = await api.post("/justification", data);
  return response.data;
};