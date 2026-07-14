import api from "@/api/axios";

const authHeaders = () => ({
  headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
});

export default {
  apply(offerId, formData) {
    return api.post(`/job-offers/${offerId}/apply`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
  },
  getApplicationsForOffer(offerId) {
    return api.get(`/job-offers/${offerId}/applications`, authHeaders());
  },
  updateStatus(applicationId, status) {
    return api.put(`/applications/${applicationId}/status`, { status }, authHeaders());
  },
  deleteApplication(applicationId) {
    return api.delete(`/applications/${applicationId}`, authHeaders());
  },
};