import api from "@/api/axios";

export const getNotifications = async () => {
   const response = await api.get('/notifications');
   return response.data;
};

export const markAsRead = async (id) => {
    return await api.put(`/notifications/${id}/read`);
};

export const deleteNotification = async (id) => {
    return await api.delete(`/notifications/${id}`);
};

export const markAllAsRead = async () => {
    return await api.put("/notifications/read-all");
};