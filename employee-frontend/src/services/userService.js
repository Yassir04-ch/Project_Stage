import api from "@/api/axios";

export const getUser = async (id) => {
  const response = await api.get(`/getUser/${id}`, {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('token')}`
    }
  })
  return response.data
}

