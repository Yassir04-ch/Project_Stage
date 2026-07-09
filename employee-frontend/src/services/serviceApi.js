import api from "@/api/axios";

const authHeaders = () => ({
  headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
});

export default {
  getServices() {
    return api.get("/services", authHeaders());
  },

  getEmployees() {
    return api.get("/emploiyee", authHeaders());
  },

  getCurrentUser() {
    return api.get("/users", authHeaders());
  },

  getServiceDetail(serviceId) {
    return api.get(`/services/${serviceId}`, authHeaders());
  },

  createService(payload) {
    return api.post("/services", payload, authHeaders());
  },

  updateService(serviceId, payload) {
    return api.put(`/services/${serviceId}`, payload, authHeaders());
  },

  deleteService(serviceId) {
    return api.delete(`/services/${serviceId}`, authHeaders());
  },

  assignEmployee(serviceId, userId) {
    return api.post(
      `/services/${serviceId}/assign`,
      { user_id: userId },
      authHeaders()
    );
  },

  removeEmployee(serviceId, userId) {
    return api.post(
      `/services/${serviceId}/remove-employee`,
      { user_id: userId },
      authHeaders()
    );
  },
};