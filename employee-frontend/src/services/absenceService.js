import api from "@/api/axios";

export const getAbsences = async () => {
  const response = await api.get("/absences", {
    headers: {
      Authorization: `Bearer ${localStorage.getItem("token")}`,
    },
  });

  return response.data;
};

export const createAbsence = async (data) => {
  const response = await api.post("/absences", data, {
    headers: {
      Authorization: `Bearer ${localStorage.getItem("token")}`,
    },
  });

  return response.data;
};


export const updateAbsence = async (id, data) => {
  const response = await api.put(`/absences/${id}`, data, {
    headers: {
      Authorization: `Bearer ${localStorage.getItem("token")}`,
    },
  });

  return response.data;
};

export const deleteAbsence = async (id) => {
  const response = await api.delete(`/absences/${id}`, {
    headers: {
      Authorization: `Bearer ${localStorage.getItem("token")}`,
    },
  });

  return response.data;
};