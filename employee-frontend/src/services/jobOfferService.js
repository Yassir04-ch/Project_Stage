import api from "@/api/axios";

const authHeaders = () => ({
  headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
});

export default {
  getOpenOffers() {
    return api.get("/job-offers");
  },
  getAllOffers() {
    return api.get("/job-offers?all=1", authHeaders());
  },
  getOffer(id) {
    return api.get(`/job-offers/${id}`);
  },
  createOffer(payload) {
    return api.post("/job-offers", payload, authHeaders());
  },
  updateOffer(id, payload) {
    return api.put(`/job-offers/${id}`, payload, authHeaders());
  },
  deleteOffer(id) {
    return api.delete(`/job-offers/${id}`, authHeaders());
  },
};